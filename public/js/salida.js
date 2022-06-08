$(function() {
    let accion = "",
        grabado = false,
        indice_nota=0;

    $("#esperar").fadeOut();

    $("#nuevoRegistro").click(function (e) { 
        e.preventDefault();

        $("#estado")
            .removeClass()
            .addClass("textoCentro estado w100por procesando");
        $("#proceso").fadeIn();
        
        accion = 'n';

        return false;
    });

    $("#closeProcess").click(function (e) { 
        e.preventDefault();

        $("#proceso").fadeOut();

        /*$.post(RUTA+"recepcion/actualizaNotas",
            function (data, textStatus, jqXHR) {
                $(".itemsTabla table tbody")
                    .empty()
                    .append(data);

                $("#proceso").fadeOut(function(){
                    grabado = false;
                    $("form")[0].reset();
                    $("form")[1].reset();
                    $("#tablaDetalles tbody,.listaArchivos").empty();
                });
            },
            "text"
        );*/

        return false;
    });

    $("#importData").click(function (e) { 
        e.preventDefault();
        
        $.post(RUTA+"salida/ingresos",
            function (data, textStatus, jqXHR) {
                $("#notas tbody")
                    .empty()
                    .append(data);

                $("#busqueda").fadeIn();
            },
            "text"
        );

        return false
    });

    $(".tituloVentana").on("click","a", function (e) {
        e.preventDefault();

        $(this).parent().parent().parent().parent().fadeOut();

        return false;
    });

    $("#notas tbody").on("click","tr", function (e) {
        e.preventDefault();

        $("#codigo_ingreso").val($(this).data("idnit"));

        $.post(RUTA+"salida/notaId", {id:$(this).data("idnit")},
            function (data, textStatus, jqXHR) {
                $("#codigo_costos").val(data.cabecera[0].ncodpry);
                $("#codigo_area").val(data.cabecera[0].ncodarea);
                //$("#codigo_aprueba").val(data.cabecera[0].aprueba);
                $("#codigo_almacen").val(data.cabecera[0].ncodalm1);
                $("#codigo_pedido").val(data.cabecera[0].idref_pedi);
                $("#codigo_orden").val(data.cabecera[0].idref_abas);
                $("#codigo_estado").val(data.cabecera[0].nEstadoDoc);
                $("#costos").val(data.cabecera[0].proyecto);
                $("#area").val(data.cabecera[0].area);
                $("#solicita").val(data.cabecera[0].solicita);
                //$("#aprueba").val(data.cabecera[0].cnombres);
                $("#almacen").val(data.cabecera[0].almacen);
                $("#pedido").val(data.cabecera[0].pedido);
                $("#fecha_pedido").val(data.cabecera[0].emision);
                $("#orden").val(data.cabecera[0].orden);
                $("#fecha_orden").val(data.cabecera[0].ffechadoc);
                $("#concepto").val(data.cabecera[0].concepto);
                $("#estado").val(data.cabecera[0].cdescripcion);
                $("#guia").val(data.cabecera[0].cnumguia);
                $("#numero").val(data.numero.numero);
                $("#movimiento").val(data.movimiento);

                $("#tablaDetalles tbody")
                    .empty()
                    .append(data.detalles)

                $("#busqueda").fadeOut();
            },
            "json"
        );

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
        }

        return false;
    });

    $("#preview").click(function (e) { 
        e.preventDefault();

        try {
            let result = {};

            $.each($("#formProceso").serializeArray(),function(){
                result[this.name] = this.value;
            });

            if (result['codigo_salida'] == "") throw "Por favor grabar el documento";

            $("#vistaprevia").fadeIn();

        } catch (error) {
            mostrarMensaje(error,'mensaje_error');
        }

        return false;
    });

    $("#saveDoc").click(function (e) { 
        e.preventDefault();

        try {
            let result = {};

            $.each($("#formProceso").serializeArray(),function(){
                result[this.name] = this.value;
            });

            if (result['codigo_movimiento'] == "") throw "Elija el tipo de movimiento";
            if (result['codigo_ingreso'] == "") throw "Seleccione una nota de ingreso";
            if (result['codigo_aprueba'] == "") throw "Seleccione la persona que aprueba";

            $.post(RUTA+"salida/nuevaSalida", {cabecera:result,
                                                detalles:JSON.stringify(detalles())},
                function (data, textStatus, jqXHR) {
                    mostrarMensaje(data.mensaje,data.clase);
                    $("#codigo_salida").val(data.indice);
                },
                "json"
            );

        } catch (error) {
            mostrarMensaje(error,'mensaje_error');
        }

        return false;
    });
})

detalles = () =>{
    DETALLES = [];

    let TABLA = $("#tablaDetalles tbody >tr");
    
    TABLA.each(function(){
        let ITEM        = $(this).find('td').eq(1).text(),
            IDDETORDEN  = $(this).data("detorden"),
            IDDETPED    = $(this).data("iddetped"),
            IDPROD      = $(this).data("idproducto"),
            PEDIDO      = $("#codigo_pedido").val(),
            ORDEN       = $("#codigo_orden").val(),
            ALMACEN     = $("#codigo_almacen").val(),
            CANTIDAD    = $(this).find('td').eq(5).children().val(),// cantidad
            OBSER       = $(this).find('td').eq(6).children().val(),
            VENCE       = $(this).find('td').eq(8).children().val(),
            SERIE       = $(this).find('td').eq(7).text(),
            CODIGO      = $(this).find('td').eq(2).text(),//codigo
            DESCRIPCION = $(this).find('td').eq(3).text(),//descripcion
            UNIDAD      = $(this).find('td').eq(4).text(),//unidad
            NESTADO     = $(this).find("select[name='estado']").val(),
            CESTADO     = $(this).find("select[name='estado'] option:selected").text(),
            UBICACION   = "";
    
        item = {};

        item['item']        = ITEM;
        item['iddetorden']  = IDDETORDEN;
        item['iddetped']    = IDDETPED;
        item['idprod']      = IDPROD;
        item['pedido']      = ORDEN;
        item['orden']       = PEDIDO;
        item['almacen']     = ALMACEN;
        item['cantidad']    = CANTIDAD;
        item['obser']       = OBSER;
        item['vence']       = VENCE;
        item['serie']       = SERIE;

        item['codigo']     = CODIGO;
        item['descripcion']= DESCRIPCION;
        item['unidad']     = UNIDAD;
        item['nestado']    = NESTADO;
        item['cestado']    = CESTADO;
        item['ubicacion']  = UBICACION;

        DETALLES.push(item);
    })

    return DETALLES; 
}