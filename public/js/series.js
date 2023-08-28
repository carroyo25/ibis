$(function(){
    $("#esperar").fadeOut();

    $("#descripcion").keypress(function (e) { 
        if(e.which == 13) { 
           $.post("series/consulta",{costos:$("#costosSearch").val(),
                                            serie:$("#serie").val(),
                                            descripcion:$("#descripcion").val()},
            function (data, text, requestXHR) {
                $("#tablaPrincipal")
                            .empty()
                            .append(data);
            },
            "text"
           );
        }
    });
})