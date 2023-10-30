$(function(){
    var accion = "";
    var grabado = false;
    var aprobacion = 0;

    $("#esperar").fadeOut();

    $("#btnConsulta").click(function(e){
        e.preventDefault();

        $.post(RUTA+"detallecs/consulta", {cc:$("#costosSearch").val(),codigo:$("#codigoBusqueda").val(),descripcion:$("#descripcionSearch").val()},
            function (data, text, requestXHR) {
                console.log(data);
                $("#tablaPrincipal tbody").empty().append(data);
            },
            "text"
        );

        return false;
    });
})