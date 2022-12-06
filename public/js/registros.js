$(function(){
    var accion = "";
    var index = "";

    $("#nuevoRegistro").click(function (e) { 
        e.preventDefault();

        $("#estado")
            .removeClass()
            .addClass("textoCentro w35por estado procesando");
        $("#proceso").fadeIn();
        $("form")[1].reset();
        $("#tablaDetalles tbody").empty();

        accion = 'n';


        return false;
    });

    $("#esperar").fadeOut();

    $("#tablaPrincipal tbody").on("click","tr", function (e) {
        e.preventDefault();

        $.post(RUTA+"registros/despachosID", {id:$(this).data("despacho")},
            function (data, textStatus, jqXHR) {
                $("#codigo_costos").val(data.cabecera[0].codigo_costos);
                $("#codigo_area").val(data.cabecera[0].codigo_area);
                $("#codigo_almacen_origen").val(data.cabecera[0].codigo_origen);
                $("#codigo_almacen_destino").val(data.cabecera[0].codigo_destino);
                $("#codigo_pedido").val(data.cabecera[0].codigo_pedido);
                $("#codigo_orden").val(data.cabecera[0].codigo_orden);
                $("#codigo_estado").val(data.cabecera[0].nEstadoDoc);
                $("#codigo_entidad").val(data.cabecera[0].id_centi);
                $("#codigo_ingreso").val(data.cabecera[0].idref_abas);
                $("#codigo_salida").val(data.cabecera[0].id_despacho);
                $("#almacen_origen_despacho").val(data.cabecera[0].origen);
                $("#almacen_destino_despacho").val(data.cabecera[0].destino);
                $("#numero").val(data.numero);
                $("#costos").val(data.cabecera[0].costos);
                $("#area").val(data.cabecera[0].area);
                $("#solicita").val(data.cabecera[0].solicita);
                $("#orden").val(data.cabecera[0].orden);
                $("#pedido").val(data.cabecera[0].pedido);
                $("#guia").val(data.cabecera[0].guia);
                $("#concepto").val(data.cabecera[0].concepto);
                $("#fecha_pedido").val(data.cabecera[0].emision);
                $("#fecha_orden").val(data.cabecera[0].fecha_orden);
                $("#bultos").val(data.cabecera[0].nbultos);
                $("#peso").val(data.cabecera[0].npesotot);
                
                $("#tablaDetalles tbody")
                    .empty()
                    .append(data.detalles);
            },
            "json"
        );

        accion = "u";
        grabado = true;
        $("#proceso").fadeIn();

        return false;
    });

    $("#closeProcess").click(function (e) { 
        e.preventDefault();

        $("#proceso").fadeOut()

        /*$.post(RUTA+"registros/actualizarDespachos",
            function (data, textStatus, jqXHR) {
                $(".itemsTabla table tbody")
                    .empty()
                    .append(data);

                $("#proceso").fadeOut(function(){
                    grabado = false;
                    $("form")[0].reset();
                    $("form")[1].reset();
                });
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

        if(contenedor_padre == "listaRecepciona"){
            $("#codigo_autoriza").val(codigo);
        }

        return false;
    });


    $("#updateDocument").click(function(e){
        e.preventDefault();

        let result = {};

        $.each($("#formProceso").serializeArray(),function(){
            result[this.name] = this.value;
        });

        try {
            if (result['codigo_autoriza'] == '') throw "Elija el responsable de la recepcion";

            console.log(detalles());  
            
            $.post(RUTA+"registros/nuevoRegistro", {cabecera:result,detalles:JSON.stringify(detalles())},
                function (data, textStatus, jqXHR) {
                    console.log(data);  
                },
                "json"
            );
        } catch (error) {
            mostrarMensaje(error,'mensaje_error');
        }

        return false;
    });

    $("#itemsImport").click(function (e) { 
        e.preventDefault();

        $.post(RUTA+"registros/despachos",
            function (data, textStatus, jqXHR) {
                $("#despachos tbody")
                    .empty()
                    .append(data);
                
                    $("#busqueda").fadeIn();

            },
            "text"
        );

        return false;
    });

    $("#closeSearch").click(function (e) { 
        e.preventDefault();
        
        $("#busqueda").fadeOut();

        return false;
    });

    $("#despachos tbody").on("click","tr", function (e) {
        e.preventDefault();

        $.post(RUTA+"registros/consultaID", {indice:$(this).data("indice")},
            function (data, textStatus, jqXHR) {

                $("#numero").val(data.numero);
                $("#costos").val(data.cabecera[0].costos);
                $("#almacen_destino_ingreso").val(data.cabecera[0].destino);
                $("#almacen_origen_ingreso").val(data.cabecera[0].origen);
                $("#guia").val(data.cabecera[0].cnumguia);
                $("#referido").val(data.cabecera[0].nReferido);
                $("#codigo_almacen_origen").val(data.cabecera[0].ncodalm1);
                $("#codigo_almacen_destino").val(data.cabecera[0].ncodalm2);
                $("#codigo_costos").val(data.cabecera[0].ncodpry);
                

                $("#tablaDetalles tbody")
                    .empty()
                    .append(data.detalles);
                $("#busqueda").fadeOut();
            },
            "json"
        );
       

        return false
    });

})

detalles = () =>{
    DETALLES = [];

    let TABLA = $("#tablaDetalles tbody tr");

    TABLA.each(function(){
        let IDDEPET     = $(this).data("idpet"),
            CODPROD     = $(this).data("codprod"),
            AREA        = $(this).data("area"),
            ALMACEN     = $(this).data("almacen"),
            COSTOS       = $(this).data("costos"),
            CANTRECEP   = $(this).find('td').eq(5).children().val(),
            OBSERVAC    = $(this).find('td').eq(6).children().val(),
            VENCE       = $(this).find('td').eq(8).children().val(),
            UBICA       = $(this).find('td').eq(9).children().val(),
            ORDEN       = $(this).find('td').eq(10).children().text(),
            PEDIDO      = $(this).find('td').eq(11).text();

        item = {};

        if ( CANTRECEP > 0 ) {

            item['iddepet']     = IDDEPET;
            item['codprod']     = CODPROD;
            item['area']        = AREA;
            item['cantrecep']   = CANTRECEP;
            item['observac']    = OBSERVAC;
            item['vence']       = VENCE;
            item['ubica']       = UBICA;
            item['pedido']      = PEDIDO;
            item['orden']       = ORDEN;
            item['almacen']     = ALMACEN;
            item['costos']      = COSTOS;
            
            DETALLES.push(item);
        }
    })

    return DETALLES
}
