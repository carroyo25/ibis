$(() => {
  $("#esperar").css({ display: "none", opacity: "0" });

  const ventana = document.getElementById("registroti");
  const header = document.getElementById("ventanaHeader");

  if (!ventana || !header) return;

  let isDragging = false;
  let startX, startY;
  let initialLeft, initialTop;

  header.addEventListener("mousedown", startDrag);
  document.addEventListener("mousemove", drag);
  document.addEventListener("mouseup", stopDrag);

  // Posición inicial centrada
  ventana.style.position = "absolute";
  ventana.style.left = "50%";
  ventana.style.top = "50%";
  ventana.style.transform = "translate(-50%, -50%)";
  ventana.style.margin = "0";

  let id, cc, docidetuser, serie, fila;

  const tabla_principal = document.getElementById("tablaPrincipal");

  tabla_principal.addEventListener("click", (e) => {
    e.preventDefault();

    if (e.target.matches(".click_link_date *")) {
      serie = e.target.parentNode.getAttribute("href");
      docidetuser = e.target.parentNode.dataset.documento;

      $("#cambio_fecha").fadeIn();
    } else if (e.target.matches(".click_link_serie *")) {
      serie = e.target.parentNode.getAttribute("href");
      docidetuser = e.target.parentNode.dataset.documento;

      fila = e.target.closest("tr").getAttribute("data-id");

      $("#serie_nueva").val(serie);

      $("#cambio_serie").fadeIn();
    } else if (e.target.matches(".click_tr *")) {
      const tr = e.target.closest(".click_tr");

      $("#serie").val(e.target.closest(".click_tr").dataset.serie);
      $("#idmmtto").val(e.target.closest(".click_tr").dataset.id);
      $("#descripcion").val(e.target.closest(".click_tr").cells[1].innerHTML);
      $("#fecha_sugerida").val(
        e.target.closest(".click_tr").cells[5].innerHTML,
      );
      $("#usuario").val(e.target.closest(".click_tr").cells[2].innerHTML);
      $("#sendNotify").prop("href", e.target.closest(".click_tr").dataset.id);

      $("#correo_usuario").val(
        tr.dataset.correo === "null" ? "" : tr.dataset.correo,
      );
      $("#procesador").val(
        tr.dataset.procesador === "null" ? "" : tr.dataset.procesador,
      );
      $("#ram").val(tr.dataset.ram === "null" ? "" : tr.dataset.ram);
      $("#hdd").val(tr.dataset.hdd === "null" ? "" : tr.dataset.hdd);
      $("#otros").val(tr.dataset.otros === "null" ? "" : tr.dataset.otros);

      idprod = $(this).data("idprod");
      cc = $(this).data("costos");
      docidetuser = $(this).data("documento");

      $("#tabla_detalles_mttos tbody").empty();

      id = $(this).data("id");

      let formData = new FormData();
      formData.append(
        "serie",
        e.target.closest(".click_tr").cells[3].innerHTML,
      );
      formData.append(
        "documento",
        e.target.closest(".click_tr").dataset.documento,
      );

      fetch(RUTA + "timmtto/anteriores", {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json())
        .then((data) => {
          data.mmttos.forEach((element) => {
            let row = `<tr>
                                    <td class="textoCentro">${element.frelmtto}</td>
                                    <td class="pl20px">${element.cobserva}</td>
                                    <td class="pl20px">${element.tecnico}</td>
                                    <td class="textoCentro"><a href="#" class="photo_details"><i class="fas fa-images"></i></a></td>
                                    <td class="textoCentro"><a href="#" class="photo_details"><i class="fas fa-trash"></i></a></td></td>
                                </tr>`;

            $("#tabla_detalles_mttos tbody").append(row);
          });

          $("#idlastmmtto").val(data.lastmmttos.id);
          $("#fecha_sugerida").val(data.lastmmttos.fecha_proxima);

          $("#dialogo_registro").fadeIn();
        });

      return false;
    }

    return false;
  });

  $("#btnAceptarDialogo").click(function (e) {
    e.preventDefault();

    try {
      if ($("#fecha_mmto").val() == "")
        throw new Error("No ingreso fecha del mantenimiento");

      let formData = new FormData();
      formData.append("id", $("#idmmtto").val());
      formData.append("fmmto", $("#fecha_mmto").val());
      formData.append("correo", $("#correo_usuario").val());
      formData.append("observa", $("#observaciones_dialogo").val());
      formData.append("user", $("#id_user").val());
      formData.append("tecnico", $("#name_user").val());
      formData.append("correo_tecnico", $("#mail_user").val());
      formData.append("asignado", $("#usuario").val());
      formData.append("tipo_mmtto", $("#tipo_mmtto").val());

      formData.append("procesador", $("#procesador").val()); //
      formData.append("ram", $("#ram").val()); //
      formData.append("hdd", $("#hdd").val()); //
      formData.append("otros", $("#otros").val()); //
      formData.append("estado", $("#estado_equipo").val()); //

      formData.append("codigo_costos", cc);
      formData.append("codigo_producto", null);
      formData.append("serie_producto", $("#serie").val());
      formData.append("documento_usuario", docidetuser);

      formData.append("lastMmtto", $("#idlastmmtto").val());

      fetch(RUTA + "timmtto/mantenimiento", {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.respuesta) {
            mostrarMensaje("Mantenimiento registrado", "mensaje_correcto");
            $("#dialogo_registro").fadeOut();
          }
        });
    } catch (error) {
      mostrarMensaje(error, "mensaje_error");
    }

    return false;
  });

  $("#btnCancelarDialogo").click(function (e) {
    e.preventDefault();

    $("#dialogo_registro").fadeOut();

    return false;
  });

  $("#sendNotify").click(function (e) {
    e.preventDefault();

    $("#esperar").css({ display: "block", opacity: "1" });

    try {
      let formData = new FormData();
      formData.append("id", id);
      formData.append("fmmto", $("#fecha_mmto").val());
      formData.append("correo", $("#correo_usuario").val());
      formData.append("tecnico", $("#name_user").val());
      formData.append("correo_tecnico", $("#mail_user").val());
      formData.append("serie_producto", $("#serie").val());
      formData.append("usuario", $("#usuario").val());
      formData.append("fecha", $("#fecha_sugerida").val());

      fetch(RUTA + "timmtto/notificar", {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.respuesta) {
            $("#esperar").css("opacity", "0").fadeOut();
            mostrarMensaje("Correo Enviado", "mensaje_correcto");
            $("#dialogo_registro").fadeOut();
          }
        });
    } catch (error) {
      mostrarMensaje(error, "mensaje_error");
    }

    return false;
  });

  $("#btnAceptarGrabar").click(function (e) {
    e.preventDefault();
    try {
      if ($("#fecha_nueva").val() === "") throw new Error("Escoja una fecha");

      let formData = new FormData();
      formData.append("fecha", $("#fecha_nueva").val());
      formData.append("serie", serie);
      formData.append("documento", docidetuser);

      fetch(RUTA + "timmtto/cambiofechas", {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json())
        .then((data) => {
          console.log(data);
        });
    } catch (error) {
      mostrarMensaje(error, "mensaje_error");
    }

    return false;
  });

  $("#btnCancelarGrabar, #btnCancelarSerie").click(function (e) {
    e.preventDefault();

    $(this).closest(".modal").fadeOut();

    return false;
  });

  $("#btnAceptarSerie").click(function (e) {
    e.preventDefault();

    let formData = new FormData();

    formData.append("serie_nueva", $("#serie_nueva").val());
    formData.append("serie_anterior", serie);
    formData.append("documento", docidetuser);

    fetch(RUTA + "timmtto/cambioSeries", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.respuesta) {
          //***con esto actualiza la fila sin necesidad de recargar la pagina */

          $("#" + fila)
            .find("td")
            .eq("3")
            .text($("#serie_nueva").val());

          mostrarMensaje("👍 Serie actualizada", "mensaje_correcto");
          $(this).closest(".modal").fadeOut();
        } else {
          mostrarMensaje("😥 Error al actualixar", "mensaje_error");
        }
      });

    return false;
  });

  $("#btnConsulta").click(function (e) {
    e.preventDefault();

    let formData = new FormData();

    const serie = document.getElementById("serieBusqueda");
    const costos = document.getElementById("costosSearch");
    const nombre = document.getElementById("usuarioBusqueda");

    formData.append("serie", serie.value);
    formData.append("costos", costos.value);
    formData.append("nombre", nombre.value);

    const grupos = {};

    try {
      fetch(RUTA + "timmtto/listaMmttos", {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json())
        .then((data) => {
          datosOriginales = agruparPorSerie(data.datos);
          datosFiltrados = [...datosOriginales];
          renderSeries(datosOriginales);
        });
    } catch (error) {}

    return false;
  });

  $("#excelFile").click(function (e) {
    e.preventDefault();

    $("#esperarCargo").css("opacity", "1").fadeIn();

    let formdata = new FormData();

    formdata.append("detalles", JSON.stringify(detalles()));

    fetch(RUTA + "timmtto/archivoExcel", {
      method: "POST",
      body: formdata,
    })
      .then((response) => {
        return response.json();
      })
      .then((json) => {
        $("#esperarCargo").css("opacity", "0").fadeOut();
        window.location.href = json.documento;
      })
      .catch((err) => {
        console.log(err);
      });

    return false;
  });

  function startDrag(e) {
    // No arrastrar si se hizo clic en el botón de notificar
    if (e.target.closest("#sendNotify")) return;

    isDragging = true;

    // Obtener posición actual (sin transform)
    const computedStyle = window.getComputedStyle(ventana);
    const leftValue = computedStyle.left;
    const topValue = computedStyle.top;

    // Si está centrado con transform, calcular posición real
    if (ventana.style.transform === "translate(-50%, -50%)") {
      const rect = ventana.getBoundingClientRect();
      initialLeft = rect.left;
      initialTop = rect.top;
      ventana.style.left = initialLeft + "px";
      ventana.style.top = initialTop + "px";
      ventana.style.transform = "none";
    } else {
      initialLeft = parseFloat(leftValue);
      initialTop = parseFloat(topValue);
    }

    startX = e.clientX;
    startY = e.clientY;

    ventana.classList.add("dragging");
    e.preventDefault();
  }

  function drag(e) {
    if (!isDragging) return;

    const deltaX = e.clientX - startX;
    const deltaY = e.clientY - startY;

    let newLeft = initialLeft + deltaX;
    let newTop = initialTop + deltaY;

    // Limitar dentro de la ventana (opcional)
    const maxX = window.innerWidth - ventana.offsetWidth;
    const maxY = window.innerHeight - ventana.offsetHeight;

    newLeft = Math.max(0, Math.min(newLeft, maxX));
    newTop = Math.max(0, Math.min(newTop, maxY));

    ventana.style.left = newLeft + "px";
    ventana.style.top = newTop + "px";
  }

  function stopDrag() {
    isDragging = false;
    ventana.classList.remove("dragging");
  }
});

detalles = () => {
  let DATA = [];

  let TABLA = $("#tablaPrincipal tbody >tr");

  TABLA.each(function () {
    item = {};
    ((item["item"] = $(this).find("td").eq(0).text()),
      (item["descripcion"] = $(this).find("td").eq(1).text()),
      (item["usuario"] = $(this).find("td").eq(2).text()),
      (item["serie"] = $(this).find("td").eq(3).text()),
      (item["entrega"] = $(this).find("td").eq(4).text()),
      (item["costos"] = $(this).find("td").eq(5).text()),
      (item["mmtto1"] = $(this).find("td").eq(6).text()),
      (item["estado1"] = $(this).find("td").eq(7).text()),
      (item["mmtto2"] = $(this).find("td").eq(8).text()),
      (item["estado2"] = $(this).find("td").eq(9).text()),
      (item["mmtto3"] = $(this).find("td").eq(10).text()),
      (item["estado3"] = $(this).find("td").eq(11).text()),
      (item["mmtto3"] = $(this).find("td").eq(12).text()),
      (item["estado4"] = $(this).find("td").eq(13).text()));

    DATA.push(item);
  });

  return DATA;
};

 function formatearFecha(fecha) {
            if (!fecha) return '-';
            const partes = fecha.split('-');
            return `${partes[2]}/${partes[1]}/${partes[0]}`;
        }

        function formatearDias(dias, flgestado) {
            if (flgestado === 1) {
                return `<span class="dias-positivo">✅ Realizado</span>`;
            } else if (dias < 0) {
                return `<span class="dias-negativo">📉 ${Math.abs(dias)} días (vencido)</span>`;
            } else if (dias === 0) {
                return `<span class="dias-positivo">📅 Hoy</span>`;
            } else {
                return `<span class="dias-positivo">📈 +${dias} días</span>`;
            }
        }

        function getEstadoBadge(dias, flgestado) {
            if (flgestado === 1) {
                return '<span class="badge-status badge-completado">✅ COMPLETADO</span>';
            } else if (dias < 0) {
                return '<span class="badge-status badge-vencido">⚠️ VENCIDO</span>';
            } else if (dias === 0) {
                return '<span class="badge-status badge-pendiente">📅 HOY</span>';
            } else if (dias <= 30) {
                return '<span class="badge-status badge-pendiente">🔄 PRÓXIMO</span>';
            } else {
                return '<span class="badge-status badge-normal">📆 NORMAL</span>';
            }
        }

        function agruparPorSerie(data) {
            const grupos = {};
            
            data.forEach(item => {
                const serie = item.cserie;
                if (!grupos[serie]) {
                    grupos[serie] = {
                        cserie: serie,
                        documento: item.nrodoc,
                        fentrega: item.fentrega,
                        nombre: item.nombre,
                        estado: item.ESTADO,
                        flgactivo: item.flgactivo,
                        cdesprod: item.cdesprod,
                        mantenimientos: []
                    };
                }
                grupos[serie].mantenimientos.push({
                    fmtto: item.fmtto,
                    cobserva: item.cobserva,
                    idreg: item.idreg,
                    dias_diferencia: item.dias_diferencia,
                    idprod: item.idprod,
                    flgestado: item.flgestado,
                    ntipo: item.ntipo
                });
            });
            
            for (let serie in grupos) {
                grupos[serie].mantenimientos.sort((a, b) => a.dias_diferencia - b.dias_diferencia);
            }
            
            return Object.values(grupos);
        }

        function getClaseFila(mantto) {
            if (mantto.flgestado === 1) return 'completado-row';
            if (mantto.dias_diferencia < 0) return 'vencido-row';
            return 'pendiente-row';
        }

        function getEstadoGeneralSerie(mantenimientos) {
            const tieneVencido = mantenimientos.some(m => m.flgestado === 0 && m.dias_diferencia < 0);
            const tienePendiente = mantenimientos.some(m => m.flgestado === 0 && m.dias_diferencia >= 0);
            
            if (tieneVencido) return 'badge-vencido';
            if (tienePendiente) return 'badge-pendiente';
            return 'badge-normal';
        }

        function getTextoEstadoGeneral(mantenimientos) {
            const tieneVencido = mantenimientos.some(m => m.flgestado === 0 && m.dias_diferencia < 0);
            const tienePendiente = mantenimientos.some(m => m.flgestado === 0 && m.dias_diferencia >= 0);
            
            if (tieneVencido) return '⚠️ Con vencidos';
            if (tienePendiente) return '⏳ Pendientes';
            return '✅ Todos completados';
        }

        function limpiarId(str) {
            return str.replace(/[^a-zA-Z0-9]/g, '_');
        }

        function renderSeries(series) {
            const tbody = document.getElementById("tableBody");
            if (!tbody) return;

            tbody.innerHTML = "";

            let totalMant = 0;
            let totalVencidos = 0;
            let totalPendientes = 0;
            let totalCompletados = 0;

            series.forEach((serie) => {
                totalMant += serie.mantenimientos.length;
                totalVencidos += serie.mantenimientos.filter(
                    (m) => m.flgestado === 0 && m.dias_diferencia < 0,
                ).length;
                totalPendientes += serie.mantenimientos.filter(
                    (m) => m.flgestado === 0 && m.dias_diferencia >= 0,
                ).length;
                totalCompletados += serie.mantenimientos.filter(
                    (m) => m.flgestado === 1,
                ).length;

                const estadoClase = getEstadoGeneralSerie(serie.mantenimientos);
                const estadoTexto = getTextoEstadoGeneral(serie.mantenimientos);
                const serieId = limpiarId(serie.cserie);

                const row = tbody.insertRow();
                row.classList.add('serie-principal');
                row.setAttribute('data-serie-id', serieId);
                row.setAttribute('onclick', `toggleSerie('${serieId}')`);
                row.innerHTML = `
                    <td class="serie-td">
                        <span class="toggle-icon" id="icon-${serieId}">▶</span>
                        <span class="serie-code">🔧 ${serie.cserie}</span>
                    </td>
                    <td class="serie-nombre">${serie.nombre}</td>
                    <td class="serie-nombre">${serie.documento}</td>
                    <td class="serie-producto">${serie.cdesprod}</td>
                    <td>${formatearFecha(serie.fentrega)}</td>
                    <td><span class="badge-status ${estadoClase}">${estadoTexto}</span></td>
                    <td><a href="#" class="textoCentro actions"><i class="fas fa-cogs"></i></a></td>
                `;

                const detailRow = tbody.insertRow();
                detailRow.classList.add('detail-row', 'hidden');
                detailRow.setAttribute('data-serie-id', serieId);
                detailRow.innerHTML = `
                    <td colspan="7" style="padding: 0;">
                        <table class="sub-table">
                            <thead>
                                <tr>
                                    <th>ID Registro</th>
                                    <th>Fecha Mantenimiento</th>
                                    <th>Estado</th>
                                    <th>Días</th>
                                    <th>Observaciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${serie.mantenimientos.map(m => `
                                    <tr class="${getClaseFila(m)}">
                                        <td>${m.idreg}</td>
                                        <td>${formatearFecha(m.fmtto)}</td>
                                        <td>${getEstadoBadge(m.dias_diferencia, m.flgestado)}</td>
                                        <td>${formatearDias(m.dias_diferencia, m.flgestado)}</td>
                                        <td>${m.cobserva || '-'}</td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </td>
                `;
            });
        }

        function toggleSerie(serieId) {
            const rows = document.querySelectorAll('#tableBody tr');
            let detailRow = null;
            let icon = null;
            
            rows.forEach(row => {
                if (row.getAttribute('data-serie-id') === serieId) {
                    if (row.classList.contains('detail-row')) {
                        detailRow = row;
                    } else if (row.classList.contains('serie-principal')) {
                        icon = row.querySelector(`#icon-${serieId}`);
                    }
                }
            });
            
            if (!detailRow || !icon) return;
            
            if (detailRow.classList.contains('hidden')) {
                detailRow.classList.remove('hidden');
                icon.classList.add('rotated');
            } else {
                detailRow.classList.add('hidden');
                icon.classList.remove('rotated');
            }
        }