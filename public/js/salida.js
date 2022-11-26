$(function() {
    let accion = "",
        grabado = false,
        indice_nota=0;

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
        }else if(contenedor_padre == "listaOrigen"){
            $("#codigo_origen").val(codigo);
            $("#almacen_origen_direccion").val($(this).data('direccion'));
            $("#almacen_origen_dpto").val($(this).data('dpto'));
            $("#almacen_origen_prov").val($(this).data('prov'));
            $("#almacen_origen_dist").val($(this).data('dist'));
        }else if(contenedor_padre == "listaDestino"){
            $("#codigo_destino").val(codigo);
            $("#almacen_destino_direccion").val($(this).data('direccion'));
            $("#almacen_destino_dpto").val($(this).data('dpto'));
            $("#almacen_destino_prov").val($(this).data('prov'));
            $("#almacen_destino_dist").val($(this).data('dist'));
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
        }else if(contenedor_padre == "listaAlmacenDestino"){
            $("#codigo_almacen_destino").val(codigo);
        }else if(contenedor_padre == "listaEntidad"){
            $("#codigo_entidad_transporte").val(codigo);
            $("#direccion_entidad_transporte").val($(this).data('direccion'));
            $("#ruc_entidad_transporte").val($(this).data('ruc'));
        }

        return false;
    });

    $("#preview").click(function (e) { 
        e.preventDefault();

        try {
            let result = {};

            $.each($("#formProceso").serializeArray(),function(){
                result[this.name] = this.value;
            });

            //if (result['codigo_salida'] == "") throw "Por favor grabar el documento";

            $.post(RUTA+"salida/documentoPdf", {cabecera:result,
                                                detalles:JSON.stringify(detalles()),
                                                condicion:0},
                function (data, textStatus, jqXHR) {
                    $(".ventanaVistaPrevia iframe")
                    .attr("src","")
                    .attr("src",data);

                    $("#vistaprevia").fadeIn();
                },
                "text"
            );
            $("#vistaprevia").fadeIn();

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

    $("#guiaRemision").click(function (e) { 
        e.preventDefault();
        
         $.post(RUTA+"salida/almacenes", {origen:$("#codigo_almacen").val(),
                                            destino:$("#codigo_almacen_destino").val()},
            function (data, text, requestXHR) {
                $("#almacen_origen").val(data[0].almacen);
                $("#codigo_origen").val(data[0].ncodalm);
                $("#almacen_origen_direccion").val(data[0].direccion);
                $("#almacen_origen_dpto").val(data[0].dpto);
                $("#almacen_origen_prov").val(data[0].prov);
                $("#almacen_origen_dist").val(data[0].dist);

                $("#almacen_destino").val(data[1].almacen);
                $("#codigo_destino").val(data[1].ncodalm);
                $("#almacen_destino_direccion").val(data[1].direccion);
                $("#almacen_destino_dpto").val(data[1].dpto);
                $("#almacen_destino_prov").val(data[1].prov);
                $("#almacen_destino_dist").val(data[1].dist);
            },"json");          
        
        $("#vistadocumento").fadeIn();

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
            if (result['codigo_origen'] == "") throw "Seleccione Almacen origen";
            if (result['codigo_destino'] == "") throw "Seleccione Almacen destino";
            if (result['codigo_entidad'] == "") throw "Seleccione la empresa de transportes";
            if (result['codigo_traslado'] == "") throw "Seleccione la modalidad de traslado";
            if (result['codigo_autoriza'] == "") throw "Seleccione la persona que autoriza";
            
            $.post(RUTA+"salida/preImpreso", {cabecera:result,
                                                detalles:JSON.stringify(detalles())},
                function (data, textStatus, jqXHR) {
                        
                       if (data.archivo !== ""){
                            $("#imprimir #iFramePdf").attr("src",data.archivo);
                       }
                    },
                    "json"
            );

            ;        

        } catch (error) {
            mostrarMensaje(error,'mensaje_error');
        }

        return false;
    });

    $("#imprimir #iFramePdf").on('load', function() {
        $("iframe")[2].contentWindow.print();
    })

    $("#updateDocument").click(function(e) {
        e.preventDefault()

        try {

            let result = {};

            $.each($("#formProceso").serializeArray(),function(){
                result[this.name] = this.value;
            });

            $.post(RUTA+"salida/cerrarNota", {cabecera:result,
                                              detalles:JSON.stringify(detalles()),
                                              despacho:$("#codigo_salida").val(),
                                              pedido:$("#codigo_pedido").val(),
                                              orden:$("#codigo_orden").val(),
                                              ingreso:$("#codigo_ingreso").val(),},
                function (data, textStatus, jqXHR) {
                    $(".itemsTabla table tbody")
                    .empty()
                    .append(data);

                    $("#proceso").fadeOut(function(){
                        grabado = false;
                        $("form")[0].reset();
                        $("form")[1].reset();
                    
                    });
                },"text");


        } catch (error) {
            mostrarMensaje(error,'mensaje_error');
        }

        return false;
    })

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

        if(contenedor_padre == "listaCostos"){
            $("#codigo_costos").val(codigo);

            $.post(RUTA+"salida/ultimoIndice",
                function (data, text, requestXHR) {
                    $("#numero").val(data.salida);
                },
                "text"
            );
        }

        return false;
    });

    $("#atachDocs").click(function(e){
        e.preventDefault();

        console.log(detalles());

        return false;
    });

    $(".action-button").click(function (e) { 
        e.preventDefault();
        
        console.log("Mando Guia");

        return false;
    });

    $("#inputSearch").keyup(function (e) { 
        if(e.which == 13) {
            $("#esperar").fadeIn();
            
            $.post(RUTA+"salida/filtraIngreso", {id:$(this).val()},
                function (data, textStatus, jqXHR) {
                    $("#notas tbody")
                        .empty()
                        .append(data);
                    $("#esperar").fadeOut();
                },
                "text"
            );
        }
    });

    $("#btnAceptItems").click(function (e) { 
        e.preventDefault();
        
        let TABLA = $("#notas tbody >tr");
        let id = [];

        TABLA.each(function(){
            let checked = $(this).find('td').eq(0).children().prop('checked');
            
            $("#codigo_movimiento").val(145);
            $("#tipo").val("SALIDA POR OC")
            
            if (checked) {
                $("#costos").val($(this).find('td').eq(2).text());
                $("#codigo_costos").val($(this).data('costos'));

                id.push($(this).data('ingreso'));
            }
        })

        try {
            if (id.length == 0 ) throw "No se selecciono ningún item";

            $.post(RUTA+"salida/llamarData", {data:JSON.stringify(id)},
                function (data, textStatus, jqXHR) {
                    $("#busqueda").fadeOut();
                    $("#numero").val(data.numero);
                    $("#tablaDetalles tbody")
                        .append(data.items);
                },
                "json"
            );
        } catch (error) {
            mostrarMensaje(error,"mensaje_error");
        }

        return false
    });

    $("#tablaDetalles tbody").on("click","a", function (e) {
        e.preventDefault();

        $(this).parent().parent().remove();
        fillTables($("#tablaDetalles tbody > tr"),1);

        return false;
    });

    $("#previewDocument").click(function (e) { 
        e.preventDefault();
        
        try {
            let result = {};

            $.each($("#guiaremision").serializeArray(),function(){
                result[this.name] = this.value;
            });

            if (result['numero_guia'] == "") throw "Ingrese el Nro. de Guia";
            if (result['codigo_origen'] == "") throw "Seleccione Almacen origen";
            if (result['codigo_destino'] == "") throw "Seleccione Almacen destino";
            if (result['codigo_entidad'] == "") throw "Seleccione la empresa de transportes";
            if (result['codigo_traslado'] == "") throw "Seleccione la modalidad de traslado";
            if (result['codigo_autoriza'] == "") throw "Seleccione la persona que autoriza";
            
            $.post(RUTA+"salida/vistaPreviaGuiaRemision", {cabecera:result,
                                                detalles:JSON.stringify(detalles())},
                function (data, textStatus, jqXHR) {
                        
                       if (data.archivo !== ""){
                            $(".ventanaVistaPrevia iframe")
                            .attr("src","")
                            .attr("src",data.archivo);
        
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
})


detalles = () =>{
    DETALLES = [];

    let TABLA = $("#tablaDetalles tbody >tr");
    
    TABLA.each(function(){
        let ITEM        = $(this).find('td').eq(1).text(),
            //IDDETORDEN  = $(this).data("idorden"),
            IDDETPED    = $(this).data("idpedido"),
            IDPROD      = $(this).data("idproducto"),
            //IDINGRESO   = $(this).data("idingreso"),
            //IDDESPACHO  = $(this).data("iddespacho"),
            PEDIDO      = $(this).data("pedido"),
            ORDEN       = $(this).data("orden"),
            INGRESO     = $(this).data("ingreso"),
            ALMACEN     = $("#codigo_almacen_origen").val(),
            CANTIDAD    = $(this).find('td').eq(5).text(),// cantidad
            CANTDESP    = $(this).find('td').eq(6).children().val(),
            OBSER       = $(this).find('td').eq(7).children().val(),
            CODIGO      = $(this).find('td').eq(2).text(),//codigo
            DESCRIPCION = $(this).find('td').eq(3).text(),//descripcion
            UNIDAD      = $(this).find('td').eq(4).text(),//unidad
            DESTINO     = $("#codigo_almacen_destino").val(),
    
        item = {};

        item['item']         = ITEM;
        //item['iddetorden'] = IDDETORDEN;
        item['iddetped']     = IDDETPED;
        //item['idingreso']  = IDINGRESO;
        //item['iddespacho'] = IDDESPACHO;
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
    })

    return DETALLES; 
}


