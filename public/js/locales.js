$(function(){

    let accion = "",
        tipoMovimiento = 0;  //guia remision  = 1, transferencias = 2

    $("#nuevoRegistro").click(function (e) { 
            e.preventDefault();
    
            $("#estado")
                .removeClass()
                .addClass("textoCentro w35por estado procesando");
            $("#proceso").fadeIn();
            $("form")[1].reset();
            $("#tablaDetalles tbody").empty();

            $("#registrado").val($("#name_user").val());
            $("#codigo_registra").val($("#id_user").val());
    
            accion = 'n';
    
            return false;
    }); 
    
    $("#closeProcess").click(function (e) { 
        e.preventDefault();

        $("#proceso").fadeOut();

        /*$.post(RUTA+"registros/actualizarRegistros",
            function (data, textStatus, jqXHR) {
                $("#tablaPrincipal tbody")
                    .empty()
                    .append(data);
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
        }else if (contenedor_padre == "listaCostos"){
            $("#codigo_costos").val(codigo);
        }

        return false;
    });

    $("#importRequest").click(function (e) { 
        e.preventDefault();

        try {
            if ($("#codigo_aprueba").val() == 0) throw "Elija la persona que aprueba";
            if ($("#codigo_costos_origen").val() == 0) throw "Indique el centro de costos"; 

            $("#esperar").fadeIn();

            $.post(RUTA+"locales/pedidos",{cc:$("#codigo_costos").val(),pedido:""},
                function (data, textStatus, jqXHR) {
                    $("#tablaPedidos tbody")
                        .empty()
                        .append(data);

                        $("#pedidos").fadeIn();
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

    $("#tablaPedidos tbody").on("click","tr", function () {
        $.post(RUTA+"locales/items", {indice:$(this).data('indice'),origen:$("#codigo_costos").val()},
            function (data, textStatus, jqXHR) {
                $("#tablaDetalles tbody")
                    .empty()
                    .append(data.items);

                $("#total_items").val(data.total_items);
            },
            "json"
        );
    });

    $("#saveRegister").click(function(e){
        e.preventDefault();

        let result = {},
            pedido = $("#tablaDetalles tbody >tr").data("pedido");

        $.each($("#formProceso").serializeArray(),function(){
            result[this.name] = this.value;
        });

        try {

            if  ( $("#codigo_costos").val() == "") throw "Elija el centro de costos";
            if  ( $("#codigo_autoriza").val() == "" ) throw "Elija la persona que autoriza";

            if (accion == "n") {
                
                $("#esperar").css("opacity","1").fadeIn();

                $.post(RUTA+"locales/registro",{cabecera:result,
                                                detalles:JSON.stringify(detalles()),
                                                idpedido:pedido},
                    function (data, textStatus, jqXHR) {
                        if(data.estado){
                            mostrarMensaje(data.mensaje,"mensaje_correcto");
                            $("#numero").val(data.documento);
                            $("#codigo_transferencia").val(data.indice);
                        }else{
                            mostrarMensaje(data.mensaje,"mensaje_error");
                        }

                        $("#esperar").css("opacity","0").fadeOut();

                        accion = "d";
                    },
                    "json"
                );
            }

        } catch (error) {
            mostrarMensaje(error,'mensaje_error');
        }

        return false;
    });
});

detalles = () =>{
    DETALLES = [];

    let TABLA = $("#tablaDetalles tbody >tr");
    
    TABLA.each(function(){
        let ITEM            = $(this).find('td').eq(2).text(),
            IDPROD          = $(this).data("idprod"),
            GRABADO         = $(this).data("grabado"),
            COSTOS          = $(this).data("costos"),
            CODIGO          = $(this).find('td').eq(3).text(),//codigo
            DESCRIPCION     = $(this).find('td').eq(4).text(),//unidad
            UNIDAD          = $(this).find('td').eq(5).text(),//unidad
            CANTIDAD        = $(this).find('td').eq(6).text(),// cantidad
            OBSER           = $(this).find('td').eq(7).children().val(),
            PEDIDO          = $(this).data("pedido"),
            IDITEM          = $(this).data("iditem"),
            APROBADO        = $(this).data("aprobado"),
            NROPEDIDO       = $(this).find('td').eq(8).text(),

    
        item = {};

        if (!GRABADO) {
            item['item']         = ITEM;
            item['idprod']       = IDPROD;
            item['origen']       = ORIGEN;
            item['cantidad']     = CANTIDAD;
            item['obser']        = OBSER;
            item['codigo']       = CODIGO;
            item['descripcion']  = DESCRIPCION;
            item['unidad']       = UNIDAD;
            item['destino']      = DESTINO;
            item['iditem']       = IDITEM;
            item['pedido']       = PEDIDO;
            item['aprobado']     = APROBADO;
            item['comprado']     = COMPRADO;
            item['costos']       = COSTOS;
            item['nropedido']    = NROPEDIDO;
            item['separado']     = SEPARADO;
            item['atendido']     = ATENDIDO;
                
            DETALLES.push(item);
        }     
    })

    return DETALLES; 
}
