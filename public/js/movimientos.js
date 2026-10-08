$(function () {
  $("#esperar").css({ display: "none", opacity: "0" });
   
  const cuerpoTabla = document.getElementById("cuerpoTabla");

  cuerpoTabla.innerHTML= `<tr><td colspan="13" style="text-align: center; color: #94a3b8; padding: 30px;">✨ No hay registros para mostrar, seleccione un centro de costos y precione <b>Consultar</b></td></tr>`;

  $("#btnConsulta").click(function (e) {
    e.preventDefault();

    renderizarTabla();

    return false;
  });

  function renderizarTabla() {
    let str = $("#formConsulta").serialize();

    try {
      if ($("#costosSearch").val() == -1)
        throw "Por favor elija un centro de costos para la consulta";

      $("#esperar").css("opacity", "1").fadeIn();

      $.post(
        RUTA + "movimientos/consulta",
        str,
        function (data, textStatus, jqXHR) {
          
        if(data.datos.length == 0 ){
            cuerpoTabla.innerHTML= `<tr><td colspan="13" style="text-align: center; color: #94a3b8; padding: 30px;">✨ No hay registros para mostrar, seleccione un centro de costos y precione <b>Consultar</b></td></tr>`;
            $("#esperar").css("opacity", "0").fadeOut();
            return
        }

        document.getElementById("cuerpoTabla").innerHTML='';

          data.datos.forEach((element) => {
            const tr = document.createElement("tr");
            tr.dataset.idprod = element.id_cprod;
            tr.dataset.costos = element.ingresos;
            tr.dataset.existencia = element.idreg;
            tr.dataset.transferencia = element.iditem;
            tr.classList.add("pointer");

            let saldo =
              element["ingresos"] +
              element["inventarios"] +
              element["devoluciones"] -
              (element["consumos"] + element["salidas_transferencia"]) +
              element["ajustes"];

            let c1 =
              element["condicion"] == "1A" ||
              element["condicion"] == "1.A." ||
              element["condicion"] == "1.A"
                ? element["inventarios"]
                : "";
            let c2 =
              element["condicion"] == "1B" ||
              element["condicion"] == "1.B." ||
              element["condicion"] == "1.B"
                ? element["inventarios"]
                : "";
            let c3 =
              element["condicion"] == "2A" ||
              element["condicion"] == "2.A." ||
              element["condicion"] == "2.A"
                ? element["inventarios"]
                : "";
            let c4 =
              element["condicion"] == "2B" ||
              element["condicion"] == "2.B." ||
              element["condicion"] == "2.B"
                ? element["inventarios"]
                : "";
            let c5 =
              element["condicion"] == "3A" ||
              element["condicion"] == "3.A." ||
              element["condicion"] == "3.A"
                ? element["inventarios"]
                : "";
            let c6 =
              element["condicion"] == "3B" ||
              element["condicion"] == "3.B." ||
              element["condicion"] == "3.B"
                ? element["inventarios"]
                : "";
            let c7 =
              element["condicion"] == "3C" ||
              element["condicion"] == "3.C." ||
              element["condicion"] == "3.C"
                ? element["inventarios"]
                : "";

            tr.innerHTML = `<td class="textoCentro"><input type="checkbox" name="item"></td>
                                            <td class="textoCentro">${element.ccodprod}</td>
                                            <td class="pl20px">${element.cdesprod}</td>
                                            <td class="textoCentro">${element.cabrevia}</td>
                                            <td class="textoDerecha">${saldo.toFixed(2)}</td>
                                            <td class="textoCentro" width="150px"><input type="number" name="cantidad" style="text-align: right;padding-right: 10px; border:none" placeholder="00.00"></td>
                                            <td class="textoCentro">${c1}</td>
                                            <td class="textoCentro">${c2}</td>
                                            <td class="textoCentro">${c3}</td>
                                            <td class="textoCentro">${c4}</td>
                                            <td class="textoCentro">${c5}</td>
                                            <td class="textoCentro">${c6}</td>
                                            <td class="textoCentro">${c7}</td>`;

            if (saldo > 0) {
              document.getElementById("cuerpoTabla").appendChild(tr);
            }
          });

          $("#esperar").css("opacity", "0").fadeOut();
        },
        "json",
      );
    } catch (error) {
      mostrarMensaje(error, "mensaje_error");
    }
  }
});
