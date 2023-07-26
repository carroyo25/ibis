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

        $("#codigo_item").text( $(this).find('td').eq(1).text() );
        $("#descripcion_item").text( $(this).find('td').eq(2).text() );

        resumen(idprod);

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

    $("#btnAgregarMin").click(function(e){
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

        if (tabActive == "tab1") {
            resumen(idprod);
        }

        $(tab).fadeIn();
    });
})

resumen = (codigo_producto) => {
     $.post(RUTA+"stocks/resumen",{codigo:codigo_producto,cc:$("#costosSearch").val()},
            function (data, textStatus, jqXHR) {
               //pedidos
                $("#tabla1_tab1 tbody").find('tr').eq(0).find('td').eq(1).text(esnulo(data.pedidos.numeros));
                $("#tabla1_tab1 tbody").find('tr').eq(0).find('td').eq(2).text(esnulo(data.pedidos.cantidad));

                //ordenes
                $("#tabla1_tab1 tbody").find('tr').eq(1).find('td').eq(1).text(esnulo(data.ordenes.numeros));
                $("#tabla1_tab1 tbody").find('tr').eq(1).find('td').eq(2).text(esnulo(data.ordenes.cantidad));

                //recepcion
                $("#tabla1_tab1 tbody").find('tr').eq(2).find('td').eq(1).text(esnulo(data.recepcion.numeros));
                $("#tabla1_tab1 tbody").find('tr').eq(2).find('td').eq(2).text(esnulo(data.recepcion.cantidad));

                //despacho
                $("#tabla1_tab1 tbody").find('tr').eq(3).find('td').eq(1).text(esnulo(data.despacho.numeros));
                $("#tabla1_tab1 tbody").find('tr').eq(3).find('td').eq(2).text(esnulo(data.despacho.cantidad));

                //ingresos Obra
                $("#tabla1_tab1 tbody").find('tr').eq(4).find('td').eq(1).text(esnulo(data.existencias.numeros));
                $("#tabla1_tab1 tbody").find('tr').eq(4).find('td').eq(2).text(esnulo(data.existencias.cantidad));

                //Consumos
                $("#tabla2_tab1 tbody").find('tr').eq(0).find('td').eq(1).text(esnulo(data.consumos.numeros));
                $("#tabla2_tab1 tbody").find('tr').eq(0).find('td').eq(2).text(esnulo(data.consumos.cantidad));

                //Devoluciones
                $("#tabla2_tab1 tbody").find('tr').eq(1).find('td').eq(1).text(esnulo(data.devoluciones.numeros));
                $("#tabla2_tab1 tbody").find('tr').eq(1).find('td').eq(2).text(esnulo(data.devoluciones.cantidad));
                
                //Inventarios
                $("#tabla2_tab1 tbody").find('tr').eq(2).find('td').eq(1).text(esnulo(data.inventarios.numeros));
                $("#tabla2_tab1 tbody").find('tr').eq(2).find('td').eq(2).text(esnulo(data.inventarios.cantidad));

                //Transferencias
                $("#tabla2_tab1 tbody").find('tr').eq(3).find('td').eq(1).text(esnulo(data.transferencias.numeros));
                $("#tabla2_tab1 tbody").find('tr').eq(3).find('td').eq(2).text(esnulo(data.transferencias.cantidad));

                let saldo = (parseFloat(esnulo(data.existencias.cantidad))+
                            parseFloat(esnulo(data.inventarios.cantidad))+
                            parseFloat(esnulo(data.devoluciones.cantidad))) - parseFloat(esnulo(data.consumos.cantidad));

                $("#tabla2_tab1 tbody").find('tr').eq(4).find('td').eq(1).text(saldo);

                $("#tabla1_tab2 tbody")
                    .empty()
                    .append(data.minimos);

                $("#tabla1_tab3 tbody")
                    .empty()
                    .append(data.precios);
                


                $("#vistadocumento").fadeIn();
            },
            "json"
        );
}

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

esnulo = (valor) => {
    return v = valor === null ? '0.00' : valor;
}
