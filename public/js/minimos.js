$(() => {
  $("#esperar").fadeOut();

  let fila, producto, costos;

  $("#btnConsulta").click(function (e) {
    e.preventDefault();

    let formData = new FormData();
    formData.append("costos", $("#costosSearch").val());
    formData.append("codigo", $("#codigoBusqueda").val());
    formData.append("descripcion", $("#descripcionSearch").val());

    fetch(RUTA + "minimos/consultaProductos", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        let row = "",
          item = 1;

        data[0].forEach((element) => {
          const saldo =
            parseFloat(element.ingresos) - parseFloat(element.consumos);
          const estado = saldo <= element.ntotal ? "CRÍTICO" : "NORMAL";

          row += `<tr class="pointer" 
                            data-indice='${element.idreg}' 
                            data-idproducto='${element.codprod}'
                            data-detpedido='${element.idpedido}'
                            data-costos='${element.idcostos}'>
                            <td class="textoDerecha">${item++}</td>
                            <td class="textoCentro">${element.ccodprod}</td>
                            <td class="pl20px">${element.cdesprod}</td>
                            <td class="textoCentro">${element.cabrevia}</td>
                            <td class="textoDerecha">${element.ingresos || ""}</td>
                            <td class="textoDerecha">${element.consumos || ""}</td>
                            <td class="textoCentro">${element.ffecha || ""}</td>
                            <td class="textoDerecha">${element.ntotal || ""}</td>
                            <td class="textoCentro"><span style="background:${estado === "CRÍTICO" ? "#FF6B6B" : "#C6F6D5"}; padding:4px 8px; border-radius:12px; font-size:11px;">${estado}</span></td>
                        </tr>`;
        });

        $("#tablaPrincipal tbody").empty().append(row);
      });

    return false;
  });

  $("#tablaPrincipal tbody").on("dblclick", "tr", function (e) {
    e.preventDefault();

    fila = $(this);
    producto = $(this).data("idproducto");
    costos = $(this).data("costos");

    $("#codigoSearch").val($(this).find("td").eq(1).text());
    $("#descripSearch").val($(this).find("td").eq(2).text());

    $("#dialogo_registro").fadeIn();

    return false;
  });

  $("#btnCancelarDialogoMinimo").click(function (e) {
    e.preventDefault();

    $("#dialogo_registro").fadeOut();

    return false;
  });

  $("#btnAceptarDialogoMinimo").click(function (e) {
    e.preventDefault();

    fila.find("td").eq(5).text($("#fecha").val());
    fila.find("td").eq(6).text($("#total_minimo").val());

    try {
      const formData = new FormData();
      formData.append("idprod", producto);
      formData.append("registra", $("#id_user").val());
      formData.append("fecha", $("#fecha").val());
      formData.append("personal", $("#cant_personal").val());
      formData.append("porcentaje", $("#porcentaje_minimo").val());
      formData.append("total", $("#total_minimo").val());
      formData.append("observaciones", $("#observaciones_dialogo").val());
      formData.append("costos", costos);

      fetch(RUTA + "minimos/registro", {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.error == 1) {
            mostrarMensaje("📌 Registro grabado");
            $("#dialogo_registro").fadeOut();
          } else {
            mostrarMensaje("💣 Error al registrar el consumo");
          }
        });
    } catch (error) {
      console.log(error.message);
    }

    return false;
  });

  $("#cant_personal,#porcentaje_minimo").keypress(function (e) {
    if (e.which == 13) {
      let total_minimo =
        $("#cant_personal").val() * ($("#porcentaje_minimo").val() / 100);

      $("#total_minimo").val(total_minimo);
    }
  });

  $("#excelFile").click(function(e){
    exportarExcel();
  });

  async function exportarExcel() {
    // Obtener la tabla y sus filas
    const tabla = document.getElementById("tablaPrincipal");
    const tbody = tabla.getElementsByTagName("tbody")[0];
    const filas = tbody.getElementsByTagName("tr");

    // Verificar si hay datos
    if (filas.length === 0) {
        alert("No hay datos en la tabla para exportar");
        return;
    }

    // Crear workbook
    const workbook = new ExcelJS.Workbook();
    workbook.creator = "Sistema de Inventario";
    workbook.created = new Date();

    // Hoja principal
    const worksheet = workbook.addWorksheet("Reporte de Inventario", {
        pageSetup: { paperSize: 9, orientation: "landscape" },
    });

    // Estilos
    const headerStyle = {
        font: { bold: true, color: { argb: "FFFFFFFF" }, size: 11, name: "Segoe UI" },
        fill: { type: "pattern", pattern: "solid", fgColor: { argb: "FF1E3C72" } },
        alignment: { horizontal: "center", vertical: "middle", wrapText: true },
        border: { top: { style: "thin" }, bottom: { style: "thin" }, left: { style: "thin" }, right: { style: "thin" } },
    };

    const titleStyle = {
        font: { bold: true, size: 16, color: { argb: "FFFFFFFF" }, name: "Segoe UI" },
        fill: { type: "pattern", pattern: "solid", fgColor: { argb: "FF1E3C72" } },
        alignment: { horizontal: "center", vertical: "middle" },
    };

    const subtitleStyle = {
        font: { bold: true, size: 11, color: { argb: "FF333333" }, name: "Segoe UI" },
        fill: { type: "pattern", pattern: "solid", fgColor: { argb: "FFF0F0F0" } },
    };

    // Título
    worksheet.mergeCells("A1:I1");
    worksheet.getCell("A1").value = "REPORTE DE INVENTARIO DE PRODUCTOS";
    worksheet.getCell("A1").style = titleStyle;
    worksheet.getRow(1).height = 30;

    worksheet.mergeCells("A2:I2");
    worksheet.getCell("A2").value = `Fecha de generación: ${new Date().toLocaleString()} | Grupo: 17 | Clase: 56 | Centro Costos: 57`;
    worksheet.getCell("A2").style = subtitleStyle;
    worksheet.getRow(2).height = 20;

    // Encabezados de la tabla
    const headers = ["Item", "Código", "Descripción", "Unidad", "Cantidad Ingreso", "Cantidad Consumida", "Fecha Registro", "Cantidad Mínima", "Estado"];

    // Configurar columnas
    worksheet.columns = [
        { header: "Item", key: "item", width: 8 },
        { header: "Código", key: "codigo", width: 12 },
        { header: "Descripción", key: "descripcion", width: 55 },
        { header: "Unidad", key: "unidad", width: 10 },
        { header: "Cantidad Ingreso", key: "ingresos", width: 15 },
        { header: "Cantidad Consumida", key: "consumos", width: 15 },
        { header: "Fecha Registro", key: "fecha", width: 15 },
        { header: "Cantidad Mínima", key: "stock_minimo", width: 15 },
        { header: "Estado", key: "estado", width: 15 },
    ];

    // Aplicar estilo al encabezado (fila 4)
    const headerRow = worksheet.getRow(4);
    headerRow.values = headers;
    headerRow.eachCell((cell) => {
        cell.style = headerStyle;
    });
    headerRow.height = 40;

    // LEER DATOS DE CADA FILA DE LA TABLA HTML
    let totalIngresos = 0;
    let totalConsumos = 0;
    let criticos = 0;
    let stockMinimoCount = 0;
    let bajoCount = 0;
    let excesoCount = 0;
    let normalCount = 0;

    for (let i = 0; i < filas.length; i++) {
        const celdas = filas[i].getElementsByTagName("td");

        if (celdas.length >= 9) {  // Cambiado a 9 porque tienes 9 columnas
            // Extraer datos de cada celda
            const item = celdas[0]?.innerText || "";
            const codigo = celdas[1]?.innerText || "";
            const descripcion = celdas[2]?.innerText || "";
            const unidad = celdas[3]?.innerText || "";
            let ingresos = celdas[4]?.innerText || "0";
            let consumos = celdas[5]?.innerText || "0";
            const fecha = celdas[6]?.innerText || "-";
            const stockMinimo = celdas[7]?.innerText || "-";
            let estado = celdas[8]?.innerText?.trim() || "";

            // Limpiar formato de números
            ingresos = parseFloat(ingresos.toString().replace(/,/g, "")) || 0;
            consumos = parseFloat(consumos.toString().replace(/,/g, "")) || 0;

            // Acumular totales
            totalIngresos += ingresos;
            totalConsumos += consumos;

            // Contar por estado
            if (estado === "CRÍTICO") criticos++;
            else if (estado === "STOCK MÍNIMO") stockMinimoCount++;
            else if (estado === "BAJO") bajoCount++;
            else if (estado === "EXCESO") excesoCount++;
            else if (estado === "NORMAL") normalCount++;

            // Agregar fila al Excel
            const row = worksheet.addRow({
                item: item,
                codigo: codigo,
                descripcion: descripcion,
                unidad: unidad,
                ingresos: ingresos.toFixed(2),
                consumos: consumos.toFixed(2),
                fecha: fecha,
                stock_minimo: stockMinimo,
                estado: estado,
            });

            // Aplicar estilos según estado
            row.getCell(9).alignment = { horizontal: "center", vertical: "middle" };

            if (estado === "CRÍTICO") {
                row.getCell(9).style = {
                    fill: { type: "pattern", pattern: "solid", fgColor: { argb: "FFDC3545" } },
                    font: { bold: true, color: { argb: "FFFFFFFF" } },
                };
            } else if (estado === "STOCK MÍNIMO" || estado === "BAJO") {
                row.getCell(9).style = {
                    fill: { type: "pattern", pattern: "solid", fgColor: { argb: "FFFFC107" } },
                    font: { bold: true, color: { argb: "FF856404" } },
                };
            } else if (estado === "EXCESO") {
                row.getCell(9).style = {
                    fill: { type: "pattern", pattern: "solid", fgColor: { argb: "FF17A2B8" } },
                    font: { bold: true, color: { argb: "FFFFFFFF" } },
                };
            } else if (estado === "NORMAL") {
                row.getCell(9).style = {
                    fill: { type: "pattern", pattern: "solid", fgColor: { argb: "FF28A745" } },
                    font: { bold: true, color: { argb: "FFFFFFFF" } },
                };
            }

            row.height = 20;
        }
    }

    // Agregar fila de totales
    const totalRow = worksheet.addRow({
        item: "",
        codigo: "",
        descripcion: "TOTALES:",
        unidad: "",
        ingresos: totalIngresos.toLocaleString(),
        consumos: totalConsumos.toLocaleString(),
        fecha: "",
        stock_minimo: "",
        estado: "",
    });

    totalRow.getCell(3).style = { font: { bold: true }, alignment: { horizontal: "right" } };
    totalRow.getCell(5).style = { font: { bold: true, color: { argb: "FF28A745" } }, alignment: { horizontal: "right" } };
    totalRow.getCell(6).style = { font: { bold: true, color: { argb: "FFDC3545" } }, alignment: { horizontal: "right" } };
    totalRow.height = 25;

    // Resumen de estados
    worksheet.addRow([]);
    const resumenHeader = worksheet.addRow(["RESUMEN POR ESTADO:"]);
    resumenHeader.getCell(1).style = { font: { bold: true, size: 12 }, fill: { type: "pattern", pattern: "solid", fgColor: { argb: "FFF0F0F0" } } };
    worksheet.addRow(["CRÍTICO (Sin stock)", criticos]);
    worksheet.addRow(["STOCK MÍNIMO", stockMinimoCount]);
    worksheet.addRow(["BAJO (1-50 UND)", bajoCount]);
    worksheet.addRow(["NORMAL (51-200 UND)", normalCount]);
    worksheet.addRow(["EXCESO (>200 UND)", excesoCount]);

    // Congelar paneles
    worksheet.views = [{ state: "frozen", xSplit: 0, ySplit: 4 }];

    // ✅ GENERAR EL BLOB DESDE EL WORKBOOK
    const buffer = await workbook.xlsx.writeBuffer();
    const blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
    
    // Crear link de descarga
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    const fecha = new Date().toISOString().slice(0, 19).replace(/:/g, '-');
    
    link.href = url;
    link.download = `Reporte_StockMinimos_${fecha}.xlsx`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
}
});
