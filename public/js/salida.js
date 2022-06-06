$(function() {
    let accion = "",
        grabado = false,
        indice_nota=0;

    $("#esperar").fadeOut();

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

        $("#proceso").fadeOut();

        /*$.post(RUTA+"recepcion/actualizaNotas",
            function (data, textStatus, jqXHR) {
                $(".itemsTabla table tbody")
                    .empty()
                    .append(data);

                $("#proceso").fadeOut(function(){
                    grabado = false;
                    $("form")[0].reset();
                    $("form")[1].reset();
                    $("#tablaDetalles tbody,.listaArchivos").empty();
                });
            },
            "text"
        );*/

        return false;
    });

    $("#importData").click(function (e) { 
        e.preventDefault();
        
        $.post(RUTA+"salida/ingresos",
            function (data, textStatus, jqXHR) {
                $("#notas tbody")
                    .empty()
                    .append(data);

                $("#busqueda").fadeIn();
            },
            "text"
        );

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
                $("#codigo_aprueba").val(data.cabecera[0].aprueba);
                $("#codigo_almacen").val(data.cabecera[0].ncodalm1);
                $("#codigo_pedido").val(data.cabecera[0].idref_pedi);
                $("#codigo_orden").val(data.cabecera[0].idref_abas);
                $("#codigo_estado").val(data.cabecera[0].nEstadoDoc);
                $("#costos").val(data.cabecera[0].proyecto);
                $("#area").val(data.cabecera[0].area);
                $("#solicita").val(data.cabecera[0].solicita);
                $("#aprueba").val(data.cabecera[0].cnombres);
                $("#almacen").val(data.cabecera[0].almacen);
                $("#pedido").val(data.cabecera[0].pedido);
                $("#fecha_pedido").val(data.cabecera[0].emision);
                $("#orden").val(data.cabecera[0].orden);
                $("#fecha_orden").val(data.cabecera[0].ffechadoc);
                $("#concepto").val(data.cabecera[0].concepto);
                $("#estado").val(data.cabecera[0].cdescripcion);
                $("#guia").val(data.cabecera[0].cnumguia);
                $("#numero").val(data.numero.numero);

                $("#tablaDetalles tbody")
                    .empty()
                    .append(data.detalles)

                $("#busqueda").fadeOut();
            },
            "json"
        );

        return false;
    });
})