$(function () {
  let accion = "";
  let index = 0;

  const POR_PAGINA = 10;

  let paginaActual = 1;
  let totalRegistros = 0;
  let datosGrupos = [];

  $("#esperar").fadeOut(function (e) {
    consultarDatos(1);
  });

  $(".ventanaProceso ").click(function (e) {
    e.preventDefault();

    $(".lista").slideUp();

    return false;
  });

  $("#nuevoRegistro").click(function (e) {
    e.preventDefault();

    $("#proceso").fadeIn();
    accion = "n";

    return false;
  });

  $("#grupo").click(function (e) {
    e.preventDefault();

    $(this).next().slideDown();

    return false;
  });

  $(".lista").on("click", "a", function (e) {
    e.preventDefault();

    let control = $(this).parent().parent().parent();
    let destino = $(this).parent().parent().parent().prev();
    let id = "";

    control.slideUp();
    destino.val($(this).text());
    id = destino.attr("id");

    $("#codgrupo").val($(this).attr("href"));
    $("#codigo").val($(this).data("grupo"));

    return false;
  });

  $("#grabarItem").click(function (e) {
    e.preventDefault();

    var result = {};

    $.each($("#formProceso").serializeArray(), function () {
      result[this.name] = this.value;
    });

    try {
      if (result["codGrupo"] == "") throw "Escoja una clase";
      if (result["codigo"] == "") throw "Ingrese el codigo de la clase";
      if (result["descripcion"] == "") throw "Ingrese el nombre de la clase";

      if (accion == "n")
        $.post(
          RUTA + "clases/nuevaClase",
          { datos: result },
          function (data, textStatus, jqXHR) {
            $("#tablaPrincipal tbody").empty().append(data.items);
            mostrarMensaje(data.mensaje, data.clase);
            $("form")[0].reset();
          },
          "json",
        );
      else {
        $.post(
          RUTA + "clases/modificaClase",
          { datos: result },
          function (data, textStatus, jqXHR) {
            mostrarMensaje(data.mensaje, data.clase);
          },
          "json",
        );
      }
    } catch (error) {
      mostrarMensaje(error, "mensaje_error");
    }

    return false;
  });

  $("#cerrarVentana").click(function (e) {
    e.preventDefault();

    $.post(
      RUTA + "clases/actualizaTabla",
      function (data, textStatus, jqXHR) {
        $("form")[0].reset();
        $(".lista").hide();
        $("#tablaPrincipal tbody").empty().append(data);
        $("#proceso").fadeOut();
      },
      "text",
    );

    return false;
  });

  $("#tablaPrincipal tbody").on("click", ".pointer", function (e) {
    e.preventDefault();

    $.post(
      RUTA + "clases/claseId",
      { id: $(this).data("id") },
      function (data, textStatus, jqXHR) {
        $("#codclase").val(data.clase[0].ncodclase);
        $("#codgrupo").val(data.clase[0].ncodgrupo);
        $("#grupo").val(data.clase[0].nombre_grupo);
        $("#codigo").val(data.clase[0].ccodcata);
        $("#descripcion").val(data.clase[0].cdescrip);

        $("#proceso").fadeIn();
        accion = "u";
      },
      "json",
    );

    return false;
  });

  $("#tablaPrincipal tbody").on("click", ".pointer a", function (e) {
    e.preventDefault();

    index = $(this).attr("href");

    $("#pregunta").fadeIn();

    return false;
  });

  $("#btnCancelarPregunta").click(function (e) {
    e.preventDefault();

    $("#pregunta").fadeOut();

    return false;
  });

  $("#btnAceptarPregunta").click(function (e) {
    e.preventDefault();

    $.post(
      RUTA + "clases/desactivaClase",
      { id: index },
      function (data, textStatus, jqXHR) {
        $("#tablaPrincipal tbody").empty().append(data);

        $("#pregunta").fadeOut();
      },
      "text",
    );

    return false;
  });

  $("#consulta").keypress(function (e) {
    if (e.which == 13) {
      consultarDatos(1);
    } 
  });

  // =============================================
  // CONSULTAR DATOS
  // =============================================
  function consultarDatos(page = 1) {
    try {
      const descripcion = document.getElementById("consulta").value;
      const tbody = document.getElementById("clasesTbody");

      // Mostrar cargando
      tbody.innerHTML = `
            <tr class="cargando" style="text-align:center;"><td colspan="3" ><i class="fas fa-spinner fa-spin" style="font-size:40px;"></i><br> Cargando...</td></tr>`;

      let formData = new FormData();
      formData.append("descripcion", descripcion);
      formData.append("page", page);
      formData.append("porPagina", POR_PAGINA);

      fetch(RUTA + "clases/listarClases", {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json())
        .then((data) => {

          if (data && data.grupos) {
            datosGrupos = data.grupos;
            totalRegistros = data.total_clases || 0;
            renderizar(data.grupos);
            actualizarPaginador(page);
          } else if (data && data.length > 0) {
            // Si la API devuelve un array plano
            datosGrupos = data;
            totalRegistros = data.reduce((acc, g) => acc + g.items.length, 0);
            renderizar(data);
            actualizarPaginador(page);
          } else {
            tbody.innerHTML = `
                    <tr class="vacio"><td colspan="3"><i class="fas fa-search" style="font-size:24px;display:block;margin-bottom:10px;color:#b0bec5;"></i>No se encontraron clases</td></tr>
                `;
            actualizarPaginador(1);
          }

          // Llenar select de grupos para el modal
          //llenarSelectGrupos();
        })
        .catch((error) => {
          console.error("Error:", error);
          tbody.innerHTML = `
                <tr class="vacio"><td colspan="2"><i class="fas fa-exclamation-circle" style="font-size:24px;display:block;margin-bottom:10px;color:#e74c3c;"></i>Error al cargar datos</td></tr>
            `;
          mostrarMensaje("Error al cargar datos: " + error.message, "mensaje_error");
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
      html = `<tr class="vacio"><td colspan="2"><i class="fas fa-search" style="font-size:24px;display:block;margin-bottom:10px;color:#b0bec5;"></i>No hay datos</td></tr>`;
    } else {
      grupos.forEach((grupo) => {
        // Estado de expansión
        const expandido = obtenerEstadoGrupo(grupo.id || grupo.codigo);

        html += `<tr class="grupo-row" data-grupo="${grupo.id || grupo.codigo}">
                <td colspan="3">
                    <i class="${grupo.icon || "fa-solid fa-folder"}" style="color:${colorMap[grupo.color] || "#2d4054"}"></i>
                    ${grupo.codigo || grupo.id} - ${grupo.nombre}
                    <span class="badge-grupo">${grupo.items ? grupo.items.length : 0}</span>
                    <i class="fas fa-chevron-down toggle-icon-clase ${expandido ? "" : "cerrado"}"></i>
                </td>
            </tr>`;

        if (grupo.items) {
          grupo.items.forEach((item) => {
            const oculto = expandido ? "" : "oculto";
            html += `<tr class="item-row ${oculto}" data-grupo="${grupo.id || grupo.codigo}" data-idclase="${grupo.clase}">
                        <td><span class="code">${item.code}</span></td>
                        <td>${item.desc}</td>
                        <td><a href="${grupo.clase}"><i class="fas fa-trash-alt"></i></a></td>
                    </tr>`;
          });
        }
      });
    }

    document.getElementById("clasesTbody").innerHTML = html;

    // 🔥 Agregar event listeners después de renderizar
    document.querySelectorAll(".grupo-row").forEach((row) => {
      row.addEventListener("click", function () {
        const grupoId = this.dataset.grupo;
        toggleGrupo(grupoId);
      });
    });
  }
// =============================================
// PAGINADOR
// =============================================
function actualizarPaginador(pagina) {
    paginaActual = pagina;
    const totalPaginas = Math.ceil(totalRegistros / POR_PAGINA) || 1;
    
    const inicio = (pagina - 1) * POR_PAGINA + 1;
    const fin = Math.min(pagina * POR_PAGINA, totalRegistros);
    document.getElementById('infoPaginador').innerHTML = 
        `Mostrando <strong>${totalRegistros > 0 ? inicio : 0}</strong> - <strong>${fin}</strong> de <strong>${totalRegistros}</strong>`;
    
    let botones = '';
    
    // Botón Anterior
    botones += `<button class="page-btn" data-page="${pagina - 1}" ${pagina <= 1 ? 'disabled' : ''}>
        <i class="fas fa-chevron-left"></i>
    </button>`;
    
    // Calcular rango de páginas a mostrar
    let inicioPag = Math.max(1, pagina - 3);
    let finPag = Math.min(totalPaginas, pagina + 3);
    
    if (finPag - inicioPag < 6) {
        if (inicioPag === 1) finPag = Math.min(7, totalPaginas);
        else if (finPag === totalPaginas) inicioPag = Math.max(1, totalPaginas - 6);
    }
    
    // Primera página y puntos suspensivos
    if (inicioPag > 1) {
        botones += `<button class="page-btn" data-page="1">1</button>`;
        if (inicioPag > 2) botones += `<button disabled>...</button>`;
    }
    
    // Páginas del medio
    for (let i = inicioPag; i <= finPag; i++) {
        botones += `<button class="page-btn ${i === pagina ? 'active' : ''}" data-page="${i}">${i}</button>`;
    }
    
    // Última página y puntos suspensivos
    if (finPag < totalPaginas) {
        if (finPag < totalPaginas - 1) botones += `<button disabled>...</button>`;
        botones += `<button class="page-btn" data-page="${totalPaginas}">${totalPaginas}</button>`;
    }
    
    // Botón Siguiente
    botones += `<button class="page-btn" data-page="${pagina + 1}" ${pagina >= totalPaginas ? 'disabled' : ''}>
        <i class="fas fa-chevron-right"></i>
    </button>`;
    
    // Insertar botones en el HTML
    document.getElementById('botonesPaginador').innerHTML = botones;
    
    // 🔥 Agregar event listeners a todos los botones de página
    document.querySelectorAll('.page-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const page = parseInt(this.dataset.page);
            irPagina(page);
        });
    });
}

// =============================================
// FUNCIÓN IR A PÁGINA (GLOBAL)
// =============================================
function irPagina(pagina) {
    const totalPaginas = Math.ceil(totalRegistros / POR_PAGINA) || 1;
    if (pagina < 1 || pagina > totalPaginas) return;
    consultarDatos(pagina);
}

  // =============================================
  // ESTADOS DE EXPANSIÓN
  // =============================================
  const estadosGrupo = {};

  function obtenerEstadoGrupo(id) {
    if (estadosGrupo[id] === undefined) estadosGrupo[id] = true;
    return estadosGrupo[id];
  }

  // =============================================
  // TOGGLE GRUPO (versión con event listeners)
  // =============================================
  function toggleGrupo(id) {
    estadosGrupo[id] = !estadosGrupo[id];
    const items = document.querySelectorAll(`tr.item-row[data-grupo="${id}"]`);
    const icon = document.querySelector(
      `tr.grupo-row[data-grupo="${id}"] .toggle-icon-clase`,
    );
    if (estadosGrupo[id]) {
      items.forEach((el) => el.classList.remove("oculto"));
      if (icon) icon.classList.remove("cerrado");
    } else {
      items.forEach((el) => el.classList.add("oculto"));
      if (icon) icon.classList.add("cerrado");
    }
  }

  // =============================================
  // MAPA DE COLORES
  // =============================================
  const colorMap = {
    b01: "#2a7de1",
    b02: "#e67e22",
    b03: "#27ae60",
    b04: "#8e44ad",
    b05: "#e74c3c",
  };

  // =============================================
  // LLENAR SELECT DE GRUPOS
  // =============================================
  function llenarSelectGrupos() {
    const select = document.getElementById("grupoSelect");
    select.innerHTML = '<option value="">Seleccione...</option>';
    datosGrupos.forEach((g) => {
      select.innerHTML += `<option value="${g.id || g.codigo}">${g.codigo || g.id} - ${g.nombre}</option>`;
    });
  }
});
