$(function () {
  let accion = "";

  // =============================================
  // CONSTANTES Y ESTADO
  // =============================================
  const POR_PAGINA = 10;

  let paginaActual = 1;
  let totalRegistros = 0;
  let datosGrupos = []; // Agrupados
  let datosClases = []; // Planos (para editar)
  let estadosGrupo = {}; // { idGrupo: true/false }
  let registroEdit = null; // Registro que se está editando
  let accionPregunta = null;
  let timerBusqueda = null;

  const colorMap = {
    B01: "#2a7de1",
    B02: "#e67e22",
    B03: "#27ae60",
    B04: "#8e44ad",
    B05: "#e74c3c",
  };

  $("#esperar").fadeOut(function () {
    consultarDatos(1);
    llenarSelectGrupos();
  });

  // =============================================
  // 2. ESCUCHAR EL CAMBIO DE GRUPO (Corregido con jQuery)
  // =============================================
  // Usamos "on" delegado al documento para que funcione incluso si el select
  // se crea dinámicamente al abrir el modal.
  $(document).on("change", "#grupoSelect", function () {
    const grupoId = $(this).val();
    const claseSelect = $("#claseSelect");

    // Limpiamos el select de clases
    claseSelect.html('<option value="">Seleccione...</option>');

    if (!grupoId) return;

    console.log("🔄 Buscando clases para el grupo ID:", grupoId);

    const formData = new FormData();
    formData.append("grupo", grupoId);

    fetch(RUTA + "familias/clasesGrupo", {
      method: "POST",
      body: formData,
    })
      .then((r) => {
        if (!r.ok) throw new Error("Error HTTP: " + r.status);
        return r.json();
      })
      .then((data) => {
        console.log("✅ Respuesta del servidor:", data);

        if (data.error) {
          mostrarMensaje("❌ Error del backend:", mensaje_error);
          claseSelect.html('<option value="">Error al cargar</option>');
          return;
        }

        const clases = data.datos || data.data || data || [];

        if (clases.length === 0) {
          claseSelect.html(
            '<option value="">No hay clases para este grupo</option>',
          );
          return;
        }

        // Llenar el select
        let opciones = '<option value="">Seleccione...</option>';
        clases.forEach((c) => {
          const cod = c.codigo || c.ccodcata || "";
          const nom = c.nombre || c.cdescrip || "";
          const val = c.id || c.ncodclase;
          // AGREGAMOS data-codigo="${cod}"
          opciones += `<option value="${val}" data-codigo="${cod}">${cod} - ${nom}</option>`;
        });

        claseSelect.html(opciones);
        console.log("✅ Clases cargadas:", clases.length);
      })
      .catch((err) => {
        console.error("❌ Error en Fetch:", err);
        claseSelect.html('<option value="">Error de conexión</option>');
        mostrarMensaje("Error al cargar las clases", mensaje_error);
      });
  });

  // =============================================
  // 3. AUTO-GENERAR CÓDIGO AL SELECCIONAR CLASE
  // =============================================
  $(document).on("change", "#claseSelect", function () {
    // 1. Obtenemos el código del grupo seleccionado
    // Buscamos la opción del grupo que está seleccionada y leemos su data-codigo
    const grupoCodigo = $("#grupoSelect option:selected").data("codigo") || "";

    // 2. Obtenemos el código de la clase seleccionada
    const claseCodigo = $(this).find("option:selected").data("codigo") || "";

    // 3. Los unimos (Ej: B01 + B0101 = B01B0101)
    // Nota: Según tu ejemplo, B01 es grupo y B0101 es clase, pero el resultado final es B010101.
    // Si el código de la clase ya incluye el grupo (ej: B0101), entonces solo usamos el de la clase.
    // Si el código de la clase es independiente (ej: 01), entonces los concatenamos.

    // En tu caso, el código de la clase ya es B0101, así que el código final es simplemente el de la clase.
    // Si necesitas concatenar, cambia la línea por: const codigoFinal = grupoCodigo + claseCodigo;

    const codigoFinal = claseCodigo;

    // 4. Lo asignamos al input de código (asegúrate de que el ID sea 'codigoInput')
    const formData = new FormData();
    formData.append("grupo",$("#grupoSelect").val());
    formData.append("clase",$("#claseSelect").val());

    fetch(RUTA+'familias/nuevoCodigo',{
      method:'POST',
      body:formData
    })
    .then(response =>response.json())
    .then(data => {
      $("#codigoFamilia").val(codigoFinal+data);
    })
    
    // (Opcional) Actualizamos también el cuadro de vista previa si lo tienes
    const preview = document.querySelector(".preview .code-display");
    if (preview) {
      preview.textContent = codigoFinal ? codigoFinal + "-XXXX" : "B???? - XXXXX";
    }
  });

  $("#nuevoRegistro").click(function (e) {
    e.preventDefault();

    $("#proceso").fadeIn();
    accion = "n";

    return false;
  });

  $("#consulta").keyup(function () {
    _this = this;
    buscar(_this); // arrow function para activa el buscador
  });

  $("#btnGuardar").click(function(e) {
    e.preventDefault();
    
    // 1. Validaciones básicas
    const grupoId = $("#grupoSelect").val();
    const claseId = $("#claseSelect").val();
    const codigo = $("#codigoFamilia").val();
    const nombre = $("#nombreFamilia").val();

    if (!grupoId || !claseId || !codigo || !nombre) {
        mostrarMensaje("Por favor, complete todos los campos obligatorios", "mensaje_error");
        return;
    }

    // 2. Determinar la URL según la acción
    const url = (accion === 'n') 
        ? RUTA + 'familias/nuevaFamilia' 
        : RUTA + 'familias/modificarFamilia';

    // 3. Preparar los datos
    const formData = new FormData();
    formData.append("grupo", grupoId);
    formData.append("clase", claseId);
    formData.append("codigo", codigo);
    formData.append("nombre", nombre);
    
    // Si estamos editando, enviamos el ID del registro
    if (accion === 'u' && registroEdit) {
        formData.append("id", registroEdit.id);
    }

    // 4. Enviar al backend
    fetch(url, {
        method: "POST",
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.error) {
            throw new Error(data.error);
        }
        
        mostrarMensaje("Registro guardado con éxito", "success");
        $("#proceso").fadeOut(); // Cerrar modal
        consultarDatos(paginaActual); // Recargar tabla
        
    })
    .catch(error => {
        console.error("Error:", error);
        mostrarMensaje("Error al guardar: " + error.message, "error");
    });
});

  //filtrado en la lista de solicitante
  $(".busqueda").on("keyup", function () {
    var value = $(this).val().toLowerCase();
    $(this).next().attr("id");

    //aignar a una variable el contenido
    let l = "#" + $(this).next().attr("id") + " li a";

    $(l).filter(function () {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
    });
  });

  $("#lnkCerrarModal,#btnCerrarModal").click(function(e){
    e.preventDefault();

    closeModal();

    return false
  })

  function consultarDatos(page = 1) {
    try {
      //const descripcion = document.getElementById("consulta").value;
      const tbody = document.getElementById("clasesTbody");

      tbody.innerHTML = `
            <tr class="cargando" style="text-align:center;">
                <td colspan="3"><i class="fas fa-spinner fa-spin" style="font-size:40px;"></i><br> Cargando...</td>
            </tr>`;

      const formData = new FormData();
      //formData.append("descripcion", descripcion);
      formData.append("page", page);
      formData.append("porPagina", POR_PAGINA);

      fetch(RUTA + "familias/listaJerarquia", {
        method: "POST",
        body: formData,
      })
        .then((r) => r.json())
        .then((data) => {
          if (data.error) {
            throw new Error(data.error);
          }

          if (data && data.grupos && data.grupos.length > 0) {
            // Guardamos el total que viene del backend
            totalRegistros = data.total_registros || 0;

            // Renderizamos la tabla
            renderizar(data.grupos);

            // Actualizamos el paginador
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

      // --- NIVEL 1: GRUPO ---
      html += `<tr class="grupo-row" data-target="g-${idGrupo}">
                <td colspan="3" style="cursor:pointer;">
                    <!-- Aquí respetamos el icono que venga del backend (grupo.icon) -->
                    <span class="icon-wrapper icon-grupo">
                        <i class="${grupo.icon || "fa-solid fa-folder"}"></i>
                    </span>
                    <strong>${grupo.codigo}</strong> - ${grupo.nombre}
                    <span class="badge-grupo">${grupo.items.length}</span>
                    <i class="fas fa-chevron-down toggle-icon-clase" style="float:right;"></i>
                </td>
            </tr>`;

      // --- NIVEL 2: CLASES ---
      (grupo.items || []).forEach((clase) => {
        const idClase = clase.id;

        html += `<tr class="sub-grupo-row oculto g-${idGrupo}" data-target="c-${idClase}">
        <td colspan="3" style="background:#f4f7fa; cursor:pointer; padding-left: 30px;">
            <!-- Aquí respetamos el icono de clase que venga del backend (clase.icon) -->
            <span class="icon-wrapper icon-clase">
                <i class="${clase.icon || "fa-solid fa-layer-group"}"></i>
            </span>
            <span style="color:#1a2634; font-weight:600;">${clase.codigo} - ${clase.nombre}</span>
            <span class="badge-grupo" style="background:#e2e8f0; color:#4a5568;">${clase.items.length}</span>
            <i class="fas fa-chevron-down toggle-icon-clase" style="float:right;"></i>
        </td>
    </tr>`;

        // --- NIVEL 3: FAMILIAS ---
        (clase.items || []).forEach((familia) => {
          html += `<tr class="item-row oculto c-${idClase}">
            <td style="padding-left: 30px;">
                <!-- Aquí FORZAMOS el icono de etiqueta para las familias -->
                <span class="icon-wrapper icon-familia">
                    <i class="fas fa-tag"></i> 
                </span>
                <span class="code">${familia.codigo}</span>
            </td>
            <td>${familia.nombre}</td>
            <td style="text-align:center;">
                <button type="button" class="btn-icon" title="Editar">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
            </td>
        </tr>`;
        });
      });
    });

    tbody.innerHTML = html;

    // =============================================
    // LISTENERS PARA EL ACORDEÓN
    // =============================================

    // Listener Grupos
    document.querySelectorAll(".grupo-row").forEach((row) => {
      row.addEventListener("click", function () {
        const target = this.dataset.target;
        const hijos = document.querySelectorAll(`.${target}`);
        const icon = this.querySelector(".toggle-icon-clase");

        hijos.forEach((hijo) => hijo.classList.toggle("oculto"));
        if (icon) icon.classList.toggle("cerrado");
      });
    });

    // Listener Clases
    document.querySelectorAll(".sub-grupo-row").forEach((row) => {
      row.addEventListener("click", function (e) {
        e.stopPropagation(); // Evita que se cierre el grupo padre

        const target = this.dataset.target;
        const hijos = document.querySelectorAll(`.${target}`);
        const icon = this.querySelector(".toggle-icon-clase");

        hijos.forEach((hijo) => hijo.classList.toggle("oculto"));
        if (icon) icon.classList.toggle("cerrado");
      });
    });
  }

  // =============================================
  // PAGINADOR
  // =============================================
  function actualizarPaginador(pagina) {
    paginaActual = pagina;
    const totalPaginas = Math.ceil(totalRegistros / POR_PAGINA) || 1;

    // Info de "Mostrando X - Y de Z"
    const inicio = totalRegistros > 0 ? (pagina - 1) * POR_PAGINA + 1 : 0;
    const fin = Math.min(pagina * POR_PAGINA, totalRegistros);

    document.getElementById("infoPaginador").innerHTML =
      `Mostrando <strong>${inicio}</strong> - <strong>${fin}</strong> de <strong>${totalRegistros}</strong>`;

    let botones = "";

    // Botón Anterior
    botones += `<button class="page-btn" data-page="${pagina - 1}" ${pagina <= 1 ? "disabled" : ""}>
        <i class="fas fa-chevron-left"></i></button>`;

    // Lógica para mostrar números de página (1, 2, 3, ...)
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

    // Botón Siguiente
    botones += `<button class="page-btn" data-page="${pagina + 1}" ${pagina >= totalPaginas ? "disabled" : ""}>
        <i class="fas fa-chevron-right"></i></button>`;

    document.getElementById("botonesPaginador").innerHTML = botones;

    // Agregar eventos a los botones
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

  function closeModal(){
    $("#proceso").fadeOut();
    limpiarCampos();
    accion = "";
  }

  function limpiarCampos(){
    $("#grupoSelect").val('');
    $("#claseSelect").val('');
    $("#codfamilia").val('');
    $("#nombreInput").val('');
  }
});
