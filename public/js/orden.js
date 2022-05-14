$(function(){
    let accion   = "",
        grabado  = false,
        entidad  = "",
        pedido   = 0,
        proforma = "",
        moneda   = "",
        cmoneda  = "",
        pago     = "";

    $("#nuevoRegistro").click(function (e) { 
        e.preventDefault();

        $("#estado")
            .removeClass()
            .addClass("textoCentro estado procesando");
        $("#proceso").fadeIn();
        accion = 'n';

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
                                data-itPed="${proforma}"
                                data-profroma="${id_item}">
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
                    $("#costos").val(data.pedido[0].proyecto);
                    $("#area").val(data.pedido[0].area);
                    $("#concepto").val(data.pedido[0].concepto);
                    $("#detalle").val(data.pedido[0].detalle);
                    $("#tipo").val(data.pedido[0].tipo);
                    $("#pedidopdf").val(data.pedido[0].docPdfAprob);
                    
                    $("#numero").val(data.orden.numero);
                    $("#moneda").val(cmoneda);
                    $("#cpago").val(pago);

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
        
        let date = fechaActual();
        
        let row = `<tr>
                        <td data-grabar="1"></td>
                        <td><input type="date" value="${date}" readonly></td>
                        <td><input type="text" placeholder="Escriba su comentario"" ></td>
                        <td class="con_borde centro"><a href="#"><i class="far fa-trash-alt"></i></a></td>
                    </tr>`
        
        $('#tablaComentarios > tbody tr:eq(0)').before(row);


        $("#comentarios").fadeIn();

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
            ITEMPEDIDO  = $(this).data('iditem');

        item= {};
        
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
        item['itempedido']  = ITEMPEDIDO;
       

        DATA.push(item);
    })

    return DATA;
}


