const body = document.querySelector("#contenedor tbody");
const txtCodigoSearch = document.getElementById('codigo');
const txtDescripSearch = document.getElementById('descripcion');
const btnExportar = document.getElementById('excelFile');


//bloque de scrol
let listItemFinal = null;
let estoyPidiendo = false;

const observandoListItem = listItem => {
	if ( listItem[0].isIntersecting ) {
		query();
	}
}

const settings = {
	threshold: 1
}

let observador = new IntersectionObserver(
	observandoListItem,
	settings
);

const query = async () => {
	if (estoyPidiendo) return;
	estoyPidiendo = true;
	let pagina = parseInt(body.dataset.p) || 1;
	const FD = new FormData();
	FD.append('pagina',pagina);

	const r = await fetch(RUTA+'catalogo/listaScroll',{
		method: 'POST',
		body:FD
	});

	const j  = await r.json();
	j[0].productos.forEach(i => {
		const tr = document.createElement('tr');
		tr.innerHTML = `<td class="textoCentro">${i.ccodprod}</td>
                        <td class="textoCentro bienes">${i.tipo}</td>
                        <td class="pl20px">${i.cdesprod}</td>
                        <td class="textoCentro">${i.cabrevia}</td>`;
        tr.classList.add("pointer");
		body.appendChild(tr);
	})

	if (listItemFinal){
		observador.unobserve(listItemFinal);
	}

	if (j[0].quedan) { //devuelve falso si ya no quedan mas registros
		listItemFinal = body.lastElementChild.previousElementSibling;
		observador.observe( listItemFinal);
		estoyPidiendo = false;
		body.dataset.p = ++pagina;
	}
}

query();

//fin del bloque de scroll

txtCodigoSearch.addEventListener("keypress", e =>{
    if(e.which === 13){
        const FD = new FormData();
        FD.append('criterio',e.target.value);
        
        fetch(RUTA+"catalogo/buscaCodigo",{
            method: 'POST',
            body:FD
        })
        .then(function(response){
            return response.json();
        })
        .then(dataJson => {
            if(dataJson.productos){
                body.innerHTML = "";
                dataJson.productos.forEach(i => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `<td class="textoCentro">${i.ccodprod}</td>
                                <td class="textoCentro bienes">${i.tipo}</td>
                                <td class="pl20px">${i.cdesprod}</td>
                                <td class="textoCentro">${i.cabrevia}</td>`;
                    tr.classList.add("pointer");
                    body.appendChild(tr);
                })
            }
        })
        .catch(error => console.log("Proceso fallido",error));
    }else {
        console.log("No sale");
    };
})

txtDescripSearch.addEventListener("keypress", e =>{
    if(e.which === 13){
        const FD = new FormData();
        FD.append('criterio',e.target.value);
        
        fetch(RUTA+"catalogo/buscaPalabra",{
            method: 'POST',
            body:FD
        })
        .then(function(response){
            return response.json();
        })
        .then(dataJson => {
            if(dataJson.productos){
                body.innerHTML = "";
                dataJson.productos.forEach(i => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `<td class="textoCentro">${i.ccodprod}</td>
                                <td class="textoCentro bienes">${i.tipo}</td>
                                <td class="pl20px">${i.cdesprod}</td>
                                <td class="textoCentro">${i.cabrevia}</td>`;
                    tr.classList.add("pointer");
                    body.appendChild(tr);
                })
            }
        })
        .catch(error => console.log("Proceso fallido",error));
    }else {
        console.log("No sale");
    }; 
})

btnExportar.addEventListener("click",e => {
    e.preventDefault();

    //let response = fetch(RUTA+'catalogo/catalogoXls');
    fetch(RUTA+"catalogo/catalogoXls",{
        method: 'POST',
    })
    .then(function(response){
        return response.json();
    })
    .then(dataJson => {
        if(dataJson.productos){
            
        }
    })
    .catch(error => console.log("Proceso fallido",error));


    return false;
})

