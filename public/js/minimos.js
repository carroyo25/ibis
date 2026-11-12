$(() => {
  $("#esperar").fadeOut();

  let fila, producto, costos, origen;
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
  /*function consultarDatos(pagina) {
    pagina = pagina || 1;

    const costosSearch = $("#costosSearch").val() || "";
    const codigoBusqueda = $("#codigoBusqueda").val() || "";
    const descripcionSearch = $("#descripcionSearch").val() || "";

    // Mostrar loading
    $("#tablaPrincipal tbody").html(
      '<tr><td colspan="9" style="text-align:center; padding:40px;"><i class="fas fa-spinner fa-spin" style="font-size:24px;"></i><p>Cargando...</p></td></tr>'
    );

    let formData = new FormData();
    formData.append("costos", costosSearch);
    formData.append("codigo", codigoBusqueda);
    formData.append("descripcion", descripcionSearch);
    formData.append("page", pagina);

    fetch(RUTA + "minimos/consultaProductosPaginado", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        let row = "";
        let item = (pagina - 1) * 5 + 1; // Asumiendo limit 5 del backend

        if (data.success && data.data && data.data.length > 0) {
          data.data.forEach((element) => {
            
            // ===== MAPEO DE CAMPOS =====
            const idprod = element.id_cprod || "";
            const ccodprod = element.ccodprod || "";
            const cdesprod = element.cdesprod || "";
            const unidad = element.unidad || "";
            const proyecto = element.proyecto || "";
            
            // Parseo de números enviados por el backend
            const saldoReal = parseFloat(element.saldo || 0);
            const stockMinimo = parseFloat(element.nminimo || 0);
            const estado = element.estado_stock || "NORMAL";
            const ffecha = element.ffecha || "-";

            // Formateador a 2 decimales con comas
            const fmt = (n) => Number(n).toLocaleString("es-PE", { minimumFractionDigits: 2, maximumFractionDigits: 2 });

            // ===== COLORES DEL ESTADO =====
            let colorEstado, textoColor;
            switch (estado) {
              case "CRÍTICO":
                colorEstado = "#DC3545"; textoColor = "white"; break;
              case "MÍNIMO":
                colorEstado = "#FFC107"; textoColor = "#856404"; break;
              case "EXCESO":
                colorEstado = "#17A2B8"; textoColor = "white"; break;
              case "SIN MÍNIMO":
                colorEstado = "#6C757D"; textoColor = "white"; break;
              default:
                colorEstado = "#C6F6D5"; textoColor = "#2D3748"; break;
            }

            // ===== CONSTRUIR FILA =====
            row += `<tr class="pointer" 
                          data-idproducto="${idprod}"
                          data-costos="${element.idcostos || ''}"
                          data-ccodprod="${ccodprod}"
                          data-cdesprod="${cdesprod}">
                      <td class="textoDerecha">${item++}</td>
                      <td class="textoCentro">${ccodprod}</td>
                      <td class="pl20px">${cdesprod}</td>
                      <td class="textoCentro">${unidad}</td>
                      <td class="textoCentro">${ffecha}</td>
                      <td class="textoDerecha">${stockMinimo > 0 ? fmt(stockMinimo) : "-"}</td>
                      <td class="textoDerecha"><strong>${fmt(saldoReal)}</strong></td>
                      <td class="textoCentro">
                          <span style="background:${colorEstado}; padding:4px 10px; border-radius:12px; font-size:11px; color:${textoColor}; font-weight:bold; display:inline-block; white-space:nowrap;">
                              ${estado}
                          </span>
                      </td>
                  </tr>`;
          });
        } else {
          row = `<tr><td colspan="8" style="text-align:center; padding:30px; color:#999;">
                      <i class="fas fa-inbox" style="font-size:40px; display:block; margin-bottom:10px;"></i>
                      No se encontraron resultados
                  </td></tr>`;
        }

        // ===== INSERTAR FILAS =====
        $("#tablaPrincipal tbody").empty().append(row);

        // ===== GENERAR PAGINADOR =====
        const total = data.total || 0;
        const totalPaginas = data.total_paginas || 1;
        const paginaActual = data.pagina || pagina;

        let pag = `<div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; padding:10px 0; gap:10px;">
                  <div style="font-size:14px; color:#666;">
                      <i class="fas fa-database" style="margin-right:5px;"></i>
                      <strong>${total}</strong> registros encontrados
                  </div>
                  <div style="display:flex; justify-content:center; align-items:center; gap:5px; flex-wrap:wrap;">`;

        if (totalPaginas > 1) {
          pag += `<button onclick="consultarDatos(${paginaActual - 1})" 
                              style="padding:5px 12px; border:1px solid #ddd; background:white; cursor:pointer; border-radius:4px; ${paginaActual <= 1 ? "opacity:0.5; cursor:not-allowed;" : ""}" 
                              ${paginaActual <= 1 ? "disabled" : ""}>
                              <i class="fas fa-chevron-left"></i>
                          </button>`;

          const maxPaginasMostrar = 5;
          let inicioPaginas = Math.max(1, paginaActual - Math.floor(maxPaginasMostrar / 2));
          let finPaginas = Math.min(totalPaginas, inicioPaginas + maxPaginasMostrar - 1);

          if (finPaginas - inicioPaginas < maxPaginasMostrar - 1) {
            inicioPaginas = Math.max(1, finPaginas - maxPaginasMostrar + 1);
          }

          if (inicioPaginas > 1) {
            pag += `<span style="padding:5px 10px; color:#999;">…</span>`;
          }

          for (let i = inicioPaginas; i <= finPaginas; i++) {
            const isActive = i === paginaActual;
            pag += `<button onclick="consultarDatos(${i})" 
                                      style="padding:5px 12px; border:1px solid #ddd; background:${isActive ? "#1e3c72" : "white"}; color:${isActive ? "white" : "#333"}; cursor:pointer; border-radius:4px; font-weight:${isActive ? "bold" : "normal"};">
                                      ${i}
                                  </button>`;
          }

          if (finPaginas < totalPaginas) {
            pag += `<span style="padding:5px 10px; color:#999;">…</span>`;
          }

          pag += `<button onclick="consultarDatos(${paginaActual + 1})" 
                              style="padding:5px 12px; border:1px solid #ddd; background:white; cursor:pointer; border-radius:4px; ${paginaActual >= totalPaginas ? "opacity:0.5; cursor:not-allowed;" : ""}" 
                              ${paginaActual >= totalPaginas ? "disabled" : ""}>
                              <i class="fas fa-chevron-right"></i>
                          </button>`;

          pag += `<span style="margin-left:10px; font-size:13px; color:#666;">Página ${paginaActual} de ${totalPaginas}</span>`;
        } else {
          pag += `<span style="font-size:13px; color:#999;">Página 1 de 1</span>`;
        }

        pag += `</div></div>`;
        $("#paginador").html(pag);
      })
      .catch((error) => {
        console.error("❌ Error:", error);
        $("#tablaPrincipal tbody").html(
          `<tr><td colspan="8" style="text-align:center; color:red; padding:40px;">
                          <i class="fas fa-exclamation-triangle" style="font-size:24px; display:block; margin-bottom:10px;"></i>
                          Error al cargar datos: ${error.message}
                      </td></tr>`
        );
        $("#paginador").empty();
      });
  }*/

  function consultarDatos(pagina) {
    pagina = pagina || 1;

    const costosSearch = $("#costosSearch").val() || "";
    const codigoBusqueda = $("#codigoBusqueda").val() || "";
    const descripcionSearch = $("#descripcionSearch").val() || "";

    $("#tablaPrincipal tbody").html(
      '<tr><td colspan="8" style="text-align:center; padding:40px;"><i class="fas fa-spinner fa-spin" style="font-size:24px;"></i><p>Cargando...</p></td></tr>'
    );

    let formData = new FormData();
    formData.append("costos", costosSearch);
    formData.append("codigo", codigoBusqueda);
    formData.append("descripcion", descripcionSearch);
    formData.append("page", pagina);

    fetch(RUTA + "minimos/consultaProductosPaginado", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        let row = "";
        let item = (pagina - 1) * 5 + 1;

        if (data.success && data.data && data.data.length > 0) {
          
          // ===== 🔥 AGRUPACIÓN POR CÓDIGO DE PRODUCTO 🔥 =====
          const agrupado = data.data.reduce((acc, el) => {
              const key = `${el.idcostos}-${el.id_cprod}`; 
              if (!acc[key]) {
                  acc[key] = {
                      codigo: el.ccodprod,
                      descripcion: el.cdesprod,
                      unidad: el.unidad,
                      proyecto: el.proyecto,
                      registros: []
                  };
              }
              acc[key].registros.push(el);
              return acc;
          }, {});

          // Recorremos los productos agrupados
          for (const [key, prod] of Object.entries(agrupado)) {
              
              prod.registros.forEach((reg, index) => {
                  
                  const saldoReal = parseFloat(reg.saldo || 0);
                  const stockMinimo = parseFloat(reg.nminimo || 0);
                  const estado = reg.estado_stock || "NORMAL";
                  const ffecha = reg.ffecha || "-";
                  const fmt = (n) => Number(n).toLocaleString("es-PE", { minimumFractionDigits: 2, maximumFractionDigits: 2 });

                  let colorEstado, textoColor;
                  switch (estado) {
                      case "CRÍTICO": colorEstado = "#DC3545"; textoColor = "white"; break;
                      case "MÍNIMO":  colorEstado = "#FFC107"; textoColor = "#856404"; break;
                      case "EXCESO":  colorEstado = "#17A2B8"; textoColor = "white"; break;
                      case "SIN MÍNIMO": colorEstado = "#6C757D"; textoColor = "white"; break;
                      default:        colorEstado = "#C6F6D5"; textoColor = "#2D3748"; break;
                  }

                  // Si es el primer registro del grupo, mostramos código, descripción y proyecto
                  row += `<tr class="pointer" 
                                data-idproducto="${reg.id_cprod}"
                                data-costos="${reg.idcostos || ''}">
                            <td class="textoDerecha">${item++}</td>
                            
                            <td class="textoCentro" style="font-weight: ${index === 0 ? 'bold' : 'normal'}; color: ${index === 0 ? '#1e3c72' : 'transparent'};">
                                ${index === 0 ? prod.codigo : "---"}
                            </td>
                            <td class="pl20px" style="font-weight: ${index === 0 ? 'bold' : 'normal'};">
                                ${index === 0 ? prod.descripcion : ""}
                            </td>
                            <td class="textoCentro">${index === 0 ? prod.unidad : ""}</td>
                            
                            <td class="textoCentro">${ffecha}</td>
                            <td class="textoDerecha">${stockMinimo > 0 ? fmt(stockMinimo) : "-"}</td>
                            <td class="textoDerecha"><strong>${fmt(saldoReal)}</strong></td>
                            <td class="textoCentro">
                                <span style="background:${colorEstado}; padding:4px 10px; border-radius:12px; font-size:11px; color:${textoColor}; font-weight:bold; display:inline-block; white-space:nowrap;">
                                    ${estado}
                                </span>
                            </td>
                        </tr>`;
              });
              
              // Línea separadora entre productos
              row += `<tr><td colspan="8" style="border-bottom: 2px solid #cbd5e1; padding: 0;"></td></tr>`;
          }
          // ===== FIN DE LA AGRUPACIÓN =====
          
        } else {
          row = `<tr><td colspan="8" style="text-align:center; padding:30px; color:#999;">
                      <i class="fas fa-inbox" style="font-size:40px; display:block; margin-bottom:10px;"></i>
                      No se encontraron resultados
                  </td></tr>`;
        }

        $("#tablaPrincipal tbody").empty().append(row);

        // Paginador
        const total = data.total || 0;
        const totalPaginas = data.total_paginas || 1;
        const paginaActual = data.pagina || pagina;

        let pag = `<div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; padding:10px 0; gap:10px;">
                  <div style="font-size:14px; color:#666;">
                      <i class="fas fa-database" style="margin-right:5px;"></i>
                      <strong>${total}</strong> registros encontrados
                  </div>
                  <div style="display:flex; justify-content:center; align-items:center; gap:5px; flex-wrap:wrap;">`;

        if (totalPaginas > 1) {
          pag += `<button onclick="consultarDatos(${paginaActual - 1})" 
                              style="padding:5px 12px; border:1px solid #ddd; background:white; cursor:pointer; border-radius:4px; ${paginaActual <= 1 ? "opacity:0.5; cursor:not-allowed;" : ""}" 
                              ${paginaActual <= 1 ? "disabled" : ""}>
                              <i class="fas fa-chevron-left"></i>
                          </button>`;

          const maxPaginasMostrar = 5;
          let inicioPaginas = Math.max(1, paginaActual - Math.floor(maxPaginasMostrar / 2));
          let finPaginas = Math.min(totalPaginas, inicioPaginas + maxPaginasMostrar - 1);

          if (finPaginas - inicioPaginas < maxPaginasMostrar - 1) {
            inicioPaginas = Math.max(1, finPaginas - maxPaginasMostrar + 1);
          }

          if (inicioPaginas > 1) pag += `<span style="padding:5px 10px; color:#999;">…</span>`;

          for (let i = inicioPaginas; i <= finPaginas; i++) {
            const isActive = i === paginaActual;
            pag += `<button onclick="consultarDatos(${i})" 
                                      style="padding:5px 12px; border:1px solid #ddd; background:${isActive ? "#1e3c72" : "white"}; color:${isActive ? "white" : "#333"}; cursor:pointer; border-radius:4px; font-weight:${isActive ? "bold" : "normal"};">
                                      ${i}
                                  </button>`;
          }

          if (finPaginas < totalPaginas) pag += `<span style="padding:5px 10px; color:#999;">…</span>`;

          pag += `<button onclick="consultarDatos(${paginaActual + 1})" 
                              style="padding:5px 12px; border:1px solid #ddd; background:white; cursor:pointer; border-radius:4px; ${paginaActual >= totalPaginas ? "opacity:0.5; cursor:not-allowed;" : ""}" 
                              ${paginaActual >= totalPaginas ? "disabled" : ""}>
                              <i class="fas fa-chevron-right"></i>
                          </button>`;

          pag += `<span style="margin-left:10px; font-size:13px; color:#666;">Página ${paginaActual} de ${totalPaginas}</span>`;
        } else {
          pag += `<span style="font-size:13px; color:#999;">Página 1 de 1</span>`;
        }

        pag += `</div></div>`;
        $("#paginador").html(pag);
      })
      .catch((error) => {
        console.error("❌ Error:", error);
        $("#tablaPrincipal tbody").html(
          `<tr><td colspan="8" style="text-align:center; color:red; padding:40px;">
              <i class="fas fa-exclamation-triangle" style="font-size:24px; display:block; margin-bottom:10px;"></i>
              Error al cargar datos: ${error.message}
          </td></tr>`
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

  // REGISTRAR CODIGOS NUEVOS
  $("#newRegister").click(async function (e) {
    e.preventDefault();

    // Verificar permisos antes de abrir el modal
    const idUser = $("#id_user").val() || 1;
    const permisos = await verificarPermisos(idUser, 65);

    origen = "boton";

    if (!permisos || !permisos.datos[0].agrega) {
      mostrarMensaje(
        "⚠️ No tienes permisos para registrar mínimos",
        "mensaje_error",
      );
      return; // No abre el modal
    }

    if ($("#costosSearch").val() == "-1") {
      mostrarMensaje("⚠️ Seleccione un centro de costos", "mensaje_error");
      return; // No abre el modal
    }

    // Si tiene permiso, continúa con el registro
    costos = $("#costosSearch").val();

    $("#cant_personal").val("");
    $("#porcentaje_minimo").val("");
    $("#total_minimo").val("");
    $("#observaciones_dialogo").val("");
    $("#fecha").val(new Date().toISOString().split("T")[0]);

    $("#dialogo_registro").fadeIn();

    return false;
  });

  // ===== DOBLE CLIC EN FILA PARA REGISTRAR MÍNIMO =====
  $("#tablaPrincipal tbody").on("dblclick", "tr.pointer", async function (e) {
    e.preventDefault();

    // Verificar permisos antes de abrir el modal
    const idUser = $("#id_user").val() || 1;
    const permisos = await verificarPermisos(idUser, 65);

    origen = "tabla";

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

          if (typeof ExcelJS === "undefined") {
              mostrarMensaje("⏳ Cargando librería ExcelJS, espere un momento...", "mensaje_info");
              setTimeout(function () { $("#excelFile").click(); }, 2000);
              return;
          }

          const workbook = new ExcelJS.Workbook();
          workbook.creator = "Sistema de Inventario";
          workbook.created = new Date();

          const worksheet = workbook.addWorksheet("Reporte de Stock Mínimos", {
              pageSetup: { paperSize: 9, orientation: "landscape" },
          });

          // ===== ESTILOS =====
          const titleStyle = { font: { bold: true, size: 16, color: { argb: "FFFFFFFF" }, name: "Arial" }, fill: { type: "pattern", pattern: "solid", fgColor: { argb: "FF1E3C72" } }, alignment: { horizontal: "center", vertical: "middle" } };
          const headerStyle = { font: { bold: true, color: { argb: "FFFFFFFF" }, size: 11, name: "Arial" }, fill: { type: "pattern", pattern: "solid", fgColor: { argb: "FF1E3C72" } }, alignment: { horizontal: "center", vertical: "middle", wrapText: true }, border: { top: { style: "thin" }, bottom: { style: "thin" }, left: { style: "thin" }, right: { style: "thin" } } };
          
          const criticalStyle = { fill: { type: "pattern", pattern: "solid", fgColor: { argb: "FFDC3545" } }, font: { bold: true, color: { argb: "FFFFFFFF" } }, alignment: { horizontal: "center", vertical: "middle" } };
          const warningStyle  = { fill: { type: "pattern", pattern: "solid", fgColor: { argb: "FFFFC107" } }, font: { bold: true, color: { argb: "FF856404" } }, alignment: { horizontal: "center", vertical: "middle" } };
          const normalStyle   = { fill: { type: "pattern", pattern: "solid", fgColor: { argb: "FFC6F6D5" } }, font: { bold: true, color: { argb: "FF2D3748" } }, alignment: { horizontal: "center", vertical: "middle" } };
          const excessStyle   = { fill: { type: "pattern", pattern: "solid", fgColor: { argb: "FF17A2B8" } }, font: { bold: true, color: { argb: "FFFFFFFF" } }, alignment: { horizontal: "center", vertical: "middle" } };
          const noMinStyle    = { fill: { type: "pattern", pattern: "solid", fgColor: { argb: "FF6C757D" } }, font: { bold: true, color: { argb: "FFFFFFFF" } }, alignment: { horizontal: "center", vertical: "middle" } };

          // ===== TÍTULO =====
          worksheet.mergeCells("A1:J1");
          worksheet.getCell("A1").value = "REPORTE DE STOCK MÍNIMOS";
          worksheet.getCell("A1").style = titleStyle;
          worksheet.getRow(1).height = 35;

          worksheet.mergeCells("A2:J2");
          worksheet.getCell("A2").value = `Fecha de generación: ${new Date().toLocaleString()} | Total: ${data.total} registros`;
          worksheet.getCell("A2").style = { font: { bold: true, size: 11, color: { argb: "FF333333" }, name: "Arial" }, fill: { type: "pattern", pattern: "solid", fgColor: { argb: "FFF0F0F0" } } };
          worksheet.getRow(2).height = 22;

          // ===== ENCABEZADOS (Agregamos Proyecto y Saldo) =====
          const headers = ["Item", "Proyecto", "Código", "Descripción", "Unidad", "Ingresos", "Salidas", "Saldo Real", "Mínimo", "Estado"];

          worksheet.columns = [
              { width: 8 },  // Item
              { width: 20 }, // Proyecto
              { width: 18 }, // Código
              { width: 50 }, // Descripción
              { width: 10 }, // Unidad
              { width: 15 }, // Ingresos
              { width: 15 }, // Salidas
              { width: 15 }, // Saldo
              { width: 15 }, // Mínimo
              { width: 18 }  // Estado
          ];

          const headerRow = worksheet.getRow(4);
          headerRow.values = headers;
          headerRow.eachCell((cell) => { cell.style = headerStyle; });
          headerRow.height = 40;

          // ===== DATOS (TODOS) =====
          let totalIngresos = 0;
          let totalSalidas = 0;
          let rowIndex = 5;
          let item = 1;

          data.data.forEach((element) => {
              // Al venir limpios del backend, usamos Number directo
              const ingresos = Number(element.total_ingresos || 0);
              const salidas = Number(element.total_salidas || 0);
              const saldo = Number(element.saldo || 0);
              const stockMinimo = Number(element.nminimo || 0);

              totalIngresos += ingresos;
              totalSalidas += salidas;

              const row = worksheet.getRow(rowIndex);
              row.getCell(1).value = item++;
              row.getCell(2).value = element.proyecto || "";
              row.getCell(3).value = element.ccodprod || "";
              row.getCell(4).value = element.cdesprod || "";
              row.getCell(5).value = element.unidad || "";
              row.getCell(6).value = ingresos;
              row.getCell(7).value = salidas;
              row.getCell(8).value = saldo;
              row.getCell(9).value = stockMinimo;
              row.getCell(10).value = element.estado_stock || "NORMAL";

              // Formatear números a 2 decimales en Excel
              [6, 7, 8, 9].forEach(col => row.getCell(col).numFmt = '#,##0.00');
              [6, 7, 8, 9].forEach(col => row.getCell(col).alignment = { horizontal: 'right' });

              // Aplicar estilo según estado
              const estadoCell = row.getCell(10);
              const estado = element.estado_stock || "NORMAL";
              if (estado === "CRÍTICO") estadoCell.style = criticalStyle;
              else if (estado === "MÍNIMO") estadoCell.style = warningStyle;
              else if (estado === "EXCESO") estadoCell.style = excessStyle;
              else if (estado === "SIN MÍNIMO") estadoCell.style = noMinStyle;
              else estadoCell.style = normalStyle;

              // Bordes
              row.eachCell((cell) => {
                  cell.border = { top: { style: "thin" }, bottom: { style: "thin" }, left: { style: "thin" }, right: { style: "thin" } };
              });

              row.height = 20;
              rowIndex++;
          });

          // ===== FILA DE TOTALES =====
          const totalRow = worksheet.getRow(rowIndex);
          totalRow.getCell(4).value = "TOTALES:";
          totalRow.getCell(6).value = totalIngresos;
          totalRow.getCell(7).value = totalSalidas;
          
          totalRow.getCell(6).numFmt = '#,##0.00';
          totalRow.getCell(7).numFmt = '#,##0.00';
          
          totalRow.getCell(4).style = { font: { bold: true }, alignment: { horizontal: "right" } };
          totalRow.getCell(6).style = { font: { bold: true, color: { argb: "FF28A745" } }, alignment: { horizontal: "right" } };
          totalRow.getCell(7).style = { font: { bold: true, color: { argb: "FFDC3545" } }, alignment: { horizontal: "right" } };
          
          totalRow.eachCell((cell) => {
              cell.border = { top: { style: "medium" }, bottom: { style: "medium" }, left: { style: "thin" }, right: { style: "thin" } };
          });
          totalRow.height = 25;

          // ===== RESUMEN POR ESTADO =====
          rowIndex += 2;
          const resumenRow = worksheet.getRow(rowIndex);
          resumenRow.getCell(1).value = "RESUMEN POR ESTADO:";
          resumenRow.getCell(1).style = { font: { bold: true, size: 12 }, fill: { type: "pattern", pattern: "solid", fgColor: { argb: "FFF0F0F0" } } };

          rowIndex++;
          const estados = {};
          data.data.forEach((element) => {
              const estado = element.estado_stock || "NORMAL";
              estados[estado] = (estados[estado] || 0) + 1;
          });

          const colores = {
              CRÍTICO: "FFDC3545",
              MÍNIMO: "FFFFC107",
              NORMAL: "FFC6F6D5",
              EXCESO: "FF17A2B8",
              "SIN MÍNIMO": "FF6C757D"
          };

          for (const [estado, cantidad] of Object.entries(estados)) {
              const row = worksheet.getRow(rowIndex);
              row.getCell(1).value = estado;
              row.getCell(2).value = cantidad;

              const cell = row.getCell(1);
              if (colores[estado]) {
                  cell.style = {
                      fill: { type: "pattern", pattern: "solid", fgColor: { argb: colores[estado] } },
                      font: { bold: true, color: { argb: estado === "NORMAL" || estado === "MÍNIMO" ? "FF2D3748" : "FFFFFFFF" } },
                      alignment: { horizontal: "center" },
                  };
              }
              row.height = 18;
              rowIndex++;
          }

          // ===== CONGELAR PANELES Y GENERAR =====
          worksheet.views = [{ state: "frozen", xSplit: 0, ySplit: 4 }];

          const buffer = await workbook.xlsx.writeBuffer();
          const blob = new Blob([buffer], { type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" });
          const link = document.createElement("a");
          const url = URL.createObjectURL(blob);
          const fecha = new Date().toISOString().slice(0, 10);

          link.href = url;
          link.download = `Reporte_StockMinimos_${fecha}.xlsx`;
          document.body.appendChild(link);
          link.click();
          document.body.removeChild(link);
          URL.revokeObjectURL(url);

          mostrarMensaje(`📊 Reporte exportado correctamente (${data.total} registros)`, "mensaje_correcto");

      } catch (error) {
          console.error("Error al exportar:", error);
          mostrarMensaje("💣 Error al exportar: " + error.message, "mensaje_error");
      }
  });

  // CONSULTAR EL CODIGO SI ES NUEVO
  $("#codigoSearch").keypress(function (e) {
    if (e.which == 13) {
      if (origen == "boton") {
        if ($(this).val() === "") {
          mostrarMensaje("🚩 Ingrese un codigo válido", "mensaje_error");
        } else {
          let formData = new FormData();
          formData.append("codigo", $(this).val());

          fetch(RUTA + "minimos/buscaCodigo", {
            method: "POST",
            body: formData,
          })
            .then((response) => response.json())
            .then((data) => {
              producto = data.datos[0]["id_cprod"];
              $("#descripSearch").val(data.datos[0]["cdesprod"]);
              $("#unidad").val(data.datos[0]["cabrevia"]);
            });
        }
      }
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
