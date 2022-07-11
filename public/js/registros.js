$(function(){
    var accion = "";
    var index = "";

    $("#esperar").fadeOut();

    $("#tablaPrincipal tbody").on("click","tr", function (e) {
        e.preventDefault();

        $.post(RUTA+"registros/despachosID", {id:$(this).data("despacho")},
            function (data, textStatus, jqXHR) {
                
                /*let estado = "textoCentro w100por estado " + data.cabecera[0].cabrevia;*/
                
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
                $("#almacen_origen_despacho").val(data.cabecera[0].almacen);
                $("#almacen_destino_despacho").val(data.cabecera[0].destino);
                $("#fecha").val(data.cabecera[0].ffecdoc);
                $("#numero").val(data.cabecera[0].nnronota);
                $("#costos").val(data.cabecera[0].costos);
                $("#area").val(data.cabecera[0].area);
                $("#solicita").val(data.cabecera[0].nombres);
                $("#orden").val(data.cabecera[0].orden);
                /*$("#pedido").val(data.cabecera[0].pedido);
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
                    .append(data.detalles);*/
            },
            "json"
        );

        accion = "u";
        grabado = true;
        $("#proceso").fadeIn();

        return false;
    });
})