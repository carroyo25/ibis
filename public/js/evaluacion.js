$(function () {
  let evaluar = false;

  // Variable global para almacenar los archivos
  let archivosSeleccionados = [];
  let totalArchivos = 0;

  //===== VARIABLES PDF ========//
  let pdfDoc = null;
  let pageNum = 1;
  let pageRendering = false;
  let pageNumPending = null;
  let scale = 1.5;

  const body = document.querySelector("#tablaPrincipal tbody");

  let listItemFinal = null,
    estoyPidiendo = false;

  const observandoListItem = (listItem) => {
    if (listItem[0].isIntersecting) {
      query();
    }
  };

  const settings = {
    threshold: 1,
  };

  let observador = new IntersectionObserver(observandoListItem, settings);

  const query = async () => {
    if (estoyPidiendo) return;
    estoyPidiendo = true;
    let pagina = parseInt(body.dataset.p) || 1;
    const FD = new FormData();
    FD.append("pagina", pagina);

    const r = await fetch(RUTA + "evaluacion/listaScroll", {
      method: "POST",
      body: FD,
    });

    let item = 0;

    const j = await r.json();
    j[0].filas.forEach((i) => {
      const tr = document.createElement("tr");

      tr.innerHTML = `<td class="textoCentro">${i.cnumero}</td>
                            <td class="textoCentro">${i.emision}</td>
                            <td class="pl20px">${i.concepto}</td>
                            <td class="pl20px">${i.ccodproy}</td>
                            <td class="pl20px">${i.area}</td>
                            <td class="pl20px">${i.proveedor}</td>`;

      tr.classList.add("pointer");
      tr.dataset.indice = i.id_regmov;
      tr.dataset.tipo = i.ntipmov;
      tr.dataset.rol = i.nrol;

      body.appendChild(tr);
    });

    if (listItemFinal) {
      observador.unobserve(listItemFinal);
    }

    if (j[0].quedan) {
      //devuelve falso si ya no quedan mas registros
      listItemFinal = body.lastElementChild.previousElementSibling;
      observador.observe(listItemFinal);
      estoyPidiendo = false;
      body.dataset.p = ++pagina;
    }
  };

  query();
  $("#esperar").fadeOut();

  $("#tablaPrincipal tbody").on("click", "tr", function (e) {
    e.preventDefault();

    $("#esperar").fadeIn();

    $.post(
      RUTA + "evaluacion/criterios",
      {
        id: $(this).data("indice"),
        tipo: $(this).data("tipo"),
        rol: $("#rol_user").val(),
      },
      function (data, textStatus, jqXHR) {
        $("#codigo_orden").val(data.cabecera[0].id_regmov);
        $("#codigo_rol").val(data.cabecera[0].nrol);
        $("#codigo_entidad").val(data.cabecera[0].id_centi);
        $("#tipo_orden").val(data.cabecera[0].idregmov);
        $("#numero").val(data.cabecera[0].cnumero);
        $("#emision").val(data.cabecera[0].ffechadoc);
        $("#costos").val(data.cabecera[0].proyecto);
        $("#detalle").val(data.cabecera[0].concepto);
        $("#entidad").val(data.cabecera[0].entidad);

        evaluar = data.evaluada;

        $("#tablaDetalles tbody").empty().append(data.criterios);

        let totalOrden = sumarTotales($("#tablaDetalles tbody tr"));

        $("#puntaje").val(totalOrden.toFixed(0));

        $("#proceso").fadeIn();

        $("#esperar").fadeOut();
      },
      "json",
    );

    return false;
  });

  $("#saveOrden").click(function (e) {
    e.preventDefault();

    try {
      if (evaluar) throw "Ya registro la evaluación";
      if (checkCantTablesMinMax($("#tablaDetalles tbody > tr"), 2))
        throw "El puntaje debe estar entre 1 y 5";

      $.post(
        RUTA + "evaluacion/evaluar",
        { items: JSON.stringify(items()) },
        function (data, textStatus, jqXHR) {
          mostrarMensaje(data.mensaje, data.clase);
          $("#cerrarVentana").trigger("click");
        },
        "json",
      );
    } catch (error) {
      mostrarMensaje(error, "mensaje_error");
    }

    return false;
  });

  $("#cerrarVentana").click(function (e) {
    e.preventDefault();

    $(".filtro").hide();
    $("#proceso").fadeOut();

    return false;
  });

  $("#btnConsulta").click(function (e) {
    e.preventDefault();

    let str = $("#formConsulta").serialize();

    $.post(
      RUTA + "evaluacion/listaFiltrada",
      str,
      function (data, textStatus, jqXHR) {
        $("#tablaPrincipal tbody").empty().append(data);
      },
      "text",
    );

    return false;
  });

  $("#tablaDetalles tbody").on("blur", "input", function (e) {
    if ($(this).val() < 1 || $(this).val() > 5) {
      mostrarMensaje("Valor de calificacion incorrecto...", "mensaje_error");
      $(this).val(5);
    }
  });

  $(".cabezaModulo,.barraTrabajo").on("click", "*", function () {
    $(".filtro").fadeOut();
  });

  //**************************************************/
  //******** CONSULTA LOS ADJUNTOS ANTERIORES ********/
  //**************************************************/
  $("#evalAtach").click(function (e) {
    e.preventDefault();

    try {
      const codigo = $("#codigo_orden").val();
      renderPrevAtachs(codigo);
    } catch (error) {
      mostrarMensaje(error.message, "mensaje_error");
    }

    return false;
  });

  // ============ DRAG & DROP ============
  const dropZone = document.getElementById("dropZone");
  const fileInput = document.getElementById("fileInput");

  dropZone.addEventListener("dragover", function (e) {
    e.preventDefault();
    this.classList.add("dragover");
  });

  dropZone.addEventListener("dragleave", function (e) {
    e.preventDefault();
    this.classList.remove("dragover");
  });

  dropZone.addEventListener("drop", function (e) {
    e.preventDefault();
    this.classList.remove("dragover");
    procesarArchivos(e.dataTransfer.files);
  });

  fileInput.addEventListener("change", function () {
    procesarArchivos(this.files);
    this.value = "";
  });

  // ============ SUBIR ARCHIVOS ============
  $("#subirArchivos").on("click", function () {
    // Buscar archivos con clase 'completado'
    const archivosItems = document.querySelectorAll(
      "#listaArchivos .archivo-item.completado",
    );

    // Ver cada item
    archivosItems.forEach((item, i) => {
      const idx = parseInt(item.dataset.index);
    });

    if (archivosItems.length === 0) {
      alert("No hay archivos listos para subir");
      return;
    }

    // Crear FormData
    const formData = new FormData();

    // Agregar los archivos al FormData
    let agregados = 0;
    archivosItems.forEach((item) => {
      const fileIndex = parseInt(item.dataset.index);
      const file = archivosSeleccionados[fileIndex];
      if (file) {
        formData.append("file[]", file);
        agregados++;
        console.log("✅ Archivo agregado al FormData:", file.name);
      } else {
        console.log("❌ No se encontró archivo para índice:", fileIndex);
        console.log(
          "   archivosSeleccionados en ese índice:",
          archivosSeleccionados[fileIndex],
        );
      }
    });

    if (agregados === 0) {
      mostrarMensaje(
        "No se pudo agregar ningún archivo al FormData",
        "mensaje_error",
      );
      btn.html('<i class="fas fa-upload"></i> Subir Archivos');
      btn.prop("disabled", false);
      return;
    }

    // Agregar el código de la orden
    formData.append("codigo", $("#codigo_orden").val() || "001503");

    // Verificar FormData
    for (let pair of formData.entries()) {
      if (pair[1] instanceof File) {
        console.log("File:", pair[0], pair[1].name, pair[1].size);
      } else {
        console.log("Otro:", pair[0], pair[1]);
      }
    }

    const btn = $(this);
    btn.html('<i class="fas fa-spinner fa-spin"></i> Subiendo...');
    btn.prop("disabled", true);

    // ============ ENVÍO AL SERVIDOR ============
    fetch(RUTA + "evaluacion/subeArchivos", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data && data.subidos > 0) {
          mostrarMensaje(
            data.mensaje || "Archivos subidos correctamente",
            "mensaje_correcto",
          );

          if (data.adjuntos !== undefined) {
            $("#contadorAdjuntos").text(data.adjuntos);
          }

          setTimeout(() => {
            document.getElementById("listaArchivos").innerHTML = "";
            archivosSeleccionados = [];
            totalArchivos = 0;
            cerrarModalAdjuntos();
          }, 1500);

          renderPrevAtachs($("#codigo_orden").val());
        } else {
          mostrarMensaje(
            data.mensaje || "Error al subir archivos",
            "mensaje_error",
          );
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        mostrarMensaje("Error de conexión: " + error.message, "mensaje_error");
      })
      .finally(() => {
        btn.html('<i class="fas fa-upload"></i> Subir Archivos');
        btn.prop("disabled", false);
      });
  });

  // ============ ELIMINAR ARCHIVO SUBIDO ============
  function eliminarArchivoSubido(id, elemento) {
    try {
      const formData = new FormData();
      formData.append("id", id);
      formData.append("modulo", "ORD");

      fetch(RUTA + "orden/anulaAdjunto", {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            elemento.remove();
            const restantes = document.querySelectorAll(
              "#listaSubidos .archivo-subido",
            ).length;
            $("#contadorAdjuntos").text(restantes);
            if (restantes === 0) {
              document.getElementById("listaSubidos").innerHTML =
                '<div class="mensaje-vacio">No hay archivos adjuntos</div>';
            }
            mostrarMensaje(
              "Archivo eliminado correctamente",
              "mensaje_success",
            );
          } else {
            mostrarMensaje(
              data.mensaje || "Error al eliminar archivo",
              "mensaje_error",
            );
          }
        });
    } catch (error) {
      mostrarMensaje(error.message, "menesaje_error");
    }
  }

  /*funciones del modal*/
  // ============ CERRAR MODAL ============
  function cerrarModalAdjuntos() {
    $("#archivos").fadeOut();
  }

  function procesarArchivos(files) {
    const lista = document.getElementById("listaArchivos");
    console.log("Procesando archivos:", files.length);

    Array.from(files).forEach((file) => {
      // Validar tamaño (10MB)
      if (file.size > 10 * 1024 * 1024) {
        alert(`El archivo "${file.name}" excede los 10MB`);
        return;
      }

      // Validar extensiones
      const ext = file.name.split(".").pop().toLowerCase();
      const permitidas = ["pdf", "jpg", "jpeg", "png", "doc", "docx"];
      if (!permitidas.includes(ext)) {
        alert(`El archivo "${file.name}" no tiene una extensión permitida`);
        return;
      }

      // Guardar archivo en el array
      archivosSeleccionados.push(file);
      const index = archivosSeleccionados.length - 1;

      const iconos = {
        pdf: "fa-file-pdf",
        jpg: "fa-file-image",
        jpeg: "fa-file-image",
        png: "fa-file-image",
        doc: "fa-file-word",
        docx: "fa-file-word",
      };
      const colores = {
        pdf: "#ea4335",
        jpg: "#fbbc04",
        jpeg: "#fbbc04",
        png: "#fbbc04",
        doc: "#1a73e8",
        docx: "#1a73e8",
      };

      const tamaño =
        file.size < 1024 * 1024
          ? (file.size / 1024).toFixed(1) + " KB"
          : (file.size / (1024 * 1024)).toFixed(1) + " MB";

      const item = document.createElement("div");
      item.className = "archivo-item completado";
      item.dataset.index = index; // <-- ESTO ES LO QUE FALTA
      item.innerHTML = `
            <i class="fas ${iconos[ext] || "fa-file"}" style="color:${colores[ext] || "#5f6368"};"></i>
            <span class="archivo-nombre">${file.name}</span>
            <span class="archivo-tamaño">${tamaño}</span>
            <button class="btn-eliminar"><i class="fas fa-trash"></i></button>
        `;

      // Eliminar archivo
      item
        .querySelector(".btn-eliminar")
        .addEventListener("click", function (e) {
          e.stopPropagation();
          if (confirm(`¿Eliminar "${file.name}"?`)) {
            const idx = parseInt(item.dataset.index);
            archivosSeleccionados.splice(idx, 1);
            document
              .querySelectorAll("#listaArchivos .archivo-item")
              .forEach((el, i) => {
                el.dataset.index = i;
              });
            item.remove();
            totalArchivos--;
            actualizarContador();
          }
        });

      lista.appendChild(item);
      totalArchivos++;
      actualizarContador();
      console.log("Archivo agregado con data-index:", index, file.name);
    });
  }

  // ============ ACTUALIZAR CONTADOR ============
  function actualizarContador() {
    $("#contadorArchivos").text(totalArchivos);
  }

  $("#cancelarModal, #lnkCerrarAdjuntos").on("click", cerrarModalAdjuntos);

  //**************************************************/
  //******** VISTA PREVIA DOCUMENTOS          ********/
  //**************************************************/
  $("#listaSubidos").on("click", ".archivo-subido", function (e) {
    e.preventDefault();

    const pdfUrl =
      RUTA +
      "public/documentos/evaluaciones/adjuntos/" +
      $(this).data("filename");
    console.log(pdfUrl);
    cargarPDF(pdfUrl);

    $("#modalVistaPrevia").fadeIn();

    return false;
  });

  // Función para cargar PDF
  function cargarPDF(url) {
    const loading = document.getElementById("pdfLoading");
    const canvas = document.getElementById("pdfCanvas");
    const error = document.getElementById("pdfError");

    loading.style.display = "block";
    canvas.style.display = "none";
    error.style.display = "none";

    pdfjsLib.GlobalWorkerOptions.workerSrc =
      "https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js";

    pdfjsLib
      .getDocument(url)
      .promise.then(function (pdf) {
        pdfDoc = pdf;
        pageNum = 1;
        loading.style.display = "none";
        canvas.style.display = "block";
        document.getElementById("pdfPageInfo").textContent =
          `Página 1 de ${pdfDoc.numPages}`;
        renderPage(pageNum);
        $("#pdfPrevPage").prop("disabled", true);
        $("#pdfNextPage").prop("disabled", pdfDoc.numPages <= 1);
      })
      .catch(function (err) {
        console.error("Error al cargar PDF:", err);
        loading.style.display = "none";
        error.style.display = "block";
        error.querySelector("p").textContent = "Error: " + err.message;
      });
  }

  // Renderizar página
  function renderPage(num) {
    if (pageRendering) {
      pageNumPending = num;
      return;
    }
    pdfDoc.getPage(num).then(function (page) {
      const viewport = page.getViewport({ scale: scale });
      const canvas = document.getElementById("pdfCanvas");
      const context = canvas.getContext("2d");
      canvas.width = viewport.width;
      canvas.height = viewport.height;
      const renderContext = {
        canvasContext: context,
        viewport: viewport,
      };
      pageRendering = true;
      page.render(renderContext).promise.then(function () {
        pageRendering = false;
        if (pageNumPending !== null) {
          renderPage(pageNumPending);
          pageNumPending = null;
        }
      });
      $("#pdfPrevPage").prop("disabled", num <= 1);
      $("#pdfNextPage").prop("disabled", num >= pdfDoc.numPages);
      document.getElementById("pdfPageInfo").textContent =
        `Página ${num} de ${pdfDoc.numPages}`;
    });
  }

  function cambiarPagina(delta) {
    const newPage = pageNum + delta;
    if (newPage >= 1 && newPage <= pdfDoc.numPages) {
      pageNum = newPage;
      renderPage(pageNum);
    }
  }

  // Controles
  $("#pdfPrevPage").on("click", function () {
    cambiarPagina(-1);
  });
  $("#pdfNextPage").on("click", function () {
    cambiarPagina(1);
  });

  $("#pdfZoomIn").on("click", function () {
    if (scale < 3) {
      scale += 0.25;
      renderPage(pageNum);
    }
  });

  $("#pdfZoomOut").on("click", function () {
    if (scale > 0.5) {
      scale -= 0.25;
      renderPage(pageNum);
    }
  });

  // Cerrar modal
  function cerrarVistaPrevia() {
    $("#modalVistaPrevia").fadeOut();
    pdfDoc = null;
    const canvas = document.getElementById("pdfCanvas");
    const context = canvas.getContext("2d");
    context.clearRect(0, 0, canvas.width, canvas.height);
    canvas.style.display = "none";
    document.getElementById("pdfPageInfo").textContent = "Página 0 de 0";
  }

  $("#cerrarVistaPrevia, #cerrarVistaPreviaBtn").on("click", cerrarVistaPrevia);

  $("#modalVistaPrevia").on("click", function (e) {
    if (e.target === this) cerrarVistaPrevia();
  });

  $(document).on("keydown", function (e) {
    if (e.key === "Escape" && $("#modalVistaPrevia").hasClass("active")) {
      cerrarVistaPrevia();
    }
  });

  function renderPrevAtachs(codigo) {
    // Mostrar loading
    const lista = document.getElementById("listaSubidos");
    lista.innerHTML =
      '<div class="mensaje-vacio"><i class="fas fa-spinner fa-spin"></i> Cargando archivos...</div>';

    // Enviar petición POST al servidor
    $.post(
      RUTA + "evaluacion/listarAdjuntosEvaluacion",
      {
        orden: codigo,
        tipo: "EVA",
      },
      function (data) {
        // Limpiar lista
        lista.innerHTML = "";

        if (data.success && data.lista && data.lista.length > 0) {
          // Mostrar archivos
          data.lista.forEach(function (archivo) {
            const item = document.createElement("div");
            item.className = "archivo-subido";
            item.dataset.id = archivo.idreg;
            item.dataset.filename = archivo.creferencia;

            item.innerHTML = `
                            ${archivo.icono}
                            <span class="nombre" title="${archivo.cdocumento}">${archivo.cdocumento}</span>
                            <span class="fecha">${archivo.dfechareg || ""}</span>
                            <button class="btn-eliminar-subido" data-id="${archivo.idreg}" title="Eliminar archivo">
                                <i class="fas fa-trash"></i>
                            </button>`;

            // Evento para eliminar
            item
              .querySelector(".btn-eliminar-subido")
              .addEventListener("click", function (e) {
                e.stopPropagation();
                if (confirm('¿Eliminar "' + archivo.cdocumento + '"?')) {
                  eliminarArchivoSubido(archivo.idreg, item);
                }
              });

            lista.appendChild(item);
          });

          // Actualizar contador
          $("#contadorAdjuntos").text(data.total);
        } else {
          lista.innerHTML =
            '<div class="mensaje-vacio">No hay archivos adjuntos</div>';
          $("#contadorAdjuntos").text(0);
        }

        // Mostrar el contenedor
        $("#archivos").fadeIn();
      },
      "json",
    ).fail(function (xhr, status, error) {
      console.error("Error:", error);
      lista.innerHTML =
        '<div class="mensaje-vacio">Error al cargar archivos</div>';
      mostrarMensaje("Error al cargar archivos", "mensaje_error");
    });
  }
});

items = () => {
  DATA = [];
  let TABLA = $("#tablaDetalles tbody >tr");

  TABLA.each(function () {
    let REG = $(this).data("reg"),
      TIPO = $(this).data("tipo"),
      PUNTAJE = $(this).find("td").eq(2).children().val(),
      PESO = $(this).data("peso"),
      ENTIDAD = $("#codigo_entidad").val(),
      ORDEN = $("#codigo_orden").val(),
      USUARIO = $("#id_user").val(),
      ROL = $("#codigo_rol").val();

    item = {};

    item["reg"] = REG;
    item["tipo"] = TIPO;
    item["puntaje"] = PUNTAJE;
    item["peso"] = PESO;
    item["entidad"] = ENTIDAD;
    item["orden"] = ORDEN;
    item["usuario"] = USUARIO;
    item["rol"] = ROL;

    DATA.push(item);
  });

  return DATA;
};
