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
                $("#codigo_almacen").val(data.cabecera[0].origen);
                $("#codigo_almacen_destino").val(data.cabecera[0].destino);
                $("#codigo_pedido").val(data.cabecera[0].codigo_pedido);
                $("#codigo_orden").val(data.cabecera[0].codigo_orden);
                $("#codigo_estado").val(data.cabecera[0].nEstadoDoc);
                $("#codigo_entidad").val(data.cabecera[0].id_centi);
                $("#codigo_ingreso").val(data.cabecera[0].idref_abas);
                $("#codigo_salida").val(data.cabecera[0].id_regalm);
                $("#almacen_origen_despacho").val(data.cabecera[0].origen);
                $("#almacen_destino_despacho").val(data.cabecera[0].destino);
                $("#numero").val(data.cabecera[0].guia);
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
                
                /*$("#estado")
                    .removeClass()
                    .addClass(estado);*/
                
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

        /*$.post(RUTA+"registros/actualizaRegistros",
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
        );*/

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
})