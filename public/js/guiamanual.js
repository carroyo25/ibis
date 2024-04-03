$(function() {
    let accion = "",
        tipoVista = null,
        cc = "",
        fila = "",
        idfila = "",
        ordenes = [],
        sw=0,
        grabado = false;
        

    $("#esperar").fadeOut();

    $("#nuevoRegistro").click(function (e) { 
        e.preventDefault();

        $("#estado")
            .removeClass()
            .addClass("textoCentro estado w100por procesando");
        $("#tablaDetalles tbody").empty();
        $("#proceso").fadeIn();

        document.getElementById("formProceso").reset();
        document.getElementById("guiaremision").reset();

        $('input[type="hidden"]').val('');

        $("#tipo").val("SALIDA X OC");
        $("#codigo_movimiento").val(144);
        
        accion = 'n';
        cc = "";
        grabado = false;

        tipoVista = null;

        $(".primeraBarra").css("background","#0078D4");
        $(".primeraBarra span").text("Datos Generales");

        return false;
    });

    $("#closeProcess").click(function (e) { 
        e.preventDefault();

        $("#proceso").fadeOut();

        /*$.post(RUTA+"salida/actualizaDespachos",
            function (data, textStatus, jqXHR) {
                $(".itemsTabla table tbody")
                    .empty()
                    .append(data);

                $("#proceso").fadeOut();
                $("#codigo_costos").val("");

                document.getElementById("formProceso").reset();
                document.getElementById("guiaremision").reset();
               
            },
            "text"
        );*/

        return false;
    });
})