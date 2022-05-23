$(function(){
    var accion = "";
    var grabado = false;

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
        let aprobacion = $(this).data("aprobacion");
        
        control.slideUp()

        destino.val($(this).text());
        id = destino.attr("id");

        if (contenedor_padre == "listaCostos"){
            $("#codigo_costos").val(codigo);

            if (aprobacion == 0) {
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
        }

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
                    $("form")[0].reset();
                    $("form")[1].reset();
                    $("#tablaDetalles tbody,.listaArchivos").empty();
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

    //cuanndo cambia algo en la tabla de detalles
    $("#tablaDetalles tbody ").on("change","input", function (e) {
        //e.preventDefault();

        if (accion == 'u') {
            $(this).parent().parent().attr("data-grabado",0);
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

            if (accion == 'n'){
                $.post(RUTA+"pedidos/nuevoPedido", {cabecera:result,detalles:JSON.stringify(itemsSave())},
                    function (data, textStatus, jqXHR) {
                        mostrarMensaje(data.mensaje,data.clase);

                        $("#fileAtachs").trigger("submit");
                        $("#saveItem").addClass("desactivado");
                        
                        grabado = true;
                        accion = null;
                    },
                    "json"
                );
            }else{
                $.post(RUTA+"pedidos/modificaPedido", {cabecera:result,detalles:JSON.stringify(itemsSave())},
                    function (data, textStatus, jqXHR) {
                        mostrarMensaje(data.mensaje,data.clase);
                    },
                    "json"
            );
            }

        } catch (error) {
            mostrarMensaje(error,'mensaje_error');
        }

        return false;
    });

    $("#vence").change(function (e) { 
        e.preventDefault();
        let dias = diferenciadefechas($(this).val(),$("#emision").val());
        
        $("#dias_atencion").val(dias);

        if ( dias <= 0 ){
            mostrarMensaje("Verifique la fecha de pedido","mensaje_error");
        }else if(dias <= 7){
            $("#codigo_atencion").val(46);
        }else if(dias > 7){
            $("#codigo_atencion").val(47);
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

        if (!checkExistTable($("#tablaDetalles tbody tr"),codigo,2)){
            let row = `<tr data-grabado="${grabado}" data-idprod="${idprod}" data-codund="${nunid}" data-indice="-">
                        <td class="textoCentro"><a href="#"><i class="fas fa-eraser"></i></a></td>
                        <td class="textoCentro">${nFilas}</td>
                        <td class="textoCentro">${codigo}</td>
                        <td class="pl20px">${descrip}</td>
                        <td class="textoCentro">${unidad}</td>
                        <td><input type="number" step="any" placeholder="0.00" onchange="(function(el){el.value=parseFloat(el.value).toFixed(2);})(this)"></td>
                        <td></td>
                        <td class="textoCentro"><input type="checkbox"></td>
                    </tr>`;

            $("#tablaDetalles tbody").append(row);
        }else{
            mostrarMensaje("Item duplicado","mensaje_error")
        }

        return false;
    });

    $("#tablaPrincipal tbody").on("click","tr", function (e) {
        e.preventDefault();

        $.post(RUTA+"pedidos/consultaId", {id:$(this).data("indice")},
            function (data, textStatus, jqXHR) {
                
                let numero = $.strPad(data.cabecera[0].nrodoc,6);
                let estado = "textoCentro w35por estado " + data.cabecera[0].cabrevia;
                
                $("#codigo_costos").val(data.cabecera[0].idcostos);
                $("#codigo_area").val(data.cabecera[0].idarea);
                $("#codigo_transporte").val(data.cabecera[0].idtrans);
                $("#codigo_solicitante").val(data.cabecera[0].idsolicita);
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

                if (data.cabecera[0].veralm == 0) {
                    $("#requestAprob").removeClass("desactivado");
                    $("#sendItem").addClass("desactivado");
                }else {
                    $("#requestAprob").addClass("desactivado");
                    $("#sendItem").removeClass("desactivado");
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

        return false;
    });

    $("#upAttach").click(function (e) { 
       e.preventDefault();
    
       if ($("#numero").val() == ""){
            mostrarMensaje("Faltan datos del pedido","mensaje_error")
       }else{
            $("#archivos").fadeIn();
       }
       
       return false;
    });

    $("#preview").click(function (e) { 
        e.preventDefault();
    
        if ($("#numero").val() == ""){
            mostrarMensaje("Faltan datos del pedido","mensaje_error")
        }else{
            let result = {};

            $.each($("#formProceso").serializeArray(),function(){
                result[this.name] = this.value;
            })

            $.post(RUTA+"pedidos/vistaprevia", {cabecera:result,detalles:JSON.stringify(itemsPreview())},
                function (data, textStatus, jqXHR) {
                    $(".ventanaVistaPrevia iframe")
                    .attr("src","")
                    .attr("src","public/documentos/pedidos/vistaprevia/"+data);

                    $("#vista_previa").val(data);

                    $("#vistaprevia").fadeIn();
                },
                "text"
            );
        }
        
        return false;
    });

    $("#closePreview").click(function (e) { 
        e.preventDefault();

        $(".ventanaVistaPrevia iframe").attr("src","");
        $("#vistaprevia").fadeOut();

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

        $.ajax({
            // URL to move the uploaded image file to server
            url: RUTA + 'pedidos/adjuntos',
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

    $("#sendItem,#requestAprob").click(function (e) { 
        e.preventDefault();

        $("#estadoPedido,#codigo_estado").val($(this).data("estado"));
                
        if (grabado){
            $.post(RUTA+"pedidos/buscaRol", {rol:$(this).data("rol"),cc:$("#codigo_costos").val()},
                function (data, textStatus, jqXHR) {
                    $("#listaCorreos tbody").empty().append(data);
                    $("#sendMail").fadeIn();
                },
                "text"
            );
        }else{
            mostrarMensaje("Por favor grabar el pedido","mensaje_error");
        }

        return false;
    });

    $("#closeMail").click(function (e) { 
        e.preventDefault();

        $("form")[2].reset();
        $(".atachs").empty();
        $(".messaje div").empty();
        $("#sendMail").fadeOut();
        $("#estadoPedido,#codigo_estado").val(49);

        return false;
    });
    
    //proceso con el correo
    //para cambiar el tipo de letra en el mensaje
   $(".js-boton").mousedown(function(event) {
        event.preventDefault(); // Esto no es necesario, es por vicio xD
            
        var comando = $(this).attr('data-type');
        document.execCommand(comando, false, null);

        return false
   });

   $("#btnAtach").click(function (e) { 
        e.preventDefault();
        
        $("#mailAtach").trigger("click");
        return false;
   });

   $("#mailAtach").on("change", function (e) {
        e.preventDefault();
 
        let fp = $(this);
        let lg = fp[0].files.length;
        let items = fp[0].files;
        let fragment = "";

        adjuntos = hadfledFiles(this.files);
 
        if (lg > 0) {
             for (let i = 0; i < lg; i++) {
                 let fileName = items[i].name; // get file name
 
                 // append li to UL tag to display File info
                 fragment +=`<li><a href="${i}"><i class="far fa-file"></i> ${fileName}</a></li>`;
             }
 
             $(".atachs").append(fragment);
         }
 
        return false;
   });

   $("#btnConfirmSend").click(function (e) { 
       e.preventDefault();

       let result = {};

       $.each($("#formProceso").serializeArray(),function(){
           result[this.name] = this.value;
       })

       $.post(RUTA+"pedidos/vistaprevia", {cabecera:result,detalles:JSON.stringify(itemsPreview())},
            function (data, textStatus, jqXHR) {
                $("#vista_previa").val(data);
                $("#formMails").trigger("submit");
            },
            "text"
        );

       
       return false;
   });

   //generar el documento y enviar el emitido
   $("#formMails").submit(function (e) { 
       e.preventDefault();

        let parametros = new FormData( this );
            parametros.append("correos", JSON.stringify(mailsList()));
            parametros.append("mensaje",$(".messaje div").html());
            parametros.append("pedido",$("#codigo_pedido").val());
            parametros.append("detalles",JSON.stringify(itemsPreview()));
            parametros.append("emitido",$("#vista_previa").val());

        $.ajax({
                // URL to move the uploaded image file to server
                url: RUTA + 'pedidos/envioCorreos',
                // Request type
                type: "POST", 
                // To send the full form data
                data: parametros,
                contentType:false,      
                processData:false,
                dataType:"json",    
                // UI response after the file upload  
                success: function(response)
                {   
                    //$("#closeMail,#closePreview,#closeProcess").trigger("click");
                    //$("#closeMail").trigger("click");
                    mostrarMensaje(response.mensaje,response.clase);
                    $("#sendMail").fadeOut();
                }
            });

       return false;
   });
})

itemsPreview = () =>{
    DATA = [];
    let TABLA = $("#tablaDetalles tbody >tr");

    TABLA.each(function(){
        let ITEM        = $(this).find('td').eq(1).text(),
            CODIGO      = $(this).find('td').eq(2).text(),
            DESCRIPCION = $(this).find('td').eq(3).text(),
            UNIDAD      = $(this).find('td').eq(4).text(),
            CANTIDAD    = $(this).find('td').eq(5).children().val(),
            NROPARTE    = $(this).find('td').eq(6).text();
            ITEMPEDIDO  = $(this).data('idx'),
            OBSERVAC    = ""

        item= {};
        
        item['item']        = ITEM;
        item['codigo']      = CODIGO;
        item['descripcion'] = DESCRIPCION;
        item['unidad']      = UNIDAD;
        item['cantidad']    = CANTIDAD;
        item['nroparte']    = NROPARTE;
        item['itempedido']  = ITEMPEDIDO;
        item['observac']    = OBSERVAC;
        item['atendida']    = 0;

        DATA.push(item);
    })

    return DATA;
}

itemsSave = () =>{
    DATA = [];
    let TABLA = $("#tablaDetalles tbody >tr");

    TABLA.each(function(){
        let IDPROD      = $(this).data('idprod'),
            UNIDAD      = $(this).data('codund'),
            CANTIDAD    = $(this).find('td').eq(5).children().val(),
            NROPARTE    = $(this).find('td').eq(6).text(),
            IDX         = $(this).data('idx'),
            CALIDAD     = $(this).find('td').eq(7).children().prop("checked"),
            ESTADO      = $(this).data('grabado');

        item= {};
        
        if (ESTADO == 0) {
            item['idprod']      = IDPROD;
            item['unidad']      = UNIDAD;
            item['cantidad']    = CANTIDAD;
            item['nroparte']    = NROPARTE;
            item['itempedido']         = IDX;
            item['calidad']     = CALIDAD;

            DATA.push(item);
        } 
    })

    return DATA;
}

mailsList = () => {
    CORREOS = [];

    let TABLA =  $("#listaCorreos tbody >tr");

    TABLA.each(function(){
        let CORREO      = $(this).find('td').eq(1).text(),
            NOMBRE      = $(this).find('td').eq(0).text(),
            ENVIAR      = $(this).find('td').eq(2).children().prop("checked"),

        item= {};
        
        if (ENVIAR) {
            item['nombre']= NOMBRE;
            item['correo']= CORREO;

            CORREOS.push(item);
        }
        
    })

    return CORREOS;
}

hadfledFiles = (files) =>{
    let filesvar = "";
    filesvar = files;
    
    return filesvar;
}