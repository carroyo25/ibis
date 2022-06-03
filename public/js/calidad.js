$(function(){
    var accion = "";
    var index = "";

    $("#esperar").fadeOut();

    $("#tablaPrincipal tbody").on("click","tr", function (e) {
        e.preventDefault();

        $.post(RUTA+"calidad/consultaId", {id:$(this).data("indice")},
            function (data, textStatus, jqXHR) {
                
                let estado = "textoCentro w100por estado " + data.cabecera[0].estado;
                
                $("#codigo_costos").val(data.cabecera[0].ncodpry);
                $("#codigo_area").val(data.cabecera[0].ncodarea);
                $("#codigo_movimiento").val(data.cabecera[0].ncodmov);
                $("#codigo_aprueba").val(data.cabecera[0].id_userAprob);
                $("#codigo_almacen").val(data.cabecera[0].ncodalm1);
                $("#codigo_pedido").val(data.cabecera[0].idref_pedi);
                $("#codigo_orden").val(data.cabecera[0].idref_abas);
                $("#codigo_estado").val(data.cabecera[0].nEstadoDoc);
                $("#codigo_entidad").val(data.cabecera[0].id_centi);
                $("#codigo_ingreso").val(data.cabecera[0].id_regalm);
                $("#almacen").val(data.cabecera[0].almacen);
                $("#fecha").val(data.cabecera[0].ffecdoc);
                $("#numero").val(data.cabecera[0].nnronota);
                $("#proyecto").val(data.cabecera[0].proyecto);
                $("#area").val(data.cabecera[0].area);
                $("#solicita").val(data.cabecera[0].nombres);
                $("#orden").val(data.cabecera[0].orden);
                $("#pedido").val(data.cabecera[0].pedido);
                $("#ruc").val(data.cabecera[0].cnumdoc);
                $("#guia").val(data.cabecera[0].cnumguia);
                $("#razon").val(data.cabecera[0].crazonsoc);
                $("#concepto").val(data.cabecera[0].concepto);
                $("#detalle").val(data.cabecera[0].detalle);
                $("#aprueba").val(data.cabecera[0].cnombres);
                $("#tipo").val(data.cabecera[0].cdescripcion);
                $("#estado").val(data.cabecera[0].estado);
                $("#movimiento").val(1);

                let swqaqc = data.cabecera[0].nflgCalidad == 1 ? true: false;
                
                $("#qaqc").prop("checked",swqaqc);
                
                $("#estado")
                    .removeClass()
                    .addClass(estado);
                
                
                $("#tablaDetalles tbody")
                    .empty()
                    .append(data.detalles);
            },
            "json"
        );

        accion = "u";
        grabado = true;
        $("#proceso").fadeIn();

        return false;
    });

    $("#saveDocument").click(function (e) { 
        e.preventDefault();

        $.post(RUTA+"calidad/grabaCalidad", {detalles:JSON.stringify(detalles())},
            function (data, textStatus, jqXHR) {
                console.log(data);
            },
            "text"
        );

        return false
    });

    $("#freeDocument").click(function (e) { 
        e.preventDefault();
        
        $.post(RUTA+"calidad/liberaNota", {id:$("#codigo_ingreso").val(),estado:60,detalles:JSON.stringify(detalles())},
            function (data, textStatus, jqXHR) {
                console.log(data);
            },
            "text"
        );

        return false;
    });

    $("#closeProcess").click(function (e) { 
        e.preventDefault();

        $("#proceso").fadeOut();

        $.post(RUTA+"calidad/actualizaNotas",
            function (data, textStatus, jqXHR) {
                $(".itemsTabla table tbody")
                    .empty()
                    .append(data);

                $("#proceso").fadeOut(function(){
                    $("form")[0].reset();
                    $("form")[1].reset();
                    $("#tablaDetalles tbody,.listaArchivos").empty();
                });
            },
            "text"
        );

        return false;
    });

    $("#tablaDetalles tbody").on("click","a", function (e) {
        e.preventDefault();


        return false;
    });
})

detalles = () =>{
    DETALLES = [];

    let TABLA = $("#tablaDetalles tbody >tr");
    
    TABLA.each(function(){
        let ITEM        = $(this).find('td').eq(1).text(),
            IDDETORDEN  = $(this).data("detorden"),
            IDDETPED    = $(this).data("iddetped"),
            IDDETNOTA   = $(this).data("iddetnota")
            IDPROD      = $(this).data("idprod"),
            PEDIDO      = $("#codigo_pedido").val(),
            ORDEN       = $("#codigo_orden").val(),
            ALMACEN     = $("#codigo_almacen").val(),
            CANTSOL     = parseFloat($(this).find('td').eq(5).text()),
            CANTREC     = $(this).find('td').eq(6).children().val(),// cantidad
            OBSER       = $(this).find('td').eq(7).children().val(),
            VENCE       = $(this).find('td').eq(8).children().val(),
            CODIGO      = $(this).find('td').eq(2).text(),//codigo
            DESCRIPCION = $(this).find('td').eq(3).text(),//descripcion
            UNIDAD      = $(this).find('td').eq(4).text(),//unidad
            NESTADO     = $(this).find("select[name='estado']").val(),
            CESTADO     = $(this).find("select[name='estado'] option:selected").text(),
            UBICACION   = "";
    
        item = {};

        item['item']        = ITEM;
        item['iddetorden']  = IDDETORDEN;
        item['iddetped']    = IDDETPED;
        item['idprod']      = IDPROD;
        item['pedido']      = ORDEN;
        item['orden']       = PEDIDO;
        item['almacen']     = ALMACEN;
        item['cantrec']     = CANTREC;
        item['obser']       = OBSER;
        item['vence']       = VENCE;
        item['cantsol']     = CANTSOL;
        item['iddetnota']   = IDDETNOTA;

        item['codigo']     = CODIGO;
        item['descripcion']= DESCRIPCION;
        item['unidad']     = UNIDAD;
        item['nestado']    = NESTADO;
        item['cestado']    = CESTADO;
        item['ubicacion']  = UBICACION;

        DETALLES.push(item);
    })

    return DETALLES; 
}