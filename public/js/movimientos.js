$(function () {
  $("#esperar").css({ display: "none", opacity: "0" });

  const cuerpoTabla = document.getElementById("cuerpoTabla");

  cuerpoTabla.innerHTML = `<tr><td colspan="13" style="text-align: center; color: #94a3b8; padding: 30px;">✨ No hay registros para mostrar, seleccione un centro de costos y precione <b>Consultar</b></td></tr>`;

  document.getElementById("fechaTraspaso").value = fechaLocalISO();

  /* =====================================================
   ENTER + VALIDACIÓN + SALTO DE FILA
   ===================================================== */
  document
    .querySelector("#tablaPrincipal tbody")
    .addEventListener("keydown", function (e) {
      // 1. Solo Enter
      if (e.key !== "Enter") return;

      // 2. Solo inputs de cantidad
      const input = e.target;
      if (!input.classList.contains("cantidad-traspaso")) return;

      // 3. Evitar submit del form o cualquier acción por defecto
      e.preventDefault();

      // 4. Leer valores
      const cantidad = parseFloat(input.value) || 0;
      const saldo = parseFloat(input.dataset.saldo) || 0;
      const descprod = input.dataset.descprod || "Producto";

      // =====================================================
      // 5. VALIDACIÓN — Si supera el saldo, NO cambia de fila
      // =====================================================
      if (cantidad > saldo) {
        // Marcar en rojo
        input.style.border = "2px solid red";
        input.style.background = "#ffe6e6";

        // Marcar la fila
        input.closest("tr").classList.add("fila-error");

        // Mensaje
        mostrarMensaje(`⚠️ "Saldo no disponible`, "mensaje_error");

        // 👇 NO cambia de fila, se queda en el mismo input
        input.focus();
        input.select();
        return;
      }

      // =====================================================
      // 6. Si es válido, limpiar marcas de error
      // =====================================================
      input.style.border = "";
      input.style.background = "";
      input.closest("tr").classList.remove("fila-error");

      // =====================================================
      // 7. Pasar a la siguiente fila
      // =====================================================
      const inputs = [...document.querySelectorAll(".cantidad-traspaso")];
      const index = inputs.indexOf(input);

      if (index >= 0 && index < inputs.length - 1) {
        const siguiente = inputs[index + 1];
        siguiente.focus();
        siguiente.select();
      } else {
        // Es el último: puedes abrir el modal automáticamente (opcional)
        input.blur();
        // abrirModalTraspaso();   // 👈 descomenta si quieres abrir el modal al final
      }
    });

  document
    .getElementById("btnConfirmarTraspaso")
    .addEventListener("click", function () {
      const autorizado = document
        .getElementById("id_user")
        .value.trim();
      const responsable = document
        .getElementById("id_user")
        .value.trim();
      const ccDestino = document.getElementById("ccDestinoTraspaso").value;
      const ccOrigen = window.ccOrigenActual;

      // Validaciones
      if (!autorizado) {
        mostrarMensaje("Debe ingresar quién autoriza.", "mensaje_error");
        return;
      }
      if (!responsable) {
        mostrarMensaje("Debe ingresar el responsable.", "mensaje_error");
        return;
      }
      if (!ccDestino) {
        mostrarMensaje("Debe seleccionar el CC Destino.");
        return;
      }
      if (ccDestino === ccOrigen) {
        mostrarMensaje("El CC Origen y Destino no pueden ser iguales.", "mensaje_error");
        return;
      }

      // 👇 Payload completo para el backend
      const payload = {
        fecha: document.getElementById("fechaTraspaso").value,
        autorizado: autorizado,
        responsable: responsable,
        ccOrigen: ccOrigen,
        ccDestino: ccDestino,
        items: window.itemsTraspaso, // 👈 array de ítems con cantidad > 0
      };

      console.log("Payload a enviar:", payload);

      // 👇 Enviar al backend
      fetch(RUTA+"movimientos/transfiere", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload),
      })
        .then((res) => res.json())
        .then((data) => {
          if (data.success) {
            mostrarMensaje("✅ Traspaso registrado correctamente.", "mensaje_correcto");
            document.getElementById("modalTraspaso").classList.remove("active");
            console.log(data.idtransfer);
          } else {
            mostrarMensaje("❌ Error: " + (data.error || "No se pudo grabar.", "mensaje_error"));
          }
        })
        .catch((err) => {
          console.error(err);
          mostrarMensaje("Error de conexión al grabar el traspaso.", "mensaje_error");
        });
    });

  $("#btnConsulta").click(function (e) {
    e.preventDefault();

    renderizarTabla();

    return false;
  });

  $("#transferItems").click(function (e) {
    e.preventDefault();

    abrirModalTraspaso();

    return false;
  });

  $("#closeDialog,#closeDialogButton").click(function (e) {
    e.preventDefault();

    $("#movimientos").fadeOut();
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
          if (data.datos.length == 0) {
            cuerpoTabla.innerHTML = `<tr><td colspan="13" style="text-align: center; color: #94a3b8; padding: 30px;">✨ No hay registros para mostrar, seleccione un centro de costos y precione <b>Consultar</b></td></tr>`;
            $("#esperar").css("opacity", "0").fadeOut();
            return;
          }

          document.getElementById("cuerpoTabla").innerHTML = "";

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
                                            <td class="textoCentro" width="150px">
                                              <input type="number"
                                                name="cantidad"
                                                class="cantidad-traspaso"
                                                data-idprod="${element.id_cprod}"
                                                data-descprod="${element.cdesprod}"
                                                data-saldo="${saldo}"
                                                min="0"
                                                max="${saldo}"
                                                step="1"
                                                style="text-align: right; padding-right: 10px; border: none"
                                                placeholder="00.00">
                                            </td>
                                            <td>
                                              <input type="text" 
                                              name="observaciones" 
                                              class="observacion-item"
                                              data-idprod="${element.id_cprod}">
                                            </td>
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

  function fechaLocalISO() {
    const d = new Date();
    const year = d.getFullYear();
    const month = String(d.getMonth() + 1).padStart(2, "0"); // mes empieza en 0
    const day = String(d.getDate()).padStart(2, "0");
    return `${year}-${month}-${day}`;
  }

  /* =====================================================
   CERRAR MODAL
   ===================================================== */
  function cerrarModalTraspaso() {
    $("#movimientos").fadeOut();
  }

  /* =====================================================
   RECOLECTAR ÍTEMS CON CANTIDAD > 0
   ===================================================== */
  function obtenerItemsParaBackend() {
    const items = []; // ítems válidos para enviar
    const errores = []; // ítems que superan el saldo

    document.querySelectorAll(".cantidad-traspaso").forEach((input) => {
      const cantidad = parseFloat(input.value) || 0;

      // 👇 Solo los que tienen cantidad > 0
      if (cantidad <= 0) return;

      const idprod = input.dataset.idprod;
      const descprod = input.dataset.descprod;
      const saldo = parseFloat(input.dataset.saldo) || 0;

      // 👇 Buscar la observación de la MISMA fila
      const tr          = input.closest('tr');
      const inputObs    = tr.querySelector('.observacion-item');
      const observacion = inputObs ? inputObs.value.trim() : '';

      // 👇 Validación: no puede superar el saldo
      if (cantidad > saldo) {
        errores.push({
          idprod: idprod,
          descprod: descprod,
          cantidad: cantidad,
          saldo: saldo,
          observacion:  observacion   // 👈 nuevo campo
        });
        return;
      }

      // 👇 Ítem válido
      items.push({
        idprod: idprod,
        descprod: descprod,
        cantidad: cantidad,
        saldo: saldo,
        observacion: observacion    // 👈 ESTA LÍNEA ES LA QUE FALTA
      });
    });

    return { items, errores };
  }

  function abrirModalTraspaso() {
    const ccOrigen = $("#costosSearch").val();
    if (!ccOrigen) {
      mostrarMensaje(
        "Debe seleccionar un Centro de Costos Origen.",
        "mensaje_error",
      );
      return;
    }

    // 👇 Recolectar ítems
    const { items, errores } = obtenerItemsParaBackend();

    // 👇 Si hay errores, avisar y no continuar
    if (errores.length > 0) {
      let mensaje = "⚠️ Los siguientes productos superan el saldo:\n\n";
      errores.forEach((e) => {
        mensaje += `• ${e.descprod}\n   Cantidad: ${e.cantidad} | Saldo: ${e.saldo}\n\n`;
      });
      mostrarMensaje(mensaje, "mensaje_error");
      return;
    }

    // 👇 Si no hay ítems seleccionados
    if (items.length === 0) {
      mostrarMensaje("Debe ingresar al menos un ítem con cantidad mayor a cero.", "mensaje_error");
      return;
    }

    // 👇 Guardar y abrir el modal
    window.itemsTraspaso = items;
    window.ccOrigenActual = ccOrigen;

    // Llenar el modal
    document.getElementById("fechaTraspaso").value = fechaLocalISO();
    document.getElementById("ccOrigenTraspaso").value = $(
      "#costosSearch option:selected",
    ).text();
    document.getElementById("itemsTraspaso").value = items.length;

    $("#autorizadoTraspaso,#responsableTraspaso").val($("#name_user").val());

    // Mostrar modal
    $("#movimientos").fadeIn();
  }
});
