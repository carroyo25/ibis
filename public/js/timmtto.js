$(() => {
    $("#esperar").css({"display":"none","opacity":"0"});

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
                formData.append('fecha',$("#fecha_sugerida").val());

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
                });

                let estado1,semaforo1,estado2,semaforo2,estado3,semaforo3,estado4,semaforo4 = null;

                if ( element.est1 == 1 ){
                    estado1 = 'Realizado';
                    semaforo1 = 'semaforoVerde';
                }else {
                    estado1  =  element.periodo > 0 ? 'Pendiente':'Vencido';
                    semaforo1 = element.periodo > 0 ? 'semaforoNaranja':'semaforoRojo';
                }

                if ( element.est2 == 1 ){
                    estado2 = 'Realizado';
                    semaforo2 = 'semaforoVerde';
                }else {
                    estado2  =  element.periodo2 > 0 ? 'Pendiente':'Vencido';
                    semaforo2 = element.periodo2 > 0 ? 'semaforoNaranja':'semaforoRojo';
                } 

                if ( element.est3 == 1 ){
                    estado3 = 'Realizado';
                    semaforo3 = 'semaforoVerde';
                }else {
                    estado3  =  element.periodo3 > 0 ? 'Pendiente':'Vencido';
                    semaforo3 = element.periodo3 > 0 ? 'semaforoNaranja':'semaforoRojo';
                } 

                if ( element.est4 == 1 ){
                    estado4 = 'Realizado';
                    semaforo4 = 'semaforoVerde';
                }else {
                    estado4  =  element.periodo4 > 0 ? 'Pendiente':'Vencido';
                    semaforo4 = element.periodo4 > 0 ? 'semaforoNaranja':'semaforoRojo';
                } 
               

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
                                <td class="textoCentro ${semaforo1}">${estado1}</td>
                                <td class="textoCentro">${element.fmtto2}</td>
                                <td class="textoCentro ${semaforo2}">${estado2}</td>
                                <td class="textoCentro">${element.fmtto3}</td>
                                <td class="textoCentro ${semaforo3}">${estado3}</td>
                                <td class="textoCentro">${element.fmtto4}</td>
                                <td class="textoCentro ${semaforo4}">${estado4}</td>
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

    $("#excelFile").click(function (e) { 
        e.preventDefault();
        
        $("#esperarCargo").css("opacity","1").fadeIn();

        let formdata = new FormData();

        formdata.append('detalles',JSON.stringify(detalles()));

        fetch (RUTA+"timmtto/archivoExcel",{
            method: "POST",
            body: formdata
        })
            .then((response)=> {
                return response.json();
            })
            .then((json)=> {
                $("#esperarCargo").css("opacity","0").fadeOut();
                window.location.href = json.documento;
            })
            .catch((err)=> {
                console.log(err);
            });

        return false;
    });
})

detalles = () => {
    let DATA = [];
        
    let TABLA = $("#tablaPrincipal tbody >tr");
    
    TABLA.each(function(){
        item= {};
        item['item']        = $(this).find('td').eq(0).text(),
        item['descripcion'] = $(this).find('td').eq(1).text(),
        item['usuario']     = $(this).find('td').eq(2).text(),
        item['serie']       = $(this).find('td').eq(3).text(),
        item['entrega']     = $(this).find('td').eq(4).text(),
        item['costos']      = $(this).find('td').eq(5).text(),
        item['mmtto1']      = $(this).find('td').eq(6).text(),
        item['estado1']     = $(this).find('td').eq(7).text(),
        item['mmtto2']      = $(this).find('td').eq(8).text(),
        item['estado2']      = $(this).find('td').eq(9).text(),
        item['mmtto3']      = $(this).find('td').eq(10).text(),
        item['estado3']      = $(this).find('td').eq(11).text(),
        item['mmtto3']      = $(this).find('td').eq(12).text(),
        item['estado4']      = $(this).find('td').eq(13).text();

        DATA.push(item);
    })

    return DATA;
}