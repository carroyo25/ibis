$(function(){

    $("#esperar").fadeOut();
    
    $("#tablaPrincipal tbody").on("click","tr", function (e) {
        e.preventDefault();

       $.post(RUTA+"aprobacion/consultaId", {id:$(this).data("indice")},
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

        $.post(RUTA+"aprobacion/actualizaListado",
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

    $("#requestAprob").click(function (e) { 
        e.preventDefault();
        
        /*$.post(RUTA+"aprobacion/buscaRol", {rol:$(this).data("rol"),cc:$("#codigo_costos").val()},
            function (data, textStatus, jqXHR) {
                $("#listaCorreos tbody").empty().append(data);
                $("#sendMail").fadeIn();
            },
            "text"
        );*/

        $("#pregunta").fadeIn();
        

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
            if ($("messaje div").html() =="") throw "Escriba el mensaje";

            $("#esperar").fadeIn();

            let result = {};

            $.each($("#formProceso").serializeArray(),function(){
                result[this.name] = this.value;
            });
            
            $.post(RUTA+"aprobacion/confirma", {pedido:$("#codigo_pedido").val(),
                                            detalles:JSON.stringify(itemsPreview()),
                                            correos:JSON.stringify(mailsList()),
                                            asunto:$("#subject").val(),
                                            mensaje:$(".messaje div").html(),
                                            estado:54,
                                            cabecera:result},
                                                
             function (data, textStatus, jqXHR) {
                $("#sendMail,#proceso,#esperar").fadeOut();
                $("#tablaPrincipal tbody")
                    .empty()
                    .append(data.pedidos);
                mostrarMensaje(data.mensaje,data.clase);
             },
             "json"
         );
        } catch (error) {
            mostrarMensaje(error,'mensaje_error');
        }
        
        return false;
    });

    $("#btnAceptarPregunta").click(function (e) { 
        e.preventDefault();

        let result = {};
        $("#pregunta,#proceso").fadeOut();

        $.each($("#formProceso").serializeArray(),function(){
            result[this.name] = this.value;
        });

        $("#esperar").fadeIn();

        $.post(RUTA+"aprobacion/confirma", {pedido:$("#codigo_pedido").val(),
                                            detalles:JSON.stringify(itemsPreview()),
                                            estado:54,
                                            cabecera:result},
            function (data, textStatus, jqXHR) {              
                $("#esperar").fadeOut();
                $("#tablaPrincipal tbody")
                    .empty()
                    .append(data.pedidos);
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

    $("#btnConsulta").on('click', function(e) {
        e.preventDefault();

        let str = $("#formConsulta").serialize();

        $.post(RUTA+"aprobacion/filtroPedidos", str,
            function (data, text, requestXHR) {
                $("#tablaPrincipal tbody")
                    .empty()
                    .append(data);
            },
            "text"
        );
        
        return false
    });

    //Anular pedido

    $("#cancelRequest").click(function (e) { 
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

        $.post(RUTA+"aprobacion/anulapedido", {id:$("#codigo_pedido").val()},
            function (data, textStatus, jqXHR) {
                $("#preguntaAnula").fadeOut();
                $("#ventanaEspera").fadeOut();

                mostrarMensaje(data,"mensaje_correcto");
            },
            "text"
        );
        
        return false;
    });

   
    //Cancelar pedido

    $("#returnRequest").click(function (e) { 
        e.preventDefault();
        
        $("#preguntaCancela").fadeIn();

        return false;
    });

    $("#btnCancelarCancela").click(function (e) { 
        e.preventDefault();

        $("#preguntaCancela").fadeOut();
        
        return false;
    });

    $("#btnAceptarCancela").click(function (e) { 
        e.preventDefault();

        $("#ventanaEspera").fadeIn();

        $.post(RUTA+"aprobacion/cancelapedido", {id:$("#codigo_pedido").val()},
            function (data, textStatus, jqXHR) {
                $("#preguntaAnula").fadeOut();
                $("#ventanaEspera").fadeIn();

                mostrarMensaje(data,"mensaje_correcto");
            },
            "text"
        );
        
        return false;
    });
})

itemsPreview = () =>{
    DATA = [];
    let TABLA = $("#tablaDetalles tbody >tr");

    TABLA.each(function(){
        let ITEM        = $(this).find('td').eq(0).text(),
            CODIGO      = $(this).find('td').eq(1).text(),
            DESCRIPCION = $(this).find('td').eq(2).text(),
            UNIDAD      = $(this).find('td').eq(3).text(),
            CANTIDAD    = $(this).find('td').eq(4).text(),
            ATENDIDA    = $(this).find('td').eq(5).text(),
            APROBADA    = $(this).find('td').eq(6).children().val(),
            NROPARTE    = $(this).find('td').eq(7).text(),
            OBSERVA     = $(this).find('td').eq(8).children().val(),
            VERIFICA    = true;
            //VERIFICA    = $(this).find('td').eq(9).children().prop("checked"),  //aca esta el problema
            ITEMPEDIDO  = $(this).data('idx');
            OBSERVAC    = ""

        item= {};
        
        if (VERIFICA){
            item['item']        = ITEM;
            item['codigo']      = CODIGO;
            item['descripcion'] = DESCRIPCION;
            item['unidad']      = UNIDAD;
            item['cantidad']    = CANTIDAD;
            item['nroparte']    = NROPARTE;
            item['itempedido']  = ITEMPEDIDO;
            item['observa']     = OBSERVA;
            item['atendida']    = ATENDIDA;
            item['aprobada']    = APROBADA;
            item['verifica']    = VERIFICA;
    
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