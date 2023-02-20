$(function() {
    
    $("#esperar").fadeOut();
    
    $("#btnProcesa").click(function(e){
        e.preventDefault();

        let str = $("#formConsulta").serialize();

        $("#esperar").css({"display":"block","opacity":"1"});

        $.post(RUTA+"cargoplanner/filtroCargoPlan",str,
            function (data, text, requestXHR) {
                $(".itemsCargoPlanner table tbody")
                    .empty()
                    .append(data);

                    $("#esperar").fadeOut();

            "text"
        });
        return false;
    });

    $("#btnExporta").click(function (e) { 
        e.preventDefault();

        $.post(RUTA+"cargoplanner/export", {registros:JSON.stringify(detalles())},
            function (data, textStatus, jqXHR) {
                window.location.href = data.documento;
            },
            "json"
        );

        return false;
    });

    $("#cargoPlanDescrip tbody").on('click','tr', function(e) {
        e.preventDefault();

        $("#vistadocumento").fadeIn();

        let tabla = $("#cargoPlanDescrip tbody > tr");

        $("#codigo").val(tabla.find('td').eq(12).text());
        $("#producto").val(tabla.find('td').eq(14).text());
        $("#unidad").val(tabla.find('td').eq(13).text());
        $("#cantidad").val(tabla.find('td').eq(11).text());
        $("#estado").val(tabla.find('td').eq(1).text());
        $("#nropedido").val(tabla.find('td').eq(8).text());
        $("#tipo_pedido").val(tabla.find('td').eq(6).text());
        $("#emision_pedido").val(tabla.find('td').eq(9).text());
        $("#aprobacion_pedido").val(tabla.find('td').eq(10).text());
        $("#aprobado_por").val(tabla.data("aprueba"));

        $.post(RUTA+"cargoplanner/resumen", {orden:tabla.data("orden"),refpedido:$(this).data('itempedido')},
            function (data, textStatus, jqXHR) {
                $("#tablaOrdenes tbody").empty().append(data.orden);
                $("#tablaIngresos tbody").empty().append(data.ingresos);
                $("#tablaDespachos tbody").empty().append(data.despachos);
            },
            "json"
        );

        return false;
    });

    $("#closeDocument").click(function (e) { 
        e.preventDefault();
        
        $("#vistadocumento").fadeOut();

        return false
    });
    
})

detalles = () =>{
    DATA = [];

    let TABLA = $("#cargoPlanDescrip tbody >tr");

    TABLA.each(function(){
        let ITEM                = $(this).find('td').eq(0).text(),
            ESTADO              = $(this).find('td').eq(1).text(),
            PROYECTO            = $(this).find('td').eq(2).text(),
            AREA                = $(this).find('td').eq(3).text(),
            PARTIDA             = $(this).find('td').eq(4).text(),
            ATENCION            = $(this).find('td').eq(5).text(),
            TIPO                = $(this).find('td').eq(6).text(),
            ANIO_PEDIDO         = $(this).find('td').eq(7).text(),
            NUM_PEDIDO          = $(this).find('td').eq(8).text(),
            CREA_PEDIDO         = $(this).find('td').eq(9).text(),
            APRO_PEDIDO         = $(this).find('td').eq(10).text(),
            CANTIDAD            = $(this).find('td').eq(11).text(),
            CODIGO              = $(this).find('td').eq(12).text(),
            UNIDAD              = $(this).find('td').eq(13).text(),
            DESCRIPCION         = $(this).find('td').eq(14).text(),
            TIPO_ORDEN          = $(this).find('td').eq(15).text(),
            ANIO_ORDEN          = $(this).find('td').eq(16).text(),
            NRO_ORDEN           = $(this).find('td').eq(17).text(),
            FECHA_ORDEN         = $(this).find('td').eq(18).text(),
            CANTIDAD_ORDEN      = $(this).find('td').eq(19).text(),
            ITEM_ORDEN          = $(this).find('td').eq(20).text(),
            AUTORIZA_ORDEN      = $(this).find('td').eq(21).text(),
            PROVEEDOR           = $(this).find('td').eq(22).text(),
            FECHA_ENTREGA       = $(this).find('td').eq(23).text(),
            CANTIDAD_RECIBIDA   = $(this).find('td').eq(24).text(),
            SALDO_RECIBIR       = $(this).find('td').eq(25).text(),
            DIAS_ENTREGA        = $(this).find('td').eq(26).text(),
            DIAS_ATRASO         = $(this).find('td').eq(27).text(),
            SEMAFORO            = $(this).find('td').eq(28).text(),
            DESPACHO            = $(this).find('td').eq(29).text(),
            CANTIDA_OBRA        = $(this).find('td').eq(31).text(),
            NUMERO_GUIA         = $(this).find('td').eq(30).text(),
            ESTADO_PEDIDO       = $(this).find('td').eq(33).text(),
            ESTADO_ITEM         = $(this).find('td').eq(34).text(),
            NUMERO_PARTE        = $(this).find('td').eq(35).text(),
            CODIGO_ACTIVO       = $(this).find('td').eq(36).text(),
            OPERADOR            = $(this).find('td').eq(37).text(),
            TRANSPORTE          = $(this).find('td').eq(38).text(),
            OBSERVACIONES       = $(this).find('td').eq(39).text();
          

        item = {};

        item['item']                = ITEM;
        item['estado']              = ESTADO;
        item['proyecto']            = PROYECTO;
        item['area']                = AREA;
        item['partida']             = PARTIDA;
        item['atencion']            = ATENCION;
        item['tipo']                = TIPO;
        item['anio_pedido']         = ANIO_PEDIDO;
        item['num_pedido']          = NUM_PEDIDO;
        item['crea_pedido']         = CREA_PEDIDO;
        item['apro_pedido']         = APRO_PEDIDO;
        item['codigo']              = CODIGO;
        item['unidad']              = UNIDAD;
        item['descripcion']         = DESCRIPCION;
        item['cantidad']            = CANTIDAD;
        item['tipo_orden']          = TIPO_ORDEN;
        item['anio_orden']          = ANIO_ORDEN;
        item['nro_orden']           = NRO_ORDEN;
        item['fecha_orden']         = FECHA_ORDEN;
        item['proveedor']           = PROVEEDOR;
        item['fecha_entrega']       = FECHA_ENTREGA;
        item['cantidad_recibida']   = CANTIDAD_RECIBIDA;
        item['saldo_recibir']       = SALDO_RECIBIR;
        item['dias_entrega']        = DIAS_ENTREGA;
        item['dias_atraso']         = DIAS_ATRASO;
        item['semaforo']            = SEMAFORO;
        item['cantidad_obra']       = CANTIDA_OBRA;
        item['estado_pedido']       = ESTADO_PEDIDO;
        item['estado_item']         = ESTADO_ITEM;
        item['numero_parte']        = NUMERO_PARTE;
        item['codigo_activo']       = CODIGO_ACTIVO;
        item['operador']            = OPERADOR;
        item['transporte']          = TRANSPORTE;
        item['observaciones']       = OBSERVACIONES;
        item['cantidad_orden']      = CANTIDAD_ORDEN;
        item['despacho']            = DESPACHO;
        item['item_orden']          = ITEM_ORDEN;
        item['autoriza_orden']      = AUTORIZA_ORDEN;
        item['numero_guia']         = NUMERO_GUIA;
        
        DATA.push(item);
    })

    return DATA;
}

function s2ab(s) {

    var buf = new ArrayBuffer(s.length);
    var view = new Uint8Array(buf);
    for (var i=0; i<s.length; i++) view[i] = s.charCodeAt(i) & 0xFF;
    return buf;
}
