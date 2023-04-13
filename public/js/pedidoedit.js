$(function(){
    let iditempedido = "",
        fila=0;

    $("#tablaPrincipal tbody").on("click","tr", function (e) {
        e.preventDefault();

        $.post(RUTA+"pedidoedit/consultaRqAdmin", {id:$(this).data("indice")},
            function (data, textStatus, jqXHR) {
                
                let numero = $.strPad(data.cabecera[0].nrodoc,6);
                let estado = "textoCentro w35por estado " + data.cabecera[0].cabrevia;
                
                $("#codigo_costos").val(data.cabecera[0].idcostos);
                $("#codigo_area").val(data.cabecera[0].idarea);
                $("#codigo_transporte").val(data.cabecera[0].idtrans);
                $("#codigo_solicitante").val(data.cabecera[0].idsolicita);
                $("#codigo_partida").val(data.cabecera[0].idpartida);
                $("#codigo_tipo").val(data.cabecera[0].idtipomov);
                $("#codigo_pedido").val(data.cabecera[0].idreg);
                $("#codigo_estado").val(data.cabecera[0].estadodoc);
                $("#codigo_verificacion").val(data.cabecera[0].verificacion);
                $("#codigo_atencion").val(data.cabecera[0].nivelAten);
                $("#vista_previa").val(data.cabecera[0].docfPdfPrev);
                $("#numero").val(numero);
                $("#emision").val(data.cabecera[0].emision);
                $("#costos").val(data.cabecera[0].proyecto);
                $("#area").val(data.cabecera[0].area);
                $("#transporte").val(data.cabecera[0].transporte);
                $("#concepto").val(data.cabecera[0].concepto);
                $("#solicitante").val(data.cabecera[0].nombres);
                $("#tipo").val(data.cabecera[0].tipo);
                $("#vence").val(data.cabecera[0].vence);
                $("#estado").val(data.cabecera[0].estado);
                $("#espec_items").val(data.cabecera[0].detalle);
                $("#partida").val(data.cabecera[0].cdescripcion);
               

                $("#tablaDetalles tbody")
                    .empty()
                    .append(data.detalles);

                $("#estado")
                    .removeClass()
                    .addClass(estado);
                
                grabado = true;
            },
            "json"
        );

        $("#proceso").fadeIn();

        return false;
    });

    $("#closeProcess").click(function (e) { 
        e.preventDefault();

        $("#proceso").fadeOut();

        /*$.post(RUTA+"pedidoedit/actualizaListado",
            function (data, textStatus, jqXHR) {
                $(".itemsTabla table tbody")
                    .empty()
                    .append(data);

                $("#proceso").fadeOut(function(){
                    grabado = false;
                    $("form")[1].reset();
                    $("#tablaDetalles tbody,.listaArchivos").empty();
                    $(".lista").fadeOut();
                });
            },
            "text"
        );*/
        return false;
    });

    $("#btnAnular").click(function (e) { 
        e.preventDefault();

        try {
            if ($("#codigo_estado").val() >= 54) throw "El pedido no puede ser modificado";
            $("#preguntaAnula").fadeIn();
        } catch (error) {
            mostrarMensaje(error,"mensaje_correcto");
        }
    
        return false;
    });

    $("#btnCancelarAnula").click(function (e) { 
        e.preventDefault();

        $("#preguntaAnula").fadeOut();
        
        return false;
    });

    $("#btnAceptarAnula").click(function (e) { 
        e.preventDefault();

        $("#ventanaEspera").fadeIn();

        $.post(RUTA+"pedidoedit/cambiaPedido", {id:$("#codigo_pedido").val(),valor:105},
            function (data, textStatus, jqXHR) {
                $("#preguntaAnula").fadeOut();
                $("#ventanaEspera").fadeOut();

                mostrarMensaje(data,"mensaje_correcto");
            },
            "text"
        );
        
        return false;
    });
    
    $("#btnRetornar").click(function (e) { 
        e.preventDefault();

        try {
            if ($("#codigo_estado").val() >= 54) throw "El pedido no puede ser modificado";
            $("#preguntaProceso").fadeIn();
        } catch (error) {
            mostrarMensaje(error,"mensaje_correcto");
        }

        return false;
    });

    $("#btnCancelarProceso").click(function (e) { 
        e.preventDefault();

        $("#preguntaProceso").fadeOut();
        
        return false;
    });

    $("#btnAceptarProceso").click(function (e) { 
        e.preventDefault();

        $("#ventanaEspera").fadeIn();

        $.post(RUTA+"pedidoedit/cambiaPedido", {id:$("#codigo_pedido").val(),valor:49},
            function (data, textStatus, jqXHR) {
                $("#preguntaProceso").fadeOut();
                $("#ventanaEspera").fadeOut();

                mostrarMensaje(data,"mensaje_correcto");
            },
            "text"
        );
        
        return false;
    });

    $("#tablaDetalles tbody").on("click","a", function (e) {
        e.preventDefault();

        let nro_items = $("#tablaDetalles tbody tr").length;

        iditempedido = $(this).parent().parent().data('idx');
        fila = $(this).parent().parent();

        if ($(this).data('accion') == "eliminar") {
            try {
                if ( nro_items <= 1 ) throw new Error("El pedido sólo tiene un item");

                $("#preguntaItemBorra").fadeIn();

            } catch (error) {
                mostrarMensaje(error,"mensaje_error");
            }
        }

        return false;
    });

    $("#btnAceptarEliminaItem").click(function (e) { 
        e.preventDefault();

        $.post(RUTA+"pedidoedit/accionItem",{id:iditempedido,estado:0},
            function (data, textStatus, jqXHR) {
                fila.remove();
                $("#preguntaItemBorra").fadeOut();
                fillTables($("#tablaDetalles tbody > tr"),1);
            },
            "json"
        );
        
        return false;
    });

    $("#btnCancelarEliminaItem").click(function (e) { 
        e.preventDefault();

        $("#preguntaItemBorra").fadeOut();

        return false;
    });

    $("#btnAgregar").click(function (e) { 
        e.preventDefault();
        
        $.post(RUTA+"pedidos/llamaProductos", {tipo:$("#codigo_tipo").val()},
            function (data, textStatus, jqXHR) {
                $("#tablaModulos tbody")
                    .empty()
                    .append(data);
                $("#busqueda").fadeIn();
            },
            "text"
        );

        return false;
    });

    $("#tablaModulos tbody").on("click","tr", function (e) {
        e.preventDefault();

        let nFilas = $.strPad($("#tablaDetalles tr").length,3);
        let idprod = $(this).data("idprod");
        let nunid = $(this).data("ncomed");
        let codigo = $(this).children('td:eq(0)').text();
        let descrip = $(this).children('td:eq(1)').text();
        let unidad = $(this).children('td:eq(2)').text();
        let grabado = 0;
        

        let row = `<tr data-grabado="${grabado}" data-idprod="${idprod}" data-codund="${nunid}" data-idx="-" data-registro="">
                    <td class="textoCentro"><a href="#" title="eliminar" data-accion ="eliminar"><i class="fas fa-eraser"></i></a></td>
                    <td class="textoCentro">${nFilas}</td>
                    <td class="textoCentro">${codigo}</td>
                    <td class="pl20px">${descrip}</td>
                    <td class="textoCentro">${unidad}</td>
                    <td><input type="number" step="any" placeholder="0.00" onchange="(function(el){el.value=parseFloat(el.value).toFixed(2);})(this)"></td>
                    <td><textarea></textarea></td>
                    <td class="textoCentro"><input type="text"></td>
                    <td class="textoCentro"><input type="text"></td>
                    <td class="textoCentro"><a href="-" title="Cambiar Item" data-accion="cambiar"><i class="fas fa-exchange-alt"></i></a></td>
                    <td class="textoCentro"><a href="-" title="Liberar Item" data-accion="liberar"><i class="fas fa-wrench"></i></a></td>
                    <td class="textoCentro"><a href="-" title="Agregar Item debajo" data-accion="agregar"><i class="far fa-calendar-plus"></i></a></td>
                </tr>`;

        $("#tablaDetalles tbody").append(row);

        return false;
    });

    $(".tituloVentana").on("click","a", function (e) {
        e.preventDefault();

        $(this).parent().parent().parent().parent().fadeOut();

        return false;
    });
})