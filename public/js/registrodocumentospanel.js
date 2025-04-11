
let logon = localStorage.getItem("logon");

const $ = document;
const entidad = $.getElementById('entidad');
const ruc = $.getElementById('ruc');
const id = $.getElementById('id_ent');

const ordenes = $.querySelector("#listaOrdenes");

const inputUpload = $.getElementById("uploadAtach");

let colorsIcons = ['gray','brown','blueviolet','cornflowerblue','#cb2025'],
    indexOrden = 0,
    textOrden = "";


//verifica si se hizo un correcto ingreso al sistema
if (logon) {
    entidad.textContent = localStorage.getItem("entidad");
    ruc.textContent     = localStorage.getItem("ruc");
    id.value            = localStorage.getItem("id");
}else{
    localStorage.clear();
    window.location.href = "http://localhost/ibis/public/registrodocumentos/";
}

//inicializar para la notificacion
let notifier = new AWN(),
    errorCantEnti = false,
    errorMail = false;

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
            let bkgIcon = element.nEstadoReg == null ? 0 : element.nEstadoReg;

            const li = document.createElement("li");
            li.dataset.enti = element.id_centi;
            li.dataset.ordenid = element.id_regmov;
            li.dataset.estado  = element.nEstadoReg;
            li.dataset.cnumero = element.cnumero;
            li.classList.add("orden_class");
            
            const link = document.createElement("a");
            link.style.color=colorsIcons[bkgIcon];

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

        indexOrden = e.target.closest('li').dataset.ordenid;
        textOrden = e.target.closest('li').dataset.cnumero;

        return false;
   }else if(e.target.matches('.botones__click_accion *')){
        e.preventDefault();
 
        if (e.target.closest('a').getAttribute('href') == 'click_upload'){
            try {
                if (indexOrden == 0) throw new Error ('Seleccione una orden de compra o servicio');
                
                notifier.info("Se procesara la orden Nro : "+ textOrden);

                inputUpload.click();

            } catch (error) {
                notifier.alert(error.message);
            }
        }
   

        return false;
   }
})

$.addEventListener('change',(e) => {
    if (e.target.matches('#uploadAtach')){
        e.preventDefault();

        let fp = e.target.files,
            lg = fp.length;

        if ( lg > 0 ) {
            for (let i = 0; i < lg; i++){
                let fileName = fp[i].name;
                console.log(fileName);
            }
        }

        //console.log(lg);
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