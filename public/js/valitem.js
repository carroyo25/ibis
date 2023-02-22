$(() => {
    $("#btnConsulta").click(function(e){

        let str = $("#formConsulta").serialize();

        $.post(RUTA+"valitem/consulta",str,
            function (data, textStatus, jqXHR) {
                $("#tablaPrincipal tbody")
                    .empty()
                    .append(data);
            },
            "text"
        );
    });
})