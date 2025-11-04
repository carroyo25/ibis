$(function(){

    const body = document.querySelector("#tablaPrincipal tbody");

    $.post(RUTA+'ordenconsult/listaOrdenesPaginador',
        function (data, text, requestXHR) {
            $("#tablaPrincipal tbody")
                .empty()
                .append(data);

                $("#esperar").fadeOut().promise().done(function(){
                    iniciarPaginadorConsulta();
                });

        "text"
    });


    $(".dataProceso_2, #tablaDetalles").css("pointer-events","none");

    $("#tablaPrincipal tbody").on("click","tr", function (e) {
        e.preventDefault();

        autorizado = $(this).data('finanzas')+$(this).data('logistica')+$(this).data('operaciones');

        $.post(RUTA+"ordenedit/ordenId", {id:$(this).data("indice")},
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

        item= {};
        
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

        item= {};
        
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


function iniciarPaginadorConsulta() {
    const content = document.querySelector('.itemsTabla'); 
    const contentTarget = document.querySelector('.paginadorWrap');
    let itemsPerPage = 50; // Valor por defecto
    let currentPage = 0;
    const maxVisiblePages = 15; // Número máximo de botones visibles
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
            contentTarget.appendChild(paginationContainer);
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

function iniciarPaginadorConFiltro() {
    const content = document.querySelector('.itemsTabla');
    const contentTarget = document.querySelector('.paginadorWrap');
    let itemsPerPage = 50;
    let currentPage = 0;
    const maxVisiblePages = 15;
    
    // Almacenar todos los datos originales y filtrados
    let allItems = [];
    let filteredItems = [];
    let currentFilters = {};
    
    // Inicializar datos
    function initialize() {
        const allRows = content.getElementsByTagName('tr');
        allItems = Array.from(allRows).slice(1); // Excluir encabezado
        filteredItems = [...allItems]; // Inicialmente, todos los items están visibles
        applyFilters(); // Aplicar filtros existentes si los hay
    }

    // Función de filtrado que trabaja con TODOS los datos
    function aplicarFiltro(columna, valor) {
        if (!valor || valor === 'todos') {
            // Eliminar filtro de esta columna
            delete currentFilters[columna];
        } else {
            // Aplicar filtro
            currentFilters[columna] = valor;
        }
        
        applyFilters();
        currentPage = 0; // Volver a la primera página después de filtrar
        createPageButtons();
        showPage(currentPage);
    }

    // Aplicar todos los filtros activos
    function applyFilters() {
        if (Object.keys(currentFilters).length === 0) {
            // Sin filtros, mostrar todos los items
            filteredItems = [...allItems];
        } else {
            // Aplicar filtros
            filteredItems = allItems.filter(item => {
                const celdas = item.getElementsByTagName('td');
                let coincide = true;
                
                for (const [columna, valor] of Object.entries(currentFilters)) {
                    const columnaIndex = parseInt(columna);
                    if (celdas[columnaIndex] && celdas[columnaIndex].textContent.trim() !== valor) {
                        coincide = false;
                        break;
                    }
                }
                
                return coincide;
            });
        }
    }

    // Mostrar página actual (solo de los datos filtrados)
    function showPage(page) {
        const startIndex = page * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        
        // Primero ocultar todos los items originales
        allItems.forEach(item => {
            item.style.display = 'none';
        });
        
        // Luego mostrar solo los items filtrados de la página actual
        filteredItems.forEach((item, index) => {
            const isVisible = index >= startIndex && index < endIndex;
            item.style.display = isVisible ? '' : 'none';
        });
        
        updatePaginationState();
    }

    // Crear botones de paginación basados en datos FILTRADOS
    function createPageButtons() {
        const totalPages = Math.ceil(filteredItems.length / itemsPerPage);
        
        let paginationContainer = document.querySelector('.pagination');
        if (!paginationContainer) {
            paginationContainer = document.createElement('div');
            paginationContainer.classList.add('pagination');
            contentTarget.innerHTML = '';
            contentTarget.appendChild(paginationContainer);
        } else {
            paginationContainer.innerHTML = '';
        }

        createItemsPerPageSelector(paginationContainer);
        createNavigationButtons(paginationContainer, totalPages);
        createPageNumberButtons(paginationContainer, totalPages);
        createFilterInfo(paginationContainer);
        
        updatePaginationState();
    }

    // Información del filtro aplicado
    function createFilterInfo(container) {
        const filterInfo = document.createElement('div');
        filterInfo.className = 'filter-info';
        
        if (Object.keys(currentFilters).length > 0) {
            filterInfo.innerHTML = `
                <span class="active-filters">Filtros activos: ${Object.keys(currentFilters).length}</span>
                <button class="clear-filters">Limpiar filtros</button>
            `;
            
            filterInfo.querySelector('.clear-filters').addEventListener('click', limpiarFiltros);
        } else {
            filterInfo.innerHTML = '<span>Sin filtros aplicados</span>';
        }
        
        container.appendChild(filterInfo);
    }

    // Limpiar todos los filtros
    function limpiarFiltros() {
        currentFilters = {};
        applyFilters();
        currentPage = 0;
        createPageButtons();
        showPage(currentPage);
    }

    // Navegación
    function goToPage(page) {
        const totalPages = Math.ceil(filteredItems.length / itemsPerPage);
        if (page < 0 || page >= totalPages) return;
        
        currentPage = page;
        showPage(currentPage);
        updatePaginationState();
    }

    // Selector de items por página
    function createItemsPerPageSelector(container) {
        const selectorWrapper = document.createElement('div');
        selectorWrapper.className = 'items-per-page-selector';
        
        const select = document.createElement('select');
        const options = [25, 50, 100, 150, 200, 250, 300];
        
        options.forEach(option => {
            const opt = document.createElement('option');
            opt.value = option;
            opt.textContent = option;
            if (option === itemsPerPage) opt.selected = true;
            select.appendChild(opt);
        });

        select.addEventListener('change', function() {
            itemsPerPage = parseInt(this.value);
            currentPage = 0;
            createPageButtons();
            showPage(currentPage);
        });

        selectorWrapper.appendChild(select);
        container.appendChild(selectorWrapper);
    }

    // Botones de navegación
    function createNavigationButtons(container, totalPages) {
        const navButtons = [
            { text: 'Primera', action: () => goToPage(0), disabled: currentPage === 0 },
            { text: 'Anterior', action: () => goToPage(currentPage - 1), disabled: currentPage === 0 },
            { text: 'Siguiente', action: () => goToPage(currentPage + 1), disabled: currentPage >= totalPages - 1 },
            { text: 'Última', action: () => goToPage(totalPages - 1), disabled: currentPage >= totalPages - 1 }
        ];

        navButtons.forEach(button => {
            const btn = document.createElement('button');
            btn.textContent = button.text;
            btn.disabled = button.disabled;
            btn.addEventListener('click', button.action);
            container.appendChild(btn);
        });
    }

    // Botones numéricos
    function createPageNumberButtons(container, totalPages) {
        let startPage = Math.max(0, currentPage - Math.floor(maxVisiblePages / 2));
        let endPage = Math.min(totalPages, startPage + maxVisiblePages);
        
        if (endPage - startPage < maxVisiblePages) {
            startPage = Math.max(0, endPage - maxVisiblePages);
        }

        if (startPage > 0) {
            const ellipsis = document.createElement('span');
            ellipsis.textContent = '...';
            ellipsis.className = 'page-ellipsis';
            container.appendChild(ellipsis);
        }

        for (let i = startPage; i < endPage; i++) {
            const pageButton = document.createElement('button');
            pageButton.textContent = i + 1;
            pageButton.className = 'page-button';
            pageButton.disabled = i === currentPage;
            pageButton.dataset.page = i;
            pageButton.addEventListener('click', () => goToPage(i));
            container.appendChild(pageButton);
        }

        if (endPage < totalPages) {
            const ellipsis = document.createElement('span');
            ellipsis.textContent = '...';
            ellipsis.className = 'page-ellipsis';
            container.appendChild(ellipsis);
        }
    }

    // Actualizar estado
    function updatePaginationState() {
        const totalPages = Math.ceil(filteredItems.length / itemsPerPage);
        const buttons = document.querySelectorAll('.pagination .page-button');
        
        buttons.forEach(button => {
            const pageNum = parseInt(button.dataset.page);
            button.disabled = pageNum === currentPage;
            button.classList.toggle('active', pageNum === currentPage);
        });

        updatePageInfo();
    }

    // Información de página
    function updatePageInfo() {
        let infoElement = document.querySelector('.page-info');
        
        if (!infoElement) {
            infoElement = document.createElement('div');
            infoElement.className = 'page-info';
            document.querySelector('.pagination').appendChild(infoElement);
        }
        
        const startItem = (currentPage * itemsPerPage) + 1;
        const endItem = Math.min((currentPage + 1) * itemsPerPage, filteredItems.length);
        const totalItems = filteredItems.length;
        const originalTotal = allItems.length;
        
        let infoText = `Mostrando ${startItem}-${endItem} de ${totalItems}`;
        if (totalItems !== originalTotal) {
            infoText += ` (filtrado de ${originalTotal} total)`;
        }
        
        infoElement.textContent = infoText;
    }

    // Inicializar
    initialize();
    createPageButtons();
    showPage(currentPage);

    // API pública para integración con filtros
    return {
        aplicarFiltro,
        limpiarFiltros,
        goToPage,
        getCurrentPage: () => currentPage,
        getTotalFilteredItems: () => filteredItems.length,
        getTotalOriginalItems: () => allItems.length,
        getActiveFilters: () => ({ ...currentFilters })
    };
}

