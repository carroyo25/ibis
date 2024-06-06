$(function(){  
    const body = document.querySelector("#tablaPrincipal tbody");

    let listItemFinal = null,estoyPidiendo = false,iditempedido = "",fila=0,estadoItem=0,accion = "";

    //LISTA PARA EL SCROLL

    const observandoListItem = listItem => {
        if ( listItem[0].isIntersecting ) {
            query();
        }
    }

    const settings = {
        threshold: 1
    }

    let observador = new IntersectionObserver(
        observandoListItem,
        settings
    );

    const query = async () => {
        if (estoyPidiendo) return;
        estoyPidiendo = true;
        let pagina = parseInt(body.dataset.p) || 1;
        const FD = new FormData();
        FD.append('pagina',pagina);

        const r = await fetch(RUTA+'segpedgen/listaScroll',{
            method: 'POST',
            body:FD
        });

        let item = 0;

        const j  = await r.json();
        j[0].pedidos.forEach(i => {
            const tr = document.createElement('tr');
            
            
            tr.innerHTML = `<td class="textoCentro">${i.nrodoc}</td>
                            <td class="textoCentro">${i.emision}</td>
                            <td class="textoCentro">${i.idtipomov}</td>
                            <td class="pl20px">${i.concepto}</td>
                            <td class="pl20px">${i.costos}</td>
                            <td class="pl20px">${i.nombres}</td>
                            <td class="pl20px">${i.area}</td>
                            <td class="textoCentro ${i.cabrevia.toLowerCase()}">${i.cabrevia}</td>
                            <td class="textoCentro ${i.atencion.toLowerCase()}">${i.atencion}</td>`;
            tr.classList.add("pointer");
            tr.dataset.indice = i.idreg;
            body.appendChild(tr);
        })

        if (listItemFinal){
            observador.unobserve(listItemFinal);
        }

        if (j[0].quedan) { //devuelve falso si ya no quedan mas registros
            listItemFinal = body.lastElementChild.previousElementSibling;
            observador.observe( listItemFinal);
            estoyPidiendo = false;
            body.dataset.p = ++pagina;
        }
    }

    query();

    ///FIN DEL SCROLL

    $("#tablaPrincipal tbody").on("click","tr", function (e) {
        e.preventDefault();

        $.post(RUTA+"pedidoseg/seguimientoID", {id:$(this).data("indice")},
            function (data, textStatus, jqXHR) {
                
                let numero = $.strPad(data.cabecera[0].nrodoc,6);
                let estado = "textoCentro w35por estado " + data.cabecera[0].cabrevia;
                
                $("#codigo_costos").val(data.cabecera[0].idcostos);
                $("#codigo_area").val(data.cabecera[0].idarea);
                $("#codigo_transporte").val(data.cabecera[0].idtrans);
                $("#codigo_solicitante").val(data.cabecera[0].idsolicita);
                $("#codigo_partida").val(data.cabecera[0].idpartida);
                $("#codigo_tipo").val(data.cabecera[0].idtipomov);
                $("#codigo_pedido").val(data.cabecera[0].idreg);
                $("#codigo_estado").val(data.cabecera[0].estadodoc);
                $("#codigo_verificacion").val(data.cabecera[0].verificacion);
                $("#codigo_atencion").val(data.cabecera[0].nivelAten);
                $("#vista_previa").val(data.cabecera[0].docfPdfPrev);
                $("#numero").val(numero);
                $("#emision").val(data.cabecera[0].emision);
                $("#costos").val(data.cabecera[0].proyecto);
                $("#area").val(data.cabecera[0].area);
                $("#transporte").val(data.cabecera[0].transporte);
                $("#concepto").val(data.cabecera[0].concepto);
                $("#solicitante").val(data.cabecera[0].nombres);
                $("#tipo").val(data.cabecera[0].tipo);
                $("#vence").val(data.cabecera[0].vence);
                $("#estado").val(data.cabecera[0].estado);
                $("#espec_items").val(data.cabecera[0].detalle);
                $("#partida").val(data.cabecera[0].cdescripcion);
               

                $("#tablaDetalles tbody")
                    .empty()
                    .append(data.detalles);

                $("#estado")
                    .removeClass()
                    .addClass(estado);
                
                grabado = true;
            },
            "json"
        );

        $("#proceso").fadeIn();

        return false;
    });

    $("#closeProcess").click(function (e) { 
        e.preventDefault();

        $("#proceso").fadeOut(function(){
            $("form")[0].reset();
            $("form")[1].reset();
            $("#tablaDetalles tbody,.listaArchivos").empty();
            $(".lista").fadeOut();

        });

        return false;
    });

    $("#closePreview").click(function (e) { 
        e.preventDefault();

        $(".ventanaVistaPrevia iframe").attr("src","");
        $("#vistaprevia").fadeOut();

        return false;
    });

    $("#preview").click(function (e) { 
        e.preventDefault();
    
        let result = {};
        let ruta = $("#codigo_estado").val() == 49 ? "public/documentos/pedidos/vistaprevia/":"public/documentos/pedidos/emitidos/";

        $.each($("#formProceso").serializeArray(),function(){
            result[this.name] = this.value;
        })

        $.post(RUTA+"pedidos/vistaprevia", {cabecera:result,detalles:JSON.stringify(itemsPreview())},
                function (data, textStatus, jqXHR) {
                    $(".ventanaVistaPrevia iframe")
                    .attr("src","")
                    .attr("src",ruta+data);

                    $("#vista_previa").val(data);

                    $("#vistaprevia").fadeIn();
                },
            "text"
        );
        
        return false;
    });

    $("#verDetalles").click(function(e){
        e.preventDefault();

        $.post(RUTA+"pedidoseg/infoPedido", {id:$("#codigo_pedido").val()},
            function (data, textStatus, jqXHR) {
                $("#tableInfo tbody").find('tr').eq(1).find('td').eq(1).children().text(data.pedido);
                $("#tableInfo tbody").find('tr').eq(1).find('td').eq(3).children().text(data.emision);
                $("#tableInfo tbody").find('tr').eq(2).find('td').eq(1).children().text(data.costos);
                $("#tableInfo tbody").find('tr').eq(3).find('td').eq(1).children().text(data.elaborado);
                $("#tableInfo tbody").find('tr').eq(4).find('td').eq(1).children().text($("#tablaDetalles tbody tr").length);

                if(data.aprobador != null) {
                    $("#tableInfo tbody").find('tr').eq(6).find('td').eq(1).children().text(data.aprobacion);
                    $("#tableInfo tbody").find('tr').eq(7).find('td').eq(1).children().text(data.aprobador);
                }

                let point = chartSpeed.series[0].points[0],
                    avance = parseInt(data.avance),
                    estados = (avance/10);
                point.update(parseInt(avance));
                
                for (let index = 0; index < estados; index++) {
                    let circulo_externo = "#ce"+index,
                        circulo_interno = "#ci"+index;

                    $(circulo_externo)
                        .removeClass("avance_inactivo")
                        .addClass("avance_activo_externo");

                    $(circulo_interno)
                        .removeClass("avance_inactivo")
                        .addClass("avance_activo_interno");
                }

                $("#detalles").fadeIn();

                $(".div4 table tbody")
                    .empty();

                $("#tabla_ordenes").append(data.ordenes);
                $("#tabla_ingresos").append(data.ingresos);
                $("#tabla_despachos").append(data.despachos);
                $("#tabla_registros").append(data.registros);
            },
            "json"
        );

        return false;
    });

    $("#cerrarDetalles").click(function(e){
        e.preventDefault();

        $("#detalles").fadeOut();

        for (let index = 0; index < 10; index++) {
            let circulo_externo = "#ce"+index,
                circulo_interno = "#ci"+index;

            $(circulo_externo)
                .removeClass("avance_activo_externo")
                .addClass("avance_inactivo");

            $(circulo_interno)
                .removeClass("avance_activo_interno")
                .addClass("avance_inactivo");
        }

        return false;
    });

    $("#tabla_ordenes").on('click','a', function(e) {
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

    $("#btnProceso").on('click', function(e) {
        e.preventDefault();

        let srt = $("#formConsulta").serialize();

        $.post(RUTA+"segpedgen/filtroPedidosAdmin", srt,
            function (data, text, requestXHR) {
                $("#tablaPrincipal tbody")
                    .empty()
                    .append(data);
            },
            "text"
        );
        
        return false
    });
})

itemsPreview = () =>{
    DATA = [];
    let TABLA = $("#tablaDetalles tbody >tr");

    TABLA.each(function(){
        let ITEM        = $(this).find('td').eq(1).text(),
            CODIGO      = $(this).find('td').eq(2).text(),
            DESCRIPCION = $(this).find('td').eq(3).text(),
            UNIDAD      = $(this).find('td').eq(4).text(),
            CANTIDAD    = $(this).find('td').eq(5).children().val(),
            ESPECIFICA  = $(this).find('td').eq(6).children().val(),
            ITEMPEDIDO  = $(this).data('idx'),
            OBSERVAC    = "",
            NROPARTE    = $(this).find('td').eq(7).text(),
            ACTIVO      = $(this).find('td').eq(8).text(),

        item= {};
        
        item['item']        = ITEM;
        item['codigo']      = CODIGO;
        item['descripcion'] = DESCRIPCION;
        item['unidad']      = UNIDAD;
        item['cantidad']    = CANTIDAD;
        item['especifica']  = ESPECIFICA;
        item['itempedido']  = ITEMPEDIDO;
        item['observac']    = OBSERVAC;
        item['atendida']    = 0;
        item['nroparte']    = NROPARTE;
        item['activo']      = ACTIVO;

        DATA.push(item);
    })

    return DATA;
}