$(function(){
    listarAdjuntos();
})

listarAdjuntos = async () => {
    try {
        let formData = new FormData();
        formData.append('nombre',document.getElementById('nameSearch').value);

        const response = await fetch(RUTA + "adjuntoproveedor/listaAdjuntos", {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        const tablaCuerpo = document.getElementById("tablaPrincipalCuerpo");

        if (!tablaCuerpo) {
            throw new Error("Element with ID 'tablaPrincipalCuerpo' not found");
        }

        tablaCuerpo.innerHTML = "";

        data.datos.forEach(e => {
            const tr = document.createElement("tr");
            tr.classList.add("pointer");
            tr.dataset.id = e.id_centi;

            tr.innerHTML = `<td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>`;
            
            tablaCuerpo.appendChild(tr);
        });

    } catch (error) {
        mostrarMensaje('No hay registros para procesar','mensaje_error');
        $("#esperar").fadeOut();
    }
}
