$(function(){
    var accion = "";
    var index = 0;

    $("#nuevoRegistro").click(function (e) { 
        e.preventDefault();

        $("#proceso").fadeIn();
        accion = 'n';

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
                $.post(RUTA+"grupos/nuevoGrupo", {datos: result},
                    function (data, textStatus, jqXHR) {
                        mostrarMensaje(data.mensaje,data.clase);
                    },
                    "json"
                );
            }else{
                $.post(RUTA+"grupos/modificaGrupo", {datos: result},
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

        $.post(RUTA+"grupos/actualizaTabla",
            function (data, textStatus, jqXHR) {
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

    $("#tablaPrincipal tbody").on("click","tr", function (e) {
        e.preventDefault();

        $.post(RUTA+"grupos/consultaId", {id:$(this).data("id")},
            function (data, textStatus, jqXHR) {
                $("#codclase").val(data.grupo[0].ncodclase);
                $("#codigo").val(data.grupo[0].ccodcata);
                $("#descripcion").val(data.grupo[0].cdescrip);
                $("input[name=tipoClase][value='"+data.grupo[0].ntipclase+"']").prop("checked",true);
            },
            "json"
        );
        accion = "u";
        $("#proceso").fadeIn();

        return false;
    });

    $("#tablaPrincipal tbody").on("click","a", function (e) {
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

        $.post(RUTA+"grupos/desactivaGrupo", {id:index},
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