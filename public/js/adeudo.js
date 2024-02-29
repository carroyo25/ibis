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

        $.post(RUTA+"adeudo/formato",{cc:$("#costosSearch").val(),
                                        doc:$("#docident").val(),
                                        nombre:$("#nombre").val(),
                                        proyecto:$('select[name="costosSearch"] option:selected').text()},
            function (data, textStatus, jqXHR) {
                $(".ventanaVistaPrevia iframe")
                    .attr("src","")
                    .attr("src","public/documentos/adeudos/"+data);
                
                $("#vistaprevia").fadeIn();
            },
            "text"
        );

        return false; 
    });

    $("#closePreview").click(function (e) { 
        e.preventDefault();

        $(".ventanaVistaPrevia iframe").attr("src","");
        $("#vistaprevia").fadeOut();

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
            if ( verificarCantidadesInput() ) throw "Verifique las cantidades";

            $("#pregunta").fadeIn();

        } catch (error) {
            mostrarMensaje(error,"mensaje_error");
        }

        return false
    });

    $("#btnAceptarGrabar").click(function (e) { 
        e.preventDefault();

        let canvas = document.getElementById("cnv");

        $.post(RUTA+'adeudo/firma', {img : canvas.toDataURL(),
                                    detalles:JSON.stringify(detalles()),
                                    nombre:$("#nombre").val(),
                                    proyecto:$("#costosSearch option:selected").text(),
                                    correo:$("#correo").val()},
            function (data, textStatus, jqXHR) {
                if (data) {
                    mostrarMensaje("Devolucion registrada","mensaje_correcto");
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
            CONDICION   = $(this).data("condicion");
            CODIGO      = $(this).find('td').eq(1).text(),
            DESCRIPCION = $(this).find('td').eq(2).text(),
            UNIDAD      = $(this).find('td').eq(3).text(),
            CANTIDAD    = $(this).find('td').eq(4).text(),
            DEVUELTO    = $(this).find('td').eq(5).children().val(),
            FECHA       = $(this).find('td').eq(6).text(),
            FDEVUELTO   = $(this).find('td').eq(7).children().val(),
            HOJA        = $(this).find('td').eq(8).text(),
            ISOMETRICO  = $(this).find('td').eq(9).text(),
            OBSERVAC    = $(this).find('td').eq(10).text(),
            SERIE       = $(this).find('td').eq(11).text(),
            PATRIMONIO  = $(this).find('td').eq(12).children().prop('checked'),
            ESTADO      = $(this).find('td').eq(13).children().val(),
            COSTOS      = $("#costosSearch").val(),
            NRODOC      = $("#docident").val();
            IDREG       = $(this).data('item');


        item = {};
        
        if (!CONDICION) {
            item['item']        = ITEM;
            item['codigo']      = CODIGO;
            item['descripcion'] = DESCRIPCION;
            item['unidad']      = UNIDAD;
            item['cantidad']    = CANTIDAD;
            item['devuelto']    = DEVUELTO;
            item['fecha']       = FECHA;
            item['fdevuelto']   = FDEVUELTO;
            item['hoja']        = HOJA;
            item['isometrico']  = ISOMETRICO;
            item['observac']    = OBSERVAC;
            item['patrimonio']  = PATRIMONIO;
            item['serie']       = SERIE;
            item['estado']      = ESTADO;
            item['costos']      = COSTOS;
            item['nrodoc']      = NRODOC;
            item['idprod']      = IDPROD;
            item['idreg']       = IDREG;

            DATA.push(item);
        }  
    })

    return DATA;
}


verificarCantidadesInput = () =>{
    let TABLA = $("#tablaPrincipal tbody >tr"),
        errorCantidad = false;

    TABLA.each(function(){
        let cantidad    = parseInt($(this).find("td").eq(4).text()),// cantidad
            canting    = parseInt($(this).find('td').eq(5).children().val());

        if( cantidad < canting) {
            errorCantidad = true
        }else if(canting == 0 )
            errorCantidad = true       
    })

    return errorCantidad;
}