$(function(){
    let accion = "u";

    $("#esperar").fadeOut();

    $("#tablaPrincipal tbody").on('click','tr', function(e) {
        e.preventDefault();

        $.post(RUTA+"transferencias/consultID",{id:$(this).data('indice')},
            function (data, text, requestXHR) {

                let numero = $.strPad(data.cabecera[0].idreg,6);

                $("#codigo_costos_origen").val(data.cabecera[0].codigo_origen);
                $("#codigo_costos_destino").val(data.cabecera[0].codigo_destino);
                $("#numero").val(numero);
                //$("#fecha").val(ftraslado);
                $("#corigen").val(data.cabecera[0].costo_origen);
                $("#cdestino").val(data.cabecera[0].costo_destino);
                $("#aprueba").val(data.cabecera[0].cnombres);
                $("#almacen_origen_despacho").val(data.cabecera[0].origen);
                $("#almacen_destino_despacho").val(data.cabecera[0].destino);
                $("#tipo").val(data.cabecera[0].cdescripcion);
                $("#codigo_transferencia").val(data.cabecera[0].idreg);

                $("#tablaDetalles tbody")
                    .empty()
                    .append(data.detalles);

                if (data.guias.length == 1) {
                    $("#id_guia").val(data.guias[0].idreg);
                    $("#numero_guia").val(data.guias[0].cnumguia);
                    $("#fgemision").val(data.guias[0].fguia);
                    $("#ftraslado").val(data.guias[0].ftraslado);
                    $("#almacen_origen").val(data.guias[0].corigen);
                    $("#almacen_origen_direccion").val(data.guias[0].cdirorigen);
                    $("#almacen_destino").val(data.guias[0].cdestino);
                    $("#almacen_destino_direccion").val(data.guias[0].cdirdest);
                    $("#empresa_transporte_razon").val(data.guias[0].centi);
                    $("#direccion_proveedor").val(data.guias[0].centidir);
                    $("#ruc_proveedor").val(data.guias[0].centiruc);
                    $("#modalidad_traslado").val(data.guias[0].ctraslado);
                    $("#tipo_envio").val(data.guias[0].cenvio);
                    $("#autoriza").val(data.guias[0].cautoriza);
                    $("#destinatario").val(data.guias[0].cdestinatario);
                    $("#observaciones").val(data.guias[0].cobserva);
                    $("#nombre_conductor").val(data.guias[0].cnombre);
                    $("#licencia_conducir").val(data.guias[0].clicencia);
                    $("#marca").val(data.guias[0].cmarca);
                    $("#placa").val(data.guias[0].cplaca);
                }
            },
            "json"
        );

        $("#proceso").fadeIn();

        return false;
    });

    $("#nuevoRegistro").click(function (e) { 
        e.preventDefault();

        document.getElementById("guiaremision").reset();
        document.getElementById("formProceso").reset();

        $("#estado")
            .removeClass()
            .addClass("textoCentro estado w100por procesando");
        $("#tablaDetalles tbody").empty();
        $("#proceso").fadeIn();

        $("#tipo").val("SALIDA X OC");
        $("#codigo_movimiento").val(144);

        $('#barra_notifica').removeClass('primerabarra');
        
        accion = 'n';

        return false;
    });

    $("#closeProcess").click(function (e) { 
        e.preventDefault();

        $("form")[0].reset();
        $("form")[1].reset();

        document.getElementById("guiaremision").reset();

        /*$.post(RUTA+"salida/actualizaDespachos",
            function (data, textStatus, jqXHR) {
                $(".itemsTabla table tbody")
                    .empty()
                    .append(data);

                $("#proceso").fadeOut(function(){
                    grabado = false;
                    $("form")[0].reset();
                    $("form")[1].reset();
                    $("form")[2].reset();
                });
            },
            "text"
        );*/
        
        $("#proceso").fadeOut();
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
        let almacen = $(this).data("almacen");
        
        control.slideUp()
        destino.val($(this).text());
        id = destino.attr("id");

        if (contenedor_padre == "listaCostosOrigen"){
            $("#codigo_costos_origen").val(codigo);
            $("#codigo_almacen_origen").val(almacen);
        }else if(contenedor_padre == "listaCostosDestino"){
            $("#codigo_costos_destino").val(codigo);
            $("#codigo_almacen_destino").val(almacen);
        }else if(contenedor_padre == "listaAprueba"){
            $("#codigo_aprueba").val(codigo);
        }else if(contenedor_padre == "listaAprueba"){
            $("#codigo_aprueba").val(codigo);
        }else if(contenedor_padre == "listaOrigen"){
            $("#codigo_almacen_origen").val(codigo);
        }else if(contenedor_padre == "listaDestino"){
            $("#codigo_almacen_destino").val(codigo);
        }else if(contenedor_padre == "listaMovimiento"){
            $("#codigo_movimiento").val(codigo);
        }

        return false;
    });

    $(".tituloVentana").on("click","a", function (e) {
        e.preventDefault();

        $(this).parent().parent().parent().parent().fadeOut();

        return false;
    });

    $("#guiaRemision").click(function (e) { 
        e.preventDefault();
        
        try {
            if ($("#numero").val().length == 0 ) throw "Grabe el documento";

            $("#vistadocumento").fadeIn();

        } catch (error) {
            mostrarMensaje(error,'mensaje_error');
        }

        return false;
    });

    $("#tablaDetalles tbody").on('blur','input', function (e) {
        let ingreso     = parseInt($(this).parent().parent().find("td").eq(8).children().val()),
            atendida    = parseInt($(this).parent().parent().find("td").eq(7).text()),
            aprobado    = parseInt($(this).parent().parent().find("td").eq(6).text()),
            
            registrado = ingreso+atendida;

        if (registrado > aprobado ) {
            mostrarMensaje('La cantidad ingresada, es mayor a lo aprobado','mensaje_error')
            return false;
        }

    });

    $("#tablaDetalles tbody").on('keypress','input', function (e) {
        if (e.which == 13) {
            $('#tablaDetalles tbody tr:last').find('td').eq(5).children().focus();
        }
    });

    $("#tablaDetalles tbody").on('click','a', function (e) {
        e.preventDefault();

        return false;
    });

    $(".tituloDocumento").on("click","#closeDocument", function (e) {
        e.preventDefault();

        $(this).parent().parent().parent().parent().parent().fadeOut();

        return false;
    });

    $("#saveRegister").click(function(e){
        e.preventDefault();

        let result = {},
            pedido = $("#tablaDetalles tbody >tr").data("pedido");

        $.each($("#formProceso").serializeArray(),function(){
            result[this.name] = this.value;
        });

        try {

            //if  ( checkCantTables($("#tablaDetalles tbody > tr"),6) ) throw "Revise las cantidades ingresadas";

            if (accion == "n") {
                
                $("#esperar").css("opacity","1").fadeIn();

                $.post(RUTA+"transferencias/registro",{cabecera:result,
                                                    detalles:JSON.stringify(detalles(false)),
                                                    idpedido:pedido,
                                                    atendidos:suma_atendidos()},
                    function (data, textStatus, jqXHR) {
                        if(data.estado){
                            mostrarMensaje(data.mensaje,"mensaje_correcto");
                            $("#numero").val(data.documento);
                            $("#codigo_transferencia").val(data.indice);
                        }else{
                            mostrarMensaje(data.mensaje,"mensaje_error");
                        }

                        $("#esperar").css("opacity","0").fadeOut();

                        accion = "d";
                    },
                    "json"
                );
            }

        } catch (error) {
            mostrarMensaje(error,'mensaje_error');
        }

        return false;
    });

    $(".cerrarLista").focus(function (e) { 
        e.preventDefault();
        
        $(".lista").fadeOut();

        return false;
    });
    
    $(".btnCallMenu").click(function (e) { 
        e.preventDefault();
        
        $(this).next().fadeIn();

        return false
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
            $("#autoriza").val($(this).text());
            $("#codigo_autoriza").val(codigo);
        }else if(contenedor_padre == "listaDespacha"){
            $("#codigo_despacha").val(codigo);
        }else if(contenedor_padre == "listaDestinatario"){
            $("#destinatario").val($(this).text());
            $("#codigo_destinatario").val(codigo);
        }else if(contenedor_padre == "listaModalidad"){
            $("#modalidad_traslado").val($(this).text());
            $("#codigo_modalidad").val(codigo);
        }else if(contenedor_padre == "listaEnvio"){
            $("#tipo_envio").val($(this).text());
            $("#codigo_tipo").val(codigo);
        }else if(contenedor_padre == "listaEntidad"){
            $("#codigo_entidad_transporte").val(codigo);
            $("#empresa_transporte_razon").val($(this).text());
            $("#ruc_proveedor").val($(this).data("ruc"));
            $("#direccion_proveedor").val($(this).data("direccion"));
        }

        return false;
    });

    $("#importRequest").click(function (e) { 
        e.preventDefault();

        try {
            if ($("#codigo_aprueba").val() == 0) throw "Elija la persona que aprueba";
            if ($("#codigo_costos_origen").val() == 0) throw "Indique el centro de costos"; 

            $("#esperar").fadeIn();

            $.post(RUTA+"transferencias/pedidos", {cc:$("#codigo_costos_destino").val(),pedido:""},
                function (data, textStatus, jqXHR) {
                    $("#tablaPedidos tbody")
                        .empty()
                        .append(data);

                        $("#pedidos").fadeIn();
                        $("#esperar").fadeOut();
                },
                "text"
            );
        } catch (error) {
            mostrarMensaje(error,"mensaje_error");
        }
        
        return false
    });

    $("#tablaPedidos tbody").on("click","tr", function () {
        $.post(RUTA+"transferencias/items", {indice:$(this).data('indice'),origen:$("#codigo_costos_origen").val()},
            function (data, textStatus, jqXHR) {
                $("#tablaDetalles tbody")
                    .empty()
                    .append(data.items);

                if (data.respuesta){
                    $("#total_items").val(data.total_items);
                }else{
                    mostrarMensaje("No hay items marcados para atencion","mensaje_error");
                }
            },
            "json"
        );
    });

    $("#tablaDetalles tbody").on('dblclick','tr', function(e) {
        e.preventDefault();

        $(this).toggleClass('semaforoNaranja');

        if ($(this).hasClass('semaforoNaranja')){
            $(this).attr('data-separado',1);
        }else {
            $(this).attr('data-separado',0);
        }

        return false;
    });

    $("#txtBuscarPedido").keyup(function (e) { 
        if(e.which == 13) {
            $("#esperar").fadeIn();
            
            $.post(RUTA+"transferencias/pedidos", {cc:$("#codigo_costos_destino").val(),pedido:$(this).val()},
                function (data, textStatus, jqXHR) {
                    $("#tablaPedidos tbody")
                        .empty()
                        .append(data);
                    $("#esperar").fadeOut();
                },
                "text"
            );
        }
    });

    $("#saveDocument").click(function(e){
        e.preventDefault();

        let result = {};

        $.each($("#guiaremision").serializeArray(),function(){
            result[this.name] = this.value;
        });

        $.post(RUTA+"transferencias/GrabaGuia", {cabecera:result,
                                                nota: $("#codigo_transferencia").val(),
                                                operacion:accion},
            function (data, textStatus, jqXHR) {
                mostrarMensaje(data.mensaje,"mensaje_correcto");
                $("#guia").val(data.guia);
            },
            "json"
        );

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
            if (result['codigo_entidad'] == "") throw "Seleccione la empresa de transportes";
            if (result['codigo_traslado'] == "") throw "Seleccione la modalidad de traslado";
            
            
            $.post(RUTA+"transferencias/vistaPreviaGuiaRemisioNotas", {cabecera:result,
                                                            detalles:JSON.stringify(detalles(false)),
                                                            proyecto: $("#corigen").val()},
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

    $("#closePreview").click(function (e) { 
        e.preventDefault();

        $(".ventanaVistaPrevia iframe").attr("src","");
        $("#vistaprevia").fadeOut();

        return false;
    })

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
            
            $.post(RUTA+"transferencias/preImpresoGuiasTransf", {cabecera:result,
                                              detalles:JSON.stringify(detalles(false)),
                                              proyecto: $("#corigen").val(),
                                              nota: $("#codigo_transferencia").val(),
                                              operacion:accion},
                function (data, textStatus, jqXHR) {
                        
                       if (data.archivo !== ""){
                            $("#imprimir iframe")
                            .attr("src","")
                            .attr("src",data.archivo);

                            $("#imprimir").fadeIn(function(){
                                $("#imprimir").fadeOut();
                                document.getElementById("iFramePdf").contentWindow.print();
                            })
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

    $("#tablaPrincipalPedidos").on('click','tr', function(e) {
        e.preventDefault();

        $.post(RUTA+"transferencias/consultaId", {id:$(this).data("indice")},
            function (data, textStatus, jqXHR) {
                
                
                $("#numero_pedido").val($.strPad(data.cabecera[0].nrodoc,6));
                $("#vista_previa").val(data.cabecera[0].docfPdfPrev);
                $("#emision").val(data.cabecera[0].emision);
                $("#costos").val(data.cabecera[0].proyecto);
                $("#area").val(data.cabecera[0].area);
                $("#transporte").val(data.cabecera[0].transporte);
                $("#concepto").val(data.cabecera[0].concepto);
                $("#solicitante").val(data.cabecera[0].nombres);
                $("#tipo_pedido").val(data.cabecera[0].tipo);
                //$("#vence").val(data.cabecera[0].vence);
                $("#estado").val(data.cabecera[0].estado);
                $("#espec_items").val(data.cabecera[0].detalle);
                $("#partida").val(data.cabecera[0].cdescripcion);
               
               

                $("#tablaDetallesPedido tbody")
                    .empty()
                    .append(data.detalles);
                
                $("#vistaPedido").fadeIn();
            },
            "json"
        );

        return false;
    });

    $("#closeProcessRequest").click(function (e) { 
        e.preventDefault();

        $("#vistaPedido").fadeOut();
        
        return false;
    });

    $("#preview").click(function(e){
        e.preventDefault();

        $("#vistaTransferencia").fadeIn();

        return false;
    });
})

suma_atendidos = () => {
    let TABLA = $("#tablaDetalles tbody >tr"),
        suma = 0;

    TABLA.each(function(){
        cantidad = $(this).find('td').eq(8).children().val();

        if (cantidad != '')
            suma = parseFloat(cantidad) + suma;
    });

    return suma;
}

detalles = (flag) =>{
    DETALLES = [];

    let TABLA = $("#tablaDetalles tbody >tr");
    
    TABLA.each(function(){
        let ITEM            = $(this).find('td').eq(0).text(),
            IDPROD          = $(this).data("idprod"),
            GRABADO         = $(this).data("grabado"),
            COSTOS          = $(this).data("costos"),
            ORIGEN          = $("#codigo_almacen_origen").val(),
            CANTIDAD        = $(this).find('td').eq(6).children().val(),// cantidad
            OBSER           = $(this).find('td').eq(10).children().val(),
            CODIGO          = $(this).find('td').eq(1).text(),//codigo
            UNIDAD          = $(this).find('td').eq(3).text(),//unidad
            DESTINO         = $("#codigo_almacen_destino").val(),
            DESCRIPCION     = $(this).find('td').eq(2).text(),//unidad
            PEDIDO          = $(this).data("pedido"),
            IDITEM          = $(this).data("iditem"),
            APROBADO        = $(this).data("aprobado"),
            COMPRADO        = 0,
            NROPEDIDO       = $(this).find('td').eq(11).text(),
            SEPARADO        = $(this).data("separado"),
            VENCE           = $(this).find('td').eq(7).val(),
            CONDICION       = $(this).find('td').eq(8).val(),
            ATENDIDO        = null;

    
        item = {};

        if (!GRABADO) {
            item['item']         = ITEM;
            item['idprod']       = IDPROD;
            item['origen']       = ORIGEN;
            item['cantidad']     = CANTIDAD;
            item['obser']        = OBSER;
            item['codigo']       = CODIGO;
            item['descripcion']  = DESCRIPCION;
            item['unidad']       = UNIDAD;
            item['destino']      = DESTINO;
            item['iditem']       = IDITEM;
            item['pedido']       = PEDIDO;
            item['aprobado']     = APROBADO;
            item['comprado']     = COMPRADO;
            item['costos']       = COSTOS;
            item['nropedido']    = NROPEDIDO;
            item['separado']     = SEPARADO;
            item['atendido']     = ATENDIDO;
            item['vence']        = VENCE;
            item['condicion']    = CONDICION;
                
            DETALLES.push(item);
        }     
    })

    return DETALLES; 
}

