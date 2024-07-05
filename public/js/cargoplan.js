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

        let str = $("#formConsulta").serialize();

        $.post(RUTA+"cargoplan/crearExcel", str,
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
            TIPO                = $(this).find('td').eq(4).text(),
            CANTIDAD            = $(this).find('td').eq(5).text(),
            APROBADO            = $(this).find('td').eq(6).text(),
            COMPRA              = $(this).find('td').eq(7).text(),
            CODIGO              = $(this).find('td').eq(8).text(),
            UNIDAD              = $(this).find('td').eq(9).text(),
            DESCRIPCION         = $(this).find('td').eq(10).text(),
            TIPO_ORDEN          = $(this).find('td').eq(11).text(),
            ANIO_ORDEN          = $(this).find('td').eq(12).text(),
            NRO_ORDEN           = $(this).find('td').eq(13).text(),
            FECHA_ORDEN         = $(this).find('td').eq(14).text(),
            CANTIDAD_ORDEN      = $(this).find('td').eq(15).text(),
            PROVEEDOR           = $(this).find('td').eq(16).text(),        
            FECHA_ENTREGA       = $(this).find('td').eq(17).text(),
            CANTIDAD_RECIBIDA   = $(this).find('td').eq(18).text(),
            NOTA_INGRESO        = $(this).find('td').eq(19).text(),
            FECHA_RECEPCION     = $(this).find('td').eq(21).text(),
            SALDO_RECIBIR       = $(this).find('td').eq(22).text(),
            DIAS_ENTREGA        = $(this).find('td').eq(23).text(),
            DIAS_ATRASO         = $(this).find('td').eq(24).text(),
            SEMAFORO            = $(this).find('td').eq(25).text(),
            OPERADOR            = $(this).find('td').eq(26).text(),
            OBSERVACIONES       = $(this).find('td').eq(27).text(),
            TIPO_MONEDA         = $(this).find('td').eq(28).text(),
            TIPO_CAMBIO         = $(this).find('td').eq(29).text(),
            PRECIO_DOLARES      = $(this).find('td').eq(30).text(),
            PRECIO_SOLES        = $(this).find('td').eq(31).text(),
            IMPORTE_TOTAL       = $(this).find('td').eq(32).text(),
            FORMA_PAGO          = $(this).find('td').eq(33).text(),
            REFERENCIA_PAGO     = $(this).find('td').eq(34).text(),
            FAMILIA             = $(this).find('td').eq(35).text(),


        item = {};

        item['item']                = ITEM;
        item['estado']              = ESTADO;
        item['proyecto']            = PROYECTO;
        item['area']                = AREA;
        item['tipo']                = TIPO;
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
        item['cantidad_orden']      = CANTIDAD_ORDEN;
        item['proveedor']           = PROVEEDOR;
        item['fecha_entrega']       = FECHA_ENTREGA;
        item['cantidad_recibida']   = CANTIDAD_RECIBIDA;
        item['nota_ingreso']        = NOTA_INGRESO;
        item['fecha_recepcion']     = FECHA_RECEPCION;
        item['saldo_recibir']       = SALDO_RECIBIR;
        item['dias_entrega']        = DIAS_ENTREGA;
        item['dias_atraso']         = DIAS_ATRASO;
        item['semaforo']            = SEMAFORO;
        item['operador']            = OPERADOR;
        item['observaciones']       = OBSERVACIONES;

        item['tipo_moneda']         = TIPO_MONEDA;         
        item['tipo_cambio']         = TIPO_CAMBIO;         
        item['precio_dolares']      = PRECIO_DOLARES;      
        item['precio_soles']        = PRECIO_SOLES;        
        item['importe_total']       = IMPORTE_TOTAL;       
        item['forma_pago']          = FORMA_PAGO;          
        item['referencia_pago']     = REFERENCIA_PAGO;     
        item['familia']             = FAMILIA;             
        
        DATA.push(item);
    })

    return DATA;
}

