$(function(){
    let registro = 0,fila;

    $("#esperar").fadeOut();

    $("#tablaPrincipal tbody").on("click","tr", function (e) {
        e.preventDefault();

        registro = $(this).data('doc');
        fila = $(this);

        $.post(RUTA+"ajustes/consulta", {id:$(this).data('doc')},
            function (data, text, requestXHR) {
                $("#codigo_costos").val(data.cabecera[0].idcostos);
                $("#codigo_almacen").val(data.cabecera[0].ncodalm);
                $("#codigo_autoriza").val(data.cabecera[0].iduser);
                $("#codigo_tipo").val(data.cabecera[0].ntipomov);
                $("#fecha").val(data.cabecera[0].ffechadoc);
                $("#numero").val($.strPad(data.cabecera[0].idreg,6));
                $("#costos").val(data.cabecera[0].cdesproy);
                $("#almacen").val(data.cabecera[0].almacen);
                $("#registra").val(data.cabecera[0].cnombres);
                $("#tipoMovimiento").val(data.cabecera[0].cdescripcion);
                $("#fechaIngreso").val(data.cabecera[0].ffechaInv);

                $("#tablaDetalles tbody")
                    .empty()
                    .append(data.detalles);
            },
            "json"
        );

        accion = 1;

        $("#proceso").fadeIn();

        return false;
    });

    $("#btnAceptarAjuste").click(function (e) { 
        e.preventDefault();

        let formData = new FormData();
        formData.append("id",registro);
        formData.append("user",$("#id_user").val());

        fetch(RUTA+'autorizaajuste/autoriza',{
            method:'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            fila.remove();
            mostrarMensaje(data.mensaje,"mensaje_correcto");
        })

        $("#pregunta").fadeOut();
        
        return false;
    });

    $("#btnCancelarAjuste").click(function (e) { 
        e.preventDefault();
        
        $("#pregunta").fadeOut();

        return false;
    });
    
    $("#processRequest").click(function(e){
        e.preventDefault();

        $("#pregunta").fadeIn();

        return false;
    });

    $("#closeProcess").click(function (e) { 
        e.preventDefault();

        $("#proceso").fadeOut();
        
        return false;
    });

})