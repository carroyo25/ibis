$(function () {
  // =============================================
  // CONSTANTES Y ESTADO
  // =============================================
  const POR_PAGINA = 10;

  let paginaActual    = 1;
  let totalRegistros  = 0;
  let datosGrupos     = [];   // Agrupados
  let datosClases     = [];   // Planos (para editar)
  let estadosGrupo    = {};   // { idGrupo: true/false }
  let registroEdit    = null; // Registro que se está editando
  let accionPregunta  = null;
  let timerBusqueda   = null;

  const colorMap = {
    B01: "#2a7de1",
    B02: "#e67e22",
    B03: "#27ae60",
    B04: "#8e44ad",
    B05: "#e74c3c",
  };

  // =============================================
  // ARRANQUE
  // =============================================
  $("#esperar").fadeOut(function () {
    consultarDatos(1);
    llenarSelectGrupos();
  });

  // =============================================
  // EVENTOS
  // =============================================
  $("#nuevoRegistro").click(function (e) {
    e.preventDefault();
    abrirModalNuevo();
    return false;
  });

  $("#irInicio").click(function (e) {
    e.preventDefault();
    window.location.href = RUTA;
    return false;
  });

  // Búsqueda con debounce
  $("#consulta").on("input", function () {
    clearTimeout(timerBusqueda);
    timerBusqueda = setTimeout(() => consultarDatos(1), 350);
  });

  // Vista previa dinámica
  $("#codigoInput").on("input", function () {
    validarCodigo(this);
    actualizarPreview();
  });

  $("#grupoSelect").on("change", function () {
    actualizarPreview();
  });

  // Modal de pregunta
  $("#btnAceptarPregunta").on("click", function () {
    cerrarPregunta();
    if (typeof accionPregunta === "function") accionPregunta();
    accionPregunta = null;
  });

  $("#btnCancelarPregunta").on("click", function () {
    cerrarPregunta();
    accionPregunta = null;
  });

  // ESC cierra modales
  $(document).on("keydown", function (e) {
    if (e.key !== "Escape") return;
    if ($("#pregunta").is(":visible")) { cerrarPregunta(); accionPregunta = null; }
    else if ($("#proceso").is(":visible")) cerrarModal();
  });

  // =============================================
  // CONSULTAR DATOS (LISTADO)
  // =============================================
  function consultarDatos(page = 1) {
    try {
      const descripcion = document.getElementById("consulta").value;
      const tbody = document.getElementById("clasesTbody");

      tbody.innerHTML = `
        <tr class="cargando" style="text-align:center;">
          <td colspan="3"><i class="fas fa-spinner fa-spin" style="font-size:40px;"></i><br> Cargando...</td>
        </tr>`;

      const formData = new FormData();
      formData.append("descripcion", descripcion);
      formData.append("page", page);
      formData.append("porPagina", POR_PAGINA);

      fetch(RUTA + "clases/listarClases", {
        method: "POST",
        body: formData,
      })
        .then((r) => r.json())
        .then((data) => {
          if (data && data.grupos) {
            datosGrupos    = data.grupos;
            datosClases    = data.grupos.flatMap((g) =>
              (g.items || []).map((i) => ({ ...i, grupo: g }))
            );
            totalRegistros = data.total_clases || 0;
            renderizar(datosGrupos);
            actualizarPaginador(page);
          } else if (Array.isArray(data) && data.length > 0) {
            datosGrupos    = data;
            datosClases    = data.flatMap((g) =>
              (g.items || []).map((i) => ({ ...i, grupo: g }))
            );
            totalRegistros = data.reduce((acc, g) => acc + (g.items?.length || 0), 0);
            renderizar(data);
            actualizarPaginador(page);
          } else {
            tbody.innerHTML = `
              <tr class="vacio">
                <td colspan="3" style="text-align:center;padding:30px;color:#999;">
                  <i class="fas fa-search" style="font-size:40px;display:block;margin-bottom:10px;"></i>
                  No se encontraron clases
                </td>
              </tr>`;
            actualizarPaginador(1);
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          tbody.innerHTML = `
            <tr class="vacio">
              <td colspan="3" style="text-align:center;padding:30px;color:#e74c3c;">
                <i class="fas fa-exclamation-circle" style="font-size:40px;display:block;margin-bottom:10px;"></i>
                Error al cargar datos
              </td>
            </tr>`;
          mostrarMensaje("Error al cargar datos: " + error.message, "error");
        });
    } catch (error) {
      mostrarMensaje(error.message, "error");
    }
  }

  // =============================================
  // RENDERIZAR TABLA
  // =============================================
  function renderizar(grupos) {
    let html = "";

    if (!grupos || grupos.length === 0) {
      html = `<tr class="vacio">
        <td colspan="3" style="text-align:center;padding:30px;color:#999;">
          <i class="fas fa-search" style="font-size:40px;display:block;margin-bottom:10px;"></i>
          No hay datos
        </td></tr>`;
      document.getElementById("clasesTbody").innerHTML = html;
      return;
    }

    grupos.forEach((grupo) => {
      const idGrupo = grupo.id || grupo.codigo;
      const expandido = obtenerEstadoGrupo(idGrupo);
      const color = colorMap[grupo.codigo] || "#2d4054";

      html += `<tr class="grupo-row" data-grupo="${idGrupo}">
        <td colspan="3" style="cursor:pointer;">
          <i class="${grupo.icon || "fa-solid fa-folder"}" style="color:${color}"></i>
          <strong>${grupo.codigo || grupo.id}</strong> - ${grupo.nombre || grupo.descripcion || ""}
          <span class="badge-grupo">${grupo.items ? grupo.items.length : 0}</span>
          <i class="fas fa-chevron-down toggle-icon-clase ${expandido ? "" : "cerrado"}" style="float:right;"></i>
        </td>
      </tr>`;

      (grupo.items || []).forEach((item) => {
        const oculto = expandido ? "" : "oculto";
        const key = `${idGrupo}|${item.code}`;
        html += `<tr class="item-row ${oculto}" data-grupo="${idGrupo}" data-idclase="${item.code}" data-key="${key}">
          <td><span class="code">${item.code}</span></td>
          <td>${item.desc}</td>
          <td style="text-align:center;">
            <button type="button" class="btn-icon" title="Editar"
                    onclick="editarDesdeTabla('${key}')">
              <i class="fas fa-ellipsis-v"></i>
            </button>
          </td>
        </tr>`;
      });
    });

    document.getElementById("clasesTbody").innerHTML = html;

    // Listeners de expansión
    document.querySelectorAll(".grupo-row").forEach((row) => {
      row.addEventListener("click", function () {
        toggleGrupo(this.dataset.grupo);
      });
    });
  }

  // Exponer al window para los onclick inline
  window.editarDesdeTabla = function (key) {
    const [idGrupo, codigo] = key.split("|");
    abrirModalEditar(idGrupo, codigo);
  };

  // =============================================
  // PAGINADOR
  // =============================================
  function actualizarPaginador(pagina) {
    paginaActual = pagina;
    const totalPaginas = Math.ceil(totalRegistros / POR_PAGINA) || 1;

    const inicio = (pagina - 1) * POR_PAGINA + 1;
    const fin    = Math.min(pagina * POR_PAGINA, totalRegistros);
    document.getElementById("infoPaginador").innerHTML =
      `Mostrando <strong>${totalRegistros > 0 ? inicio : 0}</strong> - <strong>${fin}</strong> de <strong>${totalRegistros}</strong>`;

    let botones = "";
    botones += `<button class="page-btn" data-page="${pagina - 1}" ${pagina <= 1 ? "disabled" : ""}>
      <i class="fas fa-chevron-left"></i></button>`;

    let inicioPag = Math.max(1, pagina - 3);
    let finPag    = Math.min(totalPaginas, pagina + 3);

    if (finPag - inicioPag < 6) {
      if (inicioPag === 1) finPag = Math.min(7, totalPaginas);
      else if (finPag === totalPaginas) inicioPag = Math.max(1, totalPaginas - 6);
    }

    if (inicioPag > 1) {
      botones += `<button class="page-btn" data-page="1">1</button>`;
      if (inicioPag > 2) botones += `<button disabled>...</button>`;
    }

    for (let i = inicioPag; i <= finPag; i++) {
      botones += `<button class="page-btn ${i === pagina ? "active" : ""}" data-page="${i}">${i}</button>`;
    }

    if (finPag < totalPaginas) {
      if (finPag < totalPaginas - 1) botones += `<button disabled>...</button>`;
      botones += `<button class="page-btn" data-page="${totalPaginas}">${totalPaginas}</button>`;
    }

    botones += `<button class="page-btn" data-page="${pagina + 1}" ${pagina >= totalPaginas ? "disabled" : ""}>
      <i class="fas fa-chevron-right"></i></button>`;

    document.getElementById("botonesPaginador").innerHTML = botones;

    document.querySelectorAll(".page-btn").forEach((btn) => {
      btn.addEventListener("click", function () {
        const page = parseInt(this.dataset.page);
        irPagina(page);
      });
    });
  }

  function irPagina(pagina) {
    const totalPaginas = Math.ceil(totalRegistros / POR_PAGINA) || 1;
    if (pagina < 1 || pagina > totalPaginas) return;
    consultarDatos(pagina);
  }

  // =============================================
  // EXPANSIÓN DE GRUPOS
  // =============================================
  function obtenerEstadoGrupo(id) {
    if (estadosGrupo[id] === undefined) estadosGrupo[id] = true;
    return estadosGrupo[id];
  }

  function toggleGrupo(id) {
    estadosGrupo[id] = !estadosGrupo[id];
    const items = document.querySelectorAll(`tr.item-row[data-grupo="${id}"]`);
    const icon  = document.querySelector(`tr.grupo-row[data-grupo="${id}"] .toggle-icon-clase`);
    if (estadosGrupo[id]) {
      items.forEach((el) => el.classList.remove("oculto"));
      if (icon) icon.classList.remove("cerrado");
    } else {
      items.forEach((el) => el.classList.add("oculto"));
      if (icon) icon.classList.add("cerrado");
    }
  }

  // =============================================
  // LLENAR SELECT DE GRUPOS
  // =============================================
  function llenarSelectGrupos() {
    const select = document.getElementById("grupoSelect");
    select.innerHTML = '<option value="">Seleccione...</option>';

    fetch(RUTA + "clases/grupos", { method: "POST" })
      .then((r) => r.json())
      .then((data) => {
        const lista = data.datos || data.data || data || [];
        lista.forEach((g) => {
          const cod = g.codigo || g.codgrupo || g.id;
          const nom = g.descripcion || g.nombre || cod;
          select.innerHTML += `<option value="${g.id ?? cod}" data-codigo="${cod}">${cod} - ${nom}</option>`;
        });
      })
      .catch((err) => {
        console.error("Error cargando grupos:", err);
        mostrarMensaje("No se pudieron cargar los grupos", "error");
      });
  }

  // =============================================
  // MODAL: ABRIR / CERRAR
  // =============================================
  function abrirModalNuevo() {
    registroEdit = null;
    document.getElementById("editId").value = "";
    document.getElementById("codgrupo").value = "";
    document.getElementById("codclase").value = "";
    document.getElementById("grupoSelect").value = "";
    document.getElementById("codigoInput").value = "";
    document.getElementById("nombreInput").value = "";
    document.getElementById("codigoInput").classList.remove("error");
    document.getElementById("btnEliminar").style.display = "none";
    document.getElementById("labelGuardar").textContent = "Guardar";
    document.getElementById("modalTitulo").innerHTML =
      '<i class="fas fa-plus-circle"></i> Agregar Clase';
    document.getElementById("previewGrupo").textContent = "B??";
    document.getElementById("previewCodigo").textContent = "XXXX";

    $("#proceso").fadeIn();
    setTimeout(() => document.getElementById("grupoSelect").focus(), 300);
  }

  function abrirModalEditar(idGrupo, codigo) {
    const registro = datosClases.find(
      (c) => String(c.grupo.id || c.grupo.codigo) === String(idGrupo) && c.code === codigo
    );
    if (!registro) {
      mostrarMensaje("No se encontró el registro", "error");
      return;
    }

    registroEdit = registro;

    document.getElementById("editId").value      = registro.ncodclase || registro.id || "";
    document.getElementById("codgrupo").value    = idGrupo;
    document.getElementById("codclase").value    = codigo;

    const sel = document.getElementById("grupoSelect");
    sel.value = idGrupo;
    // Si no existe la opción aún, la agregamos
    if (sel.value !== String(idGrupo)) {
      const opt = document.createElement("option");
      opt.value = idGrupo;
      opt.dataset.codigo = registro.grupo.codigo || "";
      opt.textContent = `${registro.grupo.codigo || idGrupo} - ${registro.grupo.nombre || ""}`;
      sel.appendChild(opt);
      sel.value = idGrupo;
    }

    document.getElementById("codigoInput").value = registro.code || "";
    document.getElementById("nombreInput").value = registro.desc || "";

    document.getElementById("labelGuardar").textContent = "Actualizar";
    document.getElementById("modalTitulo").innerHTML =
      '<i class="fas fa-edit"></i> Editar Clase';
    document.getElementById("btnEliminar").style.display = "inline-block";

    actualizarPreview();
    $("#proceso").fadeIn();
  }

  function cerrarModal() {
    $("#proceso").fadeOut();
  }
  window.cerrarModal = cerrarModal; // Para el onclick del HTML

  // =============================================
  // VISTA PREVIA
  // =============================================
  function actualizarPreview() {
    const sel = document.getElementById("grupoSelect");
    const codGrupo = sel.options[sel.selectedIndex]?.dataset.codigo || "B??";
    const codClase = document.getElementById("codigoInput").value.toUpperCase() || "XXXX";
    document.getElementById("previewGrupo").textContent  = codGrupo;
    document.getElementById("previewCodigo").textContent = codClase;
  }

  // =============================================
  // VALIDAR CÓDIGO
  // =============================================
  function validarCodigo(input) {
    const val = input.value.toUpperCase();
    input.value = val;
    const regex = /^[A-Z]\d{4}$/;
    if (val === "" || regex.test(val)) {
      input.classList.remove("error");
      input.style.borderColor = "";
    } else {
      input.classList.add("error");
      input.style.borderColor = "#e74c3c";
    }
  }
  window.validarCodigo = validarCodigo; // Para el oninput del HTML

  // =============================================
  // GUARDAR (CREAR / ACTUALIZAR)
  // =============================================
  window.guardarClase = function (event) {
    event.preventDefault();

    const sel = document.getElementById("grupoSelect");
    const codGrupo = sel.value;
    const codClase = document.getElementById("codigoInput").value.trim().toUpperCase();
    const nombre   = document.getElementById("nombreInput").value.trim();

    if (!codGrupo)            return mostrarMensaje("Seleccione un grupo", "error");
    if (!/^[A-Z]\d{4}$/.test(codClase))
                              return mostrarMensaje("Código inválido (Ej: B0104)", "error");
    if (!nombre)              return mostrarMensaje("Ingrese el nombre", "error");

    const formData = new FormData();
    formData.append("codgrupo", codGrupo);
    formData.append("codclase", codClase);
    formData.append("nombre",   nombre);

    const esEdicion = !!registroEdit;

    if (esEdicion) {
      formData.append("accion", "actualizar");
      formData.append("id", document.getElementById("editId").value);
    } else {
      formData.append("accion", "crear");
    }

    $("#esperar").fadeIn();

    fetch(RUTA + "clases/guardaClase", {
      method: "POST",
      body: formData,
    })
      .then((r) => r.json())
      .then((data) => {
        $("#esperar").fadeOut();
        if (data && (data.ok || data.success)) {
          mostrarMensaje(data.mensaje, "mensaje_correcto");
          cerrarModal();
          consultarDatos(esEdicion ? paginaActual : 1);
        } else {
          mostrarMensaje(data.mensaje, "mensaje_error");
        }
      })
      .catch((err) => {
        $("#esperar").fadeOut();
        console.error(err);
        mostrarMensaje("Error al guardar", "error");
      });
  };

  // =============================================
  // ELIMINAR
  // =============================================
  window.eliminarClase = function () {
    if (!registroEdit) return;

    accionPregunta = function () {
      const formData = new FormData();
      formData.append("id", document.getElementById("editId").value);
      formData.append("codgrupo", registroEdit.grupo.id || registroEdit.grupo.codigo);
      formData.append("codclase", registroEdit.code);

      $("#esperar").fadeIn();

      fetch(RUTA + "clases/eliminarClase", {
        method: "POST",
        body: formData,
      })
        .then((r) => r.json())
        .then((data) => {
          $("#esperar").fadeOut();
          if (data && (data.ok || data.success)) {
            mostrarMensaje(data.mensaje || "Eliminado correctamente", "ok");
            cerrarModal();
            consultarDatos(paginaActual);
          } else {
            mostrarMensaje(data.mensaje || "No se pudo eliminar", "error");
          }
        })
        .catch((err) => {
          $("#esperar").fadeOut();
          console.error(err);
          mostrarMensaje("Error al eliminar", "error");
        });
    };

    abrirPregunta();
  };

  // =============================================
  // MODAL DE PREGUNTA
  // =============================================
  function abrirPregunta() {
    $("#pregunta").fadeIn();
  }
  
  function cerrarPregunta() {
    $("#pregunta").fadeOut();
  }


  $("#grupoSelect").change(function(e){
    e.preventDefault();

    const codigo = $(this).find(":selected").data("codigo");

    const formData = new FormData();
    formData.append("grupo",$(this).find(":selected").val());
  
    fetch(RUTA+'clases/siguienteClase',{
      method:'POST',
      body:formData
    })
    .then(response => response.json())
    .then(data => {
      $("#codigoInput").val(codigo+data[0]);
      actualizarPreview();
    })
    return false;  
  })
});