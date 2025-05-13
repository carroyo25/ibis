var gaugeOptions = {

    chart: {
        type: 'solidgauge'
    },
    title: null,

    pane: {
        center: ['50%', '55%'],
        size: '80%',
        startAngle: -90,
        endAngle: 90,
        background: {
            backgroundColor:
                Highcharts.defaultOptions.legend.backgroundColor || '#EEE',
            innerRadius: '60%',
            outerRadius: '100%',
            shape: 'arc'
        }
    },

    exporting: {
        enabled: false
    },

    tooltip: {
        enabled: false
    },

    // the value axis
    yAxis: {
        stops: [
            [0.0, '#55BF3B'], // green
        ],
        lineWidth: 2,
        tickWidth: 2,
        minorTickInterval: null,
        tickAmount: 0,
        title: {
            y: 0
        },
        labels: {
            y: 16
        }
    },

    plotOptions: {
        solidgauge: {
            dataLabels: {
                y: 5,
                borderWidth: 0,
                useHTML: true
            }
        }
    }
};

// The speed gauge
var chartSpeed = Highcharts.chart('container-speed', Highcharts.merge(gaugeOptions, {
    yAxis: {
        min: 0,
        max: 100,
        title: {
            text: 'Avance'
        }
    },

    pane:{
        size: '80%',
    },

    credits: {
        enabled: false
    },

    series: [{
        name: 'Speed',
        data: [0],
        dataLabels: {
            format:
                '<div style="text-align:center">' +
                '<span style="font-size:25px">{y}</span><br/>' +
                '<span style="font-size:12px;opacity:0.4">%</span>' +
                '</div>'
        },
        tooltip: {
            valueSuffix: ' km/h'
        }
    }]

}));