$(function () {
  let allrows = [];
  let consulta = "";

  $("#esperar").css({ display: "none", opacity: "0" });

  const modal_registro = document.getElementById("dialogo_registro");
  const modal_carga = document.getElementById("cargarArchivo");
  const modal_cargar_certificados =
    document.getElementById("cargarCertificados");

  const btnRegister = document.getElementById("nuevoRegistro");
  const btnExport = document.getElementById("excelFile");
  const btnCancelDialog = document.getElementById("btnCancelarDialogoActivos");
  const btnSave = document.getElementById("btnGrabarDialogoActivos");
  const btnImport = document.getElementById("importXls");
  const btnAcceptLoad = document.getElementById("btnAceptarCargar");
  const btnCancelLoad = document.getElementById("btnCancelarCargar");
  const btnConsult = document.getElementById("btnConsulta");
  const btnAtach = document.getElementById("btnAtachDialogoActivos");
  const btnQr = document.getElementById("btnbtQrDialogoActivos");
  const btnLoadAtach = document.getElementById("openArch");

  const inputSearchCode = document.getElementById("codigoSearch");
  const inputSerie = document.getElementById("serie");
  const inputItemCode = document.getElementById("codigo_interno");
  const inputCalibra = document.getElementById("fecha_calibra");
  const inputEstado = document.getElementById("estado_actual");
  const inputUbicacion = document.getElementById("ubicacion");
  const inputImport = document.getElementById("fileInput");
  const inputAtach = document.getElementById("uploadAtach");

  const sltCostos = document.getElementById("centro_costos");
  const sltCostosLoad = document.getElementById("loadProyect");
  const sltFrecuencia = document.getElementById("frecuencia");
  const sltCostosSearch = document.getElementById("costosSearch");

  const fmrActivos = document.getElementById("activos_form");

  const lnkLoad = document.getElementById("lnkLoad");

  btnRegister.addEventListener("click", (e) => {
    e.preventDefault();

    //llama el codigo del usuario que registra
    document.getElementById("codigo_usuario").value =
      document.getElementById("id_user").value;

    limpiarFormulario(true);

    modal_registro.style.display = "block";

    return false;
  });

  btnCancelDialog.addEventListener("click", (e) => {
    e.preventDefault();

    limpiarFormulario(true);
    modal_registro.style.display = "none";

    return false;
  });

  //busca el item en el centro de costos
  inputSearchCode.addEventListener("keydown", (e) => {
    if (e.key == "Enter") {
      try {
        if (sltCostos.value == -1)
          throw new Error("Seleccione un centro de costos");
        if (e.target.value == "")
          throw new Error("Escriba un codigo para validar");

        const formData = new FormData();
        formData.append("codigo", e.target.value);
        formData.append("costos", sltCostos.value);

        fetch(RUTA + "activos/buscaCodigo", {
          method: "POST",
          body: formData,
        })
          .then((response) => response.json())
          .then((data) => {
            document.getElementById("descripSearch").value =
              data.datos[0]["descripcion"];
            document.getElementById("unidad").value = data.datos[0]["cabrevia"];
            document.getElementById("codigo_interno").value =
              data.datos[0]["id_cprod"];
            document.getElementById("codigo_unidad").value =
              data.datos[0]["ncodmed"];

            mostrarMensaje("👌 Codigo existente", "mensaje_correcto");
          });
      } catch (error) {
        mostrarMensaje(error.message, "mensaje_error");
        document.getElementById("codigo_interno").value = "";
      }
    }
  });

  //buscar si esta asignado
  inputSerie.addEventListener("keydown", (e) => {
    if (e.key == "Enter") {
      try {
        if (sltCostos.value == -1)
          throw new Error("Seleccione un centro de costos");
        if (inputItemCode.value == "")
          throw new Error("Seleccione un codigo de producto para validar");
        if (e.target.value == "")
          throw new Error("Escriba una serie para validar");

        const formData = new FormData();
        formData.append(
          "codigo",
          document.getElementById("codigo_interno").value,
        );
        formData.append("costos", sltCostos.value);
        formData.append("serie", e.target.value);

        fetch(RUTA + "activos/asignados", {
          method: "POST",
          body: formData,
        })
          .then((response) => response.json())
          .then((data) => {
            if (
              data.existe &&
              document.getElementById("codigo_registro").value == ""
            ) {
              mostrarMensaje(
                "💡 La serie ya se encuentra registrada..",
                "mensaje_error",
              );
              return false;
            }

            if (data.asignado) {
              document.getElementById("dni").value = data.datos[0]["dni"];
              document.getElementById("cargo").value =
                data.datos[0]["cargo"].toUpperCase();
              document.getElementById("nombres").value =
                data.datos[0]["nombres"] +
                " " +
                data.datos[0]["paterno"] +
                " " +
                data.datos[0]["materno"];

              document.getElementById("fecha_asigna").value = data.salida;

              document.getElementById("ubicacion").value = "ASIGNADO";
              document.getElementById("ubicacion").style.color = "#c62828";
              document.getElementById("ubicacion").style.backgroundColor =
                "#ffebee";
            } else {
              document.getElementById("dni").value = "";
              document.getElementById("cargo").value = "";
              document.getElementById("nombres").value = "";

              document.getElementById("ubicacion").value = "ALMACEN";
              document.getElementById("ubicacion").style.color = "#2e7d32";
              document.getElementById("ubicacion").style.backgroundColor =
                "#ffebee";
            }
          });
      } catch (error) {
        mostrarMensaje(error.message, "mensaje_error");
      }
    }
  });

  inputCalibra.addEventListener("change", (e) => {
    calcularVencimiento();
    return false;
  });

  sltFrecuencia.addEventListener("change", (e) => {
    calcularVencimiento();
  });

  btnSave.addEventListener("click", (e) => {
    e.preventDefault();

    $("#esperar").css({ display: "block", opacity: "1" });

    const formData = new FormData(fmrActivos);

    if (document.getElementById("codigo_registro").value == "") {
      consulta = RUTA + "activos/registro";
    } else {
      consulta = RUTA + "activos/modifica";
    }

    fetch(consulta, {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        limpiarFormulario(false);

        mostrarMensaje(data.mensaje, data.clase);

        $("#esperar").css({ display: "none", opacity: "0" });
      });

    return false;
  });

  inputEstado.addEventListener("change", (e) => {
    e.preventDefault();

    e.target.style.color = "#000";
    e.target.style.backgroundColor = "#fff";

    document.getElementById("observa_estado").value = "";

    return false;
  });

  inputUbicacion.addEventListener("change", (e) => {
    e.preventDefault();

    e.target.style.color = "#000";
    e.target.style.backgroundColor = "#fff";

    return false;
  });

  btnImport.addEventListener("click", (e) => {
    e.preventDefault();

    modal_carga.style.display = "block";

    return false;
  });

  inputImport.addEventListener("change", (e) => {
    const file = e.target.files[0];
    if (!file) return;

    document.getElementById("fileName").textContent =
      `📄 Archivo seleccionado: ${file.name}`;

    const tableContainer = document.getElementById("tablaPrincipal");

    const reader = new FileReader();
    reader.onload = function (e) {
      try {
        const data = new Uint8Array(e.target.result);
        const workbook = XLSX.read(data, { type: "array" });

        const firstSheet = workbook.SheetNames[0];
        const worksheet = workbook.Sheets[firstSheet];

        const jsonData = XLSX.utils.sheet_to_json(worksheet, { header: 1 });

        if (jsonData.length > 0) {
          allrows = jsonData
            .slice(1)
            .filter((row) => row.some((cell) => cell !== null && cell !== ""));
        } else {
          tableContainer.innerHTML =
            '<div class="error-message">El archivo está vacío</div>';
        }
      } catch (error) {
        tableContainer.innerHTML = `<div class="error-message">Error al procesar el archivo: ${error.message}</div>`;
      }
    };

    reader.readAsArrayBuffer(file);

    return false;
  });

  lnkLoad.addEventListener("click", (e) => {
    inputImport.click();
  });

  btnAcceptLoad.addEventListener("click", (e) => {
    e.preventDefault();

    try {
      if (allrows.length === 0) throw new Error("No hay datos para guardar");
      if (sltCostosLoad.value === "-1")
        throw new Error("Seleccione un centro de costos");

      let formData = new FormData();
      formData.append("proyecto", sltCostosLoad.value);
      formData.append("filas", JSON.stringify(allrows));
      formData.append(
        "registra",
        document.getElementById("codigo_usuario").value,
      );

      $("#esperar").css({ display: "block", opacity: "1" });

      fetch(RUTA + "activos/registrosXls", {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json())
        .then((data) => {
          modal_carga.style.display = "block";
          $("#esperar").css({ display: "none", opacity: "0" });
        })
        .catch((error) => {
          mostrarMensaje(error.message, "mensaje_error");
        });
    } catch (error) {
      mostrarMensaje(error.message, "mensaje_error");
    }

    return false;
  });

  btnCancelLoad.addEventListener("click", (e) => {
    e.preventDefault();

    modal_carga.style.display = "none";

    return false;
  });

  btnConsult.addEventListener("click", (e) => {
    e.preventDefault();

    let formData = new FormData();

    formData.append("costos", sltCostosSearch.value);

    const grupos = {};

    fetch(RUTA + "activos/consultaEquipos", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        data.datos.forEach((equipo) => {
          const codigo = equipo.ccodprod;

          if (!grupos[codigo]) {
            grupos[codigo] = {
              codigo: codigo,
              descripcion: equipo.descripcion,
              idreg: equipo.idreg,
              equipos: [],
              ubicaciones: new Set(),
            };
          }

          grupos[codigo].equipos.push(equipo);
          if (equipo.cubica) grupos[codigo].ubicaciones.add(equipo.cubica);
        });

        renderizarTabla(grupos);
      });

    return false;
  });

  btnExport.addEventListener("click", (e) => {
    e.preventDefault();

    let formData = new FormData();

    formData.append("costos", sltCostosSearch.value);

    const grupos = {};

    fetch(RUTA + "activos/consultaEquipos", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then(async (json) => {
        $("#esperar").css({ display: "block", opacity: "1" });
        await excelJson(json.datos);
      });

    return false;
  });

  btnAtach.addEventListener("click", (e) => {
    e.preventDefault();

    modal_cargar_certificados.style.display = "block";

    return false;
  });

  btnLoadAtach.addEventListener("click", (e) => {
    e.preventDefault();

    inputAtach.click();

    return false;
  });

  $("#uploadAtach").on("change", function (e) {
    e.preventDefault();

    let fp = $(this);
    let lg = fp[0].files.length;
    let items = fp[0].files;
    let fragment = "";

    if (lg > 0) {
      for (let i = 0; i < lg; i++) {
        let fileName = items[i].name; // get file name

        // append li to UL tag to display File info
        fragment += `<li><a class="icono_archivo"><i class="far fa-file"></i><p>${fileName}</p></a></li>`;
      }

      $(".listaArchivos").append(fragment);
    }

    return false;
  });
});

function actualizarEstado(fechaVenc) {
  const estadoSelect = document.getElementById("estado_actual");
  const observaciones = document.getElementById("observa_estado");

  if (!fechaVenc) {
    estadoSelect.value = "";
    estadoSelect.style.color = "#555";
    estadoSelect.style.backgroundColor = "#f0f0f0";
    observaciones.value = "";
    return;
  }

  const hoy = new Date();
  hoy.setHours(0, 0, 0, 0);
  const vencimiento = new Date(fechaVenc + "T00:00:00");
  const diffMs = vencimiento - hoy;
  const diffDias = Math.abs(Math.round(diffMs / (1000 * 60 * 60 * 24)));

  if (hoy < vencimiento) {
    estadoSelect.value = "306";
    estadoSelect.style.color = "#2e7d32";
    estadoSelect.style.backgroundColor = "#e8f5e9";
    observaciones.value = `Faltan ${diffDias} DIA(s) PARA VENCER.`;
  } else {
    estadoSelect.value = "307";
    estadoSelect.style.color = "#c62828";
    estadoSelect.style.backgroundColor = "#ffebee";
    observaciones.value = `VENCIO HACE ${diffDias} DIA(S).`;
  }
}

function calcularVencimiento() {
  const fechaRevision = document.getElementById("fecha_calibra").value;
  const periodo = document.getElementById("frecuencia").value;
  const fechaVencimiento = document.getElementById("vence_calibra");

  if (fechaRevision) {
    const fecha = new Date(fechaRevision);
    let diasASumar = periodo === "303" ? 365 : 180;
    fecha.setDate(fecha.getDate() + diasASumar);

    const año = fecha.getFullYear();
    const mes = String(fecha.getMonth() + 1).padStart(2, "0");
    const dia = String(fecha.getDate()).padStart(2, "0");
    const fechaCalc = `${año}-${mes}-${dia}`;
    fechaVencimiento.value = fechaCalc;
    actualizarEstado(fechaCalc); // 👈 llama al estado automático
  } else {
    fechaVencimiento.value = "";
    actualizarEstado("");
  }
}

function limpiarFormulario(sw) {
  if (sw) {
    document.getElementById("codigo_interno").value = "";
    document.getElementById("centro_costos").value = "-1";
    document.getElementById("codigoSearch").value = "";
    document.getElementById("descripSearch").value = "";
  }

  document.getElementById("unidad").value = "";
  document.getElementById("cantidad").value = "1";
  document.getElementById("serie").value = "";
  document.getElementById("marca").value = "";
  document.getElementById("modelo").value = "";
  document.getElementById("dni").value = "";
  document.getElementById("nombres").value = "";
  document.getElementById("cargo").value = "";
  document.getElementById("area").value = "";
  document.getElementById("fecha_asigna").value = "";
  document.getElementById("frecuencia").value = "303";
  document.getElementById("fecha_calibra").value = "";
  document.getElementById("vence_calibra").value = "";
  document.getElementById("estado_actual").value = "";
  document.getElementById("observa_estado").value = "";
  document.getElementById("guia_envio").value = "";
  document.getElementById("fecha_envio").value = "";
  document.getElementById("guia_recepcion").value = "";
  document.getElementById("fecha_recepcion").value = "";
  document.getElementById("ubicacion").value = "";
  document.getElementById("contenedor").value = "";
  document.getElementById("estante").value = "";
  document.getElementById("letra").value = "";
  document.getElementById("columna").value = "";

  document.getElementById("estado_actual").style.color = "#000";
  document.getElementById("estado_actual").style.backgroundColor = "#fff";

  document.getElementById("ubicacion").style.color = "#000";
  document.getElementById("ubicacion").style.backgroundColor = "#fff";
}

function renderizarTabla(grupos) {
  const tbody = document.getElementById("tableBody");

  let html = "";

  Object.values(grupos).forEach((grupo) => {
    const counts = contarEstados(grupo.equipos);
    const total = grupo.equipos.length;
    const ubicaciones = Array.from(grupo.ubicaciones).slice(0, 3);

    html += `
                    <tr class="group-row" onclick="toggleDetalles(this)" data-id="${grupo.idreg}">
                        <td>
                            <span class="badge badge-purple" style="font-family: monospace;">
                                ${grupo.codigo}
                            </span>
                        </td>
                        <td>
                            <strong>${grupo.descripcion}</strong>
                        </td>
                        <td><span class="badge badge-primary">${total} unid.</span></td>
                        <td>
                            <div style="display: flex; gap: 4px; flex-wrap: wrap;">
                                ${counts.vigentes > 0 ? `<span class="badge badge-success">${counts.vigentes} vig.</span>` : ""}
                                ${counts.porVencer > 0 ? `<span class="badge badge-warning">${counts.porVencer} prox.</span>` : ""}
                                ${counts.vencidos > 0 ? `<span class="badge badge-danger">${counts.vencidos} venc.</span>` : ""}
                            </div>
                        </td>
                        <td>${counts.vigentes}</td>
                        <td>${counts.porVencer}</td>
                        <td>${counts.vencidos}</td>
                        <td>
                            <div class="location-tags">
                                ${ubicaciones.map((ub) => `<span class="location-tag">${ub}</span>`).join("")}
                                ${grupo.ubicaciones.size > 3 ? `<span class="location-tag">+${grupo.ubicaciones.size - 3}</span>` : ""}
                            </div>
                        </td>
                        <td>
                            <button class="expand-btn" onclick="event.stopPropagation(); toggleDetalles(this.closest('tr'))">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="9 18 15 12 9 6"></polyline>
                                </svg>
                                Ver detalles
                            </button>
                        </td>
                    </tr>
                    <tr class="detalles-row" style="display: none;">
                        <td colspan="9" style="padding: 0; border: none; background: transparent;">
                            ${crearTablaDetalles(grupo.equipos)}
                        </td>
                    </tr>
                `;
  });

  tbody.innerHTML = html;
}

// Función para calcular días hasta vencimiento
function calcularDiasVencimiento(fechaVence) {
  if (!fechaVence) return null;

  const hoy = new Date();
  hoy.setHours(0, 0, 0, 0);

  const vence = new Date(fechaVence);
  vence.setHours(0, 0, 0, 0);

  return Math.round((vence - hoy) / (1000 * 60 * 60 * 24));
}

// Función para determinar estado
function getEstadoEquipo(dias) {
  if (dias === null) return { texto: "SIN FECHA", clase: "badge-info" };
  if (dias < 0) return { texto: "VENCIDO", clase: "badge-danger" };
  if (dias <= 15) return { texto: "POR VENCER", clase: "badge-warning" };
  return { texto: "VIGENTE", clase: "badge-success" };
}

// Función para agrupar equipos por código
function agruparEquipos() {
  const grupos = {};

  equiposData.forEach((equipo) => {
    const codigo = equipo.ccodprod;

    if (!grupos[codigo]) {
      grupos[codigo] = {
        codigo: codigo,
        descripcion: equipo.descripcion,
        equipos: [],
        ubicaciones: new Set(),
      };
    }

    grupos[codigo].equipos.push(equipo);
    if (equipo.cubica) grupos[codigo].ubicaciones.add(equipo.cubica);
  });

  return grupos;
}

// Función para contar equipos por estado en un grupo
function contarEstados(equipos) {
  let vigentes = 0,
    porVencer = 0,
    vencidos = 0;

  equipos.forEach((e) => {
    const dias = calcularDiasVencimiento(e.ffvence);
    if (dias === null) return;
    if (dias < 0) vencidos++;
    else if (dias <= 15) porVencer++;
    else vigentes++;
  });

  return { vigentes, porVencer, vencidos };
}

// Función para formatear fecha
function formatearFecha(fecha) {
  if (!fecha) return "—";
  return new Date(fecha).toLocaleDateString("es-PE");
}

// Función para crear tabla de detalles
function crearTablaDetalles(equipos) {
  let html = `
                <div class="details-subtable">
                    <div class="details-header">
                        <span>📋 Detalle de equipos (${equipos.length} unidades)</span>
                    </div>
                    <table class="details-table">
                        <thead>
                            <tr>
                                <th>Serie</th>
                                <th>Marca/Modelo</th>
                                <th>F. Vencimiento</th>
                                <th>Estado</th>
                                <th>Ubicación</th>
                                <th>Asignado</th>
                                <th>Obs.</th>
                                <th>Documentos:</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

  equipos.forEach((e) => {
    const dias = calcularDiasVencimiento(e.ffvence);
    const estado = getEstadoEquipo(dias);
    const claseDias =
      dias < 0 ? "vencido" : dias <= 15 ? "por-vencer" : "vigente";

    html += `
                    <tr>
                        <td>
                          <span class="serie-link" data-tooltip="Click para ver detalles" onclick="mostrarDetalleEquipo(${e.idreg})">
                            <strong>${e.cserie || "—"}</strong>
                          </span>
                        </td>
                        <td>${e.cmarca || "—"} ${e.cmodelo || ""}</td>
                        <td>
                            ${formatearFecha(e.ffvence)}
                            <br>
                            ${e.frecuencia}
                        </td>
                        <td>
                            <span class="badge ${estado.clase}">${estado.texto}</span>
                            <br>
                            <small>
                                <span class="estado-fisico ${e.cestado === "307" ? "estado-bueno" : e.cestado === "308" ? "estado-regular" : "estado-malo"}"></span>
                                ${e.estado}
                            </small>
                        </td>
                        <td>
                            ${e.cubica || "—"}
                            <br>
                            ${e.ccontenedor || ""} - ${e.cestante || ""} - ${e.cletra || ""} - ${e.ccolumna || ""}
                        </td>
                        <td>
                            ${
                              e.casigna
                                ? `<span class="badge badge-info" data-tooltip="DNI: ${e.casigna}">Asignado</span>`
                                : '<span class="badge badge-primary">Almacén</span>'
                            }
                        </td>
                        <td>
                            <span data-tooltip="${e.cobservaciones || ""}" style="cursor: help;">
                                ${e.cobservaciones ? e.cobservaciones.substring(0, 20) + "..." : "—"}
                            </span>
                        </td>
                        <td>
                          <p>Nr.Guia Envio : ${e.cgrenvio || ""}</p>
                          <p>Fecha Envio : ${e.ffenvio || ""}</p>
                          <p>Nr.Guia Recepcion : ${e.cgrrecepcion || ""}</p>
                          <p>Fecha Recepcion : ${e.ffrecepcion || ""}</p>
                        </td>
                    </tr>
                `;
  });

  html += `
                        </tbody>
                    </table>
                </div>
            `;

  return html;
}

// Hacer la función disponible globalmente
window.mostrarDetalleEquipo = mostrarDetalleEquipo;

// Función para toggle detalles
window.toggleDetalles = function (row) {
  const detallesRow = row.nextElementSibling;
  const btn = row.querySelector(".expand-btn");

  if (detallesRow.style.display === "none") {
    detallesRow.style.display = "table-row";
    btn.innerHTML = `
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="18 15 12 9 6 15"></polyline>
                    </svg>
                    Ocultar
                `;
  } else {
    detallesRow.style.display = "none";
    btn.innerHTML = `
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                    Ver detalles
                `;
  }
};

//para llamar a los detalles del equipo
function mostrarDetalleEquipo(id) {
  document.getElementById("dialogo_registro").style.display = "block";

  let formData = new FormData();
  formData.append("codigo", id);

  fetch(RUTA + "activos/editaEquipo", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      //console.log(data);
      document.getElementById("codigo_interno").value = data.datos[0]["idprod"];
      document.getElementById("codigo_unidad").value = data.datos[0]["ncodmed"];
      document.getElementById("codigo_usuario").value = "";
      document.getElementById("codigo_registro").value = data.datos[0]["idreg"];
      document.getElementById("centro_costos").value =
        data.datos[0]["idcostos"];
      document.getElementById("codigoSearch").value = data.datos[0]["ccodprod"];
      document.getElementById("descripSearch").value =
        data.datos[0]["cdesprod"];
      document.getElementById("unidad").value = data.datos[0]["cabrevia"];
      document.getElementById("cantidad").value = data.datos[0]["ncant"];
      document.getElementById("serie").value = data.datos[0]["cserie"];
      document.getElementById("marca").value = data.datos[0]["cmarca"];
      document.getElementById("modelo").value = data.datos[0]["cmodelo"];
      document.getElementById("dni").value = data.datos[0]["casigna"];

      document.getElementById("area").value = data.datos[0]["carea"] || "";
      document.getElementById("fecha_asigna").value =
        data.datos[0]["fechasalida"];
      document.getElementById("frecuencia").value =
        data.datos[0]["nfrecuencia"];
      document.getElementById("fecha_calibra").value =
        data.datos[0]["ffcalibra"];
      document.getElementById("vence_calibra").value = data.datos[0]["ffvence"];
      document.getElementById("estado_actual").value = data.datos[0]["cestado"];
      document.getElementById("observa_estado").value =
        data.datos[0]["cobservaciones"];
      document.getElementById("guia_envio").value = data.datos[0]["cgrenvio"];
      document.getElementById("fecha_envio").value = data.datos[0]["cgrenvio"];
      document.getElementById("guia_recepcion").value =
        data.datos[0]["cgrrecepcion"];
      document.getElementById("fecha_recepcion").value =
        data.datos[0]["ffrecepcion"];
      document.getElementById("ubicacion").value = data.datos[0]["cubica"];
      document.getElementById("contenedor").value =
        data.datos[0]["ccontenedor"];
      document.getElementById("estante").value = data.datos[0]["cestante"];
      document.getElementById("letra").value = data.datos[0]["cletra"];
      document.getElementById("columna").value = data.datos[0]["ccolumna"];

      if (data.datos[0]["casigna"]) {
        document.getElementById("nombres").value =
          data.personal[0]["paterno"] ||
          "" + " " + data.personal[0]["materno"] ||
          "" + " " + data.personal[0]["nombres"] ||
          "";
        document.getElementById("cargo").value =
          data.personal[0]["cargo"].toUpperCase();
      }

      calcularVencimiento();
    });

  // Función para exportar a Excel
  function exportarAExcel(datos, nombreArchivo, hojas = null) {
    try {
      let wb;

      if (hojas) {
        // Múltiples hojas
        wb = XLSX.utils.book_new();
        hojas.forEach((hoja) => {
          const ws = XLSX.utils.json_to_sheet(hoja.datos);
          XLSX.utils.book_append_sheet(wb, ws, hoja.nombre);
        });
      } else {
        // Una sola hoja
        const ws = XLSX.utils.json_to_sheet(datos);
        wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, "Equipos");
      }

      // Generar archivo
      XLSX.writeFile(
        wb,
        `${nombreArchivo}_${new Date().toISOString().split("T")[0]}.xlsx`,
      );
      mostrarNotificacion(`✅ Archivo exportado: ${nombreArchivo}`);
    } catch (error) {
      console.error("Error al exportar:", error);
      mostrarNotificacion("❌ Error al exportar el archivo", "error");
    }
  }
}

async function excelJson(datos) {
  const workbook = new ExcelJS.Workbook();

  workbook.creator = "Sical";
  workbook.lastModifiedBy = "Sical";
  workbook.created = new Date();
  workbook.modified = new Date();

  const worksheet = workbook.addWorksheet("Cargo Plan");

  const columns = [
    { width: 10 },
    { width: 10 },
    { width: 15 },
    { width: 50 },
    { width: 30 },
    { width: 12 },
    { width: 15 },
    { width: 12 },
    { width: 15 },
    { width: 20 },
    { width: 20 },
    { width: 15 },
    { width: 15 },
    { width: 15 },
    { width: 20 },
    { width: 15 },
    { width: 70 },
    { width: 15 },
    { width: 12 },
    { width: 15 },
    { width: 15 },
    { width: 15 },
    { width: 15 },
    { width: 15 },
    { width: 15 },
    { width: 15 },
    { width: 15 },
    { width: 15 },
  ];

  // Establecer propiedades del título
  worksheet.mergeCells("A1:AW1");
  worksheet.getCell("A1").value = "CARGO PLAN";
  worksheet.getCell("A1").alignment = {
    horizontal: "center",
    vertical: "center",
  };
  worksheet.getRow(2).height = 60;

  worksheet.columns = columns;

  // Establecer valores de cabecera
  const headers = [
    "Item",
    "CODIGO SICAL",
    "DESCRIPCION DEL EQUIPO",
    "TIPO",
    "UND/MED",
    "CANTIDAD",
    "SERIE",
    "MARCA",
    "MODELO",
    "FRECUENCIA DE CALIBRACION",
    "FECHA DE CALIBRACION",
    "VENCIMIENTO DE CALIBRACION",
    "ESTADO ACTUAL",
    "ESTADO ACTUAL 1",
    "GR.ENVIO",
    "FECHA ENVIO",
    "GR. RECEPCION",
    "FECHA RECEPCION",
    "UBICACION ACTUAL",
    "DNI",
    "NOBRES Y APELLIDOS",
    "CARGO",
    "AREA",
    "FECHA DE ASIGANCION",
    "CONTENEDOR",
    "ESTANTE",
    "LETRA",
    "COLUNNA",
  ];

  /* worksheet.addRow(headers); */
  worksheet.getRow(2).values = headers;

  // Configurar wrapText para cada columna
  headers.forEach((header, index) => {
    const columnIndex = index + 1; // Las columnas en ExcelJS comienzan en 1
    worksheet.getColumn(columnIndex).alignment = { wrapText: true }; // Aplicar wrapText a toda la columna
  });

  let fila = 3;

  datos.forEach((dato, index) => {
    worksheet.addRow([
      index++,
      dato.ccodprod,
      dato.descripcion,
      "BIENES",
      "UND",
      "1",
      dato.cserie,
      dato.cmarca,
      dato.cmodelo,
      dato.frecuencia,
      dato.ffcalibra,
      dato.ffvence,
      dato.estado,
      dato.cobservaciones,
      dato.cgrenvio,
      dato.ffenvio,
      dato.cgrrecepcion,
      dato.cubica,
      dato.casigna,
      "",
      "",
      dato.carea,
      dato.ccontenedor,
      dato.cestante,
      dato.cletra,
      dato.ccolumna,
    ]);
  });

  // Exportar como archivo Blob
  const buffer = await workbook.xlsx.writeBuffer();
  const blob = new Blob([buffer], {
    type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
  });

  // Descargar archivo

  const url = URL.createObjectURL(blob);
  const a = document.createElement("a");
  a.href = url;
  a.download = "control_de_activos.xlsx";
  a.click();
  URL.revokeObjectURL(url);

  $("#esperar").css({ display: "none", opacity: "0" });
}
