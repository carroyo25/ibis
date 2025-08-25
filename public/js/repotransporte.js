$(function(){
    $("#espera").fadeOut();

    llenarListadoTransportes()
})

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && document.getElementById("vistaAdjuntos").style.display === 'block') {
        fadeOut(document.getElementById("vistaAdjuntos"));
    }
});

document.addEventListener("click",(e)=>{
    if (e.target.id == 'btnConsulta'){
        e.preventDefault();

        llenarListadoTransportes();

        return false;
    }else if(e.target.matches(".pointer *")){
        e.preventDefault();

        const parentRq = e.target.closest('.pointer').dataset.pedido;
        const parentOrd = e.target.closest('.pointer').dataset.orden;

        verAdjuntos(parentRq);

        fadeIn(document.getElementById("vistaAdjuntos"));

        return false;
    }else if (e.target.id == "closeAtach"){
        e.preventDefault();
        fadeOut(document.getElementById("vistaAdjuntos"));

        return false;
    }else if (e.target.matches('.icono_archivo')){
        e.preventDefault();

        let adjunto = e.target.closest('.icono_archivo').getAttribute('href');

        document.getElementById("pdfPreview").setAttribute('src',adjunto);
        
        return false;
    }
})

llenarListadoTransportes = async () => {
    try {
        let formData = new FormData();
        formData.append("orden", document.getElementById("ordenSearch").value);
        formData.append("proyecto", document.getElementById("costosSearch").value);
        formData.append("descripcion", document.getElementById("descripSearch").value);
        formData.append("pedido", document.getElementById("nroPedido").value);
        formData.append("anio", document.getElementById("anioSearch").value);

        $("#esperar").css({"display":"block"});

        const response = await fetch(RUTA + "repotransporte/transportes", {
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
            tr.dataset.orden = element.id_regmov;
            tr.dataset.pedido = element.idreg;
            tr.innerHTML = `<td class="textoCentro">${element.orden}</td>
                            <td class="textoCentro">${element.pedido || ''}</td>
                            <td class="textoCentro">${element.cper || ''}</td>
                            <td class="textoCentro">${element.ccodprod || ''}</td>
                            <td class="textoCentro">${element.ccodproy || ''}</td>
                            <td class="pl20px">${element.cobserva || ''}</td>`;

            fragment.appendChild(tr);
        });

        tablaCuerpo.appendChild(fragment);
        $("#esperar").css({"display":"none"});

    } catch (error) {
        console.error("Error en llenarListado:", error.messagge);
    }
}

function verAdjuntos(orden){
    try {
        let formData = new FormData();
        formData.append("orden",orden);

        $("#esperar").css({"display":"block"});

        fetch(RUTA + "repotransporte/adjuntos", {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data =>{

            document.getElementById("pdfPreview").setAttribute('src','');
            document.getElementById('listaAdjuntos').innerHTML = "";

            data.datos.forEach(element => {
                const li = document.createElement("li");
                const a = document.createElement('a');
                a.href = 'http://sicalsepcon.net/ibis/public/documentos/pedidos/adjuntos/'+element.creferencia;
                a.target = "pdfPreview";
                a.classList.add('icono_archivo');
                
                a.innerHTML = `<i class="fas fa-file" style="pointer-events:none"></i>
                                <p style="pointer-events:none">${element.cdocumento}</p>`; 

                li.appendChild(a);
                document.getElementById('listaAdjuntos').appendChild(li);
            });
        })
        .catch(error => {
            console.log(error.message);
        })

        $("#esperar").css({"display":"none"});

    } catch (error) {
        console.error("Error en llenarListado:", error.message);
        $("#esperar").css({"display":"none"});
    }
}

reporteExcel = async() = () => {
    try {
        
    } catch (error) {
        
    }
}

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


async function getElements(){
    try {
        $("#esperar").fadeIn();
    } catch (error) {
        
    }
}