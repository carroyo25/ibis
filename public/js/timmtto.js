$(() => {
  $("#esperar").css({ display: "none", opacity: "0" });

  const ventana = document.getElementById("registroti");
  const header = document.getElementById("ventanaHeader");

  if (!ventana || !header) return;

  let isDragging = false;
  let startX, startY;
  let initialLeft, initialTop;

  header.addEventListener("mousedown", startDrag);
  document.addEventListener("mousemove", drag);
  document.addEventListener("mouseup", stopDrag);

  // Posición inicial centrada
  ventana.style.position = "absolute";
  ventana.style.left = "50%";
  ventana.style.top = "50%";
  ventana.style.transform = "translate(-50%, -50%)";
  ventana.style.margin = "0";

  let id, cc, docidetuser, serie, fila;

  const tabla_principal = document.getElementById("tablaPrincipal");

  $("#btnAceptarDialogo").click(function (e) {
    e.preventDefault();

    const formData = new FormData();

    try {
      if ($("#fecha_mmto").val() == "")
        throw new Error("No ingreso fecha del mantenimiento");

      formData.append(
        "lastMmtto",
        document.getElementById("idlastmmtto").value,
      );

      formData.append("id", $("#idmmtto").val());
      formData.append("fmmto", $("#fecha_mmto").val());
      formData.append("correo", $("#correo_usuario").val());
      formData.append("observa", $("#observaciones_dialogo").val());
      formData.append("user", $("#id_user").val());
      formData.append("tecnico", $("#name_user").val());
      formData.append("correo_tecnico", $("#mail_user").val());
      formData.append("asignado", $("#usuario").val());
      formData.append("tipo_mmtto", $("#tipo_mmtto").val());

      formData.append("procesador", $("#procesador").val()); //
      formData.append("ram", $("#ram").val()); //
      formData.append("hdd", $("#hdd").val()); //
      formData.append("otros", $("#otros").val()); //
      formData.append("estado", $("#estado_equipo").val()); //

      formData.append("codigo_costos", $("#codigo_proyecto").val());
      formData.append("serie_producto", $("#serie").val());
      formData.append("documento_usuario", $("#nro_documento").val());
      formData.append("codigo_producto", $("#codigo_producto").val());
      formData.append("proximos", $("#proximos").val());

      fetch(RUTA + "timmtto/mantenimiento", {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.respuesta) {
            mostrarMensaje("Mantenimiento registrado", "mensaje_correcto");
            $("#dialogo_registro").fadeOut();
          }
        });
    } catch (error) {
      mostrarMensaje(error.message, "mensaje_error");
    }

    return false;
  });

  $("#btnCancelarDialogo").click(function (e) {
    e.preventDefault();

    $("#dialogo_registro").fadeOut();

    return false;
  });

  $("#sendNotify").click(function (e) {
    e.preventDefault();

    $("#esperar").css({ display: "block", opacity: "1" });

    try {
      let formData = new FormData();
      formData.append("id", id);
      formData.append("fmmto", $("#fecha_mmto").val());
      formData.append("correo", $("#correo_usuario").val());
      formData.append("tecnico", $("#name_user").val());
      formData.append("correo_tecnico", $("#mail_user").val());
      formData.append("serie_producto", $("#serie").val());
      formData.append("usuario", $("#usuario").val());
      formData.append("fecha", $("#fecha_sugerida").val());

      fetch(RUTA + "timmtto/notificar", {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.respuesta) {
            $("#esperar").css("opacity", "0").fadeOut();
            mostrarMensaje("Correo Enviado", "mensaje_correcto");
            $("#dialogo_registro").fadeOut();
          }
        });
    } catch (error) {
      mostrarMensaje(error, "mensaje_error");
    }

    return false;
  });

  $("#btnAceptarGrabar").click(function (e) {
    e.preventDefault();
    try {
      if ($("#fecha_nueva").val() === "") throw new Error("Escoja una fecha");

      let formData = new FormData();
      formData.append("fecha", $("#fecha_nueva").val());
      formData.append("serie", serie);
      formData.append("documento", docidetuser);

      fetch(RUTA + "timmtto/cambiofechas", {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json())
        .then((data) => {
          console.log(data);
        });
    } catch (error) {
      mostrarMensaje(error, "mensaje_error");
    }

    return false;
  });

  $("#btnCancelarGrabar, #btnCancelarSerie").click(function (e) {
    e.preventDefault();

    $(this).closest(".modal").fadeOut();

    return false;
  });

  $("#btnAceptarSerie").click(function (e) {
    e.preventDefault();

    let formData = new FormData();

    formData.append("serie_nueva", $("#serie_nueva").val());
    formData.append("serie_anterior", serie);
    formData.append("documento", docidetuser);

    fetch(RUTA + "timmtto/cambioSeries", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.respuesta) {
          //***con esto actualiza la fila sin necesidad de recargar la pagina */

          $("#" + fila)
            .find("td")
            .eq("3")
            .text($("#serie_nueva").val());

          mostrarMensaje("👍 Serie actualizada", "mensaje_correcto");
          $(this).closest(".modal").fadeOut();
        } else {
          mostrarMensaje("😥 Error al actualixar", "mensaje_error");
        }
      });

    return false;
  });

  $("#btnConsulta").click(function (e) {
    e.preventDefault();

    let formData = new FormData();

    const serie = document.getElementById("serieBusqueda");
    const costos = document.getElementById("costosSearch");
    const nombre = document.getElementById("usuarioBusqueda");

    formData.append("serie", serie.value);
    formData.append("costos", costos.value);
    formData.append("nombre", nombre.value);

    const grupos = {};

    try {
      fetch(RUTA + "timmtto/listaMmttos", {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json())
        .then((data) => {
          datosOriginales = agruparPorSerie(data.datos);
          datosFiltrados = [...datosOriginales];
          renderSeries(datosOriginales);
        });
    } catch (error) {}

    return false;
  });

  $("#excelFile").click(function (e) {
    e.preventDefault();

    $("#esperarCargo").css("opacity", "1").fadeIn();

    let formdata = new FormData();

    formdata.append("detalles", JSON.stringify(detalles()));

    fetch(RUTA + "timmtto/archivoExcel", {
      method: "POST",
      body: formdata,
    })
      .then((response) => {
        return response.json();
      })
      .then((json) => {
        $("#esperarCargo").css("opacity", "0").fadeOut();
        window.location.href = json.documento;
      })
      .catch((err) => {
        console.log(err);
      });

    return false;
  });

  function startDrag(e) {
    // No arrastrar si se hizo clic en el botón de notificar
    if (e.target.closest("#sendNotify")) return;

    isDragging = true;

    // Obtener posición actual (sin transform)
    const computedStyle = window.getComputedStyle(ventana);
    const leftValue = computedStyle.left;
    const topValue = computedStyle.top;

    // Si está centrado con transform, calcular posición real
    if (ventana.style.transform === "translate(-50%, -50%)") {
      const rect = ventana.getBoundingClientRect();
      initialLeft = rect.left;
      initialTop = rect.top;
      ventana.style.left = initialLeft + "px";
      ventana.style.top = initialTop + "px";
      ventana.style.transform = "none";
    } else {
      initialLeft = parseFloat(leftValue);
      initialTop = parseFloat(topValue);
    }

    startX = e.clientX;
    startY = e.clientY;

    ventana.classList.add("dragging");
    e.preventDefault();
  }

  function drag(e) {
    if (!isDragging) return;

    const deltaX = e.clientX - startX;
    const deltaY = e.clientY - startY;

    let newLeft = initialLeft + deltaX;
    let newTop = initialTop + deltaY;

    // Limitar dentro de la ventana (opcional)
    const maxX = window.innerWidth - ventana.offsetWidth;
    const maxY = window.innerHeight - ventana.offsetHeight;

    newLeft = Math.max(0, Math.min(newLeft, maxX));
    newTop = Math.max(0, Math.min(newTop, maxY));

    ventana.style.left = newLeft + "px";
    ventana.style.top = newTop + "px";
  }

  function stopDrag() {
    isDragging = false;
    ventana.classList.remove("dragging");
  }
});

detalles = () => {
  let DATA = [];

  let TABLA = $("#tablaPrincipal tbody >tr");

  TABLA.each(function () {
    item = {};
    ((item["item"] = $(this).find("td").eq(0).text()),
      (item["descripcion"] = $(this).find("td").eq(1).text()),
      (item["usuario"] = $(this).find("td").eq(2).text()),
      (item["serie"] = $(this).find("td").eq(3).text()),
      (item["entrega"] = $(this).find("td").eq(4).text()),
      (item["costos"] = $(this).find("td").eq(5).text()),
      (item["mmtto1"] = $(this).find("td").eq(6).text()),
      (item["estado1"] = $(this).find("td").eq(7).text()),
      (item["mmtto2"] = $(this).find("td").eq(8).text()),
      (item["estado2"] = $(this).find("td").eq(9).text()),
      (item["mmtto3"] = $(this).find("td").eq(10).text()),
      (item["estado3"] = $(this).find("td").eq(11).text()),
      (item["mmtto3"] = $(this).find("td").eq(12).text()),
      (item["estado4"] = $(this).find("td").eq(13).text()));

    DATA.push(item);
  });

  return DATA;
};

function formatearFecha(fecha) {
  if (!fecha) return "-";
  const partes = fecha.split("-");
  return `${partes[2]}/${partes[1]}/${partes[0]}`;
}

function formatearDias(dias, flgestado) {
  if (flgestado === 1) {
    return `<span class="dias-positivo">✅ Realizado</span>`;
  } else if (dias < 0) {
    return `<span class="dias-negativo">📉 ${Math.abs(dias)} días (vencido)</span>`;
  } else if (dias === 0) {
    return `<span class="dias-positivo">📅 Hoy</span>`;
  } else if (dias <= 30) {
    return `<span class="dias-positivo">📈 +${dias} días (próximo)</span>`;
  } else {
    return `<span class="dias-positivo">📈 +${dias} días</span>`;
  }
}

function getEstadoBadge(dias, flgestado) {
  if (flgestado === 1) {
    return '<span class="badge-status badge-completado">✅ COMPLETADO</span>';
  } else if (dias < 0) {
    return '<span class="badge-status badge-vencido">⚠️ VENCIDO</span>';
  } else if (dias === 0) {
    return '<span class="badge-status badge-pendiente">📅 HOY</span>';
  } else if (dias <= 30) {
    return '<span class="badge-status badge-pendiente">🔄 PRÓXIMO</span>';
  } else {
    return '<span class="badge-status badge-normal">📆 NORMAL</span>';
  }
}

function agruparPorSerie(data) {
  const grupos = {};
  data.forEach((item) => {
    const serie = item.cserie;
    if (!grupos[serie]) {
      grupos[serie] = {
        cserie: serie,
        idx: item.idreg,
        producto: item.idprod,
        nrodoc: item.nrodoc,
        fentrega: item.fentrega,
        nombre: item.nombre,
        documento: item.nrodoc,
        correo: item.correo,
        cdesprod: item.cdesprod,
        hdd: item.chdd,
        procesador: item.cprocesador,
        ram: item.cram,
        otros: item.totros,
        estado: item.nestado,
        proyecto: item.idcostos,
        mantenimientos: [],
      };
    }
    grupos[serie].mantenimientos.push({
      fmtto: item.fmtto,
      cobserva: item.cobserva,
      idreg: item.idreg,
      dias_diferencia: item.dias_diferencia,
      flgestado: item.flgestado,
      tecnico: item.cnameuser,
      dni: item.nrodoc,
      fotos: item.fotos,
    });
  });
  // Ordenar mantenimientos por fecha
  for (let serie in grupos) {
    grupos[serie].mantenimientos.sort(
      (a, b) => a.dias_diferencia - b.dias_diferencia,
    );
  }
  return Object.values(grupos);
}

function getClaseFila(mantto) {
  if (mantto.flgestado === 1) return "completado-row";
  if (mantto.dias_diferencia < 0) return "vencido-row";
  return "pendiente-row";
}

function getEstadoGeneralSerie(mantenimientos) {
  const tieneVencido = mantenimientos.some(
    (m) => m.flgestado === 0 && m.dias_diferencia < 0,
  );
  const tienePendiente = mantenimientos.some(
    (m) => m.flgestado === 0 && m.dias_diferencia >= 0,
  );
  if (tieneVencido) return "badge-vencido";
  if (tienePendiente) return "badge-pendiente";
  return "badge-normal";
}

function getTextoEstadoGeneral(mantenimientos) {
  const tieneVencido = mantenimientos.some(
    (m) => m.flgestado === 0 && m.dias_diferencia < 0,
  );
  const tienePendiente = mantenimientos.some(
    (m) => m.flgestado === 0 && m.dias_diferencia >= 0,
  );
  const total = mantenimientos.length;
  const completados = mantenimientos.filter((m) => m.flgestado === 1).length;

  if (tieneVencido) return `⚠️ ${total - completados} vencidos`;
  if (tienePendiente) return `⏳ ${total - completados} pendientes`;
  return `✅ ${completados}/${total} completados`;
}

function limpiarId(str) {
  return str.replace(/[^a-zA-Z0-9]/g, "_");
}

function toggleSerie(serieId) {
  const detailRow = document.querySelector(
    `.detail-row[data-serie-id="${serieId}"]`,
  );
  const icon = document.getElementById(`icon-${serieId}`);
  if (!detailRow || !icon) return;
  if (detailRow.classList.contains("hidden")) {
    detailRow.classList.remove("hidden");
    icon.classList.add("rotated");
  } else {
    detailRow.classList.add("hidden");
    icon.classList.remove("rotated");
  }
}

function abrirModalSerie(serie) {
  const modal = document.getElementById("dialogo_registro");

  document.getElementById("serie").value = serie.cserie;
  document.getElementById("descripcion").value = serie.cdesprod;
  document.getElementById("usuario").value = serie.nombre;
  document.getElementById("correo_usuario").value = serie.correo;
  document.getElementById("procesador").value = serie.procesador;
  document.getElementById("ram").value = serie.ram == "null" ? "" : serie.ram;
  document.getElementById("hdd").value = serie.hdd == "null" ? "" : serie.hdd;
  document.getElementById("estado_equipo").value = serie.estado;
  document.getElementById("otros").value =
    serie.otros == "null" ? "" : serie.otros;
  document.getElementById("idlastmmtto").value = 0;
  document.getElementById("nro_documento").value = serie.documento;
  document.getElementById("codigo_proyecto").value = serie.proyecto;
  document.getElementById("codigo_producto").value = serie.producto;

  const detalle = document.getElementById("bodyDetalle");
  detalle.innerHTML = "";
  let proximos = 0;

  serie.mantenimientos.forEach((mmtto) => {
    const estado = mmtto.flgestado;
    const tr = document.createElement("tr");

    tr.innerHTML = `<td>${formatearFecha(mmtto.fmtto)}</td>
                    <td>${mmtto.cobserva}</td>
                    <td>${mmtto.tecnico}</td>`;
    if (estado == 1) {
      detalle.appendChild(tr);
    } else {
      document.getElementById("idlastmmtto").value = mmtto.idreg;
      document.getElementById("proximos").value = proximos++;
    }
  });

  modal.style.display = "block";
}

function cerrarModalSerie() {
  document.getElementById("dialogo_registro").style.display = "none";
  document.body.style.overflow = "auto";
}

function renderSeries(series) {
  const tbody = document.getElementById("tableBody");
  if (!tbody) return;
  tbody.innerHTML = "";

  series.forEach((serie) => {
    const estadoClase = getEstadoGeneralSerie(serie.mantenimientos);
    const estadoTexto = getTextoEstadoGeneral(serie.mantenimientos);
    const serieId = limpiarId(serie.cserie);

    const row = tbody.insertRow();
    row.classList.add("serie-principal");
    row.setAttribute("data-serie-id", serieId);
    row.innerHTML = `
            <td class="serie-td">
                <span class="toggle-icon" id="icon-${serieId}">▶</span>
                <span class="serie-code">🔧 ${serie.cserie}</span>
            </td>
            <td>${serie.nombre}</td>
            <td>${serie.documento}</td>
            <td>${serie.cdesprod}</td>
            <td>${formatearFecha(serie.fentrega)}</td>
            <td><span class="badge-status ${estadoClase}">${estadoTexto}</span></td>
            <td style="text-align: center;">
                <span class="modal-icon" onclick="event.stopPropagation();abrirModalSerie(${JSON.stringify(serie).replace(/"/g, "&quot;")})">📋</span>
            </td>
        `;

    row.addEventListener("click", function (e) {
      if (e.target.classList && e.target.classList.contains("modal-icon"))
        return;
      toggleSerie(serieId);
    });

    const detailRow = tbody.insertRow();
    detailRow.classList.add("detail-row", "hidden");
    detailRow.setAttribute("data-serie-id", serieId);
    detailRow.innerHTML = `
            <td colspan="7" style="padding: 0;">
                <table class="sub-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Fecha Mantenimiento</th>
                            <th>Estado</th>
                            <th>Días</th>
                            <th>Observaciones</th>
                            <th>Subir Fotos</th>
                            <th>Ver Fotos</th>
                            <th>Eliminar MMTTO</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${serie.mantenimientos
                          .map(
                            (m) => `
                            <tr class="${getClaseFila(m)}" id="${m.idreg}">
                                <td>${m.idreg}</td>
                                <td>${formatearFecha(m.fmtto)}</td>
                                <td>${getEstadoBadge(m.dias_diferencia, m.flgestado)}</td>
                                <td>${formatearDias(m.dias_diferencia, m.flgestado)}</td>
                                <td>${m.cobserva || "-"}</td>
                                <td style="text-align:center"><span data-id="${m.idreg}" onclick="event.stopPropagation();abrirUploadModal(${m.idreg})"><i class="fas fa-file-upload"></i></span></td>
                                <td style="text-align:center"><span data-id="${m.idreg}" style="position:relative" onclick="event.stopPropagation();abrirViewModal(${m.idreg})"><i class="fas fa-image"></i><span class="contador_fotos">${m.fotos || ""}</span></span></td>
                                <td style="text-align:center"><span data-id="${m.idreg}" onclick="event.stopPropagation();eliminarRegistro(${m.idreg})"><i class="fas fa-trash"></i></span></td>
                            </tr>
                        `,
                          )
                          .join("")}
                    </tbody>
                </table>
            </td>
        `;
  });
}

function eliminarRegistro(id) {
  try {
    const formData = new FormData();
    formData.append("indice", id);

    fetch(RUTA + "timmtto/anula", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        document.getElementById(id).remove();
        mostrarMensaje("Registro Eliminado", "mensaje_correcto");
      });
  } catch (error) {
    mostrarMensaje(error.message, "mensaje_error");
  }
}

function cerrarUploadModal() {
  const modal = document.getElementById("uploadModal");

  // Fadeout
  modal.style.transition = "opacity 0.2s";
  modal.style.opacity = "0";

  setTimeout(() => {
    modal.classList.remove("show");
    modal.style.opacity = "";
    modal.style.transition = "";

    // Limpiar todo
    currentMantenimientoId = null;
    fotosPendientes = [];
    document.getElementById("previewContainer").innerHTML = "";
    document.getElementById("fileInput").value = "";
    //document.getElementById("fotoCount").innerText = "0";
    const dropArea = document.getElementById("dropArea");
    if (dropArea) dropArea.classList.remove("drag-over");
  }, 200);
}

function abrirUploadModal(mantenimientoId) {
  currentMantenimientoId = mantenimientoId;
  fotosPendientes = [];
  actualizarPreview();
  document.getElementById("fileInput").value = "";
  document.getElementById("uploadModal").classList.add("show");
  setTimeout(setupDragAndDrop, 100);
}

function actualizarPreview() {
  const previewContainer = document.getElementById("previewContainer");
  const fotoCountSpan = document.getElementById("fotoCount");

  previewContainer.innerHTML = fotosPendientes
    .map(
      (foto, idx) => `
                <div class="preview-item">
                    <img src="${foto}" class="preview-img">
                    <button class="remove-img" onclick="removerFotoPendiente(${idx})">×</button>
                </div>
            `,
    )
    .join("");

  if (fotoCountSpan) {
    fotoCountSpan.innerText = fotosPendientes.length;
  }
}

function setupDragAndDrop() {
  const dropArea = document.getElementById("dropArea");
  if (!dropArea) return;

  ["dragenter", "dragover", "dragleave", "drop"].forEach((eventName) => {
    dropArea.addEventListener(eventName, preventDefaults, false);
  });

  function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
  }

  ["dragenter", "dragover"].forEach((eventName) => {
    dropArea.addEventListener(eventName, highlight, false);
  });

  ["dragleave", "drop"].forEach((eventName) => {
    dropArea.addEventListener(eventName, unhighlight, false);
  });

  function highlight(e) {
    dropArea.classList.add("drag-over");
  }

  function unhighlight(e) {
    dropArea.classList.remove("drag-over");
  }

  dropArea.addEventListener("drop", handleDrop, false);
}

function handleDrop(e) {
  const dt = e.dataTransfer;
  const files = dt.files;
  handleFiles(files);
}

function handleFiles(files) {
  const previewContainer = document.getElementById("previewContainer");
  const fileArray = Array.from(files);

  fileArray.forEach((file) => {
    if (file.type.startsWith("image/")) {
      const reader = new FileReader();
      reader.onload = function (e) {
        fotosPendientes.push(e.target.result);
        actualizarPreview();
      };
      reader.readAsDataURL(file);
    }
  });
}

function removerFotoPendiente(index) {
  fotosPendientes.splice(index, 1);
  actualizarPreview();
}

function subirFotos() {
  if (fotosPendientes.length === 0) {
    alert("Selecciona o arrastra al menos una foto");
    return;
  }

  // Mostrar indicador de carga
  const btnSubir = document.querySelector(".btn-subir");
  const textoOriginal = btnSubir.innerHTML;
  btnSubir.innerHTML = "⏳ Subiendo...";
  btnSubir.disabled = true;

  // Crear FormData para enviar los archivos
  const formData = new FormData();
  formData.append("mantenimiento_id", currentMantenimientoId);

  // Convertir las fotos pendientes (base64) a archivos Blob
  fotosPendientes.forEach((base64, index) => {
    const blob = dataURLtoBlob(base64);
    formData.append(`fotos[]`, blob, `foto_${Date.now()}_${index}.jpg`);
  });

  // Enviar al backend
  fetch(RUTA + "timmtto/upload_fotos", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        cerrarUploadModal();

        // Mostrar mensaje de éxito
      } else {
        alert("Error al subir fotos: " + data.message);
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      alert("Error de conexión al servidor");
    })
    .finally(() => {
      // Restaurar botón
      btnSubir.innerHTML = `📤 Subir <span id="fotoCount">0</span> fotos`;
      btnSubir.disabled = false;
    });

  function guardarFotosStorage() {
    localStorage.setItem(
      "mantenimiento_fotos_desktop",
      JSON.stringify(fotosStorage),
    );
  }
}

// Función auxiliar: Convertir dataURL a Blob
function dataURLtoBlob(dataURL) {
  const arr = dataURL.split(",");
  const mime = arr[0].match(/:(.*?);/)[1];
  const bstr = atob(arr[1]);
  let n = bstr.length;
  const u8arr = new Uint8Array(n);
  while (n--) {
    u8arr[n] = bstr.charCodeAt(n);
  }
  return new Blob([u8arr], { type: mime });
}

function abrirViewModal(mantenimientoId) {
  const fotos = obtenerFotos(mantenimientoId);

  const galleryContainer = document.getElementById("galleryContainer");

  try {
    const formData = new FormData();
    formData.append("id", mantenimientoId);

    fetch(RUTA + "timmtto/fotos", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.datos.length > 0) {
          galleryContainer.innerHTML = `<div class="gallery-grid">
                                              ${data.datos
                                                .map(
                                                  (dato, idx) => `
                                                <div class="gallery-item">
                                                    <img src="${"public/documentos/ti/fotos/" + dato.foto}" 
                                                    class="gallery-img" onclick="abrirFullscreen('${"public/documentos/ti/fotos/" + dato.foto}')">
                                                </div>`,
                                                )
                                                .join("")}
                                          </div>`;

          document.getElementById("viewModal").classList.add("show");
        }
      });
  } catch (error) {
    mostrarMensaje("No se regitraron fotos", "mensaje_error");
  }
}

function cerrarViewModal() {
  document.getElementById("viewModal").classList.remove("show");
}

function obtenerFotos(mantenimientoId) {
  try {
    const formData = new FormData();
    formData.append("id", mantenimientoId);

    fetch(RUTA + "timmtto/fotos", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        return data;
      });
  } catch (error) {
    mostrarMensaje("No se regitraron fotos", "mensaje_error");
  }
}
