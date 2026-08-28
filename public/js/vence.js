$(() => {
  $("#esperar").fadeOut();

  let producto = "";

  // ===== MÁSCARA PARA FECHA (dd/mm/aaaa) =====
  const fechaInput = document.getElementById("fecha");

  fechaInput.addEventListener("input", function (e) {
    let value = this.value.replace(/\D/g, "");
    if (value.length > 8) value = value.slice(0, 8);

    let formatted = "";
    for (let i = 0; i < value.length; i++) {
      if (i === 2 || i === 4) formatted += "/";
      formatted += value[i];
    }
    this.value = formatted;
  });

  $("#btnConsulta").click(function (e) {
    e.preventDefault();

    $.post(
      RUTA + "vence/consulta",
      {
        cc: $("#costosSearch").val(),
        codigo: $("#codigoBusqueda").val(),
        descripcion: $("#descripcionSearch").val(),
      },
      function (data, text, requestXHR) {
        $("#tablaPrincipal tbody").empty().append(data);
      },
      "text",
    );

    return false;
  });

  $("#tablaPrincipal tbody").on("click", "a", function (e) {
    e.preventDefault();

    $("#registrar").fadeIn();

    return false;
  });

  $("#registrarlnk").click(async function (e) {
    e.preventDefault();

    // Verificar permisos antes de abrir el modal
    const idUser = $("#id_user").val() || 1;
    const permisos = await verificarPermisos(idUser, 55);

    if (!permisos || !permisos.datos[0].agrega) {
      mostrarMensaje(
        "⚠️ No tienes permisos para registrar vecimientos",
        "mensaje_error",
      );
      return; // No abre el modal
    }

    if ($("#costosSearch").val() == "-1") {
      mostrarMensaje("⚠️ Seleccione un centro de costos", "mensaje_error");
      return; // No abre el modal
    }

    $("#registrar").fadeIn();

    return false;
  });

  $("#btnAcceptRegister").click((e) => {
    e.preventDefault();

    const boton = $(this);

    boton.html(`<i class="fas fa-spinner fa-spin"></i> Procesando`);

    if ($("#codigo").val() == "")
      throw new Error("❓ Ingrese un codigo para registro");
    if ($("#vencimiento").val() == "")
      throw new Error("❓ Ingrese fecha de vencimiento");
    if ($("#costosSearch").val() == "")
      throw new Error("❓ Seleccione un Centro de Costos");

    try {
      const formData = new FormData();

      formData.append("codigo", producto);
      formData.append("costos", $("#costosSearch").val());
      formData.append("fecha", convertirFechaParaBD($("#fecha").val()));

      fetch(RUTA + "vence/registraVencimiento", {
        method: "POST",
        body: formData,
      }).then((response) =>
        response.json().then((data) => {
          boton.html(`<i class="fas fa-check"></i> Aceptar`);
          if (data.success) {
            mostrarMensaje("✅ Vencimientos actualizados", "mensaje_correcto");
            limpiarEntradas();
          } else {
            mostrarMensaje("😒 Error al actualizar", "mensaje_error");
          }
        }),
      );
    } catch (error) {
      mostrarMensaje(error.message, "mensaje_error");
    }

    return false;
  });

  // CONSULTAR EL CODIGO SI ES NUEVO
  $("#codigo").keypress(function (e) {
    if (e.which == 13) {
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
            $("#descripcion").val(data.datos[0]["cdesprod"]);
          });
      }
    }
  });

  $("#btnCancelRegister").click((e) => {
    e.preventDefault();

    $("#registrar").fadeOut();
    limpiarEntradas();

    return false;
  });

  $("#tablaPrincipal tbody").on("dblclick", "tr", function (e) {
    e.preventDefault();

    $("#codigo_item").text($(this).find("td").eq(2).text());
    $("#nombre_item").text($(this).find("td").eq(3).text());

    $.post(
      RUTA + "vence/consultaItem",
      { item: $(this).data("idproducto"), costos: $("#costosSearch").val() },
      function (data, text, requestXHR) {
        $("#listaVencimientos tbody").empty().append(data);

        $("#vistadocumento").fadeIn();
      },
      "text",
    );

    return false;
  });

  $("#closeDocument").click(function (e) {
    e.preventDefault();

    $("#vistadocumento").fadeOut();

    return false;
  });

  $("#excelFile").click(function (e) {
    e.preventDefault();

    $("#esperar").css("opacity", "1").fadeIn();

    $.post(
      RUTA + "vence/exportaExcel",
      { registros: JSON.stringify(detalles()) },
      function (data, textStatus, jqXHR) {
        $("#esperar").css("opacity", "0").fadeOut();
        window.location.href = data.documento;
      },
      "json",
    );

    return false;
  });

  $("#sendNotificacion").click(function (e) {
    e.preventDefault();

    $.post(
      RUTA + "vence/enviaNotificacion",
      {
        costos: $("#costosSearch").val(),
        codigo: $("#codigoBusqueda").val(),
        descripcion: $("#descripcionSearch").val(),
      },
      function (data, text, requestXHR) {
        $("#listaVencimientos tbody").empty().append(data);
      },
      "json",
    );

    return false;
  });

  function limpiarEntradas() {
    $("#codigo,#descripcion").val("");
    $("#fecha").val("dd/mm/yyyy");
  }

  function convertirFechaParaBD(fechaStr) {
    const partes = fechaStr.split("/");
    return `${partes[2]}-${partes[1]}-${partes[0]}`;
  }

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
});

detalles = () => {
  DATA = [];
  let TABLA = $("#tablaPrincipal tbody >tr");

  TABLA.each(function () {
    let ITEM = $(this).find("td").eq(0).text(),
      COSTOS = $(this).find("td").eq(1).text(),
      CODIGO = $(this).find("td").eq(2).text(),
      DESCRIPCION = $(this).find("td").eq(3).text(),
      UNIDAD = $(this).find("td").eq(4).text(),
      VENCE = $(this).find("td").eq(5).text(),
      DIAS = $(this).find("td").eq(6).text();

    item = {};

    item["item"] = ITEM;
    item["costos"] = COSTOS;
    item["codigo"] = CODIGO;
    item["descripcion"] = DESCRIPCION;
    item["unidad"] = UNIDAD;
    item["vence"] = VENCE;
    item["dias"] = DIAS;

    DATA.push(item);
  });

  return DATA;
};
