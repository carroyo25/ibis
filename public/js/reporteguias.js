$(function(){
    llenarListado();
})

document.addEventListener("click",(e)=>{
    if (e.target.id == 'btnConsulta'){
        e.preventDefault();

        llenarListado();

        return false;
    }else if (e.target.matches(".pointer")){
        console.log(e.target);
    }
})

const llenarListado = async () => {
    try {
        let formData = new FormData();
        formData.append("anio", document.getElementById("anioSearch").value);
        formData.append("guia", document.getElementById("guiaSearch").value);
        formData.append("sunat", document.getElementById("guiaSunat").value);

        const response = await fetch(RUTA + "reporteguias/listaGuias", {
            method: 'POST',
            body: formData
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        const tablaCuerpo = document.getElementById("tablaPrincipalCuerpo");
        
        if (!tablaCuerpo) {
            throw new Error("Element with ID 'tablaPrincipalCuerpo' not found");
        }

        tablaCuerpo.innerHTML = "";

        const fragment = document.createDocumentFragment();

        data.datos.forEach(element => {
            const tr = document.createElement("tr");
            
            tr.classList.add("pointer");
            tr.dataset.guiaid = element.cnumguia;
            tr.innerHTML = `<td class="textoCentro">${element.cnumguia}</td>
                            <td class="textoCentro">${element.freg || ''}</td>
                            <td class="pl20px">${element.corigen || ''}</td>
                            <td class="pl20px">${element.cdestino || ''}</td>
                            <td class="textoCentro">${element.anio || ''}</td>
                            <td class="textoCentro">${element.guiasunat || ''}</td>
                            <td class="textoCentro">${element.cenvio || ''}</td>
                            <td class="pl20px">${element.cobserva || ''}</td>`;

            fragment.appendChild(tr);
        });

        tablaCuerpo.appendChild(fragment);

    } catch (error) {
        console.error("Error in llenarListado:", error);
        // You might want to show an error message to the user here
    }
};

