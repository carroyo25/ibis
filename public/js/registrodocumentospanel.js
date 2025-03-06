let logon = localStorage.getItem("logon");


const $ = document;
const entidad = $.getElementById('entidad');
const ruc = $.getElementById('ruc');

//verifica si se hizo un correcto ingreso al sistema
if (logon) {
    entidad.textContent = localStorage.getItem("entidad");
    ruc.textContent = localStorage.getItem("ruc");
    id = localStorage.getItem("id");
}else{
    localStorage.clear();
    window.location.href = "http://localhost/ibis/public/registrodocumentos/php/panel.php";
}

listarOrdenes(id)



const listarOrdenes = (id) =>{
    try {
        let formData = new FormData();
        formData.append('id', id);
        formData.append('funcion','listarOrdenesEntidad');

        fetch('/public/registrodocumentos/inc/procesos.php',{
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data =>{
            console.log(data);
        })


    } catch (error) {
        
    }
}


