$(() =>{

    $("#esperar").fadeOut();
    
    let fila;

    $("#btnConsulta").click(function (e) { 
        e.preventDefault();
        
        let formData = new FormData();
            formData.append("costos",$("#costosSearch").val());
            formData.append("codigo",$("#codigoBusqueda").val());
            formData.append("descripcion",$("#descripcionSearch").val());

        
        fetch(RUTA+'minimos/consultaProductos',{
            method: 'POST',
            body:formData
        })
        .then(response => response.json())
        .then(data => {
            let row ="",item = 1;
            data[0].forEach(element =>{
                row += `<tr class="pointer" 
                            data-indice='${element.idreg}' 
                            data-idproducto='${element.idprod}'
                            data-detpedido='${element.idpedido}'
                            data-costos='${element.idcostos}'>
                            <td class="textoDerecha">${item++}</td>
                            <td class="textoCentro">${element.ccodprod}</td>
                            <td class="pl20px">${element.cdesprod}</td>
                            <td class="textoCentro">${element.cabrevia}</td>
                            <td class="textoDerecha">${element.ingresos}</td>
                            <td></td>
                            <td class="textoDerecha"></td>
                        </tr>`;
            })

            $("#tablaPrincipal tbody").empty().append(row);
        })

        return false; 
    });

    $("#tablaPrincipal tbody").on('dblclick','tr', function (e) {
        e.preventDefault();

        fila = $(this);

        $("#codigoSearch").val($(this).find('td').eq(1).text());
        $("#descripSearch").val($(this).find('td').eq(2).text());

        $("#dialogo_registro").fadeIn();

        return false;
    });

    $("#btnCancelarDialogoMinimo").click(function (e) { 
        e.preventDefault();

        $("#dialogo_registro").fadeOut();
        
        return false;
    });

    $("#btnAceptarDialogoMinimo").click(function (e) { 
        e.preventDefault();

        fila.find('td').eq(6).text( $("#total_minimo").val());
        $("#dialogo_registro").fadeOut();
        
        return false;
    });

    $("#cant_personal,#porcentaje_minimo").keypress(function (e) { 
        if(e.which == 13) {
            let total_minimo  = $("#cant_personal").val()*($("#porcentaje_minimo").val()/100);

            $("#total_minimo").val(total_minimo);
        }
    });
})