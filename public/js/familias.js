$(function () {
  let accion = "";

  // =============================================
  // CONSTANTES Y ESTADO
  // =============================================
  const POR_PAGINA = 10;

  let paginaActual = 1;
  let totalRegistros = 0;
  let registroEdit = null;

  // Variables para los buscadores
  let grupoBuscador = null;
  let claseBuscador = null;

  // Variables para la lógica en memoria
  let jerarquiaCompleta = []; // Aquí se guarda toda la data: Grupos -> Clases -> Familias
  let clasePendienteDeEdicion = null; // Para saber qué clase pintar al editar

  // Inicialización
  $("#esperar").fadeOut(function () {
    consultarDatos(1);
    cargarJerarquiaEnMemoria(); // Nueva función que trae todo de golpe
  });

  // =============================================
  // 1. CARGAR TODA LA JERARQUÍA EN MEMORIA
  // =============================================
  function cargarJerarquiaEnMemoria() {
    console.log("⏳ Cargando toda la jerarquía en memoria...");

    // IMPORTANTE: Esta ruta debe devolver los grupos con sus items (clases)
    // Si tu ruta "familias/listaJerarquia" ya lo hace, la usamos.
    fetch(RUTA + "familias/listaJerarquia", { method: "POST" })
      .then((r) => {
        if (!r.ok) throw new Error("Error HTTP: " + r.status);
        return r.json();
      })
      .then((data) => {
        // Guardamos los datos en memoria
        jerarquiaCompleta = data.grupos || data.datos || data || [];
        console.log("✅ Jerarquía cargada en memoria. Grupos:", jerarquiaCompleta.length);

        // Llenar el select de grupos con esta data
        const select = document.getElementById("grupoSelect");
        let optionsHtml = '<option value="">Seleccione...</option>';

        jerarquiaCompleta.forEach((g) => {
          const cod = g.codigo || g.codgrupo || "";
          const nom = g.nombre || g.descripcion || "";
          const valorSafe = String(g.id).replace(/"/g, '&quot;');
          const textoSafe = `${cod} - ${nom}`.replace(/</g, '&lt;').replace(/>/g, '&gt;');
          optionsHtml += `<option value="${valorSafe}">${textoSafe}</option>`;
        });

        select.innerHTML = optionsHtml;

        // Inicializar el buscador de grupos
        grupoBuscador = new SelectBuscador("#grupoSelect", {
          placeholder: "Seleccione grupo...",
          textoBuscar: "Buscar grupo..."
        });

      })
      .catch((err) => {
        console.error("❌ Error cargando jerarquía:", err);
        mostrarMensaje("No se pudieron cargar los grupos", "error");
      });
  }

  // =============================================
  // 2. CAMBIO DE GRUPO (Lógica en Memoria)
  // =============================================
  $(document).on("change", "#grupoSelect", function () {
    const grupoId = $(this).val();
    const claseSelect = $("#claseSelect");

    console.log("🔥 Cambio de grupo. ID:", grupoId);

    if (claseBuscador) {
      claseBuscador.destruir();
      claseBuscador = null;
    }

    claseSelect.empty().append('<option value="">Seleccione...</option>');

    if (!grupoId) return;

    // Buscamos el grupo en el array en memoria
    const grupoEncontrado = jerarquiaCompleta.find(g => String(g.id) === String(grupoId));

    if (grupoEncontrado && grupoEncontrado.items && grupoEncontrado.items.length > 0) {
      let opciones = '<option value="">Seleccione...</option>';
      
      grupoEncontrado.items.forEach(function (c) {
        const cod = c.codigo || "";
        const nom = c.nombre || "";
        const val = String(c.id).replace(/"/g, '&quot;');
        const textoSafe = `${cod} - ${nom}`.replace(/</g, '&lt;').replace(/>/g, '&gt;');
        
        opciones += `<option value="${val}">${textoSafe}</option>`;
      });

      claseSelect.html(opciones);

      claseBuscador = new SelectBuscador("#claseSelect", {
        placeholder: "Seleccione clase...",
        textoBuscar: "Buscar clase..."
      });

      console.log("✅ Clases cargadas desde memoria. Total:", grupoEncontrado.items.length);

      // Si venimos de editar, pintamos la clase AHORA MISMO
      if (clasePendienteDeEdicion) {
        claseSelect.val(clasePendienteDeEdicion).trigger('change');
        let textoClase = claseSelect.find("option:selected").text();
        claseSelect.siblings('.select-buscador-display').find('.select-buscador-texto').text(textoClase).css('color', '#333');
        
        clasePendienteDeEdicion = null;
        $(".select-buscador-panel").removeClass("abierto");
      }

    } else {
      claseSelect.append('<option value="">No hay clases para este grupo</option>');
      claseBuscador = new SelectBuscador("#claseSelect", { placeholder: "Seleccione clase..." });
    }
  });

  // =============================================
  // 3. AUTO-GENERAR CÓDIGO AL SELECCIONAR CLASE
  // =============================================
  $(document).on("change", "#claseSelect", function () {
    const claseId = $(this).val();

    if (!claseId) {
      $("#codigoFamilia").val('');
      return;
    }

    if (accion === 'u' && registroEdit) return; 

    const claseCodigo = $(this).find("option:selected").text().split(" - ")[0]; 
    const codigoFinal = claseCodigo;

    const formData = new FormData();
    formData.append("grupo", $("#grupoSelect").val());
    formData.append("clase", claseId);

    fetch(RUTA + 'familias/nuevoCodigo', {
      method: 'POST',
      body: formData
    })
    .then(function (response) { return response.json(); })
    .then(function (data) {
      $("#codigoFamilia").val(codigoFinal + data);
    })
    .catch(function (err) { console.error("Error al generar código:", err); });
  });

  // =============================================
  // 4. EVENTOS DE UI
  // =============================================
  $("#nuevoRegistro").click(function (e) {
    e.preventDefault();
    $("#proceso").fadeIn();
    accion = "n";
    registroEdit = null;
    bloquearCampos(false);
    limpiarCampos();
    return false;
  });

  $("#consulta").keyup(function () {
    _this = this;
    buscar(_this);
  });

    $("#btnGuardar").click(function (e) {
    e.preventDefault();

    // 1. Capturamos los valores actuales
    const grupoId = $("#grupoSelect").val();
    const claseId = $("#claseSelect").val();
    const codigo = $("#codigoFamilia").val().trim();
    const nombre = $("#nombreInput").val().trim();

    // 2. Validamos
    if (!grupoId || !claseId || !codigo || !nombre) {
      mostrarMensaje("Por favor, complete todos los campos obligatorios", "mensaje_error");
      return;
    }

    // 3. Determinamos la URL
    const url = (accion === 'n') 
        ? RUTA + 'familias/nuevaFamilia' 
        : RUTA + 'familias/modificaFamilia';

    // 4. Preparamos los datos
    const formData = new FormData();
    formData.append("grupo", grupoId);
    formData.append("clase", claseId);
    formData.append("codigo", codigo);
    formData.append("nombre", nombre);

    if (accion === 'u' && registroEdit) {
        formData.append("id", registroEdit.id);
    }

    // 5. Enviamos al backend
    fetch(url, {
        method: "POST",
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        console.log("📦 Respuesta del servidor:", data);

        if (data.respuesta || data.success) {
            mostrarMensaje(data.mensaje || "Guardado con éxito", "mensaje_correcto");
            
            // Refrescamos la jerarquía en memoria (por si cambió algún nombre)
            cargarJerarquiaEnMemoria();
            
            // Cerramos el modal
            $("#proceso").fadeOut();
            
            // Recargamos la tabla en la página actual
            consultarDatos(paginaActual); 

            // ========================================================
            // TRUCO: Si fue edición, abrimos las carpetas automáticamente
            // ========================================================
            if (accion === 'u') {
                // Esperamos un momento a que la tabla se pinte en el DOM
                setTimeout(() => {
                    // Hacemos clic en el grupo para desplegar
                    $(`.grupo-row[data-target="g-${grupoId}"]`).trigger('click');
                    
                    // Esperamos un poquito más y hacemos clic en la clase
                    setTimeout(() => {
                        $(`.sub-grupo-row[data-target="c-${claseId}"]`).trigger('click');
                    }, 150);
                }, 500);
            }

        } else {
            mostrarMensaje(data.mensaje || "Error al guardar", "mensaje_error");
        }
    })
    .catch(error => {
        console.error("❌ Error al guardar:", error);
        mostrarMensaje("Error de conexión al guardar: " + error.message, "mensaje_error");
    });
  });

  $(".busqueda").on("keyup", function () {
    var value = $(this).val().toLowerCase();
    $(this).next().attr("id");
    let l = "#" + $(this).next().attr("id") + " li a";
    $(l).filter(function () {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
    });
  });

  $("#lnkCerrarModal,#btnCerrarModal").click(function (e) {
    e.preventDefault();
    closeModal();
    return false;
  });

  // =============================================
  // 5. FUNCIONES DE DATOS (Tabla y Paginación)
  // =============================================
  function consultarDatos(page = 1) {
    try {
      const tbody = document.getElementById("clasesTbody");

      tbody.innerHTML = `
            <tr class="cargando" style="text-align:center;">
                <td colspan="3"><i class="fas fa-spinner fa-spin" style="font-size:40px;"></i><br> Cargando...</td>
            </tr>`;

      const formData = new FormData();
      formData.append("page", page);
      formData.append("porPagina", POR_PAGINA);

      fetch(RUTA + "familias/listaJerarquia", {
        method: "POST",
        body: formData,
      })
      .then((r) => r.json())
      .then((data) => {
        if (data.error) throw new Error(data.error);

        if (data && data.grupos && data.grupos.length > 0) {
          totalRegistros = data.total_registros || 0;
          renderizar(data.grupos);
          actualizarPaginador(page);
        } else {
          tbody.innerHTML = `
                <tr class="vacio">
                    <td colspan="3" style="text-align:center;padding:30px;color:#999;">
                        <i class="fas fa-search" style="font-size:40px;display:block;margin-bottom:10px;"></i>
                        No se encontraron datos
                    </td>
                </tr>`;
          totalRegistros = 0;
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

  function renderizar(grupos) {
    let html = "";
    const tbody = document.getElementById("clasesTbody");

    if (!grupos || grupos.length === 0) {
      tbody.innerHTML = `<tr class="vacio"><td colspan="3" style="text-align:center;">No hay datos</td></tr>`;
      return;
    }

    grupos.forEach((grupo) => {
      const idGrupo = grupo.id;

      html += `<tr class="grupo-row" data-target="g-${idGrupo}">
                <td colspan="3" style="cursor:pointer;">
                    <span class="icon-wrapper icon-grupo">
                        <i class="${grupo.icon || "fa-solid fa-folder"}"></i>
                    </span>
                    <strong>${grupo.codigo}</strong> - ${grupo.nombre}
                    <span class="badge-grupo">${grupo.items.length}</span>
                    <i class="fas fa-chevron-down toggle-icon-clase" style="float:right;"></i>
                </td>
            </tr>`;

      (grupo.items || []).forEach((clase) => {
        const idClase = clase.id;

        html += `<tr class="sub-grupo-row oculto g-${idGrupo}" data-target="c-${idClase}">
        <td colspan="3" style="background:#f4f7fa; cursor:pointer; padding-left: 30px;">
            <span class="icon-wrapper icon-clase">
                <i class="${clase.icon || "fa-solid fa-layer-group"}"></i>
            </span>
            <span style="color:#1a2634; font-weight:600;">${clase.codigo} - ${clase.nombre}</span>
            <span class="badge-grupo" style="background:#e2e8f0; color:#4a5568;">${clase.items.length}</span>
            <i class="fas fa-chevron-down toggle-icon-clase" style="float:right;"></i>
        </td>
    </tr>`;

        (clase.items || []).forEach((familia) => {
          html += `<tr class="item-row oculto c-${idClase}">
            <td style="padding-left: 30px;">
                <span class="icon-wrapper icon-familia">
                    <i class="fas fa-tag"></i> 
                </span>
                <span class="code">${familia.codigo}</span>
            </td>
            <td>${familia.nombre}</td>
            <td style="text-align:center;">
                <button type="button" class="btn-icon" title="Editar" onclick="editarDesdeTabla(${familia.id})">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
            </td>
        </tr>`;
        });
      });
    });

    tbody.innerHTML = html;

    document.querySelectorAll(".grupo-row").forEach((row) => {
      row.addEventListener("click", function () {
        const target = this.dataset.target;
        const hijos = document.querySelectorAll(`.${target}`);
        const icon = this.querySelector(".toggle-icon-clase");
        hijos.forEach((hijo) => hijo.classList.toggle("oculto"));
        if (icon) icon.classList.toggle("cerrado");
      });
    });

    document.querySelectorAll(".sub-grupo-row").forEach((row) => {
      row.addEventListener("click", function (e) {
        e.stopPropagation();
        const target = this.dataset.target;
        const hijos = document.querySelectorAll(`.${target}`);
        const icon = this.querySelector(".toggle-icon-clase");
        hijos.forEach((hijo) => hijo.classList.toggle("oculto"));
        if (icon) icon.classList.toggle("cerrado");
      });
    });
  }

  function actualizarPaginador(pagina) {
    paginaActual = pagina;
    const totalPaginas = Math.ceil(totalRegistros / POR_PAGINA) || 1;

    const inicio = totalRegistros > 0 ? (pagina - 1) * POR_PAGINA + 1 : 0;
    const fin = Math.min(pagina * POR_PAGINA, totalRegistros);

    document.getElementById("infoPaginador").innerHTML =
      `Mostrando <strong>${inicio}</strong> - <strong>${fin}</strong> de <strong>${totalRegistros}</strong>`;

    let botones = "";

    botones += `<button class="page-btn" data-page="${pagina - 1}" ${pagina <= 1 ? "disabled" : ""}>
        <i class="fas fa-chevron-left"></i></button>`;

    let inicioPag = Math.max(1, pagina - 2);
    let finPag = Math.min(totalPaginas, pagina + 2);

    if (inicioPag > 1) {
      botones += `<button class="page-btn" data-page="1">1</button>`;
      if (inicioPag > 2)
        botones += `<button class="page-btn" disabled>...</button>`;
    }

    for (let i = inicioPag; i <= finPag; i++) {
      botones += `<button class="page-btn ${i === pagina ? "active" : ""}" data-page="${i}">${i}</button>`;
    }

    if (finPag < totalPaginas) {
      if (finPag < totalPaginas - 1)
        botones += `<button class="page-btn" disabled>...</button>`;
      botones += `<button class="page-btn" data-page="${totalPaginas}">${totalPaginas}</button>`;
    }

    botones += `<button class="page-btn" data-page="${pagina + 1}" ${pagina >= totalPaginas ? "disabled" : ""}>
        <i class="fas fa-chevron-right"></i></button>`;

    document.getElementById("botonesPaginador").innerHTML = botones;

    document.querySelectorAll(".page-btn").forEach((btn) => {
      btn.addEventListener("click", function () {
        if (this.disabled) return;
        const page = parseInt(this.dataset.page);
        if (page >= 1 && page <= totalPaginas) {
          consultarDatos(page);
        }
      });
    });
  }

  // =============================================
  // 6. EDITAR REGISTRO DESDE LA TABLA
  // =============================================
  window.editarDesdeTabla = function (idFamilia) {
    accion = "u"; 
    $("#proceso").fadeIn();
    limpiarCampos(); 

    const formData = new FormData();
    formData.append("id", idFamilia);

    fetch(RUTA + "familias/familiaId", {
      method: "POST",
      body: formData
    })
    .then(r => r.json())
    .then(data => {
      if (data.error) return;

      const fam = data.familia[0];
      registroEdit = { id: fam.ncodfamilia, codigo: fam.ccodcata };

      // 1. Llenar inputs
      $("#nombreInput").val(fam.cdescrip.trim());
      $("#codigoFamilia").val(fam.ccodcata.trim());

      // 2. Guardar clase pendiente
      clasePendienteDeEdicion = fam.ncodclase;

      // 3. FORZAR EL SELECT DE GRUPO
      const selectGrupo = document.getElementById("grupoSelect");
      
      // Asegurarnos de que NO esté deshabilitado antes de disparar
      selectGrupo.disabled = false; 
      
      // Asignar valor
      selectGrupo.value = fam.ncodgrupo;

      // Disparar el evento change de forma nativa y forzada
      const event = new Event('change', { bubbles: true, cancelable: true });
      selectGrupo.dispatchEvent(event);

      // 4. Actualizar el texto visual del grupo
      setTimeout(() => {
        let textoGrupo = $("#grupoSelect option:selected").text();
        $("#grupoSelect").siblings('.select-buscador-display').find('.select-buscador-texto').text(textoGrupo).css('color', '#333');
        bloquearCampos(true);
      }, 50);

    })
    .catch(err => {
      console.error("❌ Error:", err);
    });
  };

  // =============================================
  // 7. UTILIDADES (Limpiar y Cerrar)
  // =============================================
  function closeModal() {
    $("#proceso").fadeOut();
    limpiarCampos();
    accion = "";
    registroEdit = null;
  }

  function limpiarCampos() {
    $("#grupoSelect").val('');
    $("#grupoSelect").siblings('.select-buscador-display').find('.select-buscador-texto').text('Seleccione grupo...').css('color', '#999');

    $("#claseSelect").empty().append('<option value="">Seleccione...</option>');
    $("#claseSelect").siblings('.select-buscador-display').find('.select-buscador-texto').text('Seleccione clase...').css('color', '#999');

    $("#codigoFamilia").val('');
    $("#nombreInput").val('');
    
    $(".select-buscador-panel").removeClass("abierto");
  }

    function bloquearCampos(bloquear) {
    if (bloquear) {
      // Deshabilitar los selects reales
      $("#grupoSelect").prop("disabled", true);
      $("#claseSelect").prop("disabled", true);
      
      // Deshabilitar el input de código
      $("#codigoFamilia").prop("readonly", true).css("background-color", "#f3f4f6");
      
      // Hacer que los divs visuales del buscador parezcan deshabilitados
      $(".select-buscador-display").css({
        "background-color": "#f3f4f6",
        "cursor": "not-allowed",
        "pointer-events": "none", // Evita que se abra el panel
        "opacity": "0.7"
      });
      
    } else {
      // Habilitar los selects reales
      $("#grupoSelect").prop("disabled", false);
      $("#claseSelect").prop("disabled", false);
      
      // Habilitar el input de código (aunque normalmente es readonly por diseño)
      $("#codigoFamilia").prop("readonly", true).css("background-color", "");
      
      // Restaurar el estilo visual del buscador
      $(".select-buscador-display").css({
        "background-color": "#fff",
        "cursor": "pointer",
        "pointer-events": "auto",
        "opacity": "1"
      });
    }
  }

});