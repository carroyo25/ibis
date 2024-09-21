$(function(){
    let accion = "";
    let grabado = false,indice,tipo;

    $("#esperar").fadeOut();

    $("#tablaPrincipal tbody").on("click","a", function (e) {
        e.preventDefault();

        let id = $(this).attr("href");

        if($(this).data("accion") == 'status'){
            let formData = new FormData();
            
            formData.append("id",id);
            formData.append("transferencia",id);

            fetch(RUTA+'autorizacion/status',{
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data =>{
                if (data[0].frecepcion !== null) {
                    $("#fecha1").text(data[0].frecepcion);
                    $("#circle1")
                        .removeClass('etapa_falta')
                        .addClass('etapa_completa');

                    $("#circle1 p")
                        .removeClass('faltante')
                        .addClass('completado');

                    $("#circle1 p i")
                        .removeClass('fa-times')
                        .addClass('fa-check');
                }else{
                    $("#circle1")
                        .removeClass('etapa_completa')
                        .addClass('etapa_falta');

                    $("#circle1 p")
                        .removeClass('completado')
                        .addClass('faltante');

                    $("#circle1 p i")
                        .removeClass('fa-check')
                        .addClass('fa-times');
                };

                if (data[0].fentrelog !== null) {
                    $("#fecha2").text(data[0].fentrelog);

                    $("#circle2")
                        .removeClass('etapa_falta')
                        .addClass('etapa_completa');

                    $("#circle2 p")
                        .removeClass('faltante')
                        .addClass('completado');

                    $("#circle2 p i")
                        .removeClass('fa-times')
                        .addClass('fa-check');
                }else{
                    $("#circle2")
                        .removeClass('etapa_completa')
                        .addClass('etapa_falta');

                    $("#circle2 p")
                        .removeClass('completado')
                        .addClass('faltante');

                    $("#circle2 p i")
                        .removeClass('fa-check')
                        .addClass('fa-times');
                };

                if (data[0].freceplog !== null) {
                    $("#fecha3").text(data[0].freceplog);

                    $("#circle3")
                    .removeClass('etapa_falta')
                    .addClass('etapa_completa');

                    $("#circle3 p")
                        .removeClass('faltante')
                        .addClass('completado');

                    $("#circle3 p i")
                        .removeClass('fa-times')
                        .addClass('fa-check');
                }else{
                    $("#circle3")
                        .removeClass('etapa_completa')
                        .addClass('etapa_falta');

                    $("#circle3 p")
                        .removeClass('completado')
                        .addClass('faltante');

                    $("#circle3 p i")
                        .removeClass('fa-check')
                        .addClass('fa-times');
                };

                if (data[0].fentreuser !== null) {
                    $("#fecha4").text(data[0].fentreuser);

                    $("#circle4")
                    .removeClass('etapa_falta')
                    .addClass('etapa_completa');

                    $("#circle4 p")
                        .removeClass('faltante')
                        .addClass('completado');

                    $("#circle4 p i")
                        .removeClass('fa-times')
                        .addClass('fa-check');
                }else{
                    $("#circle4")
                        .removeClass('etapa_completa')
                        .addClass('etapa_falta');

                    $("#circle4 p")
                        .removeClass('completado')
                        .addClass('faltante');

                    $("#circle4 p i")
                        .removeClass('fa-check')
                        .addClass('fa-times');
                };

                $("#status").fadeIn();
             })

        }else{
            console.log(id); 
        };

        return false;
    });

    $("#tablaPrincipal tbody").on("click","tr", function (e) {
        e.preventDefault();

        accion = "";

        indice  = $(this).data("indice");
        tipo = $(this).data("tipo"),
            formData = new FormData();
            formData.append('indice', indice);
            formData.append('tipo', tipo);

        fetch(RUTA+'autorizacion/documentoId',{
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            $("#codigo_costos_origen").val(data.datos[0].cc_codigo_origen);
            $("#codigo_costos_destino").val(data.datos[0].cc_codigo_destino);
            $("#codigo_area").val(data.datos[0].narea);
            $("#codigo_tipo").val(data.datos[0].ntipo);
            $("#codigo_solicitante").val(data.datos[0].celabora);
            $("#codigo_origen").val(data.datos[0].norigen);
            $("#codigo_destino").val(data.datos[0].ndestino);
            $("#codigo_estado").val(data.datos[0].nestado);
            $("#codigo_usuario").val(data.datos[0].celabora);
            $("#codigo_autoriza").val(data.datos[0].cautoriza);
            $("#numero").val(data.datos[0].idreg);
            $("#emision").val(data.datos[0].emision);
            $("#costosOrigen").val(data.datos[0].cc_descripcion_origen);
            $("#costosDestino").val(data.datos[0].cc_descripcion_destino);
            $("#area").val(data.datos[0].area);
            $("#solicitante").val(data.datos[0].solicita);
            $("#origen").val(data.datos[0].almacenorigen);
            $("#destino").val(data.datos[0].almacendestino);
            $("#transferencia").val(data.datos[0].transferencia);
            $("#autoriza").val(data.datos[0].autoriza);
            $("#observaciones").val(data.datos[0].observac);
            $("#codigo_traslado").val(data.datos[0].indice);
            $("#tipo").val(data.datos[0].tipo);
            $("#estado_autorizacion").val(data.datos[0].nflgautoriza);
            $("#codigo_traslado").val(data.datos[0].cc_codigo_origen);

            let fila = 1;

            if (data.datos[0].ntipo == 277)
                data.detalles.forEach(element => {
                    let row = `<tr>
                                    <td></td>
                                    <td class="textoCentro">${fila++}</td>
                                    <td class="textoCentro">${element.ccodprod}</td>
                                    <td class="pl20px">${element.cdesprod}</td>
                                    <td class="textoCentro">${element.cabrevia}</td>
                                    <td class="textoDerecha">${element.ncantidad}</td>
                                    <td class="pl20px">${element.cserie}</td>
                                    <td class="pl20px">${element.cdestino}</td>
                                    <td class="pl20px">${element.nparte}</td>
                                    <td class="pl20px">${element.cobserva}</td>
                                </tr>`;
                    
                    $("#tablaDetalles tbody").append(row);
                });
            else
                data.detalles.forEach(element => {
                    let row = `<tr>
                                    <td></td>
                                    <td class="textoCentro">${fila++}</td>
                                    <td class="textoCentro">${element.cregistro}</td>
                                    <td class="pl20px">${element.cdescripcion}</td>
                                    <td class="textoCentro">UND</td>
                                    <td class="textoDerecha">${element.ncantidad}</td>
                                    <td class="pl20px">${element.serie_equipo}</td>
                                    <td class="pl20px">${element.cdestino}</td>
                                    <td class="pl20px">${element.nparte}</td>
                                    <td class="pl20px">${element.cobserva}</td>
                                </tr>`;
                    
                    $("#tablaDetalles tbody").append(row);
                });

            $("#proceso").fadeIn();
        })

        return false;
    });

    $("#closeProcess").click(function (e) { 
        e.preventDefault();

        $.post(RUTA+"autorizatraslado/actualizaListado",
            function (data, textStatus, jqXHR) {
                $(".itemsTabla table tbody")
                    .empty()
                    .append(data);

                $("#proceso").fadeOut(function(){
                    grabado = false;
                    $("form")[1].reset();
                    $("#tablaDetalles tbody").empty();
                    $(".lista").fadeOut();
                });
            },
            "text"
        );
        return false;
    });

    $("#authorizeDocument").click(function (e) { 
        e.preventDefault();

        let formData = new FormData();
        formData.append("id",indice);
        formData.append("user",$("#id_user").val());

        fetch(RUTA+'autorizatraslado/aprueba',{
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            console.log(data);
        })

        return false;
    });
})