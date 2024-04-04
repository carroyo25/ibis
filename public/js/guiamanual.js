$(function() {
    let accion = "u",
        tipoVista = null,
        cc = "",
        fila = "",
        idfila = "",
        ordenes = [],
        sw=0,
        grabado = false,
        nfila = 0;
        

    $("#esperar").fadeOut();

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
        }else if(contenedor_padre == "listaCostos"){
            $("#codigo_costos").val(codigo);
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
            if (accion == "n") throw "Grabe el documento";

            $("#vistadocumento").fadeIn();

        } catch (error) {
            mostrarMensaje(error,'mensaje_error');
        }
         
        return false;
    });

    $("#saveRegister").click(function (e) { 
        e.preventDefault();

        accion="u";

        return false;
    });

    $(".tituloDocumento").on("click","#closeDocument", function (e) {
        e.preventDefault();

        $(this).parent().parent().parent().parent().parent().fadeOut();

        return false;
    });

    $("#saveDocument").click(function(e){
        e.preventDefault();

        let result = {};

        accion = "u";

        $.post(RUTA+"guiamanual/nroguia",{operacion:accion},
            function (data, textStatus, jqXHR) {
                $("#numero_guia,#numero").val(data.guia);
            },
            "json"
        );

        /*$.each($("#guiaremision").serializeArray(),function(){
            result[this.name] = this.value;
        });

        $.post(RUTA+"transferencias/GrabaGuia", {cabecera:result,
                                                nota: $("#codigo_transferencia").val(),
                                                operacion:accion},
            function (data, textStatus, jqXHR) {
                mostrarMensaje(data.mensaje,"mensaje_correcto");
                $("#guia,#numero_guia").val(data.guia);

                accion = "u";
            },
            "json"
        );*/

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

})