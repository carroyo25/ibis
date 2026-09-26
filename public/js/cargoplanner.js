$(function () {
  let idpedido = "",
    progreso = 0;

  const display = document.getElementById("display");

  $("#esperar").fadeOut();

  $("#btnProcesa").click(function (e) {
    e.preventDefault();

    $("#estado_item").val("");

    filtrarCargoPlan();

    return false;
  });

  // Función para realizar paginación después de la carga de datos
  function iniciarPaginador() {
    const content = document.querySelector(".itemsCargoPlanner");
    let itemsPerPage = 100; // Valor por defecto
    let currentPage = 0;
    const maxVisiblePages = 10; // Número máximo de botones visibles
    const items = Array.from(content.getElementsByTagName("tr")).slice(1); // Tomar todos los <tr>, excepto el primero (encabezado)

    // Mostrar una página específica
    function showPage(page) {
      const startIndex = page * itemsPerPage;
      const endIndex = startIndex + itemsPerPage;
      items.forEach((item, index) => {
        item.classList.toggle(
          "hidden",
          index < startIndex || index >= endIndex,
        );
      });
      updateActiveButtonStates();
      createPageButtons();
    }

    // Crear los botones de paginación y el selector de elementos por página
    function createPageButtons() {
      const totalPages = Math.ceil(items.length / itemsPerPage);
      let paginationContainer = document.querySelector(".pagination");

      // Si el contenedor de paginación no existe, crearlo
      if (!paginationContainer) {
        paginationContainer = document.createElement("div");
        paginationContainer.classList.add("pagination");
        content.appendChild(paginationContainer);
      } else {
        // Limpiar el contenedor existente
        paginationContainer.innerHTML = "";
      }

      // Crear el selector para elementos por página
      const itemsPerPageSelect = document.createElement("select");
      const options = [25, 50, 100, 150, 200, 250, 300];

      options.forEach((option) => {
        const opt = document.createElement("option");
        opt.value = option;
        opt.textContent = option;
        if (option === itemsPerPage) opt.selected = true; // Establecer 100 como seleccionado por defecto
        itemsPerPageSelect.appendChild(opt);
      });

      // Agregar evento al selector
      itemsPerPageSelect.addEventListener("change", function () {
        itemsPerPage = parseInt(this.value); // Actualizar el número de elementos por página
        currentPage = 0; // Reiniciar a la primera página
        createPageButtons();
        showPage(currentPage);
      });

      paginationContainer.appendChild(itemsPerPageSelect); // Agregar el selector al contenedor de paginación

      // Botón "Primera"
      const firstButton = document.createElement("button");
      firstButton.textContent = "Primera";
      firstButton.disabled = currentPage === 0;
      firstButton.addEventListener("click", () => {
        currentPage = 0;
        showPage(currentPage);
      });
      paginationContainer.appendChild(firstButton);

      // Botón "Anterior"
      const prevButton = document.createElement("button");
      prevButton.textContent = "Anterior";
      prevButton.disabled = currentPage === 0;
      prevButton.addEventListener("click", () => {
        if (currentPage > 0) {
          currentPage--;
          showPage(currentPage);
        }
      });
      paginationContainer.appendChild(prevButton);

      // Mostrar botones limitados
      const startPage = Math.max(
        0,
        currentPage - Math.floor(maxVisiblePages / 2),
      );
      const endPage = Math.min(totalPages, startPage + maxVisiblePages);

      for (let i = startPage; i < endPage; i++) {
        const pageButton = document.createElement("button");
        pageButton.textContent = i + 1;
        pageButton.disabled = i === currentPage; // Deshabilitar botón si es la página actual
        pageButton.classList.toggle("active", i === currentPage); // Agregar la clase 'active' si es la página actual
        pageButton.addEventListener("click", () => {
          currentPage = i;
          showPage(currentPage);
        });

        paginationContainer.appendChild(pageButton);
      }

      // Botón "Siguiente"
      const nextButton = document.createElement("button");
      nextButton.textContent = "Siguiente";
      nextButton.disabled = currentPage === totalPages - 1;
      nextButton.addEventListener("click", () => {
        if (currentPage < totalPages - 1) {
          currentPage++;
          showPage(currentPage);
        }
      });
      paginationContainer.appendChild(nextButton);

      // Botón "Última"
      const lastButton = document.createElement("button");
      lastButton.textContent = "Última";
      lastButton.disabled = currentPage === totalPages - 1;
      lastButton.addEventListener("click", () => {
        currentPage = totalPages - 1;
        showPage(currentPage);
      });
      paginationContainer.appendChild(lastButton);
    }

    // Actualizar los estados activos de los botones de paginación
    function updateActiveButtonStates() {
      const pageButtons = document.querySelectorAll(".pagination button");
      pageButtons.forEach((button, index) => {
        // Remover clase 'active' de todos los botones
        button.classList.remove("active");
        // Si el botón es el de la página actual, agregar la clase 'active'
        if (parseInt(button.textContent) === currentPage + 1) {
          button.classList.add("active");
        }
      });
    }

    // Inicializar la paginación
    createPageButtons();
    showPage(currentPage); // Mostrar la primera página
  }

  $("#btnExporta").click(function (e) {
    e.preventDefault();

    $("#esperar").css("opacity", "1").fadeIn();

    $.post(
      RUTA + "cargoplanner/export",
      { registros: JSON.stringify(detalles()) },
      function (data, textStatus, jqXHR) {
        $("#esperar").css("opacity", "0").fadeOut();
        window.location.href = data.documento;
      },
      "json",
    );

    return false;
  });

  $("#cargoPlanDescrip tbody").on("dblclick", "tr", function (e) {
    e.preventDefault();

    let tabla = $(this);

    idpedido = tabla.data("pedido");

    $("#codigo").text(tabla.find("td").eq(14).text());
    $("#producto").text(tabla.find("td").eq(16).text());
    $("#unidad").text(tabla.find("td").eq(15).text());
    $("#cantidad").text(tabla.find("td").eq(11).text());
    $("#estado").text(tabla.find("td").eq(1).text());
    $("#nropedido").text(tabla.find("td").eq(8).text());
    $("#tipo_pedido").text(tabla.find("td").eq(6).text());
    $("#emision_pedido").text(tabla.find("td").eq(9).text());
    $("#aprobacion_pedido").text(tabla.find("td").eq(10).text());
    $("#aprobado_por").text(tabla.data("aprueba"));

    $.post(
      RUTA + "cargoplanner/resumen",
      {
        orden: tabla.data("orden"),
        refpedido: $(this).data("itempedido"),
        despacho: $(this).data("despacho"),
        registro: $(this).data("registro"),
      },
      function (data, textStatus, jqXHR) {
        let orden_count = data.orden.datos.length || 0;
        let ingresos_count = data.ingresos.datos.length || 0;
        let salidas_count = data.despachos.datos.length || 0;
        let registros_count = data.registros.datos.length || 0;

        console.log(data.orden);

        $("#orden_count").text(orden_count);
        $("#ingresos_count").text(ingresos_count);
        $("#salidas_count").text(salidas_count);
        $("#registros_count").text(registros_count);

        renderizarOrdenes(data.orden.datos);
        renderizarIngresos(data.ingresos.datos);
        renderizarDespachos(data.despachos.datos);
        renderizarRegistros(data.registros.datos);

        $("#cpModal").addClass("active");
      },
      "json",
    );

    return false;
  });

  $("#closeDocument").click(function (e) {
    e.preventDefault();

    $("#vistadocumento").fadeOut();

    return false;
  });

  $(".exportReport").click(function (e) {
    e.preventDefault(e);

    let estado = $(this).attr("href"),
      formData = new FormData();

    formData.append("estado", estado);

    $("#esperarCargo").css("opacity", "1").fadeIn();
    //startTimer();

    fetch(RUTA + "cargoplanner/dataExcelTotalCargoPlan", {
      method: "POST",
      body: formData,
    })
      .then((response) => {
        return response.json();
      })
      .then((json) => {
        $("#esperarCargo").css("opacity", "0").fadeOut();
        //resetTimer();
        window.location.href = json.documento;
      })
      .catch((err) => {
        console.log(err);
      });

    return false;
  });

  $(".exportFast").click(function (e) {
    e.preventDefault();

    $("#esperarCargo").css("opacity", "1").fadeIn();

    fetch(RUTA + "cargoplanner/exceljs")
      .then((response) => response.json())
      .then(async (json) => {
        document.getElementById("waitMessage").innerHTML =
          "Exportado a hoja de calculo...";
        document.getElementById("excelProcces").value = 50;

        return false;

        await excelJson(json.datos);
      });

    return false;
  });

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
      { width: 70 },
      { width: 15 },
      { width: 15 },
      { width: 15 },
      { width: 15 },
      { width: 15 },
      { width: 15 },
      { width: 15 },
      { width: 15 },
      { width: 15 },
      { width: 15 },
      { width: 15 },
      { width: 15 },
      { width: 15 },
      { width: 15 },
      { width: 15 },
      { width: 15 },
      { width: 15 },
      { width: 15 },
      { width: 15 },
      { width: 15 },
      { width: 15 },
      { width: 70 },
      { width: 50 },
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
      "Items",
      "Estado Actual",
      "Codigo Proyecto",
      "Area",
      "Partida",
      "Atención",
      "Tipo",
      "Año Pedido",
      "N° Pedido",
      "Creación Pedido",
      "Aprobación del Pedido",
      "Cantidad Pedida",
      "Cantidad Aprobada",
      "Cantidad Compra",
      "Codigo del Bien/Servicio",
      "Unidad Medida",
      "Descripcion del Bien/Servicio",
      "Tipo Orden",
      "Año Orden",
      "Nro Orden",
      "Fecha Orden",
      "Cantidad Orden",
      "Item Orden",
      "Fecha Autorizacion",
      "Atencion Almacen",
      "Descripcion del proveedor",
      "Fecha Entrega Proveedor",
      "Cant. Recibida",
      "Nota de Ingreso",
      "Fecha Recepcion Proveedor",
      "Saldo por Recibir",
      "Dias Entrega",
      "Días Atrazo",
      "Semáforo",
      "Cantidad Despachada",
      "Nro. Guia",
      "Nro. Guia Transferencia",
      "Fecha Traslado",
      "Registro Almacen",
      "Fecha Ingreso Almacen",
      "Cantidad en Obra",
      "Estado Pedido",
      "Estado Item",
      "N° Parte",
      "Codigo Activo",
      "Operador Logístico",
      "Tipo Transporte",
      "Observaciones/Concepto",
      "Solicitante",
    ];

    /* worksheet.addRow(headers); */
    worksheet.getRow(2).values = headers;

    // Configurar wrapText para cada columna
    headers.forEach((header, index) => {
      const columnIndex = index + 1; // Las columnas en ExcelJS comienzan en 1
      worksheet.getColumn(columnIndex).alignment = { wrapText: true }; // Aplicar wrapText a toda la columna
    });

    let fila = 3;

    //const progress = document.getElementById("excelProcces");

    // Rellenar los datos en el archivo
    datos.forEach((dato, index) => {
      document.getElementById("waitMessage").innerHTML = "Insertando items...";

      let tipo_orden = dato.idtipomov === 37 ? "BIENES" : "SERVICIO";
      let clase_operacion = dato.idtipomov === 37 ? "B" : "S";

      let saldoRecibir =
        dato.cantidad_orden - dato.ingreso > 0
          ? dato.cantidad_orden - dato.ingreso
          : "-";

      let dias_atraso =
        saldoRecibir > 0 && dato.dias_atraso < 1 ? dato.dias_atraso : "-";

      let suma_atendido = (
        Number(dato.cantidad_orden) + Number(dato.cantidad_atendida)
      ).toFixed(2);

      let cantidad = dato.cantidad_pedido;

      let estado_pedido = dato.estadoItem >= 54 ? "Atendido" : "Pendiente";
      let estado_item = dato.estadoItem >= 54 ? "Atendido" : "Pendiente";

      let transporte = dato.nidreg === 39 ? "TERRESTRE" : dato.transporte;
      let atencion = dato.atencion === 47 ? "NORMAL" : "URGENTE";

      let color_mostrar = "FFFFFF";
      let color_semaforo = "FFFFFF";
      let porcentaje = "";

      let fecha_entrega = null;
      let fecha_autoriza = null;

      let dias_plazo = parseInt(dato.plazo) + 1 + " days";

      if (
        dato.fechaLog != null &&
        dato.fechaOpe != null &&
        dato.fechaFin != null
      ) {
        fecha_autoriza = dato.fecha_autorizacion;
        fecha_entrega = dato.fecha_entrega_final;
      }

      /* Datos para el semáforo */
      let semaforoEstado = "";
      let dias_atraso_semaforo = "";

      let contador = 0,
        total_items = datos.length;

      if (dato.estadoItem !== 105) {
        if (fecha_entrega !== null) {
          dias_atraso_semaforo = dato.dias_atraso;

          if (dato.ingreso_obra === dato.cantidad_orden) {
            semaforoEstado = "Entregado";
            color_semaforo = "90EE90";
            dias_atraso_semaforo = "";
          } else if (dias_atraso_semaforo > 7) {
            semaforoEstado = "Verde";
            color_semaforo = "90EE90";
            dias_atraso_semaforo = "";
          } else if (dias_atraso_semaforo >= 0 && dias_atraso_semaforo <= 7) {
            semaforoEstado = "Naranja";
            color_semaforo = "FFD700";
            dias_atraso_semaforo = "";
          } else if (dias_atraso_semaforo < 0) {
            semaforoEstado = "Rojo";
            color_semaforo = "FF0000";
            dias_atraso_semaforo = dato.dias_atraso * -1; // Para que no salga negativo
          }
        } else {
          dias_atraso_semaforo = "";
          semaforoEstado = "Procesando";
          color_semaforo = "FFFF00";

          if (
            dato.ingreso_obra > 0 &&
            dato.ingreso_obra === dato.cantidad_atendida
          ) {
            semaforoEstado = "Entregado";
            color_semaforo = "90EE90";
            dias_atraso_semaforo = "";
          } else if (dato.cantidad_atendida > 0) {
            semaforoEstado = "Stock";
            color_semaforo = "90EE90";
            dias_atraso_semaforo = "";
          }
        }
      } else {
        color_semaforo = "CDCDCD";
        semaforoEstado = "Anulado";
      }

      if (dato.estadoItem === 105) {
        porcentaje = "0%";
        estadofila = "anulado";
        estado_item = "anulado";
        estado_pedido = "anulado";
        color_mostrar = "C8C8C8";
      } else if (dato.estadoItem === 49) {
        porcentaje = "10%";
        estadofila = "Procesando";
        estado_item = "item_stock";
        estado_pedido = "Procesando";
        color_mostrar = "F8CAAD";
      } else if (dato.estadoItem === 53) {
        porcentaje = "10%";
        estadofila = "emitido";
        estado_item = "Emitido";
        estado_pedido = "Pedido Emitido";
      } else if (dato.estadoItem === 230) {
        porcentaje = "100%";
        estadofila = "comprado";
        estado_item = "Compra Local";
        estado_pedido = "Compra Local";
        color_mostrar = "FF0000";
      } else if (dato.estadoItem === 54) {
        porcentaje = "15%";
        estadofila = "aprobado";
        estado_item = "aprobado";
        estado_pedido = "aprobado";
        color_mostrar = "FC4236";
      } else if (
        dato.estadoItem === 52 &&
        dato.ingreso_obra === dato.cantidad_pedido
      ) {
        porcentaje = "100%";
        estadofila = "entregado";
        estado_item = "atendido";
        estado_pedido = "atendido";
        color_mostrar = "00FF00";
      } else if (
        dato.estadoItem === 52 &&
        dato.ingreso_obra === dato.cantidad_aprobada &&
        dato.cantidad_aprobada > 0
      ) {
        porcentaje = "100%";
        estadofila = "entregado";
        estado_item = "atendido";
        estado_pedido = "atendido";
        color_mostrar = "00FF00";
      } else if (dato.estadoItem === 52) {
        porcentaje = "20%";
        estadofila = "stock";
        estado_item = "item_stock";
        estado_pedido = "stock";
        color_mostrar = "B3C5E6";
      } else if (!dato.orden) {
        porcentaje = "15%";
        estadofila = "item_aprobado";
        estado_item = "aprobado";
        estado_pedido = "aprobado";
        color_mostrar = "FC4236";
      } else if (dato.orden && !dato.proveedor) {
        porcentaje = "25%";
        estadofila = "item_orden";
        estado_item = "aprobado";
        estado_pedido = "aprobado";
      } else if (dato.proveedor && !dato.ingreso) {
        porcentaje = "30%";
        estadofila = "item_enviado";
        estado_item = "atendido";
        estado_pedido = "atendido";
        color_mostrar = "C0DCC0";
      } else if (dato.ingreso && dato.ingreso < dato.cantidad_orden) {
        porcentaje = "40%";
        estadofila = "item_ingreso_parcial";
        estado_item = "atendido";
        estado_pedido = "atendido";
        color_mostrar = "C0DCC0";
      } else if (
        !dato.despachos &&
        dato.ingreso &&
        dato.ingreso === dato.cantidad_orden
      ) {
        porcentaje = "50%";
        estadofila = "item_ingreso_total";
        estado_item = "atendido";
        estado_pedido = "atendido";
        color_mostrar = "A9D08F";
      } else if (dato.despachos && !dato.ingreso_obra) {
        porcentaje = "75%";
        estadofila = "item_transito";
        estado_item = "atendido";
        estado_pedido = "atendido";
        color_mostrar = "00FFFF";
      } else if (
        Math.round(dato.ingreso_obra) < Math.round(dato.cantidad_orden)
      ) {
        porcentaje = "85%";
        estadofila = "item_ingreso_parcial";
        estado_item = "atendido";
        estado_pedido = "atendido";
        color_mostrar = "FFFFE1";
      } else if (
        dato.ingreso_obra &&
        Math.round(suma_atendido, 2) === Math.round(dato.cantidad_aprobada, 2)
      ) {
        porcentaje = "100%";
        estadofila = "entregado";
        estado_item = "atendido";
        estado_pedido = "atendido";
        semaforo = "Entregado";
        color_mostrar = "00FF00";
      } else if (
        dato.ingreso_obra &&
        Math.round(dato.ingreso_obra, 2) === Math.round(dato.cantidad_orden, 2)
      ) {
        porcentaje = "100%";
        estadofila = "entregado";
        estado_item = "atendido";
        estado_pedido = "atendido";
        color_mostrar = "00FF00";
      }

      let color = {
        type: "pattern",
        pattern: "solid",
        fgColor: { argb: color_mostrar }, // Color de fondo
        bgColor: { argb: color_mostrar }, // Color de fondo
      };

      //añadir a los datos
      worksheet.addRow([
        index + 1,
        porcentaje,
        dato.ccodproy,
        dato.area,
        dato.partida,
        atencion,
        clase_operacion,
        dato.anio_pedido,
        dato.pedido,
        dato.crea_pedido ? new Date(dato.crea_pedido) : null,
        dato.aprobacion_pedido ? new Date(dato.aprobacion_pedido) : null,
        dato.cantidad_pedido,
        dato.cantidad_aprobada,
        dato.cantidad_compra,
        dato.ccodprod,
        dato.unidad,
        dato.descripcion,
        dato.tipo_orden,
        dato.anio_orden,
        dato.cnumero,
        dato.fecha_orden ? new Date(dato.fecha_orden) : null,
        dato.cantidad_orden,
        dato.item_orden,
        dato.fecha_autorizacion ? new Date(dato.fecha_autorizacion) : null,
        dato.cantidad_atendida,
        dato.proveedor,
        dato.fecha_entrega ? new Date(dato.fecha_entrega) : null,
        dato.ingreso,
        dato.nota_ingreso,
        dato.fecha_recepcion_proveedor
          ? new Date(dato.fecha_recepcion_proveedor)
          : null,
        dato.saldo_recibir,
        dato.plazo,
        dato.dias_atraso,
        dato.semaforo_estado,
        dato.despachos,
        dato.cnumguia,
        dato.fecha_traslado ? new Date(dato.fecha_traslado) : null,
        dato.nota_transferencia,
        dato.nota_obra,
        dato.fecha_registro_almacen
          ? new Date(dato.fecha_registro_almacen)
          : null,
        dato.ingreso_obra,
        dato.estado_pedido,
        dato.estado_item,
        dato.nroparte,
        dato.cregistro,
        dato.operador,
        dato.transporte,
        dato.concepto,
        dato.nombre_elabora,
      ]);

      worksheet.getCell(`B${fila}`).fill = color;

      fila++;
    });

    // Rango A2:K2 con color 'BFCDDB'
    applyBackgroundColor(worksheet, 2, 2, 1, 11, "BFCDDB");

    // Rango L2:N2 con color 'FC4236'
    applyBackgroundColor(worksheet, 2, 2, 12, 14, "FC4236");

    // Rango O2:P2 con color 'BFCDDB'
    applyBackgroundColor(worksheet, 2, 2, 15, 16, "BFCDDB");

    // Rango Q2:V2 con color '00FFFF'
    applyBackgroundColor(worksheet, 2, 2, 17, 22, "00FFFF");

    // Rango W2:AD2 con color 'BFCDDB'
    applyBackgroundColor(worksheet, 2, 2, 23, 30, "BFCDDB");

    // Rango AE2:AM2 con color 'FFFF00'
    applyBackgroundColor(worksheet, 2, 2, 31, 39, "FFFF00");

    // Rango AN2:AW2 con color '127BDD'
    applyBackgroundColor(worksheet, 2, 2, 40, 49, "127BDD");

    // Exportar como archivo Blob
    const buffer = await workbook.xlsx.writeBuffer();
    const blob = new Blob([buffer], {
      type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
    });

    // Descargar archivo

    document.getElementById("waitMessage").innerHTML =
      "Descargar cargo plan...";

    const url = URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url;
    a.download = "datos_personalizados.xlsx";
    a.click();
    URL.revokeObjectURL(url);

    $("#esperarCargo").css("opacity", "0").fadeOut();
  }

  $("#cargoPlanDescrip tbody").on("click", "tr", function (e) {
    e.preventDefault();

    $(this).toggleClass("semaforoNaranja");

    return false;
  });

  $("#filtrosAvanzados").click(function (e) {
    e.preventDefault();

    $.post(
      RUTA + "cargoplanner/proyectos",
      function (data, text, requestXHR) {
        // ===== LIMPIAR LA LISTA =====
        const lista = document.getElementById("faLista");
        lista.innerHTML = "";

        // ===== VALIDAR QUE HAYA DATOS =====
        if (!data || !data.datos || !Array.isArray(data.datos)) {
          lista.innerHTML = `<div style="padding:20px; text-align:center; color:#5f6368;">
                    No hay proyectos disponibles
                </div>`;
          $("#faModal").addClass("active");
          actualizarContador();
          return;
        }

        // ===== CREAR LOS CHECKBOXES =====
        data.datos.forEach((element) => {
          const label = document.createElement("label");
          label.classList.add("fa-item"); // ✅ Función, no propiedad

          label.innerHTML = `
                    <input type="checkbox" 
                           name="${element.ncodproy}" 
                           id="${element.ncodproy}"
                           value="${element.ncodproy}">
                    <span>${element.nombre}</span>
                `;

          lista.appendChild(label);
        });

        $("#faModal").addClass("active");
        actualizarContador();
      },
      "json",
    ).fail(function (xhr, status, error) {
      console.error("Error al cargar proyectos:", error);
      mostrarMensaje("Error al cargar los proyectos", "mensaje_error");
    });

    return false;
  });

  $("#closeFilters,#btnCancelarFiltro").click(function (e) {
    e.preventDefault();

    $("#filtros").fadeOut();

    return false;
  });

  $("#faAceptar").click(function (e) {
    e.preventDefault();

    const items = [];
    const formData = new FormData();

    // ===== OBTENER CHECKBOXES SELECCIONADOS =====
    $("#faLista input[type='checkbox']:checked").each(function () {
      items.push($(this).val());
    });

    try {
      // ===== VALIDACIONES =====
      if (items.length === 0) {
        throw new Error("Debe seleccionar al menos un centro de costos");
      }
      if (!$("#faFechaInicio").val()) {
        throw new Error("Seleccione una fecha de inicio");
      }
      if (!$("#faFechaFinal").val()) {
        throw new Error("Seleccione una fecha final");
      }

      // ===== PREPARAR DATOS =====
      formData.append("costos", JSON.stringify(items));
      formData.append("fechaInicio", $("#faFechaInicio").val());
      formData.append("fechaFinal", $("#faFechaFinal").val());

      // ===== MOSTRAR LOADING =====
      $("#esperar").css({ display: "block", opacity: "1" });

      // ===== ENVIAR AL BACKEND =====
      fetch(RUTA + "cargoplanner/filtroCargoPlanExporta", {
        method: "POST",
        body: formData,
      })
        .then((response) => {
          if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
          }
          return response.json();
        })
        .then((data) => {
          $("#esperar").css({ display: "none", opacity: "0" });

          // ===== VALIDAR RESPUESTA =====
          if (data && data.documento) {
            window.location.href = data.documento;
          } else if (data && data.mensaje) {
            mostrarMensaje(data.mensaje, "mensaje_error");
          } else {
            mostrarMensaje("Error al generar el documento", "mensaje_error");
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          $("#esperar").css({ display: "none", opacity: "0" });
          mostrarMensaje("Error al generar el documento", "mensaje_error");
        });
    } catch (error) {
      mostrarMensaje(error.message, "mensaje_error");
    }

    return false;
  });

  $("#csvFile").click(function (e) {
    e.preventDefault();

    $.post(
      RUTA + "cargoplanner/archivocvs",
      { usuario: $("#id_user").val() },
      function (data, text, requestXHR) {
        console.log(data);
      },
      "json",
    );

    return false;
  });

  $("#verAyuda").click(function (e) {
    e.preventDefault();

    $("#leyendaModal").addClass("active");

    return false;
  });

  // =============================================
  // CERRAR LEYENDA
  // =============================================
  function cerrarLeyenda() {
    $("#leyendaModal").removeClass("active");
  }

  $("#leyendaCerrar, #leyendaCerrarBtn").on("click", cerrarLeyenda);

  // Cerrar al hacer clic fuera
  $("#leyendaModal").on("click", function (e) {
    if (e.target === this) cerrarLeyenda();
  });

  // Cerrar con ESC
  $(document).on("keydown", function (e) {
    if (e.key === "Escape") cerrarLeyenda();
  });

  // =============================================
  // CLICK EN UN ESTADO
  // =============================================
  $(".leyenda-item").on("click", function (e) {
    e.preventDefault();

    const estado = $(this).data("estado");
    const texto = $(this).find(".leyenda-texto").text();

    let str = $("#formConsulta").serialize();

    $("#estado_item").val(estado);

    filtrarCargoPlan();

    return false;
  });

  // =============================================
  // CERRAR MODAL
  // =============================================
  function cerrarModal() {
    $("#cpModal").removeClass("active");
    $("body").css("overflow", "");
  }

  $("#cpCerrar").on("click", cerrarModal);

  // Cerrar al hacer clic fuera
  $("#cpModal").on("click", function (e) {
    if (e.target === this) cerrarModal();
  });

  // Cerrar con ESC
  $(document).on("keydown", function (e) {
    if (e.key === "Escape") cerrarModal();
  });

  $("#cp-pdf-pedido").click(function (e) {
    e.preventDefault();

    $.post(
      RUTA + "panel/pdfPedido",
      { pedido: idpedido },
      function (data, textStatus, jqXHR) {
        $("#documentosRelacionados iframe")
          .attr("src", "")
          .attr("src", "public/documentos/temp/" + data)
          .show();

        $("#documentosRelacionados").fadeIn();
      },
      "text",
    );

    return false;
  });

  $("#adjCerrarBtn, #adjCerrar").click(function (e) {
    e.preventDefault();

    // Intentar cargar en iframe
    const iframe = document.getElementById("adjIframe");
    iframe.src = "";

    $("#documentosRelacionados").fadeOut();

    return false;
  });

  // =============================================
  // RENDERIZAR TABLA DE ORDENES
  // =============================================
  function renderizarOrdenes(data) {
    const tbody = document.getElementById("cuerpo_ordenes");
    const contenedor = document.getElementById("cp_ordenes");

    // Limpiar tabla
    tbody.innerHTML = "";

    if (!data || data.length === 0) {
      tbody.innerHTML = `
            <tr>
                <td colspan="5" style="text-align:center; padding:20px; color:#5f6368;">
                    <i class="fas fa-search" style="font-size:12px; display:block; margin-bottom:8px; color:#9aa0a6;"></i>
                    No hay órdenes registradas
                </td>
            </tr>`;

      contenedor.classList.add("oculto");

      return;
    }

    data.forEach((element) => {
      const tr = document.createElement("tr");
      tr.dataset.id = element.id_regmov;

      tr.innerHTML = `
            <td><strong>${element.cnumero}</strong></td>
            <td>${element.ffechadoc}</td>
            <td>${element.crazonsoc}</td>
            <td>${element.ccodproy}</td>
            <td class="text-center">
                <button class="cp-btn-pdf" title="Ver PDF">
                    <i class="fas fa-file-pdf"></i>
                </button>
            </td>
        `;

      tbody.appendChild(tr);
    });

    contenedor.classList.remove("oculto");
  }

  function renderizarIngresos(data) {
    const tbody = document.getElementById("cuerpo_ingresos");
    const contenedor = document.getElementById("cp_ingresos");

    // Limpiar tabla
    tbody.innerHTML = "";

    if (!data || data.length === 0) {
      tbody.innerHTML = `
            <tr>
                <td colspan="4" style="text-align:center; padding:20px; color:#5f6368;">
                    <i class="fas fa-search" style="font-size:12px; display:block; margin-bottom:8px; color:#9aa0a6;"></i>
                    No hay órdenes registradas
                </td>
            </tr>
        `;

      contenedor.classList.add("oculto");

      return;
    }

    data.forEach((element) => {
      const tr = document.createElement("tr");
      tr.dataset.id = element.id_regalm;

      tr.innerHTML = `<td><strong>${element.nnronota}</strong></td>
            <td>${element.ffecdoc}</td>
            <td>${element.cnumguia}</td>
            <td class="text-center">
                <button class="cp-btn-pdf" title="Ver PDF">
                    <i class="fas fa-file-pdf"></i>
                </button>
            </td>
        `;

      tbody.appendChild(tr);
    });

    contenedor.classList.remove("oculto");
  }

  function renderizarDespachos(data) {
    const tbody = document.getElementById("cuerpo_despachos");
    const contenedor = document.getElementById("cp_despachos");

    // Limpiar tabla
    tbody.innerHTML = "";

    if (!data || data.length === 0) {
      tbody.innerHTML = `
            <tr>
                <td colspan="5" style="text-align:center; padding:20px; color:#5f6368;">
                    <i class="fas fa-search" style="font-size:12px; display:block; margin-bottom:8px; color:#9aa0a6;"></i>
                    No hay órdenes registradas
                </td>
            </tr>
        `;
      contenedor.classList.add("oculto");

      return;
    }

    data.forEach((element) => {
      const tr = document.createElement("tr");
      tr.dataset.id = element.id_regalm;

      tr.innerHTML = `
            <td><strong>${element.nnronota}</strong></td>
            <td>${element.ffecdoc}</td>
            <td>${element.cnumguia}</td>
            <td>${element.nReferido}</td>
            <td class="text-center">
                <button class="cp-btn-pdf" title="Ver PDF">
                    <i class="fas fa-file-pdf"></i>
                </button>
            </td>
        `;

      tbody.appendChild(tr);
    });

    contenedor.classList.remove("oculto");
  }

  function renderizarRegistros(data) {
    const tbody = document.getElementById("cuerpo_registros");
    const contenedor = document.getElementById("cp_registros");

    // Limpiar tabla
    tbody.innerHTML = "";

    if (!data || data.length === 0) {
      tbody.innerHTML = `<tr>
                <td colspan="3" style="text-align:center; padding:20px; color:#5f6368;">
                    <i class="fas fa-search" style="font-size:12px; display:block; margin-bottom:8px; color:#9aa0a6;"></i>
                    No hay órdenes registradas
                </td>
            </tr>
        `;

      contenedor.classList.add("oculto");

      return;
    }

    data.forEach((element) => {
      const tr = document.createElement("tr");
      tr.dataset.id = element.idregistro;

      // ✅ Si hay archivo, muestra el botón. Si no, muestra un guión
      let boton =
        element.creferencia != null && element.creferencia != ""
          ? `<button class="cp-btn-pdf" title="Ver PDF"><i class="fas fa-file-pdf"></i></button>`
          : `<span class="cp-sin-archivo" style="color:#9aa0a6; font-size:12px;">Sin archivo</span>`;

      tr.innerHTML = `
        <td><strong>${element.idregistro}</strong></td>
        <td>${element.ffechadoc}</td>
        <td class="text-center">
            ${boton}
        </td>`;

      tbody.appendChild(tr);
    });

    contenedor.classList.remove("oculto");
  }

  // =============================================
  // EVENTO PDF (delegación)
  // =============================================
  $(document).on("click", ".cp-btn-pdf", function (e) {
    e.stopPropagation();

    const $fila = $(this).closest("tr");
    const id = $fila.data("id");
    const numero = $fila.find("td").eq(0).text().trim();
    const origen = $(this).closest("tbody").attr("id");

    $("#documentosRelacionados").fadeIn();

    switch (origen) {
      case "cuerpo_ordenes":
        $.post(
          RUTA + "pedidoseg/datosOrden",
          { id: id },
          function (data, text, requestXHR) {
            $("#documentosRelacionados iframe")
              .attr("src", "")
              .attr("src", data)
              .show();

            $("#documentosRelacionados").fadeIn();
          },
          "text",
        );
        break;

      case "cuerpo_ingresos":
        $.post(
          RUTA + "cargoplanner/vistaIngreso",
          { id: id },
          function (data, text, requestXHR) {
            $("#documentosRelacionados iframe")
              .attr("src", "")
              .attr("src", data)
              .show();

            $("#documentosRelacionados").fadeIn();
          },
          "text",
        );
        break;

      case "cuerpo_despachos":
        $.post(
          RUTA + "cargoplanner/vistaDespachos",
          { id: id },
          function (data, text, requestXHR) {
            $("#documentosRelacionados iframe")
              .attr("src", "")
              .attr("src", data)
              .show();

            $("#documentosRelacionados").fadeIn();
          },
          "text",
        );
        break;

      case "cuerpo_registros":
        $.post(
          RUTA + "cargoplanner/vistaRegistros",
          { id: id, tipo: "GA" },
          function (data, text, requestXHR) {
            $("#documentosRelacionados iframe")
              .attr("src", "")
              .attr(
                "src",
                "https://sicalsepcon.net/ibis/public/documentos/almacen/adjuntos/" +
                  data,
              )
              .show();

            $("#documentosRelacionados").fadeIn();
          },
          "json",
        );

        break;
    }
  });

  function cerrarModalFa() {
    $("#faModal").removeClass("active");
  }

  $("#faModal").on("click", function (e) {
    if (e.target === this) cerrarModalFa();
  });

  $("#faCerrar, #faCancelar").on("click", cerrarModalFa);

  $("#btnAbrirFiltros").on("click", function () {
    $("#faModal").addClass("active");
    actualizarContador();
  });

  function actualizarContador() {
    const total = $('#faLista input[type="checkbox"]').length;
    const seleccionados = $('#faLista input[type="checkbox"]:checked').length;
    $("#faContador").text(seleccionados);
    $("#faTotal").text(total);
  }

  $("#faSeleccionarTodos").on("click", function () {
    $('#faLista input[type="checkbox"]').prop("checked", true);
    actualizarContador();
  });

  $("#faQuitarTodos").on("click", function () {
    $('#faLista input[type="checkbox"]').prop("checked", false);
    actualizarContador();
  });

  $("#faLista").on("change", 'input[type="checkbox"]', actualizarContador);

  $("#faAceptar").on("click", function () {
    const seleccionados = [];
    $('#faLista input[type="checkbox"]:checked').each(function () {
      seleccionados.push($(this).val());
    });
    cerrarModalFa();
  });

  function filtrarCargoPlan() {
    let str = $("#formConsulta").serialize();

    $("#esperar").css({ display: "block", opacity: "1" });

    $.post(
      RUTA + "cargoplanner/filtroCargoPlan",
      str,
      function (data, text, requestXHR) {
        $(".itemsCargoPlanner table tbody").empty().append(data);

        $("#esperar")
          .fadeOut()
          .promise()
          .done(function () {
            iniciarPaginador();
          });

        ("text");
      },
    );
  }
});

detalles = () => {
  DATA = [];

  let TABLA = $("#cargoPlanDescrip tbody >tr");

  TABLA.each(function () {
    let ITEM = $(this).find("td").eq(0).text(),
      ESTADO = $(this).find("td").eq(1).text(),
      PROYECTO = $(this).find("td").eq(2).text(),
      AREA = $(this).find("td").eq(3).text(),
      PARTIDA = $(this).find("td").eq(4).text(),
      ATENCION = $(this).find("td").eq(5).text(),
      TIPO = $(this).find("td").eq(6).text(),
      ANIO_PEDIDO = $(this).find("td").eq(7).text(),
      NUM_PEDIDO = $(this).find("td").eq(8).text(),
      CREA_PEDIDO = $(this).find("td").eq(9).text(),
      APRO_PEDIDO = $(this).find("td").eq(10).text(),
      CANTIDAD = $(this).find("td").eq(11).text(),
      APROBADO = $(this).find("td").eq(12).text(),
      COMPRA = $(this).find("td").eq(13).text(),
      CODIGO = $(this).find("td").eq(14).text(),
      UNIDAD = $(this).find("td").eq(15).text(),
      DESCRIPCION = $(this).find("td").eq(16).text(),
      TIPO_ORDEN = $(this).find("td").eq(17).text(),
      ANIO_ORDEN = $(this).find("td").eq(18).text(),
      NRO_ORDEN = $(this).find("td").eq(19).text(),
      FECHA_ORDEN = $(this).find("td").eq(20).text(),
      CANTIDAD_ORDEN = $(this).find("td").eq(21).text(),
      ITEM_ORDEN = $(this).find("td").eq(22).text(),
      AUTORIZA_ORDEN = $(this).find("td").eq(23).text(),
      CANTIDAD_ALMACEN = $(this).find("td").eq(24).text(),
      PROVEEDOR = $(this).find("td").eq(25).text(),
      FECHA_ENTREGA = $(this).find("td").eq(26).text(),
      CANTIDAD_RECIBIDA = $(this).find("td").eq(27).text(),
      NOTA_INGRESO = $(this).find("td").eq(28).text(),
      FECHA_RECEPCION = $(this).find("td").eq(29).text(),
      SALDO_RECIBIR = $(this).find("td").eq(30).text(),
      DIAS_ENTREGA = $(this).find("td").eq(31).text(),
      DIAS_ATRASO = $(this).find("td").eq(32).text(),
      SEMAFORO = $(this).find("td").eq(33).text(),
      DESPACHO = $(this).find("td").eq(34).text(),
      NUMERO_GUIA = $(this).find("td").eq(35).text(),
      GUIA_SUNAT = $(this).find("td").eq(36).text(),
      FECHA_ENVIO = $(this).find("td").eq(37).text(),
      GUIA_TRANSFER = $(this).find("td").eq(38).text(),
      FECHA_TRASLADO = $(this).find("td").eq(39).text(),
      REGISTRO_ALMACEN = $(this).find("td").eq(40).text(),
      FECHA_REGISTRO_OBRA = $(this).find("td").eq(41).text(),
      CANTIDA_OBRA = $(this).find("td").eq(42).text(),
      ESTADO_PEDIDO = $(this).find("td").eq(43).text(),
      ESTADO_ITEM = $(this).find("td").eq(44).text(),
      NUMERO_PARTE = $(this).find("td").eq(45).text(),
      CODIGO_ACTIVO = $(this).find("td").eq(46).text(),
      OPERADOR = $(this).find("td").eq(47).text(),
      TRANSPORTE = $(this).find("td").eq(48).text(),
      OBSERVACIONES = $(this).find("td").eq(49).text(),
      SOLICITANTE = $(this).find("td").eq(50).text(),
      DESCARGA = $(this).find("td").eq(52).text(),
      INDESCRIP = $(this).find("td").eq(53).text(),
      CPUNTOENTREGA = $(this).find("td").eq(54).text(),
      FCOMPROMISO = $(this).find("td").eq(55).text();

    item = {};

    item["item"] = ITEM;
    item["estado"] = ESTADO;
    item["proyecto"] = PROYECTO;
    item["area"] = AREA;
    item["partida"] = PARTIDA;
    item["atencion"] = ATENCION;
    item["tipo"] = TIPO;
    item["anio_pedido"] = ANIO_PEDIDO;
    item["num_pedido"] = NUM_PEDIDO;
    item["crea_pedido"] = CREA_PEDIDO;
    item["apro_pedido"] = APRO_PEDIDO;
    item["codigo"] = CODIGO;
    item["unidad"] = UNIDAD;
    item["descripcion"] = DESCRIPCION;
    item["cantidad"] = CANTIDAD;
    item["aprobado"] = APROBADO;
    item["compra"] = COMPRA;

    item["tipo_orden"] = TIPO_ORDEN;
    item["anio_orden"] = ANIO_ORDEN;
    item["nro_orden"] = NRO_ORDEN;
    item["fecha_orden"] = FECHA_ORDEN;
    item["item_orden"] = ITEM_ORDEN;
    item["cantidad_orden"] = CANTIDAD_ORDEN;
    item["autoriza_orden"] = AUTORIZA_ORDEN;
    item["cantidad_almacen"] = CANTIDAD_ALMACEN;

    item["proveedor"] = PROVEEDOR;
    item["fecha_entrega"] = FECHA_ENTREGA;

    item["cantidad_recibida"] = CANTIDAD_RECIBIDA;
    item["nota_ingreso"] = NOTA_INGRESO;
    item["fecha_recepcion"] = FECHA_RECEPCION;

    item["saldo_recibir"] = SALDO_RECIBIR;
    item["dias_entrega"] = DIAS_ENTREGA;
    item["dias_atraso"] = DIAS_ATRASO;
    item["semaforo"] = SEMAFORO;

    item["despacho"] = DESPACHO;
    item["numero_guia"] = NUMERO_GUIA;
    item["guia_sunat"] = GUIA_SUNAT;
    item["fecha_envio"] = FECHA_ENVIO;

    item["registro_almacen"] = REGISTRO_ALMACEN;
    item["fecha_registro_obra"] = FECHA_REGISTRO_OBRA;
    item["cantidad_obra"] = CANTIDA_OBRA;

    item["guia_transfer"] = GUIA_TRANSFER;
    item["fecha_traslado"] = FECHA_TRASLADO;

    item["estado_pedido"] = ESTADO_PEDIDO;
    item["estado_item"] = ESTADO_ITEM;
    item["numero_parte"] = NUMERO_PARTE;
    item["codigo_activo"] = CODIGO_ACTIVO;
    item["operador"] = OPERADOR;
    item["transporte"] = TRANSPORTE;
    item["observaciones"] = OBSERVACIONES;
    item["solicitante"] = SOLICITANTE;
    item["fecha_descarga"] = DESCARGA;

    item["indescrip"] = INDESCRIP;
    item["cPuntoEntrega"] = CPUNTOENTREGA;
    item["fCompromiso"] = FCOMPROMISO;

    DATA.push(item);
  });

  return DATA;
};

function s2ab(s) {
  var buf = new ArrayBuffer(s.length);
  var view = new Uint8Array(buf);
  for (var i = 0; i < s.length; i++) view[i] = s.charCodeAt(i) & 0xff;
  return buf;
}

async function processItems() {
  fetch(RUTA + "cargoplanner/itemsProcesados")
    .then((response) => {
      return response.text();
    })
    .then((text) => {
      console.log(text);
    })
    .catch((err) => {
      console.log(err);
    });
}

function myTimer() {
  const date = new Date();
  document.getElementById("demo").innerHTML = date.toLocaleTimeString();
}

function myStopFunction() {
  clearInterval(myInterval);
}
