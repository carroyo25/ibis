$(function (){
    const body = document.querySelector("#tablaPrincipal tbody");

    let listItemFinal = null,estoyPidiendo = false,iditempedido = "",fila=0,estadoItem=0,accion = "";

    //LISTA PARA EL SCROLL

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

        const r = await fetch(RUTA+'marcas/listaScroll',{
            method: 'POST',
            body:FD
        });

        const j  = await r.json();
        j[0].pedidos.forEach(i => {
            const tr = document.createElement('tr');
            
            tr.innerHTML = `<td class="textoCentro">${i.nrodoc}</td>
                            <td class="textoCentro">-</td>
                            <td class="textoCentro">${i.emision}</td>
                            <td class="textoCentro">${i.idtipomov}</td>
                            <td class="pl20px">${i.concepto}</td>
                            <td class="pl20px">${i.costos}</td>
                            <td class="pl20px">${i.nombres}</td>
                            <td class="textoCentro ${i.cabrevia.toLowerCase()}">${i.cabrevia}</td>
                            <td class="textoCentro ${i.atencion.toLowerCase()}">${i.atencion}</td>`;
            tr.classList.add("pointer");
            tr.dataset.indice = i.idreg;
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

    ///FIN DEL SCROLL

    $("#tablaPrincipal tbody").on("click","tr", function (e) {
        e.preventDefault();

        $.post(RUTA+"pedidoedit/consultaRqAdmin", {id:$(this).data("indice")},
            function (data, textStatus, jqXHR) {
                
                let numero = $.strPad(data.cabecera[0].nrodoc,6);
                let estado = "textoCentro w35por estado " + data.cabecera[0].cabrevia;
                
                $("#codigo_costos").val(data.cabecera[0].idcostos);
                $("#codigo_area").val(data.cabecera[0].idarea);
                $("#codigo_transporte").val(data.cabecera[0].idtrans);
                $("#codigo_solicitante").val(data.cabecera[0].idsolicita);
                $("#codigo_partida").val(data.cabecera[0].idpartida);
                $("#codigo_tipo").val(data.cabecera[0].idtipomov);
                $("#codigo_pedido").val(data.cabecera[0].idreg);
                $("#codigo_estado").val(data.cabecera[0].estadodoc);
                $("#codigo_verificacion").val(data.cabecera[0].verificacion);
                $("#codigo_atencion").val(data.cabecera[0].nivelAten);
                $("#vista_previa").val(data.cabecera[0].docfPdfPrev);
                $("#numero").val(numero);
                $("#emision").val(data.cabecera[0].emision);
                $("#costos").val(data.cabecera[0].proyecto);
                $("#area").val(data.cabecera[0].area);
                $("#transporte").val(data.cabecera[0].transporte);
                $("#concepto").val(data.cabecera[0].concepto);
                $("#solicitante").val(data.cabecera[0].nombres);
                $("#tipo").val(data.cabecera[0].tipo);
                $("#vence").val(data.cabecera[0].vence);
                $("#estado").val(data.cabecera[0].estado);
                $("#espec_items").val(data.cabecera[0].detalle);
                $("#partida").val(data.cabecera[0].cdescripcion);
                $("#asigna").val(data.cabecera[0].asigna);
                $("#aprueba").val(data.cabecera[0].aprueba);
                $("#fecha_aprobacion").val(data.cabecera[0].faprueba);

                $("#tablaDetalles tbody")
                    .empty()
                    .append(data.detalles);

                $("#estado")
                    .removeClass()
                    .addClass(estado);
                
                grabado = true;
            },
            "json"
        );

        $("#proceso").fadeIn();

        return false;
    });

    $("#tablaDetalles tbody").on("click","a", function (e) {
        e.preventDefault();

        return false;
    });

    $("#closeProcess").click(function (e) { 
        e.preventDefault();

        $("#proceso").fadeOut();
        
        //llama la funcion para recargar la tabla
        query();
        
        return false;
    });

    $("#save").click(function (e) { 
        e.preventDefault();
        
        let result = {};

        $.each($("#formProceso").serializeArray(),function(){
            result[this.name] = this.value;
        });

        try {
            $.post(RUTA+"marcas/marcaAtencion",{cabecera:result,
                                                detalles:JSON.stringify(itemsSave()),
                                                user:$("#id_user").val()},
                function (data, textStatus, jqXHR) {
                    //mostrarMensaje(data.mensaje,data.clase);     
                },
                "json"
            );

        } catch (error) {
            mostrarMensaje(error,'mensaje_error');
        }

        return false;
    });

    $("#btnProceso").click(function (e) { 
        e.preventDefault();
        
        let str = $("#formConsulta").serialize();

        $.post(RUTA+"marcas/filtro",str,
            function (data, textStatus, jqXHR) {
                $("#tablaPrincipal tbody")
                    .empty()
                    .append(data);
            },
            "text"
        );

        return false
    });
})

itemsSave = () =>{
    DATA = [];
    let TABLA = $("#tablaDetalles tbody >tr");

    TABLA.each(function(){

        let item= {};
        
        item['idx']         = $(this).data('idx');
        item['cantidad']    = $(this).find('td').eq(5).children().val();
        item['atendida']    = $(this).find('td').eq(9).children().val();

        DATA.push(item);

    })

    return DATA;
}