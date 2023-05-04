$(function(){
    let fila = "",
        registro = 0,
        sw = 0;


    $("#espera").fadeOut();

    let row = ``;
    $("#docident").focus;

    $("#docident").keypress(function (e) { 
        if(e.which == 13) { 
            $.post(RUTA+"consumo/datosapi",{documento:$(this).val(),costos:$("#costosSearch").val()},
                function (data, textStatus, jqXHR) {
                    if (data.registrado) {
                        $("#nombre").val(data.datos[0].paterno+' '+data.datos[0].materno+' '+data.datos[0].nombres);
                        $("#cargo").val(data.datos[0].cargo.toUpperCase());
                        $("#cut").val(data.datos[0].cut);
                        $("#correo").val(data.datos[0].correo);

                        $("#tablaPrincipal tbody")
                            .empty()
                            .append(data.anteriores);

                        pdfjsLib.getDocument(data.ruta).promise.then(doc => {
                            pdf = doc;
                            render();
                        });
    
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

    $("#codeRead").keypress(function (e) { 
        if(e.which == 13) {
            $.post(RUTA+"consumo/productos", {codigo:$(this).val()},
                function (data, textStatus, jqXHR) {
                    let fecha = fechaActual();
                    let nfilas = $("#tablaPrincipal tr").length;

                    var row = `<tr data-registrado=0 class="pointer" data-idprod="${data.idprod}">
                                <td class="textoDerecha">${nfilas}</td>
                                <td class="textoCentro">${data.codigo}</td>
                                <td class="pl20px">${data.descripcion}</td>
                                <td class="textoCentro">${data.unidad}</td>
                                <td class=""><input type="text" value=1 class="textoDerecha unstyled entrada" onFocus="this.select();"></td>
                                <td class=""><input type="date" class="unstyled textoCentro entrada" value="${fecha}"></td>
                                <td class=""><input type="text" class="entrada"></td>
                                <td class=""><input type="text" class="entrada"></td>
                                <td class=""><input type="text" class="entrada"></td>
                                <td class=""><input type="text" class="entrada"></td>
                                <td class="textoCentro"><input type="checkbox" class="entrada"></td>
                                <td class=""><input type="text" class="entrada"></td>
                                <td class=""></td>
                                <td class="textoCentro"><a href=""><i class="far fa-trash-alt"></i></a></td>
                        </tr>`;

                    if (data.registrado) {
                        if ( $("#tablaPrincipal tbody tr").length == 0) {
                            $("#tablaPrincipal tbody").append(row);
                            $('#tablaPrincipal tbody tr:last').find('td').eq(4).children().focus();
                        }
                        else {
                            $(row).insertBefore("#tablaPrincipal tbody tr:first");
                            $('#tablaPrincipal tbody tr:first').find('td').eq(4).children().focus();
                        }   
                    }

                    $("#codeRead").val('').focus();
                },
                "json"
            );
         }
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

    $("#tablaPrincipal tbody").on("keypress",".entrada", function (e) {
        if(e.which == 13) {
            
            if ($(this).val() != "") {
                $("#codeRead")
                    .val("")
                    .focus();

                //$("#codeRead").focus();
            }
        }
    });

    $("#btnAceptarGrabar").click(function (e) { 
        e.preventDefault();

        let canvas = document.getElementById("cnv");

        $.post(RUTA+'consumo/firma', {img : canvas.toDataURL(),detalles:JSON.stringify(detalles())},
            function (data, textStatus, jqXHR) {
                if (data) {
                    mostrarMensaje("Consumo registrado","mensaje_correcto");
                    
                    $("#tablaPrincipal tbody").empty();
                    $("#costosSearch").val(-1);
                    $(".ingreso").val("");
                    
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

        $("#tablaPrincipal tbody").empty();
        $("#costosSearch").val(-1);
        $(".ingreso").val("");

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

    $("#tablaPrincipal tbody").on("click",".hideItem", function (e) {
        e.preventDefault();

        filaAnula = $(this).parent();

        console.log($("#tablaPrincipal tbody > tr").length);

        try {

            if ( $("#tablaPrincipal tbody > tr").length <= 1) throw new Error ("Error al eliminar");

            $.post(RUTA+"consumo/anulaItem",{item:$(this).data('idreg')},
                function (data, textStatus, jqXHR) {

                    if (data) {
                        filaAnula.remove();
                    }
                    
                },
                "text"
            );
        } catch (error) {
            mostrarMensaje(error,"mensaje_error");
        }

        
        return false;
    });

    $("#tablaPrincipal tbody").on("click","a", function (e) {
        e.preventDefault();

        fila = $(this).parent().parent();
        sw = fila.data("registrado");
        registro = $(this).attr("href");

        $("#borrarFila").fadeIn();

        return false;
    });

    $("#btnAceptarBorrar").click(function (e) { 
        e.preventDefault();

       if (sw) {
            $.post(RUTA+"consumo/borraFila",{id:registro},
                function (data, textStatus, jqXHR) {
                    mostrarMensaje(data.mensaje,"mensaje_correcto");
                },
                "json"
            );
       }

       fila.remove();
       $("#borrarFila").fadeOut();
        
        return false;
    });

    $("#btnCancelarBorrar").click(function (e) { 
        e.preventDefault();

        $("#borrarFila").fadeOut();
        
        return false;
    });

    $("#excelFile").click(function (e) { 
        e.preventDefault();

        $("#exporta").fadeIn();

        return false;
    });

    $("#btnCancelarExport").click(function (e) { 
        e.preventDefault();

        $("#exporta").fadeOut();

        return false;
    });

    $("#btnDescarga").click(function (e) { 
        e.preventDefault();

        try {
            if ( $("#costosExport").val() == -1) throw "Elija un centro de costos";

            $("#espera").fadeIn();

            $.post(RUTA+"consumo/reporte",{cc:$("#costosExport").val()},
                function (data, textStatus, jqXHR) {
                    $("#espera,#exporta").fadeOut();
                    window.location.href = data.documento;
                },
                "json"
            );
        } catch (error) {
            mostrarMensaje("Elija el centro de costos/Sede/Proyecto","mensaje_error");
        }
        
        
        return false;
    });
})

detalles = () => {
    DATA = [];
    let TABLA = $("#tablaPrincipal tbody >tr");

    TABLA.each(function(){
        let ITEM        = $(this).find('td').eq(0).text(),
            IDPROD      = $(this).data("idprod"),
            GRABADO     = $(this).data("grabado"),
            CODIGO      = $(this).find('td').eq(1).text(),
            DESCRIPCION = $(this).find('td').eq(2).text(),
            UNIDAD      = $(this).find('td').eq(3).text(),
            CANTIDAD    = $(this).find('td').eq(4).children().val(),
            FECHA       = $(this).find('td').eq(5).children().val(),
            HOJA        = $(this).find('td').eq(6).children().val(),
            ISOMETRICO  = $(this).find('td').eq(7).children().val(),
            OBSERVAC    = $(this).find('td').eq(8).children().val(),
            SERIE       = $(this).find('td').eq(9).children().val(),
            PATRIMONIO  = $(this).find('td').eq(10).children().prop('checked'),
            ESTADO      = $(this).find('td').eq(11).children().val(),
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
            item['serie']       = SERIE;

            DATA.push(item);
        }
    })

    return DATA;
}

function render() {
    pdf.getPage(1).then(page => {
        var myCanvas = document.getElementById("pdfCanvas");
        var context = myCanvas.getContext("2d");
        var viewport = page.getViewport({ scale: 1 });
        
        myCanvas.width = 600;
        myCanvas.height = 700;
        

        // Render PDF page into canvas context
        let renderContext = {
            canvasContext: context,
            viewport: viewport
        };

        let renderTask = page.render(renderContext);
        
        renderTask.promise.then(function () {
            const canvas = document.getElementById('pdfCanvas');
            const imagen = document.getElementById('vistafirma');

            let imageContentRaw = canvas.getContext('2d').getImageData(50,450,220,110);
            let canvasImg = document.createElement('canvas');

            canvasImg.width = 220;
            canvasImg.height = 110;
            canvasImg.getContext('2d').putImageData(imageContentRaw, 0, 0);

            imagen.src = canvasImg.toDataURL("image/jpeg", 1.0)
        });
    });
}

