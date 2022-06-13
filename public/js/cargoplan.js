$(function(){

    $("#esperar").fadeOut();


    $("#tablaPrincipal tbody").on("click","tr", function (e) {
        e.preventDefault();

        $("#vistadocumento").fadeIn();

        return false;
    });
})