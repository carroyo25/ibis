$(function(){

    $("#esperar").fadeOut();


    $("#tablaPrincipal tbody").on("click","tr", function (e) {
        e.preventDefault();

        $.post(RUTA+"cargoplan/consultaItem", { codigo:$(this).data("producto"),
                                                pedido:$(this).data("pedido"),
                                                orden:$(this).data("orden"),
                                                ingreso:$(this).data("ingreso"),
                                                despacho:$(this).data("despacho"),
                                                item:$(this).data("item")},
            function (data, textStatus, jqXHR) {

                let estado = "textoCentro "+ data.producto[0].estado;

                $("#codigo").val(data.producto[0].ccodprod);
                $("#producto").val(data.producto[0].producto);
                $("#unidad").val(data.producto[0].cabrevia);
                $("#cantidad").val(data.producto[0].cantidad);
                $("#estado").val(data.producto[0].cdescripcion);
                $("#nropedido").val();
                $("#tipo_pedido").val();
                $("#emision_pedido").val();
                $("#aprobacion_pedido").val();
                $("#aprobado_por").val();
                $("#nroorden").val();
                $("#emision_orden").val();
                $("#aprobacion_logistica").val();
                $("#aprobacion_operaciones").val();
                $("#aprobacion_finanzas").val();
                $("#ingreso").val();
                $("#fecha_ingreso").val();
                $("#ingresada").val();
                $("#despacho").val();
                $("#fecha_salida").val();
                $("#enviada").val();

                $("#estado")
                    .removeClass()
                    .addClass(estado);
                    
                $("#vistadocumento").fadeIn();

                

            },
            "json"
        );
       
        return false;
    });

    $(".tituloDocumento").on("click","a", function (e) {
        e.preventDefault();

        $(this).parent().parent().parent().parent().parent().fadeOut();

        return false;
    });
})