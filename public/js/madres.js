$(() =>{
    let accion = "";

    $("#esperar").fadeOut();

    $("#nuevoRegistro").click(function (e) { 
        e.preventDefault();

        $("#proceso").fadeIn();

        accion = 'n';

        document.getElementById("formProceso").reset();
        document.getElementById("guiaremision").reset();

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
        let almacen = $(this).data("almacen");
        
        control.slideUp()
        destino.val($(this).text());
        id = destino.attr("id");

        if(contenedor_padre == "listaCostosDestino"){
            $("#codigo_costos_destino").val(codigo);
            $("#codigo_almacen_destino").val(almacen);
        }else if(contenedor_padre == "listaAprueba"){
            $("#codigo_aprueba").val(codigo);
        }else if(contenedor_padre == "listaOrigen"){
            $("#codigo_almacen_origen").val(codigo);
            $("#codigo_origen").val(codigo);
            $("#almacen_origen").val($(this).text());
            $("#almacen_origen_direccion").val($(this).data('direccion'));
        }else if(contenedor_padre == "listaDestino"){
            $("#codigo_almacen_destino").val(codigo);
            $("#almacen_destino").val($(this).text());
            $("#almacen_destino_direccion").val($(this).data('direccion'));
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
        }else if(contenedor_padre == "listaAutoriza"){
            $("#autoriza").val($(this).text());
            $("#codigo_autoriza").val(codigo);
        }else if(contenedor_padre == "listaDespacha"){
            $("#codigo_despacha").val(codigo);
        }else if(contenedor_padre == "listaDestinatario"){
            $("#destinatario").val($(this).text());
            $("#codigo_destinatario").val(codigo);
        }

        return false;
    });

    $("#importData").click(function (e) { 
        e.preventDefault();

        try {
            if ($("#codigo_aprueba").val() == 0) throw "Elija la persona que aprueba";
            if ($("#codigo_costos_destino").val() == 0) throw "Indique el centro de costos"; 

            $("#esperar").fadeIn();

            $.post(RUTA+"madres/guias", {cc:$("#codigo_costos_destino").val(),guia:""},
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
                if ($("#codigo_costos_destino").val() == 0 ) throw "Indique el centro de costos"; 
    
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
            if (result['codigo_entidad'] == "") throw "Seleccione la empresa de transportes";
            if (result['codigo_traslado'] == "") throw "Seleccione la modalidad de traslado";
            
            
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
})

detalles = () =>{
    DETALLES = [];

    let TABLA = $("#tablaDetalles tbody >tr");
    
    TABLA.each(function(){
        let ITEM        = $(this).find('td').eq(0).text(),
            IDDETORDEN  = "",
            IDDETPED    = "",
            IDPROD      = "",
            IDDESPACHO  = "",
            PEDIDO      = "",
            ORDEN       = "",
            INGRESO     = "",
            ALMACEN     = "",
            CANTDESP    = $(this).find('td').eq(4).text(),
            OBSER       = "",
            CODIGO      = $(this).find('td').eq(1).text(),//codigo
            DESCRIPCION = $(this).find('td').eq(2).text(),//descripcion
            UNIDAD      = $(this).find('td').eq(3).text(),//unidad
            DESTINO     = $("#codigo_almacen_destino").val(),
            CANTIDAD    = $(this).find('td').eq(4).text();
    
        item = {};

        //if (CHECKED == flag) {
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
        //}
    })

    return DETALLES; 
}