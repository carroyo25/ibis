$(function(){
    var  fila = [];

    $("#esperar").fadeOut();


    $("#tablaPrincipal tbody").on("click","tr", function (e) {
        e.preventDefault();

       $.post(RUTA+"estudio/consultaId", {id:$(this).data("indice")},
            function (data, textStatus, jqXHR) {
                
                let numero = $.strPad(data.cabecera[0].nrodoc,6);
                let estado = "textoCentro w50por estado " + data.cabecera[0].cabrevia;
                
                $("#codigo_costos").val(data.cabecera[0].idcostos);
                $("#codigo_area").val(data.cabecera[0].idarea);
                $("#codigo_transporte").val(data.cabecera[0].idtrans);
                $("#codigo_solicitante").val(data.cabecera[0].idsolicita);
                $("#codigo_tipo").val(data.cabecera[0].idtipomov);
                $("#codigo_pedido").val(data.cabecera[0].idreg);
                $("#codigo_estado").val(data.cabecera[0].estadodoc);
                $("#codigo_verificacion").val(data.cabecera[0].verificacion);
                $("#codigo_atencion").val(data.cabecera[0].nivelAten);
                $("#aprobado").val(data.cabecera[0].docPdfAprob);
                $("#elabora").val(data.cabecera[0].cnombres);
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
                
                $("#tablaDetalles")
                    .empty()
                    .append(data.detalles);

                $("#estado")
                    .removeClass()
                    .addClass(estado);
            },
            "json"
        );

        $("#proceso").fadeIn();

        return false;
    });

    $("#closeProcess").click(function (e) { 
        e.preventDefault();

        $.post(RUTA+"estudio/actualizaListado",
            function (data, textStatus, jqXHR) {
                $(".itemsTabla table tbody")
                    .empty()
                    .append(data);

                $("#proceso").fadeOut(function(){
                    $("form")[0].reset();
                    $("form")[1].reset();
                    $("#tablaDetalles tbody").empty();
                });
            },
            "text"
        );

        $("#proceso").fadeOut();
        
        return false;  
    });

    $("#tablaDetalles").on("click","input", function (e) {
        item = {};

        let posicion = $(this).parent().parent().data("fila");
        let indice = fila.findIndex(criterio => criterio.lugar === posicion);
        let entidad = $(this).parent().data("entidad");

        if( $(this).prop('checked') ) {
            if (indice == -1){
                item["lugar"]       = posicion;
                item["pedido"]      = $(this).parent().data("pedido");
                item['entidad']     = $(this).parent().data("entidad");
                item["detprof"]     = $(this).parent().data("detprof");
                item["unitario"]    = $(this).parent().data("precio");
                item["detpedido"]   = $(this).parent().data("detped");
                item["entrega"]     = $(this).parent().data("entrega");
                item["total"]       = $(this).parent().data("total");
                item["espec"]       = $(this).parent().data("detalle");
            
                fila.push(item);
            }else {
                fila[posicion-1]['entidad']     = $(this).parent().data("entidad");
                fila[posicion-1]["detprof"]     = $(this).parent().data("detprof");
                fila[posicion-1]["unitario"]    = $(this).parent().data("precio");
                fila[posicion-1]["entrega"]     = $(this).parent().data("entrega");
                fila[posicion-1]["total"]       = $(this).parent().data("total");
                fila[posicion-1]["espec"]       = $(this).parent().data("detalle");
            }

        }
    });

    $("#tablaDetalles").on("click","a", function (e) {
        e.preventDefault();

        $(".ventanaVistaPrevia iframe")
                    .attr("src","")
                    .attr("src",$(this).attr("href"));
        
        $("#vistaprevia").fadeIn();

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

        $.post(RUTA+"estudio/proformas", {id:$("#codigo_pedido").val()},
            function (data, textStatus, jqXHR) {
                $("#listaAdjuntos")
                    .empty()
                    .append(data);

                $("#vistaAdjuntos").fadeIn();
            },
            "text"
        );
        return false;
    });

    $("#closeAtach").click(function (e) { 
        e.preventDefault();

        $(".ventanaAdjuntos iframe").attr("src","");
        $("#vistaAdjuntos").fadeOut();

        return false;
    });

    $("#vistaAdjuntos").on("click","a", function (e) {
        e.preventDefault();
        
        $(".ventanaAdjuntos iframe")
            .attr("src","")
            .attr("src","public/documentos/cotizaciones/"+$(this).attr("href"));
        
        return false;
    });

    $("#requestAprob").click(function (e) { 
        e.preventDefault();
        
        $("#pregunta").fadeIn();

        return false;
    });

    
    $("#btnAceptarPregunta").click(function (e) { 
        e.preventDefault();

        if (fila.length > 1) {
            mostrarMensaje("No hay seleccion de items","mensaje_error");
            return false;
        }

        $.post(RUTA+"estudio/procesaEstudio", {datos:JSON.stringify(fila),id:$("#codigo_pedido").val()},
            function (data, textStatus, jqXHR) {
                $("#pregunta").fadeOut();
            },
            "text"
        );
        
        return false;
    });

    $("#btnCancelarPregunta").click(function (e) { 
        e.preventDefault();

        $("#pregunta").fadeOut();

        return false;
    });
})