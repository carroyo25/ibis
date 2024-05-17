$(function() {
    let campo = "",
        ffemision = [],
        cCostos = [],
        cEntidad = [];

    let valores =  [ 1, 2, 3, 4, 5, 5, 7, 8, 8, 10, 11, 12 ];
    
    $("#espera").fadeOut();


    $(".contenedorfiltro *").click(function(e){
        e.preventDefault();

        let control = $(this);
        
        campo = $(this).parent().parent().data("campo");
        
        $(".filter_options").fadeOut();

        llamarFiltro(control,campo);

        return false;
    });

    $(".btn_sendfilter").click(function (e) { 
        e.preventDefault();
    
        let indice = 0,
            formdata = new FormData();
    
        $('.filterList input[type=checkbox]:checked').each(function() {
            if (campo == 'ffemision')
                ffemision[indice++] = $(this).attr("id");
            else if (campo == 'cCostos')
                cCostos[indice++] = $(this).attr("id");
            else if (campo == 'cEntidad')
                cEntidad[indice++] = $(this).attr("id");
        });

        formdata.append("filtro_emision",JSON.stringify(ffemision));
        formdata.append("filtro_costos",JSON.stringify(cCostos));
        formdata.append("filtro_entidad",JSON.stringify(cEntidad));

        fetch(RUTA+'repoprove/filtros',{
            method: 'POST',
            body:formdata
        })
        .then(response => response.json())
        .then(data => {
            $("#tablaPrincipalProveedor tbody").empty();
            
            let row = "",
                montoDolares = "",
                montoSoles = "",
                estado = "",
                ope="",
                fin="",
                log="",
                anio_proceso        = "",
                ordenes_proceso     = "",
                compras_proceso     = "",
                servicio_proceso    = "",
                soles_proceso       = "",
                dolares_proceso     = "";

            anio_proceso    = data.anios;
            ordenes_proceso = data.ordenes;
            compras_proceso = data.compras;
            servicio_proceso = data.servicios;
            soles_proceso = data.soles == null ? "-" : "S/. "+data.soles;
            dolares_proceso = data.dolares == null ? "-" : "$ "+data.dolares;

            data.filas.forEach(fila => {
                if ( fila['ncodmon'] == 20) {
                    montoSoles = "S/. " + fila['ntotal'];
                    montoDolares = "";
                }else{
                    montoSoles = "";
                    montoDolares =  "$ " + fila['ntotal'],2;
                }

                if ( fila['nEstadoDoc'] == 49) {
                    estado = "procesando";
                }else if ( fila['nEstadoDoc'] == 59 ) {
                    estado = "firmas";
                }else if ( fila['nEstadoDoc'] == 60 ) {
                    estado = "recepcion";
                }else if ( fila['nEstadoDoc'] == 62 ) {
                    estado = "despacho";
                }else if ( fila['nEstadoDoc'] == 105 ) {
                    estado = "anulado";
                    montoDolares = "";
                    montoSoles = "";
                }

                log = fila['nfirmaLog'] == null ? '<i class="far fa-square"></i>' : '<i class="far fa-check-square"></i>';
                ope = fila['nfirmaOpe'] == null ? '<i class="far fa-square"></i>' : '<i class="far fa-check-square"></i>';
                fin = fila['nfirmaFin'] == null ? '<i class="far fa-square"></i>' : '<i class="far fa-check-square"></i>';

                row += `<tr>
                            <td class="textoCentro">${fila['cnumero']}</td>
                            <td class="textoCentro">${fila['ffechadoc']}</td>
                            <td class="pl20px">${fila['concepto']}</td>
                            <td class="pl20px">${fila['ccodproy']}</td>
                            <td class="pl20px">${fila['area']}</td>
                            <td class="pl20px">${fila['proveedor']}</td>
                            <td class="textoDerecha">${montoSoles}</td>
                            <td class="textoDerecha">${montoDolares}</td>
                            <td class="textoCentro ${estado}">${estado.toUpperCase()}</td>
                            <td class="textoCentro">${log}</td>
                            <td class="textoCentro">${fin}</td>
                            <td class="textoCentro">${ope}</td>
                        </tr>`;
            });

            $("#anios").text(anio_proceso);
            $("#nroOrdenes").text(ordenes_proceso);
            $("#orden_compra").text(compras_proceso);
            $("#orden_servicio").text(servicio_proceso);
            $("#total_soles").text(soles_proceso);
            $("#total_dolares").text(dolares_proceso);

            barras(valores);

            $("#tablaPrincipalProveedor tbody").append(row);
        })

    
        $(this).parent().parent().fadeOut();
    
        return false;
    });
    

    $(".btn_cancelfilter").click(function(e){
        e.preventDefault();

        $(this).parent().parent().fadeOut();

        return false;
    });

    $(".textoNuevoBuscar").keyup(function () {
        let value = $(this).val().toLowerCase(),
            f = ".filterList"+" li span";

        $(f).filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
})


llamarFiltro = (control,campo) => {
    $(".filter_options").children('ul').empty();

    let formdata = new FormData();
    formdata.append("campo",campo);

    fetch(RUTA+"repoprove/consultarValoresLista",{
        method: "POST",
        body: formdata
    })
    .then(reponse => reponse.json())
    .then(data => {
        data.valores.forEach(valor => {
            let item = ` <li><span><input type="checkbox" id="${valor['id']}"> ${valor['onumero']}</span></li>`;
            $(".filter_options").children('ul').append(item);
        });

        control.parent().parent().children(".filter_options").fadeToggle();
    });
}

barras = (valores) => {
    Highcharts.chart('repo_graphic', {
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
            text: 'Total Items Soles',
        },
        series: [{
            name: "",
            data: valores
        }]
    });
}

