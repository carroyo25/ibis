$(function(){
    $("#esperar").fadeOut();
    
    $("#tablaPrincipal tbody").on("click","tr", function (e) {
        e.preventDefault();

       $.post(RUTA+"asigna/consultaId", {id:$(this).data("indice")},
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
                $("#user_asigna").val(data.cabecera[0].asigna);
                
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

        $.post(RUTA+"asigna/actualizaListado",
            function (data, textStatus, jqXHR) {
                $(".itemsTabla table tbody")
                    .empty()
                    .append(data);

                $("#proceso").fadeOut(function(){
                    $("form")[0].reset();
                    $("form")[1].reset();
                    $("#tablaDetalles tbody").empty();
                    $("#operadores *").removeClass("itemSeleccionado");
                });
            },
            "text"
        );

        $("#proceso").fadeOut();
        
        return false;  
    });

    $("#asingRequest").click(function (e) { 
        e.preventDefault();

        $("#comentarios").fadeIn();

        return false;
    });

    $("#cancelaAsigna").click(function (e) { 
        e.preventDefault();

        $("#comentarios").fadeOut();
        
        return false;
    });

    $("#aceptaAsigna").click(function (e) { 
        e.preventDefault();

        try {
            if ($("#operador_asignado").val() =="" ) throw "No selecciono operador";

            $.post(RUTA+"asigna/asignaOperador", {pedido:$("#codigo_pedido").val(),
                                             detalles:JSON.stringify(itemsDetalles()),
                                             asignado:$("#operador_asignado").val()},
            function (data, textStatus, jqXHR) {
                $("#comentarios").fadeOut();
                mostrarMensaje("Pedido asignado","mensaje_correcto")
            },
            "text"
        );
        } catch (error) {
            mostrarMensaje(error,"mensaje_error")
        }
        
        return false;
    });

    $("#operadores").on("click","a", function (e) {
        e.preventDefault();

        $("#operadores *").removeClass("itemSeleccionado");
        $(this).addClass("itemSeleccionado");
        $("#operador_asignado").val($(this).attr("href"));

        return false;
    });

    $("#btnConsulta").on('click', function(e) {
        e.preventDefault();

        let str = $("#formConsulta").serialize();

        $.post(RUTA+"asigna/filtroPedidos", str,
            function (data, text, requestXHR) {
                $("#tablaPrincipal tbody")
                    .empty()
                    .append(data);
            },
            "text"
        );
        
        return false
    });

    $("#viewAtach").click(function (e) { 
        e.preventDefault();

        $.post(RUTA+"aprobacion/adjuntos", {id:$("#codigo_pedido").val()},
            function (data, textStatus, jqXHR) {
                if (data.archivos > 0){
                    $("#listaAdjuntos")
                        .empty()
                        .append(data.adjuntos);
                        $("#vistaAdjuntos").fadeIn();
                }else{
                    mostrarMensaje("No hay archivos adjuntos","mensaje_error");
                }
            },
            "json"
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
            .attr("src","public/documentos/pedidos/adjuntos/"+$(this).attr("href"));
        
        return false;
    });

    $("#quitarAsigna").click(function(e){
        e.preventDefault();

        try {
            if ($("#user_asigna").val() == "") throw new Error("El pedido no esta asignado");

            $.post(RUTA+"asigna/libera",{pedido:$("#codigo_pedido").val(),usuario:$("#id_user").val()},
                function (data, text, requestXHR) {
                    if (data.existe){
                        mostrarMensaje("El pedido tiene orden","mensaje_error");
                    }else{
                        mostrarMensaje("Se quito la asignación","mensaje_correcto");
                    }
                },
                "json"
            );
        } catch (error) {
            mostrarMensaje(error,"mensaje_error");
        }

        
        return false;
    });
})


itemsDetalles = () =>{
    DATA = [];
    let TABLA = $("#tablaDetalles tbody >tr");

    TABLA.each(function(){
        let ITEMPEDIDO  = $(this).data('idx');

        item= {};
        item['itempedido']  = ITEMPEDIDO;
               
        DATA.push(item);
    })

    return DATA;
}