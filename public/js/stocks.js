$(() => {
    $("#esperar").fadeOut();

    $("#nuevoRegistro").click(function (e) { 
        e.preventDefault();
        
        $("#proceso").fadeIn();

        return false;
    });
})