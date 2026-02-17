$("#esperar").css({"display":"none","opacity":"0"});

const modal_registro = document.getElementById("dialogo_registro");

const bntRegister = document.getElementById("nuevoRegistro");
const bntExport = document.getElementById("excelFile");


bntRegister.addEventListener('click',(e) =>{
    e.preventDefault();

    modal_registro.style.display = 'block';

    return false;
})