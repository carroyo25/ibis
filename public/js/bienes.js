$(function() {
    var accion = "";
    var index = 0;

    $("#esperar").fadeOut();


    $("#nuevoRegistro").click(function (e) { 
        e.preventDefault();

        $("#proceso").fadeIn();
        accion = 'n';

        return false;
    });

    $(".cerrarLista").focus(function (e) { 
        e.preventDefault();
        
        $(".lista").fadeOut();

        return false;
    });

    $("#tipo_item").click(function (e) { 
        e.preventDefault();

        $("input").val("");
        
        $(this).next().slideDown();

        return false;
    });

    $("#grupo").click(function (e) { 
        e.preventDefault();

        $("#codigo_clase,#codigo_familia,#clase,#familia").val('');
        
        if ($("#tipo_item").val() != "") {
            $.post(RUTA+"bienes/grupos", {id:$("#codigo_tipo").val()},
                function (data, textStatus, jqXHR) {
                    $("#listaGrupo ul")
                    .empty()
                    .append(data);

                    $("#listaGrupo").slideDown();
                },
                "text"
            );
        }

        return false;
    });

    $("#clase").click(function (e) { 
        e.preventDefault();

        $("#codigo_familia,#familia").val('');
        
        if ($("#grupo").val() != "") {
            $.post(RUTA+"bienes/clases", {id:$("#codigo_grupo").val()},
                function (data, textStatus, jqXHR) {
                    $("#listaClase ul")
                    .empty()
                    .append(data);

                    $("#listaClase").slideDown();
                },
                "text"
            );
        }
        
        return false;
    });

    $("#familia").click(function (e) { 
        e.preventDefault();
        
        if ($("#clase").val() != "") {
            $.post(RUTA+"bienes/familias", {grupo:$("#codigo_grupo").val(),clase:$("#codigo_clase").val()},
                function (data, textStatus, jqXHR) {
                    $("#listaFamilia ul")
                    .empty()
                    .append(data);

                    $("#listaFamilia").slideDown();
                },
                "text"
            );
        }
        
        return false;
    });

    $("#unidad").click(function (e) { 
        e.preventDefault();
        
        if ($("#familia").val() != "") {
            $.post(RUTA+"bienes/unidades",
                function (data, textStatus, jqXHR) {
                    $("#listaUnidad ul")
                    .empty()
                    .append(data);

                    $("#listaUnidad").slideDown();
                },
                "text"
            );
        }
        
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

        if (contenedor_padre == "listaTipo"){
            $("#codigo_tipo").val(codigo);
        }else if(contenedor_padre == "listaGrupo"){
            $("#codigo_grupo").val(codigo);
        }else if(contenedor_padre == "listaClase"){
            $("#codigo_clase").val(codigo);
        }else if(contenedor_padre == "listaFamilia"){
            $("#codigo_familia").val(codigo);
            $("#codigo_catalogo").val(catalogo);
        }else if(contenedor_padre == "listaUnidad"){
            $("#codigo_unidad").val(codigo);
        }

        return false;
    });

    $("#descripcion").blur(function (e) { 
        e.preventDefault();
        
        if ($(this).val() !== ""){
            $.post(RUTA+"bienes/codigo", {codigo:$("#codigo_catalogo").val()},
                function (data, textStatus, jqXHR) {
                    $("#codigo").val(data);
                },
                "text"
            );
        }

        return false;
    });

    $("#foto").on("click", function (e) {
        e.preventDefault();

        $("#image_product").trigger('click');

        return false;
    });

    $("#image_product").on("change", function (event) {
		if(-1!=$.inArray($("#image_product")[0].files[0].type, ["image/jpeg","image/jpg","image/png"])){	
            var populateImg = new FileReader();
            populateImg.onload = previewImg;
			populateImg.readAsDataURL($("#image_product")[0].files[0]);
			$("#tipofoto").val($("#image_product")[0].files[0].type);
        }else {
			mostrarMensaje("Formato no soportado","mensaje_error");
		}
    });

    $("#grabarItem").click(function (e) { 
        e.preventDefault();

        let result = {};

        $.each($("#formProceso").serializeArray(),function(){
            result[this.name] = this.value;
        })
        
        try {
            if (result['codigo_tipo'] == '') throw "Seleccione el tipo";
            if (result['codigo_grupo'] == '') throw "Seleccione el grupo";
            if (result['codigo_clase'] == '') throw "Seleccione la clase";
            if (result['codigo_familia'] == '') throw "Seleccione la familia";
            if (result['descripcion'] == '') throw "Ingrese una descripción";
            if (result['codigo_unidad'] == '') throw "Seleccione la unidad";
  
            if (accion == 'n') {
                $.post(RUTA+"bienes/nuevoItem", {datos:result},
                    function (data, textStatus, jqXHR) {
                        mostrarMensaje(data.mensaje,data.clase);
                        
                        $("#formProceso").trigger("submit");

                        $("#descripcion,#codigo,#codigo_unidad,#unidad,#nro_parte").val('');
                        $("input[type=checkbox]").prop("checked",false);
                        $("#foto").attr('src','public/img/noimagen.jpg');
                    },
                    "json"
                );
            }
            else {
                $.post(RUTA+"bienes/modificaItem", {datos:result},
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

    $("#cerrarVentana").click(function (e) { 
        e.preventDefault();

        $.post(RUTA+"bienes/actualizaTabla",
            function (data, textStatus, jqXHR) {
                $("#descripcion,#codigo,#codigo_unidad,#unidad,#nro_parte").val('');
                $("input[type=checkbox]").prop("checked",false);
                $("#foto").attr('src','public/img/noimagen.jpg');
                $("form")[0].reset();
                $("#tablaPrincipal tbody")
                    .empty()
                    .append(data);
                $("#proceso").fadeOut();
            },
            "text"
        );
        
        

        return false
    });

    $("#formProceso").submit(function (e) { 
        e.preventDefault();
        
        $.ajax({
            type: "POST",
            url: RUTA+"bienes/foto",
            data: new FormData(this),
            dataType: "text",
            contentType:false,      
            processData:false,
            success: function (response) {
                
            }
        });

        return false;
    });

    $("#tablaPrincipal tbody").on("click",".pointer", function (e) {
        e.preventDefault();

        $.post(RUTA+"bienes/itemsId", {id:$(this).data("id")},
            function (data, textStatus, jqXHR) {
                let foto = "";
                $("#codigo_item").val(data.item[0].id_cprod);
                $("#codigo_tipo").val(data.item[0].ntipo);
                $("#codigo_grupo").val(data.item[0].ngrupo);
                $("#codigo_clase").val(data.item[0].nclase);
                $("#codigo_familia").val(data.item[0].nfam);
                $("#codigo_unidad").val(data.item[0].nund);
                
                $("#tipofoto").val(data.item[0].rfoto);
                $("#codigo").val(data.item[0].ccodprod);
                $("#tipo_item").val(data.item[0].tipo);
                $("#grupo").val(data.item[0].grupo);
                $("#clase").val(data.item[0].clase);
                $("#familia").val(data.item[0].familia);
                $("#descripcion").val(data.item[0].cdesprod);
                $("#unidad").val(data.item[0].unidad);
                $("#nro_parte").val(data.item[0].cnparte);
                
                
                $("#serie").prop("checked",data.item[0].flgSerie);
                $("#detraccion").val("checked",data.item[0].flgDetrac);
                
                switch (data.item[0].rfoto) {
                    case 'image/jpeg':
                        foto = "public/documentos/fotos/catalogo/"+data.item[0].ccodprod+'.jpg';
                        break;
                    case 'image/png':
                        foto = "public/documentos/fotos/catalogo/"+data.item[0].ccodprod+'.png';
                        break;
                    case 'image/gif':
                        foto = "public/documentos/fotos/catalogo/"+data.item[0].ccodprod+'.gif';
                        break;
                }

                console.log(foto);
                
                $("#foto").attr("src",foto)
                            .attr('width', '300px')
                            .attr('height', '250px');

                $("#proceso").fadeIn();
                accion = 'u';

            },
            "json"
        );

        return false;
    });

    $("#tablaPrincipal tbody").on("click",".pointer a", function (e) {
        e.preventDefault();

        index = $(this).attr("href");

        $("#pregunta").fadeIn();
        
        return false;
    });

    $("#btnCancelarPregunta").click(function (e) { 
        e.preventDefault();
        
        $("#pregunta").fadeOut();

        return false;
    });

    $("#btnAceptarPregunta").click(function (e) { 
        e.preventDefault();

        $.post(RUTA+"bienes/desactivaItem", {id:index},
            function (data, textStatus, jqXHR) {
                $("#tablaPrincipal tbody")
                    .empty()
                    .append(data);

                $("#pregunta").fadeOut();
            },
            "text"
        );
        
        return false;
    });

    $("#consulta").on("keypress", function (e) {
        if(e.which == 13 && $(this).val().length > 1) {
            $("#waitmodal").fadeIn();
            $.post(RUTA+"bienes/buscaPalabra", {criterio:$(this).val()},
                function (data, textStatus, jqXHR) {
                    $("#tablaPrincipal tbody")
                        .empty()
                        .append(data);
                    //$("#waitmodal").fadeOut();  
                },
                "text"
            );
        }
    });
})