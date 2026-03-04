$(function(){  
    $("#esperar").css({"display":"none","opacity":"0"});

    const modal_registro = document.getElementById("dialogo_registro");

    const btnRegister = document.getElementById("nuevoRegistro");
    const btnExport = document.getElementById("excelFile");
    const btnCancelDialog = document.getElementById("btnCancelarDialogoActivos");

    const inputSearchCode = document.getElementById("codigoSearch");
    const inputSerie = document.getElementById("serie");
    const inputItemCode = document.getElementById("codigo_interno");

    const sltCostos = document.getElementById("centro_costos");


    btnRegister.addEventListener('click',(e) =>{
        e.preventDefault();

        modal_registro.style.display = 'block';

        return false;
    });

    btnCancelDialog.addEventListener('click',(e)=>{
        e.preventDefault();

        modal_registro.style.display = 'none';

        return false;
    })

    inputSearchCode.addEventListener('keydown',(e)=>{
        if (e.key == 'Enter'){
            try {
                if (sltCostos.value == -1) throw new Error("Seleccione un centro de costos");
                if (e.target.value == '' ) throw new Error("Escriba un codigo para validar");

                const formData = new FormData();
                formData.append("codigo",e.target.value);
                formData.append("costos",sltCostos.value);

                fetch(RUTA+'activos/buscaCodigo',{
                    method:'POST',
                    body:formData
                })
                .then(response => response.json())
                .then(data =>{
                    document.getElementById("descripSearch").value  = data.datos[0]['descripcion'];
                    document.getElementById("unidad").value         = data.datos[0]['cabrevia'];
                    document.getElementById("codigo_interno").value = data.datos[0]['id_cprod'];
                    document.getElementById("codigo_unidad").value  = data.datos[0]['ncodmed'];

                    mostrarMensaje('👌 Codigo existente','mensaje_correcto');
                })

            } catch (error) {
                mostrarMensaje(error.message,'mensaje_error')
                document.getElementById("codigo_interno").value = '';
            }
        }
    })

    inputSerie.addEventListener('keydown',(e)=>{
        if (e.key == 'Enter'){
            try {
                if (sltCostos.value == -1) throw new Error("Seleccione un centro de costos");
                if (inputItemCode.value == '') throw new Error("Seleccione un codigo de producto para validar");
                if (e.target.value == '' ) throw new Error("Escriba una serie para validar");

                const formData = new FormData();
                formData.append("codigo",inputSearchCode);
                formData.append("costos",sltCostos.value);
                formData.append("serie",e.target.value);


                fetch(RUTA+'activos/asignados',{
                    method:'POST',
                    body:formData
                })
                .then(response => response.json())
                .then(data =>{
                    /*document.getElementById("descripSearch").value  = data.datos[0]['descripcion'];
                    document.getElementById("unidad").value         = data.datos[0]['cabrevia'];
                    document.getElementById("codigo_interno").value = data.datos[0]['id_cprod'];
                    document.getElementById("codigo_unidad").value  = data.datos[0]['ncodmed'];
                    
                    mostrarMensaje('👌 Codigo existente','mensaje_correcto')*/
                })

            } catch (error) {
                mostrarMensaje(error.message,'mensaje_error')
            }
        }
    })
})


