$(function (){
    $("#btnSend").click(function (e) { 
        e.preventDefault();
        
        $("#pregunta").fadeIn();

        return false;
    });

    $("#btnCancelarPregunta").click(function (e) { 
        e.preventDefault();
        
        $("#pregunta").fadeOut();
        
        return false;
    });

    $("#btnAtach").click(function (e) { 
        e.preventDefault();
        
        $("#cotizacion").trigger("click");

        return false;
    });

    $(".tablas_items tbody").on("keypress",".precio", function (e) {
        if (e.which == 13){
            try {
                if ( $('input:radio[name=igv]:checked').val() == undefined) throw "El precio incluye IGV?";
                let cant = $(this).parent().parent().find("td").eq(4).text();
                let precio = $(this).val();
                let total = (parseFloat(cant) * parseFloat(precio));

                $(this).parent().parent().find("td").eq(6).text(total.toFixed(2));    
            } catch (error) {
                mostrarMensaje(error,'mensaje_error');
            }
        }
    });
})