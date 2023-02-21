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

        $.post(RUTA+"registros/registroID",{id:$(this).data("indice")},
            function (data, textStatus, jqXHR) {

                $("#fecha").val(data.cabecera[0].ffechadoc);
                $("#costos").val(data.cabecera[0].costos);
                $("#codigo_costos").val(data.idcostos);
                $("#codigo_almacen_origen").val(data.cabecera[0].ncodalm1);
                $("#codigo_almacen_destino").val(data.cabecera[0].ncodalm2);
                $("#codigo_autoriza").val(data.cabecera[0].idautoriza);
                $("#codigo_recepcion").val(data.cabecera[0].idrecepciona);
                $("#codigo_ingreso").val(data.cabecera[0].idreg);
                $("#guia").val(data.cabecera[0].numguia);
                $("#referido").val(data.cabecera[0].nreferido);
                $("#almacen_origen_ingreso").val(data.cabecera[0].origen);
                $("#almacen_destino_ingreso").val(data.cabecera[0].destino);
                $("#autoriza").val(data.cabecera[0].cnombres);
                $("#numero").val(data.cabecera[0].numero);
                
                
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

        $.post(RUTA+"registros/actualizarRegistros",
            function (data, textStatus, jqXHR) {
                $("#tablaPrincipal tbody")
                    .empty()
                    .append(data);
            },
            "text"
        );

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
            if (result['cnumguia'] == '') throw "Seleccione un numero de guia";
            if (accion != "n") throw "No se puede grabar";
        
            $.post(RUTA+"registros/nuevoRegistro", {cabecera:result,detalles:JSON.stringify(detalles())},
                function (data, textStatus, jqXHR) {
                    if (data.estado){
                        mostrarMensaje("Ingreso correcto","mensaje_correcto");
                    }else {
                        mostrarMensaje("Hubo un problema con el registro","mensaje_error");
                    }
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

        try {
            if (guias()) throw "La guia ya esta registrado";

            $.post(RUTA+"registros/despachos",{guia:$("#txtBuscar").val()},
                function (data, textStatus, jqXHR) {
                    $("#despachos tbody")
                        .empty()
                        .append(data);
                    
                        $("#busqueda").fadeIn();
                },
                "text"
            );

        } catch (error) {
            console.log(error);
        }

        
        
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
                $("#codigo_despacho").val(data.cabecera[0].id_regalm);
                
                $("#tablaDetalles tbody")
                    .empty()
                    .append(data.detalles);
                $("#busqueda").fadeOut();
            },
            "json"
        );
       

        return false
    });

    $("#txtBuscar").keyup(function(){
        let _this = this;

        $.each($("#despachos tbody > tr"), function() {
            if($(this).text().toLowerCase().indexOf($(_this).val().toLowerCase()) === -1)
                $(this).hide();
            else
                $(this).show();
        });
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
            COSTOS      = $(this).data("costos"),
            CANTENV     = $(this).find('td').eq(4).text(),
            CANTRECEP   = $(this).find('td').eq(5).children().val(),
            OBSERVAC    = $(this).find('td').eq(6).children().val(),
            VENCE       = $(this).find('td').eq(8).children().val(),
            UBICA       = $(this).find('td').eq(9).children().val(),
            ORDEN       = $(this).find('td').eq(10).text(),
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
            item['cantenv']     = CANTENV;
            
            DETALLES.push(item);
        }
    })

    return DETALLES
}

guias = (guia) => {
    existe = false;

    let TABLA = $("#tablaDetalles tbody >tr");

    TABLA.each(function(){
            ingresada    = $(this).find('td').eq(12).text()

        if (ingresada == guia) {
            existe = true;
        }
    })

    return existe;
}
