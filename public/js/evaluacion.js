$(function(){
    $("#esperar").fadeOut();

    $("#tablaPrincipal tbody").on("click","tr", function (e) {
        e.preventDefault();

       $.post(RUTA+"evaluacion/criterios", {id:$(this).data("indice"),tipo:$(this).data("tipo")},
        function (data, textStatus, jqXHR) {
            $("#codigo_orden").val(data.cabecera[0].idregmov);
            $("#codigo_entidad").val(data.cabecera[0].id_centi);
            $("#tipo_orden").val(data.cabecera[0].idregmov);
            $("#numero").val(data.cabecera[0].cnumero);
            $("#emision").val(data.cabecera[0].ffechadoc);
            $("#costos").val(data.cabecera[0].proyecto);
            $("#detalle").val(data.cabecera[0].concepto);
            $("#entidad").val(data.cabecera[0].entidad);

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
            if (checkCantTablesMinMax($("#tablaDetalles tbody > tr"),2)) throw "El puntaje debe estar entre 1 y 5";

            console.log(JSON.stringify(items()))
            /*$.post(RUTA+"evaluacion/evaluar", {items},
                function (data, textStatus, jqXHR) {
                    
                },
                "json"
            );*/

        } catch (error) {
            mostrarMensaje(error,'mensaje_error');
        }
        
        return false;
    });

    $("#cerrarVentana").click(function (e) { 
        e.preventDefault();

        /*$.post(RUTA+"proyecto/actualizaTabla",
            function (data, textStatus, jqXHR) {
                $("#tablaPrincipal tbody")
                    .empty()
                    .append(data);
                $("#proceso").fadeOut();
                $("form")[0].reset();
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
            

        item= {};
        
        item['reg']        = REG;
        item['tipo']       = TIPO;
        item['puntaje']    = PUNTAJE;
        item['peso']       = PESO;
        item['entidad']    = ENTIDAD;
        item['orden']      = ORDEN;
        item['usuario']    = USUARIO;
    
        DATA.push(item);
       
    })

    return DATA;
}