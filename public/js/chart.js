
$(function () {
    $("#esperar").fadeOut();

    if ( $("#rol_user").val() == 2) {
        $.ajax({
            type: "POST",
            url: RUTA+"panel/pedidos",
            dataType: "JSON",
            success: function (response) {
                $("#tablaPanel tbody")
                  .empty()
                  .append(response.contenido);
                  $("#pedidos_emitidos").text(response.valores[0]);

                    let xValues = response.etiquetas;
                    let yValues = response.valores;
                    let barColors = [
                    "#b91d47",
                    "#00aba9",
                    "#2b5797",
                    "#e8c3b9",
                    "#1e7145"
                    ];

                    new Chart("myChart", {
                    type: "pie",
                    data: {
                        labels: xValues,
                        datasets: [{
                        backgroundColor: barColors,
                        data: yValues
                        }]
                    },
                    options: {
                            title: {
                            display: false,
                            text: "World Wide Wine Production 2018"
                            }
                        }
                    });
            }
        });

    }else if ( $("#rol_user").val() == 68 ) {
        $.post(RUTA+"panel/ordenes",
            function (data, textStatus, jqXHR) {
                console.log(data.valores);           
            },
            "json"
        );
    }
});