$(function() {
    $("#esperar").fadeOut();

    $("#btnProcesa").click(function(e){
        e.preventDefault();

        let str = $("#formConsulta").serialize();

        $("#esperar").css({"display":"block","opacity":"1"});

        $.post(RUTA+"cargoplanlogistic/filtroCargoPlanLogistica",str,
            function (data, text, requestXHR) {
                $(".itemsCargoPlanner table tbody")
                    .empty()
                    .append(data);

                    $("#esperar").fadeOut().promise().done(function(){
                        iniciarPaginadorLogistica();
                    });
            "text"
        });

        return false;
    });

    $("#excelJS").click(function(e){
        e.preventDefault();

        let str = $("#formConsulta").serializeArray();
        
        $("#esperar").css({"display":"block","opacity":"1"}); ///para mostrar el modal de espera

        $.post(RUTA + "cargoplanlogistic/exceljs", str, function(data, textStatus, xhr) {
        
            // Verificar si la respuesta es válida
            if (!data) {
                console.error("No se recibieron datos");
                $("#esperar").css({"display": "none", "opacity": "0"});
                return;
            }

            // Depuración: Ver estructura completa de la respuesta
            console.log("Respuesta completa del servidor:", data);

            // Verificar si es un array o un objeto con propiedad items
            let datosParaExcel = Array.isArray(data) ? data : (data || []);

            // Verificar si hay datos para procesar
            if ( !datosParaExcel.length ) {
                console.warn("No hay datos para generar el Excel");
                $("#esperar").css({"display": "none", "opacity": "0"});
                return;
            }

            // Depuración: Ver primer elemento
            //console.log("Primer elemento de los datos:", datosParaExcel[0]);

            // Llamar a la función para crear el Excel
            crearReporteExcel(datosParaExcel);
        
        }, "json").
            fail(function(jqXHR, textStatus, errorThrown) {
                console.error("Error en la petición:", textStatus, errorThrown);
                $("#esperar").css({"display": "none", "opacity": "0"});
                alert("Error al obtener los datos: " + textStatus);
        });

        return false;
    });
})

async function crearReporteExcel(datos) {
    try {

        // Mostrar mensaje de espera
        $("#esperarCargo").css("opacity", "1").fadeIn();

        // Validación básica de datos
        if (!Array.isArray(datos)) {
            throw new Error("Los datos deben ser un array");
        }

        if (datos.length === 0) {
            throw new Error("No hay datos para generar el reporte");
        }

        //document.getElementById("waitMessage").innerHTML = "Generando archivo Excel...";

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
            { header: 'Tipo', key: 'ntipmov', width: 15 },
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
            { header: 'N° Bulto', key: 'nroBulto', width: 15 },
            { header: 'PCL', key: 'pcl', width: 15 },
            { header: 'Operador Logístico', key: 'operador', width: 15 },
            { header: 'Tipo Transporte', key: 'transporte', width: 15 },
            { header: 'Pedido Asignado', key: 'asigna', width: 70 },
            { header: 'Fecha Descarga', key: 'fecha_descarga', width: 50 }
        ];

         /*if (datos && datos.length > 0) {
            datos.forEach((item, rowIndex) => {
                 //console.log(item.iditem);
                 columnConfigs.forEach(col => {
                    const rowData = {};

                    rowData[col.key] = '';

                    rowData[col.key] = String(item[col.key]).trim();

                    console.log(rowData);
                 })
            })
        }
        return false*/

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
                // Calcular saldo por recibir si no existe
                if (item.saldoPorRecibir === undefined) {
                    item.saldoPorRecibir = item.cantidad_orden - (item.ingreso || 0);
                }
                
                // Determinar semáforo según días de atraso
                if (item.semaforo === undefined) {
                    item.semaforo = item.dias_atraso > 0 ? 'Rojo' : 
                                    item.dias_atraso === 0 ? 'Amarillo' : 'Verde';
                }

                const rowData = {};
                
                columnConfigs.forEach(col => {
                    // Valor por defecto
                    rowData[col.key] = '';
                    
                    //console.log(col.key);
                    
                    // Si la propiedad no existe o es null/undefined
                    //if (item[col.key] == null) return;
                    
                    /*
                    // Formateo básico de fechas (sin librerías externas)
                    if (col.key.toLowerCase().includes('fecha')) {
                        try {
                            const date = new Date(item[col.key]);
                            if (!isNaN(date)) {
                                rowData[col.key] = date.toLocaleDateString('es-PE');
                                return;
                            }
                        } catch (e) {
                            console.warn(`Error formateando fecha ${col.key}:`, e);
                        }
                    }*/
                    
                    // Convertir a string y limpiar espacios
                    //rowData[col.key] = String(item[col.key]).trim();
                });

                const row = worksheet.addRow(rowData);

                // Aplicar estilos alternados a las filas
                const fillColor = rowIndex % 2 === 0 ? 'FFF2F2F2' : 'FFFFFFFF';
                row.eachCell((cell) => {
                    cell.fill = {
                        type: 'pattern',
                        pattern: 'solid',
                        fgColor: { argb: fillColor }
                    };
                    cell.border = {
                        top: { style: 'thin' },
                        left: { style: 'thin' },
                        bottom: { style: 'thin' },
                        right: { style: 'thin' }
                    };
                    
                    // Alineación específica para columnas numéricas
                    if (['cantidad_pedido', 'cantidad_aprobada', 'cantidad_orden', 
                         'ingreso', 'despachos', 'ingreso_obra', 'dias_atraso'].includes(col.key)) {
                        cell.alignment = { horizontal: 'right' };
                    }
                    
                    // Formato de fechas
                    if (cell.value && typeof cell.value === 'string' && 
                        cell.value.match(/\d{2}\/\d{2}\/\d{4}/)) {
                        cell.numFmt = 'dd/mm/yyyy';
                    }
                });
                
                // Resaltar filas con atraso
                if (item.dias_atraso > 0) {
                    row.eachCell((cell) => {
                        cell.font = { color: { argb: 'FFFF0000' } }; // Rojo para atrasos
                    });
                }
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
            $("#esperarCargo").css("opacity", "0").fadeOut();
        }, 100);

    } catch (error) {
        console.error('Error al generar el Excel:', error);
        $("#esperarCargo").css("opacity", "0").fadeOut();
        alert('Ocurrió un error al generar el archivo. Por favor intente nuevamente.');
    }
}

// Ejemplo de cómo llamar a la función con tus datos
// crearReporteExcel(tusDatos);



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