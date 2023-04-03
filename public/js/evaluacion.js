$(function(){
    let evaluar = false;

    $("#esperar").fadeOut();

    $("#tablaPrincipal tbody").on("click","tr", function (e) {
        e.preventDefault();

       $.post(RUTA+"evaluacion/criterios", {id:$(this).data("indice"),tipo:$(this).data("tipo")},
        function (data, textStatus, jqXHR) {
            $("#codigo_orden").val(data.cabecera[0].id_regmov);
            $("#codigo_rol").val(data.cabecera[0].nrol);
            $("#codigo_entidad").val(data.cabecera[0].id_centi);
            $("#tipo_orden").val(data.cabecera[0].idregmov);
            $("#numero").val(data.cabecera[0].cnumero);
            $("#emision").val(data.cabecera[0].ffechadoc);
            $("#costos").val(data.cabecera[0].proyecto);
            $("#detalle").val(data.cabecera[0].concepto);
            $("#entidad").val(data.cabecera[0].entidad);

            evaluar = data.evaluada;

            $("#tablaDetalles tbody")
                .empty()
                .append(data.criterios);
            
            let totalOrden = sumarTotales($("#tablaDetalles tbody tr"));
            $("#puntaje").val(totalOrden.toFixed(0));    

            $("#proceso").fadeIn();
        },
        "json"
       );
    
        return false;
    });

    $("#saveOrden").click(function (e) { 
        e.preventDefault();
        
        try {
            if (evaluar) throw "Ya registro la evaluación";
            if (checkCantTablesMinMax($("#tablaDetalles tbody > tr"),2)) throw "El puntaje debe estar entre 1 y 5";

            $.post(RUTA+"evaluacion/evaluar",{items:JSON.stringify(items())},
                function (data, textStatus, jqXHR) {
                    mostrarMensaje(data.mensaje,data.clase);
                    $("#cerrarVentana").trigger("click");
                },
                "json"
            );

        } catch (error) {
            mostrarMensaje(error,'mensaje_error');
        }
        
        return false;
    });

    $("#cerrarVentana").click(function (e) { 
        e.preventDefault();

        /*$.post(RUTA+"evaluacion/actualizaTabla",
            function (data, textStatus, jqXHR) {
                $("#tablaPrincipal tbody")
                    .empty()
                    .append(data);
                $("#tablaDetalles tbody").empty();
            },
            "text"
        );*/
        
        $("#proceso").fadeOut();
        
        return false;
    });
})

items = () =>{
    DATA = [];
    let TABLA = $("#tablaDetalles tbody >tr");

    TABLA.each(function(){
        let REG         = $(this).data("reg"),
            TIPO        = $(this).data("tipo"),
            PUNTAJE     = $(this).find('td').eq(2).children().val(),
            PESO        = $(this).data("peso"),
            ENTIDAD     = $("#codigo_entidad").val(),
            ORDEN       = $("#codigo_orden").val(),
            USUARIO     = $("#id_user").val(),
            ROL         = $("#codigo_rol").val()

        item= {};
        
        item['reg']        = REG;
        item['tipo']       = TIPO;
        item['puntaje']    = PUNTAJE;
        item['peso']       = PESO;
        item['entidad']    = ENTIDAD;
        item['orden']      = ORDEN;
        item['usuario']    = USUARIO;
        item['rol']        = ROL;   
    
        DATA.push(item);
       
    })

    return DATA;
}