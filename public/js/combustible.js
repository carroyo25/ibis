$(() => {
    let accion = "u",flag=false;

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
            let formData = new FormData();

            formData.append("item",$('select[name="tipo_item"] option:selected').val());

            fetch(RUTA+'combustible/reporte',{
                method: 'POST',
                body: formData
            })
            .then(reponse => reponse.json())
            .then(data =>{
                let stock_inicial = data.stock_inicial == null ? 0 : data.stock_inicial,
                    ingreso_mes_actual = data.ingreso_mes_actual == null ? 0 : data.ingreso_mes_actual,
                    consumo_mes_actual = data.consumo_mes_actual == null ? 0 : data.consumo_mes_actual;

                $("#stockInicial").text(stock_inicial);
                $("#ingresomesactual").text(ingreso_mes_actual);
                $("#cantidadconsumo").text(consumo_mes_actual);
                $("#stockfinal").text((stock_inicial+ingreso_mes_actual)-consumo_mes_actual);
            })
        };
        
        return false;
    });
})