$(function(){
    $("#espera").fadeOut();

    let pd = [{
        name: 'Combustible',
        y: 16,
        sliced: true,
        selected: true}];

    //torta(pd);
    lineas();
    barras();

    $("#clase").on('change', function(e) {
        e.preventDefault();
        
        $.post(RUTA+"repoager/tipos",{id:$(this).val()},
            function (data, text, requestXHR) {
                $("#tipo")
                    .empty()
                    .append(data);
            },
            "text"
        );

        return false;
    });

    var chart1,options;
    $("#tipo").on('change', function(e) {
        e.preventDefault();

        $.ajax({
            url:"repoager/clases",
            type: "POST",
            dataType:"json",
            data:{clase:$(this).val(),grupo:$("#clase").val()},
            success:function(data){
                options.series[0].data = data;
                chart1 = new Highcharts.Chart(options);
                console.log(data);
            }
        })    
        torta();

        return false;
    });

    function torta(){
        options = {
            chart:{
                renderTo: 'torta',
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
                },
                title: {
                    text: 'Porcentaje de compras por familia',
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

    $("#tablaClases").on('click','tbody tr', function(e) {
        e.preventDefault();

        $.post(RUTA+"repoager/items",{grupo:$(this).data('grupo'),clase:$(this).data('clase'),familia:$(this).data('familia')},
            function (data, text, requestXHR) {
                console.log(data);
            },
            "json"
        );


        return false;
    });
})



lineas = () => {
    Highcharts.chart('lineas', {
        chart: {
            type: 'area'
        },
        xAxis: {
            categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
        },
    
        plotOptions: {
            series: {
                fillOpacity: 0.1
            }
        },
    
        series: [{
            data: [29.9, 71.5, 106.4, 129.2, 144.0, 176.0, 135.6, 148.5, 216.4, 194.1, 95.6, 54.4]
        }]
    });
}

barras = () => {
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
    
        series: [{
            data: [29.9, 71.5, 106.4, 129.2, 144.0, 176.0, 135.6, 148.5, 216.4, 194.1, 95.6, 54.4]
        }]
    });
}