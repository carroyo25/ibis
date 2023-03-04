$(function(){
    $("#espera").fadeOut();

    let row = ``;
    $("#docident").focus;

    $("#docident").keypress(function (e) { 
        if(e.which == 13) { 
            $.post(RUTA+"adeudo/datosapi",{documento:$(this).val(),costos:$("#costosSearch").val()},
                function (data, textStatus, jqXHR) {
                    if (data.registrado) {
                        $("#nombre").val(data.datos[0].paterno+' '+data.datos[0].materno+' '+data.datos[0].nombres);
                        $("#cargo").val(data.datos[0].cargo.toUpperCase());
                        $("#cut").val(data.datos[0].cut);
                        $("#correo").val(data.datos[0].correo);
                        $("#tablaPrincipal tbody")
                            .empty()
                            .append(data.anteriores);
    
                        $("#codeRead").focus();
                    }else{
                        mostrarMensaje("Trabajador no registrado","mensaje_error");
                        $("#nombre, #cargo, #cut, #correo").val("");
                    }
                },
                "json"
            );
        }
    });

    $("#btnAdeudo").click(function(e){
        e.preventDefault();

        $("#adeudo").fadeIn();

        return false; 
    });

    $("#btnAceptarAdeudo").click(function(e){
        e.preventDefault();

        $("#adeudo").fadeOut();

        return false; 
    });

   

    $("#btnGrabarKardex").click(function(e){
        e.preventDefault();

        try {
            if ( $("#costosSearch").val() == -1 ) throw "Elija el centro de costos";
            if ( $("#docident").val() == "" ) throw "Indique el N° de documento";
            if ( $("#tablaPrincipal tbody tr").length == 0 ) throw "No relleno productos";

            $("#pregunta").fadeIn();

        } catch (error) {
            mostrarMensaje(error,"mensaje_error");
        }

        return false
    });

    
    $("#btnAceptarGrabar").click(function (e) { 
        e.preventDefault();

        let canvas = document.getElementById("cnv");

        $.post(RUTA+'adeudo/firma', {img : canvas.toDataURL(),detalles:JSON.stringify(detalles())},
            function (data, textStatus, jqXHR) {
                if (data) {
                    mostrarMensaje("Adeudo registrado","mensaje_correcto");
                }else {
                    mostrarMensaje("Hubo un error al grabar","mensaje_error");
                }

                $("#pregunta").fadeOut();
            },
            "text"
        );
        
        return false;
    });


    $("#btnCancelarGrabar").click(function (e) { 
        e.preventDefault();

        $("#pregunta").fadeOut();
        
        return false;
    });

    $("#btnBuscar").click(function (e) { 
        e.preventDefault();

        try {
            if ( $("#costosSearch").val() == -1 ) throw "Elija el centro de costos";
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

        $.post(RUTA+'consumo/buscaCodigo',{codigo:$("#codigoSearch").val(),
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

detalles = () => {
    DATA = [];
    let TABLA = $("#tablaPrincipal tbody >tr");

    TABLA.each(function(){
        let ITEM        = $(this).find('td').eq(0).text(),
            IDPROD      = $(this).data("idprod");
            GRABADO     = $(this).data("grabado");
            CODIGO      = $(this).find('td').eq(1).text(),
            DESCRIPCION = $(this).find('td').eq(2).text(),
            UNIDAD      = $(this).find('td').eq(3).text(),
            CANTIDAD    = $(this).find('td').eq(4).children().val(),
            FECHA       = $(this).find('td').eq(5).children().val(),
            HOJA        = $(this).find('td').eq(6).children().val(),
            ISOMETRICO  = $(this).find('td').eq(7).children().val(),
            OBSERVAC    = $(this).find('td').eq(8).children().val(),
            PATRIMONIO  = $(this).find('td').eq(9).children().prop('checked'),
            ESTADO      = $(this).find('td').eq(10).children().val(),
            COSTOS      = $("#costosSearch").val(),
            NRODOC      = $("#docident").val();


        item = {};
        
        if (!GRABADO) {
            item['item']        = ITEM;
            item['codigo']      = CODIGO;
            item['descripcion'] = DESCRIPCION;
            item['unidad']      = UNIDAD;
            item['cantidad']    = CANTIDAD;
            item['fecha']       = FECHA;
            item['hoja']        = HOJA;
            item['isometrico']  = ISOMETRICO;
            item['observac']    = OBSERVAC;
            item['patrimonio']  = PATRIMONIO;
            item['estado']      = ESTADO;
            item['costos']      = COSTOS;
            item['nrodoc']      = NRODOC;
            item['idprod']      = IDPROD;
        }
        
        DATA.push(item);
    })

    return DATA;
}