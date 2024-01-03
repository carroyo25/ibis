(()=>{
    $("#espera").fadeOut();

    $("#tablaPrincipal tbody").on("click","tr", function (e) {
        e.preventDefault();

        $.post(RUTA+"salida/salidaId", {id:$(this).data("indice")},
            function (data, textStatus, jqXHR) {

                let estado = "textoCentro w100por estado " + data.cabecera[0].cabrevia,
                    numero = $.strPad(data.cabecera[0].id_regalm,6);
                
                $("#codigo_salida").val(data.cabecera[0].id_regalm);
                $("#id_guia").val(data.cabecera[0].id_regalm);
                $("#codigo_costos").val(data.cabecera[0].ncodpry);
                $("#codigo_movimiento").val(data.cabecera[0].ncodmov);
                $("#codigo_aprueba").val(data.cabecera[0].id_userAprob);
                $("#codigo_almacen_origen").val(data.cabecera[0].ncodalm1);
                $("#codigo_almacen_destino").val(data.cabecera[0].ncodalm2);
                $("#codigo_estado").val(data.cabecera[0].nEstadoDoc);
                $("#codigo_entidad").val(data.cabecera[0].id_centi);
                $("#almacen_origen_despacho,#almacen_origen").val(data.cabecera[0].origen);
                $("#almacen_destino_despacho,#almacen_destino").val(data.cabecera[0].destino);
                $("#fecha").val(data.cabecera[0].ffecdoc);
                $("#numero").val(numero);
                $("#costos").val(data.cabecera[0].costos);
                $("#ruc").val(data.cabecera[0].cnumdoc);
                $("#guia").val(data.cabecera[0].cnumguia);
                $("#razon").val(data.cabecera[0].crazonsoc);
                $("#aprueba,#autoriza").val(data.cabecera[0].cnombres);
                $("#tipo").val(data.cabecera[0].tipo_movimiento);
                $("#estado").val(data.cabecera[0].estado);
                $("#movimiento").val(data.cabecera[0].movimiento);
                $("#almacen_origen_direccion").val(data.cabecera[0].direccion_origen);
                $("#almacen_destino_direccion").val(data.cabecera[0].direccion_destino);
                $("#ubigeo_origen").val(data.cabecera[0].ubigeo_origen);
                $("#ubigeo_destino").val(data.cabecera[0].ubigeo_destino);
                $("#codigo_origen_sunat").val(data.cabecera[0].sunat_origen);
                $("#codigo_destino_sunat").val(data.cabecera[0].sunat_destino);

                $("#ubig_origen").val(data.cabecera[0].ubigeo_origen);
                $("#ubig_destino").val(data.cabecera[0].ubigeo_destino);

                //guias

                if (data.guias.length == 1) {
                    $("#id_guia").val(data.guias[0].idreg);
                    $("#numero_guia").val(data.guias[0].cnumguia);
                    $("#fgemision").val(data.guias[0].fguia);
                    $("#ftraslado").val(data.guias[0].ftraslado);
                    $("#almacen_origen").val(data.guias[0].corigen);
                    $("#almacen_origen_direccion").val(data.guias[0].cdirorigen);
                    $("#almacen_destino").val(data.guias[0].cdestino);
                    $("#almacen_destino_direccion").val(data.guias[0].cdirdest);
                    $("#empresa_transporte_razon").val(data.guias[0].centi);
                    $("#direccion_proveedor").val(data.guias[0].centidir);
                    $("#ruc_proveedor").val(data.guias[0].centiruc);
                    $("#modalidad_traslado").val(data.guias[0].ctraslado);
                    $("#tipo_envio").val(data.guias[0].cenvio);
                    $("#autoriza").val(data.guias[0].cautoriza);
                    $("#destinatario").val(data.guias[0].cdestinatario);
                    $("#observaciones").val(data.guias[0].cobserva);
                    $("#nombre_conductor").val(data.guias[0].cnombre);
                    $("#licencia_conducir").val(data.guias[0].clicencia);
                    $("#marca").val(data.guias[0].cmarca);
                    $("#placa").val(data.guias[0].cplaca);
                    $("#cso").val(data.cabecera[0].sunat_origen);
                    $("#csd").val(data.cabecera[0].sunat_destino);
                    
                }
                
                $("#estado")
                    .removeClass()
                    .addClass(estado);
                
                $("#tablaDetalles tbody")
                    .empty()
                    .append(data.detalles);

                accion = "u";
                grabado = true;
            
                $("#proceso").fadeIn();

                tipoVista = true;
            },
            "json"
        );

        return false;
    });

    $("#closeProcess").click(function (e) { 
        e.preventDefault();

        $.post(RUTA+"salida/actualizaDespachos",
            function (data, textStatus, jqXHR) {
                $(".itemsTabla table tbody")
                    .empty()
                    .append(data);

                $("#proceso").fadeOut();
                $("#codigo_costos").val("");

                document.getElementById("formProceso").reset();
                document.getElementById("guiaremision").reset();
               
            },
            "text"
        );

        return false;
    });

    $("#btnConsulta").click(function (e) { 
        e.preventDefault();
        
        let str = $("#formConsulta").serialize();

        $.post(RUTA+"salida/filtraDespachos", str,
            function (data, textStatus, jqXHR) {
                $("#tablaPrincipal tbody")
                    .empty()
                    .append(data);
            },
            "text"
        );
        return false;
    });

    $("#atachDocs").click(function(e){
        e.preventDefault();

        $("#archivos").fadeIn();

        return false;
    });

    $("#openArch").click(function (e) { 
        e.preventDefault();
 
        $("#uploadAtach").trigger("click");
 
        return false;
    });

    $("#uploadAtach").on("change", function (e) {
        e.preventDefault();

        fp = $(this);
        let lg = fp[0].files.length;
        let items = fp[0].files;
        let fragment = "";

        if (lg > 0) {
            for (var i = 0; i < lg; i++) {
                var fileName = items[i].name; // get file name

                // append li to UL tag to display File info
                fragment +=`<li><p><i class="far fa-file"></i></p>
                                <p>${fileName}</p></li>`;
            }

            $(".listaArchivos").append(fragment);
        }

        return false;
    });

    $("#btnConfirmAtach").on("click", function (e) {
        e.preventDefault();

        let formData = new FormData();

        formData.append('codigo',$("#codigo_salida").val());

        $.each($('#uploadAtach')[0].files, function(i, file) {
            formData.append('file-'+i, file);
        });

        $.ajax({
            type: "POST",
            url: RUTA+"guiaupdate/archivos",
            data: formData,
            data: formData,
            contentType:false,      
            processData:false,
            dataType: "json",
            success: function (response) {
                $("#atach_counter").text(response.adjuntos);
                $("#archivos").fadeOut();
            }
        });

        return false;
    });

    $("#btnCancelAtach").on("click", function (e) {
        e.preventDefault();

        $("#archivos").fadeOut();
        $("#fileAtachs")[0].reset();
        $(".listaArchivos").empty();

    });
})()