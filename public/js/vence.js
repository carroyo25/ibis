$(() => {
    $("#esperar").fadeOut();

    $("#btnConsulta").click(function(e){
        e.preventDefault();

        $.post(RUTA+"vence/consulta",{cc:$("#costosSearch").val(),codigo:$("#codigoBusqueda").val(),descripcion:$("#descripcionSearch").val()},
            function (data, text, requestXHR) {
                $("#tablaPrincipal tbody")
                    .empty()
                    .append(data);
            },
            "text"
        );

        return false;
    });

    $("#tablaPrincipal tbody").on("click","tr", function (e) {
        e.preventDefault();

        let TABLA = $("#tablaPrincipal tbody >tr");

        $.post(RUTA+"vence/consultaItem",{item:TABLA.data('idexiste')},
            function (data, text, requestXHR) {
                $("#codigo_item").text(TABLA.find('td').eq(2).text());
                $("#nombre_item").text(TABLA.find('td').eq(3).text());
                $("#detalle_item").text();

                $("#vistadocumento").fadeIn();
            },
            "json"
        );

        return false;
    });

    $("#closeDocument").click(function (e) { 
        e.preventDefault();
        
        $("#vistadocumento").fadeOut();

        return false;
    });
})

detalles = () =>{
    DATA = [];
    let TABLA = $("#tablaPrincipal tbody >tr");

    TABLA.each(function(){
        let ITEM            = $(this).find('td').eq(0).text(),
            CODIGO          = $(this).find('td').eq(1).text(),
            DESCRIPCION     = $(this).find('td').eq(2).text(),
            UNIDAD          = $(this).find('td').eq(3).text(),
            INGRESO         = $(this).find('td').eq(4).text(),
            INVENTARIO      = $(this).find('td').eq(5).text(),
            SALIDA          = $(this).find('td').eq(6).text(),
            DEVUELTO        = $(this).find('td').eq(7).text(),
            TRANSFERENCIA   = $(this).find('td').eq(8).text(),
            SALDO           = $(this).find('td').eq(10).text();
           
        item= {};
        
        item['item']            = ITEM;
        item['codigo']          = CODIGO;
        item['descripcion']     = DESCRIPCION;
        item['unidad']          = UNIDAD;
        item['ingreso']         = INGRESO;
        item['inventario']      = INVENTARIO;
        item['salida']          = SALIDA;
        item['devuelto']        = DEVUELTO;
        item['transferencias']  = TRANSFERENCIA;
        item['saldo']           = SALDO;
            
        DATA.push(item);
    })

    return DATA;
}
