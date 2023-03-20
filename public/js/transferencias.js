$(function(){
    $("#esperar").fadeOut();

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

        $("form")[0].reset();
        $("form")[1].reset();

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
        
        control.slideUp()
        destino.val($(this).text());
        id = destino.attr("id");

        if (contenedor_padre == "listaCostos"){
            $("#codigo_costos").val(codigo);
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

    $("#importData").click(function (e) { 
        e.preventDefault();

        try {
            if ($("#codigo_costos").val() == 0) throw "Indique el centro de costos"; 

            $("#esperar").fadeIn();

            $.post(RUTA+"transferencias/existencias", {cc:$("#codigo_costos").val(),codigo:'',descripcion:''},
                function (data, textStatus, jqXHR) {
                    $("#tablaModulos tbody")
                        .empty()
                        .append(data);
                        $("#busqueda").fadeIn();
                        $("#esperar").fadeOut();
                },
                "text"
            );
        } catch (error) {
            mostrarMensaje(error,"mensaje_error");
        }
        
        return false
    });

    $(".tituloVentana").on("click","a", function (e) {
        e.preventDefault();

        $(this).parent().parent().parent().parent().fadeOut();

        return false;
    });

    $("#tablaModulos tbody").on("click","tr", function (e) {
        e.preventDefault();

        let nFilas = $.strPad($("#tablaDetalles tr").length,3);
        let idprod = $(this).data("idprod");
        let nunid = $(this).data("ncomed");
        let aprobado = $(this).data("aprobado");
        let codigo = $(this).children('td:eq(1)').text();
        let descrip = $(this).children('td:eq(2)').text();
        let unidad = $(this).children('td:eq(3)').text();
        let saldo = $(this).children('td:eq(4)').text();
        let grabado = 0;

        let row = `<tr data-grabado="${grabado}" 
                        data-idprod="${idprod}" 
                        data-codund="${nunid}" 
                        data-idx="-">
                    <td class="textoCentro"><a href="#"><i class="fas fa-eraser"></i></a></td>
                    <td class="textoCentro"><a href="#"><i class="fas fa-exchange-alt"></i></a></td>
                    <td class="textoCentro">${nFilas}</td>
                    <td class="textoCentro">${codigo}</td>
                    <td class="pl20px">${descrip}</td>
                    <td class="textoCentro">${unidad}</td>
                    <td class="textoDerecha">${aprobado}</td>
                    <td><input type="number" step="any" placeholder="0.00" onchange="(function(el){el.value=parseFloat(el.value).toFixed(2);})(this)"></td>
                    <td class="textoDerecha">${saldo}</td>
                    <td><textarea></textarea></td>
                    <td></td>
                </tr>`;

        $("#tablaDetalles tbody").append(row);

        return false;
    });

    $("#guiaRemision").click(function (e) { 
        e.preventDefault();
        
        $("#vistadocumento").fadeIn();

        return false;
    });

    $("#tablaDetalles tbody").on('blur','input', function (e) {
            try {
                let ingreso = parseInt($(this).parent().parent().find("td").eq(7).children().val());
                let stock = parseInt($(this).parent().parent().find("td").eq(8).text());

                if(ingreso > stock) {
                    mostrarMensaje('La cantidad ingresada, es mayor al stock','mensaje_error')
                    return false;
                }

            } catch (error) {
                
            }
    });

    $("#tablaDetalles tbody").on('keypress','input', function (e) {
        if (e.which == 13) {
            $('#tablaDetalles tbody tr:last').find('td').eq(5).children().focus();
        }
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

            if  ( checkCantTables($("#tablaDetalles tbody > tr"),6) ) throw "Revise las cantidades ingresadas";

            
            $.post(RUTA+"transferencias/registro",{cabecera:result,
                                                    detalles:JSON.stringify(detalles()),
                                                    idpedido:pedido},
                function (data, textStatus, jqXHR) {
                    if(data.estado){
                        mostrarMensaje(data.mensaje,"mensaje_correcto");
                    }else{
                        mostrarMensaje(data.mensaje,"mensaje_error");
                    }
                },
                "json"
            );


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
            if ($("#codigo_costos").val() == 0) throw "Indique el centro de costos"; 

            $("#esperar").fadeIn();

            $.post(RUTA+"transferencias/pedidos", {cc:$("#codigo_costos").val(),pedido:""},
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
        $.post(RUTA+"transferencias/items", {indice:$(this).data('indice'),origen:$("#codigo_almacen_origen").val()},
            function (data, textStatus, jqXHR) {
                $("#tablaDetalles tbody")
                    .empty()
                    .append(data);
            },
            "text"
        );
    });

    $("#tablaDetalles tbody").on("click","a", function (e) {
        e.preventDefault();
        
        if ($(this).data("accion") == "delete") {
            $(this).parent().parent().remove
        }

        return false;
    });
})

detalles = () =>{
    DETALLES = [];

    let TABLA = $("#tablaDetalles tbody >tr");
    
    TABLA.each(function(){
        let ITEM            = $(this).find('td').eq(2).text(),
            IDPROD          = $(this).data("idprod"),
            GRABADO         = $(this).data("grabado"),
            COSTOS          = $(this).data("costos"),
            ORIGEN          = $("#codigo_almacen_origen").val(),
            CANTIDAD        = $(this).find('td').eq(8).children().val(),// cantidad
            OBSER           = $(this).find('td').eq(10).children().val(),
            CODIGO          = $(this).find('td').eq(3).text(),//codigo
            UNIDAD          = $(this).find('td').eq(5).text(),//unidad
            DESTINO         = $("#codigo_almacen_destino").val(),
            DESCRIPCION     = $(this).find('td').eq(4).text(),//unidad
            PEDIDO          = $(this).data("pedido"),
            IDITEM          = $(this).data("iditem"),
            APROBADO        = $(this).data("aprobado");
            COMPRADO        = $(this).find('td').eq(7).text()
    
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
                
            DETALLES.push(item);
        }     
    })

    return DETALLES; 
}
