var orden,descripcion,costos,area,proveedor,atencion = [];

$(function(){

    listarOrdenes();

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


    $(".datafiltros").append(`<a href="#" class="listaFiltro"><i class="fas fa-angle-down"></i></a>
                                <div class="filtro">
                                    <div class="oculto">
                                        <ul class="filtro_cantidad">
                                            <li><a>Ordenar ascedentemente</a></li>
                                            <li><a>Ordenar Descendentemente</a></li>
                                        </ul>
                                    </div>
                                    <hr>
                                    <input type="text" class="filterSearchConsult" name="filterSearch" placeholder="Buscar Elementos...">
                                    <ul class="ul_filtro"> 
                                    </ul>
                                    <div class="opciones_filtro">
                                        <button id="btn_filter_cancel">Cancelar</button>
                                    </div>
                            </div>`);

    $(".listaFiltro").click(function (e) { 
        e.preventDefault();

        $(".ul_filtro").empty();
        $(".filtro").fadeOut();

        const id = $(this).parent().attr("data-idcol");

        $(this).next().toggle(function(){
            switch (id) {
                case "0":
                    capturarValoresColumnas(orden);
                    break;
                case "2":
                    capturarValoresColumnas(descripcion);
                    break;
                case "3":
                    capturarValoresColumnas(costos);
                    break;
                case "4":
                    capturarValoresColumnas(area);
                    break;
                case "5":
                    capturarValoresColumnas(proveedor);
                    break;
                case "8":
                    capturarValoresColumnas(atencion);
                    break;
            }
            
        });

        return false;
    });

    $(".ul_filtro").on('click','a', function(e) {
            e.preventDefault();

            const columna = $(this).closest('.datafiltros').attr("data-idcol");
            const value = $(this).text();


            mostrarValoresFiltradosConsulta(columna,value);

            $(this).closest(".filtro").fadeOut(function(){
                $(".ul_filtro").empty();
            });

            return false;
    });

    $(".filterSearchConsult").keyup(function () { 
        
        let value = $(this).val().toLowerCase();

        let l = ".ul_filtro"+ " li a"

        $(l).filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });

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


function iniciarPaginadorConsulta() {
    const content = document.querySelector('.itemsTabla'); 
    const contentTarget = document.querySelector('.paginadorWrap');
    let itemsPerPage = 25; // Valor por defecto
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

        orden = data.datos.map(e => e.cnumero);
        descripcion = data.datos.map(e => e.concepto);
        costos = data.datos.map(e => e.ccodproy);
        area = data.datos.map(e => e.area);
        proveedor = data.datos.map(e => e.proveedor);
        atencion = data.datos.map(e => e.atencion);

        $("#esperar").fadeOut().promise().done(function(){
            iniciarPaginadorConsulta();
        });
    } catch (error) {
         mostrarMensaje('No hay registros para procesar','mensaje_error');
        $("#esperar").fadeOut();
    }
    
}

capturarValoresColumnas = (datos) => {
    const datosSinDuplicados = [...new Set(datos)];
    datosSinDuplicados.sort();

    const fragment = document.createDocumentFragment();

    datosSinDuplicados.forEach(e => {
        const li = document.createElement('li');
        li.innerHTML = `<li><a href='#'>${e}</a></li>`;
        fragment.appendChild(li);
    });

    $(".ul_filtro").append(fragment);

}

mostrarValoresFiltradosConsulta = (columna, valor) => {
    let tabla = $("#tablaPrincipal tbody tr");
    
    // Si el valor es vacío o "ALL", mostrar todas las filas
    if (!valor || valor === "ALL") {
        tabla.show();
        return;
    }

    tabla.each(function(){
        let textoCelda = $(this).find('td').eq(columna).text().trim();
        if (textoCelda === valor) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
}


