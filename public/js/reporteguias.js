$(function () {
  llenarListado();
});

document.addEventListener("keydown", function (e) {
  if (
    e.key === "Escape" &&
    document.getElementById("vistaprevia").style.display === "block"
  ) {
    fadeOut(document.getElementById("vistaprevia"));
  }
});

document.addEventListener("click", (e) => {
  if (e.target.id == "btnConsulta") {
    e.preventDefault();

    llenarListado();

    return false;
  } else if (e.target.id == "repoExcel") {
    e.preventDefault();

    exportarExcel();

    return false;
  } else if (e.target.matches(".pointer *")) {
    e.preventDefault();

    const parentPointer = e.target.closest(".pointer").dataset.guiaid;
    const parentSunat = e.target.closest(".pointer").dataset.guiasunatnro;

    if (parentSunat === "null" || parentSunat === null) {
      document
        .getElementById("pdfPreview")
        .setAttribute(
          "src",
          "https://sicalsepcon.net/ibis/public/documentos/guias_remision/" +
            parentPointer +
            ".pdf",
        );
    } else {
      document
        .getElementById("pdfPreview")
        .setAttribute(
          "src",
          "https://sicalsepcon.net/ibis/public/documentos/guias_remision/20504898173-09-T001-" +
            parentSunat +
            ".pdf",
        );
    }

    fadeIn(document.getElementById("vistaprevia"));

    return false;
  } else if (e.target.matches(".cerrar_vista")) {
    e.preventDefault();

    fadeOut(document.getElementById("vistaprevia"));

    return false;
  }
});

llenarListado = async () => {
  try {
    let formData = new FormData();
    formData.append("anio", document.getElementById("anioSearch").value);
    formData.append("guia", document.getElementById("guiaSearch").value);
    formData.append("sunat", document.getElementById("guiaSunat").value);

    $("#esperar").css({ display: "block" });

    const response = await fetch(RUTA + "reporteguias/listaGuias", {
      method: "POST",
      body: formData,
    });

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    const data = await response.json();

    const tablaCuerpo = document.getElementById("tablaPrincipalCuerpo");

    if (!tablaCuerpo) {
      throw new Error("Element with ID 'tablaPrincipalCuerpo' not found");
    }

    tablaCuerpo.innerHTML = "";

    const fragment = document.createDocumentFragment();

    data.datos.forEach((element) => {
      const tr = document.createElement("tr");

      tr.classList.add("pointer");
      tr.dataset.guiaid = element.cnumguia;
      tr.dataset.guiasunatnro = element.guiasunat;
      tr.innerHTML = `<td class="textoCentro">${element.cnumguia}</td>
                            <td class="textoCentro">${element.freg || ""}</td>
                            <td class="textoCentro">${element.anio || ""}</td>
                            <td class="pl20px">${element.cobserva || ""}</td>
                            <td class="textoCentro">${element.guiasunat || ""}</td>
                            <td class="textoCentro">${element.cenvio || ""}</td>`;

      fragment.appendChild(tr);
    });

    tablaCuerpo.appendChild(fragment);

    $("#esperar")
      .css("opacity", "0")
      .fadeOut()
      .promise()
      .done(function () {
        iniciarPaginador();
      });
  } catch (error) {
    console.error("Error in llenarListado:", error);
    // You might want to show an error message to the user here
  }
};

// Función para fade in
function fadeIn(element) {
  element.style.display = "block";
  // Timeout para permitir el cambio de display antes de la transición
  setTimeout(() => {
    element.style.opacity = "1";
  }, 10);
}

// Función para fade out
function fadeOut(element) {
  element.style.opacity = "0";
  // Esperar a que termine la transición antes de ocultar
  setTimeout(() => {
    element.style.display = "none";
  }, 300); // Debe coincidir con la duración de la transición en CSS (0.3s = 300ms)
}

function exportarExcel() {
  var array = [];
  /* Obtenemos todos los tr del Body*/
  var rowsBody = $("#tablaPrincipal").find("tbody > tr");
  /* Obtenemos todos los th del Thead */
  var rowsHead = $("#tablaPrincipal").find("thead > tr > th");

  /* Iteramos sobre as filas del tbody*/
  for (var i = 0; i < rowsBody.length; i++) {
    var obj = {}; /* auxiliar*/
    for (
      var j = 0;
      j < rowsHead.length;
      j++ /*  Iteramos sobre los th de THead*/
    )
      /*Asignamos como clave el text del th del thead*/
      /*Asignamos como Valor el text del tr del tbody*/
      obj[rowsHead[j].dataset.titulo] =
        rowsBody[i].getElementsByTagName("td")[j].innerText;

    array.push(obj); /* Añadimos al Array Principal*/
  }

  $.post(
    RUTA + "reporteguias/exportar",
    { detalles: JSON.stringify(array) },
    function (data, textStatus, jqXHR) {
      window.location.href = data.documento;
    },
    "json",
  );
}
