
let logon = localStorage.getItem("logon");

const $ = document;
const entidad = $.getElementById('entidad');
const ruc = $.getElementById('ruc');
const id = $.getElementById('id_ent');

const ordenes = $.querySelector("#listaOrdenes");


//verifica si se hizo un correcto ingreso al sistema
if (logon) {
    entidad.textContent = localStorage.getItem("entidad");
    ruc.textContent     = localStorage.getItem("ruc");
    id.value            = localStorage.getItem("id");
}else{
    localStorage.clear();
    window.location.href = "http://localhost/ibis/public/registrodocumentos/";
}

const listarOrdenes = async (id) =>{
    try {
        let formData = new FormData();
        formData.append('id', id.value);
        formData.append('funcion','listarOrdenesEntidad');

        const response = await fetch('../inc/procesos.php', {
            method: 'POST',
            body: formData
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        // Clear existing orders
        ordenes.innerHTML = '';
        
        // Create document fragment for better performance
        const fragment = document.createDocumentFragment();

        data.forEach(element => {
            const li = document.createElement("li");
            li.dataset.enti = element.id_centi;
            li.dataset.ordenid = element.id_regmov;
            li.classList.add("orden_class");
            
            const link = document.createElement("a");

            let tipodoc = element.ntipmov == 37 ? 'OC':'OS';

            link.href = `#${element.id_regmov}`; // Using # for href if it's not a real URL
            link.innerHTML = `<p><i class="far fa-file"></i></p><span>${tipodoc}-${element.cnumero}-${element.cper}</span>`;
            
            li.appendChild(link);
            fragment.appendChild(li);
        });
        
        ordenes.appendChild(fragment);

    } catch (error) {
        console.error('Error:', error);
    }
}

listarOrdenes(id)

$.addEventListener('click',(e)=>{
   if (e.target.matches('.orden_class *')){
        e.preventDefault();

        toggleBorder(e.target.closest('li'));

        e.target.closest('li').dataset.enti;

        return false;
   }
})


const toggleBorder = (element) => {
    if (element.style.border === '1px solid black') {
      element.style.border = 'none';
    } else {
      element.style.border = '1px solid black';
      element.style.borderRadius = '8px';
    }
  }