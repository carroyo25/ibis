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

    $("#tablaPrincipal tbody").on("dblclick","tr", function (e) {
        e.preventDefault();
        
        $("#codigo_item").text($(this).find('td').eq(2).text());
        $("#nombre_item").text($(this).find('td').eq(3).text());
        

        $.post(RUTA+"vence/consultaItem",{item:$(this).data('idproducto'),costos:$("#costosSearch").val()},
            function (data, text, requestXHR) {

                $("#listaVencimientos tbody")
                    .empty()
                    .append(data);
                
                $("#vistadocumento").fadeIn();
            },
            "text"
        );

        return false;
    });

    $("#closeDocument").click(function (e) { 
        e.preventDefault();
        
        $("#vistadocumento").fadeOut();

        return false;
    });

    $("#excelFile").click(function (e) { 
        e.preventDefault();

        $("#esperar").css("opacity","1").fadeIn();

        $.post(RUTA+"vence/exportaExcel",{registros:JSON.stringify(detalles())},
            function (data, textStatus, jqXHR) {
                $("#esperar").css("opacity","0").fadeOut();
                window.location.href = data.documento;
            },
            "json"
        );

        return false;
    });
    

    $("#sendNotificacion").click(function (e) { 
        e.preventDefault();

        $.post(RUTA+"vence/enviaNotificacion",{costos:$("#costosSearch").val(),codigo:$("#codigoBusqueda").val(),descripcion:$("#descripcionSearch").val()},
            function (data, text, requestXHR) {

                $("#listaVencimientos tbody")
                    .empty()
                    .append(data);
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
            VENCE           = $(this).find('td').eq(5).text(),
            DIAS          = $(this).find('td').eq(6).text();
           
        item= {};
        
        item['item']            = ITEM;
        item['costos']          = COSTOS;
        item['codigo']          = CODIGO;
        item['descripcion']     = DESCRIPCION;
        item['unidad']          = UNIDAD;
        item['vence']           = VENCE;
        item['dias']            = DIAS;
            
        DATA.push(item);
    })

    return DATA;
}
