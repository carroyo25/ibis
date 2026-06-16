$(() => {
  $("#esperar").fadeOut();

  let fila, producto, costos;
  const RUTA = $("#url_base").val() || "";

  // ===== VERIFICAR PERMISOS =====
  async function verificarPermisos(usuario, modulo) {
    const formData = new FormData();
    formData.append("user", usuario);
    formData.append("modulo", modulo);

    try {
      const response = await fetch(RUTA + "minimos/permisos", {
        method: "POST",
        body: formData,
      });
      const data = await response.json();
      return data;
    } catch (error) {
      return { permiso: false };
    }
  }

  // FUNCIÓN CON PAGINACIÓN
  function consultarDatos(pagina) {
    pagina = pagina || 1;

    let formData = new FormData();
    formData.append("costos", $("#costosSearch").val());
    formData.append("codigo", $("#codigoBusqueda").val());
    formData.append("descripcion", $("#descripcionSearch").val());
    formData.append("page", pagina);

    $("#tablaBody").html(
      '<tr><td colspan="9" style="text-align:center; padding:40px;"><i class="fas fa-spinner fa-spin" style="font-size:24px;"></i><p>Cargando...</p></td></tr>',
    );

    fetch(RUTA + "minimos/consultaProductosPaginado", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        /*console.log("Datos recibidos:", data);
        console.log("Total registros:", data.total);
        console.log("Total páginas:", data.total_paginas);
        console.log("Página actual:", data.pagina);*/

        let row = "";
        let item = (pagina - 1) * 10 + 1;

        if (data.success && data.data.length > 0) {
          data.data.forEach((element) => {
            const ingresos = parseFloat(element.ingresos) || 0;
            const consumos = parseFloat(element.consumos) || 0;
            const saldo = ingresos - consumos;
            const stockMinimo = parseFloat(element.ntotal) || 0;

            let estado, colorEstado;
            if (saldo <= 0) {
              estado = "CRÍTICO";
              colorEstado = "#FF6B6B";
            } else if (stockMinimo > 0 && saldo <= stockMinimo) {
              estado = "MÍNIMO";
              colorEstado = "#FFC107";
            } else if (saldo > 0 && saldo <= 50) {
              estado = "BAJO";
              colorEstado = "#FD7E14";
            } else if (saldo > 200) {
              estado = "EXCESO";
              colorEstado = "#17A2B8";
            } else {
              estado = "NORMAL";
              colorEstado = "#C6F6D5";
            }

            row += `<tr class="pointer" 
                                data-idproducto='${element.codprod || ""}'
                                data-costos='${element.idcostos || ""}'>
                                <td class="textoDerecha">${item++}</td>
                                <td class="textoCentro">${element.ccodprod || ""}</td>
                                <td class="pl20px">${element.cdesprod || ""}</td>
                                <td class="textoCentro">${element.cabrevia || ""}</td>
                                <td class="textoDerecha">${element.ingresos || "0.00"}</td>
                                <td class="textoDerecha">${element.consumos || "0.00"}</td>
                                <td class="textoCentro">${element.ffecha || ""}</td>
                                <td class="textoDerecha">${element.ntotal || ""}</td>
                                <td class="textoCentro"><span style="background:${colorEstado}; padding:4px 8px; border-radius:12px; font-size:11px; color:${estado === "CRÍTICO" || estado === "EXCESO" ? "white" : "#333"}; font-weight:bold;">${estado}</span></td>
                            </tr>`;
          });
        } else {
          row = `<tr><td colspan="9" style="text-align:center; padding:20px; color:#999;">No se encontraron resultados</td></tr>`;
        }

        $("#tablaBody").empty().append(row);

        // ===== GENERAR PAGINADOR =====
        let pag = `<div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; padding:10px 0; gap:10px;">
                <div style="font-size:14px; color:#666;">
                    <i class="fas fa-database" style="margin-right:5px;"></i>
                    <strong>${data.total || 0}</strong> registros encontrados
                </div>
                <div style="display:flex; justify-content:center; align-items:center; gap:5px; flex-wrap:wrap;">`;

        if (data.total_paginas > 1) {
          pag += `<button onclick="consultarDatos(${pagina - 1})" style="padding:5px 12px; border:1px solid #ddd; background:white; cursor:pointer; border-radius:4px; ${pagina === 1 ? "opacity:0.5; cursor:not-allowed;" : ""}" ${pagina === 1 ? "disabled" : ""}>
                    <i class="fas fa-chevron-left"></i>
                </button>`;

          const maxPaginasMostrar = 5;
          let inicioPaginas = Math.max(
            1,
            pagina - Math.floor(maxPaginasMostrar / 2),
          );
          let finPaginas = Math.min(
            data.total_paginas,
            inicioPaginas + maxPaginasMostrar - 1,
          );

          if (finPaginas - inicioPaginas < maxPaginasMostrar - 1) {
            inicioPaginas = Math.max(1, finPaginas - maxPaginasMostrar + 1);
          }

          if (inicioPaginas > 1) {
            pag += `<span style="padding:5px 10px; color:#999;">…</span>`;
          }

          for (let i = inicioPaginas; i <= finPaginas; i++) {
            pag += `<button onclick="consultarDatos(${i})" style="padding:5px 12px; border:1px solid #ddd; background:${i === pagina ? "#1e3c72" : "white"}; color:${i === pagina ? "white" : "#333"}; cursor:pointer; border-radius:4px; font-weight:${i === pagina ? "bold" : "normal"};">${i}</button>`;
          }

          if (finPaginas < data.total_paginas) {
            pag += `<span style="padding:5px 10px; color:#999;">…</span>`;
          }

          pag += `<button onclick="consultarDatos(${pagina + 1})" style="padding:5px 12px; border:1px solid #ddd; background:white; cursor:pointer; border-radius:4px; ${pagina === data.total_paginas ? "opacity:0.5; cursor:not-allowed;" : ""}" ${pagina === data.total_paginas ? "disabled" : ""}>
                    <i class="fas fa-chevron-right"></i>
                </button>`;

          pag += `<span style="margin-left:10px; font-size:13px; color:#666;">Página ${pagina} de ${data.total_paginas}</span>`;
        } else {
          pag += `<span style="font-size:13px; color:#999;">Página 1 de 1</span>`;
        }

        pag += `</div></div>`;

        $("#paginador").html(pag);
      })
      .catch((error) => {
        console.error("Error:", error);
        $("#tablaBody").html(
          `<tr><td colspan="9" style="text-align:center; color:red; padding:40px;"><i class="fas fa-exclamation-triangle" style="font-size:24px; display:block; margin-bottom:10px;"></i>Error al cargar datos</td></tr>`,
        );
        $("#paginador").empty();
      });
  }

  window.consultarDatos = consultarDatos;

  // EVENTO BUSCAR
  $("#btnConsulta").click(function (e) {
    e.preventDefault();
    consultarDatos(1);
    return false;
  });

  // ENTER EN CAMPOS DE BÚSQUEDA
  $("#codigoBusqueda, #descripcionSearch").keypress(function (e) {
    if (e.which === 13) {
      e.preventDefault();
      consultarDatos(1);
    }
  });

  // ===== DOBLE CLIC EN FILA PARA REGISTRAR MÍNIMO =====
  $("#tablaBody").on("dblclick", "tr.pointer", async function (e) {
    e.preventDefault();

    // Verificar permisos antes de abrir el modal
    const idUser = $("#id_user").val() || 1;
    const permisos = await verificarPermisos(idUser, 65);

    //console.log("Permisos:", permisos.datos[0].agrega);

    if (!permisos || !permisos.datos[0].agrega) {
      mostrarMensaje(
        "⚠️ No tienes permisos para registrar mínimos",
        "mensaje_error",
      );
      return; // No abre el modal
    }

    // Si tiene permiso, continúa con el registro
    fila = $(this);
    producto = $(this).data("idproducto");
    costos = $(this).data("costos");

    $("#codigoSearch").val($(this).find("td").eq(1).text());
    $("#descripSearch").val($(this).find("td").eq(2).text());

    $("#cant_personal").val("");
    $("#porcentaje_minimo").val("");
    $("#total_minimo").val("");
    $("#observaciones_dialogo").val("");
    $("#fecha").val(new Date().toISOString().split("T")[0]);

    $("#dialogo_registro").fadeIn();

    return false;
  });

  // CANCELAR REGISTRO
  $("#btnCancelarDialogoMinimo").click(function (e) {
    e.preventDefault();
    $("#dialogo_registro").fadeOut();
    return false;
  });

  // ACEPTAR REGISTRO
  $("#btnAceptarDialogoMinimo").click(function (e) {
    e.preventDefault();

    const fecha = $("#fecha").val();
    const total = $("#total_minimo").val();

    if (!fecha) {
      mostrarMensaje("⚠️ La fecha es obligatoria", "mensaje_error");
      return;
    }

    if (!total || total <= 0) {
      mostrarMensaje("⚠️ El total mínimo debe ser mayor a 0", "mensaje_error");
      return;
    }

    try {
      const formData = new FormData();
      formData.append("idprod", producto);
      formData.append("registra", $("#id_user").val());
      formData.append("fecha", fecha);
      formData.append("personal", $("#cant_personal").val() || 0);
      formData.append("porcentaje", $("#porcentaje_minimo").val() || 0);
      formData.append("total", total);
      formData.append("observaciones", $("#observaciones_dialogo").val());
      formData.append("costos", costos);

      fetch(RUTA + "minimos/registro", {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.error == 1) {
            mostrarMensaje(
              "📌 Registro grabado correctamente",
              "mensaje_correcto",
            );
            $("#dialogo_registro").fadeOut();
            consultarDatos(1);
          } else {
            mostrarMensaje(
              "💣 Error al registrar: " + (data.mensaje || ""),
              "mensaje_error",
            );
          }
        })
        .catch((error) => {
          console.error("Error en registro:", error);
          mostrarMensaje("💣 Error al registrar", "mensaje_error");
        });
    } catch (error) {
      console.log(error.message);
      mostrarMensaje("💣 Error en el registro", "mensaje_error");
    }

    return false;
  });

  // CALCULAR TOTAL MÍNIMO AUTOMÁTICAMENTE
  $("#cant_personal, #porcentaje_minimo").on("input", function () {
    const personal = parseFloat($("#cant_personal").val()) || 0;
    const porcentaje = parseFloat($("#porcentaje_minimo").val()) || 0;
    if (personal > 0 && porcentaje > 0) {
      const total = personal * (porcentaje / 100);
      $("#total_minimo").val(total.toFixed(2));
    } else {
      $("#total_minimo").val("");
    }
  });

  // EXPORTAR A EXCEL - TODOS LOS DATOS
  $("#excelFile").click(async function (e) {
    e.preventDefault();

    mostrarMensaje("⏳ Generando reporte completo...", "mensaje_info");

    try {
      // Obtener TODOS los datos desde el servidor
      const formData = new FormData();
      formData.append("costos", $("#costosSearch").val());
      formData.append("codigo", $("#codigoBusqueda").val());
      formData.append("descripcion", $("#descripcionSearch").val());

      const response = await fetch(RUTA + "minimos/exportarExcel", {
        method: "POST",
        body: formData,
      });

      const data = await response.json();

      if (!data.success || data.data.length === 0) {
        mostrarMensaje("⚠️ No hay datos para exportar", "mensaje_error");
        return;
      }

      // Verificar que ExcelJS esté disponible
      if (typeof ExcelJS === "undefined") {
        mostrarMensaje(
          "⏳ Cargando librería ExcelJS, espere un momento...",
          "mensaje_info",
        );
        setTimeout(function () {
          $("#excelFile").click();
        }, 2000);
        return;
      }

      const workbook = new ExcelJS.Workbook();
      workbook.creator = "Sistema de Inventario";
      workbook.created = new Date();

      const worksheet = workbook.addWorksheet("Reporte de Inventario", {
        pageSetup: { paperSize: 9, orientation: "landscape" },
      });

      // ===== ESTILOS =====
      const titleStyle = {
        font: {
          bold: true,
          size: 16,
          color: { argb: "FFFFFFFF" },
          name: "Arial",
        },
        fill: {
          type: "pattern",
          pattern: "solid",
          fgColor: { argb: "FF1E3C72" },
        },
        alignment: { horizontal: "center", vertical: "middle" },
      };

      const headerStyle = {
        font: {
          bold: true,
          color: { argb: "FFFFFFFF" },
          size: 11,
          name: "Arial",
        },
        fill: {
          type: "pattern",
          pattern: "solid",
          fgColor: { argb: "FF1E3C72" },
        },
        alignment: { horizontal: "center", vertical: "middle", wrapText: true },
        border: {
          top: { style: "thin" },
          bottom: { style: "thin" },
          left: { style: "thin" },
          right: { style: "thin" },
        },
      };

      const criticalStyle = {
        fill: {
          type: "pattern",
          pattern: "solid",
          fgColor: { argb: "FFDC3545" },
        },
        font: { bold: true, color: { argb: "FFFFFFFF" } },
        alignment: { horizontal: "center", vertical: "middle" },
      };

      const normalStyle = {
        fill: {
          type: "pattern",
          pattern: "solid",
          fgColor: { argb: "FF28A745" },
        },
        font: { bold: true, color: { argb: "FFFFFFFF" } },
        alignment: { horizontal: "center", vertical: "middle" },
      };

      const warningStyle = {
        fill: {
          type: "pattern",
          pattern: "solid",
          fgColor: { argb: "FFFFC107" },
        },
        font: { bold: true, color: { argb: "FF856404" } },
        alignment: { horizontal: "center", vertical: "middle" },
      };

      const lowStyle = {
        fill: {
          type: "pattern",
          pattern: "solid",
          fgColor: { argb: "FFFD7E14" },
        },
        font: { bold: true, color: { argb: "FFFFFFFF" } },
        alignment: { horizontal: "center", vertical: "middle" },
      };

      const excessStyle = {
        fill: {
          type: "pattern",
          pattern: "solid",
          fgColor: { argb: "FF17A2B8" },
        },
        font: { bold: true, color: { argb: "FFFFFFFF" } },
        alignment: { horizontal: "center", vertical: "middle" },
      };

      // ===== TÍTULO =====
      worksheet.mergeCells("A1:I1");
      worksheet.getCell("A1").value = "REPORTE DE INVENTARIO DE PRODUCTOS";
      worksheet.getCell("A1").style = titleStyle;
      worksheet.getRow(1).height = 35;

      worksheet.mergeCells("A2:I2");
      worksheet.getCell("A2").value =
        `Fecha de generación: ${new Date().toLocaleString()} | Total: ${data.total} registros`;
      worksheet.getCell("A2").style = {
        font: {
          bold: true,
          size: 11,
          color: { argb: "FF333333" },
          name: "Arial",
        },
        fill: {
          type: "pattern",
          pattern: "solid",
          fgColor: { argb: "FFF0F0F0" },
        },
      };
      worksheet.getRow(2).height = 22;

      // ===== ENCABEZADOS =====
      const headers = [
        "Item",
        "Código",
        "Descripción",
        "Unidad",
        "Cantidad Ingreso",
        "Cantidad Consumida",
        "Fecha Registro",
        "Cantidad Mínima",
        "Estado",
      ];

      worksheet.columns = [
        { header: "Item", key: "item", width: 8 },
        { header: "Código", key: "codigo", width: 15 },
        { header: "Descripción", key: "descripcion", width: 55 },
        { header: "Unidad", key: "unidad", width: 10 },
        { header: "Cantidad Ingreso", key: "ingresos", width: 18 },
        { header: "Cantidad Consumida", key: "consumos", width: 18 },
        { header: "Fecha Registro", key: "fecha", width: 18 },
        { header: "Cantidad Mínima", key: "stock_minimo", width: 18 },
        { header: "Estado", key: "estado", width: 18 },
      ];

      // Aplicar estilo al encabezado
      const headerRow = worksheet.getRow(4);
      headerRow.values = headers;
      headerRow.eachCell((cell) => {
        cell.style = headerStyle;
      });
      headerRow.height = 40;

      // ===== DATOS (TODOS) =====
      let totalIngresos = 0;
      let totalConsumos = 0;
      let rowIndex = 5;
      let item = 1;

      data.data.forEach((element) => {
        const ingresos = parseFloat(element.ingresos?.replace(/,/g, "") || 0);
        const consumos = parseFloat(element.consumos?.replace(/,/g, "") || 0);
        totalIngresos += ingresos;
        totalConsumos += consumos;

        const row = worksheet.getRow(rowIndex);
        row.getCell(1).value = item++;
        row.getCell(2).value = element.codprod || "";
        row.getCell(3).value = element.cdesprod || "";
        row.getCell(4).value = element.cabrevia || "";
        row.getCell(5).value = ingresos;
        row.getCell(6).value = consumos;
        row.getCell(7).value = element.ffecha || "-";
        row.getCell(8).value = element.ntotal || "-";
        row.getCell(9).value = element.estado || "NORMAL";

        // Aplicar estilo según estado
        const estadoCell = row.getCell(9);
        const estado = element.estado || "NORMAL";
        if (estado === "CRÍTICO") {
          estadoCell.style = criticalStyle;
        } else if (estado === "MÍNIMO") {
          estadoCell.style = warningStyle;
        } else if (estado === "BAJO") {
          estadoCell.style = lowStyle;
        } else if (estado === "EXCESO") {
          estadoCell.style = excessStyle;
        } else {
          estadoCell.style = normalStyle;
        }

        // Aplicar bordes a todas las celdas
        row.eachCell((cell) => {
          cell.border = {
            top: { style: "thin" },
            bottom: { style: "thin" },
            left: { style: "thin" },
            right: { style: "thin" },
          };
        });

        row.height = 20;
        rowIndex++;
      });

      // ===== FILA DE TOTALES =====
      const totalRow = worksheet.getRow(rowIndex);
      totalRow.getCell(1).value = "";
      totalRow.getCell(2).value = "";
      totalRow.getCell(3).value = "TOTALES:";
      totalRow.getCell(4).value = "";
      totalRow.getCell(5).value = totalIngresos.toFixed(2);
      totalRow.getCell(6).value = totalConsumos.toFixed(2);
      totalRow.getCell(7).value = "";
      totalRow.getCell(8).value = "";
      totalRow.getCell(9).value = "";

      totalRow.getCell(3).style = {
        font: { bold: true },
        alignment: { horizontal: "right" },
      };
      totalRow.getCell(5).style = {
        font: { bold: true, color: { argb: "FF28A745" } },
        alignment: { horizontal: "right" },
      };
      totalRow.getCell(6).style = {
        font: { bold: true, color: { argb: "FFDC3545" } },
        alignment: { horizontal: "right" },
      };
      totalRow.eachCell((cell) => {
        cell.border = {
          top: { style: "medium" },
          bottom: { style: "medium" },
          left: { style: "thin" },
          right: { style: "thin" },
        };
      });
      totalRow.height = 25;

      // ===== RESUMEN POR ESTADO =====
      rowIndex += 2;
      const resumenRow = worksheet.getRow(rowIndex);
      resumenRow.getCell(1).value = "RESUMEN POR ESTADO:";
      resumenRow.getCell(1).style = {
        font: { bold: true, size: 12 },
        fill: {
          type: "pattern",
          pattern: "solid",
          fgColor: { argb: "FFF0F0F0" },
        },
      };

      rowIndex++;
      const estados = {};
      data.data.forEach((element) => {
        const estado = element.estado || "NORMAL";
        estados[estado] = (estados[estado] || 0) + 1;
      });

      const colores = {
        CRÍTICO: "FFDC3545",
        MÍNIMO: "FFFFC107",
        BAJO: "FFFD7E14",
        NORMAL: "FF28A745",
        EXCESO: "FF17A2B8",
      };

      for (const [estado, cantidad] of Object.entries(estados)) {
        const row = worksheet.getRow(rowIndex);
        row.getCell(1).value = estado;
        row.getCell(2).value = cantidad;

        const cell = row.getCell(1);
        if (colores[estado]) {
          cell.style = {
            fill: {
              type: "pattern",
              pattern: "solid",
              fgColor: { argb: colores[estado] },
            },
            font: { bold: true, color: { argb: "FFFFFFFF" } },
            alignment: { horizontal: "center" },
          };
        }
        row.height = 18;
        rowIndex++;
      }

      // ===== CONGELAR PANELES =====
      worksheet.views = [{ state: "frozen", xSplit: 0, ySplit: 4 }];

      // ===== GENERAR Y DESCARGAR =====
      const buffer = await workbook.xlsx.writeBuffer();
      const blob = new Blob([buffer], {
        type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
      });

      const link = document.createElement("a");
      const url = URL.createObjectURL(blob);
      const fecha = new Date().toISOString().slice(0, 10);

      link.href = url;
      link.download = `Reporte_StockMinimos_${fecha}.xlsx`;
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
      URL.revokeObjectURL(url);

      mostrarMensaje(
        `📊 Reporte exportado correctamente (${data.total} registros)`,
        "mensaje_correcto",
      );
    } catch (error) {
      console.error("Error al exportar:", error);
      mostrarMensaje("💣 Error al exportar: " + error.message, "mensaje_error");
    }
  });

  // FUNCIÓN PARA MOSTRAR MENSAJES
  function mostrarMensaje(mensaje, clase) {
    if (typeof window.mostrarMensaje === "function") {
      window.mostrarMensaje(mensaje, clase);
    } else {
      alert(mensaje);
    }
  }

  // CARGAR DATOS AL INICIO
  setTimeout(function () {
    consultarDatos(1);
  }, 300);
});
