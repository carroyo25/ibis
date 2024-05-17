$(()=>{
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
                    
                    if  ( data.total_adicionales > 0 ){
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
                    $("#nro_pedido").val(data.cabecera[0].nrodoc);
                    $("#total_adicional").val(data.total_adicionales);
                    $("#oa").val(adicionales_format);
                    $("#referencia").val(data.cabecera[0].cReferencia);
                    $("#dias").val(data.cabecera[0].nplazo);
                    $("#nivel_autorizacion").val(data.cabecera[0].autorizado);
                    $("#procura").val(data.cabecera[0].nfirmaLog);
                    $("#finanzas").val(data.cabecera[0].nfirmaFin);
                    $("#operaciones").val(data.cabecera[0].nfirmaOpe);
                    $("#description_conditions").val(data.cabecera[0].condiciones);
                    
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

                    $(".primeraBarra").css("background","#0078D4");
                    $(".primeraBarra span").text("Datos Generales");
                },
                "json"
            );
        
            accion      = "u";
            grabado     = true;
            ingresos    = 0
            swcoment    = false;

            return false;
    });

    $("#closeProcess").click(function (e) { 
        e.preventDefault();

        $("#formProceso input[type='hidden']").each(function(){
            $(this).val("");
        });

        $.post(RUTA+"contratoconsult/actualizaListado",
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

    $("#preview").click(function (e) { 
        e.preventDefault();
        
        try {
            let result = {};
    
            $.each($("#formProceso").serializeArray(),function(){
                result[this.name] = this.value;
            })
    
            if (result['numero'] == "") throw "No tiene numero de orden";
            if (result['fentrega'] == "") throw "Elija la fecha de entrega";
            if (result['codigo_almacen'] == "") throw "Indique el lugar de entrega";
            if (!grabado) throw "Por favor grabar el documento";

            $.post(RUTA+"contratos/vistaPreliminar", {cabecera:result,condicion:2,detalles:JSON.stringify(detalles()),condiciones:$("#description_conditions").val()},
                function (data, textStatus, jqXHR) {
                    $(".ventanaVistaPrevia iframe")
                        .attr("src","")
                        .attr("src","public/documentos/ordenes/aprobadas/"+data);
                    
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
            PAYMENT     = $(this).find('td').eq(11).text(),
            CODPROD     = $(this).data('codprod'),
            MONEDA      = $("#codigo_moneda").val(),
            ITEMPEDIDO  = $(this).data('itped'),
            GRABAR      = $(this).attr('data-grabado'),
            CANTPED     = $(this).data('cant'),
            REFPEDI     = $(this).data('refpedi'),
            DETALLES    = $(this).find('td').eq(10).children().val(),
            INDICE      = $(this).data('itord');

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
            item['payment']     = PAYMENT;

            DATA.push(item);
        //}
    });

    return DATA;
}