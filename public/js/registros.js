$(function(){
    var accion = "";
    var index = "";

    $("#esperar").fadeOut();

    $("#tablaPrincipal tbody").on("click","tr", function (e) {
        e.preventDefault();

        $.post(RUTA+"registros/despachosID", {id:$(this).data("despacho")},
            function (data, textStatus, jqXHR) {
                $("#codigo_costos").val(data.cabecera[0].codigo_costos);
                $("#codigo_area").val(data.cabecera[0].codigo_area);
                $("#codigo_almacen").val(data.cabecera[0].codigo_origen);
                $("#codigo_almacen_destino").val(data.cabecera[0].codigo_destino);
                $("#codigo_pedido").val(data.cabecera[0].codigo_pedido);
                $("#codigo_orden").val(data.cabecera[0].codigo_orden);
                $("#codigo_estado").val(data.cabecera[0].nEstadoDoc);
                $("#codigo_entidad").val(data.cabecera[0].id_centi);
                $("#codigo_ingreso").val(data.cabecera[0].idref_abas);
                $("#codigo_salida").val(data.cabecera[0].id_despacho);
                $("#almacen_origen_despacho").val(data.cabecera[0].origen);
                $("#almacen_destino_despacho").val(data.cabecera[0].destino);
                $("#numero").val(data.numero);
                $("#costos").val(data.cabecera[0].costos);
                $("#area").val(data.cabecera[0].area);
                $("#solicita").val(data.cabecera[0].solicita);
                $("#orden").val(data.cabecera[0].orden);
                $("#pedido").val(data.cabecera[0].pedido);
                $("#guia").val(data.cabecera[0].guia);
                $("#concepto").val(data.cabecera[0].concepto);
                $("#fecha_pedido").val(data.cabecera[0].emision);
                $("#fecha_orden").val(data.cabecera[0].fecha_orden);
                $("#bultos").val(data.cabecera[0].nbultos);
                $("#peso").val(data.cabecera[0].npesotot);
                
                $("#tablaDetalles tbody")
                    .empty()
                    .append(data.detalles);
            },
            "json"
        );

        accion = "u";
        grabado = true;
        $("#proceso").fadeIn();

        return false;
    });

    $("#closeProcess").click(function (e) { 
        e.preventDefault();

        $("#proceso").fadeOut()

        $.post(RUTA+"registros/actualizarDespachos",
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

    $(".mostrarLista").focus(function (e) { 
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

        if(contenedor_padre == "listaRecepciona"){
            $("#codigo_recepciona").val(codigo);
        }

        return false;
    });

    $("#tablaDetalles tbody").on('keypress','input', function (e) {
        if (e.which == 13) {
            let cant = parseFloat($(this).parent().parent().find("td").eq(4).text()) - $(this).parent().parent().find("td").eq(5).children().val();
            
            try {
                if (cant < 0) throw "Error en el ingreso";

            } catch (error) {
                mostrarMensaje(error,"mensaje_error");
            }
        }
    });

    
    $("#updateDocument").click(function(e){
        e.preventDefault();

        let result = {};

        $.each($("#formProceso").serializeArray(),function(){
            result[this.name] = this.value;
        });

        try {
            if (result['codigo_recepciona'] == '') throw "Elija el responsable de la recepcion";
            

            $.post(RUTA+"registros/ingresoAlmacen",{detalles:JSON.stringify(detalles()),
                almacen:$('#codigo_almacen_destino').val(),
                pedido:$('#codigo_pedido').val(),
                orden:$('#codigo_orden').val(),
                recepciona:$('#codigo_recepciona').val(),
                salida:$('#codigo_salida').val(),
                cabecera:result},
                function (data, textStatus, jqXHR) {
                    if (data) {
                        mostrarMensaje("Items añadidos","mensaje_correcto");
                    }else{
                        mostrarMensaje("no se actualizo correctamente","mensaje_error");
                    }
                },
                    "text"
                );
        } catch (error) {
            mostrarMensaje(error,'mensaje_error');
        }

        return false;
    });

})

detalles = () =>{
    DETALLES = [];

    let TABLA = $("#tablaDetalles tbody tr");

    TABLA.each(function(){
        let ITEMPEDIDO      = $(this).data("itempedido"),
            ITEMDESPACHO    = $(this).data("itemdespacho"),
            IDPRODUCTO      = $(this).data("idproducto"),
            CANTIDAD        = $(this).find('td').eq(4).text(),
            INGRESO         = $(this).find('td').eq(5).children().val(),
            OBSERVACIONES   = $(this).find('td').eq(6).children().val(),
            SERIES          = $(this).find('td').eq(7).text(),
            VENCIMIENTO     = $(this).find('td').eq(8).text(),
            UBICACION       = $(this).find('td').eq(9).children().val(),
            RECEPCIONA      = $("#codigo_recepciona").val();
            //ojo grabar el estado del material

        item = {};

        if ( CANTIDAD > 0 ) {
            item['itempedido']      = ITEMPEDIDO;
            item['itemdespacho']    = ITEMDESPACHO;
            item['idproducto']      = IDPRODUCTO;
            item['cantidad']        = CANTIDAD;
            item['observaciones']   = OBSERVACIONES;
            item['series']          = SERIES;
            item['vencimiento']     = VENCIMIENTO;
            item['ubicacion']       = UBICACION;
            item['recepciona']      = RECEPCIONA;
            item['ingreso']         = INGRESO;
        }

        DETALLES.push(item);
    })

    return DETALLES
}
