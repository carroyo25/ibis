$(() => {

    let accion = 0;
    let fila = "";
    let idprod = 0;

    $("#esperar").fadeOut();


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

    $(".cerrarLista").focus(function (e) { 
        e.preventDefault();
        
        $(".lista").fadeOut();

        return false;
    });

    $("#tablaPrincipal tbody").on("click","tr", function (e) {
        e.preventDefault();

        idprod = $(this).data("idprod");
        
        $.post(RUTA+"stocks/resumen",{codigo:$(this).data("idprod")},
            function (data, textStatus, jqXHR) {
                $("#numero_pedidos").text(data.pedidos);
                $("#numero_ordenes").text(data.ordenes);
                $("#inventario").text(data.inventario);
                $("#ingresos").text(data.ingresos);
                $("#pendientes").text(data.pendientes);
                $("#tabla_precios tbody").empty().append(data.precios);
                $("#tabla_existencias tbody").empty().append(data.existencias);

                $("#saldo").text(data.inventario + data.ingresos);
            },
            "json"
        );
        $("#codigo_item").text( $(this).find('td').eq(1).text() );
        $("#descripcion_item").text( $(this).find('td').eq(2).text() );

        $("#vistadocumento").fadeIn();

        return false;
    });

    $("#closeDocument").click(function (e) { 
        e.preventDefault();
        
        $("#vistadocumento").fadeOut();

        return false;
    });

    $("#excelFile").click(function (e) { 
        e.preventDefault();

        $.post(RUTA+"stocks/exporta", {detalles:JSON.stringify(detalles())},
            function (data, textStatus, jqXHR) {
                window.location.href = data.documento;
            },
            "json"
        );

        return false;
        
    });

    $("#btnConsulta").click(function (e) { 
        e.preventDefault();

        let str = $("#formConsulta").serialize();

        try {
            if ( $("#costosSearch").val() == -1) throw "Por favor elija un centro de costos para la consulta";

            $("#esperar").fadeIn();
        
            $.post(RUTA+"stocks/consulta",str,
                function (data, textStatus, jqXHR) {
                    $("#tablaPrincipal tbody")
                        .empty()
                        .append(data);

                        $("#esperar").fadeOut();
                },
                "text"
            );
        } catch (error) {
            mostrarMensaje(error,"mensaje_error");
        }
        

        return false;
    });

    $("#btnStock").click(function(e){
        e.preventDefault();

        $("#registroStock").fadeIn();

        return false;
    });

    $("#btnAceptarStock").click(function(e){
        e.preventDefault();

        $.post(RUTA+"stocks/minimo", { cc:$(costosSearch).val(),
                                        prod:idprod,
                                        cantidad:$(stockMin).val()},
            function (data, text, requestXHR) {
                $("#registroStock").fadeOut();
            },
            "json"
        );
        
        return false;
    });

    $("#btnCancelarStock").click(function(e){
        e.preventDefault();

        $("#registroStock").fadeOut();

        return false;
    });
})

detalles = () =>{
    DATA = [];
    let TABLA = $("#tablaPrincipal tbody >tr");

    TABLA.each(function(){
        let ITEM            = $(this).find('td').eq(0).text(),
            CODIGO          = $(this).find('td').eq(1).text(),
            DESCRIPCION     = $(this).find('td').eq(2).text(),
            UNIDAD          = $(this).find('td').eq(3).text(),
            INGRESO         = $(this).find('td').eq(4).text(),
            INVENTARIO      = $(this).find('td').eq(5).text(),
            SALIDA          = $(this).find('td').eq(6).text(),
            DEVUELTO        = $(this).find('td').eq(7).text(),
            TRANSFERENCIA   = $(this).find('td').eq(8).text(),
            SALDO           = $(this).find('td').eq(10).text();
           
        item= {};
        
        item['item']            = ITEM;
        item['codigo']          = CODIGO;
        item['descripcion']     = DESCRIPCION;
        item['unidad']          = UNIDAD;
        item['ingreso']         = INGRESO;
        item['inventario']      = INVENTARIO;
        item['salida']          = SALIDA;
        item['devuelto']        = DEVUELTO;
        item['transferencias']  = TRANSFERENCIA;
        item['saldo']           = SALDO;
            
        DATA.push(item);
    })

    return DATA;
}
