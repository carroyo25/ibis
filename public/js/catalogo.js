$(function(){
    $("#descripcion").on("keypress", function (e) {
        if(e.which == 13 && $(this).val().length > 1) {
            $("#waitmodal").fadeIn();
            $.post(RUTA+"catalogo/buscaPalabra", {criterio:$(this).val()},
                function (data, textStatus, jqXHR) {
                    $("#tablaPrincipal tbody")
                        .empty()
                        .append(data);
                    //$("#waitmodal").fadeOut();  
                },
                "text"
            );
        }
    });

    $("#codigo").on("keypress", function (e) {
        if(e.which == 13 && $(this).val().length > 1) {
            $("#waitmodal").fadeIn();
            $.post(RUTA+"catalogo/buscaCodigo", {criterio:$(this).val()},
                function (data, textStatus, jqXHR) {
                    $("#tablaPrincipal tbody")
                        .empty()
                        .append(data);
                    //$("#waitmodal").fadeOut();  
                },
                "text"
            );
        }
    });
   
    $("#excelFile").on('click', function(e) {
        e.preventDefault();

        $.post(RUTA+"catalogo/catalogoXls",
            function (data, text, requestXHR) {
                console.log(data);
            },
            "text"
        );

        return false;
    });
})
