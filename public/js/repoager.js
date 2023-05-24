$(function(){
    $("#espera").fadeOut();

    var chart1,options;

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

    
    $("#tipo").on('change', function(e) {
        e.preventDefault();

        $('#tablaClases tbody').empty();
        $('#tablaClases tfoot').empty();

        let cantidad = 0,
            total = 0;

        $.ajax({
            url:"repoager/clases",
            type: "POST",
            dataType:"json",
            data:{clase:$(this).val(),grupo:$("#clase").val()},
            success:function(data){
                options.series[0].data = data;
                chart1 = new Highcharts.Chart(options);
                
                $.each(data, function (index, value) { 
                    $('#tablaClases tbody').append(`<tr data-grupo="${data[index]['grupo']}" data-clase="${data[index]['clase']}" data-familia="${data[index]['familia']}">
                                                    <td >${data[index]['name']}</td>
                                                    <td class="textoDerecha">${data[index]['y'].toFixed(2)}</td>
                                                    <td class="textoDerecha">${data[index]['total'].toFixed(2)}</td>
                                                </tr>`);

                    total = parseFloat(data[index]['total']) + parseFloat(total);
                    cantidad = parseInt(data[index]['y']) + parseInt(cantidad);
                });

                cantidad = cantidad.toFixed(2);
                total = total.toFixed(2);

                $("#tablaClases tfoot").append(`<tr>
                                                <td><strong>Total</strong></td>
                                                <td class="textoDerecha"><strong>${addComa(cantidad)}</strong></td>
                                                <td class="textoDerecha"><strong>${addComa(total)}</strong></td>
                                            </tr>`);
                
                $("#calculado span").text("S/. " + addComa(total));
            }
        });

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

        $('#tablaItems tbody').empty();
        $('#tablaItems tfoot').empty();

        let cantidad = 0,
            total = 0;

        $.post(RUTA+"repoager/items",{grupo:$(this).data('grupo'),clase:$(this).data('clase'),familia:$(this).data('familia')},
            function (data, text, requestXHR) {
                $.each(data.datos, function (index, value) { 
                    $("#tablaItems tbody").append(`<tr data-grupo="${data.datos[index]['grupo']}" 
                                                        data-clase="${data.datos[index]['clase']}" 
                                                        data-familia="${data.datos[index]['familia']}"
                                                        data-producto="${data.datos[index]['producto']}">
                                                        <td >${data.datos[index]['name']}</td>
                                                        <td class="textoDerecha">${data.datos[index]['y']}</td>
                                                        <td class="textoDerecha">${addComa(data.datos[index]['total'])}</td>
                                                    </tr>`)
                    
                    total = parseFloat(data.datos[index]['total']) + parseFloat(total);
                    cantidad = parseInt(data.datos[index]['y']) + parseInt(cantidad);
                });

                cantidad = cantidad.toFixed(2);
                total = total.toFixed(2);

                $("#tablaItems tfoot").append(`<tr>
                                                <td><strong>Total</strong></td>
                                                <td class="textoDerecha"><strong>${addComa(cantidad)}</strong></td>
                                                <td class="textoDerecha"><strong>${addComa(total)}</strong></td>
                                            </tr>`);
                
                $("#calculado span").text("S/. " + addComa(total));
            },
            "json"
        );


        return false;
    });

    $("#tablaItems tbody").on("click",'tr', function (e) {
        e.preventDefault();

        $.post(RUTA+"repoager/graficoLineas",{grupo:$(this).data('grupo'),
                                            clase:$(this).data('clase'),
                                            familia:$(this).data('familia'),
                                            producto:$(this).data('producto')},
            function (data, textStatus, jqXHR) {
                console.log(data);
            },
            "json"
        );

        return false;
    });
})



lineas = () => {
    Highcharts.chart('lineas', {

       cchart: {
        type: 'spline'
    },
    title: {
        text: 'Monthly Average Temperature'
    },
    subtitle: {
        text: 'Source: ' +
            '<a href="https://en.wikipedia.org/wiki/List_of_cities_by_average_temperature" ' +
            'target="_blank">Wikipedia.com</a>'
    },
    xAxis: {
        categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun','Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        accessibility: {
            description: 'Months of the year'
        }
    },
    yAxis: {
        title: {
            text: 'Temperature'
        },
        labels: {
            formatter: function () {
                return this.value + '°';
            }
        }
    },
    tooltip: {
        crosshairs: true,
        shared: true
    },
    plotOptions: {
        spline: {
            marker: {
                radius: 4,
                lineColor: '#666666',
                lineWidth: 1
            }
        }
    },
    series: [{
        name: 'PROTECTOR AUDITIVO TIPO TAPON',
        marker: {
            symbol: 'square'
        },
        data: [300,0,500,0,250,0,0,0,0,0,0,0]}, ]
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
        title: {
            text: 'Totales por meses',
        },
        series: [{
            data: [29.9, 71.5, 106.4, 129.2, 144.0, 176.0, 135.6, 148.5, 216.4, 194.1, 95.6, 54.4]
        }]
    });
}

