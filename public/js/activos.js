$("#esperar").css({"display":"none","opacity":"0"});

const modal_registro = document.getElementById("dialogo_registro");

const btnRegister = document.getElementById("nuevoRegistro");
const btnExport = document.getElementById("excelFile");
const btnCancelDialog = document.getElementById("btnCancelarDialogoKardex");

btnRegister.addEventListener('click',(e) =>{
    e.preventDefault();

    modal_registro.style.display = 'block';

    return false;
});

document.addEventListener('click',(e)=>{
    if (e.target.matches(".closeModal *")){
        e.preventDefault();

        e.target.closest('.modal').style.display = "none";

        return false;
    }
})
