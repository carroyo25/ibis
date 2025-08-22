$(function(){
    llenarListado();
})

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && document.getElementById("vistaprevia").style.display === 'block') {
        fadeOut(document.getElementById("vistaprevia"));
    }
});

document.addEventListener("click",(e)=>{
    if (e.target.id == 'btnConsulta'){
        e.preventDefault();

        llenarListado();

        return false;
    }else if (e.target.matches(".pointer *")){
        e.preventDefault();

        const parentPointer = e.target.closest('.pointer').dataset.guiaid;
        const parentSunat = e.target.closest('.pointer').dataset.guiasunatnro;

        if ( parentSunat === "null" || parentSunat === null ){
            document.getElementById("pdfPreview").setAttribute('src','http://sicalsepcon.net/ibis/public/documentos/guias_remision/' + parentPointer +'.pdf');
        }else{
            document.getElementById("pdfPreview").setAttribute('src','http://sicalsepcon.net/ibis/public/documentos/guias_remision/20504898173-09-T001-' + parentSunat +'.pdf');
        }

        fadeIn(document.getElementById("vistaprevia"));

        return false;
    }else if (e.target.matches(".cerrar_vista")){
        e.preventDefault();

        fadeOut(document.getElementById("vistaprevia"));

        return false;
    }
})

llenarListado = async () => {
    try {
        let formData = new FormData();
        formData.append("anio", document.getElementById("anioSearch").value);
        formData.append("guia", document.getElementById("guiaSearch").value);
        formData.append("sunat", document.getElementById("guiaSunat").value);

        $("#esperar").css({"display":"block"});
        
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
            tr.dataset.guiasunatnro = element.guiasunat;
            tr.innerHTML = `<td class="textoCentro">${element.cnumguia}</td>
                            <td class="textoCentro">${element.freg || ''}</td>
                            <td class="textoCentro">${element.anio || ''}</td>
                            <td class="textoCentro">${element.guiasunat || ''}</td>
                            <td class="textoCentro">${element.cenvio || ''}</td>
                            <td class="pl20px">${element.cobserva || ''}</td>`;

            fragment.appendChild(tr);
        });

        tablaCuerpo.appendChild(fragment);
        $("#esperar").css({"display":"none"});

    } catch (error) {
        console.error("Error en llenarListado:", error);
        // You might want to show an error message to the user here
    }
};

// Función para fade in
function fadeIn(element) {
    element.style.display = 'block';
    // Timeout para permitir el cambio de display antes de la transición
    setTimeout(() => {
        element.style.opacity = '1';
    }, 10);
}

// Función para fade out
function fadeOut(element) {
    element.style.opacity = '0';
    // Esperar a que termine la transición antes de ocultar
    setTimeout(() => {
        element.style.display = 'none';
    }, 300); // Debe coincidir con la duración de la transición en CSS (0.3s = 300ms)
}

