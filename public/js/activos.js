$("#esperar").css({"display":"none","opacity":"0"});

const modal_registro = document.getElementById("dialogo_registro");

const btnRegister = document.getElementById("nuevoRegistro");
const btnExport = document.getElementById("excelFile");
const btnCancelDialog = document.getElementById("btnCancelarDialogoKardex");

const btnAddItem = document.getElementById("btnAddItem");
const btnSearchReg = document.getElementById("btnSearchReg");
const btnSearchInv = document.getElementById("btnSearchInv");


const inpCodSearch = document.getElementById("codigoSearch");
const inpDescSearch = document.getElementById("descripSearch");
const inpInternal = document.getElementById("interno");
const inpUnidad = document.getElementById("unidad");

const slcCostos = document.getElementById("centro_costos");

const tblActivos = document.getElementById("tabla_detalles_activos");
const bdyActivos = document.getElementById("table_body");


btnRegister.addEventListener('click',(e) =>{
    e.preventDefault();

    modal_registro.style.display = 'block';

    return false;
});


btnCancelDialog.addEventListener('click',(e)=>{
    e.preventDefault();

    modal_registro.style.display = 'none';

    return false;
});

inpCodSearch.addEventListener('keydown',(e)=>{
    if( e.key  == "Enter" ){

        let formData = new FormData();
        formData.append("costos",slcCostos.value);
        formData.append("codigo",inpCodSearch.value);

        fetch(RUTA+"activos/buscaCodigo",{
            method:'POST',
            body:formData,
        })
        .then(response => response.json())
        .then(data =>{
            inpDescSearch.value = data.datos[0]['descripcion'];
            inpInternal.value = data.datos[0]['id_cprod'];
            inpUnidad.value = data.datos[0]['cabrevia'];
        })

        e.preventDefault(); // Evita el comportamiento por defecto
    }

    return false;
})


btnAddItem.addEventListener('click',(e)=>{
    e.preventDefault();

    const tr = document.createElement('tr');
    tr.dataset.grabado = 0
    tr.classList.add("pointer");

    tr.innerHTML = `<td class="textoCentro">${inpCodSearch.value}</td>
                    <td>${descripSearch.value}</td>
                    <td>${inpUnidad.value}</td>
                    <td>1</td>
                    <td><input type="text" name="registro"></td>
                    <td><input type="text" name="estado"></td>
                    <td><input type="text" name="serie"></td>
                    <td><input type="text" name="asignado"></td>
                    <td><input type="date" name="calibracion"></td>
                    <td><input type="date" name="vencimiento"></td>
                    <td><input type="text" name="observaciones"></td>
                    <td class="textoCentro"><a href=""><i class="fas fa-ban"></i></a></td>`;

    bdyActivos.appendChild(tr);

    return false;
})


