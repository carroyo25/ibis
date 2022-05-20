$(function(){
    var accion = "";
    var index = "";

    $("#esperar").fadeOut();

    $("#nuevoRegistro").click(function (e) { 
        e.preventDefault();

        $("#proceso").fadeIn();
        accion = 'n';

        return false;
    });

    $("#dpto").click(function (e) { 
        e.preventDefault();
        
        $(this).next().slideDown();

        return false;
    });

    $(".lista").on("click",'a', function (e) {
        e.preventDefault();

        let control = $(this).parent().parent().parent();
        let destino = $(this).parent().parent().parent().prev();
        let id = "";
        
        control.slideUp()
        destino.val($(this).text());
        id = destino.attr("id");

        $("#ubigeo").val($(this).attr("href"));

        return false;
    });

    $("#prov").click(function (e) { 
        e.preventDefault();

        if ( $("#ubigeo").val().length == 2) {
            $.post(RUTA+"almacen/ubigeo", {nivel:2,prefijo:$("#ubigeo").val()},
                function (data, textStatus, jqXHR) {
                    $("#listaProvincia")
                        .children("ul")
                        .empty()
                        .append(data)
                        .end()
                        .slideDown();
                },
                "text"
            );
        }

        return false;
    });

    $("#dist").click(function (e) { 
        e.preventDefault();

        if ( $("#ubigeo").val().length == 4) {
            $.post(RUTA+"almacen/ubigeo", {nivel:3,prefijo:$("#ubigeo").val()},
                function (data, textStatus, jqXHR) {
                    $("#listaDistrito")
                        .children("ul")
                        .empty()
                        .append(data)
                        .end()
                        .slideDown();
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
            if (result['descripcion'] == '') throw "Ingrese una descripción";
            
            if (accion == 'n')
                $.post(RUTA+"almacen/nuevoAlmacen", {datos:result},
                    function (data, textStatus, jqXHR) {
                        mostrarMensaje(data.mensaje,data.clase);
                    },
                    "json"
                );
            else {
                $.post(RUTA+"almacen/modificaAlmacen", {datos:result},
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

        $.post(RUTA+"almacen/actualizaTabla",
            function (data, textStatus, jqXHR) {
                $("#tablaPrincipal tbody")
                    .empty()
                    .append(data);
                $("#proceso").fadeOut();
                $("form")[0].reset();
            },
            "text"
        );

        return false;
    });

    $("#tablaPrincipal tbody").on("click","a", function (e) {
        e.preventDefault();

        index = $(this).attr("href");

        $("#pregunta").fadeIn();
        
        return false;
    });

    $("#tablaPrincipal tbody").on("click","tr", function (e) {
        e.preventDefault();

        $.post(RUTA+"almacen/consultaId", {id:$(this).data("id")},
            function (data, textStatus, jqXHR) {
                $("#ubigeo").val(data.almacen[0].ncubigeo);
                $("#codigo").val(data.almacen[0].ncodalm);
                $("#descripcion").val(data.almacen[0].cdesalm);
                $("#vtipo").val(data.almacen[0].ctipovia);
                $("#vnombre").val(data.almacen[0].cdesvia);
                $("#numero").val(data.almacen[0].cnrovia);
                $("#zona").val(data.almacen[0].czonavia);
                $("#dpto").val(data.almacen[0].departamento);
                $("#prov").val(data.almacen[0].provincia);
                $("#dist").val(data.almacen[0].distrito);
            },
            "json"
        );
        accion = "u";
        $("#proceso").fadeIn();

        return false;
    });

    $("#btnCancelarPregunta").click(function (e) { 
        e.preventDefault();
        
        $("#pregunta").fadeOut();

        return false;
    });

    $("#btnAceptarPregunta").click(function (e) { 
        e.preventDefault();

        $.post(RUTA+"almacen/desactivaAlmacen", {id:index},
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
    
})