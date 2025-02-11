$(function(){  
    $("#esperar").css({"display":"block","opacity":"1"});

    let str = $("#formConsulta").serialize();

    $.post(RUTA+"pedidoedit/actualizaListado",str,
        function (data, text, requestXHR) {
            $(".tablaPrincipal tbody")
                .empty()
                .append(data);

                $("#esperar").fadeOut().promise().done(function(){
                    iniciarPaginador();
                });

        "text"
    });

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
                $("#asigna").val(data.cabecera[0].asigna);
                $("#aprueba").val(data.cabecera[0].aprueba);
                $("#fecha_aprobacion").val(data.cabecera[0].faprueba);

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
        
        //llama la funcion para recargar la tabla
        //query();
        
        return false;
    });

    $("#btnAnular").click(function (e) { 
        e.preventDefault();

        try {
            if ($("#codigo_estado").val() >= 54) throw "El pedido no puede ser anulado..";
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

        cambiarPedido($("#codigo_pedido").val(),105);
        
        return false;
    });
    
    $("#btnRetornar").click(function (e) { 
        e.preventDefault();

        try {
            if ($("#codigo_estado").val() > 54) throw "El pedido no puede ser modificado";
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

                mostrarMensaje(data.mensaje,"mensaje_correcto");
            },
            "json"
        );
        
        return false;
    });

    $("#tablaDetalles tbody").on("click","a", function (e) {
        e.preventDefault();

        let nro_items = $("#tablaDetalles tbody tr").length;

        iditempedido = $(this).parent().parent().data('idx');
        estadoItem =  $(this).parent().parent().data('estado');
        fila = $(this).parent().parent();

        if ( $(this).data('accion') == "eliminar" ) {
            try {
                if ( nro_items <= 1 ) throw new Error("El pedido sólo tiene un item");

                $("#preguntaItemBorra").fadeIn();

            } catch (error) {
                mostrarMensaje(error,"mensaje_error");
            }
        }else if( $(this).data('accion') == "liberar" ) {
            $("#preguntaItemLibera").fadeIn();
        }else if(  $(this).data('accion') == "cambiar"){

            accion = "change";
            fila_reemplazar = $(this).parent().parent();
            item_accion = fila_reemplazar.data('estado');

            try {
                //if ( item_accion == 60 || item_accion == 62 || item_accion == 52 ) throw new Error('El item no puede ser cambiado');
                if ( item_accion == 60 || item_accion == 62 ) throw new Error('El item no puede ser cambiado');

                listarItems($("#codigo_tipo").val());
            } catch (error) {
                mostrarMensaje(error.message,'mensaje_error');
            }
        }

        return false;
    });

    $("#btnAceptarEliminaItem").click(function (e) { 
        e.preventDefault();

        if ( iditempedido == '-') {
            fila.remove();
        }else{
            $.post(RUTA+"pedidoedit/accionItem",{id:iditempedido,valor:1,estado:105},
                function (data, textStatus, jqXHR) {
                    fila.remove();
                    $("#preguntaItemBorra").fadeOut();
                    fillTables($("#tablaDetalles tbody > tr"),1);
                    mostrarMensaje(data.mensaje,"mensaje_correcto");
                },
                "json"
            );
        }
        
        return false;
    });

    $("#btnCancelarEliminaItem").click(function (e) { 
        e.preventDefault();

        $("#preguntaItemBorra").fadeOut();

        return false;
    });

    $("#btnAgregar").click(function (e) { 
        e.preventDefault();

        accion = "add";
        listarItems($("#codigo_tipo").val());
         
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
                    <td><input type="text"></td>
                    <td class="textoCentro"><a href="-" title="Cambiar Item" data-accion="cambiar"><i class="fas fa-exchange-alt"></i></a></td>
                    <td class="textoCentro"><a href="-" title="Liberar Item" data-accion="liberar"><i class="fas fa-wrench"></i></a></td>
                    <td class="textoCentro"><a href="-" title="Agregar Item debajo" data-accion="agregar"><i class="far fa-calendar-plus"></i></a></td>
                </tr>`;

        if (accion == "add")
            $("#tablaDetalles tbody").append(row);
        else {
            fila_reemplazar.attr("data-grabado",0);
            fila_reemplazar.attr("data-idprod",idprod);
            fila_reemplazar.attr("data-codund",nunid);
            fila_reemplazar.find("td").eq(2).text(codigo);
            fila_reemplazar.find("td").eq(3).text(descrip);
            fila_reemplazar.find("td").eq(4).text(unidad);
        }

        return false;
    });

    $(".tituloVentana").on("click","a", function (e) {
        e.preventDefault();

        $(this).parent().parent().parent().parent().fadeOut();

        return false;
    });

    $("#save").click(function (e) { 
        e.preventDefault();
        
        let result = {};

        $.each($("#formProceso").serializeArray(),function(){
            result[this.name] = this.value;
        });

        try {
            if (result['codigo_costos'] == '') throw "Elija Centro de Costos";
            if (result['codigo_area'] == '') throw "Elija Area";
            if (result['codigo_transporte'] == '') throw "Elija Tipo de Transporte";
            if (result['concepto'] == '') throw "Escriba el concepto";
            if (result['codigo_solicitante'] == '') throw "Elija Solicitante";
            if (result['codigo_tipo'] == '') throw "Elija el tipo de pedido";
            if ($("#tablaDetalles tbody tr").length <= 0) throw "El pedido no tienes items";
            if (checkCantTables($("#tablaDetalles tbody > tr"),5)) throw "No ingreso cantidad en un item";

            $.post(RUTA+"pedidoedit/grabaPedidoAdmin",{cabecera:result,detalles:JSON.stringify(itemsSave())},
                function (data, textStatus, jqXHR) {
                    mostrarMensaje(data.mensaje,data.clase);
                    
                },
                "json"
            );

        } catch (error) {
            mostrarMensaje(error,'mensaje_error');
        }

        return false;
    });

    $("#btnAceptarLiberaItem").click(function (e) { 
        e.preventDefault();

        try {
            if ( estadoItem < 54 ) throw new Error("El item no tiene orden");

            $.post(RUTA+"pedidoedit/accionItem",{id:iditempedido,valor:1,estado:54},
                function (data, textStatus, jqXHR) {
                    //fila.remove();
                    $(this).parent().parent().parent().fadeOut();
                    fillTables($("#tablaDetalles tbody > tr"),1);
                    mostrarMensaje(data.mensaje,"mensaje_correcto");
                },
                "json"
            );

        } catch (error) {
            mostrarMensaje(error,"mensaje_error");
        }

        return false;
    });
    
    $(".claseCancela").click(function (e) { 
        e.preventDefault();
        
        $(this).parent().parent().parent().fadeOut();

        return false;
    });

    $("#btnProceso").click(function (e) { 
        e.preventDefault();
        
        let str = $("#formConsulta").serialize();

        $.post(RUTA+"pedidoedit/filtro",str,
            function (data, textStatus, jqXHR) {
                $("#tablaPrincipal tbody")
                    .empty()
                    .append(data);
            },
            "text"
        );

        return false
    });

    //filtrar Item del pedido
    $("#txtBuscarCodigo, #txtBuscarDescrip").on("keypress", function (e) {
        if(e.which == 13) {
            $("#esperar").fadeIn();
            
            $.post(RUTA+"pedidos/filtraItems", {codigo:$("#txtBuscarCodigo").val(),
                                                descripcion:$("#txtBuscarDescrip").val(),
                                                tipo:$("#codigo_tipo").val()},
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
    
})

itemsSave = () =>{
    DATA = [];
    let TABLA = $("#tablaDetalles tbody >tr");

    TABLA.each(function(){
        let IDPROD      = $(this).data('idprod'),
            UNIDAD      = $(this).data('codund'),
            CANTIDAD    = $(this).find('td').eq(5).children().val(),
            CANTAPRO    = $(this).find('td').eq(5).children().val(),
            IDX         = $(this).data('idx'),
            CALIDAD     = 0,
            ESTADO      = $(this).attr('data-grabado'),
            ESPECIFICA  = $(this).find('td').eq(6).children().val(),
            NROPARTE    = $(this).find('td').eq(7).text(),
            REGISTRO    = $(this).data('registro'),
            REGISTEXT   = $(this).find('td').eq(8).text(),
            ATENDIDA    = $(this).find('td').eq(9).children().val(),
            ASIGNA      = $("#asigna").val(),
            APRUEBA     = $("#aprueba").val(),
            FAPROBA     = $("#fecha_aprobacion").val();

        item= {};
       
        item['idprod']      = IDPROD;
        item['unidad']      = UNIDAD;
        item['cantidad']    = CANTIDAD;
        item['atendida']    = ATENDIDA;
        item['nroparte']    = NROPARTE;
        item['itempedido']  = IDX;
        item['calidad']     = CALIDAD;
        item['especifica']  = ESPECIFICA;
        item['estado']      = ESTADO;
        item['registro']    = REGISTRO;
        item['registext']   = REGISTEXT;
        item['observac']    = "";
        item['asigna']      = ASIGNA;
        item['aprueba']     = APRUEBA;
        item['faproba']     = FAPROBA;
        item['cantapro']    = CANTAPRO;


        DATA.push(item);

    })

    return DATA;
}

cambiarPedido = (idfunction,valorfunction) => {
    $.post(RUTA+"pedidoedit/cambiaPedido", {id:idfunction,valor:valorfunction},
            function (data, textStatus, jqXHR) {
                $("#preguntaAnula").fadeOut();
                $("#ventanaEspera").fadeOut();

                mostrarMensaje(data.mensaje,"mensaje_correcto");
            },
            "json"
    );
}

listarItems = (tipoPedido) => {
    $.post(RUTA+"pedidos/llamaProductos", {tipo:tipoPedido},
        function (data, textStatus, jqXHR) {
            $("#tablaModulos tbody")
                .empty()
                .append(data);
            $("#busqueda").fadeIn();
            
        },
        "text"
    );
}

