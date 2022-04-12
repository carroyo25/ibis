$(function(){
    var accion = "";
    var index = "";

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
                    $("#numero,#nropedidoatach").val(data);
                },
                "text"
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

        /*$.post(RUTA+"usuarios/actualizaListado",
            function (data, textStatus, jqXHR) {
                $(".itemsTabla  table tbody")
                    .empty()
                    .append(data);
                
                $("#proceso").fadeOut();
            },
            "text"
        );
        return false;*/

        $("#proceso").fadeOut();
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

    $("#saveItem").click(function (e) { 
        e.preventDefault();

        let result = {};

        $.each($("#formProceso").serializeArray(),function(){
            result[this.name] = this.value;
        })

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

        let nFilas = $.strPad($("#tablaDetalles tr").length,2);
        let idprod = $(this).data("idprod");
        let nunid = $(this).data("ncomed");
        let codigo = $(this).children('td:eq(0)').text();
        let descrip = $(this).children('td:eq(1)').text();
        let unidad = $(this).children('td:eq(2)').text();

        if (!checkExistTable($("#tablaDetalles tbody tr"),codigo,2)){
            let row = `<tr data-grabado="0" data-idprod="${idprod}" data-codund="${nunid}">
                        <td class="textoCentro"><a href="#"><i class="fas fa-eraser"></i></a></td>
                        <td class="textoCentro">${nFilas}</td>
                        <td class="textoCentro">${codigo}</td>
                        <td>${descrip}</td>
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
                    console.log(data);
                },
                "text"
            );
            //$("#vistaprevia").fadeIn();
        }

        $("#vistaprevia").fadeIn();
        
        return false;
    });

    $("#closePreview").click(function (e) { 
        e.preventDefault();

        $(".ventanaVistaPrevia object").attr("data","");
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
       // $("#fileAtachs").trigger("submit");

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

        item= {};
        
        item['item']        = ITEM;
        item['codigo']      = CODIGO;
        item['descripcion'] = DESCRIPCION;
        item['unidad']      = UNIDAD;
        item['cantidad']    = CANTIDAD;
        item['nroparte']    = NROPARTE;

        DATA.push(item);
    })

    return DATA;
}