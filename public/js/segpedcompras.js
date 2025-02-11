$(function(){
    $("#esperar").fadeOut();

        cargaPrincipal();

        $("#tablaPrincipal tbody").on("click","a", function (e) {
            e.preventDefault();

            $("#cambioestado").fadeIn();

            return false;
        });

        $("#tablaPrincipal tbody").on("click","tr", function (e) {
            e.preventDefault();

        $.post(RUTA+"segpedcompras/consultaId", {id:$(this).data("indice")},
                function (data, textStatus, jqXHR) {
                    
                    let numero = $.strPad(data.cabecera[0].nrodoc,6);
                    let estado = "textoCentro w50por estado " + data.cabecera[0].cabrevia;
                    
                    $("#codigo_costos").val(data.cabecera[0].idcostos);
                    $("#codigo_area").val(data.cabecera[0].idarea);
                    $("#codigo_transporte").val(data.cabecera[0].idtrans);
                    $("#codigo_solicitante").val(data.cabecera[0].idsolicita);
                    $("#codigo_tipo").val(data.cabecera[0].idtipomov);
                    $("#codigo_pedido").val(data.cabecera[0].idreg);
                    $("#codigo_estado").val(data.cabecera[0].estadodoc);
                    $("#codigo_verificacion").val(data.cabecera[0].verificacion);
                    $("#codigo_atencion").val(data.cabecera[0].nivelAten);
                    $("#emitido").val(data.cabecera[0].docPdfEmit);
                    $("#elabora").val(data.cabecera[0].cnombres);
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
                    $("#user_asigna").val(data.cabecera[0].asigna);
                    
                    $("#tablaDetalles tbody")
                        .empty()
                        .append(data.detalles);

                    $("#estado")
                        .removeClass()
                        .addClass(estado);
                },
                "json"
            );

            $("#proceso").fadeIn();

            return false;
        });

        $("#closeProcess").click(function (e) { 
            e.preventDefault();

            $("#proceso").fadeOut();
            
            return false;  
        });
    })

    function cargaPrincipal() {
        formData = new FormData();

        fetch(RUTA+'segpedcompras/consultarPedidos',{
            method: 'POST',
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            $("#tablaPrincipal tbody").empty();

            let opc = `<option value="-1">--</option>`;

            data.opciones.forEach(element => {
                opc += `<option value="${element.nidreg}">${element.cdescripcion}</option>`;
            });

            data.datos.forEach(element =>{
                let tipo = element.idtipomov == 37 ? "B":"S",
                    asignado = element.cnameuser == null ? "--" : element.cnameuser;

                let row = `<tr>
                                <tr class="pointer" data-indice="${element.idreg}" data-compras="${element.estadoCompra}">
                                            <td class="textoCentro">${element.nrodoc}</td>
                                            <td class="textoCentro">${element.emision}</td>
                                            <td class="textoCentro">${tipo}</td>
                                            <td class="pl20px">${element.concepto}</td>
                                            <td class="pl20px">${element.costos}</td>
                                            <td class="pl20px">${element.nombres}</td>
                                            <td class="textoCentro ${element.cabrevia}">${element.estado}</td>
                                            <td class="textoCentro ${element.atencion.tolowercase}">${element.atencion}</td>
                                            <td class="textoCentro">${asignado}</td>
                                            <td class="textoCentro" style="font-size:.6rem"><a href="${element.idreg}">${element.textoEstadoCompra}</a></td>
                                            <td class="textoCentro"><a href="${element.idreg}"><i class="fas fa-exchange-alt"></i></a></td>
                                        </tr>
                            </tr>`;
                
                if (element.itemsFaltantes > 0) {
                    $("#tablaPrincipal tbody").append(row);
                }

                $("#esperar").fadeOut().promise().done(function(){
                    iniciarPaginador();
                });
                
            })
        })
    }
    