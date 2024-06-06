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
            if ($("#referencia").val() == -1) throw new Error("Seleccione una referencia adicional");
            if ($("#area").val() == -1) throw new Error("Seleccione el area");

            let result = $("#form__combustible").serialize(),
                formData = new FormDta();

            formData.append('datos',result);

            fetch(RUTA+'combustible/registro',{
                method: 'POST',
                body:formData
            })
            .then(response => response.json())
            .then(data => {
                console.log(data);
                $("#dialogo_registro").fadeOut();
                accion = "u";
            });
        } catch (error) {
            mmostrarMensaje(error.message,"mensaje_error");
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
})