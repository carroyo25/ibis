$(function () {
  var accion = "";
  var index = 0;
  var codigoTexto = "";

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

    $("#lista").hide();
    $("#codclase, #codclase").val("");

    $(this).next().slideDown();

    return false;
  });

  $("#clase").click(function (e) {
    e.preventDefault();

    try {
      if ($("#codgrupo").val() == "") throw "Seleccione un grupo";
      $.post(
        RUTA + "familias/listaClases",
        { id: $("#codgrupo").val() },
        function (data, textStatus, jqXHR) {
          $("#listaClase ul").empty().append(data).end();
        },
        "text",
      );

      $(this).next().slideDown();
    } catch (error) {
      mostrarMensaje(error, "mensaje_error");
    }
    return false;
  });

  $(".lista").on("click", "a", function (e) {
    e.preventDefault();

    let control = $(this).parent().parent().parent();
    let destino = $(this).parent().parent().parent().prev();
    let contenedor_padre = $(this).parent().parent().parent().attr("id");
    let id = "";
    let codigo = $(this).attr("href");
    codigoTexto = $(this).data("catalogo");

    control.slideUp();
    destino.val($(this).text());
    id = destino.attr("id");

    if (contenedor_padre == "listaGrupo") {
      $("#codgrupo").val(codigo);
    } else if (contenedor_padre == "listaClase") {
      $("#codclase").val(codigo);

      $.post(
        RUTA + "familias/codigo",
        { grupo: $("#codgrupo").val(), clase: $("#codclase").val() },
        function (data, textStatus, jqXHR) {
          $("#codigo").val(codigoTexto + data);
          $("#codigo_clase_catalogo").val(codigoTexto);
        },
        "text",
      );
    }

    return false;
  });

  $("#grabarItem").click(function (e) {
    e.preventDefault();

    var result = {};

    $.each($("#formProceso").serializeArray(), function () {
      result[this.name] = this.value;
    });

    try {
      if (result["codigo"] == "") throw "Ingrese un codigo";
      if (result["descripcion"] == "") throw "Ingrese una descripción";

      if (accion == "n") {
        $.post(
          RUTA + "familias/nuevaFamilia",
          { datos: result },
          function (data, textStatus, jqXHR) {
            mostrarMensaje(data.mensaje, data.clase);
            $("#descripcion").val("");

            $("#tablaPrincipal tbody").empty().append(data.items);

            $("#codigo").val(codigoTexto + data.numero);
          },
          "json",
        );
      } else {
        $.post(
          RUTA + "familias/modificaFamilia",
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

  $("#tablaPrincipal tbody").on("click", ".pointer", function (e) {
    e.preventDefault();

    $.post(
      RUTA + "familias/familiaId",
      { id: $(this).data("id") },
      function (data, textStatus, jqXHR) {
        $("#codgrupo").val(data.familia[0].ncodgrupo);
        $("#codclase").val(data.familia[0].ncodclase);
        $("#codfamilia").val(data.familia[0].ncodfamilia);
        $("#grupo").val(data.familia[0].nombre_grupo);
        $("#clase").val(data.familia[0].nombre_clase);
        $("#codigo").val(data.familia[0].ccodcata);
        $("#descripcion").val(data.familia[0].cdescrip);

        $("#proceso").fadeIn();
        accion = "u";
      },
      "json",
    );

    return false;
  });

  $("#cerrarVentana").click(function (e) {
    e.preventDefault();

    $.post(
      RUTA + "familias/actualizaTabla",
      function (data, textStatus, jqXHR) {
        $(".lista").slideUp();
        $("form")[0].reset();
        $("#tablaPrincipal tbody").empty().append(data);
        $("#proceso").fadeOut();
      },
      "text",
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
      RUTA + "familias/desactivaFamilia",
      { id: index },
      function (data, textStatus, jqXHR) {
        $("#tablaPrincipal tbody").empty().append(data);

        $("#pregunta").fadeOut();
      },
      "text",
    );

    return false;
  });

  $("#consulta").keyup(function () {
    _this = this;
    buscar(_this); // arrow function para activa el buscador
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
        if (inicioPag > 2) botones += `<button class="page-btn" disabled>...</button>`;
    }

    for (let i = inicioPag; i <= finPag; i++) {
        botones += `<button class="page-btn ${i === pagina ? "active" : ""}" data-page="${i}">${i}</button>`;
    }

    if (finPag < totalPaginas) {
        if (finPag < totalPaginas - 1) botones += `<button class="page-btn" disabled>...</button>`;
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
});
