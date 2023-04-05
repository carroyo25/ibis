$(function(){
    $("#tablaPrincipal tbody").on("click","tr", function (e) {
        e.preventDefault();

        $.post(RUTA+"pedidoedit/consultaRqAdmin", {id:$(this).data("indice")},
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

        $("#proceso").fadeOut();

        /*$.post(RUTA+"pedidoedit/actualizaListado",
            function (data, textStatus, jqXHR) {
                $(".itemsTabla table tbody")
                    .empty()
                    .append(data);

                $("#proceso").fadeOut(function(){
                    grabado = false;
                    $("form")[1].reset();
                    $("#tablaDetalles tbody,.listaArchivos").empty();
                    $(".lista").fadeOut();
                });
            },
            "text"
        );*/
        return false;
    });

    $("#btnAnular").click(function (e) { 
        e.preventDefault();

        $("#preguntaAnula").fadeIn();

        return false;
    });

    $("#btnCancelarAnula").click(function (e) { 
        e.preventDefault();

        $("#preguntaAnula").fadeOut();
        
        return false;
    });

    $("#btnAceptarAnula").click(function (e) { 
        e.preventDefault();

        $("#ventanaEspera").fadeIn();

        $.post(RUTA+"pedidoedit/anulapedido", {id:$("#codigo_pedido").val()},
            function (data, textStatus, jqXHR) {
                $("#preguntaAnula").fadeOut();
                $("#ventanaEspera").fadeOut();

                mostrarMensaje(data,"mensaje_correcto");
            },
            "text"
        );
        
        return false;
    });
})