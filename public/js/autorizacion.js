$(function(){
    var accion = "";
    var grabado = false;
    var aprobacion = 0;

    $("#esperar").fadeOut();

    $("#tablaPrincipal tbody").on("click","tr", function (e) {
        e.preventDefault();

        let indice  = $(this).data("indice"),
            formData = new FormData();
            formData.append('indice', indice);

        fetch(RUTA+'autorizacion/documentoId',{
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            console.log(data);
        })

        return false;
    });

    $("#nuevoRegistro").click(function (e) { 
        e.preventDefault();

        try {
            if ( $("#id_user").val() == "" ) throw new Error("General -- reinicie el sistema");

                $("#estado")
                    .removeClass()
                    .addClass("textoCentro w35por estado procesando");
                $("#proceso").fadeIn();
                
                accion = 'n';
                grabado = false;

                $("#codigo_usuario").val($("#id_user").val());

                $(".primeraBarra").css("background","#0078D4");
                $(".primeraBarra span").text('Datos Generales');

        } catch (error) {
            mostrarMensaje(error,"mensaje_error");
        }
       
        return false;
    });

    $("#closeProcess").click(function (e) { 
        e.preventDefault();

        $.post(RUTA+"autorizacion/actualizaListado",
            function (data, textStatus, jqXHR) {
                $(".itemsTabla table tbody")
                    .empty()
                    .append(data);

                $("#proceso").fadeOut(function(){
                    grabado = false;
                    $("form")[1].reset();
                    $("#tablaDetalles tbody").empty();
                    $(".lista").fadeOut();
                });
            },
            "text"
        );
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
        let catalogo = $(this).data("catalogo");
        
        control.slideUp()
        destino.val($(this).text());
        id = destino.attr("id");

        if(contenedor_padre == "listaCostos"){
            $("#codigo_costos").val(codigo);
        }else if(contenedor_padre == "listaAreas"){
            $("#codigo_area").val(codigo);
        }else if(contenedor_padre == "listaSolicitantes"){
            $("#codigo_solicitante").val(codigo);
        }else if(contenedor_padre == "listaTipo"){
            $("#codigo_tipo").val(codigo);
        }else if(contenedor_padre == "listaOrigen"){
            $("#codigo_origen").val(codigo);
        }else if(contenedor_padre == "listaDestino"){
            $("#codigo_destino").val(codigo);
        }else if(contenedor_padre == "listaTipos"){
            $("#codigo_tipo").val(codigo);
        }

        return false;
    });

    //filtrado en la lista de solicitante
    $(".busqueda").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $(this).next().attr("id");

        //aignar a una variable el contenido
        let l = "#"+ $(this).next().attr("id")+ " li a"

        $(l).filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });

    //filtrar Item del pedido
    $("#txtBuscarCodigo, #txtBuscarDescrip").on("keypress", function (e) {
        if(e.which == 13) {
            $("#esperar").fadeIn();
            
            $.post(RUTA+"pedidos/filtraItems", {codigo:$("#txtBuscarCodigo").val(),
                                                descripcion:$("#txtBuscarDescrip").val(),
                                                tipo:37},
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

    //cuando cambia algo en la tabla de detalles
    $("#tablaDetalles tbody ").on("change","input", function (e) {
        //e.preventDefault();

        if (accion == 'u') {
            $(this).parent().parent().attr("data-grabado",0);
        }
        
       return false;
    });

    $("#tablaDetalles tbody ").on("change","textarea", function (e) {
        //e.preventDefault();

        if (accion == 'u') {
            $(this).parent().parent().attr("data-grabado",0);
        }
        
       return false;
    });

    $("#tablaDetalles tbody").on('keypress','input', function (e) {
        if (e.which == 13) {
            //para cambiar el foco con el enter

            cb = parseInt($(this).attr('tabindex'));

            if ($(':input[tabindex=\'' + (cb + 1) + '\']') != null) {
                $(':input[tabindex=\'' + (cb + 1) + '\']').focus();
                $(':input[tabindex=\'' + (cb + 1) + '\']').select();
            }
        }
    });

    $("#addItem").click(function (e) { 
        e.preventDefault();

        grabado = false;
        
        if ( $("#codigo_tipo").val() === ""){
            mostrarMensaje("Selecione el tipo de movimiento","mensaje_error");
        }else{
            $.post(RUTA+"pedidos/llamaProductos", {tipo:37},
                function (data, textStatus, jqXHR) {
                    $("#tablaModulos tbody")
                        .empty()
                        .append(data);

                    $("#busqueda").fadeIn();
                },
                "text"
            );
        }

        return false;
    });

    $(".tituloVentana").on("click","a", function (e) {
        e.preventDefault();

        $(this).parent().parent().parent().parent().fadeOut();

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
        let tabPos  = $("#tablaDetalles tr").length;

        let row = `<tr data-grabado="${grabado}" data-idprod="${idprod}" data-codund="${nunid}" data-idx="-">
                    <td class="textoCentro"><a href="#"><i class="fas fa-eraser"></i></a></td>
                    <td class="textoCentro">${nFilas}</td>
                    <td class="textoCentro">${codigo}</td>
                    <td class="pl20px">${descrip}</td>
                    <td class="textoCentro">${unidad}</td>
                    <td><input type="number" 
                                step="any" 
                                placeholder="0.00" 
                                onchange="(function(el){el.value=parseFloat(el.value).toFixed(2);})(this)"
                                tabIndex="${tabPos}">
                    </td>
                    <td class="textoCentro"><input type="text"></td>
                    <td class="textoCentro"><input type="text"></td>
                    <td><textarea></textarea></td>
                </tr>`;

        $("#tablaDetalles tbody").append(row);

        return false;
    });
    
    $("#tablaDetalles").on('click','a', function(e) {
        e.preventDefault();

        let fila = $(this).parent().parent();

        if ($(this).attr("href") == "#") {
                $(this).parent().parent().remove();
                fillTables($("#tablaDetalles tbody > tr"),1);
        }else {
            $.post(RUTA+"pedidos/quitarItem", {query:"UPDATE alm_autorizadet SET alm_autorizadet.nflgactivo =:estado WHERE alm_autorizadet.iditem =:id",
                                                        id:$(this).attr("href")},
                function (data, text, requestXHR) {
                    fila.remove();
                    fillTables($("#tablaDetalles tbody > tr"),1);
                },
                "text"
            );
        };

        return false;
    });

    $("#saveItem").click(function (e) { 
        e.preventDefault();
        
        let result = {};

        $.each($("#formProceso").serializeArray(),function(){
            result[this.name] = this.value;
        });

        try {
            if (result['codigo_costos'] == '') throw "Elija Centro de Costos";
            if (result['codigo_area'] == '') throw "Elija Area";
            if (result['codigo_tipo'] == '') throw "Elija Tipo de Autorizacion";
            if (result['codigo_solicitante'] == '') throw "Elija el asignado";
            if (result['codigo_origen'] == '') throw "Elija el almacén origen";
            if (result['codigo_destino'] == '') throw "Elija el almacén destino";
            if ($("#tablaDetalles tbody tr").length <= 0) throw "El pedido no tienes items";
            if (checkCantTables($("#tablaDetalles tbody > tr"),5)) throw "No ingreso cantidad en un item";
            
            $("#esperar").css("opacity","1").fadeIn();

            if ( accion == 'n' ){
                $.post(RUTA+"autorizacion/nuevoDocumento", {cabecera:result,detalles:JSON.stringify(itemsSave())},
                    function (data, textStatus, jqXHR) {
                        mostrarMensaje(data.mensaje,data.clase);

                        grabado = true;
                        accion = "u";
                        
                        $(".primeraBarra").css("background","#819830");
                        $(".primeraBarra span").text('Datos Generales ... Grabado');

                        $("#numero").val(data.numero);
                        $("#esperar").css("opacity","0").fadeOut();

                    },
                    "json"
                );
            }else if(accion == 'u'){
                $.post(RUTA+"autorizacion/modificaDocumento", {cabecera:result,detalles:JSON.stringify(itemsSave())},
                    function (data, textStatus, jqXHR) {
                        mostrarMensaje(data.mensaje,data.clase);
                        accion = "u";
                        grabado = true;
                        $("#tablaDetalles tbody")
                            .empty()
                            .append(data.items.detalles);
                        $("#esperar").css("opacity","0").fadeOut();
                    },
                    "json");
            }

        } catch (error) {
            mostrarMensaje(error,'mensaje_error');
        }

        return false;
    });
})

itemsSave = () =>{
    DATA = [];
    let TABLA = $("#tablaDetalles tbody >tr");

    TABLA.each(function(){
        let IDPROD      = $(this).data('idprod'),
            UNIDAD      = $(this).data('codund'),
            CANTIDAD    = $(this).find('td').eq(5).children().val(),
            SERIE       = $(this).find('td').eq(6).children().val(),
            DESTINO     = $(this).find('td').eq(7).children().val(),
            OBSERVAC    = $(this).find('td').eq(8).children().val(),
            ESTADO      = $(this).attr('data-grabado'),
            ITEM        = $(this).find('td').eq(1).text();

        item= {};
        
        if ( ESTADO == 0 ) {
            item['idprod']      = IDPROD;
            item['unidad']      = UNIDAD;
            item['cantidad']    = CANTIDAD;
            item['serie']       = SERIE;
            item['destino']     = DESTINO;
            item['observac']    = OBSERVAC;
            item['estado']      = ESTADO;
            item['item']        = ITEM;

            $(this).attr('data-grabado',1);

            DATA.push(item);
        } 
    })

    return DATA;
}