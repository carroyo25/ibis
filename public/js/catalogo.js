/*$(function(){
    $("#descripcion").on("keypress", function (e) {
        if(e.which == 13 && $(this).val().length > 1) {
            $("#waitmodal").fadeIn();
            $.post(RUTA+"catalogo/buscaPalabra", {criterio:$(this).val()},
                function (data, textStatus, jqXHR) {
                    $("#tablaPrincipal tbody")
                        .empty()
                        .append(data);
                    //$("#waitmodal").fadeOut();  
                },
                "text"
            );
        }
    });

    $("#codigo").on("keypress", function (e) {
        if(e.which == 13 && $(this).val().length > 1) {
            $("#waitmodal").fadeIn();
            $.post(RUTA+"catalogo/buscaCodigo", {criterio:$(this).val()},
                function (data, textStatus, jqXHR) {
                    $("#tablaPrincipal tbody")
                        .empty()
                        .append(data);
                    //$("#waitmodal").fadeOut();  
                },
                "text"
            );
        }
    });
   
    $("#excelFile").on('click', function(e) {
        e.preventDefault();

        $.post(RUTA+"catalogo/catalogoXls",
            function (data, text, requestXHR) {
                console.log(data);
            },
            "text"
        );

        return false;
    });
})*/


const $ = str => document.getElementById(str);
const tabla = $('contenedor');
const body = document.querySelector("#contenedor tbody");
const txtBienes = $('codigo');

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


txtBienes.addEventListener("keypress", e =>{
    try {
        if(e.which == 13 && e.target.value.length < 1) throw "No se puede realizar la consulta";

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
    } catch (error) {
        console.log(error);
    }
})


