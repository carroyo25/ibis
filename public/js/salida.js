$(function() {
    let accion = "",
        tipoVista = null;

    $("#esperar").fadeOut();

    $("#tablaPrincipal tbody").on("click","tr", function (e) {
        e.preventDefault();

        $.post(RUTA+"salida/salidaId", {id:$(this).data("indice")},
            function (data, textStatus, jqXHR) {
                
                let estado = "textoCentro w100por estado " + data.cabecera[0].cabrevia,
                    numero = $.strPad(data.cabecera[0].id_regalm,6);
                
                $("#codigo_costos").val(data.cabecera[0].ncodpry);
                $("#codigo_area").val(data.cabecera[0].ncodarea);
                $("#codigo_movimiento").val(data.cabecera[0].ncodmov);
                $("#codigo_aprueba").val(data.cabecera[0].id_userAprob);
                $("#codigo_almacen").val(data.cabecera[0].ncodalm1);
                $("#codigo_almacen_destino").val(data.cabecera[0].ncodalm2);
                $("#codigo_pedido").val(data.cabecera[0].idref_pedi);
                $("#codigo_orden").val(data.cabecera[0].idref_ord);
                $("#codigo_estado").val(data.cabecera[0].nEstadoDoc);
                $("#codigo_entidad").val(data.cabecera[0].id_centi);
                $("#codigo_ingreso").val(data.cabecera[0].idref_abas);
                $("#codigo_salida").val(data.cabecera[0].id_regalm);
                $("#almacen_origen_despacho").val(data.cabecera[0].origen);
                $("#almacen_destino_despacho").val(data.cabecera[0].destino);
                $("#fecha").val(data.cabecera[0].ffecdoc);
                $("#numero").val(numero);
                $("#costos").val(data.cabecera[0].costos);
                $("#area").val(data.cabecera[0].area);
                $("#solicita").val(data.cabecera[0].nombres);
                $("#orden").val(data.cabecera[0].orden);
                $("#pedido").val(data.cabecera[0].pedido);
                $("#ruc").val(data.cabecera[0].cnumdoc);
                $("#guia").val(data.cabecera[0].cnumguia);
                $("#razon").val(data.cabecera[0].crazonsoc);
                $("#concepto").val(data.cabecera[0].concepto);
                $("#aprueba").val(data.cabecera[0].cnombres);
                $("#tipo").val(data.cabecera[0].tipo_movimiento);
                $("#estado").val(data.cabecera[0].estado);
                $("#movimiento").val(data.cabecera[0].movimiento);
                $("#fecha_pedido").val(data.cabecera[0].emision);
                $("#fecha_orden").val(data.cabecera[0].ffechadoc);
                
                
                $("#estado")
                    .removeClass()
                    .addClass(estado);
                
                $("#tablaDetalles tbody")
                    .empty()
                    .append(data.detalles)
            },
            "json"
        );

        accion = "u";
        grabado = true;

        $("#proceso").fadeIn();

        return false;
    });

    $("#nuevoRegistro").click(function (e) { 
        e.preventDefault();

        $("#estado")
            .removeClass()
            .addClass("textoCentro estado w100por procesando");
        $("#tablaDetalles tbody").empty();
        $("#proceso").fadeIn();

        $("#tipo").val("SALIDA X OC");
        $("#codigo_movimiento").val(144);
        
        accion = 'n';

        return false;
    });

    $("#closeProcess").click(function (e) { 
        e.preventDefault();

        $.post(RUTA+"salida/actualizaDespachos",
            function (data, textStatus, jqXHR) {
                $(".itemsTabla table tbody")
                    .empty()
                    .append(data);

                $("#proceso").fadeOut(function(){
                    grabado = false;
                    $("form")[0].reset();
                    $("form")[1].reset();
                });
            },
            "text"
        );

        return false;
    });

    $("#importData").click(function (e) { 
        e.preventDefault();
        
        if ( accion == 'n') {
            try {
                $.post(RUTA+"salida/ordenes",
                function (data, textStatus, jqXHR) {
                    $("#ordenes tbody")
                        .empty()
                        .append(data);

                    $("#busqueda").fadeIn();
                },
                "text"
                );
                
            } catch (error) {
                mostrarMensaje(error,'mensaje_error');
            }
                
        }else{
            mostrarMensaje('El despacho se esta procesando','mensaje_error');
        }

        return false
    });

    $(".tituloVentana").on("click","a", function (e) {
        e.preventDefault();
        
        $(this).parent().parent().parent().parent().fadeOut();

        return false;
    });

    $(".mostrarLista").focus(function (e) { 
        e.preventDefault();

        if (accion !="n") {
            return false;
        }
        
        $(this).next().slideDown();

        return false;
    });
    
    $(".mostrarListaInterna").focus(function (e) { 
        e.preventDefault();
        
        $(this).next().slideDown();

        return false;
    });

    $(".cerrarLista").focus(function (e) { 
        e.preventDefault();
        
        $(".lista").fadeOut();

        return false;
    });

    $(".lista").on("click",'a', function (e) {
        e.preventDefault();

        let control = $(this).parent().parent().parent();
        let destino = $(this).parent().parent().parent().prev();
        let contenedor_padre = $(this).parent().parent().parent().attr("id");
        let id = "";
        let codigo = $(this).attr("href");
        
        control.slideUp()

        destino.val($(this).text());
        id = destino.attr("id");

        if(contenedor_padre == "listaMovimiento"){
            $("#codigo_movimiento").val(codigo);
        }else if(contenedor_padre == "listaAprueba"){
            $("#codigo_aprueba").val(codigo);
            $("#autoriza").val($(this).text());
        }else if(contenedor_padre == "listaOrigen"){
            $("#codigo_almacen_origen").val(codigo);
            $("#codigo_origen").val(codigo);
            $("#almacen_origen").val($(this).text());
            $("#almacen_origen_direccion").val($(this).data('direccion'));
        }else if(contenedor_padre == "listaDestino"){
            $("#codigo_almacen_destino").val(codigo);
            $("#almacen_destino").val($(this).text());
            $("#almacen_destino_direccion").val($(this).data('direccion'));
        }else if(contenedor_padre == "listaAutoriza"){
            $("#codigo_autoriza").val(codigo);
        }else if(contenedor_padre == "listaDespacha"){
            $("#codigo_despacha").val(codigo);
        }else if(contenedor_padre == "listaDestinatario"){
            $("#codigo_destinatario").val(codigo);
        }else if(contenedor_padre == "listaModalidad"){
            $("#codigo_modalidad").val(codigo);
        }else if(contenedor_padre == "listaEnvio"){
            $("#codigo_tipo").val(codigo);
        }else if(contenedor_padre == "listaEntidad"){
            $("codigo_entidad_transporte").val(codigo)
            $("#ruc_proveedor").val($(this).data("ruc"));
            $("#direccion_proveedor").val($(this).data("direccion"));
        }

        return false;
    });

    //genera la nota de salida
    $("#preview").click(function (e) { 
        e.preventDefault();

        try {
            let result = {};

            $.each($("#formProceso").serializeArray(),function(){
                result[this.name] = this.value;
            });

            if (tipoVista == null) throw "Por favor grabar el documento";

            $.post(RUTA+"salida/documentoPdf", {cabecera:result,
                                                detalles:JSON.stringify(detalles(tipoVista)),
                                                condicion:0},
                function (data, textStatus, jqXHR) {
                    $(".ventanaVistaPrevia iframe")
                    .attr("src","")
                    .attr("src",data);

                    $("#vistaprevia").fadeIn();
                },
                "text"
            );

        } catch (error) {
            mostrarMensaje(error,'mensaje_error');
        }

        return false;
    });

    $("#saveDoc").click(function (e) { 
        e.preventDefault();

        try {
           let result = {};

            $.each($("#formProceso").serializeArray(),function(){
                result[this.name] = this.value;
            });
            
            if (accion == 'n'){
                if (result['codigo_movimiento'] == "") throw "Elija el tipo de movimiento";
                if (result['codigo_aprueba'] == "") throw "Seleccione la persona que aprueba";
                if (result['codigo_almacen_destino'] == "") throw "Seleccione el almacen destino";
            
                $.post(RUTA+"salida/nuevaSalida", {cabecera:result,
                                                   detalles:JSON.stringify(detalles())},
                    function (data, textStatus, jqXHR) {
                        mostrarMensaje(data.mensaje,data.clase);
                        $("#codigo_salida").val(data.indice);
                    },
                    "json"
                );

            }
        } catch (error) {
            mostrarMensaje(error,'mensaje_error');
        }

        return false;
    });

    $("#closePreview").click(function (e) { 
        e.preventDefault();

        $(".ventanaVistaPrevia iframe").attr("src","");
        $("#vistaprevia").fadeOut();

        return false;
    });

    $(".tituloDocumento").on("click","#closeDocument", function (e) {
        e.preventDefault();

        $(this).parent().parent().parent().parent().parent().fadeOut();

        return false;
    });

    $("#ordenes tbody").on("click","tr", function (e) {
        e.preventDefault();

        let codigo_costos = $(this).data("idcosto"); 

        $.post(RUTA+"salida/ordenId", {id:$(this).data("orden"),costo:$(this).data("idcosto")},
            function (data, textStatus, jqXHR) {
                $("#numero").val(data.numero);
                $("#tablaDetalles tbody").append(data.items);
                $("#costos").val(data.costos);
                $("#codigo_costos").val(codigo_costos);
            },
            "json"
        );

        return false; 
    });

    $("#btnPendientes, #btnTotales").click(function (e) { 
        e.preventDefault();

        tipoVista = e.target.id == "btnTotales"?true:false;

        let result = {};

        $.each($("#formProceso").serializeArray(),function(){
            result[this.name] = this.value;
        });

        try {
            if(accion != "n") throw "Documento registrado";
            if (result['codigo_almacen_origen'] == '') throw "Elija el Almacen";
            if (result['codigo_almacen_destino'] == '') throw "Elija el Almacen";
            if (result['codigo_aprueba'] == '') throw "Elija la persona que aprueba";

            console.log(detalles(tipoVista));

            if (accion == "n") {
                /*$.post(RUTA+"recepcion/nuevoIngreso", {cabecera:result,
                    detalles:JSON.stringify(detalles(tipoVista)),
                    series:JSON.stringify(series())},
                        function (data, textStatus, jqXHR) {
                            $("#codigo_ingreso").val(data.indice);
                            mostrarMensaje("Nota Grabada","mensaje_correcto");
                            $("#tablaPrincipal tbody")
                                .empty()
                                .append(data.listado);
                            accion = "u";
                        },
                        "json"
                    );*/
            }

        } catch (error) {
            mostrarMensaje(error,'mensaje_error');
        }
        
        return false;
    });
    
    $("#ordenSearch").keyup(function (e) { 
        if(e.which == 13) {
            $("#esperar").fadeIn();
            
            $.post(RUTA+"salida/filtraOrden", {id:$(this).val()},
                function (data, textStatus, jqXHR) {
                    $("#ordenes tbody")
                        .empty()
                        .append(data);
                    $("#esperar").fadeOut();
                },
                "text"
            );
        }
    });

    $("#guiaRemision").click(function (e) { 
        e.preventDefault();
        
        try {
            if (tipoVista == null) throw "Grabe el documento";

            $("#vistadocumento").fadeIn();

        } catch (error) {
            mostrarMensaje(error,'mensaje_error');
        }
         
        return false;
    });

      //filtrado en la lista de solicitante
    $(".busqueda").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $(this).next().attr("id");

        //aignar a una variable el contenido
        let l = "#"+ $(this).next().attr("id")+ " li a"

        $(l).filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });

    $("#previewDocument").click(function (e) { 
        e.preventDefault();
        
        try {
            let result = {};

            $.each($("#guiaremision").serializeArray(),function(){
                result[this.name] = this.value;
            });

            if (result['numero_guia'] == "") throw "Ingrese el Nro. de Guia";
            if (result['codigo_entidad'] == "") throw "Seleccione la empresa de transportes";
            if (result['codigo_traslado'] == "") throw "Seleccione la modalidad de traslado";
            
            
            $.post(RUTA+"salida/vistaPreviaGuiaRemision", {cabecera:result,
                                                            detalles:JSON.stringify(detalles(tipoVista)),
                                                            proyecto: $("#costos").val()},
                function (data, textStatus, jqXHR) {
                        
                       if (data.archivo !== ""){
                            $(".ventanaVistaPrevia object")
                            .attr("data","")
                            .attr("data",data.archivo);
        
                            $("#vistaprevia").fadeIn();
                       }
                    },
                    "json"
            );
        } catch (error) {
            mostrarMensaje(error,'mensaje_error');
        }
        
        return false
    });

    $("#btnAceptItems").click(function (e) { 
        e.preventDefault();
        
        $("#busqueda").fadeOut();

        return false;
    });

    $("#printDocument").click(function (e) { 
        e.preventDefault();
        
        try {
            let result = {};

            $.each($("#guiaremision").serializeArray(),function(){
                result[this.name] = this.value;
            });

            if (result['numero_guia'] == "") throw "Ingrese el Nro. de Guia";
            if (result['codigo_entidad'] == "") throw "Seleccione la empresa de transportes";
            if (result['codigo_traslado'] == "") throw "Seleccione la modalidad de traslado";
            
            $.post(RUTA+"salida/preImpreso", {cabecera:result,
                                                            detalles:JSON.stringify(detalles(tipoVista)),
                                                            proyecto: $("#costos").val()},
                function (data, textStatus, jqXHR) {
                        
                       if (data.archivo !== ""){
                            $(".ventanaVistaPrevia iframe")
                            .attr("src","")
                            .attr("src",data.archivo);

                            $("#imprimir").fadeIn();
                       }
                    },
                    "json"
            );
        } catch (error) {
            mostrarMensaje(error,'mensaje_error');
        }

        return false;
    });

    $("#closePreviewPrinter").click(function (e) { 
        e.preventDefault();
        
        $("#imprimir").fadeOut();

        return false;
    });

    /*$("#imprimir #iFramePdf").on('load', function() {
        $("iframe")[0].contentWindow.print();
    })*/
    
})


detalles = (flag) =>{
    DETALLES = [];

    let TABLA = $("#tablaDetalles tbody >tr");
    
    TABLA.each(function(){
        let ITEM        = $(this).find('td').eq(1).text(),
            IDDETORDEN  = $(this).data("idorden"),
            IDDETPED    = $(this).data("iddetped"),
            IDPROD      = $(this).data("idprod"),
            PEDIDO      = $(this).find('td').eq(9).text(),
            ORDEN       = $(this).find('td').eq(10).text(),
            INGRESO     = $(this).data("ingreso"),
            ALMACEN     = $("#codigo_almacen_origen").val(),
            CANTIDAD    = $(this).find('td').eq(6).text(),// cantidad
            CANTDESP    = $(this).find('td').eq(7).children().val(),
            OBSER       = $(this).find('td').eq(8).children().val(),
            CODIGO      = $(this).find('td').eq(3).text(),//codigo
            DESCRIPCION = $(this).find('td').eq(4).text(),//descripcion
            UNIDAD      = $(this).find('td').eq(5).text(),//unidad
            DESTINO     = $("#codigo_almacen_destino").val(),
            CHECKED     = $(this).find('td').eq(1).children().prop("checked");//codigo
    
        item = {};

        if (CHECKED == flag) {
            item['item']         = ITEM;
            item['iddetorden']   = IDDETORDEN;
            item['iddetped']     = IDDETPED;
            item['idprod']       = IDPROD;
            item['pedido']       = ORDEN;
            item['orden']        = PEDIDO;
            item['ingreso']      = INGRESO;
            item['almacen']      = ALMACEN;
            item['cantidad']     = CANTIDAD;
            item['cantdesp']     = CANTDESP;
            item['obser']        = OBSER;

            item['codigo']       = CODIGO;
            item['descripcion']  = DESCRIPCION;
            item['unidad']       = UNIDAD;
            item['destino']      = DESTINO;
            
            DETALLES.push(item);
        }
    })

    return DETALLES; 
}


