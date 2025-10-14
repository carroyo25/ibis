$(() =>{
    $("#esperar").fadeOut();
    
    const body = document.querySelector("#tablaPrincipal tbody");
    
    //para hacer cmabiar el foco de los inputs en las tablas
     // Delegación de eventos para inputs dinámicos
    $('#tablaDetalles').on('keydown', '.input-tabla', function(e) {
        if (e.which === 13) { // Tecla Enter
        e.preventDefault();
        const $td = $(this).closest('td');
        const columnIndex = $td.index();
        const $nextRow = $td.closest('tr').next();
        
            if ($nextRow.length) {
                $nextRow.find('td').eq(columnIndex).find('input').focus().select();
            }
        }
    });
        
    $("#esperar").fadeOut();

    $("#nuevoRegistro").click(function (e) { 
        e.preventDefault();

        $("#proceso").fadeIn();
        accion = 'n';
        document.getElementById("formProceso").reset();
        document.getElementById("guiaremision").reset();
        $("#tablaDetalles tbody").empty();

        return false;
    });

    $("#closeProcess").click(function (e) { 
        e.preventDefault();

        $("#proceso").fadeOut();

        return false;
    });

    $(".mostrarLista").focus(function (e) { 
        e.preventDefault();

        if (accion !="n") {
            return false;
        }
        
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
            $("#codigo_origen").val(codigo);
            $("#almacen_origen").val($(this).text());
            $("#almacen_origen_direccion").val($(this).data('direccion'));
            $("#codigo_origen_sunat").val($(this).data('sunat'));
            $("#ruc_entidad_origen").val($(this).data('ruc'));
            $("#nombre_entidad_origen").val($(this).data('razon'));
            $("#ubigeo_origen_guia,#ubig_origen").val($(this).data('ubigeo'));
            $("#cso").val($(this).data('sunat'));
        }else if(contenedor_padre == "listaDestino"){
            $("#almacen_destino").val($(this).text());
            $("#almacen_destino_direccion").val($(this).data('direccion'));
            $("#codigo_destino_sunat").val($(this).data('sunat'));
            $("#ubigeo_destino_guia,#ubig_destino").val($(this).data('ubigeo'));
            $("#ruc_entidad_destino").val($(this).data('ruc'));
            $("#nombre_entidad_destino").val($(this).data('razon'));
            $("#csd").val($(this).data('sunat'));
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
        }else if(contenedor_padre == "listaOrigenCabecera"){
            $("#codigo_almacen_origen").val(codigo);
        }else if(contenedor_padre == "listaDestinoCabecera"){
            $("#codigo_almacen_destino").val(codigo);
        }else if(contenedor_padre == "listaCostosDestinoCabecera"){
            $("#codigo_costos_origen").val(codigo);
        }

        return false;
    });

    $("#importData").click(function (e) { 
        e.preventDefault();

        try {
            if ($("#codigo_aprueba").val() == 0) throw "Elija la persona que aprueba";

            $("#esperar").fadeIn();

            $.post(RUTA+"madres/guias", {cc:$("#codigo_costos_origen").val(),guia:""},
                function (data, textStatus, jqXHR) {
                    $("#tablaGuias tbody")
                        .empty()
                        .append(data);

                        $("#guias").fadeIn();
                        $("#esperar").fadeOut();
                },
                "text"
            );
        } catch (error) {
            mostrarMensaje(error,"mensaje_error");
        }
        
        return false
    });

    $(".tituloVentana").on("click","a", function (e) {
        e.preventDefault();

        $(this).parent().parent().parent().parent().fadeOut();

        return false;
    });

    $("#txtBuscarGuia").keyup(function (e) { 
        if(e.which == 13) {
            $("#esperar").fadeIn();
            
            try {
                if ($("#codigo_aprueba").val() == 0 ) throw "Elija la persona que aprueba";
                //if ($("#codigo_costos_destino").val() == 0 ) throw "Indique el centro de costos"; 
    
                $("#esperar").fadeIn();
    
                $.post(RUTA+"madres/guias", {cc:$("#codigo_costos_destino").val(),guia:$(this).val()},
                    function (data, textStatus, jqXHR) {
                        $("#tablaGuias tbody")
                            .empty()
                            .append(data);
    
                            $("#guias").fadeIn();
                            $("#esperar").fadeOut();
                    },
                    "text"
                );
            } catch (error) {
                mostrarMensaje(error,"mensaje_error");
            }
        }
    });

    $("#tablaGuias tbody").on("click","tr", function (e) {
        e.preventDefault();
        
        $("#esperar").fadeIn();
        
        $(this).remove();


        $.post(RUTA+"madres/itemsDespacho",{idx:$(this).data("despacho")},
            function (data, textStatus, jqXHR) {
                $("#tablaDetalles tbody").append(data);

                $("#esperar").fadeOut();
            },
            "text"
        );

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

    $(".btnCallMenu").click(function (e) { 
        e.preventDefault();
        
        let callButtom = e.target.id;

        $(this).next().fadeToggle();

        return false
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

    $("#previewDocument").click(function (e) { 
        e.preventDefault();
        
        try {
            let result = {};

            $.each($("#guiaremision").serializeArray(),function(){
                result[this.name] = this.value;
            });

            if (result['numero_guia'] == "") throw "Ingrese el Nro. de Guia";
            //if (result['codigo_entidad'] == "") throw "Seleccione la empresa de transportes";
            //if (result['codigo_traslado'] == "") throw "Seleccione la modalidad de traslado";
            
            
            $.post(RUTA+"salida/vistaPreviaGuiaRemision", {cabecera:result,
                                                            detalles:JSON.stringify(detalles()),
                                                            proyecto: $("#corigen").val()},
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

        if (accion == "n") {
            $.post(RUTA+"madres/grabaGuiaMadre",{guiaCab:guia,
                                                formCab:form,
                                                detalles:JSON.stringify(detalles(false)),
                                                operacion:"n"
                                            },
                function (data, textStatus, jqXHR) {
                    mostrarMensaje(data.mensaje,"mensaje_correcto");
                    $("#guia,#numero_guia").val(data.guia);

                    $(".primeraBarra").css("background","#819830");
                    $(".primeraBarra span").text('Datos Generales ... Grabado');

                    
                    accion = "u";
                    grabado = 0;
                },
                "json"
            );
        }

        return false;
    });

    $("#tablaPrincipal tbody").on("click","tr", function (e) {
        e.preventDefault();

        $.post(RUTA+"madres/guiasRemision", {id:$(this).data("indice")},
            function (data, text, requestXHR) {

                $("#fecha").val(data.cabecera[0].emision);
                $("#numero").val(data.cabecera[0].cnumguia);

                $("#aprueba").val(data.cabecera[0].autoriza);
                $("#almacen_origen_despacho").val(data.cabecera[0].origen);
                $("#almacen_destino_despacho").val(data.cabecera[0].destino);
                $("#tipo").val(data.cabecera[0].cenvio);
                $("#tipo_envio").val(data.cabecera[0].cenvio);

                $("#numero_guia").val(data.cabecera[0].cnumguia);
                $("#numero_guia_sunat").val(data.cabecera[0].guiasunat); 
                $("#fgemision").val(data.cabecera[0].emision);
                $("#ftraslado").val(data.cabecera[0].traslado);
                $("#ticket_sunat").val(data.cabecera[0].ticketsunat);
                $("#almacen_origen").val(data.cabecera[0].origen);
                $("#almacen_origen_direccion").val(data.cabecera[0].cdirorigen);
                $("#almacen_destino").val(data.cabecera[0].destino);
                $("#almacen_destino_direccion").val(data.cabecera[0].cdirdest);
                $("#empresa_transporte_razon").val(data.cabecera[0].nombre_proveedor);
                $("#direccion_proveedor").val(data.cabecera[0].direccion_proveedor);
                $("#ruc_proveedor").val(data.cabecera[0].ruc_proveedor);
                $("#modalidad_traslado").val(data.cabecera[0].cenvio);
                $("#tipo_envio").val(data.cabecera[0].tipo_envio);
                $("#autoriza").val(data.cabecera[0].autoriza);
                $("#destinatario").val(data.cabecera[0].recibe);
                $("#observaciones").val(data.cabecera[0].cobserva);
                $("#nombre_conductor").val(data.cabecera[0].cConductor);
                $("#licencia_conducir").val(data.cabecera[0].clincencia);
                $("#conductor_dni").val(data.cabecera[0].ndni);
                $("#marca").val(data.cabecera[0].cmarca);
                $("#placa").val(data.cabecera[0].cplaca);
                $("#peso").val(data.cabecera[0].nPeso);
                $("#bultos").val(data.cabecera[0].nBultos);
                $("#observaciones").val(data.cabecera[0].cobserva);
                $("#corigen").val(data.cabecera[0].proyecto);

                $("#ubig_origen").val(data.cabecera[0].ubigeo_origen);
                $("#ubig_destino").val(data.cabecera[0].ubigeo_destino);

                $("#cso").val(data.cabecera[0].codigo_sunat_origen);
                $("#csd").val(data.cabecera[0].codigo_sunat_destino);

                $("#codigo_modalidad").val(data.cabecera[0].ntipmov);
                $("#codigo_tipo").val(data.cabecera[0].nmottranp);

                $("#tablaDetalles tbody").empty().append(data.detalles);

                $("#proceso").fadeIn();
            },
            "json"
        );

        return false;
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
        datosJSON.append("detalles",JSON.stringify(detalles(1)));

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

    $(".btnCallDialog").click(function(e){
        e.preventDefault();

        controlUbigeo = e.target.id;

        $("#ubigeo").fadeIn();
        
        return false
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

    $("#tablaDetalles tbody").on("click","a", function (e) {
        e.preventDefault();

        fila = $(this);

        $("#preguntaItemBorra").fadeIn();

        return false;
    });

    $("#btnAceptarEliminaItem").click(function (e) { 
        e.preventDefault();

        fila.closest("tr").remove();

        $("#preguntaItemBorra").fadeOut();

        return false;
    });

    $("#btnCancelarEliminaItem").click(function (e) { 
        e.preventDefault();

        $("#preguntaItemBorra").fadeOut();

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
})

detalles = () =>{
    DETALLES = [];

    let TABLA = $("#tablaDetalles tbody >tr");
    
    TABLA.each(function(){
        let ITEM        = $(this).find('td').eq(0).text(),
            IDDETORDEN  = "",
            IDDETPED    = $(this).data('itempedido'),
            IDPROD      = $(this).data('idprod'),
            IDDESPACHO  = $(this).data('itemdespacho'),
            DESPACHO    = $(this).data('despacho'),
            PEDIDO      = $(this).data('pedido'),
            ORDEN       = $(this).data('orden'),
            INGRESO     = "",
            ALMACEN     = "",
            CANTDESP    = $(this).find('td').eq(4).children().val(),
            OBSER       = "",
            CODIGO      = $(this).find('td').eq(1).text(),//codigo
            DESCRIPCION = $(this).find('td').eq(2).text().replace('&','-'),//descripcion
            UNIDAD      = $(this).find('td').eq(3).text(),//unidad
            DESTINO     = $("#codigo_almacen_destino").val(),
            CANTIDAD    = $(this).find('td').eq(4).children().val(),
            GUIA        = $(this).find('td').eq(5).text(),
            PUCALLPA    = $(this).find('td').eq(6).children().val(),
            LURIN       = $(this).find('td').eq(7).children().val();
    
        let item = {};

        //if (CHECKED == flag) {
            item['item']         = ITEM;
            item['iddetorden']   = IDDETORDEN;
            item['iddetped']     = IDDETPED;
            item['idprod']       = IDPROD;
            item['pedido']       = PEDIDO;
            item['orden']        = ORDEN;
            item['ingreso']      = INGRESO;
            item['almacen']      = ALMACEN;
            item['cantidad']     = CANTIDAD;
            item['cantdesp']     = CANTDESP;
            item['obser']        = OBSER;
            item['iddespacho']   = IDDESPACHO;
            item['despacho']     = DESPACHO;

            item['codigo']       = CODIGO;
            item['descripcion']  = DESCRIPCION;
            item['unidad']       = UNIDAD;
            item['destino']      = DESTINO;
            item['guia']         = GUIA;
            item['pucallpa']     = PUCALLPA;
            item['lurin']        = LURIN;

            
            DETALLES.push(item);
        //}
    })

    return DETALLES; 
}

