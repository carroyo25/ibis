$(function(){
    let registro = 0,fila;

    $("#esperar").fadeOut();

    $("#tablaPrincipal tbody").on("click","tr", function (e) {
        e.preventDefault();

        registro = $(this).data('doc');
        fila = $(this);

        $("#pregunta").fadeIn();

        return false;
    });

    $("#btnAceptarAjuste").click(function (e) { 
        e.preventDefault();

        let formData = new FormData();
        formData.append("id",registro);
        formData.append("user",$("#id_user").val());

        fetch(RUTA+'autorizaajuste/autoriza',{
            method:'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            fila.remove();
            mostrarMensaje(data.mensaje,"mensaje_correcto");
        })

        $("#pregunta").fadeOut();
        
        return false;
    });

    $("#btnCancelarAjuste").click(function (e) { 
        e.preventDefault();
        
        $("#pregunta").fadeOut();

        return false;
    });
    
})