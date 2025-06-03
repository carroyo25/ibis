
let logon = localStorage.getItem("logon");
let filaSeleccionada = null;

const $ = document;
const entidad = $.getElementById('entidad');
const ruc = $.getElementById('ruc');
const id = $.getElementById('id_ent');

const ordenes = $.querySelector("#listaOrdenes");

const inputUpload = $.getElementById("uploadAtach");
const listaAdjuntoVacia = $.querySelector(".atach_list_empty");
const contenedorAdjuntos = $.querySelector(".atach_list_documents");
const listaAdjuntos = $.getElementById("list_files_atachs");
const preview = $.getElementById("document_check");

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
        formData.append('funcion','listarOrdenesEntidadRevision');

        const response = await fetch('../inc/procesos.php', {
            method: 'POST',
            body: formData
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        
        const estado = {0:'',
                        1:'<i class="fas fa-inbox"></i>',
                        2:'<i class="fas fa-microscope"></i>',
                        3:'<i class="fas fa-microscope"></i>',
                        4:'<i class="fas fa-unlink"></i>'}

        // Clear existing orders
        ordenes.innerHTML = '';
        
        // Create document fragment for better performance
        const fragment = document.createDocumentFragment();

        data.forEach(element => {
            let bkgIcon = element.nEstadoReg == null ? 0 : element.nEstadoReg;

            const tr = document.createElement("tr");
            tr.dataset.enti = element.id_centi;
            tr.dataset.ordenid = element.id_regmov;
            tr.dataset.estado  = element.nEstadoReg;
            tr.dataset.cnumero = element.cnumero;

            tr.classList.add("table_cell_select");

            let tipodoc = element.ntipmov == 37 ? 'OC':'OS';
            

            tr.innerHTML = `<td>${element.cper}</td>
                            <td>${tipodoc}</td>
                            <td>${element.cnumero}</td>
                            <td>${estado[element.estado]}</td>`

            
            fragment.appendChild(tr);

            tr.addEventListener('click', function() {
                $.getElementById("id_ord").value = this.dataset.ordenid;
                documentosAdjuntos(this.dataset.ordenid,this.dataset.cnumero);

                // Quitar highlight de la fila anterior
                if (filaSeleccionada) {
                    filaSeleccionada.classList.remove('highlight');
                }
                
                // Añadir highlight a la nueva fila seleccionada
                this.classList.add('highlight');
                filaSeleccionada = this;                
            });
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
 
        if (e.target.getAttribute('href') == 'click_verify'){
            try {
                if ( indexOrden == 0 ) throw new Error ('Seleccione una orden de compra o servicio');
                
                notifier.info("Se procesara la orden Nro : "+ textOrden);

                let formData = new FormData();
                formData.append('id',indexOrden);
                formData.append('funcion','validarTotal');

                fetch('../inc/procesos.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                });


            } catch (error) {
                notifier.alert(error.message);
            }
        }else if(e.target.getAttribute('href') == 'click_send'){
            const alertDialog = document.querySelector("#question-dialog");
            $.getElementById("question-dialog").style.display = 'block';
            
            const ul = document.getElementById("list_files_atachs");
            const elementoLi = ul.querySelectorAll('li');
            const numeroDeElementos = elementoLi.length;

            try {
                if ( numeroDeElementos == 0 ) new Error ('No se ha registrado ningun archivo para procesar');
                if ( indexOrden == 0 ) throw new Error ('Seleccione una orden de compra o servicio');

                alertDialog.showModal();
                
            } catch (error) {

                notifier.alert(error.message);
            }
        }
        return false;
   }else if(e.target.matches('.atach_file')){
        e.preventDefault();

        $.getElementById("nombre_archivo").innerHTML = e.target.lastChild.textContent;

        return false;
   }else if(e.target.matches(".button_click_dialog")){
        e.preventDefault();

        $.getElementById("question-dialog").style.display = 'none';
        $.getElementById("question-dialog").close();

        const fileInput = document.getElementById('uploadAtach');
        const files = fileInput.files;

        let filesToSend = JSON.stringify(fileListArray()),
            idorden     = document.getElementById("id_ord").value,
            idproveedor = document.getElementById("id_ent").value,
            formData    = new FormData();

        formData.append("files",filesToSend);
        formData.append("funcion","registrarDocumentos");
        formData.append("ordenId",idorden);
        formData.append("entidad",idproveedor);

        for (let i = 0; i < files.length; i++) {
            formData.append('filesToUpload[]', files[i]);
        }

        fetch('../inc/procesos.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            console.log(data);
        });

        return false;
   }else if(e.target.matches(".atach_class")){
        //console.log(e.target.dataset.internal);
        
        const fileShow = "http://localhost/ibis/public/documentos/proveedores/presentados/"+e.target.dataset.internal;
        const framePreview = $.getElementById("framePreview");

        framePreview.setAttribute('src',fileShow);

        preview.style.display = "block";
   }else if(e.target.matches(".modal_children_close")){
        e.target.closest(".modal").style.display = "none";
        framePreview.setAttribute('src','');
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

$.addEventListener('keydown',(e) => {
    if(e.key === "Escape"){
        $.getElementById("question-dialog").style.display = 'none';
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

const documentosAdjuntos = (id,numero) => {
    let formData = new FormData();

        formData.append("orden",id);
        formData.append("centi",document.getElementById("id_ent").value);
        formData.append("funcion","consultarDocumentos");

        indexOrden = id;
        textOrden = numero;

        fetch('../inc/procesos.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    //e.target.classList.add("activo");
                    
                    if ( data.archivos > 0) {
                        listaAdjuntoVacia.style.zIndex = "1";
                        contenedorAdjuntos.style.zIndex = "2";
                        listaAdjuntos.innerHTML = "";
                        const atach_fragment = $.createDocumentFragment();

                        data.resultado.forEach(element =>{
                            const li = $.createElement("li");
                            li.classList.add("atach_class");
                            li.dataset.internal = element.internalname;
                            
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
}

