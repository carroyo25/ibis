$(function(){
    let accion = "",
        co = 1,
        fila = "",
        idfila = "",
        grabado = false;
    
    let tipoVista = null;
       
    $("#esperar").fadeOut();

    $("#tablaPrincipal tbody").on("click","tr", function (e) {
        e.preventDefault();

        $.post(RUTA+"recepcion/consultaId", {id:$(this).data("indice")},
            function (data, textStatus, jqXHR) {
                
                let estado = "textoCentro w100por estado " + data.cabecera[0].estado;
                
                $("#codigo_costos").val(data.cabecera[0].ncodpry);
                $("#codigo_area").val(data.cabecera[0].ncodarea);
                $("#codigo_movimiento").val(data.cabecera[0].ncodmov);
                $("#codigo_aprueba").val(data.cabecera[0].id_userAprob);
                $("#codigo_almacen").val(data.cabecera[0].ncodalm1);
                $("#codigo_pedido").val(data.cabecera[0].idref_pedi);
                $("#codigo_orden").val(data.cabecera[0].idref_abas);
                $("#codigo_estado").val(data.cabecera[0].nEstadoDoc);
                $("#codigo_entidad").val(data.cabecera[0].id_centi);
                $("#codigo_ingreso").val(data.cabecera[0].id_regalm);
                $("#almacen").val(data.cabecera[0].almacen);
                $("#fecha").val(data.cabecera[0].ffecdoc);
                $("#numero").val(data.cabecera[0].nnronota);
                $("#proyecto").val(data.cabecera[0].proyecto);
                $("#area").val(data.cabecera[0].area);
                $("#solicita").val(data.cabecera[0].nombres);
                $("#orden").val(data.cabecera[0].orden);
                $("#pedido").val(data.cabecera[0].pedido);
                $("#ruc").val(data.cabecera[0].cnumdoc);
                $("#guia").val(data.cabecera[0].cnumguia);
                $("#razon").val(data.cabecera[0].crazonsoc);
                $("#concepto").val(data.cabecera[0].concepto);
                $("#detalle").val(data.cabecera[0].detalle);
                $("#aprueba").val(data.cabecera[0].cnombres);
                $("#tipo").val(data.cabecera[0].cdescripcion);
                $("#estado").val(data.cabecera[0].estado);
                $("#movimiento").val(1);
                
                let swqaqc = data.cabecera[0].nflgCalidad == 1 ? true: false;
                tipoVista = true;
                accion = "u";
                
                $("#qaqc").prop("checked",swqaqc);
                
                $("#estado")
                    .removeClass()
                    .addClass(estado);
                
                $("#tablaDetalles tbody")
                    .empty()
                    .append(data.detalles);
                
                $("#tablaSeries tbody")
                    .empty()
                    .append(data.series);

                $(".listaArchivos")
                    .empty()
                    .append(data.adjuntos);

                $(".primeraBarra").css("background","#0078D4");
                $(".primeraBarra span").text("Datos Generales");

                accion = "u";
                grabado = true;

                $("#proceso").fadeIn();

            },
            "json"
        );

        return false;
    });

    $("#nuevoRegistro").click(function (e) { 
        e.preventDefault();

        $("#estado")
            .removeClass()
            .addClass("textoCentro estado w100por procesando");
        $("#proceso").fadeIn();
        
        accion = 'n';
        tipoVista = null;
        grabado = false;

        $(".primeraBarra").css("background","#0078D4");
        $(".primeraBarra span").text('Datos Generales');
        $("#tablaDetalles tbody").empty();

        return false;
    });

    $("#closeProcess").click(function (e) { 
        e.preventDefault();

        $("#proceso").fadeOut();

        $.post(RUTA+"recepcion/actualizaNotas",
            function (data, textStatus, jqXHR) {
                $(".itemsTabla table tbody")
                    .empty()
                    .append(data);

                $("#proceso").fadeOut(function(){
                    grabado = false;
                    $("form")[0].reset();
                    $("form")[1].reset();
                    $("#tablaDetalles tbody,.listaArchivos").empty();
                });
            },
            "text"
        );

        return false;
    });

    $(".mostrarLista").focus(function (e) { 
        e.preventDefault();

        /*if (accion !="n") {
            return false;
        }*/
        
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

        if (contenedor_padre == "listaAlmacen"){
            $("#codigo_almacen").val(codigo);

            $.post(RUTA+"recepcion/numeroIngreso", {id:codigo},
                function (data, textStatus, jqXHR) {
                    $("#numero").val(data.numero);
                    $("#movimiento").val(data.movimiento);
                },
                "json"
            );
        }else if(contenedor_padre == "listaAprueba"){
            $("#codigo_aprueba").val(codigo);
        }else if(contenedor_padre == "listaMovimiento"){
            $("#codigo_movimiento").val(codigo);
        }

        return false;
    });

    $("#importData").click(function (e) { 
        e.preventDefault();
        
        try {
            if (accion == "u") throw "No se puede añadir mas registros al ingreso";

            $.post(RUTA+"recepcion/ordenes",
                function (data, textStatus, jqXHR) {
                    $("#ordenes tbody")
                        .empty()
                        .append(data);
                    $("#busqueda").fadeIn();
                },
                "text"
            );
        } catch (error) {
            mostrarMensaje(error,"mensaje_error");
        }
        
        return false;
    });

    $(".tituloVentana").on("click","a", function (e) {
        e.preventDefault();

        $(this).parent().parent().parent().parent().fadeOut();

        return false;
    });

    $("#ordenes tbody").on("click","tr", function (e) {
        e.preventDefault();

        $("#tipo").val("ORDEN DE COMPRA");
        $("#codigo_movimiento").val(89);

        $.post(RUTA+"recepcion/ordenId",{id:$(this).data("orden")},
            function (data, textStatus, jqXHR) {
                    suma = 0;

                    $("#codigo_costos").val(data.cabecera[0].ncodcos);
                    $("#codigo_area").val(data.cabecera[0].ncodarea);
                    $("#codigo_orden").val(data.cabecera[0].id_regmov);
                    $("#codigo_pedido").val(data.cabecera[0].id_refpedi);
                    $("#codigo_estado").val(data.cabecera[0].nEstadoDoc);
                    $("#codigo_entidad").val(data.cabecera[0].id_centi);
                    $("#codigo_entidad").val(data.cabecera[0].id_centi);
                    $("#proyecto").val(data.cabecera[0].costos);
                    $("#area").val(data.cabecera[0].area);
                    $("#solicita").val(data.cabecera[0].solicita);
                    $("#orden").val(data.cabecera[0].cnumero);
                    $("#pedido").val(data.cabecera[0].pedido);
                    $("#ruc").val(data.cabecera[0].cnumdoc);
                    $("#razon").val(data.cabecera[0].crazonsoc);
                    $("#concepto").val(data.cabecera[0].concepto);
                    $("#detalle").val(data.cabecera[0].detalle);
                    $("#almacen").val(data.cabecera[0].cdesalm);
                    $("#codigo_almacen").val(data.cabecera[0].ncodalm);
                    $("#numero").val(data.numero.numero);
                    $("#movimiento").val(data.numero.movimiento);
                
                    $("#tablaDetalles tbody")
                        .empty()
                        .append(data.detalles);

                    $("#items").val($("#tablaDetalles tbody tr").length);

                $("#busqueda").fadeOut();
            },
            "json"
        );
        return false
    });

    $("#btnAceptItems").click(function (e) { 
        e.preventDefault();
        
        if (co != 0) {
            $.post(RUTA+"recepcion/cabeceraIngreso", {id:co},
                function (data, textStatus, jqXHR) {
                    $("#codigo_costos").val()
                    $("#codigo_area").val()
                    $("#codigo_orden").val()
                    $("#codigo_estado").val()
                    $("#codigo_entidad").val()
                    $("#proyecto").val(data.cabecera[0].costos)
                    $("#solicita").val()
                    $("#orden").val()
                    $("#pedido").val()
                    $("#ruc").val()
                    $("#razon").val()
                    $("#concepto").val()
                    $("#detalle").val()
                },
                "json"
            );
        }

        return false
    });

    $("#atachDocs").click(function (e) { 
        e.preventDefault();
        
        $("#archivos").fadeIn();

        return false;
    });

    $("#openArch").click(function (e) { 
        e.preventDefault();
 
        if (accion == "n")
            $("#uploadAtach").trigger("click");
 
        return false;
    });

    $("#uploadAtach").on("change", function (e) {
        e.preventDefault();
 
        let fp = $(this);
        let lg = fp[0].files.length;
        let items = fp[0].files;
        let fragment = "";
 
        if (lg > 0) {
             for (var i = 0; i < lg; i++) {
                 var fileName = items[i].name; // get file name
 
                 // append li to UL tag to display File info
                 fragment +=`<li><p><i class="far fa-file"></i></p>
                                 <p>${fileName}</p></li>`;
             }
 
             $(".listaArchivos").append(fragment);
         }
 
        return false;
    });
 
    $("#btnConfirmAtach").on("click", function (e) {
         e.preventDefault();
 
         $("#archivos").fadeOut();
 
         return false;
    });
 
    $("#btnCancelAtach").on("click", function (e) {
         e.preventDefault();
 
         $("#archivos").fadeOut();
         $("#fileAtachs")[0].reset();
         $(".listaArchivos").empty();
 
    });

    $("#btnCancelSeries").click(function (e) { 
        e.preventDefault();

        $("#tablaSeries tbody").empty();
        $("#series").fadeOut();
        
        return false;
    });

    $("#btnConfirmSeries").click(function (e) { 
        e.preventDefault();

        $("#series").fadeOut();
        
        return false;
    });

    $("#preview").click(function (e) { 
        e.preventDefault();
        
        let result = {};

        $.each($("#formProceso").serializeArray(),function(){
            result[this.name] = this.value;
        });

        try {
            if ( tipoVista == null ) throw "Debe grabar el documento...";
            if ( !grabado ) throw "Debe grabar el documento";

            $.post(RUTA+"recepcion/documentopdf",{cabecera:result,
                                                    detalles:JSON.stringify(detalles(tipoVista)),
                                                    condicion:0},
                function (data, textStatus, jqXHR) {
                    $(".ventanaVistaPrevia iframe")
                    .attr("src","")
                    .attr("src",data);

                    $("#vistaprevia").fadeIn();
                },
                "text"
            );
        } catch (error) {
            mostrarMensaje(error,'mensaje_error');
        }

        return preview;
    });

    $("#closePreview").click(function (e) { 
        e.preventDefault();

        $(".ventanaVistaPrevia iframe").attr("src","");
        $("#vistaprevia").fadeOut();

        return false;
    });
  
    $("#btnConsulta").on('click', function(e) {
        e.preventDefault();

        let str = $("#formConsulta").serialize();

        $.post(RUTA+"recepcion/filtroRecepcion", str,
            function (data, text, requestXHR) {
                $("#tablaPrincipal tbody")
                    .empty()
                    .append(data);
            },
            "text"
        );
        
        return false
    });

    $("#itemCostos").change(function (e) { 
        e.preventDefault(e);

        $.post(RUTA+"recepcion/ordenesPorCosto", {costo:$(this).val()},
            function (data, textStatus, jqXHR) {
                $("#ordenes tbody")
                    .empty()
                    .append(data);
            },
            "text"
        );

        return false        
    });

    $("#ordenSearch").keyup(function (e) { 
        if(e.which == 13) {
            $("#esperar").fadeIn();
            
            $.post(RUTA+"recepcion/filtraOrden", {id:$(this).val()},
                function (data, textStatus, jqXHR) {
                    $("#ordenes tbody")
                        .empty()
                        .append(data);
                    $("#esperar").fadeOut();
                },
                "text"
            );
        }
    });

    $("#btnPendientes, #btnTotales").click(function (e) { 
        e.preventDefault();

        tipoVista = "";

        tipoVista = e.target.id == "btnTotales"?true:false;

        let result = {};

        
        $.each($("#formProceso").serializeArray(),function(){
            result[this.name] = this.value;
        });

        try {
            if (result['codigo_almacen'] == '') throw "Elija el Almacen";
            if (result['codigo_aprueba'] == '') throw "Elija la persona que aprueba";
            if (result['guia'] == '') throw "Escriba el número de guia";
            if (verificarCantidadesInput()) throw "Verifque las cantidades ingresadas";
            if (detalles(tipoVista).length == 0) throw "No hay items que procesar";

            if (accion == "n") {
                
                $.post(RUTA+"recepcion/nuevoIngreso", {cabecera:result,
                    detalles:JSON.stringify(detalles(tipoVista)),
                    series:JSON.stringify(series())},
                        function (data, textStatus, jqXHR) {
                            $("#codigo_ingreso").val(data.indice);
                            mostrarMensaje("Nota Grabada","mensaje_correcto");
                            $("#tablaPrincipal tbody")
                                .empty()
                                .append(data.listado);
                            
                            accion = "";
                            grabado = true;

                            $(".primeraBarra").css("background","#819830");
                            $(".primeraBarra span").text('Datos Generales ... Grabado');
                        },
                        "json"
                    );
            }else if(accion == "u"){
                $.post(RUTA+"recepcion/modificarRegistro",  {cabecera:result,
                    detalles:JSON.stringify(detalles(true))},
                    function (data, textStatus, jqXHR) {
                        mostrarMensaje("Nota Modificada","mensaje_correcto");
                        
                        accion = "";
                        grabado = true;
                    },
                    "json"
                );
            }

        } catch (error) {
            mostrarMensaje(error,'mensaje_error');
        }
        
        return false;
    });

    //para no poner cantidades mayores en el formulario
    $("#tablaDetalles tbody").on('blur','input', function (e) {
        try {
            let ingreso = parseInt($(this).parent().parent().find("td").eq(7).children().val());
            let stock = parseInt($(this).parent().parent().find("td").eq(6).text());

            if(ingreso > stock) {
                mostrarMensaje('La cantidad ingresada, es mayor al stock','mensaje_error')
                return false;
            }

        } catch (error) {
            
        }
    });

    $("#atachDocs").click(function (e) { 
        e.preventDefault();

        try {
            if ($("#codigo_orden").val() == "") throw "Seleccione un orden, para ver los adjuntos";

            $.post(RUTA+"recepcion/verAdjuntos", {id:$("#codigo_orden").val()},
                function (data, textStatus, jqXHR) {
                    $("#listaAdjuntos").empty().append(data.adjuntos);
                    $("#vistaAdjuntos").fadeIn();
                },
                "json"
            );
        } catch (error) {
            mostrarMensaje(error,'mensaje_error')
        }
       
        return false;
    });

    $("#closeAtach").click(function(e){
        e.preventDefault();

        $("#vistaAdjuntos").fadeOut();
        $(".ventanaAdjuntos iframe").attr("src","");

        return false;
    });

    $("#vistaAdjuntos").on("click","a", function (e) {
        e.preventDefault();
        
        $(".ventanaAdjuntos iframe")
            .attr("src","")
            .attr("src","public/documentos/ordenes/adjuntos/"+$(this).attr("href"));
        
        return false;
    });

    $("#tablaDetalles tbody").on("click","a", function (e) {
        e.preventDefault();

        fila = $(this).parent().parent();
        idfila = $(this).parent().parent().data('iddetped');

        if ($(this).data("accion") == "setSerial") {
            let filas   = parseInt($(this).parent().parent().find("td").eq(7).children().val()),
            orden       = $(this).parent().parent().data('detorden'),
            producto    = $(this).parent().parent().data('idprod'),
            almacen     = $("#codigo_almacen").val(),
            nombre      = $(this).parent().parent().find("td").eq(3).text(),
            item        = $(this).parent().parent().data('iddetped');

            row = `<tr data-orden="${orden}" data-producto="${producto}" data-almacen="${almacen}" data-itempedido="${item}">
                        <td>${nombre}</td>
                        <td><input type="text"></td>
                    </tr>`

            if (accion == 'n') {
                $("#tablaSeries tbody").empty();

                for (let index = 0; index < filas; index++) {
                    $("#tablaSeries").append(row);        
                }
            }

            $("#series").fadeIn();
        }else {
            $.post(RUTA+"recepcion/existeSalida", {id:$(this).parent().parent().data('iddetped')},
                function (data, textStatus, jqXHR) {
                    if (data == 1)
                      mostrarMensaje("EL item ya tiene registro de salida","mensaje_error");
                    else
                        $("#pregunta").fadeIn();
                },
                "text"
            );
        }
        

        return false;
    });

    $("#btnAceptarPregunta").click(function (e) { 
        e.preventDefault();

        $.post(RUTA+"recepcion/marcaItem", {id:idfila},
            function (data, textStatus, jqXHR) {
                fila.remove();
                fillTables($("#tablaDetalles tbody > tr"),2);

                $("#pregunta").fadeOut();
            },
            "text"
        );
        
        return false;
    });

    $("#btnCancelarPregunta").click(function (e) { 
        e.preventDefault();
        
        $("#pregunta").fadeOut();

        return false;
    });

    $("#cancelRegister").click(function (e) { 
        e.preventDefault();

        $("#preguntaAnula").fadeIn();

        return false;
    });

    $("#btnAceptarAnula").click(function(e){
        e.preventDefault();

        //usuarios autorizados para anular
        let users = ['628c5d20e3173','62145bbb5a092'],
            usuario = $("#id_user").val();
        try {
            if(!users.includes(usuario)) throw Error("No puede realizar esta accion!");

            formData = new FormData();
            formData.append('id',$("#codigo_ingreso").val());
            formData.append('orden',$("#orden").val());

            $("#esperar").css("opacity","1").fadeIn();

            fetch(RUTA+'recepcion/anular',{
                method: 'POST',
                body:formData
            })
            .then(response => response.json())
            .then(data => {
                $("#preguntaAnula").fadeOut();
                $("#esperar").css("opacity","0").fadeOut();
                mostrarMensaje(data.mensaje,data.clase);
                
            })
        } catch (error) {
            mostrarMensaje(error,"mensaje_error");
        }

        return false;
    });

    $("#btnCancelarAnula").click(function(e){
        e.preventDefault();

        $("#preguntaAnula").fadeOut();

        return false;
    });
})

detalles = (flag) =>{
    DETALLES = [];

    let TABLA = $("#tablaDetalles tbody >tr");
    
    TABLA.each(function(){
        let  CHECKED  = $(this).find('td').eq(1).children().prop("checked");

        if ( CHECKED === flag ) {
            if ( $(this).find('td').eq(7).children().val() > 0) {
                let item = {};

                item['item']        = $(this).find('td').eq(1).text();
                item['iddetorden']  = $(this).data("detorden");
                item['iddetped']    = $(this).data("iddetped");
                item['idprod']      = $(this).data("idprod");
                item['iddeting']    = $(this).data("iddetnota");
                item['pedido']      = $("#codigo_pedido").val();
                item['orden']       = $("#codigo_orden").val();
                item['almacen']     = $("#codigo_almacen").val();
                item['cantrec']     = $(this).find('td').eq(7).children().val();
                item['cantsol']     = parseFloat($(this).find('td').eq(6).text());
                item['cantsal']     = null;
                item['obser']       = $(this).find('td').eq(8).children().val();
                item['vence']       = null;

                item['codigo']     = $(this).find('td').eq(3).text();
                item['descripcion']= $(this).find('td').eq(4).text();
                item['unidad']     = $(this).find('td').eq(5).text();
                item['nestado']    = null;
                item['cestado']    = null;
                item['ubicacion']  = null;
                item['itemstat']   = CHECKED;

                DETALLES.push(item);
            }
        }
    })

    return DETALLES; 
}

series = () => {
    SERIES = [];

    let TABLA = $("#tablaSeries tbody >tr");

    TABLA.each(function(){

        let ORDEN   = $(this).data('orden'),
            ALMACEN = $("#codigo_almacen").val(),
            PRODUCTO = $(this).data('producto'),
            IDPED    = $(this).data('itempedido')
            SERIE  = $(this).find('td').eq(1).children().val();
    
        item = {};

        if (SERIE != ""){
            item['orden']       = ORDEN;
            item['almacen']     = ALMACEN;
            item['producto']    = PRODUCTO;
            item['serie']       = SERIE;
            item['idped']       = IDPED;
        }
        

        SERIES.push(item);
    })

    return SERIES;
}

verificarCantidadesInput = () =>{
    let TABLA = $("#tablaDetalles tbody >tr"),
        errorCantidad = false;

    TABLA.each(function(){
        let cantidad    = parseInt($(this).find("td").eq(6).text()),// cantidad
            cantdesp    = parseInt($(this).find('td').eq(7).children().val());

        if( cantidad < cantdesp) {
            errorCantidad = true
        }       
    })

    return errorCantidad;
}