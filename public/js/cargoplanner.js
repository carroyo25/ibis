$(function() {
    let idpedido = "",progreso = 0;

    const display = document.getElementById('display');

    $("#esperar").fadeOut();
    
    $("#btnProcesa").click(function(e){
        e.preventDefault();

        let str = $("#formConsulta").serialize();

        $("#esperar").css({"display":"block","opacity":"1"});

        $.post(RUTA+"cargoplanner/filtroCargoPlan",str,
            function (data, text, requestXHR) {
                $(".itemsCargoPlanner table tbody")
                    .empty()
                    .append(data);

                    $("#esperar").fadeOut().promise().done(function(){
                        iniciarPaginador();
                    });

            "text"
        });

        return false;
    });

    // Función para realizar paginación después de la carga de datos
    function iniciarPaginador() {
        const content = document.querySelector('.itemsCargoPlanner'); 
        let itemsPerPage = 100; // Valor por defecto
        let currentPage = 0;
        const maxVisiblePages = 10; // Número máximo de botones visibles
        const items = Array.from(content.getElementsByTagName('tr')).slice(1); // Tomar todos los <tr>, excepto el primero (encabezado)
    
        // Mostrar una página específica
        function showPage(page) {
            const startIndex = page * itemsPerPage;
            const endIndex = startIndex + itemsPerPage;
            items.forEach((item, index) => {
                item.classList.toggle('hidden', index < startIndex || index >= endIndex);
            });
            updateActiveButtonStates();
            createPageButtons();
        }
    
        // Crear los botones de paginación y el selector de elementos por página
        function createPageButtons() {
            const totalPages = Math.ceil(items.length / itemsPerPage);
            let paginationContainer = document.querySelector('.pagination');
    
            // Si el contenedor de paginación no existe, crearlo
            if (!paginationContainer) {
                paginationContainer = document.createElement('div');
                paginationContainer.classList.add('pagination');
                content.appendChild(paginationContainer);
            } else {
                // Limpiar el contenedor existente
                paginationContainer.innerHTML = '';
            }
    
            // Crear el selector para elementos por página
            const itemsPerPageSelect = document.createElement('select');
            const options = [25, 50, 100, 150, 200, 250, 300];
    
            options.forEach(option => {
                const opt = document.createElement('option');
                opt.value = option;
                opt.textContent = option;
                if (option === itemsPerPage) opt.selected = true; // Establecer 100 como seleccionado por defecto
                itemsPerPageSelect.appendChild(opt);
            });
    
            // Agregar evento al selector
            itemsPerPageSelect.addEventListener("change", function() {
                itemsPerPage = parseInt(this.value); // Actualizar el número de elementos por página
                currentPage = 0; // Reiniciar a la primera página
                createPageButtons();
                showPage(currentPage);
            });
    
            paginationContainer.appendChild(itemsPerPageSelect); // Agregar el selector al contenedor de paginación
    
            // Botón "Primera"
            const firstButton = document.createElement('button');
            firstButton.textContent = 'Primera';
            firstButton.disabled = currentPage === 0;
            firstButton.addEventListener('click', () => {
                currentPage = 0;
                showPage(currentPage);
            });
            paginationContainer.appendChild(firstButton);
    
            // Botón "Anterior"
            const prevButton = document.createElement('button');
            prevButton.textContent = 'Anterior';
            prevButton.disabled = currentPage === 0;
            prevButton.addEventListener('click', () => {
                if (currentPage > 0) {
                    currentPage--;
                    showPage(currentPage);
                }
            });
            paginationContainer.appendChild(prevButton);
    
            // Mostrar botones limitados
            const startPage = Math.max(0, currentPage - Math.floor(maxVisiblePages / 2));
            const endPage = Math.min(totalPages, startPage + maxVisiblePages);
    
            for (let i = startPage; i < endPage; i++) {
                const pageButton = document.createElement('button');
                pageButton.textContent = i + 1;
                pageButton.disabled = i === currentPage; // Deshabilitar botón si es la página actual
                pageButton.classList.toggle('active', i === currentPage); // Agregar la clase 'active' si es la página actual
                pageButton.addEventListener('click', () => {
                    currentPage = i;
                    showPage(currentPage);
                });
    
                paginationContainer.appendChild(pageButton);
            }
    
            // Botón "Siguiente"
            const nextButton = document.createElement('button');
            nextButton.textContent = 'Siguiente';
            nextButton.disabled = currentPage === totalPages - 1;
            nextButton.addEventListener('click', () => {
                if (currentPage < totalPages - 1) {
                    currentPage++;
                    showPage(currentPage);
                }
            });
            paginationContainer.appendChild(nextButton);
    
            // Botón "Última"
            const lastButton = document.createElement('button');
            lastButton.textContent = 'Última';
            lastButton.disabled = currentPage === totalPages - 1;
            lastButton.addEventListener('click', () => {
                currentPage = totalPages - 1;
                showPage(currentPage);
            });
            paginationContainer.appendChild(lastButton);
        }
    
        // Actualizar los estados activos de los botones de paginación
        function updateActiveButtonStates() {
            const pageButtons = document.querySelectorAll('.pagination button');
            pageButtons.forEach((button, index) => {
                // Remover clase 'active' de todos los botones
                button.classList.remove('active');
                // Si el botón es el de la página actual, agregar la clase 'active'
                if (parseInt(button.textContent) === currentPage + 1) {
                    button.classList.add('active');
                }
            });
        }
    
        // Inicializar la paginación
        createPageButtons();
        showPage(currentPage); // Mostrar la primera página
    }

    $("#btnExporta").click(function (e) { 
        e.preventDefault();

        $("#esperar").css("opacity","1").fadeIn();

        $.post(RUTA+"cargoplanner/export", {registros:JSON.stringify(detalles())},
            function (data, textStatus, jqXHR) {

                $("#esperar").css("opacity","0").fadeOut();
                window.location.href = data.documento;
                
            },
            "json"
        );

        return false;
    });

    $("#cargoPlanDescrip tbody").on('dblclick','tr', function(e) {
        e.preventDefault();

        $("#vistadocumento").fadeIn();

        let tabla = $(this);

        idpedido = tabla.data('pedido');

        $("#codigo").val(tabla.find('td').eq(12).text());
        $("#producto").val(tabla.find('td').eq(14).text());
        $("#unidad").val(tabla.find('td').eq(13).text());
        $("#cantidad").val(tabla.find('td').eq(11).text());
        $("#estado").val(tabla.find('td').eq(1).text());
        $("#nropedido").val(tabla.find('td').eq(8).text());
        $("#tipo_pedido").val(tabla.find('td').eq(6).text());
        $("#emision_pedido").val(tabla.find('td').eq(9).text());
        $("#aprobacion_pedido").val(tabla.find('td').eq(10).text());
        $("#aprobado_por").val(tabla.data("aprueba"));

        $.post(RUTA+"cargoplanner/resumen", {orden:tabla.data("orden"),refpedido:$(this).data('itempedido'),despacho:$(this).data('despacho')},
            function (data, textStatus, jqXHR) {
                $("#tablaOrdenes tbody").empty().append(data.orden);
                $("#tablaIngresos tbody").empty().append(data.ingresos);
                $("#tablaDespachos tbody").empty().append(data.despachos);
                $("#tablaObra tbody").empty().append(data.registros);
            },
            "json"
        );

        return false;
    });

    $("#closeDocument").click(function (e) { 
        e.preventDefault();
        
        $("#vistadocumento").fadeOut();

        return false
    });

    $("#pdfpedido").click(function (e) { 
        e.preventDefault();


        $.post(RUTA+"panel/pdfPedido",{"pedido":idpedido},
            function (data, textStatus, jqXHR) {
              $(".ventanaVistaPrevia iframe")
                .attr("src","")
                .attr("src","public/documentos/temp/"+data);

                $("#vistaprevia").fadeIn();
            },
            "text"
          );

        return false;
    });

    $("#tablaOrdenes").on('click','a', function(e) {
        e.preventDefault();

        $.post(RUTA+"pedidoseg/datosOrden", {id: $(this).attr("href")},
            function (data, text, requestXHR) {
                $(".ventanaVistaPrevia iframe")
                .attr("src","")
                .attr("src",data);

                $("#vistaprevia").fadeIn();
            },"text"
        );

        return false;
    });

    $("#tablaIngresos").on('click','a', function(e) {
        e.preventDefault();

        $.post(RUTA+"cargoplanner/vistaIngreso", {id: $(this).attr("href")},
            function (data, text, requestXHR) {
                $(".ventanaVistaPrevia iframe")
                .attr("src","")
                .attr("src",data);

                $("#vistaprevia").fadeIn();
            },"text"
        );

        return false;
    });

    $("#tablaDespachos").on('click','a', function(e) {
        e.preventDefault();

        $("#vistaprevia").fadeIn();

        $.post(RUTA+"cargoplanner/vistaDespachos", {id: $(this).attr("href")},
            function (data, text, requestXHR) {
                $(".ventanaVistaPrevia iframe")
                .attr("src","")
                .attr("src",data);

                $("#vistaprevia").fadeIn();
            },"text"
        );

        return false;
    });

    $("#tablaObra").on('click','a', function(e) {
        e.preventDefault();

        $.post(RUTA+"cargoplanner/vistaRegistros", {id: $(this).attr("href"),tipo:'GA'},
            function (data, text, requestXHR) {
                $("#listaAdjuntos").empty().append(data.adjuntos);
                $("#listaAdjuntos li a:nth-child(2)").hide();

                $("#vistaAdjuntos").fadeIn();
            },"json"
        );

        return false;
    });

    $("#vistaAdjuntos").on("click","a", function (e) {
        e.preventDefault();
        
        $(".ventanaAdjuntos iframe")
            .attr("src","")
            .attr("src","public/documentos/almacen/adjuntos/"+$(this).attr("href"));
        
        return false;
    });

    $("#closeAtach").click(function (e) { 
        e.preventDefault();
        
        $(".ventanaAdjuntos iframe")
            .attr("src","");

        $("#vistaAdjuntos").fadeOut();

        return false;
    });

    $("#closePreview").click(function (e) { 
        e.preventDefault();
        
        $(".ventanaVistaPrevia iframe")
            .attr("src","");

        $("#vistaprevia").fadeOut();

        return false;
    });

    $(".procesos a").on('click', function(e) {
        e.preventDefault();

        $("#estado_item").val($(this).attr("href"));

        let str = $("#formConsulta").serialize();

        $("#esperar").css({"display":"block","opacity":"1"});

        $.post(RUTA+"cargoplanner/filtroCargoPlan",str,
            function (data, text, requestXHR) {
                $(".itemsCargoPlanner table tbody")
                    .empty()
                    .append(data);

                    $("#esperar").fadeOut().promise().done(function(){
                        iniciarPaginador();
                    });

            "text"
        });

        return false;
    });

    $(".exportReport").click(function (e) { 
        e.preventDefault(e);

        let estado = $(this).attr("href"),
            formData = new FormData();

        formData.append('estado', estado);

        $("#esperarCargo").css("opacity","1").fadeIn();
        //startTimer();
        
        fetch(RUTA+"cargoplanner/dataExcelTotalCargoPlan",{
            method:'POST',
            body:formData
        })
        .then((response)=> {
            return response.json();
        })
        .then((json)=> {
            $("#esperarCargo").css("opacity","0").fadeOut();
            //resetTimer();
            window.location.href = json.documento;
        })
        .catch((err)=> {
            console.log(err);
        });

        return false;
    });

    $(".exportFast").click(function(e){
        e.preventDefault();

        $("#esperarCargo").css("opacity","1").fadeIn();

        fetch(RUTA+"cargoplanner/exceljs")
        .then(response => response.json())
        .then(async (json)=> {
            document.getElementById("waitMessage").innerHTML = "Exportado a hoja de calculo...";
            document.getElementById("excelProcces").value = 50;

            console.log(json.datos);

            return false;

            await excelJson(json.datos);
        });

        return false;
    });

    async function excelJson(datos)   {
        const workbook = new ExcelJS.Workbook();
    
        workbook.creator = 'Sical';
        workbook.lastModifiedBy = 'Sical';
        workbook.created = new Date();
        workbook.modified = new Date();
    
        const worksheet = workbook.addWorksheet('Cargo Plan');

        const columns = [
            { width: 10 },
            { width: 10 },
            { width: 15 },
            { width: 50 },
            { width: 30 },
            { width: 12 },
            { width: 15 },
            { width: 12 },
            { width: 15 },
            { width: 20 },
            { width: 20 },
            { width: 15 },
            { width: 15 },
            { width: 15 },
            { width: 20 },
            { width: 15 },
            { width: 70 },
            { width: 15 },
            { width: 12 },
            { width: 15 },
            { width: 15 },
            { width: 15 },
            { width: 15 },
            { width: 15 },
            { width: 15 },
            { width: 70 },
            { width: 15 },
            { width: 15 },
            { width: 15 },
            { width: 15 },
            { width: 15 },
            { width: 15 },
            { width: 15 },
            { width: 15 },
            { width: 15 },
            { width: 15 },
            { width: 15 },
            { width: 15 },
            { width: 15 },
            { width: 15 },
            { width: 15 },
            { width: 15 },
            { width: 15 },
            { width: 15 },
            { width: 15 },
            { width: 15 },
            { width: 15 },
            { width: 70 },
            { width: 50 },
        ];

         // Establecer propiedades del título
         worksheet.mergeCells('A1:AW1');
         worksheet.getCell('A1').value = 'CARGO PLAN';
         worksheet.getCell('A1').alignment = { horizontal: 'center', vertical: 'center' };
         worksheet.getRow(2).height = 60;
     
         worksheet.columns = columns;
     
         // Establecer valores de cabecera
         const headers = [
             'Items', 'Estado Actual', 'Codigo Proyecto', 'Area', 'Partida', 'Atención', 'Tipo', 'Año Pedido', 'N° Pedido', 
             'Creación Pedido', 'Aprobación del Pedido', 'Cantidad Pedida', 'Cantidad Aprobada', 'Cantidad Compra',
             'Codigo del Bien/Servicio', 'Unidad Medida', 'Descripcion del Bien/Servicio', 'Tipo Orden', 'Año Orden',
             'Nro Orden', 'Fecha Orden', 'Cantidad Orden', 'Item Orden', 'Fecha Autorizacion', 'Atencion Almacen',
             'Descripcion del proveedor', 'Fecha Entrega Proveedor', 'Cant. Recibida', 'Nota de Ingreso', 'Fecha Recepcion Proveedor',
             'Saldo por Recibir', 'Dias Entrega', 'Días Atrazo', 'Semáforo', 'Cantidad Despachada', 'Nro. Guia',
             'Nro. Guia Transferencia', 'Fecha Traslado', 'Registro Almacen', 'Fecha Ingreso Almacen', 'Cantidad en Obra',
             'Estado Pedido', 'Estado Item', 'N° Parte', 'Codigo Activo', 'Operador Logístico', 'Tipo Transporte',
             'Observaciones/Concepto', 'Solicitante'
         ];
 
         /* worksheet.addRow(headers); */
         worksheet.getRow(2).values=headers;
        
 
         // Configurar wrapText para cada columna
         headers.forEach((header, index) => {
             const columnIndex = index + 1; // Las columnas en ExcelJS comienzan en 1
             worksheet.getColumn(columnIndex).alignment = { wrapText: true };  // Aplicar wrapText a toda la columna
         });
     
        let fila = 3;

        //const progress = document.getElementById("excelProcces");

        // Rellenar los datos en el archivo
        datos.forEach((dato, index) => {

                document.getElementById("waitMessage").innerHTML = "Insertando items...";

                let tipo_orden = dato.idtipomov === 37 ? 'BIENES' : 'SERVICIO';
                let clase_operacion = dato.idtipomov === 37 ? 'B' : 'S';
            
                let saldoRecibir = dato.cantidad_orden - dato.ingreso > 0 ? dato.cantidad_orden - dato.ingreso : "-";
            
                let dias_atraso = saldoRecibir > 0 && dato.dias_atraso < 1 ? dato.dias_atraso : "-";
            
                let suma_atendido = (Number(dato.cantidad_orden) + Number(dato.cantidad_atendida)).toFixed(2);
            
                let cantidad = dato.cantidad_pedido;
            
                let estado_pedido = dato.estadoItem >= 54 ? "Atendido" : "Pendiente";
                let estado_item = dato.estadoItem >= 54 ? "Atendido" : "Pendiente";
            
                let transporte = dato.nidreg === 39 ? "TERRESTRE" : dato.transporte;
                let atencion = dato.atencion === 47 ? "NORMAL" : "URGENTE";
            
                let color_mostrar = 'FFFFFF';
                let color_semaforo = 'FFFFFF';
                let porcentaje = '';
            
                let fecha_entrega = null;
                let fecha_autoriza = null;

                let dias_plazo = (parseInt(dato.plazo) + 1) + ' days';
            
                if(dato.fechaLog != null && dato.fechaOpe != null && dato.fechaFin != null) {
                    fecha_autoriza = dato.fecha_autorizacion;
                    fecha_entrega = dato.fecha_entrega_final;
                }
            
                /* Datos para el semáforo */
                let semaforoEstado = '';
                let dias_atraso_semaforo = '';

                let contador = 0,
                    total_items = datos.length;
            
                if (dato.estadoItem !== 105) {
                    if (fecha_entrega !== null) {
                        dias_atraso_semaforo = dato.dias_atraso;

                        if (dato.ingreso_obra === dato.cantidad_orden) {
                            semaforoEstado = "Entregado";
                            color_semaforo = '90EE90';
                            dias_atraso_semaforo = "";
                        } else if (dias_atraso_semaforo > 7) {
                            semaforoEstado = "Verde";
                            color_semaforo = '90EE90';
                            dias_atraso_semaforo = "";
                        } else if (dias_atraso_semaforo >= 0 && dias_atraso_semaforo <= 7) {
                            semaforoEstado = "Naranja";
                            color_semaforo = 'FFD700';
                            dias_atraso_semaforo = "";
                        } else if (dias_atraso_semaforo < 0) {
                            semaforoEstado = "Rojo";
                            color_semaforo = 'FF0000';
                            dias_atraso_semaforo = dato.dias_atraso * -1; // Para que no salga negativo
                        }
                    } else {
                        dias_atraso_semaforo = "";
                        semaforoEstado = "Procesando";
                        color_semaforo = "FFFF00";

                        if (dato.ingreso_obra > 0 && dato.ingreso_obra === dato.cantidad_atendida) {
                            semaforoEstado = "Entregado";
                            color_semaforo = '90EE90';
                            dias_atraso_semaforo = "";
                        } else if (dato.cantidad_atendida > 0) {
                            semaforoEstado = "Stock";
                            color_semaforo = '90EE90';
                            dias_atraso_semaforo = "";
                        }
                    }
                } else {
                    color_semaforo = 'CDCDCD';
                    semaforoEstado = "Anulado";
                }
            
                if (dato.estadoItem === 105) {
                    porcentaje = "0%";
                    estadofila = "anulado";
                    estado_item = "anulado";
                    estado_pedido = "anulado";
                    color_mostrar = 'C8C8C8';
                } else if (dato.estadoItem === 49) {
                    porcentaje = "10%";
                    estadofila = "Procesando";
                    estado_item = "item_stock";
                    estado_pedido = "Procesando";
                    color_mostrar = 'F8CAAD';
                } else if (dato.estadoItem === 53) {
                    porcentaje = "10%";
                    estadofila = "emitido";
                    estado_item = "Emitido";
                    estado_pedido = "Pedido Emitido";
                } else if (dato.estadoItem === 230) {
                    porcentaje = "100%";
                    estadofila = "comprado";
                    estado_item = "Compra Local";
                    estado_pedido = "Compra Local";
                    color_mostrar = 'FF0000';
                } else if (dato.estadoItem === 54) {
                    porcentaje = "15%";
                    estadofila = "aprobado";
                    estado_item = "aprobado";
                    estado_pedido = "aprobado";
                    color_mostrar = 'FC4236';
                } else if (dato.estadoItem === 52 && dato.ingreso_obra === dato.cantidad_pedido) {
                    porcentaje = "100%";
                    estadofila = "entregado";
                    estado_item = "atendido";
                    estado_pedido = "atendido";
                    color_mostrar = '00FF00';
                } else if (dato.estadoItem === 52 && dato.ingreso_obra === dato.cantidad_aprobada && dato.cantidad_aprobada > 0) {
                    porcentaje = "100%";
                    estadofila = "entregado";
                    estado_item = "atendido";
                    estado_pedido = "atendido";
                    color_mostrar = '00FF00';
                } else if (dato.estadoItem === 52) {
                    porcentaje = "20%";
                    estadofila = "stock";
                    estado_item = "item_stock";
                    estado_pedido = "stock";
                    color_mostrar = 'B3C5E6';
                } else if (!dato.orden) {
                    porcentaje = "15%";
                    estadofila = "item_aprobado";
                    estado_item = "aprobado";
                    estado_pedido = "aprobado";
                    color_mostrar = 'FC4236';
                } else if (dato.orden && !dato.proveedor) {
                    porcentaje = "25%";
                    estadofila = "item_orden";
                    estado_item = "aprobado";
                    estado_pedido = "aprobado";
                } else if (dato.proveedor && !dato.ingreso) {
                    porcentaje = "30%";
                    estadofila = "item_enviado";
                    estado_item = "atendido";
                    estado_pedido = "atendido";
                    color_mostrar = 'C0DCC0';
                } else if (dato.ingreso && dato.ingreso < dato.cantidad_orden) {
                    porcentaje = "40%";
                    estadofila = "item_ingreso_parcial";
                    estado_item = "atendido";
                    estado_pedido = "atendido";
                    color_mostrar = 'C0DCC0';
                } else if (!dato.despachos && dato.ingreso && dato.ingreso === dato.cantidad_orden) {
                    porcentaje = "50%";
                    estadofila = "item_ingreso_total";
                    estado_item = "atendido";
                    estado_pedido = "atendido";
                    color_mostrar = 'A9D08F';
                } else if (dato.despachos && !dato.ingreso_obra) {
                    porcentaje = "75%";
                    estadofila = "item_transito";
                    estado_item = "atendido";
                    estado_pedido = "atendido";
                    color_mostrar = '00FFFF';
                } else if (Math.round(dato.ingreso_obra) < Math.round(dato.cantidad_orden)) {
                    porcentaje = "85%";
                    estadofila = "item_ingreso_parcial";
                    estado_item = "atendido";
                    estado_pedido = "atendido";
                    color_mostrar = 'FFFFE1';
                } else if (dato.ingreso_obra && Math.round(suma_atendido, 2) === Math.round(dato.cantidad_aprobada, 2)) {
                    porcentaje = "100%";
                    estadofila = "entregado";
                    estado_item = "atendido";
                    estado_pedido = "atendido";
                    semaforo = "Entregado";
                    color_mostrar = '00FF00';
                } else if (dato.ingreso_obra && Math.round(dato.ingreso_obra, 2) === Math.round(dato.cantidad_orden, 2)) {
                    porcentaje = "100%";
                    estadofila = "entregado";
                    estado_item = "atendido";
                    estado_pedido = "atendido";
                    color_mostrar = '00FF00';
                }

                let color = {
                    type: 'pattern',
                    pattern: 'solid',
                    fgColor: { argb: color_mostrar }, // Color de fondo
                    bgColor: { argb: color_mostrar }  // Color de fondo
                };
            
                //añadir a los datos          
                worksheet.addRow([
                    index + 1,
                    porcentaje,
                    dato.ccodproy,
                    dato.area,
                    dato.partida,
                    atencion,
                    clase_operacion,
                    dato.anio_pedido,
                    dato.pedido,
                    dato.crea_pedido ? new Date(dato.crea_pedido) : null,
                    dato.aprobacion_pedido ? new Date(dato.aprobacion_pedido) : null,
                    dato.cantidad_pedido,
                    dato.cantidad_aprobada,
                    dato.cantidad_compra,
                    dato.ccodprod,
                    dato.unidad,
                    dato.descripcion,
                    dato.tipo_orden,
                    dato.anio_orden,
                    dato.cnumero,
                    dato.fecha_orden ? new Date(dato.fecha_orden) : null,
                    dato.cantidad_orden,
                    dato.item_orden,
                    dato.fecha_autorizacion ? new Date(dato.fecha_autorizacion) : null,
                    dato.cantidad_atendida,
                    dato.proveedor,
                    dato.fecha_entrega ? new Date(dato.fecha_entrega) : null,
                    dato.ingreso, dato.nota_ingreso,
                    dato.fecha_recepcion_proveedor ? new Date(dato.fecha_recepcion_proveedor) : null,
                    dato.saldo_recibir,
                    dato.plazo,
                    dato.dias_atraso,
                    dato.semaforo_estado,
                    dato.despachos,
                    dato.cnumguia,
                    dato.fecha_traslado ? new Date(dato.fecha_traslado) : null,
                    dato.nota_transferencia,
                    dato.nota_obra,
                    dato.fecha_registro_almacen ? new Date(dato.fecha_registro_almacen) : null,
                    dato.ingreso_obra,
                    dato.estado_pedido,
                    dato.estado_item,
                    dato.nroparte,
                    dato.cregistro,
                    dato.operador,
                    dato.transporte,
                    dato.concepto,
                    dato.nombre_elabora
            ]);
            
            worksheet.getCell(`B${fila}`).fill = color;
                            
            fila++;
        });

        // Rango A2:K2 con color 'BFCDDB'
        applyBackgroundColor(worksheet, 2, 2, 1, 11, 'BFCDDB');

        // Rango L2:N2 con color 'FC4236'
        applyBackgroundColor(worksheet, 2, 2, 12, 14, 'FC4236');

        // Rango O2:P2 con color 'BFCDDB'
        applyBackgroundColor(worksheet, 2, 2, 15, 16, 'BFCDDB');

        // Rango Q2:V2 con color '00FFFF'
        applyBackgroundColor(worksheet, 2, 2, 17, 22, '00FFFF');

        // Rango W2:AD2 con color 'BFCDDB'
        applyBackgroundColor(worksheet, 2, 2, 23, 30, 'BFCDDB');

        // Rango AE2:AM2 con color 'FFFF00'
        applyBackgroundColor(worksheet, 2, 2, 31, 39, 'FFFF00');

        // Rango AN2:AW2 con color '127BDD'
        applyBackgroundColor(worksheet, 2, 2, 40, 49, '127BDD');

        // Exportar como archivo Blob
        const buffer = await workbook.xlsx.writeBuffer();
        const blob = new Blob([buffer], { type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" });
  
        // Descargar archivo

        document.getElementById("waitMessage").innerHTML = "Descargar cargo plan...";

        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'datos_personalizados.xlsx';
        a.click();
        URL.revokeObjectURL(url);

        $("#esperarCargo").css("opacity","0").fadeOut();
    }
    
    $("#cargoPlanDescrip tbody").on('click','tr', function(e) {
        e.preventDefault();

        $(this).toggleClass('semaforoNaranja');

        return false;
    });

    $("#filtrosAvanzados").click(function(e){
        e.preventDefault();

        $.post(RUTA+"cargoplanner/proyectos",
            function (data, text, requestXHR) {
                
                $("#filtros").fadeIn(function(){
                    $("#costos").empty().append(data);
                });


            },
            "text"
        );

        return false;
    });

    $("#closeFilters,#btnCancelarFiltro").click(function (e) { 
        e.preventDefault();
 
        $("#filtros").fadeOut();

        return false;
    });

    $("#btnAceptarFiltro").click(function(e){
        e.preventDefault();
        
        let items = [];
            indice = 0,
            formData = new FormData();

        $('#costos input[type=checkbox]:checked').each(function() {
            items[indice++] = $(this).attr("id");
        });

        try {
            if (items.length == 0) throw new Error("Debe seleccionar un centro de costos");
            if ($("#fecha_inicio").val() == "") throw new Error("Selecione una fecha de inicio");
            if ($("#fecha_final").val() == "") throw new Error("Selecione una fecha final");

            formData.append('costos',JSON.stringify(items));
            formData.append('fechaInicio',$("#fecha_inicio").val());
            formData.append('fechaFinal',$("#fecha_final").val());

            $("#esperar").css({"display":"block","opacity":"1"});

            fetch(RUTA+'cargoplanner/filtroCargoPlanExporta',{
                method:'POST',
                body:formData,
            })
            .then(response =>response.json())
            .then(data => {
                $("#esperar").css({"display":"none","opacity":"0"});
                window.location.href = data.documento;
            })
        } catch (error) {
            mostrarMensaje(error,"mensaje_error");
        }
   
        return false;
    });
    
    $("#csvFile").click(function(e){
        e.preventDefault();

        $.post(RUTA+"cargoplanner/archivocvs",{"usuario":$("#id_user").val()},
            function (data, text, requestXHR) {
                console.log(data);
            },
            "json"
        );

        return false;
    });
})

detalles = () =>{
    DATA = [];

    let TABLA = $("#cargoPlanDescrip tbody >tr");

    TABLA.each(function(){
        let ITEM                = $(this).find('td').eq(0).text(),
            ESTADO              = $(this).find('td').eq(1).text(),
            PROYECTO            = $(this).find('td').eq(2).text(),
            AREA                = $(this).find('td').eq(3).text(),
            PARTIDA             = $(this).find('td').eq(4).text(),
            ATENCION            = $(this).find('td').eq(5).text(),
            TIPO                = $(this).find('td').eq(6).text(),
            ANIO_PEDIDO         = $(this).find('td').eq(7).text(),
            NUM_PEDIDO          = $(this).find('td').eq(8).text(),
            CREA_PEDIDO         = $(this).find('td').eq(9).text(),
            APRO_PEDIDO         = $(this).find('td').eq(10).text(),
            CANTIDAD            = $(this).find('td').eq(11).text(),
            APROBADO            = $(this).find('td').eq(12).text(),
            COMPRA              = $(this).find('td').eq(13).text(),
            CODIGO              = $(this).find('td').eq(14).text(),
            UNIDAD              = $(this).find('td').eq(15).text(),
            DESCRIPCION         = $(this).find('td').eq(16).text(),
            TIPO_ORDEN          = $(this).find('td').eq(17).text(),
            ANIO_ORDEN          = $(this).find('td').eq(18).text(),
            NRO_ORDEN           = $(this).find('td').eq(19).text(),
            FECHA_ORDEN         = $(this).find('td').eq(20).text(),
            CANTIDAD_ORDEN      = $(this).find('td').eq(21).text(),
            ITEM_ORDEN          = $(this).find('td').eq(22).text(),
            AUTORIZA_ORDEN      = $(this).find('td').eq(23).text(),
            CANTIDAD_ALMACEN    = $(this).find('td').eq(24).text(),
            PROVEEDOR           = $(this).find('td').eq(25).text(),
        
            FECHA_ENTREGA       = $(this).find('td').eq(26).text(),
            CANTIDAD_RECIBIDA   = $(this).find('td').eq(27).text(),
            NOTA_INGRESO        = $(this).find('td').eq(28).text(),
            FECHA_RECEPCION     = $(this).find('td').eq(29).text(),
            SALDO_RECIBIR       = $(this).find('td').eq(30).text(),
            DIAS_ENTREGA        = $(this).find('td').eq(31).text(),
            DIAS_ATRASO         = $(this).find('td').eq(32).text(),
            SEMAFORO            = $(this).find('td').eq(33).text(),
            DESPACHO            = $(this).find('td').eq(34).text(),
            NUMERO_GUIA         = $(this).find('td').eq(35).text(),
            GUIA_SUNAT          = $(this).find('td').eq(36).text(),
            FECHA_ENVIO         = $(this).find('td').eq(37).text(),


            GUIA_TRANSFER       = $(this).find('td').eq(38).text(),
            FECHA_TRASLADO      = $(this).find('td').eq(39).text(),
            
            REGISTRO_ALMACEN    = $(this).find('td').eq(40).text(),
            FECHA_REGISTRO_OBRA = $(this).find('td').eq(41).text(),
            CANTIDA_OBRA        = $(this).find('td').eq(42).text(),
            ESTADO_PEDIDO       = $(this).find('td').eq(43).text(),
            ESTADO_ITEM         = $(this).find('td').eq(44).text(),
            NUMERO_PARTE        = $(this).find('td').eq(45).text(),
            CODIGO_ACTIVO       = $(this).find('td').eq(46).text(),
            OPERADOR            = $(this).find('td').eq(47).text(),
            TRANSPORTE          = $(this).find('td').eq(48).text(),
            OBSERVACIONES       = $(this).find('td').eq(49).text(),
            SOLICITANTE         = $(this).find('td').eq(50).text();

        item = {};

        item['item']                = ITEM;
        item['estado']              = ESTADO;
        item['proyecto']            = PROYECTO;
        item['area']                = AREA;
        item['partida']             = PARTIDA;
        item['atencion']            = ATENCION;
        item['tipo']                = TIPO;
        item['anio_pedido']         = ANIO_PEDIDO;
        item['num_pedido']          = NUM_PEDIDO;
        item['crea_pedido']         = CREA_PEDIDO;
        item['apro_pedido']         = APRO_PEDIDO;
        item['codigo']              = CODIGO;
        item['unidad']              = UNIDAD;
        item['descripcion']         = DESCRIPCION;
        item['cantidad']            = CANTIDAD;
        item['aprobado']            = APROBADO;
        item['compra']              = COMPRA;

        item['tipo_orden']          = TIPO_ORDEN;
        item['anio_orden']          = ANIO_ORDEN;
        item['nro_orden']           = NRO_ORDEN;
        item['fecha_orden']         = FECHA_ORDEN;
        item['item_orden']          = ITEM_ORDEN;
        item['cantidad_orden']      = CANTIDAD_ORDEN;
        item['autoriza_orden']      = AUTORIZA_ORDEN;
        item['cantidad_almacen']    = CANTIDAD_ALMACEN;

        item['proveedor']           = PROVEEDOR;
        item['fecha_entrega']       = FECHA_ENTREGA;

        item['cantidad_recibida']   = CANTIDAD_RECIBIDA;
        item['nota_ingreso']        = NOTA_INGRESO;
        item['fecha_recepcion']     = FECHA_RECEPCION;

        item['saldo_recibir']       = SALDO_RECIBIR;
        item['dias_entrega']        = DIAS_ENTREGA;
        item['dias_atraso']         = DIAS_ATRASO;
        item['semaforo']            = SEMAFORO;
       
        item['despacho']            = DESPACHO;
        item['numero_guia']         = NUMERO_GUIA;
        item['guia_sunat']          = GUIA_SUNAT;
        item['fecha_envio']         = FECHA_ENVIO;


        item['registro_almacen']    = REGISTRO_ALMACEN;
        item['fecha_registro_obra'] = FECHA_REGISTRO_OBRA;
        item['cantidad_obra']       = CANTIDA_OBRA;

        item['guia_transfer']       = GUIA_TRANSFER;
        item['fecha_traslado']      = FECHA_TRASLADO;
        
        item['estado_pedido']       = ESTADO_PEDIDO;
        item['estado_item']         = ESTADO_ITEM;
        item['numero_parte']        = NUMERO_PARTE;
        item['codigo_activo']       = CODIGO_ACTIVO;
        item['operador']            = OPERADOR;
        item['transporte']          = TRANSPORTE;
        item['observaciones']       = OBSERVACIONES;
        item['solicitante']         = SOLICITANTE;
        
        
        DATA.push(item);
    })

    return DATA;
}

function s2ab(s) {
    var buf = new ArrayBuffer(s.length);
    var view = new Uint8Array(buf);
    for (var i=0; i<s.length; i++) view[i] = s.charCodeAt(i) & 0xFF;
    return buf;
}

async function processItems(){
    fetch(RUTA+"cargoplanner/itemsProcesados")
        .then((response) =>{
            return response.text();
        })
        .then((text) =>{
            console.log(text);
        })
        .catch((err)=> {
            console.log(err);
        });
}

function myTimer() {
    const date = new Date();
    document.getElementById("demo").innerHTML = date.toLocaleTimeString();
}
  
function myStopFunction() {
    clearInterval(myInterval);
}

function applyBackgroundColor(worksheet, startRow, endRow, startCol, endCol, color) {
    for (let row = startRow; row <= endRow; row++) {
        for (let col = startCol; col <= endCol; col++) {
            // Convertir el índice de columna numérico a su letra correspondiente (por ejemplo, 1 => 'A', 2 => 'B', etc.)
            const cellRef = worksheet.getColumn(col).letter + row;

            document.getElementById("excelProcces").value = 100;

            //console.log(cellRef)
            worksheet.getCell(cellRef).style = {
                fill : {
                type: 'pattern',  // Tipo de relleno
                pattern: 'solid', // Tipo sólido
                fgColor: { argb: color },  // Color de fondo en formato ARGB
                bgColor: { argb: color }
            },
            alignment : {
                horizontal: 'center',
                vertical: 'middle',
                wrapText: true
            }
        }}
    }
}

/*function updateDisplay() {
    const h = hours.toString().padStart(2, '0');
    const m = minutes.toString().padStart(2, '0');
    const s = seconds.toString().padStart(2, '0');
    display.textContent = `${h}:${m}:${s}`;
}

function startTimer() {
    let hours = 0;
    let minutes = 0;
    let seconds = 0;
    let timer;
    let isRunning = false;
    
    if (!isRunning) {
        isRunning = true;
        timer = setInterval(() => {
            seconds++;
            if (seconds === 60) {
                seconds = 0;
                minutes++;
            }
            if (minutes === 60) {
                minutes = 0;
                hours++;
            }
            updateDisplay();
        }, 1000);
    }
}

function resetTimer() {
    clearInterval(timer);
    isRunning = false;
    hours = 0;
    minutes = 0;
    seconds = 0;
    updateDisplay();
}*/
