$(() => {
    /*$("#esperar").fadeOut();
    
    let accion = "u",flag=false;

    let seriesData = [1,2,3,4,5,6,7,8,9,10,11,12];*/

    listarRegistrosCombustible();


    $("#esperar").fadeOut();

    $("#nuevoRegistro").click(function (e) { 
        e.preventDefault();
        
        accion = "n";
        $("#dialogo_registro").fadeIn();

    });

    $("#btn_consumo_aceptar").click(function (e) { 
        e.preventDefault();

        try {
            if ($("#tipo").val() == -1) throw new Error("Seleccione el tipo de operación");
            if ($("#codigo").val() == "") throw new Error("Selecione el codigo del item");
            if ($("#cantidad").val() == 0) throw new Error("Ingrese una cantidad válida");
            if ($("#documento").val() == "") throw new Error("Ingrese el número de documento");
            if ($("#proyecto").val() == -1) throw new Error("Seleccion el proyecto");
            //if ($("#referencia").val() == -1) throw new Error("Seleccione una referencia adicional");
            if ($("#area").val() == -1) throw new Error("Seleccione una area");

            //serializar los formulario en javascript
            const datos = new URLSearchParams(new FormData(document.getElementById("form__combustible")));

            fetch(RUTA+'combustible/registro',{
                method: 'POST',
                body:datos
            })
            .then(response => response.json())
            .then(data => {
                $("#dialogo_registro").fadeOut();
                accion = "u";
            });
            
        } catch (error) {
            mostrarMensaje(error.message,"mensaje_error");
        }
        return false;
    });

    $("#btn_consumo_cancelar").click(function (e) { 
        e.preventDefault();

        $("#dialogo_registro").fadeOut();

        return false;
    });

    $("#codigo").keypress(function (e) { 
        if(e.which == 13) {
            try {
                let codigo = $(this).val(),
                    formdata = new FormData();

                if ( codigo == "" ) throw new Error("Ingrese el codigo a registrar");

                formdata.append('codigo',codigo);

                $("#esperarCargo").css("opacity","1").fadeIn();

                fetch (RUTA+"combustible/codigo",{
                    method: "POST",
                    body: formdata
                })
                    .then((response)=> {
                        return response.json();
                    })
                    .then((json)=> {
                        $("#esperarCargo").css("opacity","0").fadeOut();
                        $("#descripcion").val(json.datos[0].cdesprod);
                        $("#unidad").val(json.datos[0].cdesmed);
                        $("#codigo_producto").val(json.datos[0].id_cprod)
                    })
                    .catch((err)=> {
                        console.log(err);
                    });
            } catch (error) {
                mostrarMensaje(error.message,"mensaje_error");
            }
        }
    });

    $("#documento").keypress(function (e) { 
        if(e.which == 13) {
            try {
                let documento = $(this).val(),
                    formdata = new FormData();

                if ( documento == "" ) throw new Error("Ingrese el N° de documento");

                formdata.append('documento',documento);

                $("#esperarCargo").css("opacity","1").fadeIn();

                fetch (RUTA+"combustible/documento",{
                    method: "POST",
                    body: formdata
                })
                    .then((response)=> {
                        return response.json();
                    })
                    .then((data)=> {
                        if(data.registrado) {
                            $("#trabajador").val(data.datos[0].nombres+' '+data.datos[0].paterno+' '+data.datos[0].materno);
                        }else{
                            mostrarMensaje("Trabajador no registrado","mensaje_error");
                        }
                        
                    })
                    .catch((err)=> {
                        console.log(err);
                    });

            } catch (error) {
                mostrarMensaje(error.message,"mensaje_error");
            }
        }
    });

    $("#kardexDetails").click(function (e) { 
        e.preventDefault();
        
        $("#filtros").fadeIn();

        return false;
    });

    $("#closeInform").click(function (e) { 
        e.preventDefault();
        
        $("#filtros").fadeOut();

        return false;
    });

    $("#tipo_item").change(function (e) { 
        e.preventDefault();

        if( $('select[name="tipo_item"] option:selected').val() !== "0"){
            let formData = new FormData(),
                seriesIngreso = [],
                seriesSalida = [];

            formData.append("item",$('select[name="tipo_item"] option:selected').val());

            fetch(RUTA+'combustible/reporte',{
                method: 'POST',
                body: formData
            })
            .then(reponse => reponse.json())
            .then(data =>{
                let stock_inicial = data.stock_inicial == null ? 0 : data.stock_inicial,
                    ingreso_mes_actual = data.ingreso_mes_actual == null ? 0 : data.ingreso_mes_actual,
                    consumo_mes_actual = data.consumo_mes_actual == null ? 0 : data.consumo_mes_actual,
                    consolidado_anual = data.consolidado_anual == null ? 0 : data.consolidado_anual;

                //seriesIngreso = data.valores_ingreso;

                $("#consolidadoAnual").text(consolidado_anual);
                $("#stockInicial").text(stock_inicial);
                $("#ingresomesactual").text(ingreso_mes_actual);
                $("#cantidadconsumo").text(consumo_mes_actual);
                $("#stockfinal").text((stock_inicial+ingreso_mes_actual)-consumo_mes_actual);

                /*data.valores[0].forEach(valor =>{
                    seriesData.push({
                        name: valor['nombre'],
                        data: valor['series']
                    });
                })*/

                data.valores_ingreso[0].forEach(valor => {
                    seriesIngreso.push({
                        
                    })
                });

                barras(seriesIngreso);
            })
        };
        
        return false;
    });

    $("#reportExport").click(function (e) { 
        e.preventDefault();

        $("#esperar").css("opacity","1").fadeIn();

        $.post(RUTA+"combustible/exporta", {detalles:JSON.stringify(detalles())},
            function (data, textStatus, jqXHR) {
                window.location.href = data.documento;
                $("#esperar").css("opacity","0").fadeOut();
            },
            "json"
        );

        return false;
        
    });
})

barras = (seriesData) => {
    Highcharts.chart('graficoEstadistico', {
        chart: {
            type: 'column'
        },
        title:{
            text:'Ingreso y consumo de combustible por mes',
            align: 'center'
        },
        xAxis: {
            categories: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic']
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Galones (GL)'
            }
        },
        plotOptions: {
            series: {
                pointWidth: 20
            }
        },
        series:[ 
            {   name:'Ingresos',
                data: seriesData,
            },
            {   name:'Salidas',
                data: [12,11,10,9,8,7,6,5,4,3,2,1]
            }
        ]
    });
}

detalles = () => {
    let DATA = [];
        
    let TABLA = $("#tablaPrincipal tbody >tr");
    
    TABLA.each(function(){
        item= {};

        item['numero']          = $(this).find('td').eq(0).text(),
        item['emision']         = $(this).find('td').eq(1).text(),
        item['almacen']         = $(this).find('td').eq(2).text(),
        item['tipo']            = $(this).find('td').eq(3).text(),
        item['codigo']          = $(this).find('td').eq(4).text(),
        item['descripcion']     = $(this).find('td').eq(5).text(),
        item['unidad']          = $(this).find('td').eq(6).text(),
        item['cantidad']        = $(this).find('td').eq(7).text(),
        item['trabajador']      = $(this).find('td').eq(8).text(),
        item['usuario']         = $(this).find('td').eq(9).text(),
        item['proyecto']        = $(this).find('td').eq(10).text(),
        item['observaciones']   = $(this).find('td').eq(11).text(),
        item['documento']       = $(this).find('td').eq(12).text(),
        item['area']            = $(this).find('td').eq(13).text(),
        item['referencia']      = $(this).find('td').eq(14).text(),
        item['mes']             = $(this).find('td').eq(15).text()

        DATA.push(item);
    })

    return DATA;
}


listarRegistrosCombustible = async () => {
    try {
        let formData = new FormData();
        formData.append('nota',document.getElementById('notaSearch').value);
        formData.append('cc',document.getElementById('costosSearch').value);
        formData.append('mes',document.getElementById('mesSearch').value);
        formData.append('anio',document.getElementById('anioSearch').value);

        const response = await fetch(RUTA + "combustible/listaCombustibles", {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        const tablaCuerpo = document.getElementById("tablaPrincipalCuerpo");

        if (!tablaCuerpo) {
            throw new Error("Element with ID 'tablaPrincipalCuerpo' not found");
        }

        tablaCuerpo.innerHTML = "";

        let item = 1;

        data.datos.forEach ( e =>{
            const tr = document.createElement("tr");
            tr.classList.add("pointer");
            tr.dataset.id_consumo = e.idreg
            tr.innerHTML = `<td>${item++}</td>
                            <td>${e.fregistro}</td>
                            <td>${e.cdesalm}</td>
                            <td>${e.idtipo == 1 ?'INGRESO':'SALIDA'}</td>
                            <td>${e.ccodprod}</td>
                            <td>${e.cdesprod}</td>
                            <td>${e.cabrevia}</td>
                            <td>${e.ncantidad}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>`;


            tablaCuerpo.appendChild(tr);
        });

        $("#esperar").fadeOut().promise().done(function(){
           //iniciarPaginadorConsulta();
        });

    } catch (error) {
        mostrarMensaje('No hay registros para procesar','mensaje_error');
        $("#esperar").fadeOut();
    }
}