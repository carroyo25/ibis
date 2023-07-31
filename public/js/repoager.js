$(function(){
    $("#espera").fadeOut();

    let valores = [0,0,0,0,0,0,0,0,0,0,0,0];

    //entrada
    barras(valores);

    qrygrupos(0,2023,7);
    qryclases(0,0,2023,7)
    qryfamilias(0,0,0,2023,7);

    //sucesos
    $("#costos").on('change', function(e) {
        e.preventDefault();

        let costos = $(this).val(),
            anio = $("#anio").val(),
            mes = $("#mes").val();

            qrygrupos(costos,anio,mes);
            qryclases(costos,0,anio,mes);
            qryfamilias(costos,0,0,anio,mes);

        return false;
    });

    $("#grupo").change(function (e) { 
        e.preventDefault();

        let costos = $("#costos").val(),
            grupo = $(this).val(),
            clase = $("#clase").val(),
            anio = $("#anio").val(),
            mes = $("#mes").val();

            qryclases(costos,grupo,anio,mes);
            qryfamilias(costos,grupo,clase,anio,mes);

        return false;
    });

    $("#clase").change(function (e) { 
        e.preventDefault();

        let costos = $("#costos").val(),
            grupo = $("#grupo").val(),
            clase = $(this).val(),
            anio = $("#anio").val(),
            mes = $("#mes").val();

            qryfamilias(costos,grupo,clase,anio,mes);

        return false;
    });

    $("#tablaClases").on("click","tr", function (e) {
        e.preventDefault();

        let mes = $("#mes").val(),
            anio = $("#anio").val(),
            costo = $("#costos").val(),
            grupo = $("#grupo").val(),
            clase = $("#clase").val(),
            familia = $(this).data("familia");

            $.post("repoager/consultaGrupos", {cc:costo,fam:familia,an:anio,mm:mes},
                function (data, textStatus, jqXHR) {
                    
                },
                "json"
            );

        return false;
    });

})

qrygrupos = (codigo_cc,ac,cm) => {

    let option = `<option value="0">Todos</option>`,
        body = "";

    $.ajax({
        type: "POST",
        url: "repoager/consultaGrupos",
        data: {cc:codigo_cc,anio:ac,mes:cm},
        dataType: "json",
        success: function (data) {
            //options.series[0].data = data.clase;
            //chart1 = new Highcharts.Chart(options); 
            grupo(data.grupo);

            $.each(data.grupo, function(i, item) {
                option += `<option value="${item.cg}">${item.name}</option>`;
            });

            $("#grupo").empty().append(option);
            
        }
    });
}

qryclases = (codigo_cc,codigo_gr,ac,cm) => {
    let option = `<option value="0">Todos</option>`,
        celdas = '';

    $.ajax({
        type: "POST",
        url: "repoager/consultaClases",
        data: {cc:codigo_cc,gr:codigo_gr,anio:ac,mes:cm},
        dataType: "json",
        success: function (data) {
            clases(data.clase);

            $.each(data.clase, function(i, item) {
                option += `<option value="${item.cc}">${item.name}</option>`;

                let c = parseFloat(item.conteo).toFixed(2),
                    t = parseFloat(item.total).toFixed(2);

                celdas += `<tr data-familia="${item.cf}">
                            <td>${item.name}</td>
                            <td class="textoDerecha">${c}</td>
                            <td class="textoDerecha">${t}</td>
                        </tr>`;
            });

            $("#clase").empty().append(option);
            $("#tablaClases tbody").empty().append(celdas);    
        }
    });
}

qryfamilias = (codigo_cc,codigo_gr,codigo_cl,ac,cm) => {
    
    let option = `<option value="0">Todos</option>`;

    $.ajax({
        type: "POST",
        url: "repoager/consultaFamilias",
        data: {cc:codigo_cc,gr:codigo_gr,cl:codigo_cl,anio:ac,mes:cm},
        dataType: "json",
        success: function (data) {
            familias(data.familias);

            $.each(data.familias, function(i, item) {
                option += `<option value="${item.cf}">${item.name}</option>`;
            });

            $("#familia").empty().append(option);
        }
       });
}

function tortaClases(){
    options = {
        chart:{
            renderTo: 'tortaClase',
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
                name: 'clase',
                colorByPoint: true,
                data: []
            }]        
    }
}

function tortaFamilias(){
    options = {
        chart:{
            renderTo: 'tortaFamilia',
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
                name: 'Familias',
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

grupo = (valores) => {
    Highcharts.chart('grupos', {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        title: {
            text: 'Porcentajes (Grupos)',
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

clases = (valores) => {
    Highcharts.chart('clases', {
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

familias = (valores) => {
    Highcharts.chart('familias', {
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



