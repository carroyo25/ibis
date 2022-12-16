$(() => {
    $("#esperar").fadeOut();

    $("#nuevoRegistro").click(function (e) { 
        e.preventDefault();
        
        $.post(RUTA+"stocks/nuevoRegistro",
            function (data, textStatus, jqXHR) {
                $("#numero").val(data.numero);
                $("#proceso").fadeIn();
            },
            "json"
        );

        return false;
    });

    $("#closeProcess").click(function (e) { 
        e.preventDefault();

        $("#proceso").fadeOut()

        return false;
    });

    $("#itemsImport").click(function (e) { 
        e.preventDefault();
        
        $("#busqueda").fadeIn();

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
        let id = "";
        let codigo = $(this).attr("href");
        let catalogo = $(this).data("catalogo");
        
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

    $("#itemsImport").click(function (e) { 
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

        let fila = $(this).parent().parent();

        if ($(this).attr("href") == "#") {
                $(this).parent().parent().remove();
                fillTables($("#tablaDetalles tbody > tr"),1);
        }else {
            $.post(RUTA+"stocks/quitarItem", {query:"",id:""},
                function (data, text, requestXHR) {
                    fila.remove();
                    fillTables($("#tablaDetalles tbody > tr"),1);
                },
                "text"
            );
        };

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

        let row = `<tr data-grabado="${grabado}" data-idprod="${idprod}" data-codund="${nunid}" data-idx="-">
                    <td class="textoCentro">${nFilas}</td>
                    <td class="textoCentro">${codigo}</td>
                    <td class="pl20px">${descrip}</td>
                    <td class="textoCentro">${unidad}</td>
                    <td><input type="number" step="any" placeholder="0.00" onchange="(function(el){el.value=parseFloat(el.value).toFixed(2);})(this)"></td>
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
            if (checkCantTables($("#tablaDetalles tbody > tr"),4)) throw "No ingreso cantidad en un item";

            $.post(RUTA+"stocks/grabaRegisto", {cabecera:result,detalles:JSON.stringify(itemsSave())},
                    function (data, textStatus, jqXHR) {
                        mostrarMensaje(data.mensaje,data.clase);
                    },
                    "json"
                );

        } catch (error) {
            mostrarMensaje(error,'mensaje_error');
        }
        return false;
    });

    $("#tablaPrincipal tbody").on("click","tr", function (e) {
        e.preventDefault();

        $("#vistadocumento").fadeIn();

        return false;
    });
})

itemsSave = () =>{
    DATA = [];
    let TABLA = $("#tablaDetalles tbody >tr");

    TABLA.each(function(){
        let IDPROD      = $(this).data('idprod'),
            UNIDAD      = $(this).data('codund'),
            ESTADO      = $(this).data('grabado');
            CANTIDAD    = $(this).find('td').eq(4).children().val(),
            PSI         = $(this).find('td').eq(5).children().val(),
            SERIE       = $(this).find('td').eq(6).children().val(),
            NCERTCAL    = $(this).find('td').eq(7).children().val(),
            FECCAL      = $(this).find('td').eq(8).children().val(),
            VENCE       = $(this).find('td').eq(9).children().val(),
            NCERT       = $(this).find('td').eq(10).children().val(),
            CONDICION   = $(this).find('td').eq(11).children().val(),
            UBICACION   = $(this).find('td').eq(12).children().val(),
            ESPECIFICA  = $(this).find('td').eq(13).children().val();
           

        item= {};
        
        if (ESTADO == 0) {
            item['idprod']      = IDPROD;
            item['unidad']      = UNIDAD;
            item['cantidad']    = CANTIDAD;
            item['psi']         = PSI;
            item['serie']       = SERIE;
            item['ncertcal']    = NCERTCAL;
            item['feccal']      = FECCAL;
            item['vence']       = VENCE;
            item['ncert']       = NCERT;
            item['condicion']   = CONDICION;
            item['ubicacion']   = UBICACION;
            item['especifica']  = ESPECIFICA;

            DATA.push(item);
        } 
    })

    return DATA;
}
