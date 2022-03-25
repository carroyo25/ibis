$(function() {
    $("#btnAcceso").click(function (e) { 
        e.preventDefault();
        
        if ($("#usuario").val() == "") {
            mostrarMensaje("Ingrese su usuario","mensaje_error");
            $("#usuario").focus();
            return false;
        }else if($("#clave").val() == ""){
            mostrarMensaje("Ingrese su clave","mensaje_error");
            $("#clave").focus()
            return false;
        }

        let str = $('form').serialize();

        $.post(RUTA+"main/accesoUsuario", str,
            function (data, textStatus, jqXHR) {
                if (data){
                    //mostrarMensaje("Vamos que si se puede...","mensaje_correcto");
                    window.location = RUTA + "panel";
                }else{
                    mostrarMensaje("Usario o clave erroneos","mensaje_error");
                }
            },
            "text"
        );

        return false;
    });

    $("#verclave").click(function (e) { 
        if ($(this).is(':checked')) {
            $('#clave').attr('type', 'text');
        }else {
            $('#clave').attr('type', 'password');
        }
    });
})