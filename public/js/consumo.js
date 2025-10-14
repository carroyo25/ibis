$(function(){
    $("#esperar").fadeOut();
    
    let fila = "",
        registro = 0,
        sw = 0,
        codigo="",
        idprod="",
        descripcion="",
        und = "",
        index__fila = 0;

    $("#espera").fadeOut();

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

                    let row = `<tr data-registrado=0 class="pointer" data-idprod="${data.idprod}">
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
                                <td class=""><select id="motivo">
                                                <option value="-1">Elija Opcion</option>   
                                                <option value="240">DESGASTE</option>
                                                <option value="241">ROTURA</option>
                                                <option value="242">PERDIDA</option>
                                                <option value="243">DEFORMADO</option>
                                                <option value="244">FALTA PARTES</option>
                                                <option value="245">OTROS</option>
                                            </select></td>
                                <td class=""></td>
                                <td class=""><input type="text" class="entrada"></td>
                                <td class="textoCentro"><a href=""><i class="fas fa-pencil"></i></a></td>
                        </tr>`;

                    if (data.registrado) {
                        if ( $("#tablaPrincipal tbody tr").length == 0)  {
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

        //para desactivar el doble click de mouse
        let boton = $(this);
        
        boton.css("pointer-events","none");

        let canvas = document.getElementById("cnv");

        $.post(RUTA+'consumo/firma', {img:canvas.toDataURL(),
                                      detalles:JSON.stringify(detalles()),
                                      correo:$("#correo").val(),
                                      nombre:$("#nombre").val(),
                                      cc:$("#costosSearch option:selected").text()},
            function (data, textStatus, jqXHR) {
                if (data) {
                    mostrarMensaje("Consumo registrado","mensaje_correcto");
                    
                    $("#tablaPrincipal tbody").empty();
                    $("#costosSearch").val(-1);
                    $(".ingreso").val("");

                    boton.css("pointer-events","all");
                    
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

    $("#btnAceptarDialogoKardex").click(function (e) { 
        e.preventDefault();

        try {
            if(idprod === "") throw new Error("Elija un producto para registrar");
            if($("#cantidad_dialogo").val() =="") throw new Error("Ingrese una cantidad para registrar");
            if(parseFloat($("#cantidad_dialogo").val()) > parseFloat($("#maximo_stock").val())) throw new Error("Verificar la cantidad para registrar");

            let 
                cant            = $("#cantidad_dialogo").val(),
                fsalida         = fechaActual(),
                nhoja           = $("#nhoja_dialogo").val(),
                isometricos     = $("#isometricos_dialogo").val(),
                observaciones   = $("#observaciones_dialogo").val(),
                serie           = $("#serie_dialogo").val(),
                patrimonio      = $("#patrimonio").prop('checked'),
                cambio          = $("#cambio_epp").val(),
                nfilas          = $("#tablaPrincipal tr").length,
                estado          = $("#estado_dialogo").val(),
                textoSelect     = "",
                pat             = "";

                pat = patrimonio === true ? '<i class="far fa-check-square"></i>' : '<i class="far fa-square"></i>';
                textoSelect = cambio !== "-1" ? $('select[name="cambio_epp"] option:selected').text() : "";

            let row = `<tr data-registrado=0 class="pointer" data-idprod="${idprod}" data-cambio="${cambio}" data-patrimonio="${patrimonio}">
                            <td class="textoDerecha">${nfilas}</td>
                            <td class="textoCentro">${codigo}</td>
                            <td class="pl20px">${descripcion}</td>
                            <td class="textoCentro">${und}</td>
                            <td class=""><input type="text" value="${cant}"></td>
                            <td class=""><input type="date" class="unstyled textoCentro entrada" value="${fsalida}"></td>
                            <td class=""><input type="text" class="entrada" value="${nhoja}"></td>
                            <td class=""><input type="text" class="entrada" value='${isometricos.replace('"','¨')}'></td>
                            <td class=""><input type="text" class="entrada" value="${observaciones}"></td>
                            <td class=""><input type="text" class="entrada" value="${serie}"></td>
                            <td class="textoCentro">${pat}</td>
                            <td class="">${textoSelect}</td>
                            <td class="">${estado}</td>
                            <td class=""></td>
                            <td class="textoCentro"><a href=""><i class="far fa-trash-alt"></i></a></td>
                    </tr>`;

            //codigos para mantenimiento        
            let arraymtto = ['B05010002','B05010006'],
                codmmtto = codigo.substring(0,9),
                formData = new FormData(),
                search = arraymtto.includes(codmmtto);

                formData.append('codigo',idprod);
                formData.append('serie',serie);
                formData.append('documento',$("#docident").val());
                formData.append("costos",$("#costosSearch").val());
                
            if ( search ) {
                fetch(RUTA+'consumo/mantenimientos',{
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data=>{
                   console.log(data.respuesta);
                })
            }

            //$(row).insertBefore("#tablaPrincipal tbody tr:first");

            if ( $("#tablaPrincipal tbody tr").length == 0)  {
                $("#tablaPrincipal tbody").append(row);
            }
            else {
                $(row).insertBefore("#tablaPrincipal tbody tr:first");
            } 

            cleanDialogControls();
            idprod="";
           
        } catch (error) {
            mostrarMensaje(error.message,"mensaje_error")
        }

        return false;
    });

    $("#btnCancelarDialogoKardex").click(function (e) { 
        e.preventDefault();

        $("#dialogo_registro").fadeOut();
        
        cleanDialogControls();
        
        idprod="";
        
        return false;
    });

    $("#tablaPrincipal tbody").on("click",".hideItem", function (e) {
        e.preventDefault();

        filaAnula = $(this).parent();

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

        if ( $("#rol_user").val() == 2 || $("#rol_user").val() == 4) {
            $("#cambiarFila").fadeIn();

            fila = $(this).parent().parent();
            index__fila = $(this).parent().parent().attr("id");
            sw = fila.data("registrado");
            registro = $(this).attr("href");

            $("#codigo__cambio").val($(this).data('codigo'));
            $("#cantidad__cambio").val($(this).data('cantidad'));
            $("#patrimonio__cambio").prop("checked",$(this).data('patrimonio'));
            $("#hoja__cambio").val($(this).data('hoja'));
            $("#serie__cambio").val($(this).data('serie'));
            return false;
        }else{
            mostrarMensaje('No se puede realizar esta acción','mensaje_error');
            return false;
        }
    });

    $("#btnAceptarModificar").click(function (e) { 
        e.preventDefault();
        
        try {
            if ( $("#rol_user").val() === 2 ) throw new Error('No puede realizar esta acción');

            let formData = new FormData();
            formData.append('id',registro);
            formData.append('codigo',$("#codigo__cambio").val());
            formData.append('cantidad',$("#cantidad__cambio").val());   
            formData.append('patrimonio',$("#patrimonio__cambio").prop('checked') ? 1:0);   
            formData.append('hoja',$("#hoja__cambio").val());   
            formData.append('serie',$("#serie__cambio").val());
            
            fetch(RUTA+'consumo/actualiza',{
                method:'POST',
                body:formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.respuesta) {

                    mostrarMensaje(data.mensaje,'mensaje_correcto');
                    $('#'+index__fila).find('td').eq('1').text($("#codigo__cambio").val());
                    $('#'+index__fila).find('td').eq('2').text(data.datos['cdesprod']);
                    $('#'+index__fila).find('td').eq('3').text(data.datos['cabrevia']);
                    $('#'+index__fila).find('td').eq('4').text($("#cantidad__cambio").val());
                    $('#'+index__fila).find('td').eq('6').text($("#hoja__cambio").val());
                    $('#'+index__fila).find('td').eq('9').text($("#serie__cambio").val());
                    $('#'+index__fila).find('td').eq('10').children().prop('checked',$("#patrimonio__cambio").prop('checked'));
                }else{
                    mostrarMensaje(data.mensaje,'mensaje_error');
                }
                
            })

        } catch (error) {
            mostrarMensaje(error.message,'mensaje_error')
        }
        
        $("#cambiarFila").fadeOut();
 
        return false;
    });

    $("#btnEliminarRegistro").click(function(e){
        e.preventDefault();

        if(sw == 1){
            try {
                if ($("#rol_user").val() !== "2") throw new Error('No puede realizar esta acción');

                let formData = new FormData();
                formData.append('id',registro);

                fetch(RUTA+'consumo/borraFila',{
                    method:'POST',
                    body:formData
                })
                .then(response => response.json())
                .then(data => {
                    mostrarMensaje('Registro Eliminado','mensaje_correcto');
                    fila.remove();
                    $("#cambiarFila").fadeOut();
                })

                fila.remove();
                $("#cambiarFila").fadeOut();

            } catch (error) {
                mostrarMensaje(error.message,'mensaje_error');
            }
        }else{
            fila.remove();
            $("#cambiarFila").fadeOut();
        }

        return false;
    });

    $("#btnCancelarModificar").click(function (e) { 
        e.preventDefault();

        $("#cambiarFila").fadeOut();
        
        return false;
    });

    $("#excelFile").click(function (e) { 
        e.preventDefault();

        $("#exporta").fadeIn();

        return false;
    });

    $("#btnCancelarExport").click(function (e) { 
        e.preventDefault();

        console.log($("#form_actualizar").serialize());

        $("#exporta").fadeOut();

        return false;
    });

    $("#btnDescarga").click(function (e) { 
        e.preventDefault();

        try {
            if ( $("#costosExport").val() == -1) throw "Elija un centro de costos";

            $("#esperar").css("opacity","1").fadeIn();

            $.post(RUTA+"consumo/reporte",{cc:$("#costosExport").val()},
                function (data, textStatus, jqXHR) {
                    $("#esperar").css("opacity","0").fadeOut();
                    window.location.href = data.documento;
                },
                "json"
            );
        } catch (error) {
            mostrarMensaje("Elija el centro de costos/Sede/Proyecto","mensaje_error");
        }
        
        
        return false;
    });

    $("#btnKardex").click(function (e) { 
        e.preventDefault();

        try {
            if ( $("#costosSearch").val() == -1 ) throw new Error("Elija centro de costos");
            if ($("#docident").val() == "") throw new Error("Elija centro de costos");

            $.post(RUTA+"consumo/kardex",{nombre:$("#nombre").val(),
                                           doc:$("#docident").val(),
                                           cargo:$("#cargo").val(),
                                           cc:$("#costosSearch option:selected").text(),
                                           detalles:JSON.stringify(detallesHoja())},
                function (data, textStatus, jqXHR) {
                    $(".ventanaVistaPrevia iframe")
                    .attr("src","")
                    .attr("src","public/documentos/kardex/"+data);
                
                    $("#hojakardex").fadeIn();
                },
                "text"
            );
        } catch (error) {
            mostrarMensaje(error,"mensaje_error");
        }

        return false;
    });

    $("#closePreview").click(function (e) { 
        e.preventDefault();
        
        $("#hojakardex").fadeOut();

        $(".ventanaVistaPrevia iframe").attr("src","");

        return false;
    });

    $("#btnRegister").click(function(e){
        e.preventDefault();

        try {
            if ($("#costosSearch").val() == " ") throw new Error("Elija el centro de costos");

            $("#dialogo_registro").fadeIn();

            return false
        } catch (error) {
            mostrarMensaje(e.message,"mensaje_error");
        }

        
        return false;
    });

    //filtrar Item del pedido
    $("#codigoSearch, #descripSearch").on("keypress", function (e) {
        if(e.which == 13) {
            $("#esperar").fadeIn();
            
            $.post(RUTA+"consumo/llamarStocks", {cod:$("#codigoSearch").val(),
                                                desc:$("#descripSearch").val(),
                                                cc:$("#costosSearch").val()},
                    function (data, textStatus, jqXHR) {
                        $("#tabla_detalles_productos tbody")
                            .empty()
                            .append(data);
                        $("#esperar").fadeOut();
                    },
                    "text"
                );
        }
    });

    $("#tabla_detalles_productos tbody").on('click','tr', function(e) {
        e.preventDefault();

        idprod = $(this).data("idprod");
        codigo = $(this).find('td').eq(0).text();
        descripcion = $(this).find('td').eq(1).text();
        und = $(this).find('td').eq(2).text();

        $("#maximo_stock").val($(this).find('td').eq(3).text());

        $(this).toggleClass('semaforoNaranja');

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
            //PATRIMONIO  = $(this).find('td').eq(10).children().prop('checked'),
            //ESTADO      = $(this).find('td').eq(11).children().val(),
            ESTADO      = $(this).find('td').eq(12).text(),
            COSTOS      = $("#costosSearch").val(),
            NRODOC      = $("#docident").val(),
            PATRIMONIO  = $(this).data("patrimonio"),
            CAMBIO      = $(this).data("cambio");


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
            item['cambio']      = CAMBIO;

            DATA.push(item);
        }
    })

    return DATA;
}

detallesHoja = () => {
    DATA = [];
    let TABLA = $("#tablaPrincipal tbody >tr");

    TABLA.each(function(){
        let ITEM        = $(this).find('td').eq(0).text(),
            IDPROD      = $(this).data("idprod"),
            GRABADO     = $(this).data("grabado"),
            CODIGO      = $(this).find('td').eq(1).text(),
            DESCRIPCION = $(this).find('td').eq(2).text(),
            UNIDAD      = $(this).find('td').eq(3).text(),
            CANTIDAD    = $(this).find('td').eq(4).text(),
            FECHA       = $(this).find('td').eq(5).text(),
            HOJA        = $(this).find('td').eq(6).children().val(),
            ISOMETRICO  = $(this).find('td').eq(7).children().val(),
            OBSERVAC    = $(this).find('td').eq(8).children().val(),
            SERIE       = $(this).find('td').eq(9).children().val(),
            PATRIMONIO  = $(this).find('td').eq(10).children().prop('checked'),
            ESTADO      = $(this).find('td').eq(11).children().val(),
            COSTOS      = $("#costosSearch").val(),
            NRODOC      = $("#docident").val(),
            FIRMA       = $(this).data('firma'),
            KARDEX      = $(this).data('kardex'),
            DEVOLUCION  = $(this).data('devolucion'),
            FIRMADELVOL = $(this).data('firmadevolucion');


        item = {};
        
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
        item['kardex']      = KARDEX;
        item['firma']       = FIRMA;
        item['devolucion']  = DEVOLUCION;
        item['fdevolucion']  = FIRMADELVOL;

        DATA.push(item);
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

cleanDialogControls = () => {
    $("#codigoSearch").val("");
    $("#descripSearch").val("");
    $("#patrimonio").prop("checked", false);
    $("#cambio_epp").val(-1);
    $("#cantidad_dialogo").val("");
    $("#serie_dialogo").val("");
    $("#nhoja_dialogo").val("");
    $("#isometricos_dialogo").val("");
    $("#observaciones_dialogo").val("");
}

