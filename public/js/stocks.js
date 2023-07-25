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
        
        $.post(RUTA+"stocks/resumen",{codigo:$(this).data("idprod"),cc:$("#costosSearch").val()},
            function (data, textStatus, jqXHR) {
                //pedidos
                $("#tabla1_tab1 tbody").find('tr').eq(0).find('td').eq(1).text(data.pedidos.numeros);
                $("#tabla1_tab1 tbody").find('tr').eq(0).find('td').eq(2).text(data.pedidos.cantidad);

                //ordenes
                $("#tabla1_tab1 tbody").find('tr').eq(1).find('td').eq(1).text(data.ordenes.numeros);
                $("#tabla1_tab1 tbody").find('tr').eq(1).find('td').eq(2).text(data.ordenes.cantidad);

                //recepcion
                $("#tabla1_tab1 tbody").find('tr').eq(2).find('td').eq(1).text(data.recepcion.numeros);
                $("#tabla1_tab1 tbody").find('tr').eq(2).find('td').eq(2).text(data.recepcion.cantidad);

                //despacho
                $("#tabla1_tab1 tbody").find('tr').eq(3).find('td').eq(1).text(data.despacho.numeros);
                $("#tabla1_tab1 tbody").find('tr').eq(3).find('td').eq(2).text(data.despacho.cantidad);

                //ingresos Obra
                $("#tabla1_tab1 tbody").find('tr').eq(4).find('td').eq(1).text(data.existencias.numeros);
                $("#tabla1_tab1 tbody").find('tr').eq(4).find('td').eq(2).text(data.existencias.cantidad);

                //Inventarios
                $("#tabla2_tab1 tbody").find('tr').eq(2).find('td').eq(1).text(data.inventarios.numeros);
                $("#tabla2_tab1 tbody").find('tr').eq(2).find('td').eq(2).text(data.inventarios.cantidad);
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

            $("#esperar").css("opacity","1").fadeIn();
        
            $.post(RUTA+"stocks/consulta",str,
                function (data, textStatus, jqXHR) {
                    $("#tablaPrincipal tbody")
                        .empty()
                        .append(data);

                        $("#esperar").css("opacity","0").fadeOut();
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

    $(".tab_button").click(function(e){
        $(".tab_button").addClass('tab_inactivo');
        $(this).removeClass('tab_inactivo');

        $(".tab").hide();
        
        ///let actual_tab = $(this).data("tab"); 
        let tab = '#'+$(this).data("tab"),
            tabActive = $(this).data("tab");

        /*if (tabActive = "tab1") {
            $.post("url", data,
                function (data, textStatus, jqXHR) {
                    
                },
                "json"
            );
        }*/

        $(tab).fadeIn();
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
