$(function () {
    var accion = "";
    var index = 0
    var codigoTexto = "";

    //$("#esperar").fadeOut();

    $(".ventanaProceso ").click(function (e) { 
        e.preventDefault();
        
        $(".lista").slideUp();

        return false;
    });

    $("#nuevoRegistro").click(function (e) { 
        e.preventDefault();

        $("#proceso").fadeIn();
        accion = 'n';

        return false;
    });

    $("#grupo").click(function (e) { 
        e.preventDefault();
        
        $("#lista").hide();
        $("#codclase, #codclase").val('');

        $(this).next().slideDown();

        return false;
    });

    $("#clase").click(function (e) { 
        e.preventDefault();

        try {
            if ($("#codgrupo").val() == "") throw "Seleccione un grupo";
                $.post(RUTA+"familias/listaClases", {id:$("#codgrupo").val()},
                    function (data, textStatus, jqXHR) {
                        $("#listaClase ul")
                            .empty()
                            .append(data)
                            .end();
                    },
                    "text"
                );
            
            $(this).next().slideDown();
        } catch (error) {
            mostrarMensaje(error,'mensaje_error');
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
        codigoTexto = $(this).data("catalogo");
        
        control.slideUp()
        destino.val($(this).text());
        id = destino.attr("id");

        if (contenedor_padre == "listaGrupo"){
            $("#codgrupo").val(codigo);

        }else if(contenedor_padre == "listaClase"){
            $("#codclase").val(codigo)

            $.post(RUTA+"familias/codigo", {grupo:$("#codgrupo").val(),clase:$("#codclase").val()},
                function (data, textStatus, jqXHR) {
                    $("#codigo").val(codigoTexto + data);
                    $("#codigo_clase_catalogo").val(codigoTexto);
                },
                "text"
            );
        }

        return false;
    });

    $("#grabarItem").click(function (e) { 
        e.preventDefault();

        var result = {};

        $.each($("#formProceso").serializeArray(),function(){
            result[this.name] = this.value;
        })

        try {
            if (result['codigo'] == '') throw "Ingrese un codigo";
            if (result['descripcion'] == '') throw "Ingrese una descripción";

            if (accion == 'n'){
                $.post(RUTA+"familias/nuevaFamilia", {datos:result},
                    function (data, textStatus, jqXHR) {
                        mostrarMensaje(data.mensaje,data.clase);
                        $("#descripcion").val('');
                        
                        $("#tablaPrincipal tbody")
                            .empty()
                            .append(data.items);
                        
                        $("#codigo").val(codigoTexto + data.numero);
                    },
                    "json"
                );
            }else{
                $.post(RUTA+"familias/modificaFamilia", {datos: result},
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

    $("#tablaPrincipal tbody").on("click",".pointer", function (e) {
        e.preventDefault();

        $.post(RUTA+"familias/familiaId", {id:$(this).data("id")},
            function (data, textStatus, jqXHR) {
                $("#codgrupo").val(data.familia[0].ncodgrupo);
                $("#codclase").val(data.familia[0].ncodclase);
                $("#codfamilia").val(data.familia[0].ncodfamilia);
                $("#grupo").val(data.familia[0].nombre_grupo);
                $("#clase").val(data.familia[0].nombre_clase);
                $("#codigo").val(data.familia[0].ccodcata);
                $("#descripcion").val(data.familia[0].cdescrip);

                $("#proceso").fadeIn();
                accion = 'u';

            },
            "json"
        );

        return false;
    });

    $("#cerrarVentana").click(function (e) { 
        e.preventDefault();

        $.post(RUTA+"familias/actualizaTabla",
            function (data, textStatus, jqXHR) {
                $(".lista").slideUp();
                $("form")[0].reset();
                $("#tablaPrincipal tbody")
                    .empty()
                    .append(data);
                $("#proceso").fadeOut();
            },
            "text"
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

        $.post(RUTA+"familias/desactivaFamilia", {id:index},
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

    $("#consulta").keyup(function(){
        _this = this;
        buscar(_this); // arrow function para activa el buscador
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
})