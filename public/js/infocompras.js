$(function(){
    $("#espera").fadeOut();

    let valores = [10,0,367,0,0,155,0,1,0,0];

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