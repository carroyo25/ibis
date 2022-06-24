$(function(){
    var accion = "";
    var index = 0;

    $("#esperar").fadeOut();

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

        $("#codgrupo").val($(this).attr("href"));
        $("#codigo").val($(this).data("grupo"));

        return false;
    });

    $("#grabarItem").click(function (e) { 
        e.preventDefault();

        var result = {};

        $.each($("#formProceso").serializeArray(),function(){
            result[this.name] = this.value;
        })
        
        try {
            if (result['codGrupo'] == '') throw "Escoja una clase";
            if (result['codigo'] == '') throw "Ingrese el codigo de la clase";
            if (result['descripcion'] == '') throw "Ingrese el nombre de la clase";
            
            if (accion == 'n')
                $.post(RUTA+"clases/nuevaClase", {datos:result},
                    function (data, textStatus, jqXHR) {
                        $("#tablaPrincipal tbody")
                            .empty()
                            .append(data.items)
                        mostrarMensaje(data.mensaje,data.clase);
                        $("form")[0].reset();
                    },
                    "json"
                );
            else {
                $.post(RUTA+"clases/modificaClase", {datos:result},
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

        $.post(RUTA+"clases/actualizaTabla",
            function (data, textStatus, jqXHR) {
                $("form")[0].reset();
                $(".lista").hide();
                $("#tablaPrincipal tbody")
                    .empty()
                    .append(data);
                $("#proceso").fadeOut();  
            },
            "text"
        );

        return false;
    });

    $("#tablaPrincipal tbody").on("click",".pointer", function (e) {
        e.preventDefault();

        $.post(RUTA+"clases/claseId", {id:$(this).data("id")},
            function (data, textStatus, jqXHR) {
                $("#codclase").val(data.clase[0].ncodclase);
                $("#codgrupo").val(data.clase[0].ncodgrupo);
                $("#grupo").val(data.clase[0].nombre_grupo);
                $("#codigo").val(data.clase[0].ccodcata);
                $("#descripcion").val(data.clase[0].cdescrip);

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

        $.post(RUTA+"clases/desactivaClase", {id:index},
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