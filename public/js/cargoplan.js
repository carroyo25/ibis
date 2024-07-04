$(function() {
    let idpedido = "";
    
    $("#esperar").fadeOut();
    
    $("#btnProcesa").click(function(e){
        e.preventDefault();

        let str = $("#formConsulta").serialize();

        $("#esperar").css({"display":"block","opacity":"1"});

        $.post(RUTA+"cargoplan/filtroCargoPlanConPrecio",str,
            function (data, text, requestXHR) {
                $(".itemsCargoPlanner table tbody")
                    .empty()
                    .append(data);

                    $("#esperar").fadeOut();

            "text"
        });
        return false;
    });

    $("#cargoPlanDescrip tbody").on('click','tr', function(e) {
        e.preventDefault();

        $(this).toggleClass('semaforoNaranja');

        return false;
    });

    $("#btnExporta").click(function (e) { 
        e.preventDefault();

        $("#esperar").css("opacity","1").fadeIn();

        $.post(RUTA+"cargoplan/crearExcel", {registros:JSON.stringify(detalles())},
            function (data, textStatus, jqXHR) {

                $("#esperar").css("opacity","0").fadeOut();
                window.location.href = data.documento;
                
            },
            "json"
        );

        return false;
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
            APROBADO            = $(this).find('td').eq(12).text(),
            COMPRA              = $(this).find('td').eq(13).text(),
            CODIGO              = $(this).find('td').eq(14).text(),
            UNIDAD              = $(this).find('td').eq(15).text(),
            DESCRIPCION         = $(this).find('td').eq(16).text(),
            TIPO_ORDEN          = $(this).find('td').eq(17).text(),
            ANIO_ORDEN          = $(this).find('td').eq(18).text(),
            NRO_ORDEN           = $(this).find('td').eq(19).text(),
            FECHA_ORDEN         = $(this).find('td').eq(20).text(),
            CANTIDAD_ORDEN      = $(this).find('td').eq(21).text(),
            ITEM_ORDEN          = $(this).find('td').eq(22).text(),
            AUTORIZA_ORDEN      = $(this).find('td').eq(23).text(),
            CANTIDAD_ALMACEN    = $(this).find('td').eq(24).text(),
            PROVEEDOR           = $(this).find('td').eq(25).text(),
        
            FECHA_ENTREGA       = $(this).find('td').eq(26).text(),
            CANTIDAD_RECIBIDA   = $(this).find('td').eq(27).text(),
            NOTA_INGRESO        = $(this).find('td').eq(28).text(),
            FECHA_RECEPCION     = $(this).find('td').eq(29).text(),
            SALDO_RECIBIR       = $(this).find('td').eq(30).text(),
            DIAS_ENTREGA        = $(this).find('td').eq(31).text(),
            DIAS_ATRASO         = $(this).find('td').eq(32).text(),
            SEMAFORO            = $(this).find('td').eq(33).text(),
            DESPACHO            = $(this).find('td').eq(34).text(),
            NUMERO_GUIA         = $(this).find('td').eq(35).text(),
            GUIA_TRANSFER       = $(this).find('td').eq(36).text(),
            FECHA_TRASLADO      = $(this).find('td').eq(37).text(),
            
            REGISTRO_ALMACEN    = $(this).find('td').eq(38).text(),
            FECHA_REGISTRO_OBRA = $(this).find('td').eq(39).text(),
            CANTIDA_OBRA        = $(this).find('td').eq(40).text(),
            ESTADO_PEDIDO       = $(this).find('td').eq(41).text(),
            ESTADO_ITEM         = $(this).find('td').eq(42).text(),
            NUMERO_PARTE        = $(this).find('td').eq(43).text(),
            CODIGO_ACTIVO       = $(this).find('td').eq(44).text(),
            OPERADOR            = $(this).find('td').eq(45).text(),
            TRANSPORTE          = $(this).find('td').eq(46).text(),
            OBSERVACIONES       = $(this).find('td').eq(47).text(),
            SOLICITANTE         = $(this).find('td').eq(48).text();

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
        item['aprobado']            = APROBADO;
        item['compra']              = COMPRA;

        item['tipo_orden']          = TIPO_ORDEN;
        item['anio_orden']          = ANIO_ORDEN;
        item['nro_orden']           = NRO_ORDEN;
        item['fecha_orden']         = FECHA_ORDEN;
        item['item_orden']          = ITEM_ORDEN;
        item['cantidad_orden']      = CANTIDAD_ORDEN;
        item['autoriza_orden']      = AUTORIZA_ORDEN;
        item['cantidad_almacen']    = CANTIDAD_ALMACEN;

        item['proveedor']           = PROVEEDOR;
        item['fecha_entrega']       = FECHA_ENTREGA;

        item['cantidad_recibida']   = CANTIDAD_RECIBIDA;
        item['nota_ingreso']        = NOTA_INGRESO;
        item['fecha_recepcion']     = FECHA_RECEPCION;

        item['saldo_recibir']       = SALDO_RECIBIR;
        item['dias_entrega']        = DIAS_ENTREGA;
        item['dias_atraso']         = DIAS_ATRASO;
        item['semaforo']            = SEMAFORO;
        item['despacho']            = DESPACHO;
        item['numero_guia']         = NUMERO_GUIA;

        item['registro_almacen']    = REGISTRO_ALMACEN;
        item['fecha_registro_obra'] = FECHA_REGISTRO_OBRA;
        item['cantidad_obra']       = CANTIDA_OBRA;

        item['guia_transfer']       = GUIA_TRANSFER;
        item['fecha_traslado']      = FECHA_TRASLADO;
        
        item['estado_pedido']       = ESTADO_PEDIDO;
        item['estado_item']         = ESTADO_ITEM;
        item['numero_parte']        = NUMERO_PARTE;
        item['codigo_activo']       = CODIGO_ACTIVO;
        item['operador']            = OPERADOR;
        item['transporte']          = TRANSPORTE;
        item['observaciones']       = OBSERVACIONES;
        item['solicitante']         = SOLICITANTE;
        
        
        DATA.push(item);
    })

    return DATA;
}

