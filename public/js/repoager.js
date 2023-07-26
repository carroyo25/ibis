$(function(){
    var chart1,options;

    $("#espera").fadeOut();

    clases(0,2023,7);
    tortaClases();
})

clases = (codigo_cc,ac,cm) => {
   $.ajax({
    type: "POST",
    url: "repoager/consultaClases",
    data: {cc:codigo_cc,anio:ac,mes:cm},
    dataType: "json",
    success: function (data) {
        options.series[0].data = data.clase;
        chart1 = new Highcharts.Chart(options);   
    }
   });
}

function tortaClases(){
    options = {
        chart:{
            renderTo: 'torta',
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
            },
            title: {
                text: 'Grafico de Porcentajes (Clases)',
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            accessibility: {
                point: {
                    valueSuffix: '%'
                }
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                    }
                }
            },
            series: [{
                name: 'brands',
                colorByPoint: true,
                data: []
            }]        
    }
}

lineas = (valores,producto) => {
    Highcharts.chart('lineas', {
       chart: {
        type: 'line'
    },
    title: {
        text: 'Cantidad de Items por Meses'
    },
    subtitle: {
        text: 'Source: ' +
            '<a href="sical.sepcon.net" ' +
            'target="_blank">sical.sepcon.net</a>'
    },
    xAxis: {
        categories: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun','Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
        accessibility: {
            description: 'Meses'
        }
    },
    yAxis: {
        title: {
            text: 'Cantidad'
        },
        labels: {
            formatter: function () {
                return this.value;
            }
        }
    },
    tooltip: {
        crosshairs: true,
        shared: true
    },
    plotOptions: {
        line: {
            dataLabels: {
                enabled: true
            },
            enableMouseTracking: true
        }
    },
    series: [{
        name: producto,
        marker: {
            symbol: 'square'
        },
        data: valores} ]
    });
}

barras = (valores) => {
    Highcharts.chart('barras', {
        chart: {
            type: 'column'
        },
        xAxis: {
            categories: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic']
        },
        plotOptions: {
            series: {
                pointWidth: 20
            }
        },
        title: {
            text: 'Total Meses',
        },
        series: [{
            name: "",
            data: valores
        }]
    });
}

torta = (valores) => {
    Highcharts.chart('torta', {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        title: {
            text: 'Porcentajes (Clases)',
            align: 'center'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        accessibility: {
            point: {
                valueSuffix: '%'
            }
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                }
            }
        },
        series: [{
            name: 'Brands',
            colorByPoint: true,
            data: valores
        }]
    });
    
}

torta1 = (valores) => {
    Highcharts.chart('torta1', {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        title: {
            text: 'Porcentajes (Familias)',
            align: 'center'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        accessibility: {
            point: {
                valueSuffix: '%'
            }
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                }
            }
        },
        series: [{
            name: 'Brands',
            colorByPoint: true,
            data: valores
        }]
    });
    
}



