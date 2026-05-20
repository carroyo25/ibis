(async () => {
  const itemsPorPagina = 12;

  function obtenerFiltros() {
    let anio = $("#anioSearch").val();
    console.log("Año obtenido:", anio); // Para depurar

    return {
      anio: anio ? parseInt(anio) : new Date().getFullYear(),
      guia: $("#guiaSearch").val(),
      sunat: $("#guiaSunat").val(),
    };
  }

  async function contarItemsConFiltros(filtros) {
    try {
      const response = await fetch(RUTA + "reporteguias/itemsConsulta", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(filtros),
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
      const response = await fetch(RUTA + "reporteguias/listaGuias", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          anio: filtros.anio,
          guia: filtros.guia,
          sunat: filtros.sunat,
          inicio: inicio,
          items: items,
        }),
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
        <tr>
          <td>${row.cnumguia || ""}</td>
          <td>${row.ftraslado || row.freg || ""}</td>
          <td>${row.anio || ""}</td>
          <td>${row.guiasunat || ""}</td>
          <td>${row.cenvio || ""}</td>
          <td>${row.cobserva || ""}</td>
        </tr>
      `);
    });
  }

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

  // Evento del botón consultar
  $("#btnConsulta").on("click", async () => {
    $("#esperar").css({ display: "flex" });
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

  $("#esperar").css({ display: "none" });

  document.addEventListener("click", async (e) => {
    const btns = dom.actualizarBtns();
    if (!btns.length) return;

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
      const { indexBtn, rangoInicio, rangoFin, paginaActual } = estado;

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
      const { indexBtn, rangoInicio, paginaActual } = estado;

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
  });
})();
