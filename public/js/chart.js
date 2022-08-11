
$(function () {
    $("#esperar").fadeOut();

    //esta parte controla la parte de informes del sistema

    if ( $("#rol_user").val() == 9 || $("#rol_user").val() == 2) {
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
    }else if ( $("#rol_user").val() == 5 ) {
        $.ajax({
            type: "POST",
            url: RUTA+"panel/ordenes",
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
    }else if ( $("#rol_user").val() == 3 ) {
        $.ajax({
            type: "POST",
            url: RUTA+"panel/pedidosxAprobar",
            dataType: "JSON",
            success: function (response) {
                $("#tablaPanel tbody")
                  .empty()
                  .append(response.contenido);
                
                $("#pedidos_emitidos").text(response.valores[0]);
                $("#pedidos_pendientes").text(response.valores[3]);

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
        $.ajax({
            type: "POST",
            url: RUTA+"panel/compras",
            dataType: "JSON",
            success: function (response) {
                $("#tablaPanelPedidos tbody")
                  .empty()
                  .append(response.aprobados);

                $("#tablaPanelOrdenes tbody")
                  .empty()
                  .append(response.ordenes);
            }
        });
    }else if ( $("#rol_user").val() == 4 ) {
        $.ajax({
            type: "POST",
            url: RUTA+"panel/resumenAlmacenSedes",
            dataType: "JSON",
            success: function (response) {
                $("#tablaPanelOrdenes tbody")
                .empty()
                .append(response.ordenes);

                $("#tablaPanelIngresos tbody")
                  .empty()
                  .append(response.aprobados);
            }
        });
    }
});