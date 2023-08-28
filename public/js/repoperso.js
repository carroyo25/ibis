$(function(){
    $("#esperar").fadeOut();

    let row = ``;

    $("#docident").focus;

    $("#docident").keypress(function (e) { 
        if(e.which == 13) { 
            $.post(RUTA+"repoperso/datosapi",{documento:$(this).val(),
                                                costos:$("#costosSearch").val(),
                                                codigo:$("#codigoSearch").val()},
                function (data, textStatus, jqXHR) {
                    if (data.registrado) {
                        $("#nombre").val(data.datos[0].paterno+' '+data.datos[0].materno+' '+data.datos[0].nombres);
                        $("#cargo").val(data.datos[0].cargo.toUpperCase());
                        $("#cut").val(data.datos[0].cut);
                        $("#correo").val(data.datos[0].correo);

                        $("#tablaPrincipal")
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

    $("#btnBuscar").click(function (e) { 
        e.preventDefault();

        try {
            if ( $("#docident").val() == "" ) throw "Indique el N° de documento";
            if ( $("#tablaPrincipal tbody tr").length == 0 ) throw "No relleno productos";  
            
            $("#dialogo").fadeIn();

        } catch (error) {
            mostrarMensaje(error,"mensaje_error");
        }
        
        return false;
    });

    $("#btnAceptarDialogo").click(function (e) { 
        e.preventDefault();

        $.post(RUTA+'repoperso/buscaCodigo',{codigo:$("#codigoSearch").val(),
                                            documento:$("#docident").val(),
                                            costos:$("#costosSearch").val()},
                function (data, textStatus, jqXHR) {
                
                    $("#tablaPrincipal tbody")
                                .empty()
                                .append(data);

                    $("#dialogo").fadeOut();
                },
                "text"
            );
        
        return false;
    });

    $("#btnCancelarDialogo").click(function (e) { 
        e.preventDefault();

        $("#dialogo").fadeOut();
        
        return false;
    });
})