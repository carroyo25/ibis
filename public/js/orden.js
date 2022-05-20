$(function(){
    let accion   = "",
        grabado  = false,
        entidad  = "",
        pedido   = 0,
        proforma = "",
        moneda   = "",
        cmoneda  = "",
        pago     = "";
        ingresos = 0;
    
    $("#esperar").fadeOut();

    $("#tablaPrincipal tbody").on("click","tr", function (e) {
        e.preventDefault();

        $.post(RUTA+"orden/ordenId", {id:$(this).data("indice")},
            function (data, textStatus, jqXHR) {

                let estado = "textoCentro " + data.cabecera[0].estado;

                $("#codigo_costos").val(data.cabecera[0].ncodcos);
                $("#codigo_area").val(data.cabecera[0].ncodarea);
                $("#codigo_transporte").val(data.cabecera[0].ctiptransp);
                $("#codigo_tipo").val(data.cabecera[0].ntipmov);
                $("#codigo_almacen").val(data.cabecera[0].ncodalm);
                $("#codigo_pedido").val(data.cabecera[0].id_refpedi);
                $("#codigo_orden").val(data.cabecera[0].id_regmov);
                $("#codigo_estado").val(data.cabecera[0].nEstadoDoc);
                $("#codigo_entidad").val(data.cabecera[0].id_centi);
                $("#codigo_moneda").val(data.cabecera[0].ncodmon);
                $("#codigo_pago").val(data.cabecera[0].ncodpago);
                $("#ruc_entidad").val(data.cabecera[0].cnumdoc);
                $("#direccion_entidad").val(data.cabecera[0].cviadireccion);
                $("#telefono_entidad").val(data.cabecera[0].ctelefono1);
                $("#correo_entidad").val(data.cabecera[0].mail_entidad);
                $("#codigo_verificacion").val(data.cabecera[0].cverificacion);
                $("#telefono_contacto").val(data.cabecera[0].ctelefono1);
                $("#correo_contacto").val(data.cabecera[0].cemail);
                $("#proforma").val(data.cabecera[0].cnumcot);
                $("#retencion").val(data.cabecera[0].nagenret);
                $("#nivel_atencion").val(data.cabecera[0].nivelAten);
                $("#numero").val(data.cabecera[0].cnumero);
                $("#emision").val(data.cabecera[0].ffechadoc);
                $("#costos").val(data.cabecera[0].costos);
                $("#area").val(data.cabecera[0].area);
                $("#concepto").val(data.cabecera[0].concepto);
                $("#detalle").val(data.cabecera[0].detalle);
                $("#moneda").val(data.cabecera[0].nombre_moneda);
                $("#total").val();
                $("#tipo").val(data.cabecera[0].tipo);
                $("#fentrega").val(data.cabecera[0].ffechaent);
                $("#cpago").val(data.cabecera[0].pagos);
                $("#estado").val(data.cabecera[0].descripcion_estado);
                $("#entidad").val(data.cabecera[0].crazonsoc);
                $("#atencion").val(data.cabecera[0].cnombres);
                $("#transporte").val(data.cabecera[0].transporte);
                $("#lentrega").val(data.cabecera[0].cdesalm);
                $("#total").val(data.total);

                $("#estado")
                    .removeClass()
                    .addClass(estado);

                $("#tablaDetalles tbody")
                    .empty()
                    .append(data.detalles);

                $("#tablaComentarios tbody")
                    .empty()
                    .append(data.comentarios);

                $("#sw").val(1);
            },
            "json"
        );
    
        accion = "u";
        $("#proceso").fadeIn();
    
        return false;
    });

    $("#nuevoRegistro").click(function (e) { 
        e.preventDefault();

        $("#estado")
            .removeClass()
            .addClass("textoCentro estado procesando");
        $("form")[0].reset();
        $("#proceso").fadeIn();
        $("#sw").val(0);

        accion = 'n';

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

        if (contenedor_padre == "listaAlmacen"){
            $("#codigo_almacen").val(codigo);
        }else if (contenedor_padre == "listaTransporte"){
            $("#codigo_transporte").val(codigo);
        }

        return false;
    });

    $("#closeProcess").click(function (e) { 
        e.preventDefault();

        $("#proceso").fadeOut(function(){
            /*grabado = false;
            $("form")[0].reset();
            $("form")[1].reset();
            $("#tablaDetalles tbody,.listaArchivos").empty();*/
        });

        /*$.post(RUTA+"pedidos/actualizaListado",
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

    $("#loadRequest").click(function (e) { 
        e.preventDefault();
        
        $("#esperar").fadeIn();

        $.post(RUTA+"orden/pedidos",
            function (data, textStatus, jqXHR) {
                $("#esperar").fadeOut(function(e){
                    $("#busqueda").fadeIn();
                    $("#pedidos tbody")
                        .empty()
                        .append(data);
                });
            },
            "text"
        );
        
        return false;
    });

    $(".tituloVentana").on("click","a", function (e) {
        e.preventDefault();

        $(this).parent().parent().parent().parent().fadeOut();

        return false;
    });

    $("#pedidos tbody").on("click","tr", function (e) {
        e.preventDefault();

        if (pedido == "" ) {
            pedido      = $(this).data("pedido");
            entidad     = $(this).data("entidad");
            proforma    = $(this).data("proforma");
            moneda      = $(this).data("moneda");
            cmoneda     = $(this).data("desmoneda");
            pago        = $(this).data("pago");
        }

        try {
            if ( pedido  != $(this).data("pedido")) throw "El item esta en otro pedido";
            if ( entidad != $(this).data("entidad")) throw "No se puede asignar una orden a dos proveedores";
            if ( moneda  != $(this).data("moneda")) throw "Los items en el pedido tiene monedas distintas"; 

            let nFilas      = $.strPad($("#tablaDetalles tr").length,3),
                codigo      = $(this).children('td:eq(5)').text(),
                descrip     = $(this).children('td:eq(6)').text(),
                unidad      = $(this).data("unidad"),
                cantidad    = $(this).data("cantidad"),
                precio      = $(this).data("precio"),
                igv         = $(this).data("igv"),
                total       = $(this).data("total"),
                nroparte    = $(this).data("nroparte"),
                request     = $.strPad($(this).data("pedido"),6),
                abrmomeda   = $(this).data("abrmoneda"),
                cod_prod    = $(this).data("codprod"),
                id_item     = $(this).data("iditem"),
                proforma    = $(this).data("nroprofoma")
                grabado     = 0;


            if (!checkExistTable($("#tablaDetalles tbody tr"),codigo,1)){
                $("#proforma").val(proforma);
                
                let row = `<tr data-grabado="${grabado}" 
                                data-total="${total}" 
                                data-codprod="${cod_prod}" 
                                data-itPed="${id_item}"
                                data-profroma="${proforma}">
                    <td class="textoCentro">${nFilas}</td>
                    <td class="textoCentro">${codigo}</td>
                    <td class="pl20px">${descrip}</td>
                    <td class="textoCentro">${unidad}</td>
                    <td class="textoDerecha pr5px">${cantidad}</td>
                    <td class="textoDerecha pr5px">${precio}</td>
                    <td class="textoDerecha pr5px">${igv}</td>
                    <td class="textoDerecha pr5px">${abrmomeda} ${total}</td>
                    <td class="textoCentro">${nroparte}</td>
                    <td class="textoCentro">${request}</td>
               </tr>`;

               $("#tablaDetalles tbody").append(row)
            }else{
                mostrarMensaje("Item duplicado","mensaje_error");
            }

        } catch (error) {
            mostrarMensaje(error,'mensaje_error');
        }

        return false;
    });

    $("#btnAceptItems").click(function (e) { 
        e.preventDefault();

        try {
            if (pedido == 0) throw "No se selecciono ningún item";
            
            $.post(RUTA+"orden/datosPedido", {pep:pedido,prof:proforma,ent:entidad},
                function (data, textStatus, jqXHR) {

                    $("#codigo_pedido").val(data.pedido[0].idreg);
                    $("#codigo_costos").val(data.pedido[0].idcostos);
                    $("#codigo_area").val(data.pedido[0].idarea);
                    $("#codigo_transporte").val(data.pedido[0].idtrans);
                    $("#codigo_tipo").val(data.pedido[0].idtipomov);
                    $("#codigo_estado").val(data.pedido[0].estadodoc);
                    $("#codigo_moneda").val(moneda);
                    $("#codigo_pago").val(data.proforma[0].ccondpago);
                    $("#costos").val(data.pedido[0].proyecto);
                    $("#area").val(data.pedido[0].area);
                    $("#concepto").val(data.pedido[0].concepto);
                    $("#detalle").val(data.pedido[0].detalle);
                    $("#tipo").val(data.pedido[0].tipo);
                    $("#pedidopdf").val(data.pedido[0].docPdfAprob);
                    $("#nivel_atencion").val(data.pedido[0].nivelAten);
                    
                    $("#numero").val(data.orden.numero);
                    $("#moneda").val(cmoneda);
                    $("#cpago").val(pago);
                    $("#codigo_verificacion").val(data.orden.codigo);

                    $("#entidad").val(data.entidad[0].crazonsoc);
                    $("#atencion").val(data.entidad[0].contacto);
                    $("#ruc_entidad").val(data.entidad[0].cnumdoc);
                    $("#codigo_entidad").val(data.entidad[0].id_centi);
                    $("#direccion_entidad").val(data.entidad[0].cviadireccion);
                    $("#telefono_entidad").val(data.entidad[0].ctelefono);
                    $("#retencion").val(data.entidad[0].nagenret);
                    $("#correo_entidad").val(data.entidad[0].correo_entidad);
                    $("#correo_contacto").val(data.entidad[0].correo_contacto);
                    $("#telefono_contacto").val(data.entidad[0].telefono_contacto)

                    let totalOrden = sumarTotales($("#tablaDetalles tbody tr"));
                    $("#total").val(totalOrden.toFixed(2)); 
                },
                "json"
            );
        } catch (error) {
            mostrarMensaje(error,"mensaje_error");
        };

        return false;
    });

    $("#preview").click(function (e) { 
        e.preventDefault();
        
        try {
            let result = {};
    
            $.each($("#formProceso").serializeArray(),function(){
                result[this.name] = this.value;
            })
    
            if (result['numero'] == "") throw "No tiene numero de orden";
            if (result['fentrega'] == "") throw "Elija la fecha de entrega";
            if (result['codigo_transporte'] == "") throw "Elija la forma de transporte";
            if (result['codigo_almacen'] == "") throw "Indique el lugar de entrega";

            $.post(RUTA+"orden/vistaPreliminar", {cabecera:result,condicion:0,detalles:JSON.stringify(detalles())},
                function (data, textStatus, jqXHR) {
                    $(".ventanaVistaPrevia iframe")
                        .attr("src","")
                        .attr("src","public/documentos/ordenes/vistaprevia/"+data);
                    
                    $("#vista_previa").val(data);    
                    $("#vistaprevia").fadeIn();
                },
                "text"
            );
            
        } catch (error) {
            mostrarMensaje(error,'mensaje_error');
        }

        return false;
    });

    $("#closePreview").click(function (e) { 
        e.preventDefault();

        $(".ventanaVistaPrevia iframe").attr("src","");
        $("#vistaprevia").fadeOut();

        return false;
    });

    $("#addMessage").click(function (e) { 
        e.preventDefault();
        

        let date = fechaActual(),
            usuario = $("#name_user").val();
        
        let row = `<tr data-grabar="0">
                        <td >${usuario}</td>
                        <td><input type="date" value="${date}" readonly></td>
                        <td><input type="text" placeholder="Escriba su comentario"></td>
                        <td class="con_borde centro"><a href="#"><i class="far fa-trash-alt"></i></a></td>
                    </tr>`;


        if (ingresos == 0) {
            if ($("#tablaComentarios tbody tr").length <= 0)
                $("#tablaComentarios tbody").append(row);
            else{
                $('#tablaComentarios > tbody tr:eq(0)').before(row);
            }

            ingresos++;
        }
        
        $("#comentarios").fadeIn();

        return false;
    });

    $("#btnAceptarDialogo").click(function (e) { 
        e.preventDefault();
        
        $("#comentarios").fadeOut();

        return false
    });

    $("#saveOrden").click(function (e) { 
        e.preventDefault();

        try {
            let result = {};
    
            $.each($("#formProceso").serializeArray(),function(){
                result[this.name] = this.value;
            })

            if (result['numero'] == "") throw "No tiene numero de orden";
            if (result['fentrega'] == "") throw "Elija la fecha de entrega";
            if (result['codigo_transporte'] == "") throw "Elija la forma de transporte";
            if (result['codigo_almacen'] == "") throw "Indique el lugar de entrega";
            if ($("#tablaDetalles tbody tr") .length <= 0) throw "No tiene productos seleccionados"

            if ( accion == 'n' ){
                $.post(RUTA+"orden/nuevoRegistro", {cabecera:result,
                                                    detalles:JSON.stringify(detalles()),
                                                    comentarios:JSON.stringify(comentarios())},
                    function (data, textStatus, jqXHR) {
                        mostrarMensaje(data.mensaje,data.clase);
                    },
                    "json"
                );
            }else {
                $.post(RUTA+"orden/modificaRegistro", {cabecera:result,
                                                        detalles:JSON.stringify(detalles()),
                                                        comentarios:JSON.stringify(comentarios())},
                    function (data, textStatus, jqXHR) {
                        mostrarMensaje(data.mensaje,data.clase);
                    },
                    "json"
                );
            }

        } catch (error) {
            mostrarMensaje(error,'mensaje_error'); 
        }

        return false;
    });

    $("#requestAprob").click(function (e) { 
        e.preventDefault();

        $("#sendMail").fadeIn();
        
        return false;
    });
})


detalles = () => {
    DATA = [];
    let TABLA = $("#tablaDetalles tbody >tr");

    TABLA.each(function(){
        let ITEM        = $(this).find('td').eq(0).text(),
            CODIGO      = $(this).find('td').eq(1).text(),
            DESCRIPCION = $(this).find('td').eq(2).text(),
            UNIDAD      = $(this).find('td').eq(3).text(),
            CANTIDAD    = $(this).find('td').eq(4).text(),
            PRECIO      = $(this).find('td').eq(5).text(),
            IGV         = $(this).find('td').eq(6).text(),
            TOTAL       = $(this).find('td').eq(7).text(),
            NROPARTE    = $(this).find('td').eq(8).text(),
            PEDIDO      = $(this).find('td').eq(9).text(),
            CODPROD     = $(this).data('codprod'),
            MONEDA      = $("#codigo_moneda").val(),
            ITEMPEDIDO  = $(this).data('itped');
            GRABAR      = $(this).data('grabado');

        item= {};
        
        if (GRABAR == 0) {
            item['item']        = ITEM;
            item['codigo']      = CODIGO;
            item['descripcion'] = DESCRIPCION;
            item['unidad']      = UNIDAD;
            item['cantidad']    = CANTIDAD;
            item['precio']      = PRECIO;
            item['igv']         = IGV;
            item['total']       = TOTAL;
            item['nroparte']    = NROPARTE;
            item['pedido']      = PEDIDO;
            item['codprod']     = CODPROD;
            item['moneda']      = MONEDA;
            item['itped']       = ITEMPEDIDO;
            item['grabado']     = GRABADO;

            DATA.push(item);
        }
    });

    return DATA;
}

comentarios = () => {
    COMENTARIOS = [];

    let TABLA = $("#tablaComentarios tbody >tr");

    TABLA.each(function (){
        let USUARIO     = $("#id_user").val(),
            FECHA       = $(this).find('td').eq(1).children().val(),
            COMENTARIO  = $(this).find('td').eq(2).children().val(),
            GRABAR      = $(this).data("grabar");

        item = {};

        if ( GRABAR == "0" && COMENTARIO !=""){
            item['usuario']     = USUARIO;
            item['fecha']       = FECHA;
            item['comentario']  = COMENTARIO;
            item['grabar']      = GRABAR;

            COMENTARIOS.push(item);
        }

        
    });

    return COMENTARIOS;
}

