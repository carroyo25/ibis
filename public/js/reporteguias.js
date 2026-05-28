(async () => {
  $("#esperar").css({ display: "none" });

  const itemsPorPagina = 15;
  let campoFiltroActual = null; // Variable global para guardar el campo del filtro
  let contenedorActual = null;

  function obtenerFiltros() {
    let anio = $("#anioSearch").val();

    return {
      anio: anio ? parseInt(anio) : new Date().getFullYear(),
      guia: $("#guiaSearch").val(),
      sunat: $("#guiaSunat").val(),
    };
  }

  async function contarItemsConFiltros(filtros) {
    try {
      let datosEnvio = {
        anio: filtros.anio,
      };

      if (filtros.guia && filtros.guia.trim() !== "") {
        datosEnvio.guia = filtros.guia.split(",");
      }

      if (filtros.sunat && filtros.sunat.trim() !== "") {
        datosEnvio.sunat = filtros.sunat.split(",");
      }

      console.log("Enviando a contarItems:", datosEnvio);

      const response = await fetch(RUTA + "reporteguias/itemsConsulta", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(datosEnvio),
      });
      const data = await response.json();
      return data.total || 0;
    } catch (error) {
      console.log(error.message);
      return 0;
    }
  }

  async function listarGuias(inicio, items, filtros) {
    try {
      let datosEnvio = {
        anio: filtros.anio,
        inicio: inicio,
        items: items,
      };

      if (filtros.guia && filtros.guia.trim() !== "") {
        datosEnvio.guia = filtros.guia.split(",");
      }

      if (filtros.sunat && filtros.sunat.trim() !== "") {
        datosEnvio.sunat = filtros.sunat.split(",");
      }

      console.log("Enviando a listarGuias:", datosEnvio);

      const response = await fetch(RUTA + "reporteguias/listaGuias", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(datosEnvio),
      });
      const data = await response.json();
      return data;
    } catch (error) {
      console.log(error.message);
      return null;
    }
  }

  // Cargar datos iniciales
  let filtros = obtenerFiltros();
  let totalItems = await contarItemsConFiltros(filtros);
  let totalPaginas = Math.ceil(totalItems / itemsPorPagina);

  let estado = {
    indexBtn: 0,
    rangoInicio: 1,
    rangoFin: Math.min(20, totalPaginas),
    paginaActual: 1,
  };

  const dom = {
    paginador: document.getElementById("paginador"),
    actualizarBtns: () => document.getElementsByClassName("page-btn"),
    cleanActives: () =>
      document
        .querySelectorAll(".page-btn")
        .forEach((btn) => btn.classList.remove("active")),
  };

  const renderizarPaginador = (numeroActivo = null) => {
    const { rangoInicio, rangoFin, indexBtn } = estado;
    const container = dom.paginador;
    if (!container) return;
    container.innerHTML = "";

    const inicioValido = Math.max(1, rangoInicio);
    const finValido = Math.min(totalPaginas, rangoFin);

    const prevBtn = document.createElement("button");
    prevBtn.textContent = "◀ Anterior";
    prevBtn.className = "first-page";
    container.appendChild(prevBtn);

    for (let i = inicioValido; i <= finValido; i++) {
      const pageBtn = document.createElement("button");
      pageBtn.textContent = i;
      pageBtn.className = "page-btn";
      if (
        (numeroActivo !== null && i === numeroActivo) ||
        (numeroActivo === null && i === indexBtn + 1)
      ) {
        pageBtn.classList.add("active");
      }
      container.appendChild(pageBtn);
    }

    const nextBtn = document.createElement("button");
    nextBtn.textContent = "Siguiente ▶";
    nextBtn.className = "next-page";
    if (finValido === totalPaginas) nextBtn.disabled = true;
    container.appendChild(nextBtn);
  };

  async function cargarPagina(pagina) {
    const inicio = (pagina - 1) * itemsPorPagina;
    const filtrosActuales = obtenerFiltros();
    const resultado = await listarGuias(
      inicio,
      itemsPorPagina,
      filtrosActuales,
    );
    if (resultado && resultado.success) {
      renderizarTabla(resultado.datos);
    } else if (resultado && !resultado.success) {
      $("#tablaPrincipalCuerpo").html(
        `<tr><td colspan="6">${resultado.message}</td></tr>`,
      );
    }
  }

  function renderizarTabla(datos) {
    const tbody = $("#tablaPrincipalCuerpo");
    tbody.empty();

    if (!datos || datos.length === 0) {
      tbody.html(`<tr><td colspan="6">No se encontraron registros</td></tr>`);
      return;
    }

    datos.forEach((row) => {
      tbody.append(`
        <tr data-interna="${row.cnumguia}" data-sunat="${row.guiasunat}" class="fila-pdf">
          <td>${row.cnumguia || ""}</td>
          <td>${row.emision || ""}</td>
          <td>${row.anio || ""}</td>
          <td>${row.guiasunat || ""}</td>
          <td>${row.cenvio || " "}</td>
          <td>${row.cobserva || ""}</td>
        </tr>
      `);
    });
  }

  function obtenerValoresSeleccionados() {
    const checkboxes = document.querySelectorAll(
      '#lista-filtro input[type="checkbox"]:checked',
    );
    const valores = Array.from(checkboxes).map((cb) => cb.value);
    return valores;
  }

  async function aplicarFiltroCheckboxes(campo) {
    const valoresSeleccionados = obtenerValoresSeleccionados();

    if (valoresSeleccionados.length === 0) {
      console.log("No hay checkboxes seleccionados");
      return;
    }

    $("#esperar").css({ display: "flex" });

    // Crear nuevos filtros directamente
    let filtrosActuales = {
      anio: $("#anioSearch").val()
        ? parseInt($("#anioSearch").val())
        : new Date().getFullYear(),
      guia: "",
      sunat: "",
    };

    if (campo === "cnumguia") {
      const valoresString = valoresSeleccionados.join(",");
      filtrosActuales.guia = valoresString;
      $("#guiaSearch").val(valoresString);
    } else if (campo === "guiasunat") {
      const valoresString = valoresSeleccionados.join(",");
      filtrosActuales.sunat = valoresString;
      $("#guiaSunat").val(valoresString);
    }

    totalItems = await contarItemsConFiltros(filtrosActuales);
    totalPaginas = Math.ceil(totalItems / itemsPorPagina);

    estado = {
      indexBtn: 0,
      rangoInicio: 1,
      rangoFin: Math.min(20, totalPaginas),
      paginaActual: 1,
    };

    if (totalPaginas > 0) {
      renderizarPaginador(1);
      const inicio = 0;
      const resultado = await listarGuias(
        inicio,
        itemsPorPagina,
        filtrosActuales,
      );
      if (resultado && resultado.success) {
        renderizarTabla(resultado.datos);
      } else if (resultado && !resultado.success) {
        $("#tablaPrincipalCuerpo").html(
          `<tr><td colspan="6">${resultado.message}</td></tr>`,
        );
      }
    } else {
      $("#paginador").html("");
      $("#tablaPrincipalCuerpo").html(
        `<tr><td colspan="6">No se encontraron registros</td></tr>`,
      );
    }

    $(".filtro-container").slideUp();
    $("#esperar").css({ display: "none" });
  }

  //esta es la funcion para llenar los filtros
  function llenarFiltros(
    campo,
    limite,
    string,
    visible,
    filtro,
    lista,
    contenedor,
  ) {
    const formData = new FormData();
    const lista_filtro = contenedor;

    formData.append("campo", campo);
    formData.append("items", limite);
    formData.append("string", string);
    formData.append("lista", lista);

    fetch(RUTA + "reporteguias/filtros", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        lista_filtro.innerHTML = "";

        data.datos.forEach((item) => {
          const li = document.createElement("li");
          li.innerHTML = `<input type="checkbox" name="filtro_check" class="filtro_check" value="${item.cnumguia}"><label>${item.cnumguia}</label>`;
          lista_filtro.appendChild(li);
        });

        if (visible) {
          filtro.slideDown();
        }
      });

    return false;
  }

  // Evento para las filas
  $("#tablaPrincipalCuerpo").on("click", "tr", function (e) {
    const ruta =
      "https://sicalsepcon.net/ibis/public/documentos/guias_remision/";
    const pdfPreview = document.getElementById("pdfPreview");

    const guiaInterna = $(this).data("interna");
    const guiaSunat = $(this).data("sunat");

    if (guiaSunat === "null" || guiaSunat === null) {
      pdfPreview.setAttribute("src", ruta + guiaInterna + ".pdf");
    } else {
      pdfPreview.setAttribute(
        "src",
        ruta + "20504898173-09-T001-" + guiaSunat + ".pdf",
      );
    }

    fadeIn(document.getElementById("vistaprevia"));
    return false;
  });

  $("#closePreview").click(function (e) {
    document.getElementById("pdfPreview").innerHTML = "";
    fadeOut(document.getElementById("vistaprevia"));
  });

  async function recargarTodo() {
    filtros = obtenerFiltros();
    totalItems = await contarItemsConFiltros(filtros);
    totalPaginas = Math.ceil(totalItems / itemsPorPagina);

    estado = {
      indexBtn: 0,
      rangoInicio: 1,
      rangoFin: Math.min(20, totalPaginas),
      paginaActual: 1,
    };

    if (totalPaginas > 0) {
      renderizarPaginador(1);
      await cargarPagina(1);
    } else {
      $("#paginador").html("");
      $("#tablaPrincipalCuerpo").html(
        `<tr><td colspan="6">No se encontraron registros</td></tr>`,
      );
    }
  }

  $("#btnConsulta").on("click", async () => {
    $("#esperar").css({ display: "flex" });
    $("#guiaSearch").val("");
    $("#guiaSunat").val("");
    await recargarTodo();
    $("#esperar").css({ display: "none" });
  });

  // Cargar datos iniciales
  if (totalPaginas > 0) {
    renderizarPaginador(1);
    await cargarPagina(1);
  } else {
    $("#tablaPrincipalCuerpo").html(
      `<tr><td colspan="6">No se encontraron registros</td></tr>`,
    );
  }

  document.addEventListener("click", async (e) => {
    const btns = dom.actualizarBtns();

    if (e.target.matches(".page-btn")) {
      e.preventDefault();
      const nuevoIndex = Array.from(btns).indexOf(e.target);
      const pagina = parseInt(e.target.textContent);
      estado.indexBtn = nuevoIndex;
      estado.paginaActual = pagina;
      dom.cleanActives();
      btns[nuevoIndex].classList.add("active");
      await cargarPagina(pagina);
      return false;
    }

    if (e.target.matches(".next-page")) {
      e.preventDefault();
      const { indexBtn, rangoInicio, rangoFin } = estado;

      if (indexBtn + 1 === btns.length) {
        if (rangoFin < totalPaginas) {
          estado.rangoInicio = rangoFin + 1;
          estado.rangoFin = Math.min(rangoFin + 20, totalPaginas);
          estado.indexBtn = 0;
          estado.paginaActual = rangoFin + 1;
          renderizarPaginador();
          await cargarPagina(estado.paginaActual);
        }
      } else {
        dom.cleanActives();
        estado.indexBtn++;
        estado.paginaActual++;
        btns[estado.indexBtn].classList.add("active");
        await cargarPagina(estado.paginaActual);
      }
      return false;
    }

    if (e.target.matches(".first-page")) {
      e.preventDefault();
      const { indexBtn, rangoInicio } = estado;

      if (indexBtn > 0) {
        dom.cleanActives();
        estado.indexBtn--;
        estado.paginaActual--;
        btns[estado.indexBtn].classList.add("active");
        await cargarPagina(estado.paginaActual);
      } else if (indexBtn === 0 && rangoInicio > 1) {
        estado.rangoFin = rangoInicio - 1;
        estado.rangoInicio = Math.max(estado.rangoFin - 19, 1);
        estado.indexBtn = btns.length - 1;
        estado.paginaActual = estado.rangoFin;
        renderizarPaginador();
        await cargarPagina(estado.paginaActual);
      }
      return false;
    }

    if (e.target.matches(".filtro")) {
      e.preventDefault();
      campoFiltroActual = $(e.target).parent().data("campo");
      const contenedorActual = document.querySelector(
        `.lista-filtro[data-campo="${campoFiltroActual}"]`,
      );

      const filtro = $(e.target).parent().find(".filtro-container");

      if (!$(".filtro-container").is(":visible")) {
        llenarFiltros(
          campoFiltroActual,
          0,
          "",
          true,
          filtro,
          null,
          contenedorActual,
        );
      } else {
        $(".filtro-container").slideUp();
      }
      return false;
    }

    if (e.target.matches(".botones_filtro")) {
      e.preventDefault();
      if (e.target.id == "aplicar-filtro") {
        console.log("Aplicando filtro con campo:", campoFiltroActual);
        await aplicarFiltroCheckboxes(campoFiltroActual);
      }
      return false;
    }

    if (
      $(".filtro-container").is(":visible") &&
      !$(e.target).closest(".filtro-container").length &&
      !$(e.target).closest(".filtro").length
    ) {
      $(".filtro-container").slideUp();
    }
  });

  document.addEventListener("keydown", async (e) => {
    if (e.target.matches(".filtro-Search")) {
      if (e.key == "Enter") {
        let campo = e.target.closest("th").dataset.campo;
        let string = e.target.value;
        llenarFiltros(campo, 0, string, false, null, null);
      }
    }
  });

  function fadeIn(element) {
    element.style.display = "block";
    setTimeout(() => {
      element.style.opacity = "1";
    }, 10);
  }

  function fadeOut(element) {
    element.style.opacity = "0";
    setTimeout(() => {
      element.style.display = "none";
    }, 300);
  }

  // Ejemplo de botón limpiar
  function limpiarFiltros() {
    document
      .querySelectorAll('#lista-filtro input[type="checkbox"]')
      .forEach((cb) => {
        cb.checked = false;
      });
    $("#guiaSearch").val("");
    $("#guiaSunat").val("");
    recargarTodo();
  }
})();
