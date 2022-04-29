$(function(){
    $("#tablaPrincipal tbody").on("click","tr", function (e) {
        e.preventDefault();

       $.post(RUTA+"cotizacion/consultaId", {id:$(this).data("indice")},
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

        $.post(RUTA+"cotizacion/actualizaListado",
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

    $("#viewAtach").click(function (e) { 
        e.preventDefault();
        
        mostrarMensaje("mensaje","mensaje_correcto");
        
        return false;
    });

    $("#preview").click(function (e) { 
        e.preventDefault();

        $(".ventanaVistaPrevia iframe")
            .attr("src","")
            .attr("src","public/documentos/pedidos/aprobados/"+$("#aprobado").val());
        
        $("#vistaprevia").fadeIn();
        
        return false;
    });

    $("#closePreview").click(function (e) { 
        e.preventDefault();

        $(".ventanaVistaPrevia iframe").attr("src","");
        $("#vistaprevia").fadeOut();

        return false;
    });

    $("#requestAprob").click(function (e) { 
        e.preventDefault();

        $.post(RUTA+"cotizacion/proveedores",
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

            let result = {};

            $.each($("#formProceso").serializeArray(),function(){
                result[this.name] = this.value;
            });

            $("#espera").fadeIn();
            
            $.post(RUTA+"cotizacion/mensajeCorreo", {pedido:$("#codigo_pedido").val(),
                                                    detalles:JSON.stringify(itemsSave()),
                                                    correos:JSON.stringify(mailsList()),
                                                    asunto:$("#subject").val(),
                                                    mensaje:$(".messaje div").html(),
                                                    estado:55},
                                                
             function (data, textStatus, jqXHR) {
                $("#espera").fadeOut();
                $("#sendMail").fadeOut();
                mostrarMensaje(data.mensaje,data.clase);
             },
             "json"
         );
        } catch (error) {
            mostrarMensaje(error,'mensaje_error');
        }
        
        return false;
    });

    $("#closeCotiz").click(function (e) { 
        e.preventDefault();

        $("#pregunta").fadeIn();
        
        return false;
    });

    $("#btnAceptarPregunta").click(function (e) { 
        e.preventDefault();

        $.post(RUTA+"cotizacion/estudio", {pedido:$("#codigo_pedido").val(),estado:56,detalles:JSON.stringify(itemsSave())},
            function (data, textStatus, jqXHR) {
                mostrarMensaje("Pedido actulizado","mensaje_correcto");
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
            UNIDAD      = $(this).data('codund'),
            CANTIDAD    = $(this).find('td').eq(5).text(),
            NROPARTE    = $(this).find('td').eq(6).text(),
            IDX         = $(this).data('idx'),
            OBSERVA     = $(this).find('td').eq(7).children().val(),
            ESTADO      = $(this).find('td').eq(0).children().prop('checked'); 

        item= {};
        
        if (ESTADO == 1) {
            item['idprod']      = IDPROD;
            item['unidad']      = UNIDAD;
            item['cantidad']    = CANTIDAD;
            item['nroparte']    = NROPARTE;
            item['itempedido']  = IDX;
            item['observac']    = OBSERVA;

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
            CODPROV     = $(this).data('doc')

        item= {};
        
        if (ENVIAR) {
            item['nombre'] = NOMBRE;
            item['correo'] = CORREO;
            item['codprov']= CODPROV;

            CORREOS.push(item);
        }
        
    })

    return CORREOS;
}