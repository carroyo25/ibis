$(() => {
    $("#esperar").fadeOut();

    $("#btnConsult").click(function(e){
        try {
            if ( $("#tipoSearch").val() === "-1" ) throw new Error("Seleccione el tipo de Evaluación");

            let cabecera = "";

            if ( $("#tipoSearch").val() === "1"){

                cabecera = `<tr>
                        <th rowspan="3"class="filter">Orden</th>  
                        <th rowspan="3">Emision</th>
                        <th rowspan="3" width="15%" class="filter">Descripción</th>
                        <th rowspan="3" class="filter">Centro Costos</th> 
                        <th rowspan="3" width="15%" class="filter">Proveedor</th>
                        <th colspan="5">ALMACÉN RECEPCIÓN</th>
                        <th colspan="7">QA/QC</th> 
                        <th colspan="5">ALMACEN OBRA</th>
                        <th colspan="8">COMPRAS</th> 
                        <th colspan="3">USUARIO / GERENTE DE PROYECTO</th>
                        <th rowspan="3" class="filter">Total</th>
                    </tr>
                    <tr>
                        <th>Fecha de Entrega</th>
                        <th>Condiciones de Llegada</th>
                        <th>Embalaje de Proveedor</th>
                        <th>Cantidad Entregada</th>
                        <th>Documentación</th>

                        <th>Cumplimiento Técnico</th>
                        <th>Documentación</th>
                        <th>Inspeccion Visual</th>
                        <th>Trazabilidad</th>
                        <th>Comunicación</th>
                        <th>Aceptación de Reclamos</th>
                        <th>Almacenamiento y Preservacion</th>

                        <th>Cantidad Entregada</th>
                        <th>Condiciones de Llegada</th>
                        <th>Embalaje</th>
                        <th>Garantía del Material</th>
                        <th>Documentación</th>

                        <th>Precio Competitivo</th>
                        <th>Descuento</th>
                        <th>Delivery</th>
                        <th>Aceptación de Reclamos</th>
                        <th>Forma de Pago</th>
                        <th>Comunicación</th>
                        <th>Seriedad</th>
                        <th>Capacitación</th>

                        <th>Fecha de Atención</th>
                        <th>Calidad</th>
                        <th>Cantidad de Procesos</th>
                    </tr>`;
            }else {
                cabecera = `<tr>
                                <th rowspan="3" class="filter">Orden</th>  
                                <th rowspan="3">Emision</th>
                                <th rowspan="3" width="15%" class="filter">Descripción</th>
                                <th rowspan="3" class="filter">Centro Costos</th> 
                                <th rowspan="3" width="15%" class="filter">Proveedor</th>
                                <th colspan="8">QA/QC</th> 
                                <th colspan="7">COMPRAS</th> 
                                <th colspan="3">USUARIO / GERENTE DE PROYECTO</th>
                                <th rowspan="3" class="filter">Total</th>
                            </tr>
                            <tr>
                                <th>Plan de Gestion</th>
                                <th>Plan de Puntos</th>
                                <th>Procedimientos</th>
                                <th>Requisitos</th>
                                <th>Aceptación de Reclamos</th>
                                <th>Comunicación</th>
                                <th>Dossier</th>
                                <th>Avisa oportunamente</th>
                                
                                <th>Precio</th>
                                <th>Descuento</th>
                                <th>Notificaciones</th>
                                <th>Aceptación de Reclamos</th>
                                <th>Forma de pago</th>
                                <th>Comunicación</th>
                                <th>Seriedad</th>
                                
                                <th>Fecha de Entrega</th>
                                <th>Calidad</th>
                                <th>Cantidad de Procesos</th>
                                
                            </tr>`;
            }

            $.post(RUTA+"evalrepo/evaluaciones", $("#formConsulta").serialize(),
                        function (data, text, requestXHR) {
                            $("#cargoPlanDescrip thead").empty().append(cabecera);
                            $("#cargoPlanDescrip tbody").empty().append(data);
                        },
                        "text"
                    );
            
            
            
        } catch (error) {
            mostrarMensaje(error,"mensaje_error");
        }
    });

    $("#btnExporta").click(function(e){
        e.preventDefault();

       
        $.post(RUTA+"evalrepo/evaluacionesExcel", $("#formConsulta").serialize(),
            function (data, textStatus, jqXHR) {
                window.location.href = data.documento;
            },
            "json"
        );

        return false;
    });
});

