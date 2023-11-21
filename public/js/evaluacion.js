$(function(){
    let evaluar = false;

    $("#esperar").fadeOut();

    const body = document.querySelector("#tablaPrincipal tbody");

    let listItemFinal = null,estoyPidiendo = false;

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

        const r = await fetch(RUTA+'evaluacion/listaScroll',{
            method: 'POST',
            body:FD
        });

        let item = 0;

        const j  = await r.json();
        j[0].filas.forEach(i => {
            const tr = document.createElement('tr');
            
            tr.innerHTML = `<td class="textoCentro">${i.cnumero}</td>
                            <td class="textoCentro">${i.emision}</td>
                            <td class="pl20px">${i.concepto}</td>
                            <td class="pl20px">${i.ccodproy}</td>
                            <td class="pl20px">${i.area}</td>
                            <td class="pl20px">${i.proveedor}</td>`;

            tr.classList.add("pointer");
            tr.dataset.indice = i.id_regmov;
            tr.dataset.tipo = i.ntipmov;
            tr.dataset.rol = i.nrol;

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

    $("#tablaPrincipal tbody").on("click","tr", function (e) {
        e.preventDefault();

       $.post(RUTA+"evaluacion/criterios", {id:$(this).data("indice"),tipo:$(this).data("tipo"),rol:$('#rol_user').val()},
        function (data, textStatus, jqXHR) {
            $("#codigo_orden").val(data.cabecera[0].id_regmov);
            $("#codigo_rol").val(data.cabecera[0].nrol);
            $("#codigo_entidad").val(data.cabecera[0].id_centi);
            $("#tipo_orden").val(data.cabecera[0].idregmov);
            $("#numero").val(data.cabecera[0].cnumero);
            $("#emision").val(data.cabecera[0].ffechadoc);
            $("#costos").val(data.cabecera[0].proyecto);
            $("#detalle").val(data.cabecera[0].concepto);
            $("#entidad").val(data.cabecera[0].entidad);

            evaluar = data.evaluada;

            $("#tablaDetalles tbody")
                .empty()
                .append(data.criterios);
            
            let totalOrden = sumarTotales($("#tablaDetalles tbody tr"));
            
            $("#puntaje").val(totalOrden.toFixed(0));    

            $("#proceso").fadeIn();

            $(".filtro").hide();
        },
        "json"
       );
    
        return false;
    });

    $("#saveOrden").click(function (e) { 
        e.preventDefault();
        
        try {
            if (evaluar) throw "Ya registro la evaluación";
            if (checkCantTablesMinMax($("#tablaDetalles tbody > tr"),2)) throw "El puntaje debe estar entre 1 y 5";

            $.post(RUTA+"evaluacion/evaluar",{items:JSON.stringify(items())},
                function (data, textStatus, jqXHR) {
                    mostrarMensaje(data.mensaje,data.clase);
                    $("#cerrarVentana").trigger("click");
                },
                "json"
            );

        } catch (error) {
            mostrarMensaje(error,'mensaje_error');
        }
        
        return false;
    });

    $("#cerrarVentana").click(function (e) { 
        e.preventDefault();
        
        $(".filtro").hide();
        $("#proceso").fadeOut();
        
        return false;
    });

    $("#btnConsulta").click(function (e) { 
        e.preventDefault();

        let str = $("#formConsulta").serialize();

        $.post(RUTA+"evaluacion/listaFiltrada",str,
            function (data, textStatus, jqXHR) {
                $("#tablaPrincipal tbody")
                    .empty()
                    .append(data);
            },
            "text"
        );
        
        return false;
    });

    $("#tablaDetalles tbody").on('blur','input', function(e) {
        if ( $(this).val() < 1 || $(this).val() > 5  ) {
            mostrarMensaje("Valor de calificacion incorrecto...","mensaje_error");
            $(this).val(5);
        }
    });

    $(".cabezaModulo,.barraTrabajo").on('click','*', function() {
        $(".filtro").fadeOut();
    });
})

items = () =>{
    DATA = [];
    let TABLA = $("#tablaDetalles tbody >tr");

    TABLA.each(function(){
        let REG         = $(this).data("reg"),
            TIPO        = $(this).data("tipo"),
            PUNTAJE     = $(this).find('td').eq(2).children().val(),
            PESO        = $(this).data("peso"),
            ENTIDAD     = $("#codigo_entidad").val(),
            ORDEN       = $("#codigo_orden").val(),
            USUARIO     = $("#id_user").val(),
            ROL         = $("#codigo_rol").val()

        item= {};
        
        item['reg']        = REG;
        item['tipo']       = TIPO;
        item['puntaje']    = PUNTAJE;
        item['peso']       = PESO;
        item['entidad']    = ENTIDAD;
        item['orden']      = ORDEN;
        item['usuario']    = USUARIO;
        item['rol']        = ROL;   
    
        DATA.push(item);
       
    })

    return DATA;
}