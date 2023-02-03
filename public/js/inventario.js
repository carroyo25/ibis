$(() => {

    let accion = 0;
    let fila = "";

    $("#esperar").fadeOut();

    $("#nuevoRegistro").click(function (e) { 
        e.preventDefault();
        $("form")[1].reset();
        $("#tablaDetalles tbody").empty();

        $.post(RUTA+"inventario/nuevoRegistro",
            function (data, textStatus, jqXHR) {
                $("#numero").val(data.numero);
                $("#proceso").fadeIn(); 
            },
            "json"
        );

        accion = 0;

        return false;
    });

    $("#closeProcess").click(function (e) { 
        e.preventDefault();

        $("#proceso").fadeOut();
        $("form")[1].reset();
        $("#tablaDetalles tbody").empty();

        return false;
    });

    $(".tituloVentana").on("click","a", function (e) {
        e.preventDefault();

        $(this).parent().parent().parent().parent().fadeOut();

        return false;
    });

    $(".mostrarLista").focus(function (e) { 
        e.preventDefault();

        $(this).next().slideDown();

        return false;
    });

    $(".cerrarLista").focus(function (e) { 
        e.preventDefault();
        
        $(".lista").fadeOut();

        return false;
    });

    $(".lista").on("click",'a', function (e) {
        e.preventDefault();

        let control = $(this).parent().parent().parent();
        let destino = $(this).parent().parent().parent().prev();
        let contenedor_padre = $(this).parent().parent().parent().attr("id");
        let codigo = $(this).attr("href");
        
        control.slideUp()
        destino.val($(this).text());
        id = destino.attr("id");

        if (contenedor_padre == "listaCostos"){
            $("#codigo_costos").val(codigo);
        }else if(contenedor_padre == "listaAlmacen"){
            $("#codigo_almacen").val(codigo);
        }else if(contenedor_padre == "listaRecepciona") {
            $("#codigo_autoriza").val(codigo);
        }else if(contenedor_padre == "listaTipo") {
            $("#codigo_tipo").val(codigo);
        }

        return false;
    });

    $("#itemsAdd").click(function (e) { 
        e.preventDefault();
        
        $.post(RUTA+"pedidos/llamaProductos", {tipo:37},
            function (data, textStatus, jqXHR) {
                $("#tablaModulos tbody")
                    .empty()
                    .append(data);
                $("#busqueda").fadeIn();
            },
            "text"
        );
        
        return false;
    });

    $("#itemsImport").click(function (e) { 
        e.preventDefault();

        if (accion == 0)
            $("#fileUpload").trigger("click");
        
        return false;
    });

    $("#fileUpload").change(function (e) { 
        e.preventDefault();

        const input = document.querySelector('#fileUpload');

        try {
            if (validarExtension(input)) throw "Archivo Inválido";

            const formData = new FormData();
            formData.append('fileUpload', input.files[0]);

            $("#esperar").fadeIn();

            fetch (RUTA+'inventario/importarItems',{
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                $("#tablaDetalles tbody")
                    .empty()
                    .append(data.datos);

                $("#esperar").fadeOut();
            })

        } catch (error) {
            mostrarMensaje(error,'mensaje_error');
        }

        return false;
    });

    //filtrar Item del pedido
    $("#txtBuscarCodigo, #txtBuscarDescrip").on("keypress", function (e) {
        if(e.which == 13) {
            $("#esperar").fadeIn();
            
            $.post(RUTA+"pedidos/filtraItems", {codigo:$("#txtBuscarCodigo").val(),
                                                descripcion:$("#txtBuscarDescrip").val(),
                                                tipo:$("#tipo").val()},
                    function (data, textStatus, jqXHR) {
                        $("#tablaModulos tbody")
                            .empty()
                            .append(data);
                        $("#esperar").fadeOut();
                    },
                    "text"
                );
        }
    });

    $("#tablaDetalles").on('click','a', function(e) {
        e.preventDefault();

        fila = $(this).parent().parent();

        $("#busqueda").fadeIn();

        return false;
    });

    $("#tablaModulos tbody").on("click","tr", function (e) {
        e.preventDefault();

        let nFilas = $.strPad($("#tablaDetalles tr").length,3);
        let idprod = $(this).data("idprod");
        let nunid = $(this).data("ncomed");
        let codigo = $(this).children('td:eq(0)').text();
        let descrip = $(this).children('td:eq(1)').text();
        let unidad = $(this).children('td:eq(2)').text();
        let grabado = 0;

        if ( !accion ){
            let row = `<tr data-grabado="${grabado}" data-idprod="${idprod}" data-codund="${nunid}" data-idx="-">
                        <td class="textoCentro">${nFilas}</td>
                        <td class="textoCentro">${codigo}</td>
                        <td class="pl20px">${descrip}</td>
                        <td class="textoCentro">${unidad}</td>
                        <td><input type="number" step="any" placeholder="0.00" onchange="(function(el){el.value=parseFloat(el.value).toFixed(2);})(this)"></td>
                        <td class="textoCentro"><input type="text"></td>
                        <td class="textoCentro"><input type="text"></td>
                        <td class="textoCentro"><input type="text"></td>
                        <td class="textoCentro"><input type="text"></td>
                        <td class="textoCentro"><input type="text"></td>
                        <td class="textoCentro"><input type="text"></td>
                        <td class="textoCentro"><input type="date"></td>
                        <td class="textoCentro"><input type="date"></td>
                        <td class="textoCentro"><input type="text"></td>
                        <td class="textoCentro"><input type="text"></td>
                        <td class="textoCentro"><input type="text"></td>
                        <td><textarea></textarea></td>
                    </tr>`;

            $("#tablaDetalles tbody").append(row);
        }else{
            changeValues(fila,idprod,descrip,codigo,unidad);
        } 
        

        return false;
    });

    $("#saveDocument").click(function (e) { 
        e.preventDefault();
        
        try {
            let result = {};

            $.each($("#formProceso").serializeArray(),function(){
                result[this.name] = this.value;
            });

            if (result['codigo_costos'] == '') throw "Elija Centro de Costos";
            if (result['codigo_almacen'] == '') throw "Elija un almacen";
            if (result['codigo_tipo'] == '') throw "Elija el concepto de ingreso";
            if ($("#tablaDetalles tbody tr").length <= 0) throw "El pedido no tienes items";
            if (checkCantTables($("#tablaDetalles tbody > tr"),5)) throw "No ingreso cantidad en un item";

            if (accion == 0) {
                $.post(RUTA+"inventario/grabaRegistro", {cabecera:result,detalles:JSON.stringify(itemsSave())},
                    function (data, textStatus, jqXHR) {
                        mostrarMensaje(data.mensaje,'mensaje_correcto');
                    },
                    "json"
                );
            }else {
                $.post(RUTA+"inventario/actualizaDetalles", {detalles:JSON.stringify(itemsSave())},
                    function (data, textStatus, jqXHR) {
                        mostrarMensaje('Items actualizados','mensaje_correcto');
                    },
                    "json"
                );
            }
        } catch (error) {
            mostrarMensaje(error,'mensaje_error');
        }
        return false;
    });

    $("#tablaPrincipal tbody").on("click","tr", function (e) {
        e.preventDefault();

        $.post(RUTA+"inventario/consulta", {id:$(this).data('doc')},
            function (data, text, requestXHR) {
                $("#codigo_costos").val(data.cabecera[0].idcostos);
                $("#codigo_almacen").val(data.cabecera[0].ncodalm);
                $("#codigo_autoriza").val(data.cabecera[0].iduser);
                $("#codigo_tipo").val(data.cabecera[0].ntipomov);
                $("#fecha").val(data.cabecera[0].ffechadoc);
                $("#numero").val($.strPad(data.cabecera[0].idreg,6));
                $("#costos").val(data.cabecera[0].cdesproy);
                $("#almacen").val(data.cabecera[0].almacen);
                $("#registra").val(data.cabecera[0].cnombres);
                $("#tipoMovimiento").val(data.cabecera[0].cdescripcion);
                $("#fechaIngreso").val(data.cabecera[0].ffechaInv);

                $("#tablaDetalles tbody")
                    .empty()
                    .append(data.detalles);
            },
            "json"
        );

        accion = 1;

        $("#proceso").fadeIn();

        return false;
    });

    $("#closeDocument").click(function (e) { 
        e.preventDefault();
        
        $("#vistadocumento").fadeOut();

        return false;
    });

    $("#itemsVerify").click(function(e){
       e.preventDefault();

       $.post(RUTA+"inventario/xlsExport", {detalles:JSON.stringify(itemsSave())},
        function (data, text, requestXHR) {
            window.location.href = data.documento;
        },
        "json"
       );

       return false;
    });
})


itemsSave = () =>{
    DATA = [];
    let TABLA = $("#tablaDetalles tbody >tr");

    TABLA.each(function(){
        let IDPROD          = $(this).data('idprod'),
            GRABADO         = $(this).data('grabado'),
            IDREG           = $(this).data('registro'),
            MARCA           = $(this).find('td').eq(4).children().val(),
            CANTIDAD        = $(this).find('td').eq(5).children().val(),
            ORDEN           = $(this).find('td').eq(6).children().val(),
            COLADA          = $(this).find('td').eq(7).children().val(),
            TAG             = $(this).find('td').eq(8).children().val(),
            SERIE           = $(this).find('td').eq(9).children().val(),
            NCERTCAL        = $(this).find('td').eq(10).children().val(),
            FECCAL          = $(this).find('td').eq(11).children().val(),
            VENCE           = $(this).find('td').eq(12).children().val(),
            REGLIB          = $(this).find('td').eq(13).children().val(),
            ESTADO          = $(this).find('td').eq(14).children().val(),
            CONDICION       = $(this).find('td').eq(15).children().val(),
            CONTENEDOR      = $(this).find('td').eq(16).children().val(),
            ESTANTE         = $(this).find('td').eq(17).children().val(),
            FILA            = $(this).find('td').eq(18).children().val(),
            OBSERVACIONES   = $(this).find('td').eq(19).children().val(),
            CODIGO          = $(this).find('td').eq(1).text(),
            DESCRIPCION     = $(this).find('td').eq(2).text();
            UNIDAD          = $(this).find('td').eq(3).text();
           

        item= {};
        
        //if ( GRABADO == 0) {
            item['idprod']         = IDPROD;
            item['marca']          = MARCA;
            item['cantidad']       = CANTIDAD;
            item['orden']          = ORDEN;
            item['colada']         = COLADA;
            item['tag']            = TAG;
            item['serie']          = SERIE;
            item['ncertcal']       = NCERTCAL;
            item['feccal']         = FECCAL;
            item['vence']          = VENCE;
            item['reglib']         = REGLIB;
            item['estado']         = ESTADO;
            item['condicion']      = CONDICION;
            item['contenedor']     = CONTENEDOR;
            item['estante']        = ESTANTE;
            item['fila']           = FILA;
            item['observaciones']  = OBSERVACIONES;
            item['idreg']          = IDREG;
            item['codigo']         = CODIGO;
            item['descripcion']    = DESCRIPCION;
            item['unidad']         = UNIDAD;

            DATA.push(item);
        //} 
    })

    return DATA;
}

changeValues = (fila,idprod,descripcion,codigo,unidad) => {
    fila
        .attr('data-idprod',idprod)
        .attr('data-estado',1)
        .css('background','rgba(56,132,192,0.2)')
        .end()
        .parent().find('td').eq(1).text(codigo)
        .parent().find('td').eq(3).text(unidad)
        .parent().find('td').eq(2).text(descripcion);
}
