$(function() {
    let accion = "",
        tipoVista = null,
        cc = "",
        fila = "",
        idfila = "",
        ordenes = [],
        sw=0,
        grabado = false,
        controlUbigeo = null,
        ubigeo = null,
        fila_actualiza;
        

    $("#esperar").fadeOut();

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
                $("#ftraslado").val(data.cabecera[0].ffecenvio);
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

                $("#nombre_entidad_origen").val(data.cabecera[0].nombre_entidad_origen);
                $("#ruc_entidad_origen").val(data.cabecera[0].ruc_entidad_origen);

                $("#nombre_entidad_destino").val(data.cabecera[0].nombre_entidad_destino);
                $("#ruc_entidad_destino").val(data.cabecera[0].ruc_entidad_destino);

                $("#ubig_origen").val(data.cabecera[0].ubigeo_origen);
                $("#ubig_destino").val(data.cabecera[0].ubigeo_destino);

                $("#ubigeo_origen_guia").val(data.cabecera[0].ubigeo_origen);
                $("#ubigeo_destino_guia").val(data.cabecera[0].ubigeo_destino);

                //guias

                if (data.guias.length == 1) {
                    $("#id_guia").val(data.guias[0].idreg);
                    $("#numero_guia").val(data.guias[0].cnumguia);
                    $("#fgemision").val(data.cabecera[0].ffecdoc);
                    $("#ftraslado").val(data.cabecera[0].ffecenvio);
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
                    $("#numero_guia_sunat").val(data.guias[0].guiasunat); 
                    $("#ticket_sunat").val(data.guias[0].ticketsunat);
                    $("#peso").val(data.guias[0].nPeso);
                    $("#registro_mtc").val(data.guias[0].cdigcateg);
                    $("#conductor_dni").val(data.guias[0].cnrodoc);
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
                estado = 1;

                $(".primeraBarra").css("background","#0078D4");
                $(".primeraBarra span").text("Datos Generales");
            },
            "json"
        );

        return false;
    });

    $("#nuevoRegistro").click(function (e) { 
        e.preventDefault();

        $("#estado")
            .removeClass()
            .addClass("textoCentro estado w100por procesando");
        $("#tablaDetalles tbody").empty();
        $("#proceso").fadeIn();

        document.getElementById("formProceso").reset();
        document.getElementById("guiaremision").reset();

        $('input[type="hidden"]').val('');

        $("#tipo").val("SALIDA X OC");
        $("#codigo_movimiento").val(144);
        
        accion = 'n';
        cc = "";
        grabado = false;

        tipoVista = null;

        $(".primeraBarra").css("background","#0078D4");
        $(".primeraBarra span").text("Datos Generales");

        return false;
    });

    $("#closeProcess").click(function (e) { 
        e.preventDefault();

        $("#proceso").fadeOut();

        $("#codigo_costos").val("");

        document.getElementById("formProceso").reset();
        document.getElementById("guiaremision").reset();


        return false;
    });

    $("#importData").click(function (e) { 
        e.preventDefault();

        try {
            $.post(RUTA+"salida/ordenes",
            function (data, textStatus, jqXHR) {
                $("#ordenes tbody")
                    .empty()
                    .append(data);

                $("#busqueda").fadeIn();
            },
            "text"
            );
            
        } catch (error) {
            mostrarMensaje(error,'mensaje_error');
        }

        return false
    });

    $(".tituloVentana").on("click","a", function (e) {
        e.preventDefault();
        
        $(this).parent().parent().parent().parent().fadeOut();

        return false;
    });

    $(".mostrarLista").focus(function (e) { 
        e.preventDefault();
        
        $(this).next().slideDown();

        return false;
    });
    
    $(".mostrarListaInterna").focus(function (e) { 
        e.preventDefault();
        
        $(this).next().slideDown();

        return false;
    });

    $(".cerrarLista").focus(function (e) { 
        e.preventDefault();
        
        $(".lista").fadeOut();

        return false;
    });

    $(".lista").on("click",'a', function (e) {
        e.preventDefault();

        let control = $(this).parent().parent().parent();
        let destino = $(this).parent().parent().parent().prev();
        let contenedor_padre = $(this).parent().parent().parent().attr("id");
        let id = "";
        let codigo = $(this).attr("href");
        
        control.slideUp()

        destino.val($(this).text());
        id = destino.attr("id");

        if(contenedor_padre == "listaMovimiento"){
            $("#codigo_movimiento").val(codigo);
        }else if(contenedor_padre == "listaAprueba"){
            $("#codigo_aprueba").val(codigo);
            $("#autoriza").val($(this).text());
        }else if(contenedor_padre == "listaOrigen"){
            $("#codigo_almacen_origen").val(codigo);
            $("#codigo_origen").val(codigo);
            $("#almacen_origen").val($(this).text());
            $("#almacen_origen_direccion").val($(this).data('direccion'));
            $("#codigo_origen_sunat").val($(this).data('sunat'));
            $("#ubigeo_origen_guia,#ubig_origen").val($(this).data('ubigeo'));
        }else if(contenedor_padre == "listaDestino"){
            $("#codigo_almacen_destino").val(codigo);
            $("#almacen_destino").val($(this).text());
            $("#almacen_destino_direccion").val($(this).data('direccion'));
            $("#codigo_destino_sunat").val($(this).data('sunat'));
            $("#ubigeo_destino_guia,#ubig_destino").val($(this).data('ubigeo'));
        }else if(contenedor_padre == "listaAutoriza"){
            $("#autoriza").val($(this).text());
            $("#codigo_autoriza").val(codigo);
        }else if(contenedor_padre == "listaDespacha"){
            $("#codigo_despacha").val(codigo);
        }else if(contenedor_padre == "listaDestinatario"){
            $("#destinatario").val($(this).text());
            $("#codigo_destinatario").val(codigo);
        }else if(contenedor_padre == "listaModalidad"){
            $("#modalidad_traslado").val($(this).text());
            $("#codigo_modalidad").val(codigo);
        }else if(contenedor_padre == "listaEnvio"){
            $("#tipo_envio").val($(this).text());
            $("#codigo_tipo").val(codigo);
        }else if(contenedor_padre == "listaEntidad"){
            $("#codigo_entidad_transporte").val(codigo);
            $("#empresa_transporte_razon").val($(this).text());
            $("#ruc_proveedor").val($(this).data("ruc"));
            $("#direccion_proveedor").val($(this).data("direccion"));
            $("#registro_mtc").val($(this).data("mtc"));
        }else if(contenedor_padre == "listaTransporte"){
            $("#codigo_transporte").val(codigo);
            $("#tipo_transporte").val($(this).text());
        }else if(contenedor_padre == "listaConductores"){
            $("#nombre_conductor").val($(this).text());
            $("#licencia_conducir").val($(this).data('licencia'));
            $("#conductor_dni").val($(this).data('dni'));
        }else if(contenedor_padre == "listaPlacas"){
            $("#placa").val($(this).text());
        }

        return false;
    });

    //genera la nota de salida
    $("#preview").click(function (e) { 
        e.preventDefault();

        try {
            let result = {};

            $.each($("#formProceso").serializeArray(),function(){
                result[this.name] = this.value;
            });

            if ( tipoVista == null ) throw "Por favor grabar el documento";

            $.post(RUTA+"salida/documentoPdf", {cabecera:result,
                                                detalles:JSON.stringify(detallesVista(1)),
                                                condicion:0},
                function (data, textStatus, jqXHR) {
                    $(".ventanaVistaPrevia iframe")
                    .attr("src","")
                    .attr("src",data);

                    $("#vistaprevia").fadeIn();
                },
                "text"
            );

        } catch (error) {
            mostrarMensaje(error,'mensaje_error');
        }

        return false;
    });

    $("#closePreview").click(function (e) { 
        e.preventDefault();

        $(".ventanaVistaPrevia iframe").attr("src","");
        $("#vistaprevia").fadeOut();

        return false;
    });

    $(".tituloDocumento").on("click","#closeDocument", function (e) {
        e.preventDefault();

        $(this).parent().parent().parent().parent().parent().fadeOut();

        return false;
    });

    $("#ordenes tbody").on("click","tr", function (e) {
        e.preventDefault();

        cc = $(this).data("idcosto");

        let orden = $(this).find('td').eq(0).text();

        try {
            if ( $("#codigo_costos").val() != cc && $("#codigo_costos").val() != "" ) throw new Error("Los orden es de otro centro de costos");
            if (buscarOrden(orden)) throw new Error("La orden ya se esta procesando");

            $.post(RUTA+"salida/ordenId", {id:$(this).data("orden"),costo:$(this).data("idcosto")},
            function (data, textStatus, jqXHR) {
                if (accion == "n")
                    $("#numero").val(data.numero);

                $("#tablaDetalles tbody").append(data.items);
                $("#costos").val(data.costos);

                if ( $("#codigo_costos").val() == "" )
                    $("#codigo_costos").val(cc);
                
                fillTables($("#tablaDetalles tbody > tr"),2);

                $("#busqueda").fadeOut();
                
                orden = "";
            },
            "json"
        );
        } catch (error) {
            mostrarMensaje(error.message,"mensaje_error");
        }

        return false; 
    });

    $("#btnPendientes, #btnTotales").click(function (e) { 
        e.preventDefault();

        tipoVista = e.target.id == "btnTotales" ? true:false;

        let result = {};

        $.each($("#formProceso").serializeArray(),function(){
            result[this.name] = this.value;
        });

        try {
            if (result['codigo_almacen_origen'] == '') throw "Elija el Almacen";
            if (result['codigo_almacen_destino'] == '') throw "Elija el Almacen";
            if (result['codigo_aprueba'] == '') throw "Elija la persona que aprueba";
            if (verificarCantidadesInput()) throw "Verifique las cantidades ingresadas";
            if (detalles(tipoVista,0).length == 0) throw "No hay items que procesar";

            if (accion == "n") {
                $.post(RUTA+"salida/nuevasalida", {cabecera:result,
                    detalles:JSON.stringify(detalles(tipoVista,0))},
                        function (data, textStatus, jqXHR) {
                            $("#codigo_ingreso").val(data.indice);  //no te dejes llevar por esto
                            mostrarMensaje("Nota Grabada","mensaje_correcto");
                           
                            $("#codigo_salida").val(data.indice);
                            accion = "u";

                            $(".primeraBarra").css("background","#819830");
                            $(".primeraBarra span").text('Datos Generales ... Grabado');

                            /*proceso para actualizar la tabla*/

                            
                            
                            grabado = true;

                        },
                    "json"
                );
            }else {
                $.post(RUTA+"salida/modificarsalida", {cabecera:result,
                    detalles:JSON.stringify(detalles(tipoVista,0)),
                    iddespacho:$("#codigo_salida").val()},
                        function (data, textStatus, jqXHR) {
                            //$("#codigo_ingreso").val(data.indice);
                            mostrarMensaje("Nota Grabada","mensaje_correcto");
                            
                            $("#codigo_salida").val(data.indice);

                            accion = " ";

                            $(".primeraBarra").css("background","#819830");
                            $(".primeraBarra span").text('Datos Generales ... Grabado');

                            grabado = true;
                        },
                    "json"
                );
            }

        } catch (error) {
            mostrarMensaje(error,'mensaje_error');
        }
        
        return false;
    });
    
    $("#ordenSearch").keyup(function (e) { 
        if(e.which == 13) {
            $("#esperar").fadeIn();
            
            $.post(RUTA+"salida/filtraOrden", {id:$(this).val()},
                function (data, textStatus, jqXHR) {
                    $("#ordenes tbody")
                        .empty()
                        .append(data);
                    $("#esperar").fadeOut();
                },
                "text"
            );
        }
    });

    $("#guiaRemision").click(function (e) { 
        e.preventDefault();
        
        try {
            if (tipoVista == null) throw "Grabe el documento";

            $("#vistadocumento").fadeIn();
            $("#mensaje_sunat").text("");

        } catch (error) {
            mostrarMensaje(error,'mensaje_error');
        }
         
        return false;
    });

    //filtrado en la lista de solicitante
    $(".busqueda").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $(this).next().attr("id");

        if ($(this).val() == "") {
            $(".lista").fadeOut();
        }else {
            //aignar a una variable el contenido
            let l = "#"+ $(this).next().attr("id")+ " li a"

            $(l).filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        }
    });

    $("#previewDocument").click(function (e) { 
        e.preventDefault();
        
        try {
            let result = {};

            $.each($("#guiaremision").serializeArray(),function(){
                result[this.name] = this.value;
            });

            if (result['numero_guia'] == "") throw "Ingrese el Nro. de Guia";
            if (result['codigo_entidad'] == "") throw "Seleccione la empresa de transportes";
            if (result['codigo_traslado'] == "") throw "Seleccione la modalidad de traslado";
            
            
            $.post(RUTA+"salida/vistaPreviaGuiaRemision", {cabecera:result,
                                                            detalles:JSON.stringify(detallesVista(1)),
                                                            proyecto: $("#costos").val()},
                function (data, textStatus, jqXHR) {
                        
                       if (data.archivo !== ""){
                            $(".ventanaVistaPrevia iframe")
                            .attr("src","")
                            .attr("src",data.archivo);
        
                            $("#vistaprevia").fadeIn();
                       }
                    },
                    "json"
            );
        } catch (error) {
            mostrarMensaje(error,'mensaje_error');
        }
        
        return false
    });

    $("#btnAceptItems").click(function (e) { 
        e.preventDefault();
        
        $("#busqueda").fadeOut();

        return false;
    });

    $("#saveDocument").click(function(e){
        e.preventDefault();

        let result = {};

        try {
            $.each($("#guiaremision").serializeArray(),function(){
                result[this.name] = this.value;
            });

            $.post(RUTA+"salida/GrabaGuia", {cabecera:result,
                                            detalles:JSON.stringify(detallesVista(1)),
                                            proyecto: $("#costos").val(),
                                            despacho: $("#codigo_salida").val(),
                                            operacion:accion,
                                            guia:$("#guia").val()},
                    function (data, textStatus, jqXHR) {
                        mostrarMensaje(data.mensaje,"mensaje_correcto");
                        $("#guia,#numero_guia").val(data.guia);
                    },
                    "json"
            );
        } catch (error) {
            mostrarMensaje(error,"mensaje_error");
        }

        return false;
    });

    $("#printDocument").click(function (e) { 
        e.preventDefault();
        
        try {
            let result = {};

            $.each($("#guiaremision").serializeArray(),function(){
                result[this.name] = this.value;
            });

            if (result['numero_guia'] == "") throw "Ingrese el Nro. de Guia";
            if (result['codigo_entidad'] == "") throw "Seleccione la empresa de transportes";
            if (result['codigo_traslado'] == "") throw "Seleccione la modalidad de traslado";
            
            $.post(RUTA+"salida/preImpreso", {cabecera:result,
                                              detalles:JSON.stringify(detallesVista(1)),
                                              proyecto: $("#costos").val(),
                                              despacho: $("#codigo_salida").val(),
                                              operacion:accion},
                function (data, textStatus, jqXHR) {
                        
                       if (data.archivo !== ""){
                            $("#imprimir iframe")
                            .attr("src","")
                            .attr("src",data.archivo);

                            $("#imprimir").fadeIn(function(){
                                $("#imprimir").fadeOut();
                                document.getElementById("iFramePdf").contentWindow.print();
                            })
                       }
                    },
                    "json"
            );
        } catch (error) {
            mostrarMensaje(error,'mensaje_error');
        }

        return false;
    });

    $("#closePreviewPrinter").click(function (e) { 
        e.preventDefault();
        
        $("#imprimir").fadeOut();

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

    $("#tablaDetalles tbody").on("click","a", function (e) {
        e.preventDefault();

        fila = $(this).parent().parent();
        idfila = $(this).parent().parent().data('idpedido');

        $.post(RUTA+"salida/existeObra", {id:$(this).parent().parent().data('idpedido')},
            function (data, textStatus, jqXHR) {
                if (data == 1)
                    mostrarMensaje("EL item tiene ingreso a obra","mensaje_error");
                else
                    $("#pregunta").fadeIn();       
            },
            "text"
         );
        
        return false;
    });

    $("#tablaDetalles tbody").on('blur','input', function (e) {
        try {
            let despacho = parseInt($(this).parent().parent().find("td").eq(8).children().val());
            let stock = parseInt($(this).parent().parent().find("td").eq(7).text());

            if(despacho > stock) {
                mostrarMensaje('La cantidad ingresada, es mayor al stock','mensaje_error');
                errorCantidad = true;
                return false;
            }else {
                errorCantidad = false;
            }

        } catch (error) {
            
        }
    });

    $(".cerrarLista").focus(function (e) { 
        e.preventDefault();
        
        $(".lista").fadeOut();

        return false;
    });
    
    $(".btnCallMenu").click(function (e) { 
        e.preventDefault();
        
        let callButtom = e.target.id;

        $(this).next().fadeToggle();

        return false
    });

    $(".btnCallDialog").click(function(e){
        e.preventDefault();

        controlUbigeo = e.target.id;

        $("#ubigeo").fadeIn();
        
        return false
    });

    $("#btnAceptarPregunta").click(function (e) { 
        e.preventDefault();

        $.post(RUTA+"salida/marcaItem",{id:idfila},
            function (data, textStatus, jqXHR) {
                fila.remove();
                fillTables($("#tablaDetalles tbody > tr"),2);

                $("#pregunta").fadeOut();
            },
            "text"
        );

        return false;
    });

    $("#btnCancelarPregunta").click(function (e) { 
        e.preventDefault();
        
        $("#pregunta").fadeOut();

        return false;
    });

    $(".buscaGuia").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        
        if ($(this).val() == "") {
            $(".datosEntidad").val("");
            $(".lista").fadeOut();
        }else {
            //asignar a una variable el contenido
            let l = "#"+ $(this).next().next().attr("id")+ " li a"

            $(l).filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        }
    });

    $("#guiaSunat").click(function(e){
        e.preventDefault();

        let result = {};

        $.each($("#guiaremision").serializeArray(),function(){
            result[this.name] = this.value;
        });

        try {
            if ( result['ftraslado'] == "") throw new Error("Indique la fecha de traslado");
            if ( result['ubigeo_origen_guia'] == "") throw new Error("Ingrese el ubigeo origen");
            if ( result['ubigeo_destino_guia'] == "") throw new Error("Ingrese el ubigeo destino");

            if ( result['codigo_transporte'] == "" ) throw new Error("Indique el tipo de transporte");
            if ( result['codigo_modalidad'] == "" ) throw new Error("Indique la modalidad de traslado");
            
            if ( result['peso'] == "") throw new Error("Ingrese el peso");

            if ( result['codigo_transporte'] == 257 && result['nombre_conductor'] == "") throw new Error("Registre el nombre del conductor");
            if ( result['codigo_transporte'] == 257 && result['licencia_conducir'] == "") throw new Error("Registre la licencia del conductor");
            if ( result['codigo_transporte'] == 257 && result['placa'] == "") throw new Error("Registre la placa del vehículo");

            if ( result['codigo_transporte'] == 258 && result['empresa_transporte_razon'] == "") throw new Error("Registre el nombre de la empresa de transportes");
            if ( result['codigo_transporte'] == 258 && result['direccion_proveedor'] == "") throw new Error("Registre la direccion del transportista");
            if ( result['codigo_transporte'] == 258 && result['ruc_proveedor'] == "") throw new Error("Registre el RUC del transportista");

            let formdata = new FormData();
                formdata.append("guia_interna",result['numero_guia']);
                formdata.append("peso",result['peso']);

            if ( $("#numero_guia_sunat" ).val() === "" ) {
                fetch(RUTA+"salida/numeroSunat",{
                    method:'POST',
                    body:formdata
                })
                .then(response => response.text())
                .then(data =>{
                    $("#numero_guia_sunat").val(data);
                    $("#aviso").fadeIn();
                });
            }else{
                $("#aviso").fadeIn();
            }
        } catch (error) {
            mostrarMensaje(error.message,"mensaje_error");
        }

        return false;
    });

    $("#btnAceptarAdvierte").click(function(e){
        e.preventDefault();

        let result = {};

        $.each($("#guiaremision").serializeArray(),function(){
            result[this.name] = this.value;
        });

        let datosJSON = new FormData();

        datosJSON.append("cabecera",JSON.stringify(result));
        datosJSON.append("detalles",JSON.stringify(detallesVista(1)));

        $.ajax({
            type: "POST",
            url: RUTA+"salida/guiaSunat",
            data: datosJSON,
            dataType: "json",
            contentType:false,      
            processData:false,
            success: function (data) {
                if (data.respuesta == 0){
                    mostrarMensaje("Comprobante aceptado","mensaje_correcto");
                }else{
                    mostrarMensaje("Comprobante no aceptado","mensaje_error");
                }

                $("#mensaje_sunat").text(data.mensaje);
                $("#aviso").fadeOut();
            }
        });
        
        return false;
    });

    $("#btnCancelarAdvierte").click(function(e){
        e.preventDefault();

        $("#aviso").fadeOut();

        return false;
    });

    $("#dpto").change(function(e){
        e.preventDefault();

        $("#prov").empty();
        $("#dist").empty();

        $.post(RUTA+"salida/ubigeoGuias", {nivel:2,prefijo:$("#dpto").val()},
            function (data, textStatus, jqXHR) {
               data.datos.forEach(element => {
                    row = `<option value="${element.ccubigeo}">${element.cdubigeo}</option>`;
                    $("#prov").append(row);
               });  
            },
            "json"
        );

        return false;
    });

    $("#prov").change(function(e){
        e.preventDefault();

        $("#dist").empty();

        $.post(RUTA+"salida/ubigeoGuias", {nivel:3,prefijo:$("#prov").val()},
            function (data, textStatus, jqXHR) {
               data.datos.forEach(element => {
                    row = `<option value="${element.ccubigeo}">${element.cdubigeo}</option>`;
                    $("#dist").append(row);
               });  
            },
            "json"
        );

        return false;
    });

    $("#dist").change(function(e){
        e.preventDefault();

        ubigeo = e.target.value;

        return false;
    });

    $("#btnCancelarUbigeo").click(function(e){
        e.preventDefault();

        $("#ubigeo").fadeOut();

        return false;
    });

    $("#btnAceptarUbigeo").click(function(e){
        e.preventDefault();

        if ( controlUbigeo == "ubigeoBtnOrigen"){
            $("#ubigeo_origen_guia,#ubig_origen").val(ubigeo);
        }else{
            $("#ubigeo_destino_guia,#ubig_destino").val(ubigeo);
        }

        $("#dist,#prov").empty();
        $("#ubigeo").fadeOut();

        return false;
    });
})


verificarCantidadesInput = () =>{
    let TABLA = $("#tablaDetalles tbody >tr"),
        errorCantidad = false;

    TABLA.each(function(){
        let cantidad    = parseInt($(this).find("td").eq(7).text()),// cantidad
            cantdesp    = parseInt($(this).find('td').eq(8).children().val());

        if( cantidad < cantdesp) {
            errorCantidad = true
        }       
    })

    return errorCantidad;
}

detalles = (flag,sw) =>{
    DETALLES = [];

    let TABLA = $("#tablaDetalles tbody >tr");

    TABLA.each(function(){
        let ITEM        = $(this).find('td').eq(1).text(),
            IDDETORDEN  = $(this).data("detorden"),
            IDDETPED    = $(this).data("iddetped"),
            ESTADO      = $(this).data("estado"),
            IDPROD      = $(this).data("idprod"),
            PEDIDO      = $(this).find('td').eq(11).text(),
            ORDEN       = $(this).find('td').eq(12).text(),
            INGRESO     = $(this).data("ingreso"),
            ALMACEN     = $("#codigo_almacen_origen").val(),
            CANTIDAD    = $(this).find('td').eq(6).text(),// cantidad
            CANTDESP    = $(this).find('td').eq(8).children().val(),
            OBSER       = $(this).find('td').eq(10).children().val(),
            CODIGO      = $(this).find('td').eq(3).text(),//codigo
            DESCRIPCION = $(this).find('td').eq(4).text(),//descripcion
            UNIDAD      = $(this).find('td').eq(5).text(),//unidad
            DESTINO     = $("#codigo_almacen_destino").val(),
            CHECKED     = $(this).find('td').eq(1).children().prop("checked");//codigo
    
        let item = {};

        if ( CHECKED == flag && ESTADO == sw ) {
            item['item']         = ITEM;
            item['iddetorden']   = IDDETORDEN;
            item['iddetped']     = IDDETPED;
            item['iddespacho']   = null;
            item['idprod']       = IDPROD;
            item['pedido']       = ORDEN;
            item['orden']        = PEDIDO;
            item['ingreso']      = INGRESO;
            item['almacen']      = ALMACEN;
            item['cantidad']     = CANTIDAD;
            item['cantdesp']     = CANTDESP;
            item['obser']        = OBSER;
            item['codigo']       = CODIGO;
            item['descripcion']  = DESCRIPCION;
            item['unidad']       = UNIDAD;
            item['destino']      = DESTINO;

            $(this).attr('data-estado',1);
            
            DETALLES.push(item);
        }
    })

    return DETALLES; 
}

detallesVista = (sw) =>{
    DETALLES = [];

    let TABLA = $("#tablaDetalles tbody >tr");

    TABLA.each(function(){
        let STATUS  = $(this).attr("data-estado");
        let item = {};

        if ( STATUS == 1 ) {
            item['item']         = $(this).find('td').eq(1).text();
            item['iddetorden']   = $(this).data("detorden");
            item['iddetped']     = $(this).data("iddetped");
            item['iddespacho']   = null;
            item['idprod']       = $(this).data("idprod");
            item['pedido']       = $(this).find('td').eq(11).text();
            item['orden']        = $(this).find('td').eq(12).text();
            item['ingreso']      = $(this).data("ingreso");
            item['almacen']      = $("#codigo_almacen_origen").val();
            item['cantidad']     = $(this).find('td').eq(6).text();
            item['cantdesp']     = $(this).find('td').eq(8).children().val();
            item['obser']        = $(this).find('td').eq(10).children().val();
            item['codigo']       = $(this).find('td').eq(3).text();
            item['descripcion']  = $(this).find('td').eq(4).text();
            item['unidad']       = $(this).find('td').eq(5).text();
            item['destino']      = $("#codigo_almacen_destino").val();
            item['estado']       = $(this).data("estado");

            
            DETALLES.push(item);
        }
    })

    return DETALLES; 
}

buscarOrden = (orden) => {
    existe = false;

    let TABLA = $("#tablaDetalles tbody >tr");

    TABLA.each(function(){
        ingresada  = $(this).find('td').eq(12).text();

        if ( ingresada == orden ) {
            existe = true;
        }
    })

    return existe;
}


