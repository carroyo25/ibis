$(function(){
    let fila = "",
        registro = 0,
        sw = 0;

    $("#esperar").fadeOut();

    let row = ``;

    $("#docident").focus;

    $("#docident").keypress(function (e) { 
        if(e.which == 13) { 
            $.post(RUTA+"terceros/datosapi",{documento:$(this).val(),costos:$("#costosSearch").val()},
                function (data, textStatus, jqXHR) {
                    if (data.datos) {
                        $("#nombre").val(data.datos.nombres);
                        $("#empresa").val(data.datos.empresa);

                        if ( data.datos.nombres != ""){
                            $("#tablaPrincipal tbody")
                                .empty()
                                .append(data.anteriores);
                        }else{
                            mostrarMensaje('No se registro el personal','mensaje_error');
                        }

                    }else{
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

    $("#btnExcelFile").click(function (e) { 
        e.preventDefault();

        try {
            if ( $("#nombre").val() == "") throw new Error('Ingrese el nombre la persona');

            let nrodoc = $("#docident").val(),
                nombre = $("#nombre").val(),
                empresa = $("#empresa").val(),
                detalles = JSON.stringify(detallesGrabados()),
                formData = new FormData();

            formData.append("nombre", nombre);
            formData.append("documento", nrodoc);
            formData.append("empresa", empresa);
            formData.append("detalles", detalles);

            fetch(RUTA+'terceros/excelFile',{
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data =>{
                window.location.href = data.documento;
            })

        } catch (error) {
            mostrarMensaje(error.message,"mensaje_error");
        }
        
        

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

    /*----------------------*/

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

detallesGrabados = () => {
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
        
        if (GRABADO) {
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
