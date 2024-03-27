$(() => {
    let id,cc,docidetuser,serie;

    const tabla_principal = document.getElementById('tablaPrincipal');

    tabla_principal.addEventListener('click',(e)=>{
        e.preventDefault();

        if (e.target.matches(".click_link *")){
            
            serie = e.target.parentNode.getAttribute('href');
            docidetuser = e.target.parentNode.dataset.documento;

            $("#cambio_fecha").fadeIn();

        }else if (e.target.matches(".click_tr *")){
            $("#serie").val( e.target.closest(".click_tr").dataset.serie );
            $("#idmmtto").val( e.target.closest(".click_tr").dataset.id );
            $("#descripcion").val( e.target.closest(".click_tr").cells[1].innerHTML );
            $("#fecha_sugerida").val( e.target.closest(".click_tr").cells[5].innerHTML );
            $("#usuario").val( e.target.closest(".click_tr").cells[2].innerHTML );
            $("#correo_usuario").val( e.target.closest(".click_tr").dataset.correo );
            $("#sendNotify").prop("href", e.target.closest(".click_tr").dataset.id );

            $("#procesador").val(e.target.closest(".click_tr").dataset.procesador);
            $("#ram").val(e.target.closest(".click_tr").dataset.ram);
            $("#hdd").val(e.target.closest(".click_tr").dataset.hdd);
            $("#otros").val(e.target.closest(".click_tr").dataset.otros);

            idprod      = $(this).data('idprod');
            cc          = $(this).data('costos');
            docidetuser = $(this).data('documento');

            $("#tabla_detalles_mttos tbody").empty();

            id = $(this).data('id');

            let formData = new FormData();
                formData.append('serie',e.target.closest(".click_tr").cells[3].innerHTML);
                formData.append('documento',e.target.closest(".click_tr").dataset.documento);

        
            fetch(RUTA+'timmtto/anteriores',{
                method: 'POST',
                body:formData
            })
            .then(response => response.json())
            .then(data => {
                data.mmttos.forEach(element => {
                    let row = `<tr>
                                    <td class="textoCentro">${element.frelmtto}</td>
                                    <td class="pl20px">${element.cobserva}</td>
                                    <td class="pl20px">${element.tecnico}</td>
                                </tr>`;
    
                    $("#tabla_detalles_mttos tbody").append(row);
                });
    
                $("#idlastmmtto").val(data.lastmmttos.id);
                $("#fecha_sugerida").val(data.lastmmttos.fecha_proxima);
    
                $("#dialogo_registro").fadeIn();
            })

            return false;
        }

        return false;
    })

    $("#btnAceptarDialogo").click(function (e) {
        e.preventDefault();
        
        try {
            if ($("#fecha_mmto").val() == "") throw new Error("No ingreso fecha del mantenimiento");

            let formData = new FormData();
                formData.append('id',$("#idmmtto").val());
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
                formData.append('estado',$("#estado_equipo").val()); //
                
                formData.append('codigo_costos',cc);
                formData.append('codigo_producto',null);
                formData.append('serie_producto',$("#serie").val());
                formData.append('documento_usuario',docidetuser);

                formData.append('lastMmtto',$("#idlastmmtto").val());

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

    $("#btnAceptarGrabar").click(function(e) {
        e.preventDefault();
        try {
            if ( $("#fecha_nueva").val() === "" ) throw new Error("Escoja una fecha");

            let formData = new FormData();
                formData.append("fecha",$("#fecha_nueva").val());
                formData.append("serie",serie);
                formData.append("documento",docidetuser);

            fetch(RUTA+'timmtto/cambiofechas',{
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                console.log(data);
            })
            
        } catch (error) {
            mostrarMensaje(error,'mensaje_error');
        }

        
        return false;
    });

    $("#btnCancelarGrabar").click(function(e) {
        e.preventDefault();

        $("#cambio_fecha").fadeOut();

        return false;
    });

    $("#btnConsulta").click(function (e) { 
        e.preventDefault();

        $("#tablaPrincipal tbody").empty();

        let formData = new FormData();
            formData.append("costos",$("#costosSearch").val());
            formData.append("serie",$("#serieBusqueda").val());
        
        fetch(RUTA+'timmtto/listaMmttos',{
            method: 'POST',
            body: formData,
        })
        .then(response=>response.json())
        .then(data=>{
            let item = 1,nombre,correo;

            $("#tablaPrincipal tbody").empty();

            data.datos.forEach (element =>{

                data.usuarios.forEach (usuario => {
                    if ( usuario.dni === element.nrodoc){
                        nombre = usuario.usuario;
                        correo = usuario.correo;
                    }
                })

                let fila = `<tr class="pointer click_tr" 
                                    data-id         ="${element.idreg}" 
                                    data-correo     ="${correo}"
                                    data-documento  ="${element.nrodoc}"
                                    data-costos     ="${element.nidreg}"
                                    data-serie      ="${element.cserie}"
                                    data-procesador ="${element.cprocesador}"
                                    data-ram        ="${element.cram}"
                                    data-hdd        ="${element.chdd}"
                                    data-otros      ="${element.totros}">
                                <td class="pl20px">${item++}</td>
                                <td class="pl20px">${element.cdesprod}</td>
                                <td class="pl20px">${nombre}</td>
                                <td class="pl20px">${element.cserie}</td>
                                <td class="textoCentro">${element.fentrega}</td>
                                <td class="textoCentro">${element.ccodproy}</td>
                                <td class="textoCentro">${element.fmtto1}</td>
                                <td class="textoCentro ${element.est1 == 0 ? 'semaforoNaranja':'semaforoVerde'}">${element.est1 == 0 ? 'Pendiente':'Realizado'}</td>
                                <td class="textoCentro">${element.fmtto2}</td>
                                <td class="textoCentro ${element.est2 == 0 ? 'semaforoNaranja':'semaforoVerde'}">${element.est2 == 0 ? 'Pendiente':'Realizado'}</td>
                                <td class="textoCentro">${element.fmtto3}</td>
                                <td class="textoCentro ${element.est3 == 0 ? 'semaforoNaranja':'semaforoVerde'}">${element.est3 == 0 ? 'Pendiente':'Realizado'}</td>
                                <td class="textoCentro">${element.fmtto4}</td>
                                <td class="textoCentro ${element.est4 == 0 ? 'semaforoNaranja':'semaforoVerde'}">${element.est4 == 0 ? 'Pendiente':'Realizado'}</td>
                                <td class="textoCentro click_link">
                                    <a href="${element.cserie}" data-fecha ="${element.entrega}" data-documento ="${element.nrodoc}">
                                        <i class="fas fa-calendar-alt"></i>
                                    </a>
                                </td>
                            </tr>`;

                $("#tablaPrincipal tbody").append(fila);
            })
        })

        return false;
    });
})