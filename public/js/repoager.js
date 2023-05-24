$(function(){
    $("#espera").fadeOut();

    var chart1,options;
    var valores = [300,0,500,0,250,0,0,0,0,0,0,0];

    let pd = [{
        name: 'Combustible',
        y: 16,
        sliced: true,
        selected: true}];

    //torta(pd);
    lineas(valores);
    barras(valores);

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

        producto = $(this).find('td').eq(0).text()

        $.post(RUTA+"repoager/graficoLineas",{grupo:$(this).data('grupo'),
                                            clase:$(this).data('clase'),
                                            familia:$(this).data('familia'),
                                            producto:$(this).data('producto')},
            function (data, textStatus, jqXHR) {
                lineas(data.lineas,producto);
                barras(data.barras,producto);
            },
            "json"
        );

        return false;
    });
})

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

