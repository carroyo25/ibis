$(function() {
    let accion = "",
        grabado = false,
        indice_nota=0;

    $("#esperar").fadeOut();

    $("#tablaPrincipal tbody").on("click","tr", function (e) {
        e.preventDefault();

        $.post(RUTA+"salida/salidaId", {id:$(this).data("indice")},
            function (data, textStatus, jqXHR) {
                
                let estado = "textoCentro w100por estado " + data.cabecera[0].cabrevia;
                
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
                $("#almacen_origen_despacho").val(data.cabecera[0].almacen);
                $("#almacen_destino_despacho").val(data.cabecera[0].destino);
                $("#fecha").val(data.cabecera[0].ffecdoc);
                $("#numero").val(data.cabecera[0].nnronota);
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
                $("#tipo").val(data.cabecera[0].cdescripcion);
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

        console.log(accion);
        
        $("#proceso").fadeIn();

        return false;
    });

    $("#nuevoRegistro").click(function (e) { 
        e.preventDefault();

        $("#estado")
            .removeClass()
            .addClass("textoCentro estado w100por procesando");
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
            $.post(RUTA+"salida/ingresos",
            function (data, textStatus, jqXHR) {
                $("#notas tbody")
                    .empty()
                    .append(data);

                $("#busqueda").fadeIn();
            },
            "text"
        );
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

    $("#notas tbody").on("click","tr", function (e) {
        e.preventDefault();

        $("#codigo_ingreso").val($(this).data("idnit"));

        $.post(RUTA+"salida/notaId", {id:$(this).data("idnit")},
            function (data, textStatus, jqXHR) {
                $("#codigo_costos").val(data.cabecera[0].ncodpry);
                $("#codigo_area").val(data.cabecera[0].ncodarea);
                //$("#codigo_aprueba").val(data.cabecera[0].aprueba);
                $("#codigo_almacen").val(data.cabecera[0].ncodalm1);
                $("#codigo_pedido").val(data.cabecera[0].idref_pedi);
                $("#codigo_orden").val(data.cabecera[0].idref_abas);
                $("#codigo_estado").val(data.cabecera[0].nEstadoDoc);
                $("#costos").val(data.cabecera[0].proyecto);
                $("#area").val(data.cabecera[0].area);
                $("#solicita").val(data.cabecera[0].solicita);
                //$("#aprueba").val(data.cabecera[0].cnombres);
                $("#almacen_origen_despacho").val(data.cabecera[0].almacen);
                $("#pedido").val(data.cabecera[0].pedido);
                $("#fecha_pedido").val(data.cabecera[0].emision);
                $("#orden").val(data.cabecera[0].orden);
                $("#fecha_orden").val(data.cabecera[0].ffechadoc);
                $("#concepto").val(data.cabecera[0].concepto);
                $("#estado").val(data.cabecera[0].cdescripcion);
                $("#guia").val(data.cabecera[0].cnumguia);
                $("#numero").val(data.numero.numero);
                $("#movimiento").val(data.movimiento);

                $("#tablaDetalles tbody")
                    .empty()
                    .append(data.detalles)

                $("#busqueda").fadeOut();
            },
            "json"
        );

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

            if (result['codigo_salida'] == "") throw "Por favor grabar el documento";

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
                if (result['codigo_ingreso'] == "") throw "debe grabar la nota de despacho";
                if (result['codigo_movimiento'] == "") throw "Elija el tipo de movimiento";
                if (result['codigo_ingreso'] == "") throw "Seleccione una nota de ingreso";
                if (result['codigo_aprueba'] == "") throw "Seleccione la persona que aprueba";
                if (result['codigo_almacen_destino'] == "") throw "Seleccione la persona que aprueba";
            
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
            if (result['codigo_autoriza'] == "") throw "Seleccione la persona que autoriza";
            if (result['codigo_destinatario'] == "") throw "Seleccione el destinatario";
            if (result['codigo_traslado'] == "") throw "Seleccione la modalidad de traslado";
            if (result['codigo_tipo'] == "") throw "Seleccione el tipo de envio";
            if (result['nro_bultos'] == "") throw "Indique el Nro. de bultos";
            if (result['peso_bruto'] == "") throw "Indique el peso bruto";
            //if (result['nombre_conductor'] == "") throw "Escriba el nombre del conductor";
            //if (result['placa'] == "") throw "Indique la placa del vehiculo";

            $.post(RUTA+"salida/guiaremision", {cabecera:result,
                                                detalles:JSON.stringify(detalles()),
                                                despacho:$("#codigo_salida").val(),
                                                pedido:$("#codigo_pedido").val(),
                                                orden:$("#codigo_orden").val(),
                                                ingreso:$("#codigo_ingreso").val(),},
                function (data, textStatus, jqXHR) {
                        
                       if (data !== ""){
                            $("#iFramePdf").attr("src",data);
                            $("#vistadocumento").fadeOut();
                       }
                    },
                    "text"
                );


        } catch (error) {
            mostrarMensaje(error,'mensaje_error');
        }

        return false;
    });

    $("#iFramePdf").on('load', function() {
        $("iframe")[1].contentWindow.print();
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

})

detalles = () =>{
    DETALLES = [];

    let TABLA = $("#tablaDetalles tbody >tr");
    
    TABLA.each(function(){
        let ITEM        = $(this).find('td').eq(1).text(),
            IDDETORDEN  = $(this).data("itemorden"),
            IDDETPED    = $(this).data("itempedido"),
            IDPROD      = $(this).data("idproducto"),
            PEDIDO      = $("#codigo_pedido").val(),
            ORDEN       = $("#codigo_orden").val(),
            ALMACEN     = $("#codigo_almacen").val(),
            INGRESO     = $("#codigo_ingreso").val(),
            CANTIDAD    = $(this).find('td').eq(6).children().val(),// cantidad
            OBSER       = $(this).find('td').eq(7).children().val(),
            VENCE       = "",
            SERIE       = "",
            CODIGO      = $(this).find('td').eq(2).text(),//codigo
            DESCRIPCION = $(this).find('td').eq(3).text(),//descripcion
            UNIDAD      = $(this).find('td').eq(4).text(),//unidad
            NESTADO     = '',
            CESTADO     = '',
            UBICACION   = "",
            DESTINO     = $("#codigo_almacen_destino").val(),
            CANTDESP    = $(this).find('td').eq(5).text();
    
        item = {};

        item['item']        = ITEM;
        item['iddetorden']  = IDDETORDEN;
        item['iddetped']    = IDDETPED;
        item['idprod']      = IDPROD;
        item['pedido']      = ORDEN;
        item['orden']       = PEDIDO;
        item['almacen']     = ALMACEN;
        item['cantidad']    = CANTIDAD;
        item['obser']       = OBSER;
        item['vence']       = VENCE;
        item['serie']       = SERIE;
        item['ingreso']     = INGRESO;

        item['codigo']     = CODIGO;
        item['descripcion']= DESCRIPCION;
        item['unidad']     = UNIDAD;
        item['nestado']    = NESTADO;
        item['cestado']    = CESTADO;
        item['ubicacion']  = UBICACION;
        item['cantdesp']   = CANTDESP;

        item['destino'] = DESTINO;

        DETALLES.push(item);
    })

    return DETALLES; 
}

printTrigger = (elementId) => {
    let getMyFrame = document.getElementById(elementId); 
        getMyFrame.focus(); 
        getMyFrame.contentWindow.print();
}