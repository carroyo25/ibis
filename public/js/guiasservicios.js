$(function() {
    let accion = "",
        tipoVista = null,
        cc = "",
        fila = "",
        grabado = 1;
        

    $("#esperar").fadeOut();

    $("#tablaPrincipal tbody").on("click","tr", function (e) {
        e.preventDefault();

        $.post(RUTA+"guiasservicios/guiaServiciosId",{indice:$(this).data("indice"),guia:$(this).data('guia')},
            function (data, textStatus, jqXHR) {
                //console.log(data);
                $("#fecha").val(data.cabecera[0].fechadocumento);
                $("#codigo_costos").val(data.cabecera[0].codproy);
                $("#codigo_aprueba").val(data.cabecera[0].iduser);
                $("#codigo_almacen_origen").val(data.cabecera[0].id_origen);
                $("#codigo_almacen_destino").val(data.cabecera[0].id_destino);
                $("#guia").val(data.cabecera[0].cnumguia);
                $("#aprueba").val(data.cabecera[0].cnombres);
                $("#almacen_origen_despacho").val(data.cabecera[0].razon_origen);
                $("#almacen_destino_despacho").val(data.cabecera[0].razon_destino);
                $("#movimiento").val();
                $("#estado").val(data.cabecera[0].estado);
                $("#tipo").val(data.cabecera[0].tipo);
                $("#costos").val(data.cabecera[0].cdesproy);
                $("#numero").val(data.cabecera[0].cnumguia);


                $("#serie_guia").val('F001');
                $("#numero_guia").val(data.cabecera[0].cnumguia);
                $("#fgemision").val(data.cabecera[0].fechadocumento);
                $("#ftraslado").val(data.cabecera[0].ftraslado);

                $("#destinatario_ruc").val(data.cabecera[0].ruc_destino);
                $("#destinatario_razon").val(data.cabecera[0].razon_destino);
                $("#destinatario_direccion").val(data.cabecera[0].direccion_destino);

                $("#almacen_origen").val(data.cabecera[0].razon_origen);
                $("#almacen_origen_direccion").val(data.cabecera[0].direccion_origen);

                $("#almacen_destino").val(data.cabecera[0].razon_destino);
                $("#almacen_destino_direccion").val(data.cabecera[0].direccion_destino);

                $("#empresa_transporte_razon").val(data.cabecera[0].centi);
                $("#direccion_proveedor").val(data.cabecera[0].centidir);
                $("#ruc_proveedor").val(data.cabecera[0].centiruc);

                $("#modalidad_traslado").val(data.cabecera[0].ctraslado);
                $("#tipo_envio").val(data.cabecera[0].cenvio);
                $("#autoriza").val(data.cabecera[0].cautoriza);
                $("#destinatario").val(data.cabecera[0].cdestinatario);
                $("#observaciones").val(data.cabecera[0].cobserva);
                $("#nombre_conductor").val(data.cabecera[0].cnombre);
                $("#licencia_conducir").val(data.cabecera[0].clicencia);
                $("#conductor_dni").val(data.cabecera[0].clicencia);
                $("#marca").val(data.cabecera[0].cmarca);
                $("#placa").val(data.cabecera[0].cplaca);
                $("#peso").val(data.cabecera[0].nPeso);
                $("#bultos").val(data.cabecera[0].nBultos);

                $("#tablaDetalles tbody")
                    .empty()
                    .append(data.detalles);

                $("#proceso").fadeIn();

                grabado = 1;
                accion = "u"
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

        $(".primeraBarra").css("background","#0078D4");
        $(".primeraBarra span").text("Datos Generales");

        /*$.post(RUTA+"salida/actualizaDespachos",
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
        );*/

        return false;
    });

    $(".mostrarLista").focus(function (e) { 
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
            $("#codigo_origen_sunat").val($(this).data('sunat'));
        }else if(contenedor_padre == "listaDestino"){
            $("#codigo_almacen_destino").val(codigo);
            $("#codigo_destino_sunat").val($(this).data('sunat'));
            $("#destinatario_ruc").val($(this).data('ruc'));
            $("#destinatario_razon").val($(this).text());
            $("#destinatario_direccion").val($(this).data('direccion'));
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
        }else if(contenedor_padre == "listaCostos"){
            $("#codigo_costos").val(codigo);
        }else if(contenedor_padre == "listaOrigenGuia"){
            $("#almacen_origen").val($(this).text());
            $("#almacen_origen_direccion").val($(this).data('direccion'));
        }else if(contenedor_padre == "listaDestinoGuia"){
            $("#almacen_destino").val($(this).text());
            $("#almacen_destino_direccion").val($(this).data('direccion'));
        }

        return false;
    });

    $("#addItem").click(function(e){
        e.preventDefault();

        try {
            //if ($("#codigo_orden").val() == "") throw new Error("Debe seleccionar una orden de servicio");

            $.post(RUTA+"pedidos/llamaProductos", {tipo:37},
            function (data, textStatus, jqXHR) {
                    $("#tablaModulos tbody")
                        .empty()
                        .append(data);
    
                    $("#busqueda").fadeIn();
                },
                "text"
            );
           
        } catch (error) {
            mostrarMensaje(error.message,"mensaje_error");
        }

        return false;
    })

    $("#addOrder").click(function(e){
        e.preventDefault();

        $("#busquedaOrden").fadeIn();

        return false;
    })

    $("#addRequest").click(function(e){
        e.preventDefault();

        $("#busquedaPedido").fadeIn();

        return false;
    })

    $("#tablaDetalles tbody").on("click","a", function (e) {
        e.preventDefault();

        if( $(this).attr("href") === 'delete' ){
            e.target.closest("tr").remove();
            fillTables($("#tablaDetalles tbody > tr"),0);
        }

        return false;
    });

    $(".tituloVentana").on("click","a", function (e) {
        e.preventDefault();

        $(this).parent().parent().parent().parent().fadeOut();

        return false;
    });

    //filtrar Item del pedido
    $("#txtBuscarCodigo, #txtBuscarDescrip").on("keypress", function (e) {
        if(e.which == 13) {
            $("#esperar").fadeIn();
            
            $.post(RUTA+"pedidos/filtraItems", {codigo:$("#txtBuscarCodigo").val(),
                                                descripcion:$("#txtBuscarDescrip").val(),
                                                tipo:37},
                    function (data, textStatus, jqXHR) {
                        $("#tablaModulos tbody")
                            .empty()
                            .append(data);
                        $("#esperar").fadeOut();
                    },
                    "text"
                );
        }
    });

    $("#tablaModulos tbody").on("click","tr", function (e) {
        e.preventDefault();

        let nFilas = $.strPad($("#tablaDetalles tr").length,3);
        let idprod = $(this).data("idprod");
        let nunid = $(this).data("ncomed");
        let codigo = $(this).children('td:eq(0)').text();
        let descrip = $(this).children('td:eq(1)').text();
        let unidad = $(this).children('td:eq(2)').text();
        let grabado = 0;
        let tabPos  = $("#tablaDetalles tr").length;

        let row = `<tr data-grabado="${grabado}" data-idprod="${idprod}" data-codund="${nunid}" data-idx="-">
                    <td></td>
                    <td class="textoCentro"><a href="delete"><i class="fas fa-eraser"></i></a></td>
                    <td class="textoCentro">${nFilas}</td>
                    <td class="textoCentro">${codigo}</td>
                    <td class="pl20px">${descrip}</td>
                    <td class="textoCentro">${unidad}</td>
                    <td><input type="number" 
                                step="any" 
                                placeholder="0.00" 
                                onchange="(function(el){el.value=parseFloat(el.value).toFixed(2);})(this)"
                                tabIndex="${tabPos}">
                    </td>
                    <td><input type="text"></td>
                    <td><input type="date"></td>
                    <td><input type="text"></td>
                    <td class="textoCentro"></td>
                    <td class="textoCentro"></td>
                </tr>`;

        $("#tablaDetalles tbody").append(row);

        return false;
    });

    $("#guiaRemision").click(function (e) { 
        e.preventDefault();
        
        try {
            $("#vistadocumento").fadeIn();

        } catch (error) {
            mostrarMensaje(error,'mensaje_error');
        }
         
        return false;
    });

    $(".tituloDocumento").on("click","#closeDocument", function (e) {
        e.preventDefault();

        $(this).parent().parent().parent().parent().parent().fadeOut();

        return false;
    });

    $("#saveDocument").click(function(e){
        e.preventDefault();

        let guia = {},
            form = {};

        $.each($("#guiaremision").serializeArray(),function(){
            guia[this.name] = this.value;
        });

        $.each($("#formProceso").serializeArray(),function(){
            form[this.name] = this.value;
        });

        if (accion == "n")
        
            $.post(RUTA+"guiasservicios/grabaGuiaServicio",{guiaCab:guia,
                                                    formCab:form,
                                                    detalles:JSON.stringify(detalles(false)),
                                                    operacion:accion
                                                },
                function (data, textStatus, jqXHR) {
                    mostrarMensaje(data.mensaje,"mensaje_correcto");
                    $("#guia,#numero_guia").val(data.guia);

                    $(".primeraBarra").css("background","#819830");
                    $(".primeraBarra span").text('Datos Generales ... Grabado');

                    accion = "x";
                    grabado = 0;
                },
                "json"
            );
        
            else if (accion == "u") {
                mostrarMensaje("Actualizar datos","mensaje_correcto");
            }

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

    $(".btnCallMenu").click(function (e) { 
        e.preventDefault();
        
        let callButtom = e.target.id;

        $(this).next().fadeToggle();

        return false
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
            
            $.post(RUTA+"guiasservicios/vistaPreviaGuia", {cabecera:result,
                                                            detalles:JSON.stringify(detalles(true)),
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

    $("#closePreview").click(function (e) { 
        e.preventDefault();

        $(".ventanaVistaPrevia iframe").attr("src","");
        $("#vistaprevia").fadeOut();

        return false;
    });

    //filtrado en la lista de solicitante
    $(".busqueda").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $(this).next().attr("id");

        //aignar a una variable el contenido
        let l = "#"+ $(this).next().attr("id")+ " li a"

        $(l).filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });

    $("#btnConsulta").click(function (e) { 
        e.preventDefault();

        $("#esperar").css({"display":"block","opacity":"1"});
        
        $.post(RUTA+"guiamanual/listaFiltrada", {guia:$("#ordenSearch").val(),costos:$("#costosSearch").val(),anio:$("#anioSearch").val()},
            function (data, textStatus, jqXHR) {
                $("#tablaPrincipal tbody").empty().append(data);

                $("#esperar").css({"display":"none","opacity":"0"});
            },
            "text"
        );

        return false;
    });

    $("#ordenSearch").keyup(function (e) { 
        if(e.which == 13) {
            $("#esperar").fadeIn();
            
            $.post(RUTA+"guiasservicios/filtraOrden", {id:$(this).val()},
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

    $("#requestSearch").keyup(function (e) { 
        if(e.which == 13) {
            $("#esperar").fadeIn();
            
            $.post(RUTA+"guiasservicios/filtraPedido", {id:$(this).val(),costos:$("#codigo_costos").val()},
                function (data, textStatus, jqXHR) {
                    $("#pedidos tbody")
                        .empty()
                        .append(data);
                    $("#esperar").fadeOut();
                },
                "text"
            );
        }
    });

    $("#ordenes tbody").on("click","tr", function (e) {
        e.preventDefault();

        $("#tipo").val("ORDEN DE SERVICIO");
        $("#codigo_movimiento").val(89);

        $.post(RUTA+"guiasservicios/ordenId",{id:$(this).data("orden")},
            function (data, textStatus, jqXHR) {
                    suma = 0;

                    $("#codigo_costos").val(data.cabecera[0].ncodcos);
                    $("#codigo_area").val(data.cabecera[0].ncodarea);
                    $("#codigo_orden").val(data.cabecera[0].id_regmov);
                    $("#codigo_pedido").val(data.cabecera[0].id_refpedi);
                    $("#codigo_estado").val(data.cabecera[0].nEstadoDoc);
                    $("#codigo_entidad").val(data.cabecera[0].id_centi);
                    $("#proyecto").val(data.cabecera[0].costos);
                    $("#area").val(data.cabecera[0].area);
                    $("#solicita").val(data.cabecera[0].solicita);
                    $("#orden").val(data.cabecera[0].cnumero);
                    $("#pedido").val(data.cabecera[0].pedido);
                    $("#ruc").val(data.cabecera[0].cnumdoc);
                    $("#razon").val(data.cabecera[0].crazonsoc);
                    $("#concepto").val(data.cabecera[0].concepto);
                    $("#detalle").val(data.cabecera[0].detalle);
                    $("#almacen").val(data.cabecera[0].cdesalm);
                    $("#codigo_almacen").val(data.cabecera[0].ncodalm);
                    $("#numero").val(data.numero.numero);
                    $("#movimiento").val(data.numero.movimiento);
                
                    $("#tablaDetalles tbody")
                        .empty()
                        .append(data.detalles);

                    $("#items").val($("#tablaDetalles tbody tr").length);

                $("#busqueda").fadeOut();
            },
            "json"
        );
        return false
    });

    $("#btnAceptOrders").click(function (e) { 
        e.preventDefault();
        
        $("#busquedaOrden").fadeOut();

        return false;
    });

    $("#pedidos tbody").on('click','tr', function(e) {
        e.preventDefault();

        $("#tipo").val("PEDIDO DE SERVICIO");
        $("#codigo_movimiento").val(89);

        $.post(RUTA+"guiasservicios/detallesPedido",{id:$(this).data("orden")},
            function (data, text, requestXHR) {
                $("#tablaDetalles tbody")
                .empty()
                .append(data);

                $("#items").val($("#tablaDetalles tbody tr").length);

            },
            "text"
        );

        $("#busquedaPedido").fadeOut();

        return false;
    });
})


detalles = (sw) =>{
    DETALLES = [];

    let TABLA = $("#tablaDetalles tbody >tr");

    TABLA.each(function(){
        let STATUS  = $(this).attr("data-grabado");
        let item = {};

        if ( STATUS == sw ) {
            item['item']         = $(this).find('td').eq(2).text();
            item['iddetorden']   = null;
            item['iddetped']     = null;
            item['iddespacho']   = null;
            item['idprod']       = $(this).data("idprod");
            item['pedido']       = null;
            item['orden']        = null;
            item['ingreso']      = null
            item['almacen']      = $("#codigo_almacen_origen").val();
            item['cantidad']     = null;
            item['cantdesp']     = $(this).find('td').eq(6).children().val();
            item['obser']        = $(this).find('td').eq(7).children().val();
            item['codigo']       = $(this).find('td').eq(3).text();
            item['descripcion']  = $(this).find('td').eq(4).text();
            item['unidad']       = $(this).find('td').eq(5).text();
            item['vence']        = $(this).find('td').eq(8).children().val();
            item['serie']        = $(this).find('td').eq(9).children().val();
            item['destino']      = $("#codigo_almacen_destino").val();
            item['estado']       = $(this).data("estado");


            $(this).attr('data-grabado',1);
            
            DETALLES.push(item);
        }
    })

    return DETALLES; 
}