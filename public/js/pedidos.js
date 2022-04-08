$(function(){
    var accion = "";
    var index = "";

    $("#nuevoRegistro").click(function (e) { 
        e.preventDefault();

        $("#proceso").fadeIn();
        accion = 'n';

        return false;
    });

    $("#addItem").click(function (e) { 
        e.preventDefault();
        
        $("#busqueda").fadeIn();

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
        
        control.slideUp()

        destino.val($(this).text());
        id = destino.attr("id");

        if (contenedor_padre == "listaCostos"){
            $("#codigo_costos").val(codigo);
        }else if(contenedor_padre == "listaAreas"){
            $("#codigo_area").val(codigo);
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
})