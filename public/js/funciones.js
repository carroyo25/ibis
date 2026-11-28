//$("#esperar").fadeOut();

var HOST = $(location).attr("hostname");

if (HOST == "localhost") {
  var RUTA = "https://localhost/ibis/";
} else if (HOST == "192.168.110.121") {
  var RUTA = "http://192.168.110.121/ibis/";
} else if (HOST == "200.41.86.58") {
  var RUTA = "http://200.41.86.58/ibis/";
} else if (HOST == "sicalsepcon.net") {
  var RUTA = "https://sicalsepcon.net/ibis/";
} else if (HOST == "200.115.23.164") {
  var RUTA = "http://200.115.23.164/ibis/";
} else if (HOST == "127.0.0.1") {
  var RUTA = "http://127.0.0.1/ibis/";
} else if (HOST == "179.49.67.42") {
  var RUTA = "http://179.49.67.42/ibis/";
} else if (HOST == "localhost/ibis/") {
  var RUTA = "https://localhost/ibis/";
}

var emailreg = /^[a-zA-Z0-9_\.\-]+@[a-zA-Z0-9\-]+\.[a-zA-Z0-9\-\.]+$/;
var numeros = /^[0-9]*\.?[0-9]*$/;
var fecha = /^([0-9]{4}\-[0-9]{2}\-[0-9]{2})$/;
var ruc = /^[0-9]{11}$/;
var url = encodeURI(
  "http://192.168.1.30/postulante/documentos/pdf/62a4da4d7a120.pdf",
);

// La forma más segura - usar window directamente
var directorioRaiz = null;
var archivosHandle = new Map();
var permisoSolicitado = false;

var totalArchivos = 0;
var archivosSeleccionados = [];

$.strPad = function (i, l, s) {
  var o = i.toString();
  if (!s) {
    s = "0";
  }
  while (o.length < l) {
    o = s + o;
  }
  return o;
};

/**
 * Convert a string to HTML entities
 */
String.prototype.toHtmlEntities = function () {
  return this.replace(/./gm, function (s) {
    // return "&#" + s.charCodeAt(0) + ";";
    return s.match(/[a-z0-9\s]+/i) ? s : "&#" + s.charCodeAt(0) + ";";
  });
};

/**
 * Create string from HTML entities
 */
String.fromHtmlEntities = function (string) {
  return (string + "").replace(/&#\d+;/gm, function (s) {
    return String.fromCharCode(s.match(/\d+/gm)[0]);
  });
};

numberWithCommas = (x) => {
  return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
};

mostrarMensaje = (mensaje, clase) => {
  $(".mensaje p").empty().text(mensaje);

  $(".mensaje")
    .removeClass("mensaje_error, mensaje_correcto")
    .addClass(clase)
    .css("right", "0");

  setTimeout(function () {
    $(".mensaje").css("right", "-100%");
  }, 3600);
};

fillTables = (table, idx) => {
  var item = 0;

  table.each(function () {
    item++;

    $(this).find("td").eq(idx).text($.strPad(item, 3));
  });
};

checkExistTable = (table, item, indice) => {
  let itenExist = false;

  table.each(function () {
    let itemTable = $(this).find("td").eq(indice).text();

    if (itemTable === item) {
      itenExist = true;
      return false;
    }
  });

  return itenExist;
};

checkCantTables = (table, idx) => {
  let sw = false;

  table.each(function () {
    if ($(this).find("td").eq(idx).children().val() == 0) {
      sw = true;
    }
  });

  return sw;
};

checkTypeItemsTables = (table, type) => {
  let critero = type == 37 ? "B" : "S",
    bienes = 0,
    servicios = 0,
    existeItem = true;

  table.each(function () {
    let itemTable = $(this).find("td").eq(2).text();

    if (itemTable.substr(0, 1) === critero) {
      bienes++;
    } else {
      servicios++;
    }
  });

  if (type == 37 && servicios == 0) {
    existeItem = false;
  } else if (type == 38 && servicios == 0) {
    existeItem = false;
  }

  return existeItem;
};

checkCantTablesMinMax = (table, idx) => {
  let sw = false;

  table.each(function () {
    if (
      $(this).find("td").eq(idx).children().val() < 1 ||
      $(this).find("td").eq(idx).children().val() > 5
    ) {
      sw = true;
    }
  });

  return sw;
};

buscar = (_this) => {
  $.each($("#tablaPrincipal tbody tr"), function () {
    if (
      $(this).text().toLowerCase().indexOf($(_this).val().toLowerCase()) === -1
    )
      $(this).hide();
    else $(this).show();
  });
};

previewImg = (event, img) => {
  $("#foto")
    .attr("src", event.target.result)
    .attr("width", "300px")
    .attr("height", "250px");
};

diferenciadefechas = (inicio, final) => {
  var entrega = new Date(inicio).getTime();
  var actual = new Date(final).getTime();

  diff = (entrega - actual) / (1000 * 60 * 60 * 24);

  return diff;
};

sumarTotales = (table) => {
  let suma = 0;

  table.each(function () {
    let valor = $(this).data("total");
    suma = suma + valor;
  });

  return suma;
};

verificarCantidades = (table) => {};

fechaActual = () => {
  var d = new Date();

  var month = d.getMonth() + 1;
  var day = d.getDate();
  var output =
    d.getFullYear() +
    "-" +
    (month < 10 ? "0" : "") +
    month +
    "-" +
    (day < 10 ? "0" : "") +
    day;

  return output;
};

sumarDias = (dias) => {
  d = new Date();

  d.setDate(d.getDate() + dias);

  var month = d.getMonth() + 1;
  var day = d.getDate();
  var output =
    d.getFullYear() +
    "-" +
    (month < 10 ? "0" : "") +
    month +
    "-" +
    (day < 10 ? "0" : "") +
    day;

  return output;
};

//queda pendiente el drag and drop
dropHandler = (ev) => {
  // Evitar el comportamiendo por defecto (Evitar que el fichero se abra/ejecute)
  ev.preventDefault();

  if (ev.dataTransfer.items) {
    // Usar la interfaz DataTransferItemList para acceder a el/los archivos)
    let fragment = "";
    for (var i = 0; i < ev.dataTransfer.items.length; i++) {
      // Si los elementos arrastrados no son ficheros, rechazarlos
      if (ev.dataTransfer.items[i].kind === "file") {
        var file = ev.dataTransfer.items[i].getAsFile();
        console.log("... file[" + i + "].name = " + file.name);
        fragment += `<li><p><i class="far fa-file"></i></p><p>${file.name}</p></li>`;
      }
    }
  } else {
    // Usar la interfaz DataTransfer para acceder a el/los archivos
    for (var i = 0; i < ev.dataTransfer.files.length; i++) {
      console.log(
        "... file[" + i + "].name = " + ev.dataTransfer.files[i].name,
      );
    }
  }

  // Pasar el evento a removeDragData para limpiar
  removeDragData(ev);
};

dragOverHandler = (ev) => {
  console.log("File(s) in drop zone");

  // Prevent default behavior (Prevent file from being opened)
  ev.preventDefault();
};

removeDragData = (ev) => {
  console.log("Removing drag data");

  if (ev.dataTransfer.items) {
    // Use DataTransferItemList interface to remove the drag data
    ev.dataTransfer.items.clear();
  } else {
    // Use DataTransfer interface to remove the drag data
    ev.dataTransfer.clearData();
  }
};

validarExtension = (fileUpload) => {
  // Obtener nombre de archivo
  let archivo = fileUpload.value,
    // Obtener extensión del archivo
    extension = archivo.substring(archivo.lastIndexOf("."), archivo.length);
  // Si la extensión obtenida no está incluida en la lista de valores
  // del atributo "accept", mostrar un error.
  if (fileUpload.getAttribute("accept").split(",").indexOf(extension) < 0) {
    return true;
  } else {
    return false;
  }
};

formatoNumeroConComas = (number, decimals, dec_point, thousands_point) => {
  if (number == null || !isFinite(number)) {
    throw new TypeError("number is not valid");
  }

  if (!decimals) {
    var len = number.toString().split(".").length;
    decimals = len > 1 ? len : 0;
  }

  if (!dec_point) {
    dec_point = ".";
  }

  if (!thousands_point) {
    thousands_point = ",";
  }

  number = parseFloat(number).toFixed(decimals);

  number = number.replace(".", dec_point);

  var splitNum = number.split(dec_point);
  splitNum[0] = splitNum[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousands_point);
  number = splitNum.join(dec_point);

  return number;
};

addComa = (x) => {
  return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
};

///filtro

$(".datafiltro").append(`
        <a href="#" class="listaFiltroTabla" data-idcol="0"><i class="fas fa-angle-down"></i></a>
        <div class="filtro">
            <div class="oculto">
                <ul class="filtro_cantidad">
                    <li><a>Ordenar ascedentemente</a></li>
                    <li><a>Ordenar Descendentemente</a></li>
                </ul>
            </div>
            <hr>
            <input type="text" class="filterSearch" placeholder="Buscar Elementos...">
            <ul class="ul_filtro"> 
            </ul>
            <div class="opciones_filtro">
                <button id="btn_filter_cancel">Cancelar</button>
            </div>
        </div>`);

//filtrar tablas por la cabecera
$(".listaFiltroTabla").click(function (e) {
  e.preventDefault();

  $(".ul_filtro").empty();
  $(".filtro").fadeOut();

  let idx = parseInt($(this).parent().data("idcol")),
    tabla = $(this).parent().parent().parent().parent().attr("id");

  t = "#" + tabla + " tbody tr";

  $(this)
    .next()
    .toggle(function () {
      capturarValoresColumnas($(t), idx);
    });

  return false;
});

$(".filtro").on("click", "#btn_filter_cancel", function (e) {
  e.preventDefault();

  $(this).closest(".filtro").fadeOut();

  return false;
});

$(".ul_filtro").on("click", "a", function (e) {
  e.preventDefault();

  let padre = $(this).parent().parent().parent(),
    value = $(this).text(),
    columna = $(this).parent().parent().parent().parent().data("idcol"),
    tabla = $(this)
      .parent()
      .parent()
      .parent()
      .parent()
      .parent()
      .parent()
      .parent()
      .attr("id");

  t = "#" + tabla + " tbody tr";

  mostrarValoresFiltrados($(t), columna, value);

  padre.fadeOut(function () {
    $(".ul_filtro").empty();
  });

  return false;
});

$(".filtro_cantidad").on("click", "a", function (e) {
  e.preventDefault();

  let padre = $(this).parent().parent().parent().parent(),
    value = $(this).text(),
    columna = $(this).parent().parent().parent().parent().data("idcol"),
    tabla = $(this)
      .parent()
      .parent()
      .parent()
      .parent()
      .parent()
      .parent()
      .parent()
      .parent()
      .attr("id");

  t = "#" + tabla + " tbody tr";

  ordernarAscende(tabla, columna, value);

  padre.fadeOut();

  return false;
});

$(".filterSearch").keyup(function () {
  let value = $(this).val().toLowerCase();

  let l = ".ul_filtro" + " li a";

  $(l).filter(function () {
    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
  });
});

//filtros en tablas

capturarValoresColumnas = (tabla, columna) => {
  DATA = [];

  tabla.each(function () {
    let VALOR = $(this).find("td").eq(columna).text();
    DATA.push(VALOR);
  });

  //elimina los duplicados
  var unique = DATA.filter((x, i) => DATA.indexOf(x) === i);
  for (i = 0; i < unique.length; i++) {
    $(".ul_filtro").append(`<li><a href='#'>${unique[i]}</a></li>`);
  }
};

mostrarValoresFiltrados = (tabla, columna, valor) => {
  tabla.each(function () {
    if ($(this).find("td").eq(columna).text() == valor) {
      $(this).show();
    } else {
      $(this).hide();
    }
  });
};

ordernarAscende = (tabla, columna, valor) => {
  let tablaProceso = document.getElementById(tabla);
  let tbody = tablaProceso.querySelector("tbody");
  let trs = tbody.querySelectorAll("tr");

  if (trs.length == 0) {
    // Nada que hacer con esta tabla
    return;
  }

  let order, lastColumn;

  trs.forEach((tr, trNum) => (tr.dataset.num = trNum));
  let ths = tablaProceso.querySelectorAll("thead th");

  ths.forEach((th, column) =>
    th.addEventListener("click", () => {
      let values = [];
      trs.forEach((tr) => {
        values.push({
          trData: tr.dataset.num,
          value: tr.querySelectorAll("td")[column].innerText,
        });
        tr.querySelectorAll("td")[column].classList.add("red");
      });
      // Ordenar valores
      values.sort((a, b) => a.value.localeCompare(b.value));
      // Definir el orden en que se va a mostrar
      if (lastColumn !== column) {
        // Si el clic no es en la misma columna que el anterior
        // Restablecer orden
        order = null;
      }
      lastColumn = column;
      // Definir el orden de salida
      if (!order || order == "DESC") {
        // En el primer clic en la misma columna, order es nulo
        order = "ASC";
      } else {
        order = "DESC";
      }
      if (order == "DESC") {
        values.reverse();
      }
      // Ordenar tabla
      values.forEach((data) => {
        let trMove = tablaProceso.querySelector(`[data-num="${data.trData}"]`);
        tbody.appendChild(trMove);
      });
    }),
  );
};

//////////////////////////////////////////////////

$(".headerTableFilter").append(
  `<a href="#" class="contenedorfiltro"><i class="fas fa-angle-down"></i></a>
    <div class="filter_options">
        <p>Buscar</p>
        <input type="text" name="strSearch" id="strSearch" class="textoNuevoBuscar" placeholder="Buscar Elementos">
        <ul class="filterList">
        </ul>
        <div class="btn_options">
            <button class="btn_sendfilter">Aceptar</button>
            <button class="btn_cancelfilter">Cancelar</button>
        </div>
    </div>`,
);

// Función para realizar paginación después de la carga de datos
function iniciarPaginador() {
  const content = document.querySelector(".itemsTabla");
  let itemsPerPage = 100; // Valor por defecto
  let currentPage = 0;
  const maxVisiblePages = 10; // Número máximo de botones visibles
  const items = Array.from(content.getElementsByTagName("tr")).slice(1); // Tomar todos los <tr>, excepto el primero (encabezado)

  // Mostrar una página específica
  function showPage(page) {
    const startIndex = page * itemsPerPage;
    const endIndex = startIndex + itemsPerPage;
    items.forEach((item, index) => {
      item.classList.toggle("hidden", index < startIndex || index >= endIndex);
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

function limpiarFormulario() {
  const formulario = document.getElementById("formProceso");

  // 1. Limpiar todos los inputs
  formulario
    .querySelectorAll("input:not([type='button']):not([type='submit'])")
    .forEach((input) => {
      if (input.type === "checkbox") {
        input.checked = false;
      } else {
        input.value = "";
      }
    });

  // 2. Restaurar valores por defecto específicos
  document.getElementById("codigo_ingreso").value = "1";
  document.getElementById("estado").value = "EN PROCESO";
  document.getElementById("estado").className =
    "textoCentro estado w100por procesando";

  // 3. Limpiar selects
  formulario.querySelectorAll("select").forEach((select) => {
    select.selectedIndex = 0;
  });

  // 4. Limpiar textareas
  formulario.querySelectorAll("textarea").forEach((textarea) => {
    textarea.value = "";
  });

  // 5. Limpiar tabla de detalles
  const tbody = document.querySelector("#tablaDetalles tbody");
  if (tbody) tbody.innerHTML = "";

  // 6. Resetear campos que deben estar vacíos pero con readonly
  const camposReadonly = [
    "almacen",
    "proyecto",
    "area",
    "solicita",
    "orden",
    "pedido",
    "ruc",
    "razon",
    "concepto",
    "aprueba",
    "tipo",
    "movimiento",
  ];
  camposReadonly.forEach((id) => {
    const campo = document.getElementById(id);
    if (campo) campo.value = "";
  });

  // 7. Limpiar campos hidden específicos
  const hiddenCampos = [
    "codigo_costos",
    "codigo_area",
    "codigo_movimiento",
    "codigo_aprueba",
    "codigo_almacen",
    "codigo_pedido",
    "codigo_orden",
    "codigo_estado",
    "codigo_entidad",
    "codigo_moneda",
    "correo_entidad",
    "codigo_verificacion",
    "codigo_recepcion",
    "usuario_elabora",
    "detalle",
  ];
  hiddenCampos.forEach((id) => {
    const campo = document.getElementById(id);
    if (campo) campo.value = "";
  });

  // 8. Resetear fecha al día actual (opcional)
  const fechaInput = document.getElementById("fecha");
  if (fechaInput) {
    const hoy = new Date().toISOString().split("T")[0];
    fechaInput.value = hoy;
  }

  // 9. Limpiar campo de guía (no es readonly)
  const guiaInput = document.getElementById("guia");
  if (guiaInput) guiaInput.value = "";
}

async function permisosUsuario() {
  const formData = new FormData();
  formData.append("modulo", $("#modulo").val());
  formData.append("user", $("#id_user").val());

  const response = await fetch(RUTA + "panel/permisos", {
    method: "POST",
    body: formData,
  });

  const data = await response.json();
  return data.datos;
}

// ===== GENERAR PAGINADOR =====
  function paginador(ftotal,ftotalPaginas,fpagina){
        const total = ftotal || 0;
        const totalPaginas = ftotalPaginas || Math.ceil(total / 10) || 1;
        const paginaActual = fpagina || 1;

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
          let inicioPaginas = Math.max(
            1,
            paginaActual - Math.floor(maxPaginasMostrar / 2),
          );
          let finPaginas = Math.min(
            totalPaginas,
            inicioPaginas + maxPaginasMostrar - 1,
          );

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
  }

  // Función para realizar paginación después de la carga de datos
  function iniciarPaginadorCargoPlan() {
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

  function procesarArchivos(files) {
    const lista = document.getElementById('listaArchivos');
    console.log('Procesando archivos:', files.length);

    Array.from(files).forEach((file) => {
        // Validar tamaño (10MB)
        if (file.size > 10 * 1024 * 1024) {
            alert(`El archivo "${file.name}" excede los 10MB`);
            return;
        }

        // Validar extensiones
        const ext = file.name.split('.').pop().toLowerCase();
        const permitidas = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];
        if (!permitidas.includes(ext)) {
            alert(`El archivo "${file.name}" no tiene una extensión permitida`);
            return;
        }

        const iconos = {
            pdf: 'fa-file-pdf',
            jpg: 'fa-file-image',
            jpeg: 'fa-file-image',
            png: 'fa-file-image',
            doc: 'fa-file-word',
            docx: 'fa-file-word'
        };
        const colores = {
            pdf: '#ea4335',
            jpg: '#fbbc04',
            jpeg: '#fbbc04',
            png: '#fbbc04',
            doc: '#1a73e8',
            docx: '#1a73e8'
        };

        const tamaño = file.size < 1024 * 1024 ?
            (file.size / 1024).toFixed(1) + ' KB' :
            (file.size / (1024 * 1024)).toFixed(1) + ' MB';

        const item = document.createElement('div');
        item.className = 'archivo-item completado';

        // ===== GUARDAR EL ARCHIVO DIRECTAMENTE EN EL ELEMENTO =====
        item._file = file;  // <--- CLAVE: guardar el archivo en el elemento
        item.dataset.index = archivosSeleccionados.length;

        // También guardar el archivo en el array global (como fallback)
        archivosSeleccionados.push(file);

        item.innerHTML = `
            <i class="fas ${iconos[ext] || 'fa-file'}" style="color:${colores[ext] || '#5f6368'};"></i>
            <span class="archivo-nombre">${file.name}</span>
            <span class="archivo-tamaño">${tamaño}</span>
            <button class="btn-eliminar"><i class="fas fa-trash"></i></button>
        `;

        // Eliminar archivo
        item.querySelector('.btn-eliminar').addEventListener('click', function (e) {
            e.stopPropagation();
            if (confirm(`¿Eliminar "${file.name}"?`)) {
                // Eliminar del array global
                const idx = archivosSeleccionados.indexOf(file);
                if (idx !== -1) {
                    archivosSeleccionados.splice(idx, 1);
                }
                // Reindexar elementos
                document.querySelectorAll('#listaArchivos .archivo-item').forEach((el, i) => {
                    el.dataset.index = i;
                });
                item.remove();
                totalArchivos--;
                actualizarContador();
            }
        });

        lista.appendChild(item);
        totalArchivos++;
        actualizarContador();
        console.log('Archivo agregado con data-index:', item.dataset.index, file.name);
    });
}

// ============ ACTUALIZAR CONTADOR ============
function actualizarContador() {
    $('#contadorArchivos').text(totalArchivos);
}

function verAdjuntos(codigo,modulo,ruta){
        try {
            //const codigo = $("#codigo_ingreso").val();

            if (codigo.length == 0) {
                throw new Error("No se selecciono ningun documento");
            }

            // Mostrar loading
            const lista = document.getElementById('listaSubidos');
            lista.innerHTML = '<div class="mensaje-vacio"><i class="fas fa-spinner fa-spin"></i> Cargando archivos...</div>';

            // Referencias a los elementos del visor
            const wrapModal = document.querySelector('.wrap_modal');
            const panelVisor = document.getElementById('panelVisor');
            const iframeVisor = document.getElementById('iframeVisor');
            const imgVisor = document.getElementById('imgVisor');
            const mensajeNoSoportado = document.getElementById('mensajeNoSoportado');
            const nombreVisor = document.getElementById('nombreVisor');
            const btnDescargarVisor = document.getElementById('btnDescargarVisor');
            let urlActualVisor = '';

            iframeVisor.src = '';

            // Ocultar visor al recargar
            // cerrarVisor();

            // Enviar petición POST al servidor
            $.post(RUTA + "registros/listaAdjuntos",
                {
                    codigo: codigo,
                    modulo: modulo
                },
                function (data) {
                    // Limpiar lista
                    lista.innerHTML = '';

                    if (data.success && data.lista && data.lista.length > 0) {
                        // Mostrar archivos
                        data.lista.forEach(function (archivo) {
                            const item = document.createElement('div');
                            item.className = 'archivo-subido';
                            item.innerHTML = `
                            ${archivo.icono}
                            <span class="nombre" title="${archivo.cdocumento}" data-referencia="${archivo.creferencia}">${archivo.cdocumento}</span>
                            <span class="fecha">${archivo.dfechareg || ''}</span>
                            <button class="btn-eliminar-subido" data-id="${archivo.idreg}" title="Eliminar archivo">
                                <i class="fas fa-trash"></i>
                            </button>`;

                            // 👇 EVENTO CLIC PARA VISUALIZAR EN EL PANEL DERECHO
                            item.addEventListener('click', function (e) {
                                // Ignorar si se hace clic en el botón de eliminar
                                if (e.target.closest('.btn-eliminar-subido')) return;

                                // Marcar selección visual
                                document.querySelectorAll('.archivo-subido').forEach(el => el.classList.remove('seleccionado'));
                                item.classList.add('seleccionado');

                                // Mostrar el visor y expandir modal
                                panelVisor.style.display = 'flex';
                                wrapModal.classList.add('visor-activo');
                                nombreVisor.textContent = archivo.cdocumento;

                                // Limpiar vistas previas
                                iframeVisor.style.display = 'none';
                                imgVisor.style.display = 'none';
                                mensajeNoSoportado.style.display = 'none';
                                iframeVisor.src = '';
                                imgVisor.src = '';

                                // Asumiendo que el backend envía archivo.ruta. Si no, ajusta esta línea.
                                const url = ruta+archivo.creferencia; 
                                urlActualVisor = url;
                                const ext = archivo.creferencia.split('.').pop().toLowerCase();

                                if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) {
                                    imgVisor.style.display = 'block';
                                    imgVisor.src = url;
                                } else if (ext === 'pdf') {
                                    iframeVisor.style.display = 'block';
                                    iframeVisor.src = url;
                                } else {
                                    mensajeNoSoportado.style.display = 'block';
                                }
                            });

                            // Evento para eliminar
                            item.querySelector('.btn-eliminar-subido').addEventListener('click', function (e) {
                                e.stopPropagation();
                                if (confirm('¿Eliminar "' + archivo.cdocumento + '"?')) {
                                    // Si el archivo a eliminar es el que estamos viendo, cerramos el visor
                                    if (item.classList.contains('seleccionado')) {
                                        //cerrarVisor();
                                    }
                                    eliminarArchivoSubido(archivo.idreg, item);
                                }
                            });

                            lista.appendChild(item);
                        });

                        // Actualizar contador
                        $('#contadorAdjuntos').text(data.total);
                    } else {
                        lista.innerHTML = '<div class="mensaje-vacio">No hay archivos adjuntos</div>';
                        $('#contadorAdjuntos').text(0);
                    }

                    // Mostrar el contenedor
                    $("#archivos").fadeIn();

                    // Funciones auxiliares del visor
                    function cerrarVisor() {
                        panelVisor.style.display = 'none';
                        wrapModal.classList.remove('visor-activo');
                        iframeVisor.src = '';
                        imgVisor.src = '';
                        document.querySelectorAll('.archivo-subido').forEach(el => el.classList.remove('seleccionado'));
                    }

                    // Botón para descargar desde el visor
                    btnDescargarVisor.onclick = function() {
                        if(urlActualVisor) window.open(urlActualVisor, '_blank');
                    };
                },
                "json"
            ).fail(function (xhr, status, error) {
                console.error('Error:', error);
                lista.innerHTML = '<div class="mensaje-vacio">Error al cargar archivos</div>';
                mostrarMensaje('Error al cargar archivos', 'mensaje_error');
            });

        } catch (error) {
            mostrarMensaje(error.message, "mensaje_error");
        }

        if($("#codigo_orden").val() == ''){
            mostrarMensaje("Debe grabar el Remito","mensaje_error");
        }

        $("#archivos").fadeIn();
    }
