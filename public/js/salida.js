$(function() {
    let accion = "",
        tipoVista = null,
        cc = "",
        fila = "",
        idfila = "",
        ordenes = [],
        sw=0;

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

    $("#importData").click(function (e) { 
        e.preventDefault();
        
        if ( accion == 'n') {
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
                
        }else{
            mostrarMensaje('El despacho se esta procesando','mensaje_error');
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
        }else if(contenedor_padre == "listaDestino"){
            $("#codigo_almacen_destino").val(codigo);
            $("#almacen_destino").val($(this).text());
            $("#almacen_destino_direccion").val($(this).data('direccion'));
            $("#codigo_destino_sunat").val($(this).data('sunat'));
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

            if (tipoVista == null) throw "Por favor grabar el documento";

            $.post(RUTA+"salida/documentoPdf", {cabecera:result,
                                                detalles:JSON.stringify(detalles(tipoVista)),
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

        try {
            if ( $("#codigo_costos").val() != cc && $("#codigo_costos").val() != "" ) throw "Los orden es de otro centro de costos";
            if (buscarOrden($(this).data("orden"))) throw "La orden se esta duplicada";

            $.post(RUTA+"salida/ordenId", {id:$(this).data("orden"),costo:$(this).data("idcosto")},
            function (data, textStatus, jqXHR) {
                $("#numero").val(data.numero);
                $("#tablaDetalles tbody").append(data.items);
                $("#costos").val(data.costos);

                if ( $("#codigo_costos").val() == "" )
                    $("#codigo_costos").val(cc);
                
                fillTables($("#tablaDetalles tbody > tr"),2);

                $("#busqueda").fadeOut();
            },
            "json"
        );
        } catch (error) {
            mostrarMensaje(error,"mensaje_error");
        }

        return false; 
    });

    $("#btnPendientes, #btnTotales").click(function (e) { 
        e.preventDefault();

        tipoVista = e.target.id == "btnTotales"?true:false;

        let result = {};

        $.each($("#formProceso").serializeArray(),function(){
            result[this.name] = this.value;
        });

        try {
            if (result['codigo_almacen_origen'] == '') throw "Elija el Almacen";
            if (result['codigo_almacen_destino'] == '') throw "Elija el Almacen";
            if (result['codigo_aprueba'] == '') throw "Elija la persona que aprueba";
            if (verificarCantidadesInput()) throw "Verifque las cantidades ingresadas";

            if (accion == "n") {
                $.post(RUTA+"salida/nuevasalida", {cabecera:result,
                    detalles:JSON.stringify(detalles(tipoVista))},
                        function (data, textStatus, jqXHR) {
                            $("#codigo_ingreso").val(data.indice);
                            mostrarMensaje("Nota Grabada","mensaje_correcto");
                           
                            $("#codigo_salida").val(data.indice);
                            accion = "u";
                        },
                    "json"
                );
            }else {
                $.post(RUTA+"salida/modificarsalida", {cabecera:result,
                    detalles:JSON.stringify(detalles(true))},
                        function (data, textStatus, jqXHR) {
                            $("#codigo_ingreso").val(data.indice);
                            mostrarMensaje("Nota Grabada","mensaje_correcto");
                            
                            $("#codigo_salida").val(data.indice);
                            accion = "u";
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
                                                            detalles:JSON.stringify(detalles(tipoVista)),
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

        $.each($("#guiaremision").serializeArray(),function(){
            result[this.name] = this.value;
        });

        $.post(RUTA+"salida/GrabaGuia", {cabecera:result,
                                        detalles:JSON.stringify(detalles(tipoVista)),
                                        proyecto: $("#costos").val(),
                                        despacho: $("#codigo_salida").val(),
                                        operacion:accion,
                                        guia:$("#guia").val()},
                function (data, textStatus, jqXHR) {
                    mostrarMensaje(data.mensaje,"mensaje_correcto");
                    $("#guia").val(data.guia);
                },
                "json"
            );

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
                                              detalles:JSON.stringify(detalles(tipoVista)),
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
        
        let datosJSON = new FormData();

        datosJSON.append("cabecera",JSON.stringify(result));
        datosJSON.append("detalles",JSON.stringify(detalles(tipoVista)));

        $.ajax({
            type: "POST",
            url: RUTA+"salida/guiaSunat",
            data: datosJSON,
            dataType: "json",
            contentType:false,      
            processData:false,
            success: function (data) {
                console.log(data);
            }
        });

        //console.log(result);

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

detalles = (flag) =>{
    DETALLES = [];

    let TABLA = $("#tablaDetalles tbody >tr");
    
    TABLA.each(function(){
        let ITEM        = $(this).find('td').eq(1).text(),
            IDDETORDEN  = $(this).data("detorden"),
            IDDETPED    = $(this).data("iddetped"),
            IDPROD      = $(this).data("idprod"),
            IDDESPACHO  = $(this).data("iddespacho"),
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
    
        item = {};

        if (CHECKED == flag) {
            item['item']         = ITEM;
            item['iddetorden']   = IDDETORDEN;
            item['iddetped']     = IDDETPED;
            item['idprod']       = IDPROD;
            item['pedido']       = ORDEN;
            item['orden']        = PEDIDO;
            item['ingreso']      = INGRESO;
            item['almacen']      = ALMACEN;
            item['cantidad']     = CANTIDAD;
            item['cantdesp']     = CANTDESP;
            item['obser']        = OBSER;
            item['iddespacho']   = IDDESPACHO;

            item['codigo']       = CODIGO;
            item['descripcion']  = DESCRIPCION;
            item['unidad']       = UNIDAD;
            item['destino']      = DESTINO;
            
            DETALLES.push(item);
        }
    })

    return DETALLES; 
}

buscarOrden = (orden) => {
    existe = false;

    let TABLA = $("#tablaDetalles tbody >tr");

    TABLA.each(function(){
            ingresada    = $(this).find('td').eq(12).text()

        if (ingresada == orden) {
            existe = true;
        }
    })

    return existe;
}


