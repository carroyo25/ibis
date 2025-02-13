$(function(){
        let pedido = "",
            estadoTexto = "";

        $("#esperar").css({"display":"block","opacity":"1"});

        cargaPrincipal($("#numeroSearch").val(),$("#costosSearch").val(),$("#mesSearch").val(),$("#anioSearch").val());

        $("#tablaPrincipal tbody").on("click","a", function (e) {
            e.preventDefault();

            pedido = $(this).attr("href");

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

        $("#operadores").on("click","a", function (e) {
            e.preventDefault();
    
            $("#operadores *").removeClass("itemSeleccionado");
            $(this).addClass("itemSeleccionado");
            $("#estadoCompra").val($(this).attr("href"));

            estadoTexto = $(this).text();
            
    
            return false;
        });

        $("#cancelaEstado").click(function (e) { 
            e.preventDefault();
    
            $("#cambioestado").fadeOut();
            
            return false;
        });
    
        $("#aceptaEstado").click(function (e) { 
            e.preventDefault();
    
            try {
                let formData = new FormData();
                    formData.append("id",pedido);
                    formData.append("estado",$("#estadoCompra").val());
                    formData.append("comentario",$("#comentarioEstado").val());
                    formData.append("user",$("#id_user").val());


                if ($("#estadoCompra").val() =="" ) throw "Seleccione el estado para asignar al pedido";

                fetch(RUTA+"segpedcompras/estadocompra",{
                    method: "POST",
                    body:formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.respuesta){
                        mostrarMensaje(data.mensaje,"mensaje_correcto");
            
                        $('#'+pedido+' td:first').parent().find('td').eq('8').children()
                            .text(estadoTexto)
                            .attr("data-title",$("#comentarioEstado").val());

                    }else{
                        mostrarMensaje(data.mensaje,"mensaje_error");
                    }

                    $("#cambioestado").fadeOut();
                    
                })
            } catch (error) {
                mostrarMensaje(error,"mensaje_error")
            }
            
            return false;
        });

        $("#btnConsulta").click((e) => {
            e.preventDefault();

            cargaPrincipal($("#numeroSearch").val(),$("#costosSearch").val(),$("#mesSearch").val(),$("#anioSearch").val());

            return false;
        })

        $("reportExport").click((e) => {
            e.preventDefault();
            return false;
        })
    })

    function cargaPrincipal(pedido,costos,mes,anio) {
        formData = new FormData();
        formData.append("pedido",pedido);
        formData.append("costos",costos);
        formData.append("mes",mes);
        formData.append("anio",anio);

        $("#esperar").css({"display":"block","opacity":"1"});

        fetch(RUTA+'segpedcompras/consultarPedidos',{
            method: 'POST',
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            $("#tablaPrincipal tbody").empty();

            data.datos.forEach(element =>{
                let tipo = element.idtipomov == 37 ? "B":"S",
                    asignado = element.cnameuser == null ? "--" : element.cnameuser,
                    comentario = element.comentariocompra == null ? "--": element.comentariocompra;

                let row = `<tr>
                                <tr class="pointer" data-indice="${element.idreg}" data-compras="${element.estadoCompra}" id="${element.idreg}">
                                            <td class="textoCentro">${element.nrodoc}</td>
                                            <td class="textoCentro">${element.emision}</td>
                                            <td class="textoCentro">${tipo}</td>
                                            <td class="pl20px">${element.concepto}</td>
                                            <td class="pl20px">${element.costos}</td>
                                            <td class="pl20px">${element.nombres}</td>
                                            <td class="textoCentro ${element.cabrevia}">${element.estado}</td>
                                            <td class="textoCentro">${asignado}</td>
                                            <td class="textoCentro" style="font-size:.6rem">
                                                <a href="${element.idreg}" data-title="${comentario}" class="bocadillo">${element.textoEstadoCompra}</a>
                                            </td>
                                            <td class="textoCentro">
                                                <a href="${element.idreg}">
                                                    <i class="fas fa-exchange-alt"></i>
                                                </a>
                                            </td>
                                        </tr>
                            </tr>`;
                
                if (element.itemsFaltantes > 0) {
                    $("#tablaPrincipal tbody").append(row);
                }
            })

            $("#esperar").fadeOut().promise().done(function(){
                iniciarPaginador();
            });

            $("#esperar").css({"display":"none","opacity":"0"});
        })
    }
    