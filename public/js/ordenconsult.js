$(function(){

    $("#espera").fadeOut();

    $('#tablaPrincipal').excelTableFilter({
        columnSelector: '.filter',
        captions: { a_to_z: 'A - Z', z_to_a: 'Z - A', search: 'Buscar', select_all: 'Seleccionar Todo' }
    });

    $(".dataProceso_2, #tablaDetalles").css("pointer-events","none");

    $("#tablaPrincipal tbody").on("click","tr", function (e) {
        e.preventDefault();

        autorizado = $(this).data('finanzas')+$(this).data('logistica')+$(this).data('operaciones');

        $.post(RUTA+"ordenedit/ordenId", {id:$(this).data("indice")},
            function (data, textStatus, jqXHR) {

                let estado = "textoCentro " + data.cabecera[0].estado;
                let total = parseFloat(data.cabecera[0].total_multiplicado).toFixed(2);
                total =  formatoNumeroConComas(total,2,'.',',');

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
                $("#total_numero").val(data.cabecera[0].total_multiplicado);
                $("#ncotiz").val(data.cabecera[0].cnumcot);
                $("#tcambio").val(data.cabecera[0].ntcambio);
                $("#referencia").val(data.cabecera[0].cReferencia);

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

                if (data.bocadillo != 0) {
                    $(".button__comment")
                        .text(data.bocadillo)
                        .show();
                }

                if (data.cabecera[0].nigv != 0) {
                    $("#si").prop("checked", true);
               }else {
                    $("#no").prop("checked", true);
               };

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

        $("#proceso").fadeOut();

        return false;
    });

    $("#btnConsult").click(function (e) { 
        e.preventDefault();

        let str = $("#formConsulta").serialize();

        $.post(RUTA+"ordenconsult/listaFiltrada",str,
            function (data, textStatus, jqXHR) {
                $("#tablaPrincipal tbody")
                    .empty()
                    .append(data);
            },
            "text"
        );
        
        return false;
    });

    $("#verDetalles").click(function (e) { 
        e.preventDefault();
        
        $.post(RUTA+"ordenseg/consulta",{id:$("#codigo_orden").val()},
            function (data, textStatus, jqXHR) {
                
                $("#fecha_documento").text(data.info[0].emision);
                $("#envio").text(data.info[0].envio);
                $("#elaborado").text(data.info[0].cnameuser);
                $("#firma_logistica").text(data.info[0].fecha_logistica);
                $("#firma_operaciones").text(data.info[0].fecha_operaciones);
                $("#firma_finanzas").text(data.info[0].fecha_finanzas);

                $("#lista_pedidos tbody")
                    .empty()
                    .append(data.pedidos);

                $("#documentos_adjuntos")
                    .empty()
                    .append(data.adjuntos);

                $("#detalles").fadeIn();
            },
            "json"
        );

        return false;
    });

    $("#closeDocument").click(function (e) { 
        e.preventDefault();

        $(".seccion4 iframe").attr("src","");
        $("#detalles").fadeOut();
        
        return false;
    });

    $("#lista_pedidos tbody").on("click","a", function (e) {
        e.preventDefault();

        $.post(RUTA+"ordenseg/vistaPedido", {id:$(this).attr("href")},
            function (data, textStatus, jqXHR){
                let archivo = RUTA+"public/documentos/temp/"+data
                $(".seccion4 iframe")
                    .attr("src","")
                    .attr("src",archivo);
            },
            "text"
        );

        return false;
    });

    $("#btnConsulta").click(function (e) { 
        e.preventDefault();
        
        let srt = $("#formConsulta").serialize();

        $.post(RUTA+"ordenseg/filtroOrdenes", srt,
            function (data, text, requestXHR) {
                $("#tablaPrincipal tbody")
                    .empty()
                    .append(data);
            },
            "text"
        );
        
        return false
    });

    $("#documentos_adjuntos").on('click','a', function(e) {
        e.preventDefault();

        let adjunto = RUTA+'/public/documentos/ordenes/adjuntos/'+$(this).attr("href");

        $(".seccion4 iframe").attr("src","").attr("src",adjunto);

        return false;
    });

    $("#preview").click(function (e) { 
        e.preventDefault();

        let result = {};
        
        $.each($("#formProceso").serializeArray(),function(){
            result[this.name] = this.value;
        })

        $.post(RUTA+"ordenedit/vistaPreliminar", {cabecera:result,condicion:0,detalles:JSON.stringify(detalles())},
                function (data, textStatus, jqXHR) {
                    $(".ventanaVistaPrevia iframe")
                        .attr("src","")
                        .attr("src","public/documentos/ordenes/vistaprevia/"+data);
                    
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

    $("#btnExporta").click(function(e){
        e.preventDefault();

        $.post(RUTA+"ordenconsult/exporta", {detalles:JSON.stringify(exports())},
            function (data, textStatus, jqXHR) {
                window.location.href = data.documento;
            },
            "json"
        );

        return false;
    });
})

exports = () => {
    DATA = [];
    let TABLA = $("#tablaPrincipal tbody >tr");

    TABLA.each(function(){
        let ITEM        = $(this).find('td').eq(0).text(),
            EMISION     = $(this).find('td').eq(1).text(),
            DESCRIPCION = $(this).find('td').eq(2).text(),
            COSTO       = $(this).find('td').eq(3).text(),
            AREA        = $(this).find('td').eq(4).text(),
            PROVEEDOR   = $(this).find('td').eq(5).text(),
            SOLES       = $(this).find('td').eq(6).text(),
            DOLARES     = $(this).find('td').eq(7).text(),
            LOGISTICA   = $(this).data('logistica'),
            OPERACIONES = $(this).data('operaciones'),
            FINANZAS    = $(this).data('finanzas');  

        item= {};
        
        item['item']         = ITEM;
        item['emision']      = EMISION;
        item['descripcion']  = DESCRIPCION;
        item['costo']        = COSTO;
        item['area']         = AREA;
        item['proveedor']    = PROVEEDOR;
        item['soles']        = SOLES;
        item['dolares']      = DOLARES;
        item['logistica']    = LOGISTICA;
        item['operaciones']  = OPERACIONES;
        item['finanzas']     = FINANZAS;
        
        DATA.push(item);
    });

    return DATA;
}