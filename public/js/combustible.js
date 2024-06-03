$(() => {
    let accion = "u",flag=false;

    $("#esperar").fadeOut();

    $("#nuevoRegistro").click(function (e) { 
        e.preventDefault();
        
        accion = "n";
        $("#dialogo_registro").fadeIn();

    });

    $("#btn_consumo_aceptar").click(function (e) { 
        e.preventDefault();

        $("#dialogo_registro").fadeOut();

        return false;
    });

    $("#btn_consumo_cancelar").click(function (e) { 
        e.preventDefault();

        $("#dialogo_registro").fadeOut();

        return false;
    });
})