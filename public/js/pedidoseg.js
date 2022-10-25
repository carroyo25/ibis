$(function(){
    $("#tablaPrincipal tbody").on("click","tr", function (e) {
        e.preventDefault();

        $.post(RUTA+"pedidoseg/seguimientoID", {id:$(this).data("indice")},
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
               
                if (data.cabecera[0].idtipomov == 38) {
                    $("#requestAprob").removeClass("desactivado");
                    $("#sendItem").addClass("desactivado");
                }else {
                    if ( data.cabecera[0].veralm == 0 ){
                        $("#requestAprob").removeClass("desactivado");
                        $("#sendItem").addClass("desactivado");
                    }else {
                        $("#requestAprob").addClass("desactivado");
                        $("#sendItem").removeClass("desactivado");
                    }                    
                }

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

        /*$.post(RUTA+"pedidoseg/actualizaListado",
            function (data, textStatus, jqXHR) {
                $(".itemsTabla table tbody")
                    .empty()
                    .append(data);

                $("#proceso").fadeOut(function(){
                   
                    $("form")[0].reset();
                    $("form")[1].reset();
                    $("#tablaDetalles tbody,.listaArchivos").empty();
                    $(".lista").fadeOut();

                });
            },
            "text"
        );*/

        $("#proceso").fadeOut(function(){
            $("form")[0].reset();
            $("form")[1].reset();
            $("#tablaDetalles tbody,.listaArchivos").empty();
            $(".lista").fadeOut();

        });

        return false;
    });

    $("#closePreview").click(function (e) { 
        e.preventDefault();

        $(".ventanaVistaPrevia iframe").attr("src","");
        $("#vistaprevia").fadeOut();

        return false;
    });

    $("#preview").click(function (e) { 
        e.preventDefault();
    
        let result = {};
        let ruta = $("#codigo_estado").val() == 49 ? "public/documentos/pedidos/vistaprevia/":"public/documentos/pedidos/emitidos/";

        $.each($("#formProceso").serializeArray(),function(){
            result[this.name] = this.value;
        })

        $.post(RUTA+"pedidos/vistaprevia", {cabecera:result,detalles:JSON.stringify(itemsPreview())},
                function (data, textStatus, jqXHR) {
                    $(".ventanaVistaPrevia iframe")
                    .attr("src","")
                    .attr("src",ruta+data);

                    $("#vista_previa").val(data);

                    $("#vistaprevia").fadeIn();
                },
            "text"
        );
        
        return false;
    });

    $("#verDetalles").click(function(e){
        e.preventDefault();

        $.post(RUTA+"pedidoseg/infoPedido", {id:$("#codigo_pedido").val()},
            function (data, textStatus, jqXHR) {
                $("#tableInfo tbody").find('tr').eq(1).find('td').eq(1).children().text(data.pedido);
                $("#tableInfo tbody").find('tr').eq(1).find('td').eq(3).children().text(data.emision);
                $("#tableInfo tbody").find('tr').eq(2).find('td').eq(1).children().text(data.costos);
                $("#tableInfo tbody").find('tr').eq(3).find('td').eq(1).children().text(data.elaborado);
                $("#tableInfo tbody").find('tr').eq(4).find('td').eq(1).children().text($("#tablaDetalles tbody tr").length);

                if(data.aprobador != null) {
                    $("#tableInfo tbody").find('tr').eq(6).find('td').eq(1).children().text(data.aprobador);
                    $("#tableInfo tbody").find('tr').eq(7).find('td').eq(1).children().text(data.aprobacion);
                }

                $("#detalles").fadeIn();
            },
            "json"
        );

        return false;
    });

    $("#cerrarDetalles").click(function(e){
        e.preventDefault();

        $("#detalles").fadeOut();

        return false;
    });
})

itemsPreview = () =>{
    DATA = [];
    let TABLA = $("#tablaDetalles tbody >tr");

    TABLA.each(function(){
        let ITEM        = $(this).find('td').eq(1).text(),
            CODIGO      = $(this).find('td').eq(2).text(),
            DESCRIPCION = $(this).find('td').eq(3).text(),
            UNIDAD      = $(this).find('td').eq(4).text(),
            CANTIDAD    = $(this).find('td').eq(5).children().val(),
            ESPECIFICA  = $(this).find('td').eq(6).children().val();
            ITEMPEDIDO  = $(this).data('idx'),
            OBSERVAC    = "",
            NROPARTE    = $(this).find('td').eq(7).text(),

        item= {};
        
        item['item']        = ITEM;
        item['codigo']      = CODIGO;
        item['descripcion'] = DESCRIPCION;
        item['unidad']      = UNIDAD;
        item['cantidad']    = CANTIDAD;
        item['especifica']  = ESPECIFICA;
        item['itempedido']  = ITEMPEDIDO;
        item['observac']    = OBSERVAC;
        item['atendida']    = 0;
        item['nroparte']    = NROPARTE;

        DATA.push(item);
    })

    return DATA;
}