const $ = document;
const btnActualiza  = $.getElementById("hojaActualiza");
const rucprove      = $.getElementById("entruc");
const claveprove    = $.getElementById("entpass");

//inicializar para la notificacion
let notifier = new AWN(),
    errorCantEnti = false,
    errorMail = false;


btnActualiza.onclick = (e) => {
    e.preventDefault();

    try {
        if (rucprove.value == "") throw new Error("Ingrese ruc del proveedor");
        if (claveprove.value == "") throw new Error("Ingrese clave del proveedor");

        let formData = new FormData();
            formData.append("ruc",rucprove.value);
            formData.append("clave",claveprove.value);
            formData.append("funcion","login");

        fetch("procesos.php",{
            method: "POST",
            body: formData
        })
        .then(response =>response.json())
        .then(data =>{

            if ( data.ruc_exist == true )
                window.location.href = "http://localhost/ibis/public/hojaregistro/actualiza.php";
            else
                notifier.alert(data.message);
                
        })   
    } catch (error) {
        notifier.alert("Ingrese sus datos para modificar");
    }

   

    return false;
}