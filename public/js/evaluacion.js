$(function(){
    $("#esperar").fadeOut();

    $("#tablaPrincipal tbody").on("click","tr", function (e) {
        e.preventDefault();

        //rol:$("#rol_user").val(),tipo:$("tipo_orden").val()
        $.post(RUTA+"evaluacion/criterios", {rol:4,tipo:$("#tipo_orden").val()},
            function (data, textStatus, jqXHR)
             {
                $("#tablaDetalles tbody")
                    .empty()
                    .append(data);

                $("#proceso").fadeIn();
             },
             "text"
        );
    
        return false;
    });

    $("#cerrarVentana").click(function (e) { 
        e.preventDefault();

        /*$.post(RUTA+"proyecto/actualizaTabla",
            function (data, textStatus, jqXHR) {
                $("#tablaPrincipal tbody")
                    .empty()
                    .append(data);
                $("#proceso").fadeOut();
                $("form")[0].reset();
            },
            "text"
        );*/
        
        $("#proceso").fadeOut();
        
        return false;
    });
})