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

        /*let wb = XLSX.utils.table_to_book(document.getElementById('cargoPlanDescrip'), {sheet:"Cargo Plan"});
        let wbout = XLSX.write(wb, {bookType:'xlsx', bookSST:true, type: 'binary'});
        saveAs(new Blob([s2ab(wbout)],{type:"application/octet-stream"}), 'cargoPlan.xlsx');*/
       
        /*let worksheet = XLSX.utils.json_to_sheet(detalles());
        const workbook = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(workbook, worksheet, "Cargo Plan");
        XLSX.utils.sheet_add_aoa(worksheet, [["Item", "Estado"]], { origin: "A1" });

        worksheet["!cols"] = [ { wch: 10 } ];
        let range = XLSX.utils.decode_range("A1:50");
        let a1_range = XLSX.utils.encode_range({ s: { c: 0, r: 0 }, e: { c: 3, r: 2 } });

        XLSX.writeFile(workbook, "cargoplan.xlsx", { compression: true });*/
        
        //tableToExcel(document.getElementById('cargoPlanDescrip'),"cargoPlan")

        return false;
    });

    
})

detalles = () =>{
    DATA = [];

    let TABLA = $("#cargoPlanDescrip tbody >tr");

    TABLA.each(function(){
        let ITEM        = $(this).find('td').eq(0).text(),
            ESTADO      = $(this).find('td').eq(1).text(),
            PROYECTO    = $(this).find('td').eq(2).text(),
            AREA        = $(this).find('td').eq(3).text(),
            PARTIDA     = $(this).find('td').eq(4).text(),
            ATENCION    = $(this).find('td').eq(5).text(),
            TIPO        = $(this).find('td').eq(6).text(),
            ANIO_PEDIDO = $(this).find('td').eq(7).text(),
            NUM_PEDIDO  = $(this).find('td').eq(8).text(),
            NUM_MMTO    = $(this).find('td').eq(9).text(),
            CREA_PEDIDO = $(this).find('td').eq(10).text(),
            FECHA_PEDIDO= $(this).find('td').eq(11).text(),
          

        item = {};

        item['item']        = ITEM;
        item['estado']      = ESTADO;
        item['proyecto']    = PROYECTO;
        item['area']        = AREA;
        item['partida']     = PARTIDA;
        item['atencion']    = ATENCION;
        item['tipo']        = TIPO;
        item['anio_pedido'] = ANIO_PEDIDO;
        item['num_pedido']  = NUM_PEDIDO;
        item['num_mmto']    = NUM_MMTO;
        item['crea_pedido'] = CREA_PEDIDO;
        item['fecha_pedido']= FECHA_PEDIDO;


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