$(function() {
    let accion = "",
        tipoVista = null,
        cc = "",
        fila = "",
        grabado = 1,
        ubigeo = null;
        

    $("#esperar").fadeOut();

    $("#tablaPrincipal tbody").on("click","tr", function (e) {
        e.preventDefault();

        $.post(RUTA+"guiamanual/guiaManualId",{indice:$(this).data("indice"),guia:$(this).data('guia')},
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

                $("#numero_guia_sunat").val(data.cabecera[0].guiasunat);

                $("#tablaDetalles tbody")
                    .empty()
                    .append(data.detalles);

                $("#proceso").fadeIn();

                grabado = 1;
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
            $("#almacen_origen").val($(this).text());
            $("#almacen_origen_direccion").val($(this).data('direccion'));
            $("#codigo_origen_sunat").val($(this).data('sunat'));
            $("#ruc_entidad_origen").val($(this).data('ruc'));
            $("#ubigeo_origen_guia,#ubig_origen").val($(this).data('ubigeo'));
            $("#cso").val($(this).data('sunat'));
        }else if(contenedor_padre == "listaDestino"){
            $("#codigo_almacen_destino").val(codigo);
            $("#almacen_destino").val($(this).text());
            $("#almacen_destino_direccion").val($(this).data('direccion'));
            $("#codigo_destino_sunat").val($(this).data('sunat'));
            $("#ubigeo_destino_guia,#ubig_destino").val($(this).data('ubigeo'));
            $("#ruc_entidad_destino").val($(this).data('ruc'));
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
            $("#codigo_origen").val(codigo);
        }else if(contenedor_padre == "listaDestinoCabecera"){
            $("#codigo_almacen_destino").val(codigo);
            $("#almacen_destino").val($(this).text());
        }

        return false;
    });

    $("#addItem").click(function(e){
        e.preventDefault();

        let nFilas = $.strPad($("#tablaDetalles tr").length,3);

        let row = `<tr data-grabado="0" >
                        <td class="textoCentro"><a href="delete"><i class="fas fa-trash-alt"></i></a></td>
                        <td class="textoCentro"><a href="search"><i class="fas fa-search"></i></a></td>
                        <td class="textoCentro">${nFilas}</td>
                        <td class="textoCentro"><input type="text" value="-"></td>
                        <td class="pl20px"><textarea></textarea></td>
                        <td><input type="text" value="-"></td>
                        <td><input type="number" value=1 min=1></td>
                        <td class="pl20px"><textarea></textarea></td>
                        <td></td>
                        <td></td>
                    </tr>`;

        $("#tablaDetalles tbody").append(row);

        return false;
    })

    $("#tablaDetalles tbody").on("click","a", function (e) {
        e.preventDefault();

        if ( $(this).attr("href") === 'search' ){
            
            fila = e.target.closest("tr");

            $.post(RUTA+"pedidos/llamaProductos", {tipo:37},
            function (data, textStatus, jqXHR) {
                    $("#tablaModulos tbody")
                        .empty()
                        .append(data);
    
                    $("#busqueda").fadeIn();
                },
                "text"
            );
        }else if( $(this).attr("href") === 'delete' ){
            e.target.closest("tr").remove();
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

        fila.cells[3].children[0].value = $(this).find('td').eq(0).text();
        fila.cells[4].children[0].value = $(this).find('td').eq(1).text();
        fila.cells[5].children[0].value = $(this).find('td').eq(2).text();

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

        $.post(RUTA+"guiaManual/grabaGuiaManual",{guiaCab:guia,
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
            
            $.post(RUTA+"guiamanual/vistaPreviaGuia", {cabecera:result,
                                                            detalles:JSON.stringify(detalles(grabado)),
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

            if ( $("#ticket_sunat" ).val() === "" ) {
                fetch(RUTA+"salida/numeroSunat")
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
                if (data.respesta == 0)
                    mostrarMensaje(data.mensaje,"mensaje_correcto");
                else
                    mostrarMensaje(data.mensaje,"mensaje_error");
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
            item['cantidad']     = $(this).find('td').eq(6).children().val();
            item['cantdesp']     = $(this).find('td').eq(6).children().val();
            item['obser']        = $(this).find('td').eq(7).children().val();
            item['codigo']       = $(this).find('td').eq(3).children().val();
            item['descripcion']  = $(this).find('td').eq(4).children().val();
            item['unidad']       = $(this).find('td').eq(5).children().val();
            item['destino']      = $("#codigo_almacen_destino").val();
            item['estado']       = $(this).data("estado");

            
            DETALLES.push(item);
        }
    })

    return DETALLES; 
}