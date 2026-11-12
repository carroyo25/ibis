$(function () {
  let nroRegistro = 0;
  permisos = [];
 
  //MUESTRA EL MODAL DE ALERTA DE MINIMOS
  if ($("#alerta_minimo").val() == 1) {

    // Mostrar loading
    $("#tabla_minimos tbody").html(
      '<tr><td colspan="10" style="text-align:center; padding:40px;"><i class="fas fa-spinner fa-spin" style="font-size:24px;"></i><p>Cargando...</p></td></tr>',
    );

    //cargar datos
    mostrarMinimos();

  }

  $(".acordeon .submenu").on("click", "a", function (e) {
    e.preventDefault();

    $(".opcion").removeClass("visitado");
    $(this).addClass("visitado");

    $("#modulo").val($(this).data("modulo"));

    $("#esperar").css({ display: "block" });

    fetch($(this).attr("href"), {
      headers: {
        "Cache-Control": "no-cache",
        Pragma: "no-cache",
      },
    })
      .then((response) => response.text())
      .then((data) => {
        $(".cargaModulo").html(data);
      });

    return false;
  });

  $(".acordeon").on("click", ".link", function (e) {
    e.preventDefault();

    $(".submenu").slideUp();

    if (open != $(this).text()) {
      $(this).next(".submenu").slideToggle();
      $(this).children(".fa-chevron-down").css("transform", "rotate(180deg)");
      open = $(this).text();
    } else {
      $(this).next(".submenu").slideToggle();
      $(this).children(".fa-chevron-down").css("transform", "rotate(0)");
    }

    return false;
  });

  $("#cabecera_main_option").click(function (e) {
    e.preventDefault();

    $("#cabecera_menu").slideToggle();

    return false;
  });

  $("body").on("focusOut", "#cabecera_menu", function (e) {
    e.preventDefault();

    $("#cabecera_menu").fadeOut();

    return false;
  });

  $("body").on("click", "#irInicio", function (e) {
    e.preventDefault();

    window.location = RUTA + "panel";

    return false;
  });

  $("#changePass").click(function (e) {
    e.preventDefault();

    $("#cambio").fadeIn();
    $("#cabecera_menu").fadeOut();

    return false;
  });

  $("#btnCancelarCambio").click(function (e) {
    e.preventDefault();

    $("#cambio").fadeOut();

    return false;
  });

  $("#btnAceptarCambio").click(function (e) {
    e.preventDefault();
    try {
      if ($("#nueva_clave").val() === "") throw "Ingrese una clave";
      if ($("#nueva_clave_comfirm").val() === "")
        throw "Confirme la clave ingresada";
      if ($("#nueva_clave").val() !== $("#nueva_clave_comfirm").val())
        throw "Las claves no son iguales";

      $.post(
        RUTA + "panel/cambiaClave",
        { clave: $("#nueva_clave").val() },
        function (data, textStatus, jqXHR) {
          $("#cambio").fadeOut();
          if (data) {
            mostrarMensaje("Clave cambiada", "mensaje_correcto");
            $("#form__clave")[0].reset();
          } else {
            mostrarMensaje("No se actualizo la clave", "mensaje_error");
          }
        },
        "text",
      );
    } catch (error) {
      mostrarMensaje(error, "mensaje_error");
    }

    return false;
  });

  $("#tablaPanelAsignaciones tbody").on("click", "a", function (e) {
    e.preventDefault();

    nroRegistro = $(this).attr("href");

    $("#preguntaVerifica").fadeIn();

    return false;
  });

  $("#btnAceptarVerifica").click(function (e) {
    e.preventDefault();

    let formData = new FormData();
    formData.append("id", nroRegistro);
    formData.append("user", $("#id_user").val());

    fetch(RUTA + "panel/marcaRegistro", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.text())
      .then((data) => {
        $("#tablaPanelAsignaciones tbody").empty().append(data);
        $("#preguntaVerifica").fadeOut();
      });

    return false;
  });

  $("#btnCancelarVerifica").click(function (e) {
    e.preventDefault();

    $("#pregunta").fadeOut();

    return false;
  });

  //PROCESOS MINIMOS
  $("#btnCerrarMinimo").click(function (e) {
    e.preventDefault();

    $("#alertaMinimos").fadeOut();

    return false;
  });

  $("#btnConsultar").click(function(e){
    e.preventDefault();

    // Mostrar loading
    $("#tabla_minimos tbody").empty().html(
      '<tr><td colspan="10" style="text-align:center; padding:40px;"><i class="fas fa-spinner fa-spin" style="font-size:24px;"></i><p>Cargando...</p></td></tr>',
    );

    //cargar datos
    mostrarMinimos();
    
    return false;
  });

  function mostrarMinimos() {
    let formData = new FormData();
    formData.append("usuario", $("#id_user").val());
    formData.append("costos", $("#filtroCostos").val());
    formData.append("codigo", $("#filtroCodigo").val());
    formData.append("producto", $("#filtroProducto").val());

    fetch(RUTA + "panel/muestraMinimos", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        $("#tabla_minimos tbody").empty();

        if (data.datos && data.datos.length > 0) {
          let row = "";

          data.datos.forEach((item) => {
            
            // ===== MAPEO DE CAMPOS =====
            const ccodprod = item.ccodprod || "";
            const cdesprod = item.cdesprod || "";
            const unidad = item.unidad || "";
            const proyecto = item.proyecto || "";
            
            // Parseo seguro de números
            const saldoReal = Number(item.saldo || 0);
            const stockMinimo = Number(item.nminimo || 0);
            const totalIngresos = Number(item.total_ingresos || 0);
            const totalSalidas = Number(item.total_salidas || 0);
            
            // Formateador para mostrar con 2 decimales
            const fmt = (n) => Number(n).toLocaleString("es-PE", { minimumFractionDigits: 2, maximumFractionDigits: 2 });

            // ===== ESTADO Y COLOR (Leído directamente del Backend) =====
            const estado = item.estado_stock || "NORMAL";
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
            row += `<tr class="pointer" data-costos="${item.idcostos || item.ncostos || ""}">
                      <td class="textoCentro">${proyecto}</td>
                      <td class="textoCentro">${ccodprod}</td>
                      <td class="pl20px">${cdesprod}</td>
                      <td class="textoCentro">${unidad}</td>
                      <td class="textoCentro">
                         <span style="background:${colorEstado}; padding:4px 10px; border-radius:12px; font-size:11px; color:${textoColor}; font-weight:bold; display:inline-block; white-space:nowrap;">
                              ${estado}
                          </span>
                      </td>
                      <td class="textoDerecha">${item.ffecha || "-"}</td>
                      <td class="textoDerecha">${stockMinimo > 0 ? fmt(stockMinimo) : "-"}</td>
                      <td class="textoDerecha"><strong>${fmt(saldoReal)}</strong></td>
                  </tr>`;
          });

          $("#tabla_minimos tbody").empty().append(row);
          $("#totalRegistros").text(data.datos.length);
        } else {
          $("#tabla_minimos tbody").html(
            `<tr><td colspan="8" style="text-align:center; padding:40px; color:#999;">
                <i class="fas fa-inbox" style="font-size:40px; display:block; margin-bottom:10px;"></i>
                No se encontraron productos con alertas de mínimo
            </td></tr>`
          );
          $("#totalRegistros").text("0");
        }

        $("#alertaMinimos").fadeIn();
      })
      .catch((error) => {
        $("#tabla_minimos tbody").html(
          `<tr><td colspan="8" style="text-align:center; color:red; padding:40px;">
              <i class="fas fa-exclamation-triangle" style="font-size:24px; display:block; margin-bottom:10px;"></i>
              Error al cargar datos: ${error.message}
          </td></tr>`
        );
      });
}
});
