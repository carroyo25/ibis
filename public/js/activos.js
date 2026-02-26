$("#esperar").css({"display":"none","opacity":"0"});

const modal_registro = document.getElementById("dialogo_registro");
const modal_items_registrados = document.getElementById('registros');
const modal_items_inventario = document.getElementById('inventarios');

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
const bdyIngresos = document.getElementById("cuerpo_ingresos");
const bdyInventarios = document.getElementById("cuerpo_inventarios");

const lnkClose = document.getElementById("closeSearch");


btnRegister.addEventListener('click',(e) =>{
    e.preventDefault();

    modal_registro.style.display = 'block';

    return false;
});

document.addEventListener('click',(e)=>{
    //console.log(e.target);

    if (e.target.matches(".closeModal *")){
        e.preventDefault();

        e.target.closest('.modal').style.display = "none";

        return false;
    }
})


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
                    <td><input type="text" name="ubicacion"></td>
                    <td><input type="date" name="calibracion"></td>
                    <td><input type="date" name="vencimiento"></td>
                    <td><input type="text" name="observaciones"></td>
                    <td class="textoCentro"><a href=""><i class="fas fa-ban"></i></a></td>`;

    bdyActivos.appendChild(tr);

    return false;
})


btnSearchReg.addEventListener('click',(e)=>{
    e.preventDefault();

    let formData = new FormData();
    formData.append('codigo',inpInternal.value);
    formData.append('costos',slcCostos.value);

    bdyIngresos.innerHTML = "";

    fetch(RUTA+"activos/registros",{
        method:'POST',
        body:formData
    })
    .then(response => response.json())
    .then(data =>{
        if (data.datos.length > 0) {
            data.datos.forEach(el => {
                const tr = document.createElement('tr');
                tr.classList.add("pointer");
                tr.dataset.id = el.idreg;
                tr.dataset.cant = el.cantidad;
                
                tr.innerHTML = `<td class="textoCentro">${el.idreg}</td>
                                <td class="textoCentro">${el.ccodproy}</td>
                                <td class="textoDerecha">${el.cantidad}</td>
                                <td class="textoCentro">${el.pedido}</td>`;

                bdyIngresos.appendChild(tr);                
            });

            modal_items_registrados.style.display = 'block';
        }else{
            mostrarMensaje("No se encontraron registros","mensaje_error");
        }
    })

    return false;
})

btnSearchInv.addEventListener('click',(e)=>{
    e.preventDefault();

    let formData = new FormData();
    formData.append('codigo',inpInternal.value);
    formData.append('costos',slcCostos.value);

    bdyInventarios.innerHTML = "";

    fetch(RUTA+"activos/inventarios",{
        method:'POST',
        body:formData
    })
    .then(response => response.json())
    .then(data =>{
        if (data.datos.length > 0) {
            data.datos.forEach(el => {
                const tr = document.createElement('tr');
                tr.classList.add("pointer");
                tr.dataset.id = el.idreg;
                tr.dataset.cant = el.cant_ingr;
                tr.dataset.serie = el.cserie,
                tr.dataset.ubicacion = el.ubicacion,
                tr.dataset.estado = el.estado
                
                tr.innerHTML = `<td class="textoCentro">${el.idreg}</td>
                                <td class="textoCentro">${el.fecha_inventario}</td>
                                <td class="textoDerecha">${el.ccodproy}</td>
                                <td class="textoCentro">${el.cant_ingr}</td>
                                <td class="textoCentro">${el.cserie}</td>
                                <td class="textoCentro">${el.ubicacion}</td>`;

                bdyInventarios.appendChild(tr);                
            });

            modal_items_inventario.style.display = 'block';
        }else{
            mostrarMensaje("No se encontraron registros","mensaje_error");
        }
    })

    return false;
})

// Usar event delegation - un solo event listener en el contenedor
bdyIngresos.addEventListener('click', (e) => {
    const tr = e.target.closest('tr'); // Encuentra el TR más cercano
    const registro = e.target.closest('tr').dataset.id
    
    if (tr) { // Si se hizo clic en un TR o dentro de él
        e.preventDefault();

        for (let index = 0; index < parseInt(tr.dataset.cant); index++) {
            const tr = document.createElement('tr');
            tr.dataset.grabado = 0
            tr.classList.add("pointer");

            tr.innerHTML = `<td class="textoCentro">${inpCodSearch.value}</td>
                            <td clas="pl20px">${descripSearch.value}</td>
                            <td>${inpUnidad.value}</td>
                            <td>1</td>
                            <td><input type="text" name="registro" value="${registro}" readonly></td>
                            <td><input type="text" name="estado"></td>
                            <td><input type="text" name="serie"></td>
                            <td><input type="text" name="asignado"></td>
                            <td><input type="text" name="ubicacion"></td>
                            <td><input type="date" name="calibracion"></td>
                            <td><input type="date" name="vencimiento"></td>
                            <td><input type="text" name="observaciones"></td>
                            <td class="textoCentro"><a href=""><i class="fas fa-ban"></i></a></td>`;

            bdyActivos.appendChild(tr);
        }

        return false;
    }
});


// Usar event delegation - un solo event listener en el contenedor
bdyInventarios.addEventListener('click', (e) => {
    const tr = e.target.closest('tr'); // Encuentra el TR más cercano
    const registro = e.target.closest('tr').dataset.id;
    const serie = e.target.closest('tr').dataset.serie;
    const estado = e.target.closest('tr').dataset.estado;
    const ubicacion = e.target.closest('tr').dataset.ubicacion;
    
    if (tr) { // Si se hizo clic en un TR o dentro de él
        e.preventDefault();

        for (let index = 0; index < parseInt(tr.dataset.cant); index++) {
            const tr = document.createElement('tr');
            tr.dataset.grabado = 0
            tr.classList.add("pointer");

            tr.innerHTML = `<td class="textoCentro">${inpCodSearch.value}</td>
                            <td clas="pl20px">${descripSearch.value}</td>
                            <td>${inpUnidad.value}</td>
                            <td>1</td>
                            <td><input type="text" name="registro" value="${registro}" readonly></td>
                            <td><input type="text" name="estado"></td>
                            <td><input type="text" name="serie"></td>
                            <td><input type="text" name="asignado"></td>
                            <td><input type="text" name="ubicacion"></td>
                            <td><input type="date" name="calibracion"></td>
                            <td><input type="date" name="vencimiento"></td>
                            <td><input type="text" name="observaciones"></td>
                            <td class="textoCentro"><a href=""><i class="fas fa-ban"></i></a></td>`;

            bdyActivos.appendChild(tr);
        }

        return false;
    }
});

bdyActivos.addEventListener('keypress', (e) => {
    if (e.key == "Enter") {
        // Verificar que el evento viene de un input
        if (e.target.tagName === 'INPUT') {
            // Obtener la celda padre (td) del input
            const celda = e.target.closest('td');
            
            if (celda) {
                // Obtener el índice de la columna
                const indiceColumna = celda.cellIndex;
                
                // Si es la columna 3 (índice 2 en JavaScript)
                if (indiceColumna === 6) {
                    const valorInput = e.target.value;
                    
                    // Aquí puedes usar el valor para lo que necesites
                    if (valorInput.trim() !== '') {
                        let formData = new FormData();

                        formData.append("serie",valorInput);
                        formData.append('costos',slcCostos.value);

                        fetch(RUTA+'activos/asignados',{
                            method:'POST',
                            body:formData
                        })
                        .then(response =>response.json())
                        .then(data=>{
                            const filaActual = e.target.closest('tr');

                            if (filaActual){
                                if (data.asignado){
                                    const celdaColumna8 = filaActual.cells[7]; // Índice 7 = columna 8
                                    if (celdaColumna8)  {
                                        const inputColumna8 = celdaColumna8.querySelector('input');
                                        if (inputColumna8) {
                                            inputColumna8.value = data.datos[0]['paterno']+' '+data.datos[0]['materno']+' '+data.datos[0]['nombres'];
                                        }
                                    }
                                }else{
                                    const celdaColumna9 = filaActual.cells[8]; // Índice 8 = columna 9
                                    if (celdaColumna9){
                                        const inputColumna9 = celdaColumna9.querySelector('input');
                                        if (inputColumna9) {
                                            inputColumna9.value = 'ALMACEN';
                                        }
                                    }
                                    
                                }
                                
                            }
                        })
                        
                    }
                }
            }
        }
    }
});
