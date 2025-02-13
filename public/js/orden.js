$(function(){
    let accion   = "",
        entidad  = "",
        pedido   = 0,
        proforma = "",
        ingresos = 0,
        swcoment = false,
        atencion = '',
        costos = "",
        fp = 0,
        idorden = 0,
        datafiltro = "",
        fila="";

    var grabado  = false;

    $("#esperar").fadeOut();

    $("#tablaPrincipal tbody").on("click","tr", function (e) {
        e.preventDefault();

        autorizado = $(this).data('finanzas')+$(this).data('logistica')+$(this).data('operaciones');

        $.post(RUTA+"orden/ordenId", {id:$(this).data("indice")},
            function (data, textStatus, jqXHR) {

                let estado = "textoCentro " + data.cabecera[0].estado;
                let total = parseFloat(data.cabecera[0].total_multiplicado).toFixed(2);
                let total_format =  formatoNumeroConComas(total,2,'.',',');
                
                let adicionales = 0;
                let adicionales_format = '0.00';
                
                if  ( data.total_adicionales !== null ){
                    adicionales = parseFloat(data.total_adicionales).toFixed(2);
                    adicionales_format = formatoNumeroConComas(adicionales,2,'.',',');
                }

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
                $("#direccion_almacen").val(data.cabecera[0].direccion);
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
                $("#total").val(total_format);
                $("#tipo").val(data.cabecera[0].tipo);
                $("#fentrega").val(data.cabecera[0].ffechaent);
                $("#cpago").val(data.cabecera[0].pagos);
                $("#estado").val(data.cabecera[0].descripcion_estado);
                $("#entidad").val(data.cabecera[0].crazonsoc);
                $("#atencion").val(data.cabecera[0].cnombres);
                $("#transporte").val(data.cabecera[0].transporte);
                $("#lentrega").val(data.cabecera[0].lentrega);
                $("#total_numero").val(data.cabecera[0].total_multiplicado);
                $("#ncotiz").val(data.cabecera[0].cnumcot);
                $("#tcambio").val(data.cabecera[0].ntcambio);
                $("#user_modifica").val(data.cabecera[0].userModifica);
                $("#user_genera").val(data.cabecera[0].usuario);
                $("#nro_pedido").val(data.cabecera[0].nrodoc);
                $("#total_adicional").val(data.total_adicionales);
                $("#oa").val(adicionales);
                $("#referencia").val(data.cabecera[0].cReferencia);
                $("#dias").val(data.cabecera[0].nplazo);
                $("#nivel_autorizacion").val(data.cabecera[0].autorizado);
                $("#procura").val(data.cabecera[0].nfirmaLog);
                $("#finanzas").val(data.cabecera[0].nfirmaFin);
                $("#operaciones").val(data.cabecera[0].nfirmaOpe);
                
                $("#in").val(total_format);

                let igv = 0;

                if (data.cabecera[0].nigv != 0) {
                        igv = parseFloat(data.cabecera[0].total_multiplicado)*.18;
                        $("#si").prop("checked", true);
                        $("#im").val(igv.toFixed(2));
                }else {
                        $("#no").prop("checked", true);
                        $("#im").val(0);
                };

                let total_orden = parseFloat(total)+parseFloat(igv)+parseFloat(adicionales),
                    total_orden_format = formatoNumeroConComas(total_orden,2,'.',',');

                $("#it").val(total_orden_format);

                $("#estado")
                    .removeClass()
                    .addClass(estado);

                $("#tablaDetalles tbody")
                    .empty()
                    .append(data.detalles);

                $("#tablaComentarios tbody")
                    .empty()
                    .append(data.comentarios);


                $("#tablaAdicionales tbody")
                    .empty()
                    .append(data.adicionales);

                $("#atach_counter").text(data.total_adjuntos);

                $("#sw").val(1);

                if ( data.bocadillo != 0) {
                    $(".button__comment")
                        .text(data.bocadillo)
                        .show();
                }else{
                    $(".button__comment").hide();
                }

                $("#proceso").fadeIn();

                $(".filtro").fadeOut();
            },
            "json"
        );
    
        accion      = "u";
        grabado     = true;
        ingresos    = 0
        swcoment    = false;

        return false;
    });

    $("#tablaPrincipal tbody").on("click","a", function (e) {
        e.preventDefault();

        try {
            let procura     = $(this).closest('tr').attr("data-logistica"),
                operaciones = $(this).closest('tr').attr("data-operaciones"),
                finanzas    = $(this).closest('tr').attr("data-finanzas"),
                firmas      = procura == 0 || operaciones == 0 || finanzas == 0;

            atencion    = $(this).closest('tr').attr("data-atencion");    

            if ( atencion == 47 && firmas ) throw new Error("La orden esta en firmas");
            
            idorden = $(this).attr("href");
            fila =  $(this).parent().parent();

            $("#preguntaDescarga").fadeIn();

        } catch (error) {
            mostrarMensaje(error.message,"mensaje_error");
        }

        return false;
    });

    $("#btnAceptarDescarga").click(function(e){
        e.preventDefault();

        $("#preguntaDescarga").fadeOut();
        
        createPdf(idorden,fila,atencion);
        
        
        return false;
    });

    $("#btnCancelarDescarga").click(function(e){
        e.preventDefault();
        $("#preguntaDescarga").fadeOut();
        return false;
    });

    $("#nuevoRegistro").click(function (e) { 
        e.preventDefault();

        $("#estado")
            .removeClass()
            .addClass("textoCentro estado procesando");

        $("#proceso").fadeIn();
        $("#sw").val(0);
        $("#codigo_estado").val(0);
        $(".button__comment").hide();
        $("#atach_counter").text(0);
        $(".listaArchivos").empty();

        $("#formProceso input[type='hidden']").each(function(){
            $(this).val("");
        });

        $("#cpago").val("CREDITO A 30 DIAS");
        $("#codigo_pago").val(73);

        $("#tablaAdicionales tbody").empty();

        accion = 'n';
        grabado = false;
        costos = "";

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

    $("#closeProcess").click(function (e) { 
        e.preventDefault();

        $.post(RUTA+"orden/actualizaListado",
            function (data, textStatus, jqXHR) {
                $(".itemsTabla table tbody")
                    .empty()
                    .append(data);

                $("#proceso").fadeOut(function(){
                    grabado = false;
                    $("form")[0].reset();
                    $("form")[1].reset();
                    $("#tablaDetalles tbody").empty();
                });
            },
            "text"
        );

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
                compra      = parseFloat($(this).data("compra"),2),
                atendida    = parseFloat($(this).data("atendida"),2),
                tabPos      = $("#tablaDetalles tr").length;

                cantidad_final = compra - atendida;
            
            
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
                                data-descrip    ="${descrip}"
                                data-atendido   ="${atendida}">
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
                    
                    if ( $("#numero").val() === "")
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

    $("#preview").click(function (e) { 
        e.preventDefault();
        
        try {
            let result = {};
    
            $.each($("#formProceso").serializeArray(),function(){
                result[this.name] = this.value;
            })
    
            if (result['numero'] == "") throw "No tiene numero de orden";
            if (!grabado) throw new Error ("Por favor grabe la orden");
            if (result['fentrega'] == "") throw "Elija la fecha de entrega";
            if (result['codigo_transporte'] == "") throw "Elija la forma de transporte";
            //if (result['codigo_almacen'] == "") throw "Indique el lugar de entrega";
            if (!grabado) throw "Por favor grabar el documento";
            

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

        if ( !swcoment) {
            $.post(RUTA+"orden/comentarios", {codigo:$("#codigo_orden").val(),
                                              comentarios:JSON.stringify(comentarios()),
                                              usuario:$("#id_user").val()},
                function (data, textStatus, jqXHR) {
                    swcoment = true;
                },
                "text"
            );
        }

        return false
    });

    $("#saveOrden").click(function (e) { 
        e.preventDefault();

        let result = {};
    
        $.each($("#formProceso").serializeArray(),function(){
            result[this.name] = this.value;
        })

        formData = new FormData();
        formData.append("usuario",$("#id_user").val());
        formData.append("cabecera",JSON.stringify(result));
        formData.append("detalles",JSON.stringify(detalles()));
        formData.append("comentarios",JSON.stringify(comentarios()));
        formData.append("adicionales",JSON.stringify(adicionales()));

        try {
            if ( accion == "" ) throw "Orden grabada";
            if ($("#codigo_estado").val() == 59) throw "La orden esta en firmas.";
            if (result['numero'] == "") throw "No tiene numero de orden";
            if (result['dias'] == "") throw "ingrese el numero de dias";
            if (result['codigo_moneda'] == "") throw "Elija la moneda";
            if (result['codigo_pago'] == "") throw "Elija el tipo de pago";
            //if (result['codigo_almacen'] == "") throw "Indique el lugar de entrega";
            if (result['total'] == "") throw "No se registro el total de la orden";
            if ($("#tablaDetalles tbody tr") .length <= 0) throw "No tiene items cargados";
            if ($("#id_user").val() <= "") throw "Error General";

            grabado = true;
            
            if ( accion == 'n' ){
                $.ajax({
                    // URL to move the uploaded image file to server
                    url: RUTA + 'orden/nuevoRegistro',
                    // Request type
                    type: "POST", 
                    // To send the full form data
                    data: formData,
                    contentType:false,      
                    processData:false,
                    dataType:"json",    
                    // UI response after the file upload
                    beforeSend: function () {
                        $("#esperar").fadeIn();
                    },  
                    success: function(response)
                    {   
                        mostrarMensaje(response.mensaje,response.clase);
                        $("#esperar").fadeOut();
                        $("#tablaDetalles tbody tr").attr('data-grabado',1);
                        $("#codigo_orden").val(response.orden);
                        $("#tablaPrincipal tbody")
                            .empty()
                            .append(response.pedidos);
                            
                            accion = ' ';
                    }
                });
            }else if ( accion == 'u' ){
                $.post(RUTA+"orden/modificaRegistro", { cabecera:result,
                                                        detalles:JSON.stringify(detalles()),
                                                        comentarios:JSON.stringify(comentarios()),
                                                        usuario:$("#id_user").val()},
                    function (data, textStatus, jqXHR) {
                        $("#tablaDetalles tbody tr").attr('data-grabado',1);
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

        try {
            if ($("#codigo_estado").val() == 59) throw "La orden esta en firmas.";
            if (!grabado) throw "Por favor grabar la orden";

            $("#subject").val($("#entidad").val() + ' - ' + $("#numero").val());

            $.post(RUTA+"orden/buscaRol", {rol:$(this).data("rol"),documento:"o"},
                function (data, textStatus, jqXHR) {
                    $("#listaCorreos tbody").empty().append(data);
                    $("#sendMail").fadeIn();
                },
                "text"
            );
        } catch (error) {
            mostrarMensaje(error,'mensaje_error'); 
        }
        
        return false;
    });

    $("#closeMail").click(function (e) { 
        e.preventDefault();

        $("form")[2].reset();
        $(".atachs").empty();
        $(".messaje div").empty();
        $("#sendMail").fadeOut();

        return false;
    });

    $("#btnConfirmSend").click(function (e) { 
        e.preventDefault();
        
        try {
            if ($("#subject").val() =="") throw "Escriba el asunto";
            if ($("messaje div").html() =="") throw "Escriba el asunto";

            let result = {};
    
            $.each($("#formProceso").serializeArray(),function(){
                result[this.name] = this.value;
            })

            $("#esperar").css("opacity","1").fadeIn();

            $.post(RUTA+"orden/correo", {cabecera:result,
                                        detalles:JSON.stringify(detalles()),
                                        correos:JSON.stringify(mailsList()),
                                        asunto:$("#subject").val(),
                                        mensaje:$(".messaje div").html()},
                                                
            function (data, textStatus, jqXHR) {
                mostrarMensaje(data.mensaje,data.clase);
                $("#sendMail").fadeOut();
                $("#esperar").css("opacity","0").fadeOut();
            },
            "json"
        );
        } catch (error) {
            mostrarMensaje(error,'mensaje_error');
        }

        return false;
    });

    $("#sendEntOrden").click(function (e) { 
        e.preventDefault();

        try {
            ///ojo con la orden urgente

            if ( ($("#procura").val() != 1 || $("#finanzas").val() != 1 || $("#operaciones").val() != 1 ) && $("#nivel_autorizacion").val() === "47" ) throw "La orden no ha sido autorizada";

            let result = {};

            //$("#fentrega").val(sumarDias( parseInt( $("#dias").val() ) ) );
    
            $.each($("#formProceso").serializeArray(),function(){
                result[this.name] = this.value;
            });

            $.post(RUTA+"orden/envioOrden", {cabecera:result,
                                            detalles:JSON.stringify(detalles())},
                function (data, textStatus, jqXHR) {
                    mostrarMensaje(data.mensaje,data.clase);
                    $("#tablaPrincipal tbody")
                        .empty()
                        .append(data.ordenes);
                    $("#esperar").fadeOut();
                },
                "json"
            );

        } catch (error) {
            mostrarMensaje(error,'mensaje_error');
        }

        return false;
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

    $("#tablaDetalles tbody").on('click',"a", function(e) {
        e.preventDefault();

        if ($("#codigo_estado").val() == 59){
            mostrarMensaje("La orden no se puede modificar","mensaje_error");
            return false;
        }

        let item    = $(this).parent().parent().remove();
        let suma = 0;

        $.post(RUTA+"orden/marcaItem", {id:$(this).parent().parent().data("itped"),
                                        io:$(this).parent().parent().data("itord"),
                                        "estado":0},
            function (data, text, requestXHR) {
                item.remove();
                fillTables($("#tablaDetalles tbody > tr"),1);
            },                        
            "text"
        );

        $("#tablaDetalles tbody  > tr").each(function () {
            suma += parseFloat($(this).find('td').eq(7).text()||0,10);
        })

        if(suma > 0) {
            $("#total").val(numberWithCommas(suma.toFixed(2)));
            $("#total_numero").val(suma.toFixed(2));

        }

        return false;
    });

    $("#tablaDetalles tbody").on('click',".consultaPrecios", function (e) {
        e.preventDefault();

          $.post(RUTA+"firmas/precios", {codigo:$(this).parent().data("codprod"),descripcion:$(this).parent().data("descrip")},
            function (data, text, requestXHR) {
                $("#tablaPrecios tbody")
                    .empty()
                    .append(data);

                $("#consultaprecios").fadeIn();
            }
            ,"text"
        );       

        return false;
    });

    $("#closePrices").click(function (e) { 
        e.preventDefault();
        
        $("#consultaprecios").fadeOut();

        return false;	
    });

    //filtrado en la lista de solicitante
    $(".busqueda").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $(this).next().attr("id");

        //aignar a una variable el contenido
        let l = "#"+ $(this).next().attr("id")+ " li a"

        $(l).filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });

    $("#uploadCotiz").click(function(e){
        e.preventDefault();

        try {

            if ( $("#codigo_orden").val().length == 0 ) throw new Error ("Por favor grabe la orden");

            if ( parseInt($("#atach_counter").text()) > 0 ){

                $.post(RUTA+"orden/listarAdjuntos", {orden:$("#codigo_orden").val(),tipo:"ORD"},
                    function (data, text, requestXHR) {
                        $(".listaArchivos")
                            .empty()
                            .append(data.adjuntos);
                            $("#archivos").fadeIn();
                    },
                    "json"
                );
            }else {
                $("#archivos").fadeIn();
            }
            
            
        } catch (error) {
            mostrarMensaje(error,"mensaje_error");
        }

        return false;
    });

    $("#openArch").click(function (e) { 
        e.preventDefault();
        
        $("#uploadAtach").trigger("click");
 
        return false;
    });

    $("#uploadAtach").on("change", function (e) {
        e.preventDefault();
 
        fp = $(this);
        let lg = fp[0].files.length;
        let items = fp[0].files;
        let fragment = "";
 
        if (lg > 0) {
             for (var i = 0; i < lg; i++) {
                 var fileName = items[i].name; // get file name
 
                 // append li to UL tag to display File info
                 fragment +=`<li><p><i class="far fa-file"></i></p>
                                 <p>${fileName}</p></li>`;
             }
 
             $(".listaArchivos").append(fragment);
        }
 
        return false;
    });
 
    $("#btnConfirmAtach").on("click", function (e) {
         e.preventDefault();

        let formData = new FormData();

        formData.append('codigo',$("#codigo_orden").val());

        $.each($('#uploadAtach')[0].files, function(i, file) {
            formData.append('file-'+i, file);
        });

        $.ajax({
            type: "POST",
            url: RUTA+"orden/archivos",
            data: formData,
            data: formData,
            contentType:false,      
            processData:false,
            dataType: "json",
            success: function (response) {
                $("#atach_counter").text(response.adjuntos);
                $("#archivos").fadeOut();
                $("#fileAtachs")[0].reset();
            }
        });

        return false;
    });
 
    $("#btnCancelAtach").on("click", function (e) {
         e.preventDefault();
 
         $("#archivos").fadeOut();
         $("#fileAtachs")[0].reset();
         $(".listaArchivos").empty();
 
    });

    $("#btnConsulta").on('click', function(e) {
        e.preventDefault();

        let str = $("#formConsulta").serialize();

        $.post(RUTA+"orden/filtroOrden", str,
            function (data, text, requestXHR) {
                $("#tablaPrincipal tbody")
                    .empty()
                    .append(data);
            },
            "text"
        );
        
        return false
    });

    $("#itemCostos").change(function (e) { 
        e.preventDefault(e);

        $.post(RUTA+"orden/ItemsPorCostos", {costo:$(this).val()},
            function (data, textStatus, jqXHR) {
                $("#pedidos tbody")
                    .empty()
                    .append(data);
            },
            "text"
        );

        return false        
    });

    $("#addCharges").click(function(e){
        e.preventDefault();
        
        try {
            if ($("#codigo_costos").val() == "") throw "Faltan datos en la orden";
            if ($("#codigo_moneda").val() == "") throw "Seleccione el tipo de moneda";

            $("#adicionales").fadeIn();
            
        } catch (error) {
            mostrarMensaje(error,"mensaje_error");
        }
        
        return false;
    });

    $("#btnCancelAdic").on("click", function (e) {
        e.preventDefault();

        $("#tablaAdicionales tbody").empty();
        $("#adicionales").fadeOut();

        return false;
    });

    $("#btnConfirmAdic").on("click", function (e) {
        e.preventDefault();

        $("#adicionales").fadeOut();

        $("#total_adicional").val(sumarAdicionales($("#tablaAdicionales tbody >tr"),2));

        calcularTotales();

        return false;
    });

    $("#addAdic").click(function (e) { 
        e.preventDefault();
        
        let moneda = $("#moneda").val();

        let row =  `<tr class="pointer">
                        <td><input type="text" class="pl20px mayusculas"></td>
                        <td class="textoCentro">${moneda}</td>
                        <td><input type="number" class="textoDerecha"></td>
                        <td class="textoCentro"><a href="#"></a><i class="fas fa-minus"></i></td>
                    </tr>`;

        $("#tablaAdicionales tbody")
            .append(row);

        return false;
    });

    //cuando presiona el icono
    $(".listaArchivos").on("click",'.icono_archivo', function (e) {
        e.preventDefault();

        console.log('No hace nada');

        return false;
    });

    $(".listaArchivos").on("click",'.file_delete', function (e) {
        e.preventDefault();

        $(this).parent().remove();

        $.post(RUTA+"pedidos/borraAdjunto", {codigo:$(this).attr("href")},
            function (data, text, requestXHR) {
                if (data.respuesta) {
                    mostrarMensaje("Registro Eliminado", "mensaje_correcto");
                    $("#atach_counter").text($(".listaArchivos li").length);
                }else {
                    mostrarMensaje("Error al eliminar", "mensaje_error");
                }
            },
            "json"
        );

        return false;
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

    $(".cabezaModulo,.barraTrabajo").on('click','*', function() {
        $(".filtro").fadeOut();
    });

})


detalles = () => {
    DATA = [];
    let TABLA = $("#tablaDetalles tbody >tr");

    TABLA.each(function(){
        let ITEM        = $(this).find('td').eq(1).text(),
            CODIGO      = $(this).find('td').eq(2).text(),
            DESCRIPCION = $(this).find('td').eq(3).text(),
            UNIDAD      = $(this).find('td').eq(4).text(),
            CANTIDAD    = $(this).find('td').eq(5).children().val(),
            PRECIO      = $(this).find('td').eq(6).children().val(),
            IGV         = 0,
            TOTAL       = $(this).find('td').eq(7).text(),
            NROPARTE    = $(this).find('td').eq(8).text(),
            PEDIDO      = $(this).find('td').eq(9).text(),
            CODPROD     = $(this).data('codprod'),
            MONEDA      = $("#codigo_moneda").val(),
            ITEMPEDIDO  = $(this).data('itped'),
            GRABAR      = $(this).data('grabado'),
            CANTPED     = $(this).data('cant'),
            REFPEDI     = $(this).data('refpedi'),
            DETALLES    = $(this).find('td').eq(10).children().val(),
            INDICE     = $(this).data('itord');

        item = {};
        
        //if (GRABAR == 0) {
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
            item['grabado']     = GRABAR;
            item['cantped']     = CANTPED;
            item['refpedi']     = REFPEDI;
            item['detalles']    = DETALLES;
            item['indice']      = INDICE;

            DATA.push(item);
        //}
    });

    return DATA;
}

comentarios = () => {
    let COMENTARIOS = [];

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

mailsList = () => {
    CORREOS = [];

    let TABLA =  $("#listaCorreos tbody >tr");

    TABLA.each(function(){
        let CORREO      = $(this).find('td').eq(1).text(),
            NOMBRE      = $(this).find('td').eq(0).text(),
            ENVIAR      = $(this).find('td').eq(2).children().prop("checked"),

        item= {};
        
        if (ENVIAR) {
            item['nombre']= NOMBRE;
            item['correo']= CORREO;

            CORREOS.push(item);
        }
        
    })

    return CORREOS;
}

adicionales = () => {
    ADICIONALES = [];

    let TABLA = $("#tablaAdicionales tbody >tr");

    TABLA.each(function (){
        let ENTIDAD      = $("#codigo_entidad").val(),
            MONEDA       = $("#codigo_moneda").val(),
            DESCRIPCION  = $(this).find('td').eq(0).children().val(),
            VALOR        = $(this).find('td').eq(2).children().val();

        item = {};

        item['entidad']     = ENTIDAD;
        item['moneda']      = MONEDA;
        item['descripcion'] = DESCRIPCION;
        item['valor']       = VALOR;

        ADICIONALES.push(item);
    });

    return ADICIONALES;
}


//funcion para sumar eliminando el problema de las comas 
sumarAdicionales = (TABLA,indice) =>{
    let sum = 0;

    TABLA.each(function() {  
        sum += parseFloat($(this).find('td').eq(indice).children().val().replace(/,/g, ''), 10);  
    }); 
       
    return sum.toFixed(2);
}

calcularTotales = () => {
    let im = 0,
        adic = parseFloat(sumarAdicionales($("#tablaAdicionales tbody >tr"),2)),
        np   = $("#total_numero").val();

        if ($("#total_numero").val() == 0) {
            $("#im").val("0.00");
        }else {
            im = parseFloat($("#total_numero").val())*parseFloat($('input[name="radioIgv"]:checked').val());
        }

    suma_total = parseFloat(np)+parseFloat(im)+parseFloat(adic);

    $("#im").val(im.toFixed(2));
    $("#oa").val(adic);
    $("#it").val((numberWithCommas(suma_total.toFixed(2))));
}

sumardias = () => {
    let fecha = new Date(),
        dias = parseInt( $('#dias').val() ),
        diaSemana = ['Domingo','Lunes','Martes','Miercoles','Jueves','Viernes','Sabado']

    fecha.setDate( fecha.getDate() + dias );

    if ( fecha.getDay() === 0 || fecha.getDay() === 6 ) {
        fecha = new Date();
        fecha.setDate( fecha.getDate() + ( dias + 2 ) );

        mostrarMensaje("La fecha de entrega se cambio un dia utíl","mensaje_correcto");
    }

    fecha = fecha.getFullYear() + '-' + $.strPad((fecha.getMonth() + 1),2) + '-' +  $.strPad(fecha.getDate(),2);
    
    $("#fentrega").val(fecha);

}

function DownloadFromUrl(fileURL, fileName) {
    var link = document.createElement('a');
    link.href = fileURL;
    link.download = fileName;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}


function createPdf(id,fila,atencion){
    let formData = new FormData();
    formData.append("id",id);

    $("#esperar").css("opacity","1").fadeOut();

    fetch(RUTA+'orden/descargaRapida',{
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        $("#esperar").css("opacity","0").fadeOut();
        DownloadFromUrl(data.ruta, data.archivo);
        if (atencion == 47)
            fila.remove();
    });
}








