$(function() {
    $("#esperar").fadeOut();

    // Con jQuery

    $("#btnProcesa").click(function(e){
        e.preventDefault();

        $("#esperar").css({"display":"block","opacity":"1"});

        const formdata = new FormData();
        formdata.append('tipoSearch',$("#tipoSearch").val());
        formdata.append('costosSearch',$("#costosSearch").val());
        formdata.append('descripSearch',$("#descripSearch").val());
        formdata.append('codigoSearch',$("#codigoSearch").val());
        formdata.append('ordenSearch',$("#ordenSearch").val());
        formdata.append('pedidoSearch',$("#pedidoSearch").val());
        formdata.append('conceptoSearch',$("#conceptoSearch").val());
        formdata.append('estado_item',$("#estado_item").val());
        formdata.append('anioSearch',$("#anioSearch").val());

        let contador_item = 1;

        const body = document.getElementById("cargoPlanDescripBody");
        const semaforo = [{}]

        fetch(RUTA+"cargoplanlogistic/filtroCargoPlanLogistica",{
            method:'POST',
            body:formdata
        })
        .then(response => response.json())
        .then(data=>{
            $("#esperar").css({"display": "none", "opacity": "0"});
        
            data.forEach(e=>{
                const tr = document.createElement('tr');
                tr.classList.add('pointer');
                
                let atencion    = e.atencion == 47 ? 'NORMAL':'URGENTE';
                let tipo        = e.idtipomov == 37 ? 'B' : 'S';
                let compra      = e.cantidad_pedido - e.cantidad_aprobada;
                let saldo       = e.cantidad_orden - e.ingreso > 0 ? e.cantidad_orden - e.ingreso : 0;
                let atrazo      = saldo > 0 ? e.dias_atraso * -1 : 0;


                tr.innerHTML = `<td class="textoCentro">${contador_item++}</td>
                                <td class="textoCentro">${e.estadoItem}</td>
                                <td class="textoCentro">${e.ccodproy}</td>
                                <td class="pl20px">${e.area}</td>
                                <td class="pl20px">${e.partida}</td>
                                <td class="textoCentro">${atencion}</td>
                                <td class="textoCentro">${tipo}</td>
                                <td class="textoCentro">${e.anio_pedido}</td>
                                <td class="textoDerecha">${e.pedido}</td>
                                <td class="textoCentro">${e.crea_pedido}</td>
                                <td class="textoCentro">${e.aprobacion_pedido}</td>
                                <td class="textoCentro">${e.cantidad_pedido}</td>
                                <td class="textoCentro">${e.cantidad_aprobada}</td>
                                <td class="textoDerecha">${compra}</td>
                                <td class="textoCentro">${e.ccodprod}</td>
                                <td class="textoCentro">${e.unidad}</td>
                                <td class="pl20px sticky-column">${e.descripcion}</td>
                                <td class="textoCentro">${tipo}</td>
                                <td class="textoCentro">${e.anio_orden}</td>
                                <td class="textoCentro">${e.cnumero}</td>
                                <td class="textoCentro">${e.fecha_orden}</td>
                                <td class="textoCentro">${e.cantidad_orden}</td>
                                <td class="pl10px">${e.item_orden}</td>
                                <td class="textoDerecha pr15px" style="background:#e8e8e8;font-weight: bold">${e.fecha_autorizacion_orden}</td>
                                <td class="pl10px">${e.cantidad_atendida ?? 0}</td>
                                <td class="pl10px">${e.proveedor}</td>
                                <td class="pl10px">${e.fecha_entrega}</td>
                                <td class="textoCentro ">${e.ingreso}</td>
                                <td class="textoCentro">${e.nota_ingreso ?? ''}</td>
                                <td class="textoDerecha pr15px">${e.fecha_recepcion_proveedor ?? ''}</td>
                                <td class="textoCentro">${saldo}</td>
                                <td class="textoCentro">${e.plazo}</td>
                                <td class="textoDerecha pr15px">${atrazo}</td>
                                <td class="textoDerecha pr15px"></td>
                                <td class="textoDerecha pr15px">${e.despachos ?? ''}</td>
                                <td class="textoDerecha">${e.cnumguia ?? ''}</td>
                                <td class="textoCentro">${e.guiasunat ?? ''}</td>
                                <td class="textoCentro">${e.salida_lurin ?? ''}</td>
                                <td class="textoCentro">${e.nota_transferencia ?? ''}</td>
                                <td class="textoCentro">${e.fecha_traslado ?? ''}</td>
                                <td class="textoCentro">${e.nota_obra ?? ''}</td>
                                <td class="textoCentro">${e.fecha_ingreso_almacen_obra ?? ''}</td>
                                <td class="textoCentro">${e.ingreso_obra ?? ''}</td>
                                <td class="textoDerecha">${e.fechaEmbarca ?? ''}</td>
                                <td class="pl10px">${e.nombreEmbarca ?? ''}</td>
                                <td class="pl10px">${e.tracking ?? ''}</td>
                                <td class="pl10px">${e.trackinglurin ?? ''}</td>
                                <td class="pl10px">${e.operador ?? ''}</td>
                                <td class="textoCentro">${e.transporte ?? ''}</td>
                                <td class="textoCentro">${e.operador ?? ''}</td>
                                <td class="pl10px">${e.fecha_descarga ?? ''}</td>`;

                body.appendChild(tr);
            });

           
        })

        return false;
    });

    $("#excelJS").click(function(e){
        e.preventDefault();

        let str = $("#formConsulta").serializeArray();
        
        $("#esperar").css({"display":"block","opacity":"1"}); ///para mostrar el modal de espera

        $.post(RUTA + "cargoplanlogistic/exceljs", str, function(data, textStatus, xhr) {
        
            // Verificar si la respuesta es válida
            if (!data) {
                //console.error("No se recibieron datos");
                $("#esperar").css({"display": "none", "opacity": "0"});
                return;
            }

            // Depuración: Ver estructura completa de la respuesta
            //console.log("Respuesta completa del servidor:", data);

            // Verificar si es un array o un objeto con propiedad items
            let datosParaExcel = Array.isArray(data) ? data : (data || []);

            // Verificar si hay datos para procesar
            if ( !datosParaExcel.length ) {
                //console.warn("No hay datos para generar el Excel");
                $("#esperar").css({"display": "none", "opacity": "0"});
                return;
            }

            // Depuración: Ver primer elemento
            //console.log("Primer elemento de los datos:", datosParaExcel[0]);

            // Llamar a la función para crear el Excel
            crearReporteExcel(datosParaExcel);
        
        }, "json").
            fail(function(jqXHR, textStatus, errorThrown) {
                //console.error("Error en la petición:", textStatus, errorThrown);
                $("#esperar").css({"display": "none", "opacity": "0"});
                alert("Error al obtener los datos: " + textStatus);
        });

        return false;
    });
})

async function crearReporteExcel(datos) {
    try {

        // Mostrar mensaje de espera
        //$("#esperarCargo").css("opacity", "1").fadeIn();

        // Validación básica de datos
        if (!Array.isArray(datos)) {
            throw new Error("Los datos deben ser un array");
        }

        if (datos.length === 0) {
            throw new Error("No hay datos para generar el reporte");
        }

        // Crear instancia del workbook
        const workbook = new ExcelJS.Workbook();
        
        // Configuración del libro
        workbook.creator = 'Sical';
        workbook.lastModifiedBy = 'Sical';
        workbook.created = new Date();
        workbook.modified = new Date();

        // Crear hoja de trabajo
        const worksheet = workbook.addWorksheet('Seguimiento Logistica');

        // Configuración de columnas con headers y mapeo a propiedades de los datos
        const columnConfigs = [
            { header: 'Items', key: 'iditem', width: 10 },
            { header: 'Estado Actual', key: 'estadoItem', width: 10 },
            { header: 'Codigo Proyecto', key: 'ccodproy', width: 15 },
            { header: 'Area', key: 'area', width: 50 },
            { header: 'Partida', key: 'partida', width: 30 },
            { header: 'Atención', key: 'nNivAten', width: 12 },
            { header: 'Tipo', key: 'idtipomov', width: 15 },
            { header: 'Año Pedido', key: 'anio_pedido', width: 12 },
            { header: 'N° Pedido', key: 'pedido', width: 15 },
            { header: 'Creación Pedido', key: 'crea_pedido', width: 20 },
            { header: 'Aprobación del Pedido', key: 'aprobacion_pedido', width: 20 },
            { header: 'Cantidad Pedida', key: 'cantidad_pedido', width: 15 },
            { header: 'Cantidad Aprobada', key: 'cantidad_aprobada', width: 15 },
            { header: 'Cantidad Compra', key: 'cantidad_orden', width: 15 },
            { header: 'Codigo del Bien/Servicio', key: 'ccodprod', width: 20 },
            { header: 'Unidad Medida', key: 'unidad', width: 15 },
            { header: 'Descripcion del Bien/Servicio', key: 'descripcion', width: 70 },
            { header: 'Tipo Orden', key: 'ntipmov', width: 15 },
            { header: 'Año Orden', key: 'anio_orden', width: 12 },
            { header: 'Nro Orden', key: 'cnumero', width: 15 },
            { header: 'Fecha Orden', key: 'fecha_orden', width: 15 },
            { header: 'Cantidad Orden', key: 'cantidad_orden', width: 15 },
            { header: 'Item Orden', key: 'item_orden', width: 15 },
            { header: 'Fecha Autorizacion', key: 'fecha_autorizacion', width: 15 },
            { header: 'Atencion Almacen', key: 'atencion_almacen', width: 15 },
            { header: 'Descripcion del proveedor', key: 'proveedor', width: 70 },
            { header: 'Fecha Entrega Proveedor', key: 'fecha_entrega', width: 15 },
            { header: 'Cant. Recibida', key: 'ingreso', width: 15 },
            { header: 'Nota de Ingreso', key: 'nota_ingreso', width: 15 },
            { header: 'Fecha Recepcion Proveedor', key: 'fecha_recepcion_proveedor', width: 15 },
            { header: 'Saldo por Recibir', key: 'saldoPorRecibir', width: 15 },
            { header: 'Dias Entrega', key: 'plazo', width: 15 },
            { header: 'Días Atrazo', key: 'dias_atraso', width: 15 },
            { header: 'Semáforo', key: 'semaforo', width: 15 },
            { header: 'Cantidad Despachada', key: 'despachos', width: 15 },
            { header: 'Nro. Guia', key: 'cnumguia', width: 15 },
            { header: 'Nro. Guia Transferencia', key: 'guia_transferencia', width: 15 },
            { header: 'Fecha Traslado', key: 'fecha_traslado', width: 15 },
            { header: 'Registro Almacen', key: 'id_regalm', width: 15 },
            { header: 'Fecha Ingreso Almacen', key: 'fecha_ingreso_almacen_obra', width: 15 },
            { header: 'Cantidad en Obra', key: 'ingreso_obra', width: 15 },
            { header: 'Fecha Embarque', key: 'fechaEmbarca', width: 15 },
            { header: 'Embarcación', key: 'nombreEmbarca', width: 15 },
            { header: 'N° Bulto', key: 'tracking', width: 15 },
            { header: 'PCL', key: 'trackinglurin', width: 15 },
            { header: 'Operador Logístico', key: 'operador', width: 15 },
            { header: 'Tipo Transporte', key: 'transporte', width: 15 },
            { header: 'Pedido Asignado', key: 'asigna', width: 70 },
            { header: 'Fecha Descarga', key: 'fecha_descarga', width: 50 }
        ];

        // Configurar columnas con headers
        worksheet.columns = columnConfigs;

        // Configuración del título
        worksheet.mergeCells('A1:AW1');
        const titleCell = worksheet.getCell('A1');
        titleCell.value = 'SEGUIMIENTO LOGISTICA';
        titleCell.alignment = { 
            horizontal: 'center', 
            vertical: 'middle',
            wrapText: true
        };
        titleCell.font = {
            bold: true,
            size: 14
        };

        // Aplicar estilos a la fila de cabecera (fila 2)
        const headerRow = worksheet.getRow(2);
        headerRow.values = columnConfigs.map(col => col.header);
        headerRow.height = 40; // Reducido de 80 para mejor visualización
        
        headerRow.eachCell((cell) => {
            cell.font = {
                bold: true,
                color: { argb: 'FFFFFFFF' }
            };
            cell.fill = {
                type: 'pattern',
                pattern: 'solid',
                fgColor: { argb: 'FF0070C0' } // Azul corporativo
            };
            cell.alignment = {
                vertical: 'middle',
                horizontal: 'center',
                wrapText: true
            };
            cell.border = {
                top: { style: 'thin' },
                left: { style: 'thin' },
                bottom: { style: 'thin' },
                right: { style: 'thin' }
            };
        });

        // Agregar datos dinámicos si existen
        if (datos && datos.length > 0) {
            datos.forEach((item, rowIndex) => {
                const rowData = {};
                const rowNumber = rowIndex + 3;
                const rowItem = rowIndex;

                let clase_operacion_pedido = item.idtipomov === 37 ? 'B' : 'S';
                let atencion = item.atencion === 47 ? "NORMAL" : "URGENTE";
                
                const estadoItemValue = item.estadoItem;

                const porcentajes = [{valor:105, rotulo:"0%"  , color:"#C8C8C8", semaforo:"#c8c8c8", estado:'anulado'},
                                     {valor:49 , rotulo:"10%" , color:"#F8CAAD", semaforo:"#FFFF00", estado:'procesando'},
                                     {valor:51 , rotulo:"12%" , color:"#00FF00", semaforo:"#FFFF00", estado:'almacen'},
                                     {valor:52 , rotulo:"20%" , color:"#B3C5E6", semaforo:"#FFFF00", estado:'stock'},
                                     {valor:53 , rotulo:"25%" , color:"#B3C5E6", semaforo:"#FFFF00", estado:'aprobacion'},
                                     {valor:54 , rotulo:"15%" , color:"#FF0000", semaforo:"#FFFF00", estado:'aprobado'},
                                     {valor:54 , rotulo:"22%" , color:"#E2D5CA", semaforo:"#FFFF00", estado:'elaboración orden'},
                                     {valor:59 , rotulo:"30%" , color:"#FFFF00", semaforo:"#FFFF00", estado:'firma orden'},
                                     {valor:60 , rotulo:"40%" , color:"#A9D08F", semaforo:"#B3C5E6", estado:'enviado proveeddor'},
                                     {valor:62 , rotulo:"50%" , color:"#A9D08F", semaforo:"#B3C5E6", estado:'recepcion parcial'},
                                     {valor:62 , rotulo:"60%" , color:"#A9D08F", semaforo:"#B3C5E6", estado:'recepcion total'},
                                     {valor:62 , rotulo:"70%" , color:"#A9D08F", semaforo:"#00FFFF", estado:'enviado parcial'},
                                     {valor:62 , rotulo:"75%" , color:"#A9D08F", semaforo:"#00FFFF", estado:'enviado total'},
                                     {valor:62 , rotulo:"80%" , color:"#A9D08F", semaforo:"#F67C2B", estado:'recepcion pucallpa'},
                                     {valor:62 , rotulo:"85%" , color:"#A9D08F", semaforo:"#F67C2B", estado:'enviado parcial'},
                                     {valor:62 , rotulo:"90%" , color:"#A9D08F", semaforo:"#F67C2B", estado:'enviado total'},
                                     {valor:299, rotulo:"95%" , color:"#0078D4", semaforo:"#0078D4", estado:'embarcado'},
                                     {valor:100, rotulo:"100%", color:"#00FF00", semaforo:"#00FF00", estado:'entregado'},
                                     {valor:230, rotulo:"100%", color:"#FF00FF", semaforo:"#FF00FF", estado:'compra local'}];


                const etiqueta = porcentajes.find(p => p.valor == estadoItemValue);

                
                // Mapeo seguro de columnas
                columnConfigs.forEach(columnDefinition => {
                    try {
                        const value = item[columnDefinition.key];

                        // Si la propiedad no existe o es null/undefined
                        if ( value == null) return;
                        
                        if ( columnDefinition.key == 'iditem' )
                            rowData[columnDefinition.key] = rowItem+1;
                        else if (columnDefinition.key == 'estadoItem'){
                            rowData[columnDefinition.key] = etiqueta.rotulo;
                        }else if ( columnDefinition.key == 'idtipomov' )
                            rowData[columnDefinition.key] = clase_operacion_pedido;
                        else if ( columnDefinition.key == 'nNivAten')
                            rowData[columnDefinition.key] = atencion;
                        else {
                            rowData[columnDefinition.key] = String(value).trim()
                        }

                    } catch (error) {
                        console.error(`Error en fila ${rowIndex}, columna ${columnDefinition.key}:`, error);
                        rowData[columnDefinition.key] = 'N/D';
                    }
                });
                
                worksheet.addRow(rowData);

                const estadoCell = worksheet.getCell(`B${rowNumber}`); // Usar referencia de la fila nueva
                estadoCell.fill = {
                    type: 'pattern',
                    pattern: 'solid',
                    fgColor: { argb: etiqueta.color }
                };
            });

        }

        // Ajustar automáticamente el ancho de las columnas según contenido
        worksheet.columns.forEach(column => {
            let maxLength = 0;
            column.eachCell({ includeEmpty: true }, cell => {
                let columnLength = cell.value ? cell.value.toString().length : 0;
                if (columnLength > maxLength) {
                    maxLength = columnLength;
                }
            });
            column.width = Math.min(Math.max(maxLength + 2, column.width || 0), 70);
        });

        // Generar el archivo
        const buffer = await workbook.xlsx.writeBuffer();
        const blob = new Blob([buffer], { 
            type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" 
        });

        // Descargar archivo
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `seguimiento_logistica_${new Date().toISOString().slice(0,10)}.xlsx`;
        document.body.appendChild(a);
        a.click();

        // Limpieza
        setTimeout(() => {
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
            $("#esperar").css({"display": "none", "opacity": "0"});
        }, 100);

    } catch (error) {
        console.error('Error al generar el Excel:', error);
        $("#esperar").css({"display": "none", "opacity": "0"});
        alert('Ocurrió un error al generar el archivo. Por favor intente nuevamente.');
    }
}

function getColorForPercentage(percentage) {
    const num = parseFloat(percentage);
    
    if (num == 100) return '00FF00'; // completo
    if (num == 49 ) return 'FFCA28';  // Verde claro
    if (num == 51 ) return 'F8CAAD';  // Amarillo
    if (num >= 53 ) return 'C9E2F4';
    if (num >= 54 ) return 'F8CAAD';
    if (num >= 230) return 'FF00FF';
    if (num >= 299) return '0078D4';
    
    return 'FFFF0000';                 // Rojo
}



function iniciarPaginadorLogistica() {
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