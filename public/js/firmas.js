$(function() {

    let ingresos = 0,
        swcoment = false;

    $("#esperar").fadeOut();

    $("#tablaPrincipal tbody").on("click","tr", function (e) {
        e.preventDefault();

        $("#estado_firmas").val($(this).data('firmas'));

        $.post(RUTA+"firmas/ordenId", {id:$(this).data("indice")},
            function (data, textStatus, jqXHR) {

                let estado = "textoCentro " + data.cabecera[0].estado;
                let total = parseFloat(data.cabecera[0].total_multiplicado).toFixed(2);

                total = formatoNumeroConComas(total,2,'.',',');

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
                $("#total").val(total);
                $("#tipo").val(data.cabecera[0].tipo);
                $("#fentrega").val(data.cabecera[0].ffechaent);
                $("#cpago").val(data.cabecera[0].pagos);
                $("#estado").val(data.cabecera[0].descripcion_estado);
                $("#entidad").val(data.cabecera[0].crazonsoc);
                $("#atencion").val(data.cabecera[0].cnombres);
                $("#transporte").val(data.cabecera[0].transporte);
                $("#lentrega").val(data.cabecera[0].cdesalm);
                $("#nro_pedido").val(data.cabecera[0].nrodoc);
                $("#referencia").val(data.cabecera[0].cReferencia);
                $("#dias").val(data.cabecera[0].nplazo);

                $("#direccion_almacen").val(data.cabecera[0].direccion);
                $("#ncotiz").val(data.cabecera[0].cnumcot);
                $("#radioIgv").val(data.cabecera[0].nigv);
                $("#total_numero").val(data.cabecera[0].total_multiplicado);
                $("#pedidopdf").val(data.cabecera[0].docPdfAprob);
                $("#total_adicional").val(data.total_adicionales);
                
                if (data.bocadillo != 0) {
                    $(".button__comment")
                        .text(data.bocadillo)
                        .show();
                }else {
                    $(".button__comment").hide();
                }

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

                $("#listaAdjuntos")
                    .empty()
                    .append(data.adjuntos.adjuntos);

                $("#listaAdjuntos .file_delete").hide();

                grabado     = true;
                ingresos    = 0
                swcoment    = false;

                $("#proceso").fadeIn();
            },
            "json"
        );
    
        return false;
    });

    $("#tablaDetalles tbody").on("click","tr",function(e) {
        e.preventDefault();

        $.post(RUTA+"firmas/precios", {codigo:$(this).data("codprod"), descripcion :$(this).find('td').eq(3).text()},
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

    $("#vistaAdjuntos").on("click","a", function (e) {
        e.preventDefault();
        
        $(".ventanaAdjuntos iframe")
            .attr("src","")
            .attr("src","public/documentos/ordenes/adjuntos/"+$(this).attr("href"));
        
        return false;
    });

    $("#closeProcess").click(function (e) { 
        e.preventDefault();

        $.post(RUTA+"firmas/actualizaListado",
            function (data, textStatus, jqXHR) {
                $(".itemsTabla table tbody")
                    .empty()
                    .append(data);

                $("#proceso").fadeOut(function(){
                    $("form")[0].reset();
                });
            },
            "text"
        );
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

        if ($("#codigo_estado").val() == 59 && !swcoment) {
            $.post(RUTA+"firmas/comentarios", {codigo:$("#codigo_orden").val(),
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

    $("#preview").click(function (e) { 
        e.preventDefault();

        let result = {};
    
        $.each($("#formProceso").serializeArray(),function(){
            result[this.name] = this.value;
        })

        $.post(RUTA+"firmas/vistaEmitida", {cabecera:result,condicion:1,detalles:JSON.stringify(detalles())},
                function (data, textStatus, jqXHR) {
                    $(".ventanaVistaPrevia iframe")
                        .attr("src","")
                        .attr("src","public/documentos/ordenes/emitidas/"+data);
                    
                    $("#vista_previa").val(data);    
                    $("#vistaprevia").fadeIn();
                },
                "text"
            );
    
        return false;
    });

    $("#closePreview").click(function (e) { 
        e.preventDefault();

        $(".ventanaVistaPrevia iframe").attr("src","");
        $("#vistaprevia").fadeOut();

        return false;
    });

    $("#closePrices").click(function (e) { 
        e.preventDefault();

        $("#consultaprecios").fadeOut();

        return false;
    });

    $("#requestAprob").click(function (e) { 
        e.preventDefault();
        
        $("#pregunta").fadeIn();

        return false;
    });

    $("#btnAceptarPregunta").click(function (e) { 
        e.preventDefault();

        $.post(RUTA+"firmas/autoriza", {id:$("#codigo_orden").val()},
            function (data, textStatus, jqXHR) {
                $(".itemsTabla table tbody").empty().append(data.listado);
                mostrarMensaje(data.mensaje,data.clase);
                $("#pregunta,#proceso").fadeOut();
            },
            "json"
        );

        return false;
    });

    $("#btnCancelarPregunta").click(function (e) { 
        e.preventDefault();
        
        $("#pregunta").fadeOut();

        return false;
    });

    $("#verCotizacion").click(function(e){
        e.preventDefault();

        $("#vistaAdjuntos").fadeIn();

        return false
    });

    $("#closeAtach").click(function(e){
        e.preventDefault();

        $("#vistaAdjuntos").fadeOut();
        $(".ventanaAdjuntos iframe").attr("src","");

        return false;
    });

    $("#culminarAprobaciones").click(function(e){
        e.preventDefault();

        try {
            if ($("#estado_firmas").val() == 3) throw "La orden ya esta autorizada";

            $("#preguntaExpress").fadeIn();
        } catch (error) {
            mostrarMensaje(error,"mensaje_error");    
        }

        return false;
    });

    $("#btnAceptarPreguntaExpress").click(function(e){
        e.preventDefault(e);

        $("#esperar").css("opacity","1").fadeIn();

        $.post(RUTA+"firmas/autorizaExpress", {id:$("#codigo_orden").val(),numero:$("#numero").val(),proveedor:$("#entidad").val(),pago:$("#cpago").val()},
            function (data, textStatus, jqXHR) {
                mostrarMensaje(data.mensaje,data.clase);

                $("#preguntaExpress").fadeOut();
                $("#esperar").css("opacity","0").fadeOut();  
                
            },
            "json"
        );
        
        return false;
    });

    $("#btnCancelarPreguntaExpress").click(function(e){
        e.preventDefault(e);

        $("#preguntaExpress").fadeOut();

        return false;
    });

    $("#btnConsulta").on('click', function(e) {
        e.preventDefault();

        let str = $("#formConsulta").serialize();

        $.post(RUTA+"firmas/filtroFirmas", str,
            function (data, text, requestXHR) {
                $("#tablaPrincipal tbody")
                    .empty()
                    .append(data);
            },
            "text"
        );
        
        return false
    });

    $("#verPedido").on('click', function(e) {
        e.preventDefault();

        $(".ventanaVistaPrevia iframe").attr("src","public/documentos/pedidos/aprobados/"+$("#pedidopdf").val());
        $("#vistaprevia").fadeIn();

        return false;
    });
     
})

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
            REFPEDI      = $(this).data('refpedi'),
            DETALLES    = $(this).find('td').eq(10).children().val();

        item= {};
        
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

            DATA.push(item);
        //}
    });

    return DATA;
}