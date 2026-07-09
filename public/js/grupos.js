$(function () {
  var accion = "";
  var index = 0;

  $("#esperar").css({ display: "none" });

  consultarDatos(1);

  $("#nuevoRegistro").click(function (e) {
    e.preventDefault();

    $("#proceso").fadeIn(function(e){
      $("#formProceso")[0].reset;
    });

    accion = "n";

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
      if (result["tipoClase"] == null) throw "Seleccione el tipo";

      if (accion == "n") {
        $.post(
          RUTA + "grupos/nuevoGrupo",
          { datos: result },
          function (data, textStatus, jqXHR) {
            mostrarMensaje(data.mensaje, data.clase);
            $("#formProceso").trigger("reset");  // Alternativa con jQuery
          },
          "json",
        );
      } else {
        $.post(
          RUTA + "grupos/modificaGrupo",
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

  $("#cerrarVentana").click(function (e) {
    e.preventDefault();

    $("#proceso").fadeOut(function(e){
      $("#formProceso").trigger("reset");  // Alternativa con jQuery
    });

    consultarDatos(1);

    return false;
  });

  $("#tablaPrincipal tbody").on("click", "tr", function (e) {
    e.preventDefault();

    $.post(
      RUTA + "grupos/consultaId",
      { id: $(this).data("id") },
      function (data, textStatus, jqXHR) {
        $("#codgrupo").val(data.grupo[0].ncodgrupo);
        $("#codigo").val(data.grupo[0].ccodcata);
        $("#descripcion").val(data.grupo[0].cdescrip);
        $(
          "input[name=tipoClase][value='" + data.grupo[0].ntipclase + "']",
        ).prop("checked", true);
      },
      "json",
    );
    accion = "u";
    $("#proceso").fadeIn();

    return false;
  });

  $("#tablaPrincipal tbody").on("click", "a", function (e) {
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
      RUTA + "grupos/desactivaGrupo",
      { id: index },
      function (data, textStatus, jqXHR) {
        $("#pregunta").fadeOut(function(){
          consultarDatos(1);
        });
      },
      "text",
    );

    return false;
  });

  $("#consulta").keypress(function (e) {
    if (e.which == 13) {
      consultarDatos(1);
    } 
  });

  //FUNCION PRINCIPAL PARA CONSULTAR DATOS DE LA TABLA
  async function consultarDatos(pagina) {
    pagina = pagina || 1;
    const descripcion = $("#consulta").val() || "";

    // Mostrar loading
    $("#tablaPrincipal tbody").html(
      '<tr><td colspan="9" style="text-align:center; padding:40px;"><i class="fas fa-spinner fa-spin" style="font-size:24px;"></i><p>Cargando...</p></td></tr>',
    );

    let formData = new FormData();
    formData.append("descripcion", descripcion);
    formData.append("page", pagina);

    fetch(RUTA + "grupos/actualizaTabla", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        let row = "";
        let item = (pagina - 1) * 10 + 1;
        let estado, colorFondo, textoColor, badge;

        //VERIFICA SI HAY DATOS EN EL ARREGLO
        if (data.success && data.data && data.data.length > 0) {
          data.data.forEach((element) => {
            // ===== MAPEO DE CAMPOS =====
            const ncodgrupo = element.ncodgrupo || "";
            const ccodcata = element.ccodcata || "";
            const cdescrip = element.cdescrip || "";

            if (element.ntipclase == 37){
              colorFondo = "linear-gradient(135deg, #17A2B8, #0F7A8A)";  // Azul/Cyan para B
              textoColor = "white";
              badge = "📦 ";
            }else{
              colorFondo = "linear-gradient(135deg, #1DAA61, #14854A)";  // Verde para S
              textoColor = "white";
              badge = "🛠️ ";  // Icono para Servicios
            }

            // ===== CONSTRUIR FILA =====
            row += `<tr data-id ="${ncodgrupo}" class="pointer">
                        <td class="textoCentro">
                            <span style="background:${colorFondo}; 
                                        padding:4px 10px; 
                                        border-radius:12px; 
                                        font-size:11px; 
                                        color:${textoColor}; 
                                        font-weight:bold; 
                                        display:inline-block; 
                                        white-space:nowrap;
                                        width:50%">${ccodcata}
                            </span>
                        </td>
                        <td class="pl20px">${badge + cdescrip}</td>
                        <td class="textoCentro"><a href="${ncodgrupo}"><i class="fas fa-trash-alt"></i></a></td>
                    </tr>`;
          });
        } else {
          //SI NO HAY ENVIA UNA FILA DE RESULTADO
          row = `<tr>
                    <td colspan="9" style="text-align:center; padding:30px; color:#999;">
                      <i class="fas fa-inbox" style="font-size:40px; display:block; margin-bottom:10px;"></i>
                      No se encontraron resultados
                    </td>
                </tr>`;
        }

        // ===== INSERTAR FILAS USANDO EL SELECTOR CORRECTO =====
        $("#tbodyGrupos").empty().append(row);

        paginador(data.total,data.total_paginas,data.pagina)
      })
      .catch((error) => {
        $("#tablaPrincipal tbody").html(
          `<tr><td colspan="9" style="text-align:center; color:red; padding:40px;">
                          <i class="fas fa-exclamation-triangle" style="font-size:24px; display:block; margin-bottom:10px;"></i>
                          Error al cargar datos: ${error.message}
                      </td></tr>`,
        );
        $("#paginador").empty();
      });
  }

  window.consultarDatos = consultarDatos;
});
