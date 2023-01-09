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
            NUM_MMTO            = $(this).find('td').eq(9).text(),
            CREA_PEDIDO         = $(this).find('td').eq(10).text(),
            APRO_PEDIDO         = $(this).find('td').eq(11).text(),
            CODIGO              = $(this).find('td').eq(12).text(),
            UNIDAD              = $(this).find('td').eq(13).text(),
            DESCRIPCION         = $(this).find('td').eq(14).text(),
            CANTIDAD            = $(this).find('td').eq(15).text(),
            TIPO_ORDEN          = $(this).find('td').eq(16).text(),
            ANIO_ORDEN          = $(this).find('td').eq(17).text(),
            NRO_ORDEN           = $(this).find('td').eq(18).text(),
            FECHA_ORDEN         = $(this).find('td').eq(19).text(),
            PROVEEDOR           = $(this).find('td').eq(20).text(),
            ENVIO_PROVEEDOR     = $(this).find('td').eq(21).text(),
            CANTIDAD_RECIBIDA   = $(this).find('td').eq(22).text(),
            SALDO_RECIBIR       = $(this).find('td').eq(23).text(),
            DIAS_ENTREGA        = $(this).find('td').eq(24).text(),
            DIAS_ATRASO         = $(this).find('td').eq(25).text(),
            SEMAFORO            = $(this).find('td').eq(26).text(),
            NOTA_INGRESO        = $(this).find('td').eq(27).text(),
            GUIA_INGRESO        = $(this).find('td').eq(28).text(),
            FECHA_INGRESO       = $(this).find('td').eq(29).text(),
            NOTA_SALIDA         = $(this).find('td').eq(30).text(),
            GUIA_REMISION       = $(this).find('td').eq(31).text(),
            FECHA_GUIAREMISION  = $(this).find('td').eq(32).text(),
            CANTIDA_OBRA        = $(this).find('td').eq(33).text(),
            NOTA_INGRESOOBRA    = $(this).find('td').eq(34).text(),
            FECHA_RECEPOBRA     = $(this).find('td').eq(35).text(),
            ESTADO_PEDIDO       = $(this).find('td').eq(36).text(),
            ESTADO_ITEM         = $(this).find('td').eq(37).text(),
            NUMERO_PARTE        = $(this).find('td').eq(38).text(),
            CODIGO_ACTIVO       = $(this).find('td').eq(39).text(),
            OPERADOR            = $(this).find('td').eq(40).text(),
            TRANSPORTE          = $(this).find('td').eq(41).text(),
            OBSERVACIONES       = $(this).find('td').eq(42).text();
          

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
        item['num_mmto']            = NUM_MMTO;
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
        item['envio_proveedor']     = ENVIO_PROVEEDOR;
        item['cantidad_recibida']   = CANTIDAD_RECIBIDA;
        item['saldo_recibir']       = SALDO_RECIBIR;
        item['dias_entrega']        = DIAS_ENTREGA;
        item['dias_atraso']         = DIAS_ATRASO;
        item['semaforo']            = SEMAFORO;
        item['nota_ingreso']        = NOTA_INGRESO;
        item['guia_ingreso']        = GUIA_INGRESO;
        item['fecha_ingreso']       = FECHA_INGRESO;
        item['nota_salida']         = NOTA_SALIDA;
        item['guia_remision']       = GUIA_REMISION;
        item['fecha_guiaremision']  = FECHA_GUIAREMISION;
        item['cantidad_obra']       = CANTIDA_OBRA;
        item['nota_ingresoobra']    = NOTA_INGRESOOBRA;
        item['fecha_recepobra']     = FECHA_RECEPOBRA;
        item['estado_pedido']       = ESTADO_PEDIDO;
        item['estado_item']         = ESTADO_ITEM;
        item['numero_parte']        = NUMERO_PARTE;
        item['codigo_activo']       = CODIGO_ACTIVO;
        item['operador']            = OPERADOR;
        item['transporte']          = TRANSPORTE;
        item['observaciones']       = OBSERVACIONES;
        
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

var tableToExcel = (function() {
    var uri = 'data:application/vnd.ms-excel;base64,'
      , template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--><meta http-equiv="content-type" content="application/vnd.ms-excel; charset=UTF-8"></head><body><table>{table}</table></body></html>'
      , base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) }
      , format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) }
    return function(table, name) {
      if (!table.nodeType) table = document.getElementById(table)
      var ctx = {worksheet: name || 'Worksheet', table: table.innerHTML}
      window.location.href = uri + base64(format(template, ctx))
    }
  })()