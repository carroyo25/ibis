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
})