
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
                
                $("#pedidos_emitidos").text(validarSeries(response.series[0].y)); //emitidos
                $("#pedidos_aprobados").text(validarSeries(response.series[4].y)); //aprobados
                $("#pedidos_culminados").text(validarSeries(response.series[10].y)); //entregados

                let valoresPedidos =  [response.series[0],
                                       response.series[1],
                                       response.series[2],
                                       response.series[3],
                                       response.series[4],
                                       response.series[5],
                                       response.series[6],
                                       response.series[7],
                                       response.series[8],
                                       response.series[9],
                                       response.series[10]];

                Highcharts.chart('containerPie', {
                    chart: {
                      type: 'pie',
                      options3d: {
                        enabled: true,
                        alpha: 45,
                        beta: 0
                      }
                    },
                    title: {
                      text: 'N° Pedidos'
                    },
                    subtitle: {
                      text: 'Fuente: Base de datos Sical'
                    },
                    accessibility: {
                      point: {
                        valueSuffix: '%'
                      }
                    },
                    tooltip: {
                      pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                    },
                    plotOptions: {
                      pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        depth: 35,
                        dataLabels: {
                          enabled: true,
                          format: '{point.name}'
                        }
                      }
                    },
                    series: [{
                      type: 'pie',
                      name: 'Share',
                      data: valoresPedidos
                    }]
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
                
                //$("#pedidos_emitidos").text(response.valores[0]);

                
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
                
                $("#pedidos_pendientes").text(validarSeries(response.series[0].y));
                $("#pedidos_aprobados").text(validarSeries(response.series[1].y)); 

                let valoresPedidos =  [response.series[0],
                                       response.series[1]];

                                       Highcharts.chart('containerPie', {
                                        chart: {
                                          type: 'pie',
                                          options3d: {
                                            enabled: true,
                                            alpha: 45,
                                            beta: 0
                                          }
                                        },
                                        title: {
                                          text: 'Porcentaje Pedidos'
                                        },
                                        subtitle: {
                                          text: 'Fuente: Base de datos Sical'
                                        },
                                        accessibility: {
                                          point: {
                                            valueSuffix: '%'
                                          }
                                        },
                                        tooltip: {
                                          pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                                        },
                                        plotOptions: {
                                          pie: {
                                            allowPointSelect: true,
                                            cursor: 'pointer',
                                            depth: 20,
                                            dataLabels: {
                                              enabled: true,
                                              format: '{point.name}'
                                            }
                                          }
                                        },
                                        series: [{
                                          type: 'pie',
                                          name: 'Share',
                                          data: valoresPedidos
                                        }]
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
                  .append(response.ingresos);
            }
        });
    }
});

const validarSeries = (valor) => {
    let ret = valor == "" ? 0 : valor;
    return ret;
}