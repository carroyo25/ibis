$(() =>{
    let accion   = "",
        grabado  = false,
        entidad  = "",
        pedido   = 0,
        proforma = "",
        ingresos = 0,
        swcoment = false,
        autorizado = 0,
        costos = "",
        fp = 0;

    $("#esperar").fadeOut();

    $("#nuevoRegistro").click(function (e) { 
        e.preventDefault();

        $("#proceso").fadeIn();

        accion = 'n';

        return false;
    });

    $("#closeProcess").click(function (e) { 
        e.preventDefault();

        $("#proceso").fadeOut();

        return false;
    });

    $("#loadRequest").click(function (e) { 
        e.preventDefault();

        if ($("#codigo_estado").val() == 59){
            mostrarMensaje("La orden no se puede modificar","mensaje_error");
            return false;
        }
            
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

        if (costos == "" ) {
            costos = $(this).data("costos");
            pedido = $(this).data("pedido");
        }

        try {
            if ( costos  != $(this).data("costos")) throw "El item esta otro centro de costos";
            //if ( pedido  != $(this).data("pedido")) throw "El item esta en  un pedido diferente";

            let nFilas      = $.strPad($("#tablaDetalles tr").length,3),
                codigo      = $(this).children('td:eq(5)').text(),
                request     = $(this).data("pedido"),
                nroreq      = $(this).children('td:eq(0)').text(),
                descrip     = $(this).children('td:eq(8)').text(),
                cantidad    = $(this).children('td:eq(6)').text(),
                unidad      = $(this).data("unidad"),
                detalle     = $(this).data("detalle"),
                total       = 0,
                cod_prod    = $(this).data("codprod"),
                id_item     = $(this).data("iditem"),
                nro_parte   = $(this).data("nparte"),
                grabado     = 0,
                compra      = $(this).data("compra"),
                tabPos      = $("#tablaDetalles tr").length;

            $("#nro_pedido").val(nroreq);

            if (!checkExistTable($("#tablaDetalles tbody tr"),codigo,5)){
                let item = $(this);
                let row = `<tr data-grabado     ="${grabado}" 
                                data-total      ="${total}" 
                                data-codprod    ="${cod_prod}" 
                                data-itPed      ="${id_item}"
                                data-cant       ="${cantidad}"
                                data-refpedi    ="${request}"
                                data-nro_parte  ="${nro_parte}"
                                data-descrip    ="${descrip}">
                            <td class="textoCentro"><a href="#"><i class="fas fa-ban"></i></a></td>
                            <td class="textoCentro">${nFilas}</td>
                            <td class="textoCentro consultaPrecios">${codigo}</td>
                            <td class="pl20px">${descrip}</td>
                            <td class="textoCentro">${unidad}</td>
                            <td class="textoDerecha pr5px">
                                <input type="number"
                                    step="any" 
                                    placeholder="0.00" 
                                    onchange="(function(el){el.value=parseFloat(el.value).toFixed(4);})(this)"
                                    onclick="this.select()"
                                    value="${compra}">
                            </td>
                            <td class="textoDerecha pr5px precio">
                                <input type="number" class="focusNext"
                                    step="any" 
                                    placeholder="0.00" 
                                    onchange="(function(el){el.value=parseFloat(el.value).toFixed(4);})(this)"
                                    onclick="this.select()"
                                    tabIndex="${tabPos}">
                            </td>
                            <<td class="textoDerecha pr5px"></td>
                            <td class="textoCentro">${nro_parte}</td>
                            <td class="textoCentro">${nroreq}</td>
                            <td class="pl20px"><textarea>${detalle}</textarea></td>
                        </tr>`;


                $.post(RUTA+"orden/marcaItem", {id:$(this).data("iditem"),"estado":1,io:$(this).data("itord")},
                    function (data, text, requestXHR) {
                        item.remove();
                        $("#tablaDetalles tbody").append(row);
                    },                        
                    "text"
                );
                
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
            if ($("#tablaDetalles tbody").length == 0) throw "No se selecciono ningún item";
            
            $.post(RUTA+"orden/datosPedido", {pep:pedido,prof:proforma,ent:entidad},
                function (data, textStatus, jqXHR) {

                    $("#codigo_pedido").val(data.pedido[0].idreg);
                    $("#codigo_costos").val(data.pedido[0].idcostos);
                    $("#codigo_area").val(data.pedido[0].idarea);
                    $("#codigo_transporte").val(data.pedido[0].idtrans);
                    $("#codigo_tipo").val(data.pedido[0].idtipomov);
                    $("#costos").val(data.pedido[0].proyecto);
                    $("#area").val(data.pedido[0].area);
                    $("#concepto").val(data.pedido[0].concepto);
                    $("#detalle").val(data.pedido[0].detalle);
                    $("#tipo").val(data.pedido[0].tipo);
                    $("#pedidopdf").val(data.pedido[0].docPdfAprob);
                    $("#nivel_atencion").val(data.pedido[0].nivelAten);
                    $("#tcambio").val(data.cambio);
                    
                    $("#numero").val(data.orden);
                    $("#codigo_verificacion").val(data.pedido[0].verificacion);
                    
                    $("#busqueda").fadeOut(); 
                },
                "json"
            );
        } catch (error) {
            mostrarMensaje(error,"mensaje_error");
        };

        return false;
    });

    $(".mostrarLista").focus(function (e) { 
        e.preventDefault();

        if ($("#codigo_estado").val() == 59)
            return false;

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
            $("#direccion_almacen").val($(this).data('direccion'));
            $("#lentrega").val($(this).text());
        }else if (contenedor_padre == "listaTransporte"){
            $("#codigo_transporte").val(codigo);
        }else if (contenedor_padre == "listaMoneda"){
            $("#codigo_moneda").val(codigo);
        }else if (contenedor_padre == "listaPago"){
            $("#codigo_pago").val(codigo);
        }else if (contenedor_padre == "listaEntidad"){
            $("#codigo_entidad").val(codigo);
            $("#ruc_entidad").val($(this).data("ruc"));
            $("#direccion_entidad").val($(this).data("direccion"));

            $.post(RUTA+"orden/detallesEntidad",{"codigo": $(this).data("ruc")},
                function (data, textStatus, jqXHR) {
                    $("#atencion").val(data[0].contacto);
                    $("#direccion_entidad").val(data[0].cviadireccion);
                    $("#telefono_entidad").val(data[0].ctelefono);
                    $("#correo_entidad").val(data[0].correo_entidad);
                },
                "json"
            );
        }

        return false;
    });

    $(".busqueda").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $(this).next().attr("id");

        //aignar a una variable el contenido
        let l = "#"+ $(this).next().attr("id")+ " li a"

        $(l).filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });

    $("#tablaDetalles tbody").on('keypress','input', function (e) {
        if (e.which == 13) {
            try {
                let cant = $(this).parent().parent().find("td").eq(5).children().val();
                let precio = $(this).parent().parent().find("td").eq(6).children().val();
                let suma = 0;
                let igv = parseFloat($('input[name="radioIgv"]:checked').val());
                
                let total = precio*cant;

                $(this).parent().parent().find("td").eq(7).text(total.toFixed(2));

                $("#tablaDetalles tbody  > tr").each(function () {
                    suma += parseFloat($(this).find('td').eq(7).text()||0,10);
                })

                if(suma > 0) {

                    $("#total").val(numberWithCommas(suma.toFixed(2)));
                    $("#total_numero").val(suma.toFixed(2));
                    $("#in").val(numberWithCommas(suma.toFixed(2)));

                    calcularTotales();
                }

                //para cambiar el foco con el enter

                cb = parseInt($(this).attr('tabindex'));

                if ($(':input[tabindex=\'' + (cb + 1) + '\']') != null) {
                    $(':input[tabindex=\'' + (cb + 1) + '\']').focus();
                    $(':input[tabindex=\'' + (cb + 1) + '\']').select();
                }

            } catch (error) {
                
            }
        }
    });

    //cuando se cambia el boton del igv
    $('input[name="radioIgv"]').change(function (e) { 
        e.preventDefault();
        if ( $("#total_numero").val() > 0 )
            calcularTotales();

        return false;
    });

    //sumar dias
    $("#dias").blur(function (e) { 
        e.preventDefault();
        
        sumardias();

        return false;
    });

    //muestra la lista para entregas
    $("#btnEntrega").click(function (e) { 
        e.preventDefault();
        
        $(this).next().slideDown();

        return false;
    });
})