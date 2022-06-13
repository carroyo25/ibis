$(function(){
    $("#esperar").fadeOut();
    
    $("#tablaPrincipal tbody").on("click","tr", function (e) {
        e.preventDefault();

       $.post(RUTA+"verificacion/consultaId", {id:$(this).data("indice")},
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
                $("#emitido").val(data.cabecera[0].docPdfEmit);
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
                
                $("#tablaDetalles tbody")
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

        $.post(RUTA+"verificacion/actualizaListado",
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

    $("#tablaDetalles").on("click","a", function (e) {
        e.preventDefault();

        $(".ventanaVistaPrevia iframe")
                    .attr("src","")
                    .attr("src","public/documentos/pedidos/especificaciones/"+$(this).attr("href"));
        
        $("#vistaprevia").fadeIn();

        return false;
    });

    $("#closePreview").click(function (e) { 
        e.preventDefault();

        $(".ventanaVistaPrevia iframe").attr("src","");
        $("#vistaprevia").fadeOut();

        return false;
    });

    $("#authProcess").click(function (e) { 
        e.preventDefault();

        $("#pregunta").fadeIn();

        return false;
    });

    $("#btnAceptarPregunta").click(function (e) { 
        e.preventDefault();

        $.post(RUTA+"verificacion/actualizaPedido", {detalles:JSON.stringify(updateItems()),id:$("#codigo_pedido").val()},
            function (data, textStatus, jqXHR) {
                mostrarMensaje(data.mensaje,data.clase);
            },
            "json"
        );

        return false;
    });

    $("#btnCancelarPregunta").click(function (e) { 
        e.preventDefault();

        $("#pregunta").fadeOut();

        return false;
    });
})

updateItems = () =>{
    DATA = [];
    let TABLA = $("#tablaDetalles tbody >tr");

    TABLA.each(function(){
        let INDICE  = $(this).data('idx'),
            OBSERVA = $(this).find('td').eq(6).children().val(),
            ESTADO  = $(this).find('td').eq(7).children().prop('checked');
        
        item= {};

        if (ESTADO) {
            item['itempedido'] = INDICE;
            item['observa'] = OBSERVA;

            DATA.push(item);
        }
    });

    return DATA;
}