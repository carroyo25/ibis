const $ = document;
const btnActualiza  = $.getElementById("hojaActualiza");
const rucprove      = $.getElementById("entruc");;
const claveprove    = $.getElementById("entpass");


btnActualiza.onclick = (e) => {
    e.preventDefault();

    try {
        if (rucprove.value == "") throw new Error("Ingrese ruc del proveedor");
        if (claveprove.value == "") throw new Error("Ingrese clave del proveedor");

        let formData = new FormData();
        formData.append("ruc",rucprove.value);
        formData.append("clave",claveprove.value);
        formData.append("funcion","verificar");

        fetch("procesos.php",{
            method: "POST",
            body: formData
        })
        .then(response =>response.json())
        .then(data =>{
            console.log('rediccionado');
            window.location.href = "http://localhost/ibis/public/hojaregistro/actualiza.php";    
        })

        
           
    } catch (error) {
        Swal.fire({
            icon: "error",
            title: "Oops...",
            text: error.message,
          });
    }

   

    return false;
}