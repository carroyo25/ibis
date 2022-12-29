$(function() {
    var accion = "";
    var grabado = false;
    var aprobacion = 0;
    let items = "";
    let sw_accion = "";

    let equipos = listarEquipos();
    
    $("#esperar").fadeOut();

    $("#nuevoRegistro").click(function (e) { 
        e.preventDefault();

        $("#estado")
            .removeClass()
            .addClass("textoCentro w35por estado procesando");
        $("#proceso").fadeIn();
        
        accion = 'n';
        

        return false;
    });

    $("#addItem").click(function (e) { 
        e.preventDefault();
        
        if ( $("#codigo_tipo").val() === ""){
            mostrarMensaje("Selecione el tipo de pedido","mensaje_error");
        }else{
            $.post(RUTA+"pedidos/llamaProductos", {tipo:$("#codigo_tipo").val()},
                function (data, textStatus, jqXHR) {
                    $("#tablaModulos tbody")
                        .empty()
                        .append(data);

                    $("#busqueda").fadeIn();
                    
                    sw_accion = "n";
                },
                "text"
            );
        }

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

        if (contenedor_padre == "listaCostos"){
            $("#codigo_costos").val(codigo);
            aprobacion = $(this).data("aprobacion");

            if ( aprobacion == 0 ) {
                $("#requestAprob").removeClass("desactivado");
                $("#sendItem").addClass("desactivado");
            }else {
                $("#requestAprob").addClass("desactivado");
                $("#sendItem").removeClass("desactivado");
            }

            $.post(RUTA+"pedidos/numeroDocumento", {cc:codigo},
                function (data, textStatus, jqXHR) {
                    $("#numero").val(data.numero);
                    $("#nropedidoatach,#codigo_verificacion").val(data.codigo);
                    $("#listaPartidas ul")
                        .empty()
                        .append(data.partidas);


                },
                "json"
            );
        }else if(contenedor_padre == "listaCostos"){
            $("#codigo_costos").val(codigo);
        }else if(contenedor_padre == "listaAreas"){
            $("#codigo_area").val(codigo);
        }else if(contenedor_padre == "listaTransportes"){
            $("#codigo_transporte").val(codigo);
        }else if(contenedor_padre == "listaSolicitantes"){
            $("#codigo_solicitante").val(codigo);
        }else if(contenedor_padre == "listaTipo"){
            $("#codigo_tipo").val(codigo);

            if (codigo == 38) {
                $("#requestAprob").removeClass("desactivado");
                $("#sendItem").addClass("desactivado");
            }else if ( codigo == 37) {
                if (aprobacion == 0) {
                    $("#sendItem").addClass("desactivado");
                    $("#requestAprob").removeClass("desactivado");
                }else {
                    $("#sendItem").removeClass("desactivado");
                    $("#requestAprob").addClass("desactivado");
                }
            }
        }else if(contenedor_padre == "listaPartidas"){
            $("#codigo_partida").val(codigo);
        }

        return false;
    });

    $(".tituloVentana").on("click","a", function (e) {
        e.preventDefault();

        $(this).parent().parent().parent().parent().fadeOut();

        return false;
    });

    $("#closeProcess").click(function (e) { 
        e.preventDefault();

        $.post(RUTA+"pedidos/actualizaListado",
            function (data, textStatus, jqXHR) {
                $(".itemsTabla table tbody")
                    .empty()
                    .append(data);

                $("#proceso").fadeOut(function(){
                    grabado = false;
                    $("form")[1].reset();
                    $("#tablaDetalles tbody,.listaArchivos").empty();
                    $(".lista").fadeOut();
                });
            },
            "text"
        );
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

    $("#tablaModulos tbody").on("click","tr", function (e) {
        e.preventDefault();

        let nFilas = $.strPad($("#tablaDetalles tr").length,3);
        let idprod = $(this).data("idprod");
        let nunid = $(this).data("ncomed");
        let codigo = $(this).children('td:eq(0)').text();
        let descrip = $(this).children('td:eq(1)').text();
        let unidad = $(this).children('td:eq(2)').text();
        let grabado = 0;
        let items = "";

        equipos.forEach(element => {
            items += `<option value="${element.valor}">${element.registro}</option>`
        });

        let row = `<tr data-grabado="${grabado}" data-idprod="${idprod}" data-codund="${nunid}" data-idx="-">
                    <td class="textoCentro"><a href="#" title="eliminar" data-accion ="delete"><i class="fas fa-eraser"></i></a></td>
                    <td class="textoCentro"><a href="#" title="cambiar" data-accion ="change"><i class="fas fa-exchange-alt"></i></a></td>
                    <td class="textoCentro">${nFilas}</td>
                    <td class="textoCentro">${codigo}</td>
                    <td class="pl20px">${descrip}</td>
                    <td class="textoCentro">${unidad}</td>
                    <td><input type="number" step="any" placeholder="0.00" onchange="(function(el){el.value=parseFloat(el.value).toFixed(2);})(this)"></td>
                    <td><textarea></textarea></td>
                    <td class="textoCentro"><input type="text"></td>
                    <td class="textoCentro"><select name="registro">${items}</select></td>
                </tr>`;


        if (sw_accion == "n") {
            $("#tablaDetalles tbody").append(row);
        }
       
       

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
            if (result['codigo_transporte'] == '') throw "Elija Tipo de Transporte";
            if (result['concepto'] == '') throw "Escriba el concepto";
            if (result['codigo_solicitante'] == '') throw "Elija Solicitante";
            if (result['codigo_tipo'] == '') throw "Elija el tipo de pedido";
            if ($("#tablaDetalles tbody tr").length <= 0) throw "El pedido no tienes items";
            if (checkCantTables($("#tablaDetalles tbody > tr"),5)) throw "No ingreso cantidad en un item";

           if ( accion == 'n' ){
                $.post(RUTA+"pedidomtto/nuevoPedido", {cabecera:result,detalles:JSON.stringify(itemsSave())},
                    function (data, textStatus, jqXHR) {
                        mostrarMensaje(data.mensaje,data.clase);

                        grabado = true;
                        accion = "u";
                        
                        $("#tablaDetalles tbody > tr").attr("data-grabado",1);

                        $("#codigo_pedido").val(data.indice);

                        //$("#fileAtachs").trigger("submit");
                    },
                    "json"
                );
            }else if(accion == 'u'){
                $.post(RUTA+"pedidomtto/modificaPedido", {cabecera:result,detalles:JSON.stringify(itemsSave())},
                    function (data, textStatus, jqXHR) {
                        mostrarMensaje(data.mensaje,data.clase);
                        accion = "";
                    },
                    "json");
            }

        } catch (error) {
            mostrarMensaje(error,'mensaje_error');
        }

        return false;
    });

    $("#tablaPrincipal tbody").on("click","tr", function (e) {
        e.preventDefault();

        $.post(RUTA+"pedidomtto/consultaId", {id:$(this).data("indice")},
            function (data, textStatus, jqXHR) {
                
                let numero = $.strPad(data.cabecera[0].nrodoc,6);
                let estado = "textoCentro w35por estado " + data.cabecera[0].cabrevia;
                
                $("#codigo_costos").val(data.cabecera[0].idcostos);
                $("#codigo_area").val(data.cabecera[0].idarea);
                $("#codigo_transporte").val(data.cabecera[0].idtrans);
                $("#codigo_solicitante").val(data.cabecera[0].idsolicita);
                $("#codigo_partida").val(data.cabecera[0].idpartida);
                $("#codigo_tipo").val(data.cabecera[0].idtipomov);
                $("#codigo_pedido").val(data.cabecera[0].idreg);
                $("#codigo_estado").val(data.cabecera[0].estadodoc);
                $("#codigo_verificacion").val(data.cabecera[0].verificacion);
                $("#codigo_atencion").val(data.cabecera[0].nivelAten);
                $("#vista_previa").val(data.cabecera[0].docfPdfPrev);
                $("#numero").val(numero);
                $("#emision").val(data.cabecera[0].emision);
                $("#costos").val(data.cabecera[0].proyecto);
                $("#area").val(data.cabecera[0].area);
                $("#transporte").val(data.cabecera[0].transporte);
                $("#concepto").val(data.cabecera[0].concepto);
                $("#solicitante").val(data.cabecera[0].nombres);
                $("#tipo").val(data.cabecera[0].tipo);
                $("#vence").val(data.cabecera[0].vence);
                $("#estado").val(data.cabecera[0].estado);
                $("#espec_items").val(data.cabecera[0].detalle);
                $("#partida").val(data.cabecera[0].cdescripcion);
                $("#pedidommto").val(data.cabecera[0].nmtto);

               
                if (data.cabecera[0].idtipomov == 38) {
                    $("#requestAprob").removeClass("desactivado");
                    $("#sendItem").addClass("desactivado");
                }else {
                    if ( data.cabecera[0].veralm == 0 ){
                        $("#requestAprob").removeClass("desactivado");
                        $("#sendItem").addClass("desactivado");
                    }else {
                        $("#requestAprob").addClass("desactivado");
                        $("#sendItem").removeClass("desactivado");
                    }                    
                }

                $("#tablaDetalles tbody")
                    .empty()
                    .append(data.detalles);

                $("#estado")
                    .removeClass()
                    .addClass(estado);
                
                grabado = true;
            },
            "json"
        );

        accion = "u";
        $("#proceso").fadeIn();
        $("#saveItem").remove("desactivado");

        return false;
    });

    $("#tablaDetalles").on('click','a', function(e) {
        e.preventDefault();

        let fila = $(this).parent().parent();

        if ($(this).data("accion") == "delete") {
           

            if ($(this).attr("href") == "#") {
                    $(this).parent().parent().remove();
                    fillTables($("#tablaDetalles tbody > tr"),1);
            }else {
                $.post(RUTA+"pedidos/quitarItem", {query:"UPDATE tb_pedidodet SET tb_pedidodet.nflgactivo =:estado WHERE tb_pedidodet.iditem =:id",
                                                            id:$(this).attr("href")},
                    function (data, text, requestXHR) {
                        fila.remove();
                        fillTables($("#tablaDetalles tbody > tr"),1);
                    },
                    "text"
                );
            };
        }else {
            fila.remove();

            $.post(RUTA+"pedidos/llamaProductos", {tipo:$("#codigo_tipo").val()},
            function (data, textStatus, jqXHR) {
                $("#tablaModulos tbody")
                    .empty()
                    .append(data);

                $("#busqueda").fadeIn();
                sw_accion = "c";
            },
            "text"
        );
            
        }

        
        return false;
    });
})

listarEquipos = () => {
    let datos = [];
    
    $.post(RUTA+"pedidomtto/registroEquipos",
        function (data, text, requestXHR) {
            data.forEach(element => {
                datos.push(element);
            });
        },
        "json"
    );

    return datos;
}

itemsSave = () =>{
    DATA = [];
    let TABLA = $("#tablaDetalles tbody >tr");

    TABLA.each(function(){
        let IDPROD      = $(this).data('idprod'),
            UNIDAD      = $(this).data('codund'),
            CANTIDAD    = $(this).find('td').eq(6).children().val(),
            IDX         = $(this).data('idx'),
            CALIDAD     = 0,
            ESTADO      = $(this).attr('data-grabado'),
            ESPECIFICA  = $(this).find('td').eq(7).children().val(),
            NROPARTE    = $(this).find('td').eq(8).children().val(),
            REGISTRO    = $(this).find("select[name='registro']").val(),
            REGISTEXT   = $(this).find("select[name='registro'] option:selected").text();

        item= {};
        
        if (ESTADO == 0) {
            item['idprod']      = IDPROD;
            item['unidad']      = UNIDAD;
            item['cantidad']    = CANTIDAD;
            item['nroparte']    = NROPARTE;
            item['itempedido']  = IDX;
            item['calidad']     = CALIDAD;
            item['especifica']  = ESPECIFICA;
            item['estado']      = ESTADO;
            item['registro']    = REGISTRO;
            item['registext']   = REGISTEXT;

            DATA.push(item);
        } 
    })

    return DATA;
}
