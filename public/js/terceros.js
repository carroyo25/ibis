$(function(){
    $("#esperar").fadeOut();

    let row = ``;

    $("#docident").focus;

    $("#docident").keypress(function (e) { 
        if(e.which == 13) { 
            $.post(RUTA+"terceros/datosapi",{documento:$(this).val(),costos:$("#costosSearch").val()},
                function (data, textStatus, jqXHR) {
                    console.log(data);
                    if (data.datos) {
                        $("#nombre").val(data.datos.nombres);
                        $("#empresa").val(data.datos.empresa);

                        /*$("#tablaPrincipal tbody")
                            .empty()
                            .append(data.anteriores);*/
    
                        $("#codeRead").focus();
                    }else{
                        $("#nombre, #cargo, #cut, #correo").val("");
                    }
                },
                "json"
            );
        }
    });

})