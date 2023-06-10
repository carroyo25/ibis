$(function(){
    $("#esperar").fadeOut();

    let row = ``;

    $("#docident").focus;

    $("#docident").focus;

    $("#docident").keypress(function (e) { 
        if(e.which == 13) { 
            $.post(RUTA+"repoperso/datosapi",{documento:$(this).val(),costos:$("#costosSearch").val()},
                function (data, textStatus, jqXHR) {
                    if (data.registrado) {
                        $("#nombre").val(data.datos[0].paterno+' '+data.datos[0].materno+' '+data.datos[0].nombres);
                        $("#cargo").val(data.datos[0].cargo.toUpperCase());
                        $("#cut").val(data.datos[0].cut);
                        $("#correo").val(data.datos[0].correo);

                        $("#tablaPrincipal tbody")
                            .empty()
                            .append(data.anteriores);
                    }else{
                        mostrarMensaje("Trabajador no registrado","mensaje_error");
                        $("#nombre, #cargo, #cut, #correo").val("");
                    }
                },
                "json"
            );
        }
    });
})