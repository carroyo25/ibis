$(function(){
    $("#espera").fadeOut();

    let valores = [1,2,3,4,5,6,7,8,9,10];

    indicadores(valores);
})

indicadores = (valores) => {
    Highcharts.chart('graficoIndicadores', {
        chart: {
            type: 'column'
        },
        xAxis: {
            categories: ['Anulado', 'Comprado', 'Almacen', 'Cotizacion', 'Obra', 'Transito', 'Stock', 'Firma', 'Creado', 'Total']
        },
        plotOptions: {
            series: {
                pointWidth: 20
            }
        },
        title: {
            text: 'Items Procesados'
        },
        series: [
            {
                name: 'Items',
                data: valores
            }
        ]
    });
}