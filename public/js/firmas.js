$(function() {

    let ingresos = 0,
        swcoment = false;

    $("#esperar").fadeOut();

    $("#tablaPrincipal tbody").on("click","tr", function (e) {
        e.preventDefault();

        $.post(RUTA+"firmas/ordenId", {id:$(this).data("indice")},
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
                $("#total").val(data.cabecera[0].ntotal);
                
                if (data.bocadillo != 0) {
                    $(".button__comment")
                        .text(data.bocadillo)
                        .show();
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

                grabado = true;
            },
            "json"
        );
    
        $("#proceso").fadeIn();
    
        return false;
    });

    $("#tablaDetalles tbody").on("click","tr",function(e) {
        e.preventDefault();

        $.post(RUTA+"firmas/precios", {codigo:$(this).data("codprod")},
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
            $.post(RUTA+"firmas/comentarios", {codigo:$("#codigo_orden").val(),comentarios:JSON.stringify(comentarios())},
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

        let archivo = "OC"+$("#numero").val()+"_"+$("#codigo_costos").val()+".pdf";

        $(".ventanaVistaPrevia iframe")
            .attr("src","")
            .attr("src","public/documentos/ordenes/emitidas/"+archivo);
                    
        $("#vista_previa").val(archivo);    
        $("#vistaprevia").fadeIn();

        console.log(archivo);
        
    
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
                mostrarMensaje(data.mensaje,data.clase);
                $("#pregunta").fadeOut();
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

        $("#preguntaExpress").fadeIn();

        return false;
    });

    $("#btnAceptarPreguntaExpress").click(function(e){
        e.preventDefault(e);

        $.post(RUTA+"firmas/autorizaExpress", {id:$("#codigo_orden").val()},
            function (data, textStatus, jqXHR) {
                mostrarMensaje(data.mensaje,data.clase);
                $("#preguntaExpress").fadeOut();
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