$(function() {
    $("#esperar").fadeOut();

    $("#btnProcesa").click(function(e){
        e.preventDefault();

        let str = $("#formConsulta").serialize();

        $("#esperar").css({"display":"block","opacity":"1"});

        $.post(RUTA+"cargoplanlogistic/filtroCargoPlanLogistica",str,
            function (data, text, requestXHR) {
                $(".itemsCargoPlanner table tbody")
                    .empty()
                    .append(data);

                    $("#esperar").fadeOut().promise().done(function(){
                        iniciarPaginador();
                    });

            "text"
        });

        return false;
    });

    $("#excelJS").click(function(e){
        e.preventDefault();

        $("#esperarCargo").css("opacity","1").fadeIn();

        let str = $("#formConsulta").serializeArray();
        
        $("#esperar").css({"display":"block","opacity":"1"}); ///para mostrar el modal de espera

        $.post(RUTA+"cargoplanlogistic/exceljs",str,
            function (data, text, requestXHR) {
                $(".itemsCargoPlanner table tbody")
                    .empty()
                    .append(data);

                    $("#esperar").fadeOut().promise().done(function(){
                        iniciarPaginador();
                    });

            "text"
        });

        return false;
    });
})