$(function(){
    accion = "";

    $("#esperar").fadeOut();

    $("#tablaPrincipal tbody").on("click","tr", function (e) {
        e.preventDefault();

        $.post(RUTA+"atencion/consultaId", {id:$(this).data("indice")},
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
                $("#partida").val(data.cabecera[0].cdescripcion );

                
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

        $.post(RUTA+"atencion/actualizaListado",
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

    $("#preview").click(function (e) { 
        e.preventDefault();
    
        $(".ventanaVistaPrevia iframe")
            .attr("src","")
            .attr("src","public/documentos/pedidos/emitidos/"+$("#emitido").val());

            $("#vistaprevia").fadeIn();
        
        return false;
    });

    $("#closePreview").click(function (e) { 
        e.preventDefault();

        $(".ventanaVistaPrevia iframe").attr("src","");
        $("#vistaprevia").fadeOut();

        return false;
    });

    $("#tablaDetalles tbody").on("focusout",".valorAtendido", function (e) {
        e.preventDefault();

        let cant_pedida = $(this).parent().parent().find('td').eq(5).text()
        let cant_atendida = $(this).val();
        let resultado = cant_pedida - cant_atendida;

        if (resultado < 0){
            mostrarMensaje("Verifique la cantidad ingresada","mensaje_error");
        }

        return false;
    });

    $("#tablaDetalles tbody").on("click","a", function (e) {
        e.preventDefault();

        $.post(RUTA+"atencion/existenciaProducto",{id:$(this).attr("href")},
            function (data, textStatus, jqXHR) {
                $("#tablaExistencias tbody")
                    .empty()
                    .append(data);
        
                    $("#archivos").fadeIn();
                
            },
            "text"
        );


        return false;s
    });

    $("#btnConfirmAtach").click(function (e) { 
        e.preventDefault();
        
        $("#archivos").fadeOut();

        return false;
    });

    $("#requestAprob").click(function (e) { 
        e.preventDefault();

        $.post(RUTA+"atencion/buscaRol", {rol:3,cc:$("#codigo_costos").val()},
            function (data, textStatus, jqXHR) {
                $("#listaCorreos tbody").empty().append(data);
                $("#sendMail").fadeIn();
            },
            "text"
        );
        
        return false;
    });

    $("#closeMail").click(function (e) { 
        e.preventDefault();

        $("form")[2].reset();
        $(".atachs").empty();
        $(".messaje div").empty();
        $("#sendMail").fadeOut();

        return false;
    });

    $("#btnConfirmSend").click(function (e) { 
        e.preventDefault();

        try {
            if ($("#subject").val() =="") throw "Escriba el asunto";
            if ($("messaje div").html() =="") throw "Escriba el asunto";

            $.post(RUTA+"atencion/correos", {pedido:$("#codigo_pedido").val(),
                                            detalles:JSON.stringify(itemsSave()),
                                            correos:JSON.stringify(mailsList()),
                                            adjunto:$("#emitido").val(),
                                            asunto:$("#subject").val(),
                                            mensaje:$(".messaje div").html(),
                                            estado:53},
                                                
             function (data, textStatus, jqXHR) {
                mostrarMensaje(data.mensaje,data.clase);
                $("#sendMail,#esperar").fadeOut();
             },
             "json"
         );
        } catch (error) {
            mostrarMensaje(error,'mensaje_error');
        }
        
        return false;
    });

    $("#closeReq").click(function (e) { 
        e.preventDefault();
    
        $("#pregunta").fadeIn();

        return false;
    });


    $("#btnAceptarPregunta").click(function (e) { 
        e.preventDefault();

        $.post(RUTA+"atencion/culminaPedido", {id:$('#codigo_pedido').val(),
                                                estado:52,
                                                detalles:JSON.stringify(itemsSave())},
            function (data, textStatus, jqXHR) {
                if (data){
                    mostrarMensaje("Pedido Culminado","mensaje_correcto");
                }else{
                    mostrarMensaje("Error,no se realizo la acción","mensaje_error");
                }

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

itemsSave = () =>{
    DATA = [];
    let TABLA = $("#tablaDetalles tbody >tr");

    TABLA.each(function(){
        let IDPROD      = $(this).data('idprod'),
            CANTIDAD    = $(this).find('td').eq(5).text(),
            ITEMPEDIDO  = $(this).data('idx'),
            ATENDIDA    = $(this).find('td').eq(6).children().val(),
            OBSERVAC    = $(this).find('td').eq(8).children().val(),
            ESTADO      = $(this).data('grabado');

        item= {};
        
        if (ESTADO == 1) {
            item['idprod']      = IDPROD;
            item['cantidad']    = CANTIDAD;
            item['itempedido']  = ITEMPEDIDO;
            item['atendida']    = ATENDIDA;
            item['observac']    = OBSERVAC;

            DATA.push(item);
        } 
    })

    return DATA;
}

mailsList = () => {
    CORREOS = [];

    let TABLA =  $("#listaCorreos tbody >tr");

    TABLA.each(function(){
        let CORREO      = $(this).find('td').eq(1).text(),
            NOMBRE      = $(this).find('td').eq(0).text(),
            ENVIAR      = $(this).find('td').eq(2).children().prop("checked"),

        item= {};
        
        if (ENVIAR) {
            item['nombre']= NOMBRE;
            item['correo']= CORREO;

            CORREOS.push(item);
        }
        
    })

    return CORREOS;
}