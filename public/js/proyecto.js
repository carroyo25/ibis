$(function(){
    var accion = "";
    var index = "";

    $("#esperar").fadeOut();

    
    $("#nuevoRegistro").click(function (e) { 
        e.preventDefault();

        $("#proceso").fadeIn();
        $("#tablaCostos tbody").empty();
        accion = 'n';

        return false;
    });

    $("#grabarItem").click(function (e) { 
        e.preventDefault();

        let result = {};

        $.each($("#formProceso").serializeArray(),function(){
            result[this.name] = this.value;
        })
        
        try {
            if (result['codigo'] == '') throw "Ingrese un codigo";
            if (result['descripcion'] == '') throw "Ingrese una descripción";
            
            if (accion == 'n') {
                $.post(RUTA+"proyecto/nuevoProyecto", {datos:result,costos:JSON.stringify(getItems())},
                    function (data, textStatus, jqXHR) {
                        mostrarMensaje(data.mensaje,data.clase);
                    },
                    "json"
                );
            }
            else {
                $.post(RUTA+"proyecto/modificaProyecto", {datos:result,costos:JSON.stringify(getItems())},
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

    $("#dpto").click(function (e) { 
        e.preventDefault();
        
        $(this).next().slideDown();

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

    $("#cerrarVentana").click(function (e) { 
        e.preventDefault();

        $.post(RUTA+"proyecto/actualizaTabla",
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

    $("#tablaPrincipal tbody").on("click","tr", function (e) {
        e.preventDefault();

        $.post(RUTA+"proyecto/consultaId", {id:$(this).data("id")},
            function (data, textStatus, jqXHR) {
                $("#ubigeo").val(data.proyecto[0].cubica);
                $("#codproy").val(data.proyecto[0].nidreg);
                $("#codigo").val(data.proyecto[0].ccodproy);
                $("#descripcion").val(data.proyecto[0].cdesproy);
                $("#abreviatura").val(data.proyecto[0].cabrevia);
                $("#dpto").val(data.proyecto[0].departamento);
                $("#prov").val(data.proyecto[0].provincia);
                $("#dist").val(data.proyecto[0].distrito);

                $("#tablaCostos tbody")
                    .empty()
                    .append(data.costos);
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

        $.post(RUTA+"proyecto/desactivaProyecto", {id:index},
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

    $("#addSubItem").click(function (e) { 
        e.preventDefault();

        let row = `<tr data-estado="0">
                        <td class="pl5px"><input type="text"></td>
                        <td class="pl5px"><input type="text"></td>
                        <td class="textoCentro"><input type="checkbox" checked="false"></td>
                        <td class="textoCentro"><a href="#"><i class="far fa-trash-alt"></i></a></td>
                    </tr>`;

        $("#tablaCostos tbody").append(row);
    });

    $("body #tablaCostos tbody").on("click","a", function (e) {
        e.preventDefault();

        let control = $(this);

        if (control.attr("href") === "#") {
            control.parent().parent().remove();
        }else{
            $(this).parent().parent().remove();
            $.post(RUTA+"proyecto/desactivaCostos", {id:$(this).attr("href")},
                function (data, textStatus, jqXHR) {
                    if (data) {
                        control.parent().parent().remove();
                    }
                },
                "text"
            );
        }

        return false;
    });
})

getItems = () =>{
    DATA = [];
    let TABLA = $("#tablaCostos tbody > tr");

    TABLA.each(function(){
        let CODIGO = $(this).find('td').eq(0).children().val(),
            DESCRIPCION = $(this).find('td').eq(1).children().val(),
            ALMACEN = $(this).find('td').eq(2).children().prop('checked'),
            ESTADO = $(this).data('estado');

        item= {};

        if ( ESTADO == 0 ) {
            item["codigo"]      = CODIGO;
            item["descripcion"] = DESCRIPCION;
            item["almacen"]     = ALMACEN;

            DATA.push(item);
        }  
    })

    return DATA;
}
