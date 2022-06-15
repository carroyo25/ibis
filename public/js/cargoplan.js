$(function(){

    $("#esperar").fadeOut();


    $("#tablaPrincipal tbody").on("click","tr", function (e) {
        e.preventDefault();

        let porcentaje_ingreso = 0,
            porcentaje_despacho = 0;

        $.post(RUTA+"cargoplan/consultaItem", { codigo:$(this).data("producto"),
                                                pedido:$(this).data("pedido"),
                                                orden:$(this).data("orden"),
                                                ingreso:$(this).data("ingreso"),
                                                despacho:$(this).data("despacho"),
                                                item:$(this).data("item"),
                                                status:$(this).data("status")},
            function (data, textStatus, jqXHR) {

                let estado = "textoCentro "+ data.producto[0].estado;

                $("#codigo").val(data.producto[0].ccodprod);
                $("#producto").val(data.producto[0].producto);
                $("#unidad").val(data.producto[0].cabrevia);
                $("#cantidad").val(data.producto[0].cantidad);
                $("#estado").val(data.producto[0].cdescripcion);
                $("#nropedido").val(data.pedido[0].pedido);
                $("#tipo_pedido").val(data.pedido[0].tipo);
                $("#emision_pedido").val(data.pedido[0].emision);

                if (data.orden.length > 0) {
                    $("#aprobacion_pedido").val(data.pedido[0].faprueba);
                    $("#aprobado_por").val(data.pedido[0].cnombres);
                    $("#nroorden").val(data.orden[0].cnumero);
                    $("#emision_orden").val(data.orden[0].ffechadoc);
                    $("#aprobacion_logistica").val(data.orden[0].fechaLog);
                    $("#aprobacion_operaciones").val(data.orden[0].fechaOpe);
                    $("#aprobacion_finanzas").val(data.orden[0].FechaFin);
                }
                
                if (data.ingreso.length > 0){
                    $("#ingreso").val(data.ingreso[0].nnronota);
                    $("#fecha_ingreso").val(data.ingreso[0].ffecdoc);
                    $("#ingresada").val(data.ingreso[0].cantidad);

                    let porcentaje_ingreso =  data.ingreso[0].cantidad*100/$("#cantidad").val()+"%";
                    
                    $("#porcentaje_ingresado")
                        .parent().css("width",porcentaje_ingreso)
                        .end()
                        .empty()
                        .text(porcentaje_ingreso);   
                }else{
                    $("#porcentaje_ingresado")
                        .parent().css("width",0)
                        .end()
                        .empty();
                }
            
                if (data.despacho.length > 0){
                    $("#despacho").val(data.despacho[0].despacho);
                    $("#fecha_salida").val(data.despacho[0].ffecdoc);
                    $("#enviada").val(data.despacho[0].cantidad);

                    let porcentaje_despacho =  data.despacho[0].cantidad*100/$("#cantidad").val()+"%";
                    
                    $("#porcentaje_despacho")
                        .parent().css("width",porcentaje_despacho)
                        .end()
                        .empty()
                        .text(porcentaje_despacho);
                }else{
                    $("#porcentaje_despacho")
                        .parent().css("width",0)
                        .end()
                        .empty();
                }
                
                


                $("#estado")
                    .removeClass()
                    .addClass(estado);
                    
                $("#vistadocumento").fadeIn();

                

            },
            "json"
        );
       
        return false;
    });

    $(".tituloDocumento").on("click","a", function (e) {
        e.preventDefault();

        $("form")[0].reset();
        $(this).parent().parent().parent().parent().parent().fadeOut();

        return false;
    });
})