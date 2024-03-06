$(() => {
    let id,idprod,cc,docidetuser;

    $("#tablaPrincipal tr").on("click", function (e) {
        e.preventDefault();

        $("#serie").val( $(this).find('td').eq(3).text() );
        $("#descripcion").val( $(this).find('td').eq(1).text() );
        $("#fecha_sugerida").val( $(this).find('td').eq(6).text() );
        $("#usuario").val($(this).find('td').eq(2).text());
        $("#correo_usuario").val( $(this).data('correo'));
        $("#sendNotify").prop("href", $(this).data('id'));

        idprod = $(this).data('idprod');
        cc = $(this).data('costos');
        docidetuser = $(this).data('documento');

        $("#tabla_detalles_mttos tbody").empty();

        id = $(this).data('id');

        let formData = new FormData();
        formData.append('serie',$(this).find('td').eq(3).text());
        formData.append('documento',$(this).data('documento'));

        fetch(RUTA+'timmtto/anteriores',{
            method: 'POST',
            body:formData
        })
        .then(response => response.json())
        .then(data => {
            data.forEach(element => {
                let row = `<tr>
                                <td class="textoCentro">${element.frelmtto}</td>
                                <td class="pl20px">${element.cobserva}</td>
                                <td class="pl20px">${element.tecnico}</td>
                            </tr>`;

                $("#tabla_detalles_mttos tbody").append(row);
            });

            $("#dialogo_registro").fadeIn();
        })

    
        return false;
    });

    $("#btnAceptarDialogo").click(function (e) {
        e.preventDefault();
        
        try {
            if ($("#fecha_mmto").val() == "") throw new Error("No ingreso fecha del mantenimiento");

            let formData = new FormData();
                formData.append('id',null);
                formData.append('fmmto',$("#fecha_mmto").val());
                formData.append('correo',$("#correo_usuario").val());
                formData.append('observa',$("#observaciones_dialogo").val());
                formData.append('user',$("#id_user").val());
                formData.append('tecnico',$("#name_user").val());
                formData.append('correo_tecnico',$("#mail_user").val());
                formData.append('asignado',$("#usuario").val());
                formData.append('tipo_mmtto',$("#tipo_mmtto").val());

                formData.append('procesador',$("#procesador").val()); //
                formData.append('ram',$("#ram").val()); //
                formData.append('hdd',$("#hdd").val()); //
                formData.append('otros',$("#otros").val()); //
                
                formData.append('codigo_costos',cc);
                formData.append('codigo_producto',null);
                formData.append('serie_producto',$("#serie").val());
                formData.append('documento_usuario',docidetuser);

            fetch(RUTA+'timmtto/mantenimiento',{
                method: 'POST',
                body:formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.respuesta){
                    mostrarMensaje('Mantenimiento registrado','mensaje_correcto');
                    $("#dialogo_registro").fadeOut();
                }
            })
        } catch (error) {
            mostrarMensaje(error,"mensaje_error")
        }
        

        return false;
    })


    $("#btnCancelarDialogo").click(function (e) {
        e.preventDefault();
        
        $("#dialogo_registro").fadeOut();

        return false;
    })

    $("#sendNotify").click(function (e) { 
        e.preventDefault();

        $("#esperar").css({"display":"block","opacity":"1"});
        
        try {
            let formData = new FormData();
                formData.append('id',id);
                formData.append('fmmto',$("#fecha_mmto").val());
                formData.append('correo',$("#correo_usuario").val());
                formData.append('tecnico',$("#name_user").val());
                formData.append('correo_tecnico',$("#mail_user").val());
                formData.append('serie_producto',$("#serie").val());
                formData.append('usuario',$("#usuario").val());

            fetch(RUTA+'timmtto/notificar',{
                method: 'POST',
                body:formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.respuesta){
                    $("#esperar").css("opacity","0").fadeOut();
                    mostrarMensaje('Correo Enviado','mensaje_correcto');
                    $("#dialogo_registro").fadeOut();
                }
            })
        } catch (error) {
            mostrarMensaje(error,"mensaje_error")
        }

        return false;
    });
})