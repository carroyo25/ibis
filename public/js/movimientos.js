$(function () {
    $("#esperar").css({"display":"none","opacity":"0"});

    $("#btnConsulta").click(function (e) { 
        e.preventDefault();

        let str = $("#formConsulta").serialize();
        
        try {
            if ( $("#costosSearch").val() == -1) throw "Por favor elija un centro de costos para la consulta";

            $("#esperar").css("opacity","1").fadeIn();
            
            $.post(RUTA+"movimientos/consulta",str,
                function (data, textStatus, jqXHR) {
                    data.datos.forEach(element => {
                       
                        const tr = document.createElement('tr');
                        tr.dataset.idprod = element.id_cprod;
                        tr.dataset.costos = element.ingresos;
                        tr.dataset.existencia = element.idreg;
                        tr.dataset.transferencia = element.iditem;
                        tr.classList.add("pointer");
                        

                        let saldo = ( element['ingresos']+element['inventarios']+element['devoluciones'] )
                                        - (element['consumos']+element['salidas_transferencia'] ) + element['ajustes'];

                        let c1 = (element['condicion'] == '1A' || element['condicion'] == '1.A.' || element['condicion'] == '1.A') ? element['inventarios'] : "";
                        let c2 = (element['condicion'] == '1B' || element['condicion'] == '1.B.' || element['condicion'] == '1.B') ? element['inventarios'] : "";
                        let c3 = (element['condicion'] == '2A' || element['condicion'] == '2.A.' || element['condicion'] == '2.A') ? element['inventarios'] : "";
                        let c4 = (element['condicion'] == '2B' || element['condicion'] == '2.B.' || element['condicion'] == '2.B') ? element['inventarios'] : "";
                        let c5 = (element['condicion'] == '3A' || element['condicion'] == '3.A.' || element['condicion'] == '3.A') ? element['inventarios'] : "";
                        let c6 = (element['condicion'] == '3B' || element['condicion'] == '3.B.' || element['condicion'] == '3.B') ? element['inventarios'] : "";
                        let c7 = (element['condicion'] == '3C' || element['condicion'] == '3.C.' || element['condicion'] == '3.C') ? element['inventarios'] : "";

                        tr.innerHTML=`<td class="textoCentro"><input type="checkbox" name="item"></td>
                                            <td class="textoCentro">${element.ccodprod}</td>
                                            <td class="pl20px">${element.cdesprod}</td>
                                            <td class="textoCentro"></td>
                                            <td class="textoCentro"></td>
                                            <td class="textoCentro"><input type="number" name="cantidad"></td>
                                            <td class="textoCentro"></td>
                                            <td class="textoCentro"></td>
                                            <td class="textoCentro"></td>
                                            <td class="textoCentro"></td>
                                            <td class="textoCentro"></td>
                                            <td class="textoCentro"></td>
                                            <td class="textoCentro"></td>`;

                        document.getElementById("cuerpoTabla").appendChild(tr);
                    });

                    $("#esperar").css("opacity","0").fadeOut();
                },
                "json"
            );
        } catch (error) {
            mostrarMensaje(error,"mensaje_error");
        }
        

        return false;
    });
})