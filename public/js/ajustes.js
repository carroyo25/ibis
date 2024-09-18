$(() => {

    let accion = 0;
    let fila = "";

    $("#esperar").fadeOut();

    $("#tablaPrincipal tbody").on("click","tr", function (e) {
        e.preventDefault();

        $.post(RUTA+"ajustes/consulta", {id:$(this).data('doc')},
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

    $("#nuevoRegistro").click(function (e) { 
        e.preventDefault();
        $("form")[1].reset();
        $("#tablaDetalles tbody").empty();

        $("#proceso").fadeIn();
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

            $("#archivo").val(input.files[0].name);
            const formData = new FormData();
            formData.append('fileUpload', input.files[0]);

            $("#esperar").fadeIn();

            fetch (RUTA+'ajustes/importarItemsAjustes',{
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .catch(error => console.error('Error:', error))
            .then(data => {
                $("#tablaDetalles tbody")
                    .empty()
                    .append(data.datos);

                $("#esperar").fadeOut();
                
                mostrarMensaje(data.mensaje,'mensaje_correcto');
            });  
        } catch (error) {
            mostrarMensaje(error,'mensaje_error');
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
            if (result['registra'] == '') throw "Elija la persona que autoriza";
            if (result['codigo_tipo'] == '') throw "Elija el concepto de ajuste";
            if ($("#tablaDetalles tbody tr").length <= 0) throw "El registro no tienes items";
            if (checkCantTables($("#tablaDetalles tbody > tr"),5)) throw "No ingreso cantidad en un item";

            $("#proceso").fadeIn();

            if (accion == 0) {
                $.post(RUTA+"ajustes/grabaRegistroAjustes", {cabecera:result,detalles:JSON.stringify(itemsSave())},
                    function (data, textStatus, jqXHR) {
                        mostrarMensaje(data.mensaje,'mensaje_correcto');
                        $("#proceso").fadeOut();
                    },
                    "json"
                );
            }else {
                $.post(RUTA+"ajustes/actualizaDetallesAjustes", {detalles:JSON.stringify(itemsSave())},
                    function (data, textStatus, jqXHR) {
                        mostrarMensaje('Items actualizados','mensaje_correcto');

                        $("#proceso").fadeOut();
                    },
                    "json"
                );
            }
        } catch (error) {
            mostrarMensaje(error,'mensaje_error');
        }
        return false;
    });

    $("#btnConsulta").click(function(e){
        e.preventDefault();

        let str = $("#formConsulta").serialize();

        $.post(RUTA+"ajustes/filtroAjustes", str,
            function (data, text, requestXHR) {
                $("#tablaPrincipal tbody")
                    .empty()
                    .append(data);
            },
            "text"
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