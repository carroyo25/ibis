$(function () {
  $("#esperar").css({ display: "none", opacity: "0" });

  const cuerpoTabla = document.getElementById("cuerpoTabla");

  let swGrabado = false,idTransfer = 0;

  cuerpoTabla.innerHTML = `<tr><td colspan="13" style="text-align: center; color: #94a3b8; padding: 30px;">✨ No hay registros para mostrar, seleccione un centro de costos y precione <b>Consultar</b></td></tr>`;

  document.getElementById("fechaTraspaso").value = fechaLocalISO();

 /* ============================================================
   MAPA DE RELACIONES: inputId → campos que rellena
   ============================================================ */
  const GR_RELACIONES = {
      'grEmpresaTransporte': {
          direccion: 'grDireccionEmpresa',
          ruc:       'grRucEmpresa'
      },
      'grAlmacenOrigen': {
          direccion: 'grDireccionOrigen'
      },
      'grAlmacenDestino': {
          direccion: 'grDireccionDestino'
      },
      'grRucDestinatario': {
          direccion: 'grDireccionDest',
          ruc:       'grRucDestinatario'
      },
      'grConductor': {
          licencia: 'grLicencia'
      },
      'grMarca': {
          placa: 'grPlaca'
      }
  };

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
        mostrarMensaje("⚠️ Saldo no disponible", "mensaje_error");

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
      const autorizado = document.getElementById("id_user").value.trim();
      const responsable = document.getElementById("id_user").value.trim();
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
        mostrarMensaje(
          "El CC Origen y Destino no pueden ser iguales.",
          "mensaje_error",
        );
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

      //console.log("Payload a enviar:", payload);

      // 👇 Enviar al backend
      fetch(RUTA + "movimientos/transfiere", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload),
      })
        .then((res) => res.json())
        .then((data) => {
          if (data.success) {
            mostrarMensaje(
              "✅ Traspaso registrado correctamente.",
              "mensaje_correcto",
            );
            document.getElementById("modalTraspaso").classList.remove("active");
            //console.log(data.idtransfer);
            swGrabado = true;
            idTransfer = data.idtransfer;
          } else {
            mostrarMensaje(
              "❌ Error: " +
                (data.error || "No se pudo grabar.", "mensaje_error"),
            );
            swGrabado = true;
            idTransfer = data.idtransfer;
          }
        })
        .catch((err) => {
          //console.error(err);
          mostrarMensaje(
            "Error de conexión al grabar el traspaso.",
            "mensaje_error",
          );
        });
    });

  $("#btnConsulta").click(function (e) {
    e.preventDefault();

    renderizarTabla();

    return false;
  });

  $("#btnDocument").click(function (e) {
    e.preventDefault();

    if(idTransfer == 0){
      mostrarMensaje('Primero debe grabar la transferencia','mensaje_error');
      return 
    }

    // Nombre + dirección
    buscarAlmacen($("#costosSearch").val(), {
      nombre: "#grAlmacenOrigen",
      direccion: "#grDireccionOrigen",
    });

    // Nombre + dirección + RUC (si el backend devuelve RUC)
    buscarAlmacen($("#ccDestinoTraspaso").val(), {
      nombre: "#grAlmacenDestino",
      direccion: "#grDireccionDestino",
    });

    $("#grFechaEmision").val(fechaLocalISO());

  
    $("#vistaDocumento").fadeIn();

    return false;
  });

  $("#grCerrarModal,#grBtnCerrarModal").click(function (e) {
    e.preventDefault();

    $("#vistaDocumento").fadeOut();
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

  $("#grEmitirGuia").click(function(e){
    e.preventDefault();

    grEmitirGuia()

    return false;
  })

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
                                            <td class="textoDerecha">${element.saldo.toFixed(2)}</td>
                                            <td class="textoCentro" width="150px">
                                              <input type="number"
                                                name="cantidad"
                                                class="cantidad-traspaso"
                                                data-idprod="${element.id_cprod}"
                                                data-codigo="${element.ccodprod}"
                                                data-descprod="${element.cdesprod}"
                                                data-saldo="${element.saldo}"
                                                data-unidad="${element.cabrevia}"
                                                min="0"
                                                max="${element.saldo}"
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

            if (element.saldo > 0) {
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
        const codigo = input.dataset.codigo;       // 👈 CAPTURADO
        const unidad = input.dataset.unidad;
        const descprod = input.dataset.descprod;
        const saldo = parseFloat(input.dataset.saldo) || 0;
        

        // 👇 Buscar la observación de la MISMA fila
        const tr = input.closest("tr");
        const inputObs = tr.querySelector(".observacion-item");
        const observacion = inputObs ? inputObs.value.trim() : "";

        // 👇 Validación: no puede superar el saldo
        if (cantidad > saldo) {
          errores.push({
            idprod: idprod,
            descprod: descprod,
            cantidad: cantidad,
            saldo: saldo,
            observacion: observacion, // 👈 nuevo campo
          });
          return;
        }

        // 👇 Ítem válido
        items.push({
          idprod: idprod,
          descprod: descprod,
          codigo: codigo,         // 👈 ENVIADO AL BACKEND
          unidad: unidad,         // 👈 ENVIADO AL BACKEND
          cantidad: cantidad,
          saldo: saldo,
          observacion: observacion, // 👈 ESTA LÍNEA ES LA QUE FALTA
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
        mostrarMensaje(
          "Debe ingresar al menos un ítem con cantidad mayor a cero.",
          "mensaje_error",
        );
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

    /* ============================================================
    BUSCAR ALMACÉN POR ID
    - selectores: objeto con { campoJSON: "#selectorInput" }
    ============================================================ */
    async function buscarAlmacen(id, selectores = {}) {
      if (!id) return null;

      try {
        const formData = new FormData();
        formData.append("id", id);

          const response = await fetch(RUTA + "movimientos/almacen", {
            method: "POST",
            body: formData,
          });

          const json = await response.json();

          // 👇 El backend devuelve { success, datos }
          if (!json.success || !json.datos) {
            console.warn("No se encontró el almacén con id:", id);
            return null;
          }

          const data = json.datos;

          // Escribir cada campo en su input
          Object.keys(selectores).forEach((campo) => {
            const selector = selectores[campo];
            if (selector && data[campo] !== undefined) {
              $(selector).val(data[campo] || "");
            }
          });

          return data;
        } catch (error) {
          console.error("Error al buscar almacén:", error);
          return null;
        }
    }

    /* ============================================================
    PANELES DE BÚSQUEDA
    ============================================================ */
    function grCerrarTodosLosPaneles() {
      document.querySelectorAll(".gr-panel-search").forEach((p) => p.remove());
      document
        .querySelectorAll(".gr-btn-search.gr-abierto")
        .forEach((b) => b.classList.remove("gr-abierto"));
    }

    /* ============================================================
    CARGAR DATOS DEL BACKEND (sin caché)
    Backend: { success: true, datos: [ {...}, {...} ] }
    ============================================================ */
    async function grCargarDatos(tipo) {
        try {
            const formData = new FormData();
            formData.append("tipo", tipo);

            const response = await fetch(RUTA + 'movimientos/buscar', {
                method: 'POST',
                body: formData
            });

            const json = await response.json();

            // Acepta varios formatos
            if (Array.isArray(json)) {
                return json;
            }

            if (json.success && Array.isArray(json.datos)) {
                return json.datos;
            }

            if (Array.isArray(json.datos)) {
                return json.datos;
            }

            console.warn("Formato no reconocido:", tipo, json);
            return [];

        } catch (error) {
            console.error("Error al cargar datos de tipo " + tipo + ":", error);
            return [];
        }
    }

    /* ============================================================
    PANELES DE BÚSQUEDA
    ============================================================ */
    function grCerrarTodosLosPaneles() {
        document.querySelectorAll('.gr-panel-search').forEach(p => p.remove());
        document.querySelectorAll('.gr-btn-search.gr-abierto').forEach(b => b.classList.remove('gr-abierto'));
    }

    async function grAbrirPanel(boton) {
        const panelId  = boton.dataset.panel;
        const inputId  = boton.dataset.target;
        const tipo     = boton.dataset.tipo;
        const fieldRow = boton.closest('.gr-field-row');

        // Si ya está abierto, cerrar
        if (boton.classList.contains('gr-abierto')) {
            grCerrarTodosLosPaneles();
            return;
        }

        grCerrarTodosLosPaneles();
        boton.classList.add('gr-abierto');

        // Crear panel con "Cargando..."
        const panel = document.createElement('div');
        panel.className = 'gr-panel-search';
        panel.id = panelId;

        panel.innerHTML = `
            <div class="gr-panel-search-buscar">
                <input type="text" placeholder="🔍 Buscar..." autocomplete="off">
            </div>
            <div class="gr-panel-search-lista">
                <div class="gr-panel-search-vacio">Cargando...</div>
            </div>
        `;

        fieldRow.appendChild(panel);

        // Focus al buscador
        setTimeout(() => panel.querySelector('input').focus(), 50);

        // 👇 Traer datos del backend CADA VEZ
        const datos = await grCargarDatos(tipo);

        const lista = panel.querySelector('.gr-panel-search-lista');
        grRenderizarLista(lista, datos, inputId, boton);

        // Filtro en vivo (sobre los datos ya cargados)
        panel.querySelector('input').addEventListener('input', function() {
            const q = this.value.toLowerCase().trim();
            const filtrados = datos.filter(d =>
                (d.nombre && d.nombre.toLowerCase().includes(q)) ||
                (d.codigo && d.codigo.toLowerCase().includes(q))
            );
            grRenderizarLista(lista, filtrados, inputId, boton);
        });

        // Cerrar con ESC
        panel.querySelector('input').addEventListener('keydown', function(e) {
            if (e.key === 'Escape') grCerrarTodosLosPaneles();
        });
    }

    function grRenderizarLista(contenedor, datos, inputId, boton) {
        contenedor.innerHTML = '';

        if (datos.length === 0) {
            contenedor.innerHTML = '<div class="gr-panel-search-vacio">Sin resultados</div>';
            return;
        }

        datos.forEach(d => {
            const item = document.createElement('div');
            item.className = 'gr-panel-search-item';

            const codigo = d.codigo || d.id || '';
            const nombre = d.nombre || d.descripcion || '';

            item.innerHTML = `
                <span class="gr-codigo">${codigo}</span>
                <span class="gr-nombre">${nombre}</span>
            `;

            item.addEventListener('click', () => grSeleccionarOpcion(d, inputId, boton));
            contenedor.appendChild(item);
        });
    }

    function grSeleccionarOpcion(dato, inputId, boton) {
        const input = document.getElementById(inputId);

        const nombre = dato.nombre || dato.descripcion || '';

        if (input) input.value = nombre;

        // Rellenar campos relacionados
        grRellenarCamposRelacionados(dato, inputId);

        grCerrarTodosLosPaneles();
    }


  /* ============================================================
    RELLENAR CAMPOS RELACIONADOS
    ============================================================ */
  function grRellenarCamposRelacionados(dato, inputId) {
      const relaciones = GR_RELACIONES[inputId];

      if (!relaciones) return;

      Object.keys(relaciones).forEach(campo => {
          const idDestino = relaciones[campo];
          const valor     = dato[campo];

          if (idDestino && valor !== undefined) {
              const input = document.getElementById(idDestino);
              if (input) input.value = valor;
          }
      });
  }


  /* ============================================================
    EVENTOS DE PANELES
    ============================================================ */
  document.querySelectorAll('.gr-btn-search').forEach(btn => {
      btn.addEventListener('click', function(e) {
          e.stopPropagation();
          grAbrirPanel(this);
      });
  });

  document.addEventListener('click', function(e) {
      if (!e.target.closest('.gr-field-row') && !e.target.closest('.gr-btn-search')) {
          grCerrarTodosLosPaneles();
      }
  });

  document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
          grCerrarTodosLosPaneles();
          grCerrarModal();
      }
  });


  /* ============================================================
   EMITIR GUÍA — Recolecta datos y envía al backend
   ============================================================ */
  async function grEmitirGuia() {

      // ============ 1. RECOLECTAR CABECERA ============
      const cabecera = {
          tipoGuia:       document.getElementById('grTipoGuia').value,
          fechaEmision:   document.getElementById('grFechaEmision').value,
          fechaTraslado:  document.getElementById('grFechaTraslado').value,

          rucDestinatario: document.getElementById('grRucDestinatario').value,
          razonSocialDest: document.getElementById('grRazonSocialDest').value,
          direccionDest:   document.getElementById('grDireccionDest').value,

          almacenOrigen:   document.getElementById('grAlmacenOrigen').value,
          direccionOrigen: document.getElementById('grDireccionOrigen').value,
          almacenDestino:  document.getElementById('grAlmacenDestino').value,
          direccionDestino:document.getElementById('grDireccionDestino').value,

          empresaTransporte: document.getElementById('grEmpresaTransporte').value,
          direccionEmpresa:  document.getElementById('grDireccionEmpresa').value,
          rucEmpresa:        document.getElementById('grRucEmpresa').value,

          modalidad:    document.getElementById('grModalidad').value,
          tipoEnvio:    document.getElementById('grTipoEnvio').value,
          transporte:    document.getElementById('grTransporte').value,
          autoriza:     document.getElementById('grAutoriza').value,
          destinatario: document.getElementById('grDestinatario').value,
          observaciones:document.getElementById('grObservaciones').value,

          conductor: document.getElementById('grConductor').value,
          licencia:  document.getElementById('grLicencia').value,
          placa:     document.getElementById('grMarca').value,

          peso:   document.getElementById('grPeso')?.value   || 0,
          bultos: document.getElementById('grBultos')?.value || 0,

          proyecto: document.getElementById('ccOrigenTraspaso').value
      };

      // ============ 2. RECOLECTAR DETALLES (tu función) ============
      const { items, errores } = obtenerItemsParaBackend();

      // ============ 3. VALIDAR ============
      if (errores.length > 0) {
          let mensaje = '⚠️ Los siguientes productos superan el saldo disponible:\n\n';
          errores.forEach(e => {
              mensaje += `• ${e.descprod}\n   Solicitado: ${e.cantidad} | Saldo: ${e.saldo}\n\n`;
          });
          mostrarMensaje(mensaje,'mensaje_error');
          return;
      }

      if (items.length === 0) {
          mostrarMensaje('⚠️ Debe ingresar al menos un producto con cantidad mayor a cero.','mensaje_error');
          return;
      }

      const faltantes = [];
      if (!cabecera.fechaEmision)   faltantes.push('Fecha de Emisión');
      if (!cabecera.fechaTraslado)  faltantes.push('Fecha de Traslado');
      if (!cabecera.almacenOrigen)  faltantes.push('Almacén Origen');
      if (!cabecera.almacenDestino) faltantes.push('Almacén Destino');
      //if (!cabecera.conductor)      faltantes.push('Conductor');
      //if (!cabecera.placa)          faltantes.push('Placa del vehículo');

      if (faltantes.length > 0) {
          mostrarMensaje('⚠️ Complete los siguientes campos:\n\n• ' + faltantes.join('\n• '),'mensaje_error');
          return;
      }

      /*if (!confirm(`¿Confirmar y emitir la guía?\n\nProductos a trasladar: ${items.length}\n\nEsta acción no se puede deshacer.`)) {
          return;
      }*/
     

      // ============ 4. ARMAR PAYLOAD ============
      const payload = {
          ...cabecera,
          detalles: items
      };

      console.log("📦 Payload a enviar:", payload);

      // ============ 5. ENVIAR AL BACKEND ============
      const btn = document.getElementById('grEmitirGuia');
      btn.disabled = true;
      btn.textContent = '⏳ Emitiendo...';

      try {
          const response = await fetch(RUTA + 'movimientos/guia', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify(payload)
          });

          const data = await response.json();

          if (data.success) {
              mostrarMensaje('✅ Guía emitida correctamente.','mensaje_correcto');

              $("#grNumber").val(data.idGuia);

              // Mostrar PDF en el visor
              const iframe = document.getElementById('grIframePDF');
              const urlPDF = RUTA + 'guia/pdf?id=' + data.idGuia;

              iframe.src = urlPDF;
              iframe.style.display = 'block';
              document.getElementById('grPdfPlaceholder').style.display = 'none';
              document.getElementById('grBtnDescargarPDF').href = urlPDF;
          } else {
              alert('❌ Error: ' + (data.error || 'No se pudo emitir.'));
          }

      } catch (error) {
          console.error(error);
          alert('Error de conexión al emitir la guía.');
      } finally {
          btn.disabled = false;
          btn.textContent = '✅ Emitir Guía';
      }
  }

  /* ============================================================
   PANELES DE BÚSQUEDA
   ============================================================ */
  function grCerrarTodosLosPaneles() {
      document.querySelectorAll('.gr-panel-search').forEach(p => p.remove());
      document.querySelectorAll('.gr-btn-search.gr-abierto').forEach(b => b.classList.remove('gr-abierto'));
  }

async function grAbrirPanel(boton) {
    const panelId  = boton.dataset.panel;
    const inputId  = boton.dataset.target;
    const tipo     = boton.dataset.tipo;
    const fieldRow = boton.closest('.gr-field-row');

    // Si ya está abierto, cerrar
    if (boton.classList.contains('gr-abierto')) {
        grCerrarTodosLosPaneles();
        return;
    }

    grCerrarTodosLosPaneles();
    boton.classList.add('gr-abierto');

    // Crear panel con "Cargando..."
    const panel = document.createElement('div');
    panel.className = 'gr-panel-search';
    panel.id = panelId;

    panel.innerHTML = `
        <div class="gr-panel-search-buscar">
            <input type="text" placeholder="🔍 Buscar..." autocomplete="off">
        </div>
        <div class="gr-panel-search-lista">
            <div class="gr-panel-search-vacio">Cargando...</div>
        </div>
    `;

    fieldRow.appendChild(panel);

    // Focus al buscador
    setTimeout(() => panel.querySelector('input').focus(), 50);

    // 👇 Traer datos del backend CADA VEZ
    const datos = await grCargarDatos(tipo);

    const lista = panel.querySelector('.gr-panel-search-lista');
    grRenderizarLista(lista, datos, inputId, boton);

    // Filtro en vivo (sobre los datos ya cargados)
    panel.querySelector('input').addEventListener('input', function() {
        const q = this.value.toLowerCase().trim();
        const filtrados = datos.filter(d =>
            (d.nombre && d.nombre.toLowerCase().includes(q)) ||
            (d.codigo && d.codigo.toLowerCase().includes(q))
        );
        grRenderizarLista(lista, filtrados, inputId, boton);
    });

    // Cerrar con ESC
    panel.querySelector('input').addEventListener('keydown', function(e) {
        if (e.key === 'Escape') grCerrarTodosLosPaneles();
    });
}

function grRenderizarLista(contenedor, datos, inputId, boton) {
    contenedor.innerHTML = '';

    if (datos.length === 0) {
        contenedor.innerHTML = '<div class="gr-panel-search-vacio">Sin resultados</div>';
        return;
    }

    datos.forEach(d => {
        const item = document.createElement('div');
        item.className = 'gr-panel-search-item';

        const codigo = d.codigo || d.id || '';
        const nombre = d.nombre || d.descripcion || '';

        item.innerHTML = `
            <span class="gr-codigo">${codigo}</span>
            <span class="gr-nombre">${nombre}</span>
        `;

        item.addEventListener('click', () => grSeleccionarOpcion(d, inputId, boton));
        contenedor.appendChild(item);
    });
}

function grSeleccionarOpcion(dato, inputId, boton) {
    const input = document.getElementById(inputId);

    const nombre = dato.nombre || dato.descripcion || '';

    if (input) input.value = nombre;

    // Rellenar campos relacionados
    grRellenarCamposRelacionados(dato, inputId);

    grCerrarTodosLosPaneles();
}

});
