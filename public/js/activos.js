$(function () {
  let allrows = [];
  let consulta = "";

  $("#esperar").css({ display: "none", opacity: "0" });

  const modal_registro = document.getElementById("dialogo_registro");
  const modal_carga = document.getElementById("cargarArchivo");
  const modal_cargar_certificados = document.getElementById("cargarCertificados");
  const modal_qr = document.getElementById("vistaQR");

  const btnRegister = document.getElementById("nuevoRegistro");
  const btnExport = document.getElementById("excelFile");
  const btnCancelDialog = document.getElementById("btnCancelarDialogoActivos");
  const btnSave = document.getElementById("btnGrabarDialogoActivos");
  const btnImport = document.getElementById("importXls");
  const btnAcceptLoad = document.getElementById("btnAceptarCargar");
  const btnCancelLoad = document.getElementById("btnCancelarCargar");
  const btnConsult = document.getElementById("btnConsulta");
  const btnAtach = document.getElementById("btnAtachDialogoActivos");
  const btnQr = document.getElementById("btnQrDialogoActivos");
  const btnCancelQr = document.getElementById("btnCancelarQr");

  const btnAnulaAcepta = document.getElementById("btnAceptarAnula");
  const btnAnulaCancel = document.getElementById("btnCancelarAnula");
  const btnDeleteRegister = document.getElementById("btnDeleteRegister");

  const inputSearchCode = document.getElementById("codigoSearch");
  const inputSerie = document.getElementById("serie");
  const inputItemCode = document.getElementById("codigo_interno");
  const inputCalibra = document.getElementById("fecha_calibra");
  const inputEstado = document.getElementById("estado_actual");
  const inputUbicacion = document.getElementById("ubicacion");
  const inputImport = document.getElementById("fileInput");

  const sltCostos = document.getElementById("centro_costos");
  const sltCostosLoad = document.getElementById("loadProyect");
  const sltFrecuencia = document.getElementById("frecuencia");
  const sltCostosSearch = document.getElementById("costosSearch");

  const fmrActivos = document.getElementById("activos_form");

  const lnkLoad = document.getElementById("lnkLoad");
  const lnkCloseAdj = document.getElementById("cerrarCertificados");

  const canvas = document.getElementById("qrCodeModal");

  /*** nueva carga de archivos */
  let filesToUpload = [];
  let existingFiles = [];
  let uploadStats = {
    total: 0,
    completed: 0,
    success: 0,
    error: 0,
    exists: 0,
  };

  // Elementos DOM
  const uploadArea = document.getElementById("uploadArea");
  const fileInput = document.getElementById("fileCerts");
  const pendingFiles = document.getElementById("pendingFiles");
  const fileList = document.getElementById("fileList");
  const fileCount = document.getElementById("fileCount");
  const uploadBtn = document.getElementById("uploadBtn");
  const statusDiv = document.getElementById("status");
  const uploadedFiles = document.getElementById("uploadedFiles");
  const uploadSummary = document.getElementById("uploadSummary");
  const totalFilesCount = document.getElementById("totalFilesCount");
  const completedFilesCount = document.getElementById("completedFilesCount");
  const successFilesCount = document.getElementById("successFilesCount");
  const errorFilesCount = document.getElementById("errorFilesCount");

  uploadArea.addEventListener("dragover", (e) => {
    e.preventDefault();
    uploadArea.classList.add("drag-over");
  });

  uploadArea.addEventListener("dragleave", () => {
    uploadArea.classList.remove("drag-over");
  });

  uploadArea.addEventListener("drop", (e) => {
    e.preventDefault();
    uploadArea.classList.remove("drag-over");
    const files = Array.from(e.dataTransfer.files);
    addFiles(files);
  });

  // File input
  fileInput.addEventListener("change", (e) => {
    const files = Array.from(e.target.files);
    addFiles(files);
    fileInput.value = "";
  });

  // Upload button
  uploadBtn.addEventListener("click", () => {
    uploadAllFiles();
  });

  // Cargar archivos existentes en el servidor
  async function loadExistingFiles() {
    try {
      /*const response = await fetch("upload.php?action=list");
      existingFiles = await response.json();
      console.log("Archivos existentes:", existingFiles);*/
    } catch (error) {
      console.error("Error al cargar archivos existentes:", error);
      existingFiles = [];
    }
  }

  // Verificar si un archivo ya existe en el servidor
  function fileExistsOnServer(filename) {
    return existingFiles.some((existingFile) => {
      // Extraer el nombre original del archivo (sin timestamp y ID)
      const existingName = existingFile.replace(/_\d+_[a-f0-9]+\./, ".");
      const newName = filename;

      // Comparar nombres
      return existingName === newName || existingFile.includes(filename);
    });
  }

  // Función para agregar archivos con verificación de existencia
  function addFiles(newFiles) {
    let added = 0;
    let duplicates = 0;
    let existsOnServer = 0;

    newFiles.forEach((file) => {
      // Verificar si ya existe en la lista actual
      const existsInList = filesToUpload.some(
        (f) => f.name === file.name && f.size === file.size,
      );

      // Verificar si ya existe en el servidor
      const existsOnServerFlag = fileExistsOnServer(file.name);

      if (!existsInList && !existsOnServerFlag) {
        filesToUpload.push({
          file: file,
          exists: false,
        });
        added++;
      } else if (existsInList) {
        duplicates++;
      } else if (existsOnServerFlag) {
        existsOnServer++;
        // Mostrar mensaje de archivo existente
        showStatus(
          `⚠️ El archivo "${file.name}" ya existe en el servidor`,
          "warning",
        );
      }
    });

    if (added > 0) {
      updateFileList();
      showStatus(`✅ ${added} archivo(s) agregado(s)`, "success");
    }

    if (duplicates > 0) {
      showStatus(
        `⚠️ ${duplicates} archivo(s) ya estaban en la lista`,
        "warning",
      );
    }

    if (existsOnServer > 0) {
      showStatus(
        `⚠️ ${existsOnServer} archivo(s) ya existen en el servidor`,
        "warning",
      );
    }
  }

  // Actualizar lista de archivos
  function updateFileList() {
    if (filesToUpload.length === 0) {
      pendingFiles.style.display = "none";
      uploadBtn.disabled = true;
      uploadSummary.style.display = "none";
      return;
    }

    pendingFiles.style.display = "block";
    fileCount.textContent = filesToUpload.length;
    uploadBtn.disabled = false;

    fileList.innerHTML = "";

    filesToUpload.forEach((item, index) => {
      const fileItem = createFileItem(item.file, index, item.exists);
      fileList.appendChild(fileItem);
    });
  }

  // Crear elemento de archivo
  function createFileItem(file, index, exists) {
    const fileItem = document.createElement("div");
    fileItem.className = "file-item";
    if (exists) fileItem.classList.add("exists");
    fileItem.id = `file_${index}`;

    // Header con preview e info
    const header = createFileHeader(file, index, exists);

    // Barra de progreso (solo si no existe)
    let progressContainer = null;
    if (!exists) {
      progressContainer = createProgressContainer(index);
    } else {
      progressContainer = createExistsContainer();
    }

    fileItem.appendChild(header);
    fileItem.appendChild(progressContainer);

    return fileItem;
  }

  // Crear header del archivo
  function createFileHeader(file, index, exists) {
    const header = document.createElement("div");
    header.className = "file-header";

    // Preview
    const preview = createFilePreview(file);

    // Info
    const info = createFileInfo(file, exists);

    // Botón eliminar
    const removeBtn = document.createElement("button");
    removeBtn.className = "remove-btn";
    removeBtn.textContent = "Eliminar";
    removeBtn.onclick = () => removeFile(index);

    header.appendChild(preview);
    header.appendChild(info);
    header.appendChild(removeBtn);

    return header;
  }

  // Crear preview del archivo
  function createFilePreview(file) {
    const preview = document.createElement("div");
    preview.className = "file-preview";

    if (file.type.startsWith("image/")) {
      const img = document.createElement("img");
      const reader = new FileReader();
      reader.onload = (e) => {
        img.src = e.target.result;
      };
      reader.readAsDataURL(file);
      preview.appendChild(img);
    } else {
      preview.innerHTML = getFileIcon(file.name);
    }

    return preview;
  }

  // Crear información del archivo
  function createFileInfo(file, exists) {
    const info = document.createElement("div");
    info.className = "file-info";

    let existsBadge = "";
    if (exists) {
      existsBadge =
        '<span class="exists-badge">⚠️ Ya existe en servidor</span>';
    }

    info.innerHTML = `
        <div class="file-name">
            ${file.name}
            ${existsBadge}
        </div>
        <div class="file-meta">
            <span class="file-size">${formatFileSize(file.size)}</span>
            <span class="file-type">${getFileType(file.name)}</span>
        </div>
    `;
    return info;
  }

  // Crear contenedor para archivo existente
  function createExistsContainer() {
    const container = document.createElement("div");
    container.className = "file-progress-container";
    container.innerHTML = `
        <div class="progress-status">
            <span class="status-exists">⚠️ Este archivo ya existe en el servidor, no se subirá</span>
        </div>
    `;
    return container;
  }

  // Crear contenedor de progreso
  function createProgressContainer(index) {
    const progressContainer = document.createElement("div");
    progressContainer.className = "file-progress-container";
    progressContainer.id = `progress_${index}`;
    progressContainer.innerHTML = `
        <div class="progress-info">
            <span class="progress-label">📊 Progreso de subida</span>
            <span class="progress-percentage" id="percent_${index}">0%</span>
        </div>
        <div class="progress-bar-wrapper">
            <div class="progress-bar-fill" id="fill_${index}" style="width: 0%">0%</div>
        </div>
        <div class="progress-status" id="status_${index}">
            <span class="status-waiting">⏳ Esperando subida...</span>
        </div>
    `;
    return progressContainer;
  }

  // Obtener icono según tipo de archivo
  function getFileIcon(filename) {
    const ext = filename.split(".").pop().toLowerCase();
    const icons = {
      pdf: "📄",
      doc: "📝",
      docx: "📝",
      xls: "📊",
      xlsx: "📊",
      txt: "📃",
      zip: "🗜️",
      rar: "🗜️",
      mp3: "🎵",
      mp4: "🎬",
      jpg: "🖼️",
      jpeg: "🖼️",
      png: "🖼️",
      gif: "🖼️",
    };
    return `<div style="font-size: 30px;">${icons[ext] || "📁"}</div>`;
  }

  // Obtener tipo de archivo
  function getFileType(filename) {
    const ext = filename.split(".").pop().toLowerCase();
    const types = {
      jpg: "Imagen",
      jpeg: "Imagen",
      png: "Imagen",
      gif: "Imagen",
      pdf: "PDF",
      doc: "Documento",
      docx: "Documento",
      txt: "Texto",
      zip: "Comprimido",
      rar: "Comprimido",
      mp3: "Audio",
      mp4: "Video",
    };
    return types[ext] || "Archivo";
  }

  // Eliminar archivo
  function removeFile(index) {
    filesToUpload.splice(index, 1);
    updateFileList();
    showStatus("Archivo eliminado", "success");
  }

  // Actualizar progreso individual
  function updateFileProgress(fileIndex, percent, status = "uploading") {
    const percentSpan = document.getElementById(`percent_${fileIndex}`);
    const fillDiv = document.getElementById(`fill_${fileIndex}`);
    const statusSpan = document.getElementById(`status_${fileIndex}`);

    if (percentSpan) {
      percentSpan.textContent = `${Math.round(percent)}%`;
    }

    if (fillDiv) {
      fillDiv.style.width = `${percent}%`;
      fillDiv.textContent = `${Math.round(percent)}%`;

      if (status === "complete") {
        fillDiv.style.background = "linear-gradient(90deg, #4caf50, #8bc34a)";
      } else if (status === "error") {
        fillDiv.style.background = "#f44336";
      }
    }

    if (statusSpan) {
      if (status === "uploading") {
        statusSpan.innerHTML =
          '<span class="status-uploading">📤 Subiendo archivo... <span class="uploading-animation">⚡</span></span>';
      } else if (status === "complete") {
        statusSpan.innerHTML =
          '<span class="status-complete">✅ Subida completada exitosamente</span>';
      } else if (status === "error") {
        statusSpan.innerHTML =
          '<span class="status-error">❌ Error al subir el archivo</span>';
      }
    }
  }

  // Actualizar resumen
  function updateSummary() {
    totalFilesCount.textContent = uploadStats.total;
    completedFilesCount.textContent = uploadStats.completed;
    successFilesCount.textContent = uploadStats.success;
    errorFilesCount.textContent = uploadStats.error;

    if (uploadStats.total > 0) {
      uploadSummary.style.display = "block";
    }
  }

  // Subir todos los archivos
  async function uploadAllFiles() {
    // Filtrar solo archivos que no existen en el servidor
    const filesToUploadFiltered = filesToUpload.filter((item) => !item.exists);

    if (filesToUploadFiltered.length === 0) {
      showStatus("⚠️ No hay archivos nuevos para subir", "warning");
      return;
    }

    // Resetear estadísticas
    uploadStats = {
      total: filesToUploadFiltered.length,
      completed: 0,
      success: 0,
      error: 0,
      exists: filesToUpload.length - filesToUploadFiltered.length,
    };

    updateSummary();
    uploadBtn.disabled = true;

    // Subir archivos uno por uno
    for (let i = 0; i < filesToUploadFiltered.length; i++) {
      const item = filesToUploadFiltered[i];
      const originalIndex = filesToUpload.findIndex(
        (f) => f.file.name === item.file.name && f.file.size === item.file.size,
      );

     // updateFileProgress(originalIndex, 0, "uploading");

      const success = await uploadFile(item.file, originalIndex);

      if (success) {
        uploadStats.success++;
        //updateFileProgress(originalIndex, 100, "complete");
      } else {
        uploadStats.error++;
        //updateFileProgress(originalIndex, 0, "error");
      }

      uploadStats.completed++;
      updateSummary();
    }

    // Limpiar lista de archivos pendientes (solo los que no existen)
    filesToUpload = filesToUpload.filter((item) => item.exists);
    updateFileList();

    // Mostrar mensaje final
    let message = `Proceso completado: ${uploadStats.success} exitosos, ${uploadStats.error} fallidos`;
    if ( uploadStats.exists > 0 ) {
      message += `, ${uploadStats.exists} archivo(s) omitidos (ya existían)`;
    }
    showStatus(message, uploadStats.success > 0 ? "success" : "error");

    // Recargar archivos existentes
    await loadExistingFiles();
    //loadUploadedFiles();

    // Resetear botón
    uploadBtn.disabled = false;
  }

  // Subir archivo individual
  function uploadFile(file, index) {
    return new Promise((resolve) => {
      const formData = new FormData();
      formData.append("file", file);
      formData.append("codigo",document.getElementById("codigo_registro").value);

      const xhr = new XMLHttpRequest();

      xhr.upload.addEventListener("progress", (e) => {
        if (e.lengthComputable) {
          const percent = (e.loaded / e.total) * 100;
          //updateFileProgress(index, percent, "uploading");
        }
      });

      xhr.addEventListener("load", () => {
        if (xhr.status === 200) {
          try {
            const response = JSON.parse(xhr.responseText);
            resolve(response.success);
            uploadedFiles.innerHTML = response.error;
          } catch (e) {
            resolve(false);
          }
        } else {
          resolve(false);
        }
      });

      xhr.addEventListener("error", () => {
        resolve(false);
      });

      xhr.open("POST",RUTA+"activos/cargar");
      xhr.send(formData);
    });
  }

  // Mostrar mensaje de estado
  function showStatus(message, type) {
    statusDiv.textContent = message;
    statusDiv.className = `status ${type}`;
    statusDiv.style.display = "block";
  }

  // Formatear tamaño de archivo
  function formatFileSize(bytes) {
    if (bytes === 0) return "0 Bytes";
    const k = 1024;
    const sizes = ["Bytes", "KB", "MB", "GB"];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + " " + sizes[i];
  }

  /************************************ */

  btnRegister.addEventListener("click", (e) => {
    e.preventDefault();

    //llama el codigo del usuario que registra
    document.getElementById("codigo_usuario").value = document.getElementById("id_user").value;

    limpiarFormulario(true);

    modal_registro.style.display = "block";

    return false;
  });

  btnCancelDialog.addEventListener("click", (e) => {
    e.preventDefault();

    limpiarFormulario(true);
    btnConsult.click();
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
    let accion = null;

    if (document.getElementById("codigo_registro").value == "") {
      consulta = RUTA + "activos/registro";
      accion = true;
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

        if (accion) {
          document.getElementById("codigo_registro").value = data.ultimo_id;
        }

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
    formData.append(
      "descripcion",
      document.getElementById("descriptSearch").value,
    );
    formData.append("serie", document.getElementById("serieSearch").value);

    if (sltCostosSearch.value == "-1") {
      mostrarMensaje("Elija un centro de costos", "mensaje_error");
      return false;
    }

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

  btnQr.addEventListener("click", (e) => {
    e.preventDefault();

    modal_qr.style.display = "block";

    const estadoFisico =
      document.getElementById("estado_actual").value === "306"
        ? "CALIBRADO"
        : document.getElementById("estado_actual").value === "307"
          ? "VENCIDOP"
          : document.getElementById("estado_actual").value === "308"
            ? "POR CAILIBRAR"
            : document.getElementById("estado_actual").value === "309"
              ? "OPERATIVO"
              : document.getElementById("estado_actual").value === "310"
                ? "OTRO"
                : "N/A";

    const equipoData = {
      codigo: document.getElementById("codigoSearch").value,
      serie: document.getElementById("serie").value,
      descripcion: document.getElementById("descripSearch").value,
      marca: document.getElementById("marca").value,
      modelo: document.getElementById("modelo").value,
      vence: document.getElementById("vence_calibra").value,
      ubicacion: document.getElementById("ubicacion").value,
      estado: estadoFisico,
      asignado: document.getElementById("nombres").value,
      observaciones: document.getElementById("observa_estado").value,
    };

    // Crear la URL base de sepcon.net
    const baseUrl = "https://sicalsepcon.net/ibis/public/activos/";

    // Codificar los parámetros
    const params = new URLSearchParams({
      codigo: equipoData.codigo || "",
      serie: equipoData.serie || "",
      descripcion: (equipoData.descripcion || "").substring(0, 100),
      marca: equipoData.marca || "",
      modelo: equipoData.modelo || "",
      fecha_vencimiento: equipoData.vence || "",
      ubicacion: equipoData.ubicacion || "",
      estado: equipoData.estado || "",
      asignado: equipoData.asignado || "",
      observaciones: equipoData.observaciones || "",
    });

    // Generar hash de verificación
    const hashInput = `${equipoData.idprod}_${equipoData.serie}`;
    const hash = btoa(hashInput).substring(0, 10).replace(/=/g, "");

    // URL completa
    const urlCompleta = `${baseUrl}?${params.toString()}&v=${hash}`;

    // AQUÍ SE GENERA EL QR USANDO LA LIBRERÍA
    setTimeout(() => {
      // Obtener el elemento canvas por su ID
      const canvas = document.getElementById("qrCodeModal");

      // Usar la librería QRCode para generar el QR en el canvas
      QRCode.toCanvas(
        canvas,
        urlCompleta,
        {
          width: 300, // Ancho del QR en píxeles
          margin: 2, // Margen alrededor del QR
          errorCorrectionLevel: "M", // Nivel de corrección de errores
        },
        function (error) {
          if (error) {
            console.error("Error al generar QR:", error);
          }
        },
      );
    }, 100);

    return false;
  });

  btnCancelQr.addEventListener("click", (e) => {
    e.preventDefault();

    modal_qr.style.display = "none";

    return false;
  });


  btnDeleteRegister.addEventListener("click",(e)=>{
    e.preventDefault();

    document.getElementById("preguntaAnula").style.display = "block";

    return false;
  })

  btnAnulaAcepta.addEventListener("click",(e)=>{
    e.preventDefault();

    let formData = new FormData();
    formData.append('codigo',document.getElementById("codigo_registro").value);

    fetch(RUTA+"activos/anula",{
      method:'POST',
      body:formData
    })
    .then(response => response.json())
    .then(data=>{
      document.getElementById("preguntaAnula").style.display = "none";

      if (data.success){
        
        limpiarFormulario(true);
        btnConsult.click();
        modal_registro.style.display = "none";

        mostrarMensaje("registro eliminado","mensaje_correcto");
      }else{
        mostrarMensaje("error al eliminar el registro","mensaje_error");
      }
    })

    return false;
  })

  
  btnAnulaCancel.addEventListener("click",(e)=>{
    e.preventDefault();

    document.getElementById("preguntaAnula").style.display = "none";

    return false;
  })

  

  $("#closeAtach").click(function (e) {
    e.preventDefault();

    $(".ventanaAdjuntos iframe").attr("src", "");
    $("#vistaCertificados").fadeOut();

    return false;
  });

  $("#vistaCertificados").on("click", "a", function (e) {
    e.preventDefault();

    $(".ventanaAdjuntos iframe")
      .attr("src", "")
      .attr(
        "src",
        "public/documentos/certificados/activos/" + $(this).attr("href"),
      );

    return false;
  });

  lnkCloseAdj.addEventListener("click",(e)=>{
    e.preventDefault();

    modal_cargar_certificados.style.display = "none";
    uploadedFiles.innerHTML="";
    statusDiv.innerHTML = "";
    fileList.innerHTML = "";
    fileCount.innerHTML = "";
    document.getElementById("filesCounter").innerHTML = "";
    filesToUpload = [];

    return false;
  })
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

  if  (hoy < vencimiento ) {
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
    document.getElementById("codigo_registro").value = "";
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
                                <th>Certificados:</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

  equipos.forEach((e) => {
    const dias = calcularDiasVencimiento(e.ffvence);
    const estado = getEstadoEquipo(dias);
    const claseDias =
      dias < 0 ? "vencido" : dias <= 15 ? "por-vencer" : "vigente";
    const archivos = e.archivos > 0 ? "📰" : "";

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
                        <td>
                          <span class="serie-link" data-tooltip="Click para ver certificados" onclick="mostrarCertificados(${e.idreg})">
                            <strong>${archivos}</strong>
                          </span>
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
window.mostrarCertificados = mostrarCertificados;

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
      document.getElementById("estado_actual").value = String(data.datos[0]["cestado"]).trim();
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

      //calcularVencimiento();

      document.getElementById("dialogo_registro").style.display = "block";
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

function mostrarCertificados(codigo_equipo) {
  try {
    const modal_vista_certificados =
      document.getElementById("vistaCertificados");

    $.post(
      RUTA + "ingresoedit/verAdjuntos",
      { id: codigo_equipo, tipo: "CER" },
      function (data, textStatus, jqXHR) {
        $("#listaAdjuntos").empty().append(data.adjuntos);
        $("#listaAdjuntos li a:nth-child(2)").hide();

        modal_vista_certificados.style.display = "block";
      },
      "json",
    );
  } catch (error) {
    console.log(error);
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

/************* funciones para cargar archivos */
