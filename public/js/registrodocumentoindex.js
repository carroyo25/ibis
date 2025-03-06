const $ = document;
const wrap = $.getElementById("wrap");
const input_ruc = $.getElementById("entidad_ruc");
const input_pass = $.getElementById("entidad_clave")

//inicializar para la notificacion
let notifier = new AWN(),
    errorCantEnti = false,
    errorMail = false;

$.addEventListener("click",(e) => {
   if (e.target.matches(".button")) {
        e.preventDefault();

        try {
            if (input_ruc.value === "") throw new Error('Por favor ingrese el RUC de la empresa');
            if (input_pass.value === "") throw new Error('Por favor ingrese la clave');

            let formData = new FormData();
            formData.append('funcion','login');
            formData.append('ruc',input_ruc.value);
            formData.append('clave',input_pass.value);

            fetch('../registrodocumentos/inc/procesos.php',{
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success'){

                    notifier.info(data.message);
                    localStorage.setItem('logon',true);
                    localStorage.setItem('ruc',data.ruc);
                    localStorage.setItem('entidad',data.entidad);
                    localStorage.setItem('id',data.id);

                    window.location.href = "http://localhost/ibis/public/registrodocumentos/php/panel.php";

                }else{
                    notifier.alert(data.message);
                    localStorage.clear();
                }
            })


        } catch (error) {
            notifier.alert(error.message);   
        }
        

        return false;
   }
})