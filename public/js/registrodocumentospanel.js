
let logon = localStorage.getItem("logon");

const $ = document;
const entidad = $.getElementById('entidad');
const ruc = $.getElementById('ruc');
const id = $.getElementById('id_ent');

const ordenes = $.querySelector("#listaOrdenes");

const inputUpload = $.getElementById("uploadAtach");
const listaAdjuntoVacia = $.querySelector(".atach_list_empty");
const contenedorAdjuntos = $.querySelector(".atach_list_documents");
const listaAdjuntos = $.getElementById("list_files_atachs");

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
            link.classList.add("orden");
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
    //console.log(e.target);
    if(e.target.matches('.botones__click_accion')){
        e.preventDefault();
 
        if (e.target.getAttribute('href') == 'click_upload'){
            try {
                if ( indexOrden == 0 ) throw new Error ('Seleccione una orden de compra o servicio');
                
                notifier.info("Se procesara la orden Nro : "+ textOrden);

                inputUpload.click();

            } catch (error) {
                notifier.alert(error.message);
            }
        }else if(e.target.getAttribute('href') == 'click_send'){
            try {
                const ul = document.getElementById("list_files_atachs");
                const elementoLi = ul.querySelectorAll('li');
                const numeroDeElementos = elementoLi.length;

                if ( numeroDeElementos == 0 ) new Error ('No se ha registrado ningun archivo para procesar');

                let filesToSend = JSON.stringify(fileListArray()),
                    idorden     = document.getElementById("id_ord").value,
                    idproveedor = document.getElementById("id_ent").value,
                    formData    = new FormData();

                formData.append("files",filesToSend);
                formData.append("funcion","registrarDocumentos");
                formData.append("ordenId",idorden);
                formData.append("entidad",idproveedor);

                fetch('../inc/procesos.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    
                });
                
            } catch (error) {
                notifier.alert(error.message);
            }
        }
        return false;
   }else if(e.target.matches('.atach_file')){
        e.preventDefault();

        $.getElementById("nombre_archivo").innerHTML = e.target.lastChild.textContent;

        return false;
   }else if(e.target.matches('.lista_ul *')){
        e.preventDefault();

        const items = document.querySelectorAll('.lista_ul li');

        items.forEach(li => {
            li.classList.remove('activo'); // Reemplaza 'clase-a-quitar' con tu clase
        });

        document.getElementById("id_ord").value = e.target.dataset.ordenid;
       
        let formData = new FormData();

        formData.append("orden",e.target.dataset.ordenid);
        formData.append("centi",document.getElementById("id_ent").value);
        formData.append("funcion","consultarDocumentos");

        indexOrden = e.target.closest('li').dataset.ordenid;
        textOrden = e.target.closest('li').dataset.cnumero;

        fetch('../inc/procesos.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    e.target.classList.add("activo");
                    
                    if ( data.archivos > 0) {
                        listaAdjuntoVacia.style.zIndex = "1";
                        contenedorAdjuntos.style.zIndex = "2";
                        listaAdjuntos.innerHTML = "";
                        const atach_fragment = $.createDocumentFragment();

                        data.resultado.forEach(element =>{
                            const li = $.createElement("li");
                            li.classList.add("atach_class");
                            
                            const link = $.createElement("a");
                            link.classList.add("atach_file");
                            link.dataset.file_estatus = 1;
                            
                            link.href = `#${element.idreg}`; // Using # for href if it's not a real URL
                            link.innerHTML = `<p><i class="fas fa-file-pdf" style="color: #a61111;"></i></p><span>${element.namefile}</span>`;
                            
                            li.appendChild(link);

                            atach_fragment.appendChild(li);
                        });

                        listaAdjuntos.appendChild(atach_fragment);

                    }else{
                        listaAdjuntoVacia.style.zIndex = "2";
                        contenedorAdjuntos.style.zIndex = "1";
                        listaAdjuntos.innerHTML = "";
                    }
                })

        return false;
   }
})

$.addEventListener('change',(e) => {
    if (e.target.matches('#uploadAtach')){
        e.preventDefault();

        const atach_fragment = $.createDocumentFragment();

        let fp = e.target.files,
            lg = fp.length;

        if ( lg > 0 ) {
            listaAdjuntoVacia.style.zIndex = "1";
            contenedorAdjuntos.style.zIndex = "2";
            listaAdjuntos.innerHTML = "";

            for (let i = 0; i < lg; i++){
                let fileName = fp[i].name;

                const li = $.createElement("li");
                li.classList.add("atach_class");

                const link = $.createElement("a");
                link.classList.add("atach_file");
                link.dataset.file_estatus = 0;
                link.href = `#${indexOrden}-${i}`; // Using # for href if it's not a real URL
                link.innerHTML = `<p><i class="fas fa-file-pdf" style="color: #a61111;"></i></p><span>${fileName}</span>`;
                
                li.appendChild(link);

                atach_fragment.appendChild(li);
            }

            listaAdjuntos.appendChild(atach_fragment);
            $.getElementById("archivos_presentados").innerHTML = lg;
            $.getElementById("fecha_presentacion_legajo").innerHTML = fechaActual()[0];
            $.getElementById("hora_presentacion_legajo").innerHTML = fechaActual()[1];
            $.getElementById("fecha_revision_legajo").innerHTML = '';
            $.getElementById("estado_revision_legajo").innerHTML = "PRESENTADO";
        }

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

const fechaActual = () => {
    const fecha_actual = new Date();
    return Array(fecha_actual.getDate() + "/" + (fecha_actual.getMonth() + 1) + "/" + fecha_actual.getFullYear(),
                fecha_actual.getHours() + ":" + fecha_actual.getMinutes() + ":" + fecha_actual.getSeconds())
}

const fileListArray = () => {
    const items = [];

    document.querySelectorAll('.atach_list_documents li').forEach(li => {
        items.push(li.textContent.trim()); // o li.getAttribute('data-value')
    });

    return items;
}

