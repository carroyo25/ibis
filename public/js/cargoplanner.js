$(function() {
    $("#btnProcesa").click(function(e){
        e.preventDefault();

        let str = $("#formConsulta").serialize();

        $.post(RUTA+"cargoplanner/filtroCargoPlan",str,
            function (data, text, requestXHR) {
                $(".itemsCargoPlanner table tbody")
                    .empty()
                    .append(data);

            "text"
        });
        return false;
    });
})