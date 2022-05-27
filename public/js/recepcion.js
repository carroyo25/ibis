$(function(){
    let accion = "",
        grabado = false,
        co = 0;

    $("#esperar").fadeOut();

    $("#saveOrder").click(function (e) { 
        e.preventDefault();
        
        let result = {};

        $.each($("#formProceso").serializeArray(),function(){
            result[this.name] = this.value;
        });

        return false;
    });
    
    $("#nuevoRegistro").click(function (e) { 
        e.preventDefault();

        $("#estado")
            .removeClass()
            .addClass("textoCentro estado procesando");
        $("#proceso").fadeIn();
        
        accion = 'n';

        return false;
    });

    $("#closeProcess").click(function (e) { 
        e.preventDefault();

        $("#proceso").fadeOut();

        /*$.post(RUTA+"pedidos/actualizaListado",
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
        );*/

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

        if (contenedor_padre == "listaAlmacen"){
            $("#codigo_almacen").val(codigo);

            $.post(RUTA+"recepcion/numeroIngreso", {id:codigo},
                function (data, textStatus, jqXHR) {
                    $("#numero").val(data.numero);
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
        
        $.post(RUTA+"recepcion/ordenes",
            function (data, textStatus, jqXHR) {
                $("#ordenes tbody")
                    .empty()
                    .append(data);
                $("#busqueda").fadeIn();
            },
            "text"
        );
        
        return false;
    });

    $(".tituloVentana").on("click","a", function (e) {
        e.preventDefault();

        $(this).parent().parent().parent().parent().fadeOut();

        return false;
    });

    $("#ordenes tbody").on("click","tr", function (e) {
        e.preventDefault();

        $.post(RUTA+"recepcion/ordenId",{id:$(this).data("orden")},
            function (data, textStatus, jqXHR) {
                    $("#codigo_costos").val(data.cabecera[0].ncodcos);
                    $("#codigo_area").val(data.cabecera[0].ncodarea);
                    $("#codigo_orden").val(data.cabecera[0].id_regmov);
                    $("#codigo_pedido").val(data.cabecera[0].id_refpedi);
                    $("#codigo_estado").val(data.cabecera[0].nEstadoDoc);
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

     //añadir registro de adjuntos
    $("#fileAtachs").on("submit", function (e) {
        e.preventDefault()

        let ainfo = new FormData(this)
            ainfo.append("nota",$("#codigo_ingreso").val());

        $.ajax({
            // URL to move the uploaded image file to server
            url: RUTA + 'recepcion/adjuntos',
            // Request type
            type: "POST", 
            // To send the full form data
            data: new FormData( this ),
            contentType:false,      
            processData:false,
            dataType:"json",    
            // UI response after the file upload  
            success: function(data)
            {   
                
            }
        });
        
        return false;
    });

    $("#tablaDetalles tbody").on("click","a", function (e) {
        e.preventDefault();

        let filas = parseInt($(this).parent().parent().find("td").eq(6).children().val()),
            orden = $(this).parent().parent().data('detorden'),
            producto = $(this).parent().parent().data('idprod'),
            almacen = $("#codigo_almacen").val(),
            nombre = $(this).parent().parent().find("td").eq(3).text();

            row = `<tr data-orden="${orden}" data-producto="${producto}" data-almacen="${almacen}">
                        <td>${nombre}</td>
                        <td><input type="text"></td>
                    </tr>`

    
        $("#tablaSeries tbody").empty();

        for (let index = 0; index < filas; index++) {
            $("#tablaSeries").append(row);        
        }

        $("#series").fadeIn();

        return false;
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

    $("#saveOrden").click(function (e) { 
        e.preventDefault();
        
        let result = {};

        $.each($("#formProceso").serializeArray(),function(){
            result[this.name] = this.value;
        });

        try {
            if (result['codigo_almacen'] == '') throw "Elija el Almacen";
            if (result['codigo_costos'] == '') throw "Elija Centro de Costos";
            if (result['codigo_aprueba'] == '') throw "Elija la persona que aprueba";
            if (result['codigo_movimiento'] == '') throw "Elija tipo de movimiento";
            if (result['guia'] == '') throw "Escriba el número de guia"

            $.post(RUTA+"recepcion/nuevoIngreso", {cabecera:result,
                                                    detalles:JSON.stringify(detalles()),
                                                    series:JSON.stringify(series())},
                function (data, textStatus, jqXHR) {
                    console.log(data);
                },
                "json"
            );
        } catch (error) {
            mostrarMensaje(error,'mensaje_error');
        }

        return false;
    });
})

detalles = () =>{
    DETALLES = [];

    let TABLA = $("#tablaDetalles tbody >tr");
    
    TABLA.each(function(){
        let ITEM        = $(this).find('td').eq(1).text(),
            IDDETORDEN  = $(this).data("detorden"),
            IDDETPED    = $(this).data("iddetped"),
            IDPROD      = $(this).data("idprod"),
            PEDIDO      = $("#codigo_pedido").val(),
            ORDEN       = $("#codigo_orden").val(),
            ALMACEN     = $("#codigo_almacen").val(),
            CANTREC     = $(this).find('td').eq(6).children().val(),
            OBSER       = $(this).find('td').eq(7).children().val(),
            VENCE       = $(this).find('td').eq(8).children().val();  
    
        item = {};

        item['item']        = ITEM;
        item['iddetorden']  = IDDETORDEN;
        item['iddetped']    = IDDETPED;
        item['idprod']      = IDPROD;
        item['pedido']      = ORDEN;
        item['orden']       = PEDIDO;
        item['almacen']     = ALMACEN;
        item['cantrec']     = CANTREC;
        item['obser']       = OBSER;
        item['vence']       = VENCE;

        DETALLES.push(item);
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
            SERIE  = $(this).find('td').eq(1).children().val();
    
        item = {};

        item['orden'] = ORDEN;
        item['almacen'] = ALMACEN;
        item['producto'] = PRODUCTO;
        item['serie']= SERIE;

        SERIES.push(item);
    })

    return SERIES;
}