$(() => {

    let swgrabar = false;

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

                $("#items").val($("#tablaDetalles tbody tr").length);

                $("#atach_counter").text(data.total_adjuntos);

                accion = "u";
                grabado = true;
                $("#proceso").fadeIn();

            },
            "json"
        );

        return false;
    });

    $("#closeProcess").click(function (e) { 
        e.preventDefault();

        $("#proceso").fadeOut();

        return false;
    });

    $("#tablaDetalles tbody").on("click","a", function (e) {
        e.preventDefault();

        let fila = $(this).parent().parent(),
            idfila = $(this).parent().parent().data('iddetped');
            nfilas = 0;

        if ( $(this).data("accion") == "series" ) {
            let filas       = parseInt($(this).parent().parent().find("td").eq(7).children().val()),
                orden       = $(this).parent().parent().data('detorden'),
                producto    = $(this).parent().parent().data('idprod'),
                almacen     = $("#codigo_almacen").val(),
                nombre      = $(this).parent().parent().find("td").eq(4).text(),
                item        = $(this).parent().parent().data('iddetped');

            row = `<tr data-orden="${orden}" data-producto="${producto}" data-almacen="${almacen}" data-itempedido="${item}" data-grabado="0">
                        <td>${nombre}</td>
                        <td><input type="text"></td>
                    </tr>`

            $.post(RUTA+"ingresoedit/seriesConsulta", {id:item},
                function (data, text, requestXHR) {
                    $("#tablaSeries tbody").empty().append(data);

                    if ( $("#tablaSeries tbody >tr").length === 0 ) {
                        $("#tablaSeries tbody").empty();
                        swgrabar = true;

                        for (let index = 0; index < filas; index++) {
                            $("#tablaSeries").append(row);        
                        }
                    } else {
                        swgrabar = false;
                    };
                },
                "text"
            );
            
            $("#series").fadeIn();
        }
        
        return false;
    });

    $("#atachDocs").click(function(e){
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

        fp = $(this);
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

        let formData = new FormData();

        formData.append('codigo',$("#codigo_ingreso").val());

        $.each($('#uploadAtach')[0].files, function(i, file) {
            formData.append('file-'+i, file);
        });

        $.ajax({
            type: "POST",
            url: RUTA+"ingresoedit/archivos",
            data: formData,
            data: formData,
            contentType:false,      
            processData:false,
            dataType: "json",
            success: function (response) {
                $("#atach_counter").text(response.adjuntos);
                $("#archivos").fadeOut();
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
            if ($("#codigo_ingreso").val() == "") throw "Seleccione un orden, para ver los adjuntos";

            $.post(RUTA+"ingresoedit/verAdjuntos", {id:$("#codigo_ingreso").val(),tipo:'NI'},
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
            .attr("src","public/documentos/notas_ingreso/adjuntos/"+$(this).attr("href"));
        
        return false;
    });

    $("#closeAtach").click(function(e){
        e.preventDefault();

        $("#vistaAdjuntos").fadeOut();
        $(".ventanaAdjuntos iframe").attr("src","");

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

        $.post("ingresoedit/series",{id:$("#codigo_ingreso").val(),series:JSON.stringify(series())},
                function (data, text, requestXHR) {
                    
                },
                "json"
            ); 
        
        $("#series").fadeOut();
        
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
})

series = () => {
    SERIES = [];

    let TABLA = $("#tablaSeries tbody >tr");

    TABLA.each(function(){

        let ORDEN    = $(this).data('orden'),
            ALMACEN  = $("#codigo_almacen").val(),
            PRODUCTO = $(this).data('producto'),
            IDPED    = $(this).data('itempedido'),
            SERIE    = $(this).find('td').eq(1).children().val(),
            GRABADO  = $(this).data('grabado');

            console.log(GRABADO);
    
        item = {};

        if ( SERIE != "" && GRABADO === 0){
            item['orden']       = ORDEN;
            item['almacen']     = ALMACEN;
            item['producto']    = PRODUCTO;
            item['serie']       = SERIE;
            item['idped']       = IDPED;

            $(this).attr('data-grabado',1);

            SERIES.push(item);
        }
    })

    return SERIES;
}