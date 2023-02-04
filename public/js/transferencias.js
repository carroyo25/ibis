$(function(){
    $("#esperar").fadeOut();

    $("#nuevoRegistro").click(function (e) { 
        e.preventDefault();

        $("#estado")
            .removeClass()
            .addClass("textoCentro estado w100por procesando");
        $("#tablaDetalles tbody").empty();
        $("#proceso").fadeIn();

        $("#tipo").val("SALIDA X OC");
        $("#codigo_movimiento").val(144);
        
        accion = 'n';

        return false;
    });

    $("#closeProcess").click(function (e) { 
        e.preventDefault();

        /*$.post(RUTA+"salida/actualizaDespachos",
            function (data, textStatus, jqXHR) {
                $(".itemsTabla table tbody")
                    .empty()
                    .append(data);

                $("#proceso").fadeOut(function(){
                    grabado = false;
                    $("form")[0].reset();
                    $("form")[1].reset();
                    $("form")[2].reset();
                });
            },
            "text"
        );*/
        
        $("#proceso").fadeOut();
        return false;
    });

    $(".mostrarLista").focus(function (e) { 
        e.preventDefault();

        if (accion !="n") {
            return false;
        }
        
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
        
        control.slideUp()
        destino.val($(this).text());
        id = destino.attr("id");

        if (contenedor_padre == "listaCostos"){
            $("#codigo_costos").val(codigo);
        }else if(contenedor_padre == "listaAprueba"){
            $("#codigo_aprueba").val(codigo);
        }else if(contenedor_padre == "listaAprueba"){
            $("#codigo_aprueba").val(codigo);
        }else if(contenedor_padre == "listaOrigen"){
            $("#codigo_almacen_origen").val(codigo);
        }else if(contenedor_padre == "listaDestino"){
            $("#codigo_almacen_destino").val(codigo);
        }else if(contenedor_padre == "listaMovimiento"){
            $("#codigo_movimiento").val(codigo);
        }

        return false;
    });

    $("#importData").click(function (e) { 
        e.preventDefault();

        try {
            if ($("#codigo_costos").val() == 0) throw "Indique el centro de costos"; 

            $("#esperar").fadeIn();

            $.post(RUTA+"transferencias/existencias", {cc:$("#codigo_costos").val(),codigo:'',descripcion:''},
                function (data, textStatus, jqXHR) {
                    $("#tablaModulos tbody")
                        .empty()
                        .append(data);
                        $("#busqueda").fadeIn();
                        $("#esperar").fadeOut();
                },
                "text"
            );
        } catch (error) {
            mostrarMensaje(error,"mensaje_error");
        }

        
        
        return false
    });

    $(".tituloVentana").on("click","a", function (e) {
        e.preventDefault();

        $(this).parent().parent().parent().parent().fadeOut();

        return false;
    });

    $("#tablaModulos tbody").on("click","tr", function (e) {
        e.preventDefault();

        let nFilas = $.strPad($("#tablaDetalles tr").length,3);
        let idprod = $(this).data("id_cprod");
        let nunid = $(this).data("ncomed");
        let codigo = $(this).children('td:eq(1)').text();
        let descrip = $(this).children('td:eq(2)').text();
        let unidad = $(this).children('td:eq(3)').text();
        let saldo = $(this).children('td:eq(4)').text();
        let grabado = 0;

        let row = `<tr data-grabado="${grabado}" data-idprod="${idprod}" data-codund="${nunid}" data-idx="-">
                    <td class="textoCentro"><a href="#"><i class="fas fa-eraser"></i></a></td>
                    <td class="textoCentro"><a href="#"><i class="fas fa-exchange-alt"></i></a></td>
                    <td class="textoCentro">${nFilas}</td>
                    <td class="textoCentro">${codigo}</td>
                    <td class="pl20px">${descrip}</td>
                    <td class="textoCentro">${unidad}</td>
                    <td><input type="number" step="any" placeholder="0.00" onchange="(function(el){el.value=parseFloat(el.value).toFixed(2);})(this)"></td>
                    <td class="textoDerecha">${saldo}</td>
                    <td><textarea></textarea></td>
                </tr>`;

        $("#tablaDetalles tbody").append(row);

        return false;
    });

    $("#guiaRemision").click(function (e) { 
        e.preventDefault();
        
        $("#vistadocumento").fadeIn();

        return false;
    });

    $("#tablaDetalles tbody").on('blur','input', function (e) {
            try {
                let ingreso = parseInt($(this).parent().parent().find("td").eq(6).children().val());
                let stock = parseInt($(this).parent().parent().find("td").eq(7).text());

                if(ingreso > stock) {
                    mostrarMensaje('La cantidad ingresada, es mayor al stock','mensaje_error')
                    return false;
                }

            } catch (error) {
                
            }
    });

    $("#tablaDetalles tbody").on('keypress','input', function (e) {
        if (e.which == 13) {
            $('#tablaDetalles tbody tr:last').find('td').eq(5).children().focus();
        }
    });

    $(".tituloDocumento").on("click","#closeDocument", function (e) {
        e.preventDefault();

        $(this).parent().parent().parent().parent().parent().fadeOut();

        return false;
    });

    $("#saveRegister").click(function(e){
        e.preventDefault();

        

        return false;
    });
})

detalles = (flag) =>{
    DETALLES = [];

    let TABLA = $("#tablaDetalles tbody >tr");
    
    TABLA.each(function(){
        let ITEM            = $(this).find('td').eq(2).text(),
            IDPROD          = $(this).data("idprod"),
            ALMACEN         = $("#codigo_almacen_origen").val(),
            CANTIDAD        = $(this).find('td').eq(6).text(),// cantidad
            OBSER           = $(this).find('td').eq(8).children().val(),
            CODIGO          = $(this).find('td').eq(3).text(),//codigo
            UNIDAD          = $(this).find('td').eq(5).text(),//unidad
            DESTINO         = $("#codigo_almacen_destino").val(),
            DESCRIPCION     = $(this).find('td').eq(4).text(),//unidad
    
        item = {};

        if (!flag) {
            item['item']         = ITEM;
            item['idprod']       = IDPROD;
            item['almacen']      = ALMACEN;
            item['cantidad']     = CANTIDAD;
            item['cantdesp']     = CANTDESP;
            item['obser']        = OBSER;

            item['codigo']       = CODIGO;
            item['descripcion']  = DESCRIPCION;
            item['unidad']       = UNIDAD;
            item['destino']      = DESTINO;
            
            DETALLES.push(item);
        }
    })

    return DETALLES; 
}
