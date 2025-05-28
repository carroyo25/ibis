$(function(){

    $("#esperar").fadeIn()

    let accion = "";
    let tipoMovimiento = 0;  //guia remision  = 1, transferencias = 2

    let str = $("#formConsulta").serialize();

    $.post(RUTA+"registros/actualizarRegistros",
        function (data, text, requestXHR) {
            $("#tablaPrincipal tbody")
                .empty()
                .append(data);

                $("#esperar").fadeOut().promise().done(function(){
                    iniciarPaginador();
                });

        "text"
    });


    $("#nuevoRegistro").click(function (e) { 
        e.preventDefault();

        $("#estado")
            .removeClass()
            .addClass("textoCentro w35por estado procesando");

        $("#proceso").fadeIn();
        $("form")[1].reset();
        $("#tablaDetalles tbody").empty();

        $(".primeraBarra").css("background","#0078D4");
        $(".primeraBarra span").text("Datos Generales");

        $("#formProceso input[type='hidden']").each(function(){
            $(this).val("");
        });

        $("#codigo_recepcion").val($("#id_user").val());

        accion = 'n';

        return false;
    });

    $("#tablaPrincipal tbody").on("click","tr", function (e) {
        e.preventDefault();

        $.post(RUTA+"registros/registroID",{id:$(this).data("indice")},
            function (data, textStatus, jqXHR) {

                $("#fecha").val(data.cabecera[0].ffechadoc);
                $("#costos").val(data.cabecera[0].costos);
                $("#codigo_costos").val(data.idcostos);
                $("#codigo_almacen_origen").val(data.cabecera[0].ncodalm1);
                $("#codigo_almacen_destino").val(data.cabecera[0].ncodalm2);
                $("#codigo_autoriza").val(data.cabecera[0].idautoriza);
                $("#codigo_recepcion").val(data.cabecera[0].idrecepciona);
                $("#codigo_ingreso").val(data.cabecera[0].idreg);
                $("#guia").val(data.cabecera[0].numguia);
                $("#referido").val(data.cabecera[0].nreferido);
                $("#almacen_origen_ingreso").val(data.cabecera[0].origen);
                $("#almacen_destino_ingreso").val(data.cabecera[0].destino);
                $("#autoriza").val(data.cabecera[0].cnombres);
                $("#numero").val(data.cabecera[0].numero);
                
                
                $("#tablaDetalles tbody")
                    .empty()
                    .append(data.detalles);

                $("#atach_counter").text(data.total_adjuntos);

                $(".primeraBarra").css("background","#0078D4");
                $(".primeraBarra span").text("Datos Generales");
            },
            "json"
        );

        accion = "u";
        grabado = true;
        $("#proceso").fadeIn();

        return false;
    });

    $("#closeProcess").click(function (e) { 
        e.preventDefault();

        $("#proceso").fadeOut();

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
        
        control.slideUp()

        destino.val($(this).text());
        id = destino.attr("id");

        if(contenedor_padre == "listaRecepciona"){
            $("#codigo_autoriza").val(codigo);
        }else if (contenedor_padre == "listaCostos"){
            $("#codigo_costos").val(codigo);
        }

        return false;
    });

    $("#updateDocument").click(function(e){
        e.preventDefault();

        $("#pregunta").fadeIn();
    
        return false;
    });

    $("#itemsImport").click(function (e) { 
        e.preventDefault();

        tipoMovimiento = 1;

        try {
            $("#txtBuscar").val('');

            $.post(RUTA+"registros/despachos",{guia:$("#txtBuscar").val()},
                function (data, textStatus, jqXHR) {
                    $("#despachos tbody")
                        .empty()
                        .append(data);
                    
                        $("#busqueda").fadeIn();
                },
                "text"
            );

        } catch (error) {
            console.log(error);
        }

        return false;
    });

    $("#closeSearch").click(function (e) { 
        e.preventDefault();
        
        $("#busqueda").fadeOut();

        return false;
    });

    $("#despachos tbody").on("click","tr", function (e) {
        e.preventDefault();

        try {

            let tipo_guia = $(this).data("salida") != null ? "S" : "M";

            $("#esperar").css({"display":"block","opacity":"1"});

            $.post(RUTA+"registros/consultaID", { indice:$(this).data("indice"), tipo:tipo_guia },
                function (data, textStatus, jqXHR) {

                    $("#numero").val(data.numero);
                    $("#costos").val(data.cabecera[0].costos);
                    $("#almacen_destino_ingreso").val(data.cabecera[0].destino);
                    $("#almacen_origen_ingreso").val(data.cabecera[0].origen);
                    $("#guia").val(data.cabecera[0].cnumguia);
                    $("#referido").val(data.cabecera[0].nReferido);
                    $("#codigo_almacen_origen").val(data.cabecera[0].ncodalm1);
                    $("#codigo_almacen_destino").val(data.cabecera[0].ncodalm2);
                    $("#codigo_costos").val(data.cabecera[0].ncodpry);
                    $("#codigo_despacho").val(data.cabecera[0].id_regalm);
                    
                    //if (data.detalles !=""){
                        $("#tablaDetalles tbody")
                        .empty()
                        .append(data.detalles);
                    //else{
                        //mostrarMensaje("Los items ya se ingresaron en su totalidad","mensaje_error");
                    //}
                    
                    $("#esperar").css({"display":"none","opacity":"0"});

                    $("#busqueda").fadeOut();
                },
                "json"
            );

        } catch (error) {
            mostrarMensaje(error.message,'mensaje_error');
        }

        return false
    });

    $("#txtBuscar").keyup(function(){
        let keycode = (event.keyCode ? event.keyCode : event.which);
        if(keycode == '13'){
            try {
                buscarGuia($("#txtBuscar").val());                
            } catch (error) {
                mostrarMensaje(error.message,',mensaje error');
            }
        }
    });

    $("#txtBuscarTrans").keyup(function(){
        let _this = this;

        $.each($("#transferencias tbody > tr"), function() {
            if($(this).text().toLowerCase().indexOf($(_this).val().toLowerCase()) === -1)
                $(this).hide();
            else
                $(this).show();
        });

    });

    $("#btnConsulta").click(function(e){
        e.preventDefault();

        $("#esperar").css({"display":"block","opacity":"1"});

        let str = $("#formConsulta").serialize();

        $.post(RUTA+"registros/filtro", str,
            function (data, text, requestXHR) {
                $("#tablaPrincipal tbody")
                    .empty()
                    .append(data);

                $("#esperar").fadeOut().promise().done(function(){
                    iniciarPaginador();
                });
            },
            "text"
        );

        return false;
    });

    $("#itemsTransfer").click(function (e) { 
        e.preventDefault();

        tipoMovimiento = 2;

        $.post(RUTA+"registros/transferencias",{nt:$("#txtBuscarTrans").val()},
            function (data, textStatus, jqXHR) {

                $("#transferencias tbody")
                        .empty()
                        .append(data);

                $("#transferencias").fadeIn();
            },
            "text"
        );

        return false;  
    });

    $("#closeSearchTrans").click(function (e) { 
        e.preventDefault();
        
        $("#transferencias").fadeOut();

        return false;
    });

    $("#transferencias tbody").on("click","tr", function (e) {
        e.preventDefault();

        $.post(RUTA+"registros/transferenciasId",{id:$(this).data("indice")},
            function (data, textStatus, jqXHR) {
                $("#numero").val(data.numero);
                $("#almacen_destino_ingreso").val(data.cabecera[0].descripcion_destino);
                $("#almacen_origen_ingreso").val(data.cabecera[0].descripcion_origen);
                $("#codigo_almacen_origen").val(data.cabecera[0].codigo_almacen_origen);
                $("#codigo_almacen_destino").val(data.cabecera[0].codigo_almacen_destino);
                $("#guia").val(data.cabecera[0].cnumguia);

                $("#tablaDetalles tbody")
                    .empty()
                    .append(data.detalles);

                $("#transferencias").fadeOut();
            },
            "json"
        );
        return false;
    });

    $("#atachDocs").click(function(e){
        e.preventDefault();

        try {
            if ($("#codigo_ingreso").val() === "") throw new Error("Debe grabar el documento");

            $("#archivos").fadeIn();
        } catch (error) {
            mostrarMensaje(error,"mensaje_error");
        }
        

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
                 fragment +=`<li>
                                 <a class="icono_archivo"><i class="far fa-file"></i>
                                     <p>${fileName}</p>
                                 </a>
                             </li>`;
             }
 
             $(".listaArchivos").append(fragment);
         }
 
        return false;
    });
 
    $("#btnConfirmAtach").on("click", function (e) {
         e.preventDefault();
 
         let formData = new FormData();
 
         formData.append('codigo',$("#codigo_ingreso").val());
 
         $.each($('#uploadAtach')[0].files, function(i, file) {
             formData.append('file-'+i, file);
         });
 
         $.ajax({
             type: "POST",
             url: RUTA+"registros/adjuntos",
             data: formData,
             data: formData,
             contentType:false,      
             processData:false,
             dataType: "json",
             success: function (response) {
                 $("#archivos").fadeOut();
                 $("#fileAtachs")[0].reset();

                 $("#atach_counter").text(response.total_adjuntos);

                 $("#fileAtachs")[0].reset();
                 $(".listaArchivos").empty();
             }
         });
 
         return false;
    });
 
    $("#btnCancelAtach").on("click", function (e) {
         e.preventDefault();
 
         $("#archivos").fadeOut();
         $("#fileAtachs")[0].reset();
         $(".listaArchivos").empty();
    });

    $("#previewDocs").click(function (e) { 
        e.preventDefault();

        try {
            if ($("#codigo_ingreso").val() == "") throw "Seleccione un ingreso, para ver los adjuntos";
            if ($("#atach_counter").text() == 0)  throw "No se han registrado adjuntos";

            $.post(RUTA+"ingresoedit/verAdjuntos", {id:$("#codigo_ingreso").val(),tipo:'GA'},
                function (data, textStatus, jqXHR) {
                    $("#listaAdjuntos").empty().append(data.adjuntos);
                    $("#listaAdjuntos li a:nth-child(2)").hide();

                    $("#vistaAdjuntos").fadeIn();
                },
                "json"
            );
        } catch (error) {
            mostrarMensaje(error,'mensaje_error')
        }
       
        return false;
    });

    $("#vistaAdjuntos").on("click","a", function (e) {
        e.preventDefault();
        
        $(".ventanaAdjuntos iframe")
            .attr("src","")
            .attr("src","public/documentos/almacen/adjuntos/"+$(this).attr("href"));
        
        return false;
    });

    $("#closeAtach").click(function (e) { 
        e.preventDefault();

        $(".ventanaAdjuntos iframe").attr("src","");
        $("#vistaAdjuntos").fadeOut();

        return false;
    });

    $(".boton_pagina").click(function(e){
        e.preventDefault();

        let valorInicial = $(this).text();
    
        $("#inicio_consulta").val(valorInicial+1);

        return false;
    });

    $("#cancelDocument").click(function(e){
        e.preventDefault();

        try {
            if ($("#rol_user").val() != 2) throw new Error("No esta autorizado para esta accion");

            let formData = new FormData();
            formData.append("id",$("#codigo_ingreso").val());

            fetch(RUTA+"registros/anula",{
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                mostrarMensaje(data.respuesta,"mensaje_correcto");
            })
            
            
        } catch (error) {
            mostrarMensaje(error.message,"mensaje_error");
        }

        return false;
    });

    $("#btnAceptarPregunta").click(function(e){
        e.preventDefault();

        $("#esperar").css({"display":"block"});

        let result = {};

        $.each($("#formProceso").serializeArray(),function(){
            result[this.name] = this.value;
        });

        try {
            if (result['codigo_autoriza'] == '') throw "Elija el responsable de la recepcion";
            if (result['cnumguia'] == '') throw "Seleccione un numero de guia";
            if (result['codigo_costos'] == "") throw "Seleccione el centro de costos";
            if ( accion != "n" ) throw "No se puede grabar";
        
            $.post(RUTA+"registros/nuevoRegistro", {cabecera:result,detalles:JSON.stringify(detalles()),tipo:tipoMovimiento},
                function (data, textStatus, jqXHR) {
                    if (data.estado){
                        mostrarMensaje("Ingreso correcto","mensaje_correcto");
                        
                        $(".primeraBarra").css("background","#819830");
                        $(".primeraBarra span").text('Datos Generales ... Grabado');

                        $("#esperar,#pregunta,#proceso").css({"display":"none"});

                    }else {
                        mostrarMensaje("Hubo un problema con el registro","mensaje_error");
                    }
                },
                "json"
            );
        } catch (error) {
            mostrarMensaje(error,'mensaje_error');
        }

        return false;
    });

    $("#btnCancelarPregunta").click(function(e){
        e.preventDefault();

        $("#pregunta").fadeOut();

        return false;
    });
    

})

detalles = () =>{
    DETALLES = [];

    let TABLA = $("#tablaDetalles tbody tr");

    TABLA.each(function(){
        let IDDEPET     = $(this).data("idpet"),
            CODPROD     = $(this).data("codprod"),
            AREA        = $(this).data("area"),
            ALMACEN     = $(this).data("almacen"),
            COSTOS      = $(this).data("costos"),
            CANTENV     = $(this).find('td').eq(4).text(),
            CANTRECEP   = $(this).find('td').eq(5).children().val(),
            OBSERVAC    = $(this).find('td').eq(6).children().val(),
            VENCE       = $(this).find('td').eq(8).children().val(),
            CONDIC      = $(this).find('td').eq(9).children().val(),
            UBICA       = $(this).find('td').eq(10).children().val(),
            PEDIDO      = $(this).find('td').eq(11).text(),
            ORDEN       = $(this).find('td').eq(12).text(),
            SERIE       = $(this).find('td').eq(14).children().val(),
            FECCAL      = $(this).find('td').eq(15).children().val(),
            CERCAL      = $(this).find('td').eq(16).children().val();

        item = {};

        if ( CANTRECEP > 0 ) {

            item['iddepet']     = IDDEPET;
            item['codprod']     = CODPROD;
            item['area']        = AREA;
            item['cantrecep']   = CANTRECEP;
            item['observac']    = OBSERVAC;
            item['vence']       = VENCE;
            item['condic']      = CONDIC;
            item['ubica']       = UBICA;
            item['pedido']      = PEDIDO;
            item['orden']       = ORDEN;
            item['almacen']     = ALMACEN;
            item['costos']      = COSTOS;
            item['cantenv']     = CANTENV;
            item['serie']       = SERIE;
            item['feccal']      = FECCAL;
            item['cercal']      = CERCAL;

            
            DETALLES.push(item);
        }
    })

    return DETALLES
}

buscarGuia = (guia) => {

    let formData = new FormData(),
        existe = true;
    formData.append("guia", guia);

    fetch(RUTA+'registros/buscaGuiaIngresada',{
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        llamarDatosGuia(guia);
    });
}

llamarDatosGuia = (guia) => {
    let formData = new FormData();
        formData.append("guia",guia); 

    fetch(RUTA+"registros/buscaGuia",{
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        
        let row = `<tr class="pointer" 
                        data-indice ="${data.cabecera[0].iddespacho}"  
                        data-guia   ="${data.cabecera[0].cnumguia}"
                        data-salida ="${data.cabecera[0].salida}"
                        data-madre  ="${data.cabecera[0].madre}">
                        <td class="textoCentro">${data.cabecera[0].iddespacho}</td>
                        <td class="textoCentro">${data.cabecera[0].fecha}</td>
                        <td class="pl20px">${data.cabecera[0].corigen}</td>
                        <td class="pl20px">${data.cabecera[0].cdestino}</td>
                        <td class="pl20px">${data.cabecera[0].proyectoGuias}</td>
                        <td class="textoCentro">${data.cabecera[0].anio}</td>
                        <td class="textoCentro">${data.cabecera[0].cnumguia}</td>
                        <td class="textoCentro">${data.cabecera[0].referido}</td>
                        <td></td>
                    </tr>`
        $("#despachos tbody")
            .empty()
            .append(row);
    })
}
