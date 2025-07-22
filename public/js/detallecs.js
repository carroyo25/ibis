$(function(){
    var accion = "";
    var grabado = false;
    var aprobacion = 0;

    $("#esperar").fadeOut();

    $("#btnConsulta").click(function(e){
        e.preventDefault();

        $.post(RUTA+"detallecs/consulta", {cc:$("#costosSearch").val(),
                                            codigo:$("#codigoBusqueda").val(),
                                            descripcion:$("#descripcionSearch").val(),
                                            isometrico:$("#isometricoSearch").val()},
            function (data, text, requestXHR) {
                $("#tablaPrincipal tbody").empty().append(data);
            },
            "text"
        );

        return false;
    });

    $("#excelFile").click(function (e) { 
        e.preventDefault();

        $("#esperar").css("opacity","1").fadeIn();

        $.post(RUTA+"detallecs/exportaExcel",{registros:JSON.stringify(detalles())},
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
    let TABLA = $("#tablaPrincipal tbody >tr");

    TABLA.each(function(){
        let ITEM            = $(this).find('td').eq(0).text(),
            COSTOS          = $(this).find('td').eq(1).text(),
            CODIGO          = $(this).find('td').eq(2).text(),
            DESCRIPCION     = $(this).find('td').eq(3).text(),
            UNIDAD          = $(this).find('td').eq(4).text(),
            DOCUMENTO       = $(this).find('td').eq(5).text(),
            NOMBRE          = $(this).find('td').eq(6).text(),
            FECHA           = $(this).find('td').eq(7).text(),
            DEVOLUCION      = $(this).find('td').eq(8).text(),
            SERIE           = $(this).find('td').eq(9).text(),
            HOJA            = $(this).find('td').eq(10).text(),
            ISOMETRICO      = $(this).find('td').eq(11).text(),
            OBSERVACIONES   = $(this).find('td').eq(12).text(),
            TOTAL           = $(this).find('td').eq(13).text();

           
        item= {};
        
        item['item']            = ITEM;
        item['costos']          = COSTOS;
        item['codigo']          = CODIGO;
        item['descripcion']     = DESCRIPCION;
        item['unidad']          = UNIDAD;
        item['documento']       = DOCUMENTO;
        item['nombre']          = NOMBRE;
        item['fecha']           = FECHA;
        item['devolucion']      = DEVOLUCION;
        item['serie']           = SERIE;
        item['hoja']            = HOJA;
        item['isometrico']      = ISOMETRICO;
        item['observaciones']   = OBSERVACIONES;
        item['total']           = TOTAL;
            
        DATA.push(item);
    })

    return DATA;
}