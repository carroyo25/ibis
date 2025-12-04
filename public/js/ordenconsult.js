$(function(){

    listarOrdenes();

    $(".dataProceso_2, #tablaDetalles").css("pointer-events","none");

    $("#tablaPrincipal tbody").on("click","tr", function (e) {
        e.preventDefault();

        autorizado = $(this).data('finanzas')+$(this).data('logistica')+$(this).data('operaciones');

        $.post(RUTA+"firmas/ordenId", {id:$(this).data("indice")},
            function (data, textStatus, jqXHR) {

                let estado = "textoCentro " + data.cabecera[0].estado;
                let total = parseFloat(data.cabecera[0].total_multiplicado).toFixed(2);
                total =  formatoNumeroConComas(total,2,'.',',');

                $("#codigo_costos").val(data.cabecera[0].ncodcos);
                $("#codigo_area").val(data.cabecera[0].ncodarea);
                $("#codigo_transporte").val(data.cabecera[0].ctiptransp);
                $("#codigo_tipo").val(data.cabecera[0].ntipmov);
                $("#codigo_almacen").val(data.cabecera[0].ncodalm);
                $("#codigo_pedido").val(data.cabecera[0].id_refpedi);
                $("#codigo_orden").val(data.cabecera[0].id_regmov);
                $("#codigo_estado").val(data.cabecera[0].nEstadoDoc);
                $("#codigo_entidad").val(data.cabecera[0].id_centi);
                $("#codigo_moneda").val(data.cabecera[0].ncodmon);
                $("#codigo_pago").val(data.cabecera[0].ncodpago);
                $("#ruc_entidad").val(data.cabecera[0].cnumdoc);
                $("#direccion_entidad").val(data.cabecera[0].cviadireccion);
                $("#telefono_entidad").val(data.cabecera[0].ctelefono1);
                $("#correo_entidad").val(data.cabecera[0].mail_entidad);
                $("#codigo_verificacion").val(data.cabecera[0].cverificacion);
                $("#telefono_contacto").val(data.cabecera[0].ctelefono1);
                $("#correo_contacto").val(data.cabecera[0].cemail);
                $("#proforma").val(data.cabecera[0].cnumcot);
                $("#retencion").val(data.cabecera[0].nagenret);
                $("#nivel_atencion").val(data.cabecera[0].nivelAten);
                $("#numero").val(data.cabecera[0].cnumero);
                $("#emision").val(data.cabecera[0].ffechadoc);
                $("#costos").val(data.cabecera[0].costos);
                $("#area").val(data.cabecera[0].area);
                $("#concepto").val(data.cabecera[0].concepto);
                $("#detalle").val(data.cabecera[0].detalle);
                $("#moneda").val(data.cabecera[0].nombre_moneda);
                $("#total").val(total);
                $("#total_adicional").val(data.total_adicionales);
                $("#tipo").val(data.cabecera[0].tipo);
                $("#fentrega").val(data.cabecera[0].ffechaent);
                $("#cpago").val(data.cabecera[0].pagos);
                $("#estado").val(data.cabecera[0].descripcion_estado);
                $("#entidad").val(data.cabecera[0].crazonsoc);
                $("#atencion").val(data.cabecera[0].cnombres);
                $("#transporte").val(data.cabecera[0].transporte);
                $("#lentrega").val(data.cabecera[0].lentrega);
                $("#total_numero").val(data.cabecera[0].total_multiplicado);
                $("#ncotiz").val(data.cabecera[0].cnumcot);
                $("#tcambio").val(data.cabecera[0].ntcambio);
                $("#referencia").val(data.cabecera[0].cReferencia);
                $("#dias").val(data.cabecera[0].nplazo);

                $("#estado")
                    .removeClass()
                    .addClass(estado);

                $("#tablaDetalles tbody")
                    .empty()
                    .append(data.detalles);

                $("#tablaComentarios tbody")
                    .empty()
                    .append(data.comentarios);

                $("#sw").val(1);

                if (data.bocadillo != 0) {
                    $(".button__comment")
                        .text(data.bocadillo)
                        .show();
                }

                if (data.cabecera[0].nigv != 0) {
                    $("#si").prop("checked", true);
               }else {
                    $("#no").prop("checked", true);
               };

                accion = "u";
                grabado = true;
                $("#proceso").fadeIn();

            },
            "json"
        );
    
        return false;
    });

    $("#closeProcess").click(function (e) { 
        e.preventDefault();

        $("#proceso").fadeOut();

        return false;
    });

    $("#btnConsult").click(function (e) { 
        e.preventDefault();

        let str = $("#formConsulta").serialize();

        $("#esperar").fadeIn()

        $.post(RUTA+"ordenconsult/listaFiltrada",str,
            function (data, textStatus, jqXHR) {
                $("#tablaPrincipal tbody")
                    .empty()
                    .append(data);

                $("#esperar").fadeOut().promise().done(function(){
                    iniciarPaginadorConsulta();
                });
            },
            "text"
        );
        
        return false;
    });

    $("#verDetalles").click(function (e) { 
        e.preventDefault();
        
        $.post(RUTA+"ordenseg/consulta",{id:$("#codigo_orden").val()},
            function (data, textStatus, jqXHR) {
                
                $("#fecha_documento").text(data.info[0].emision);
                $("#envio").text(data.info[0].envio);
                $("#elaborado").text(data.info[0].cnameuser);
                $("#firma_logistica").text(data.info[0].fecha_logistica);
                $("#firma_operaciones").text(data.info[0].fecha_operaciones);
                $("#firma_finanzas").text(data.info[0].fecha_finanzas);

                $("#lista_pedidos tbody")
                    .empty()
                    .append(data.pedidos);

                $("#documentos_adjuntos")
                    .empty()
                    .append(data.adjuntos);

                $("#detalles").fadeIn();
            },
            "json"
        );

        return false;
    });

    $("#closeDocument").click(function (e) { 
        e.preventDefault();

        $(".seccion4 iframe").attr("src","");
        $("#detalles").fadeOut();
        
        return false;
    });

    $("#lista_pedidos tbody").on("click","a", function (e) {
        e.preventDefault();

        $.post(RUTA+"ordenseg/vistaPedido", {id:$(this).attr("href")},
            function (data, textStatus, jqXHR){
                let archivo = RUTA+"public/documentos/temp/"+data
                $(".seccion4 iframe")
                    .attr("src","")
                    .attr("src",archivo);
            },
            "text"
        );

        return false;
    });

    $("#btnConsulta").click(function (e) { 
        e.preventDefault();
        
        let srt = $("#formConsulta").serialize();

        $.post(RUTA+"ordenseg/filtroOrdenes", srt,
            function (data, text, requestXHR) {
                $("#tablaPrincipal tbody")
                    .empty()
                    .append(data);
            },
            "text"
        );
        
        return false
    });

    $("#documentos_adjuntos").on('click','a', function(e) {
        e.preventDefault();

        let adjunto = RUTA+'/public/documentos/ordenes/adjuntos/'+$(this).attr("href");

        $(".seccion4 iframe").attr("src","").attr("src",adjunto);

        return false;
    });

    $("#preview").click(function (e) { 
        e.preventDefault();

        let result = {};
        
        $.each($("#formProceso").serializeArray(),function(){
            result[this.name] = this.value;
        })

        $.post(RUTA+"ordenedit/vistaPreliminar", {cabecera:result,condicion:0,detalles:JSON.stringify(detalles())},
                function (data, textStatus, jqXHR) {
                    $(".ventanaVistaPrevia iframe")
                        .attr("src","")
                        .attr("src","public/documentos/ordenes/vistaprevia/"+data);
                    
                    $("#vista_previa").val(data);    
                    $("#vistaprevia").fadeIn();
                },
                "text"
            );

        return false;
    });

    $("#closePreview").click(function (e) { 
        e.preventDefault();

        $(".ventanaVistaPrevia iframe").attr("src","");
        $("#vistaprevia").fadeOut();

        return false;
    });

    $("#btnExporta").click(function(e){
        e.preventDefault();

        $.post(RUTA+"ordenconsult/exporta", {detalles:JSON.stringify(exports())},
            function (data, textStatus, jqXHR) {
                window.location.href = data.documento;
            },
            "json"
        );

        return false;
    });
})

exports = () => {
    DATA = [];
    let TABLA = $("#tablaPrincipal tbody >tr");

    TABLA.each(function(){
        let ITEM        = $(this).find('td').eq(0).text(),
            EMISION     = $(this).find('td').eq(1).text(),
            DESCRIPCION = $(this).find('td').eq(2).text(),
            COSTO       = $(this).find('td').eq(3).text(),
            AREA        = $(this).find('td').eq(4).text(),
            PROVEEDOR   = $(this).find('td').eq(5).text(),
            SOLES       = $(this).find('td').eq(6).text(),
            DOLARES     = $(this).find('td').eq(7).text(),
            LOGISTICA   = $(this).data('logistica'),
            OPERACIONES = $(this).data('operaciones'),
            FINANZAS    = $(this).data('finanzas');  

        let item= {};
        
        item['item']         = ITEM;
        item['emision']      = EMISION;
        item['descripcion']  = DESCRIPCION;
        item['costo']        = COSTO;
        item['area']         = AREA;
        item['proveedor']    = PROVEEDOR;
        item['soles']        = SOLES;
        item['dolares']      = DOLARES;
        item['logistica']    = LOGISTICA;
        item['operaciones']  = OPERACIONES;
        item['finanzas']     = FINANZAS;
        
        DATA.push(item);
    });

    return DATA;
}

detalles = () => {
    DATA = [];
    let TABLA = $("#tablaDetalles tbody >tr");

    TABLA.each(function(){
        let ITEM        = $(this).find('td').eq(1).text(),
            CODIGO      = $(this).find('td').eq(2).text(),
            DESCRIPCION = $(this).find('td').eq(3).text(),
            UNIDAD      = $(this).find('td').eq(4).text(),
            CANTIDAD    = $(this).find('td').eq(5).children().val(),
            PRECIO      = $(this).find('td').eq(6).children().val(),
            IGV         = 0.18,
            TOTAL       = $(this).find('td').eq(7).text(),
            NROPARTE    = $(this).find('td').eq(8).text(),
            PEDIDO      = $(this).find('td').eq(9).text(),
            CODPROD     = $(this).data('codprod'),
            MONEDA      = $("#codigo_moneda").val(),
            ITEMPEDIDO  = $(this).data('itped'),
            GRABAR      = $(this).data('grabado'),
            CANTPED     = $(this).data('cant'),
            ITEMORDEN   = $(this).data('itord'),
            SALDO       = $(this).data('cant')-$(this).find('td').eq(5).children().val(),
            DETALLES    = $(this).find('td').eq(10).children().val();

        let item= {};
        
        //if (GRABAR == 0) {
            item['item']        = ITEM;
            item['codigo']      = CODIGO;
            item['descripcion'] = DESCRIPCION;
            item['unidad']      = UNIDAD;
            item['cantidad']    = CANTIDAD;
            item['precio']      = PRECIO;
            item['igv']         = IGV;
            item['total']       = TOTAL;
            item['nroparte']    = NROPARTE;
            item['pedido']      = PEDIDO;
            item['codprod']     = CODPROD;
            item['moneda']      = MONEDA;
            item['itped']       = ITEMPEDIDO;
            item['grabado']     = GRABAR;
            item['cantped']     = CANTPED;
            item['itemorden']   = ITEMORDEN;
            item['saldo']       = SALDO;
            item['detalles']    = DETALLES;

            DATA.push(item);
        //}
    });

    return DATA;
}

listarOrdenes = async () => {
    try {
        let formData = new FormData();
        formData.append('orden',document.getElementById('ordenSearch').value);
        formData.append('cc',document.getElementById('costosSearch').value);
        formData.append('mes',document.getElementById('mesSearch').value);
        formData.append('anio',document.getElementById('anioSearch').value);

        const response = await fetch(RUTA + "ordenconsult/listaOrdenesPaginador", {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        const tablaCuerpo = document.getElementById("tablaPrincipalCuerpo");

        if (!tablaCuerpo) {
            throw new Error("Element with ID 'tablaPrincipalCuerpo' not found");
        }

        tablaCuerpo.innerHTML = "";

        const estados = {
            49: "procesando",
            59: "firmas",
            60: "recepcion",
            62: "despacho",
            105: "anulado"
        };

        data.datos.forEach(e =>{

            const tr = document.createElement("tr");
            tr.dataset.indice = e.id_regmov;
            tr.dataset.estado = e.nEstadoDoc;
            tr.dataset.finanzas = e.nfirmaLog;
            tr.dataset.logistica = e.nfirmaOpe;
            tr.dataset.operaciones = e.nfirmaFin;

            tr.innerHTML = `<td class="textoCentro">${e.cnumero}</td>
                            <td class="textoCentro">${e.ffechadoc}</td>
                            <td class="pl20px">${e.concepto}</td>
                            <td class="pl20px">${e.ccodproy}</td>
                            <td class="pl20px">${e.area}</td>
                            <td class="pl20px">${e.proveedor}</td>
                            <td class="textoDerecha">${e.ncodmon == 20 ? "S/." + e.ntotal : " "}</td>
                            <td class="textoDerecha">${e.ncodmon == 21 ? "$" + e.ntotal : " "}</td>
                            <td class="textoCentro ${e.atencion.toLowerCase()}">${e.atencion}</td>
                            <td class="textoCentro ${estados[e.nEstadoDoc].toLowerCase()}">${estados[e.nEstadoDoc].toUpperCase()}</td>
                            <td class="textoCentro">${e.nfirmaLog == null ? '<i class="far fa-square"></i>': '<i class="far fa-check-square"></i>'}</td>
                            <td class="textoCentro">${e.nfirmaOpe == null ? '<i class="far fa-square"></i>': '<i class="far fa-check-square"></i>'}</td>
                            <td class="textoCentro">${e.nfirmaFin == null ? '<i class="far fa-square"></i>': '<i class="far fa-check-square"></i>'}</td>`
                            

            tr.classList.add("pointer");

            tablaCuerpo.appendChild(tr);
        });

        $("#esperar").fadeOut().promise().done(function(){
            iniciarPaginadorConsulta();
        });
    } catch (error) {
        mostrarMensaje('No hay registros para procesar','mensaje_error');
        console.log(error);
        $("#esperar").fadeOut();
    }
    
}

function iniciarPaginadorConsulta() {
    const content = document.querySelector('.tablaBusqueda'); 
    const contentTarget = document.querySelector('.paginadorWrap');
    let itemsPerPage = 25;
    let currentPage = 0;
    const maxVisiblePages = 15;
    
    // Obtener todos los tr del tbody (tus datos ya cargados)
    const items = Array.from(document.querySelectorAll('#tablaPrincipalCuerpo tr'));
    let filteredItems = [...items];
    let activeFilters = {};

    // Función optimizada para obtener valores únicos
    function getUniqueColumnValues(columnIndex) {
        // Si son muchos datos, usar estrategia optimizada
        if (items.length > 1000) {
            return getUniqueValuesOptimized(columnIndex);
        }
        
        // Método original para tablas pequeñas
        const values = items.map(item => {
            const cells = item.getElementsByTagName('td');
            return cells[columnIndex] ? cells[columnIndex].textContent.trim() : '';
        });
        
        const uniqueValues = [...new Set(values)].filter(value => value !== '').sort();
        
        // Limitar a 1000 valores máximo
        return uniqueValues.slice(0, 1000);
    }

    function getUniqueValuesOptimized(columnIndex) {
        console.log(`⚡ Optimizando columna ${columnIndex} con ${items.length} filas`);
        
        const valueSet = new Set();
        const maxValues = 800;
        
        for (let i = 0; i < 500 && valueSet.size < maxValues; i++) {
            addValueFromItem(items[i], columnIndex, valueSet);
        }
        
        for (let i = Math.max(500, items.length - 300); i < items.length && valueSet.size < maxValues; i++) {
            addValueFromItem(items[i], columnIndex, valueSet);
        }
        
        for (let i = 0; i < 200 && valueSet.size < maxValues; i++) {
            const randomIndex = Math.floor(Math.random() * items.length);
            addValueFromItem(items[randomIndex], columnIndex, valueSet);
        }
        
        const uniqueValues = Array.from(valueSet).sort();
        console.log(`📊 Optimizado: ${uniqueValues.length} valores únicos de ${items.length} filas`);
        
        return uniqueValues;
    }

    function addValueFromItem(item, columnIndex, valueSet) {
        const cells = item.getElementsByTagName('td');
        const value = cells[columnIndex] ? cells[columnIndex].textContent.trim() : '';
        if (value) {
            valueSet.add(value);
        }
    }

    function createExcelStyleFilters() {
        const headerRows = content.querySelectorAll('thead tr');
        const firstHeaderRow = headerRows[0];
        
        console.log('Filas en header:', headerRows.length);
        console.log('Celdas en PRIMERA fila:', firstHeaderRow.getElementsByTagName('th').length);
        
        const headerCells = firstHeaderRow.getElementsByTagName('th');
        
        Array.from(headerCells).forEach((headerCell, index) => {
            const hasFilter = headerCell.hasAttribute('data-filtro');
            
            if (hasFilter) {
                console.log(`✅ Agregando filtro a columna ${index}: ${headerCell.textContent.trim()}`);
                
                const headerContent = headerCell.innerHTML;
                headerCell.innerHTML = '';
                
                const headerContainer = document.createElement('div');
                headerContainer.style.display = 'flex';
                headerContainer.style.alignItems = 'center';
                headerContainer.style.justifyContent = 'space-between';
                headerContainer.style.gap = '5px';
                headerContainer.style.width = '100%';
                headerContainer.style.minHeight = '100%';
                
                const headerText = document.createElement('span');
                headerText.innerHTML = headerContent;
                headerText.style.flex = '1';
                headerText.style.textAlign = 'center';
                
                const filterButton = document.createElement('button');
                filterButton.classList.add('excel-filter-btn');
                filterButton.innerHTML = '▾';
                filterButton.title = `Filtrar ${headerCell.textContent.trim()}`;
                filterButton.style.flexShrink = '0';
                filterButton.style.marginLeft = 'auto';
                
                const filterPanel = document.createElement('div');
                filterPanel.classList.add('excel-filter-panel');
                filterPanel.style.display = 'none';
                
                const checkboxesContainer = document.createElement('div');
                checkboxesContainer.classList.add('filter-checkboxes');
                checkboxesContainer.innerHTML = '<div class="loading-message">👆 Haz clic para cargar valores</div>';
                
                let valuesLoaded = false;
                let allUniqueValues = [];
                
                const loadFilterValues = () => {
                    if (valuesLoaded) return;
                    
                    checkboxesContainer.innerHTML = '<div class="loading-message">⏳ Cargando valores...</div>';
                    
                    setTimeout(() => {
                        allUniqueValues = getUniqueColumnValues(index);
                        valuesLoaded = true;
                        renderFilterValues(allUniqueValues);
                    }, 50);
                };
                
                const renderFilterValues = (values) => {
                    checkboxesContainer.innerHTML = '';
                    
                    if (values.length === 0) {
                        checkboxesContainer.innerHTML = '<div class="no-values">No hay valores para filtrar</div>';
                        return;
                    }
                    
                    if (values.length >= 800 && items.length > 1000) {
                        const warning = document.createElement('div');
                        warning.style.cssText = 'font-size: 10px; color: #e74c3c; padding: 5px; background: #ffeaa7; margin-bottom: 5px; border-radius: 3px;';
                        warning.innerHTML = `⚠️ <strong>${values.length} valores únicos</strong> - Usa la búsqueda para filtrar`;
                        checkboxesContainer.appendChild(warning);
                    }
                    
                    // ✅ AGREGADO: Debug para columnas problemáticas
                    if (index === 2 || index === 5) { // Descripción (2) y Proveedor (5)
                        console.log(`🔍 DEBUG Columna ${index} - Primeros 5 valores:`, values.slice(0, 5));
                    }
                    
                    values.forEach(value => {
                        if (value.trim() === '') return;
                        
                        const label = document.createElement('label');
                        label.classList.add('filter-checkbox-label');
                        
                        const checkbox = document.createElement('input');
                        checkbox.type = 'checkbox';
                        checkbox.value = value;
                        checkbox.checked = false;
                        
                        const span = document.createElement('span');
                        span.textContent = value;
                        span.title = value;
                        
                        label.appendChild(checkbox);
                        label.appendChild(span);
                        checkboxesContainer.appendChild(label);
                    });
                    
                    console.log(`✅ Filtro columna ${index} cargado: ${values.length} valores`);
                };
                
                const panelControls = document.createElement('div');
                panelControls.classList.add('filter-panel-controls');
                
                const searchInput = document.createElement('input');
                searchInput.type = 'text';
                searchInput.placeholder = 'Buscar...';
                searchInput.classList.add('filter-search');
                searchInput.addEventListener('input', function(e) {
                    e.stopPropagation();
                    if (valuesLoaded) {
                        filterCheckboxes(this.value, checkboxesContainer);
                    }
                });
                
                const selectAllBtn = document.createElement('button');
                selectAllBtn.textContent = 'Seleccionar Todo';
                selectAllBtn.classList.add('filter-action-btn');
                selectAllBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    if (valuesLoaded) {
                        toggleAllCheckboxes(checkboxesContainer, true);
                    }
                });
                
                const clearAllBtn = document.createElement('button');
                clearAllBtn.textContent = 'Limpiar';
                clearAllBtn.classList.add('filter-action-btn');
                clearAllBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    if (valuesLoaded) {
                        toggleAllCheckboxes(checkboxesContainer, false);
                    }
                });
                
                const applyBtn = document.createElement('button');
                applyBtn.textContent = 'Aplicar';
                applyBtn.classList.add('filter-action-btn', 'apply-btn');
                applyBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    if (valuesLoaded) {
                        applyColumnFilter(index, checkboxesContainer);
                        filterPanel.style.display = 'none';
                    }
                });
                
                const cancelBtn = document.createElement('button');
                cancelBtn.textContent = 'Cancelar';
                cancelBtn.classList.add('filter-action-btn', 'cancel-btn');
                cancelBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    filterPanel.style.display = 'none';
                });
                
                panelControls.appendChild(searchInput);
                panelControls.appendChild(selectAllBtn);
                panelControls.appendChild(clearAllBtn);
                panelControls.appendChild(applyBtn);
                panelControls.appendChild(cancelBtn);
                
                filterPanel.appendChild(checkboxesContainer);
                filterPanel.appendChild(panelControls);
                
                filterPanel.addEventListener('click', (e) => {
                    e.stopPropagation();
                });
                
                filterButton.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const isVisible = filterPanel.style.display === 'block';
                    closeAllFilterPanels();
                    filterPanel.style.display = isVisible ? 'none' : 'block';
                    
                    if (!isVisible && !valuesLoaded) {
                        loadFilterValues();
                    }
                    
                    const rect = filterButton.getBoundingClientRect();
                    filterPanel.style.top = `${rect.bottom + 5}px`;
                    filterPanel.style.left = `${rect.left}px`;
                });
                
                headerContainer.appendChild(headerText);
                headerContainer.appendChild(filterButton);
                headerCell.appendChild(headerContainer);
                headerCell.appendChild(filterPanel);
                
                headerCell.style.position = 'relative';
            }
        });

        document.addEventListener('click', closeAllFilterPanels);
    }

    function filterCheckboxes(searchTerm, container) {
        const labels = container.getElementsByTagName('label');
        let visibleCount = 0;
        
        Array.from(labels).forEach(label => {
            if (!label.querySelector('input[type="checkbox"]')) {
                label.style.display = 'flex';
                return;
            }
            
            const text = label.textContent.toLowerCase();
            const matches = text.includes(searchTerm.toLowerCase());
            label.style.display = matches ? 'flex' : 'none';
            
            if (matches) visibleCount++;
        });
        
        const existingMessage = container.querySelector('.no-results-message');
        if (visibleCount === 0 && searchTerm.trim() !== '') {
            if (!existingMessage) {
                const message = document.createElement('div');
                message.classList.add('no-results-message');
                message.style.cssText = 'padding: 10px; text-align: center; color: #666; font-size: 12px;';
                message.textContent = 'No se encontraron resultados';
                container.appendChild(message);
            }
        } else if (existingMessage) {
            existingMessage.remove();
        }
    }

    function toggleAllCheckboxes(container, select) {
        const checkboxes = container.querySelectorAll('input[type="checkbox"]');
        Array.from(checkboxes).forEach(checkbox => {
            if (checkbox.parentElement.style.display !== 'none') {
                checkbox.checked = select;
            }
        });
    }

    function applyColumnFilter(columnIndex, checkboxesContainer) {
        const checkboxes = checkboxesContainer.querySelectorAll('input[type="checkbox"]');
        const selectedValues = Array.from(checkboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.value);
        
        const allCheckboxes = Array.from(checkboxesContainer.querySelectorAll('input[type="checkbox"]'));
        const visibleCheckboxes = allCheckboxes.filter(cb => 
            cb.parentElement.style.display !== 'none'
        );
        
        console.log(`🎯 Aplicando filtro columna ${columnIndex}:`);
        console.log('   - Valores seleccionados:', selectedValues.length);
        console.log('   - Checkboxes VISIBLES:', visibleCheckboxes.length);
        
        // ✅ AGREGADO: Debug específico para columnas problemáticas
        if (columnIndex === 2 || columnIndex === 5) {
            console.log('   - Primeros 3 valores seleccionados:', selectedValues.slice(0, 3));
            
            // Verificar coincidencia con datos reales
            if (selectedValues.length > 0) {
                const sampleItem = items[0];
                const cells = sampleItem.getElementsByTagName('td');
                const sampleValue = cells[columnIndex] ? cells[columnIndex].textContent.trim() : '';
                console.log('   - Valor de muestra en tabla:', sampleValue);
                console.log('   - Coincide con seleccionados?', selectedValues.includes(sampleValue));
            }
        }
        
        if (selectedValues.length === 0) {
            console.log('   🗑️ Eliminando filtro (ningún valor seleccionado)');
            delete activeFilters[columnIndex];
        } else if (selectedValues.length === visibleCheckboxes.length) {
            console.log('   🗑️ Eliminando filtro (todos los valores visibles seleccionados)');
            delete activeFilters[columnIndex];
        } else {
            console.log('   ✅ Guardando filtro activo');
            activeFilters[columnIndex] = selectedValues;
        }
        
        applyFilters();
        updateFilterButtonState(columnIndex, selectedValues.length > 0 && selectedValues.length !== visibleCheckboxes.length);
    }

    function updateFilterButtonState(columnIndex, isFiltered) {
        const filterButtons = document.querySelectorAll('.excel-filter-btn');
        filterButtons.forEach((button) => {
            const filterContainer = button.closest('th');
            const headerCells = Array.from(filterContainer.parentNode.children);
            const cellIndex = headerCells.indexOf(filterContainer);
            
            if (cellIndex === columnIndex) {
                button.classList.toggle('filter-active', isFiltered);
                console.log(`🎨 Botón filtro columna ${columnIndex}: ${isFiltered ? 'ACTIVO' : 'inactivo'}`);
            }
        });
    }

    function closeAllFilterPanels() {
        const panels = document.querySelectorAll('.excel-filter-panel');
        panels.forEach(panel => {
            panel.style.display = 'none';
        });
    }

    function applyFilters() {
        console.log('🔧 APLICANDO FILTROS:', activeFilters);
        
        // ✅ MODIFICADO: Función de filtrado mejorada para columnas problemáticas
        filteredItems = items.filter(item => {
            const cells = item.getElementsByTagName('td');
            let pasaTodosLosFiltros = true;
            
            for (const [columnIndex, filterValues] of Object.entries(activeFilters)) {
                const cellIndex = parseInt(columnIndex);
                if (cells[cellIndex]) {
                    const cellText = cells[cellIndex].textContent.trim();
                    
                    if (Array.isArray(filterValues)) {
                        // ✅ AGREGADO: Debug para columnas problemáticas
                        if (cellIndex === 2 || cellIndex === 5) {
                            const coincide = filterValues.includes(cellText);
                            if (!coincide) {
                                console.log(`❌ Fila NO pasa filtro columna ${cellIndex}:`);
                                console.log('   - Valor en tabla:', cellText);
                                console.log('   - Valores permitidos:', filterValues.slice(0, 3));
                                pasaTodosLosFiltros = false;
                                break;
                            }
                        } else {
                            if (!filterValues.includes(cellText)) {
                                pasaTodosLosFiltros = false;
                                break;
                            }
                        }
                    }
                }
            }
            return pasaTodosLosFiltros;
        });
        
        console.log(`📊 RESULTADOS: ${filteredItems.length} de ${items.length} filas después de filtrar`);
        
        currentPage = 0;
        createPageButtons();
        showPage(currentPage);
    }

    function clearAllFilters() {
        console.log('🧹 LIMPIANDO TODOS LOS FILTROS');
        activeFilters = {};
        filteredItems = [...items];
        
        const checkboxes = document.querySelectorAll('.filter-checkboxes input[type="checkbox"]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        
        const filterButtons = document.querySelectorAll('.excel-filter-btn');
        filterButtons.forEach(button => {
            button.classList.remove('filter-active');
        });
        
        const labels = document.querySelectorAll('.filter-checkbox-label');
        labels.forEach(label => {
            label.style.display = 'flex';
        });
        
        currentPage = 0;
        createPageButtons();
        showPage(currentPage);
    }

    function showPage(page) {
        const startIndex = page * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        
        items.forEach(item => {
            item.style.display = 'none';
        });
        
        filteredItems.forEach((item, index) => {
            if (index >= startIndex && index < endIndex) {
                item.style.display = '';
            }
        });
    }

    function createPageButtons() {
        const totalPages = Math.ceil(filteredItems.length / itemsPerPage);
        let paginationContainer = document.querySelector('.pagination');

        if (!paginationContainer) {
            paginationContainer = document.createElement('div');
            paginationContainer.classList.add('pagination');
            contentTarget.appendChild(paginationContainer);
        } else {
            paginationContainer.innerHTML = '';
        }

        const resultsCounter = document.createElement('div');
        resultsCounter.classList.add('results-counter');
        //resultsCounter.textContent = `Mostrando ${filteredItems.length} de ${items.length} resultados`;
        paginationContainer.appendChild(resultsCounter);

        if (Object.keys(activeFilters).length > 0) {
            const clearFiltersButton = document.createElement('button');
            clearFiltersButton.textContent = 'Limpiar Todos los Filtros';
            clearFiltersButton.classList.add('clear-filters-btn');
            clearFiltersButton.addEventListener('click', clearAllFilters);
            paginationContainer.appendChild(clearFiltersButton);
        }

        const itemsPerPageSelect = document.createElement('select');
        const options = [25, 50, 100, 150, 200, 250, 300];

        options.forEach(option => {
            const opt = document.createElement('option');
            opt.value = option;
            opt.textContent = option;
            if (option === itemsPerPage) opt.selected = true;
            itemsPerPageSelect.appendChild(opt);
        });

        itemsPerPageSelect.addEventListener("change", function() {
            itemsPerPage = parseInt(this.value);
            currentPage = 0;
            createPageButtons();
            showPage(currentPage);
        });

        paginationContainer.appendChild(itemsPerPageSelect);

        const firstButton = document.createElement('button');
        firstButton.textContent = 'Primera';
        firstButton.disabled = currentPage === 0;
        firstButton.addEventListener('click', () => {
            currentPage = 0;
            createPageButtons();
            showPage(currentPage);
        });
        paginationContainer.appendChild(firstButton);

        const prevButton = document.createElement('button');
        prevButton.textContent = 'Anterior';
        prevButton.disabled = currentPage === 0;
        prevButton.addEventListener('click', () => {
            if (currentPage > 0) {
                currentPage--;
                createPageButtons();
                showPage(currentPage);
            }
        });
        paginationContainer.appendChild(prevButton);

        const startPage = Math.max(0, currentPage - Math.floor(maxVisiblePages / 2));
        const endPage = Math.min(totalPages, startPage + maxVisiblePages);

        for (let i = startPage; i < endPage; i++) {
            const pageButton = document.createElement('button');
            pageButton.textContent = i + 1;
            pageButton.classList.toggle('active', i === currentPage);
            pageButton.addEventListener('click', () => {
                currentPage = i;
                createPageButtons();
                showPage(currentPage);
            });
            paginationContainer.appendChild(pageButton);
        }

        const nextButton = document.createElement('button');
        nextButton.textContent = 'Siguiente';
        nextButton.disabled = currentPage >= totalPages - 1;
        nextButton.addEventListener('click', () => {
            if (currentPage < totalPages - 1) {
                currentPage++;
                createPageButtons();
                showPage(currentPage);
            }
        });
        paginationContainer.appendChild(nextButton);

        const lastButton = document.createElement('button');
        lastButton.textContent = 'Última';
        lastButton.disabled = currentPage >= totalPages - 1;
        lastButton.addEventListener('click', () => {
            currentPage = totalPages - 1;
            createPageButtons();
            showPage(currentPage);
        });
        paginationContainer.appendChild(lastButton);

        if (totalPages <= 1) {
            firstButton.disabled = true;
            prevButton.disabled = true;
            nextButton.disabled = true;
            lastButton.disabled = true;
        }
    }

    // Inicializar el paginador
    createExcelStyleFilters();
    createPageButtons();
    showPage(currentPage);
}