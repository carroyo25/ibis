$(() => {
    /*$("#esperar").fadeOut();
    
    let accion = "u",flag=false;

    let seriesData = [1,2,3,4,5,6,7,8,9,10,11,12];*/

    listarRegistrosCombustible();


    $("#esperar").fadeOut();

    $("#nuevoRegistro").click(function (e) { 
        e.preventDefault();
        
        accion = "n";
        $("#dialogo_registro").fadeIn();

    });

    $("#btn_consumo_aceptar").click(function (e) { 
        e.preventDefault();

        try {
            if ($("#tipo").val() == -1) throw new Error("Seleccione el tipo de operación");
            if ($("#codigo").val() == "") throw new Error("Selecione el codigo del item");
            if ($("#cantidad").val() == 0) throw new Error("Ingrese una cantidad válida");
            if ($("#documento").val() == "") throw new Error("Ingrese el número de documento");
            if ($("#proyecto").val() == -1) throw new Error("Seleccion el proyecto");
            if ($("#area").val() == -1) throw new Error("Seleccione una área");
            if ($("#guia").val() == -1) throw new Error("Indique el número de guia del proveedor");

            //serializar los formulario en javascript
            const datos = new URLSearchParams(new FormData(document.getElementById("form__combustible")));

            fetch(RUTA+'combustible/registro',{
                method: 'POST',
                body:datos
            })
            .then(response => response.json())
            .then(data => {
                $("#dialogo_registro").fadeOut();
                accion = "u";
            });
            
        } catch (error) {
            mostrarMensaje(error.message,"mensaje_error");
        }
        return false;
    });

    $("#btn_consumo_cancelar").click(function (e) { 
        e.preventDefault();

        $("#dialogo_registro").fadeOut();

        return false;
    });

    $("#codigo").keypress(function (e) { 
        if(e.which == 13) {
            try {
                let codigo = $(this).val(),
                    formdata = new FormData();

                if ( codigo == "" ) throw new Error("Ingrese el codigo a registrar");

                formdata.append('codigo',codigo);

                $("#esperarCargo").css("opacity","1").fadeIn();

                fetch (RUTA+"combustible/codigo",{
                    method: "POST",
                    body: formdata
                })
                    .then((response)=> {
                        return response.json();
                    })
                    .then((json)=> {
                        $("#esperarCargo").css("opacity","0").fadeOut();
                        $("#descripcion").val(json.datos[0].cdesprod);
                        $("#unidad").val(json.datos[0].cdesmed);
                        $("#codigo_producto").val(json.datos[0].id_cprod)
                    })
                    .catch((err)=> {
                        console.log(err);
                    });
            } catch (error) {
                mostrarMensaje(error.message,"mensaje_error");
            }
        }
    });

    $("#documento").keypress(function (e) { 
        if(e.which == 13) {
            try {
                let documento = $(this).val(),
                    formdata = new FormData();

                if ( documento == "" ) throw new Error("Ingrese el N° de documento");

                formdata.append('documento',documento);

                $("#esperarCargo").css("opacity","1").fadeIn();

                fetch (RUTA+"combustible/documento",{
                    method: "POST",
                    body: formdata
                })
                    .then((response)=> {
                        return response.json();
                    })
                    .then((data)=> {
                        if(data.registrado) {
                            $("#trabajador").val(data.datos[0].nombres+' '+data.datos[0].paterno+' '+data.datos[0].materno);
                        }else{
                            mostrarMensaje("Trabajador no registrado","mensaje_error");
                        }
                        
                    })
                    .catch((err)=> {
                        console.log(err);
                    });

            } catch (error) {
                mostrarMensaje(error.message,"mensaje_error");
            }
        }
    });

    $("#kardexDetails").click(function (e) { 
        e.preventDefault();
        
        $("#filtros").fadeIn();

        return false;
    });

    $("#closeInform").click(function (e) { 
        e.preventDefault();
        
        $("#filtros").fadeOut();

        return false;
    });

    $("#tipo_item").change(function (e) { 
        e.preventDefault();

        if( $('select[name="tipo_item"] option:selected').val() !== "0"){
            let formData = new FormData(),
                seriesIngreso = [],
                seriesSalida = [];

            formData.append("item",$('select[name="tipo_item"] option:selected').val());

            fetch(RUTA+'combustible/reporte',{
                method: 'POST',
                body: formData
            })
            .then(reponse => reponse.json())
            .then(data =>{
                let stock_inicial = data.stock_inicial == null ? 0 : data.stock_inicial,
                    ingreso_mes_actual = data.ingreso_mes_actual == null ? 0 : data.ingreso_mes_actual,
                    consumo_mes_actual = data.consumo_mes_actual == null ? 0 : data.consumo_mes_actual,
                    consolidado_anual = data.consolidado_anual == null ? 0 : data.consolidado_anual;

                //seriesIngreso = data.valores_ingreso;

                $("#consolidadoAnual").text(consolidado_anual);
                $("#stockInicial").text(stock_inicial);
                $("#ingresomesactual").text(ingreso_mes_actual);
                $("#cantidadconsumo").text(consumo_mes_actual);
                $("#stockfinal").text((stock_inicial+ingreso_mes_actual)-consumo_mes_actual);

                /*data.valores[0].forEach(valor =>{
                    seriesData.push({
                        name: valor['nombre'],
                        data: valor['series']
                    });
                })*/

                data.valores_ingreso[0].forEach(valor => {
                    seriesIngreso.push({
                        
                    })
                });

                barras(seriesIngreso);
            })
        };
        
        return false;
    });

    $("#reportExport").click(function (e) { 
        e.preventDefault();

        $("#esperar").css("opacity","1").fadeIn();

        $.post(RUTA+"combustible/exporta", {detalles:JSON.stringify(detalles())},
            function (data, textStatus, jqXHR) {
                window.location.href = data.documento;
                $("#esperar").css("opacity","0").fadeOut();
            },
            "json"
        );

        return false;
        
    });
})

barras = (seriesData) => {
    Highcharts.chart('graficoEstadistico', {
        chart: {
            type: 'column'
        },
        title:{
            text:'Ingreso y consumo de combustible por mes',
            align: 'center'
        },
        xAxis: {
            categories: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic']
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Galones (GL)'
            }
        },
        plotOptions: {
            series: {
                pointWidth: 20
            }
        },
        series:[ 
            {   name:'Ingresos',
                data: seriesData,
            },
            {   name:'Salidas',
                data: [12,11,10,9,8,7,6,5,4,3,2,1]
            }
        ]
    });
}

detalles = () => {
    let DATA = [];
        
    let TABLA = $("#tablaPrincipal tbody >tr");
    
    TABLA.each(function(){
        item= {};

        item['numero']          = $(this).find('td').eq(0).text(),
        item['emision']         = $(this).find('td').eq(1).text(),
        item['almacen']         = $(this).find('td').eq(2).text(),
        item['tipo']            = $(this).find('td').eq(3).text(),
        item['codigo']          = $(this).find('td').eq(4).text(),
        item['descripcion']     = $(this).find('td').eq(5).text(),
        item['unidad']          = $(this).find('td').eq(6).text(),
        item['cantidad']        = $(this).find('td').eq(7).text(),
        item['trabajador']      = $(this).find('td').eq(8).text(),
        item['usuario']         = $(this).find('td').eq(9).text(),
        item['proyecto']        = $(this).find('td').eq(10).text(),
        item['observaciones']   = $(this).find('td').eq(11).text(),
        item['documento']       = $(this).find('td').eq(12).text(),
        item['area']            = $(this).find('td').eq(13).text(),
        item['referencia']      = $(this).find('td').eq(14).text(),
        item['mes']             = $(this).find('td').eq(15).text()

        DATA.push(item);
    })

    return DATA;
}


listarRegistrosCombustible = async () => {
    try {
        let formData = new FormData();
        formData.append('nota',document.getElementById('notaSearch').value);
        formData.append('cc',document.getElementById('costosSearch').value);
        formData.append('mes',document.getElementById('mesSearch').value);
        formData.append('anio',document.getElementById('anioSearch').value);

        const response = await fetch(RUTA + "combustible/listaCombustibles", {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        const tablaCuerpo = document.getElementById("tablaPrincipalCuerpo");

        if (!tablaCuerpo) {
            throw new Error("Element with ID 'tablaPrincipalCuerpo' not found");
        }

        tablaCuerpo.innerHTML = "";

        let item = 1;

        const mes  = ['ENE','FEB','MAR','ABR','MAY','JUN','JUL','AGO','SET','OCT','NOV','DIC'];

        data.datos.forEach ( e =>{
            const tr = document.createElement("tr");
            const trabajador = data.usuarios.find(user => user.dni == e.cdocumento);

            tr.classList.add("pointer");
            if ( e.idtipo == 1 ){
                tr.classList.add("recepcion_combustible");
            }
            
            tr.dataset.id_consumo = e.idreg
            tr.innerHTML = `<td class="textoDerecha">${item++}</td>
                            <td class="textoCentro">${e.fregistro}</td>
                            <td class="pl20px">${e.cdesalm}</td>
                            <td class="pl20px">${e.idtipo == 1 ?'INGRESO':'SALIDA'}</td>
                            <td class="textoCentro">${e.ccodprod}</td>
                            <td class="pl20px">${e.cdesprod}</td>
                            <td class="textoCentro">${e.cabrevia}</td>
                            <td class="textoDerecha">${e.ncantidad}</td>
                            <td class="pl20px">${trabajador.usuario}</td>
                            <td class="pl20px">${e.notaingreso}</td>
                            <td class="textoCentro">${e.idusuario}</td>
                            <td class="textoCentro">${e.ccodproy}</td>
                            <td class="pl20px">${e.tobseritem}</td>
                            <td class="pl20px">${e.tobserdocum}</td>
                            <td class="pl20px">${e.cdesarea}</td>
                            <td class="pl20px">${e.cregistro}</td>
                            <td class="textoCentro">${mes[e.mes-1]}</td>`;


            tablaCuerpo.appendChild(tr);
        });

        $("#esperar").fadeOut().promise().done(function(){
           iniciarPaginadorConsulta();
        });

    } catch (error) {
        mostrarMensaje('No hay registros para procesar','mensaje_error');
        console.log(error.message);
        $("#esperar").fadeOut();
    }
}

function iniciarPaginadorConsulta() {
    const content = document.querySelector('.itemstabla'); 
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