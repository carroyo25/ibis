$(function () {
  $("#esperar").fadeOut();

  $("#btnConsulta").on("click", function (e) {
    e.preventDefault();

    $("#esperar").css("opacity", "1").fadeIn();

    let str = $("#formConsulta").serialize();

    $.post(
      RUTA + "valorizado/consulta",
      str,
      function (data, text, requestXHR) {
        $("#esperar").css("opacity", "0").fadeOut();
        $("#tableValorizado tbody").empty().append(data);
      },
      "text",
    );

    return false;
  });

  $("#btnExporta").click(function (e) {
    e.preventDefault();

    var array = [];
    /* Obtenemos todos los tr del Body*/
    var rowsBody = $("#tableValorizado").find("tbody > tr");
    /* Obtenemos todos los th del Thead */
    var rowsHead = $("#tableValorizado").find("thead > tr > th");

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
      RUTA + "valorizado/exportar",
      { detalles: JSON.stringify(array) },
      function (data, textStatus, jqXHR) {
        window.location.href = data.documento;
      },
      "json",
    );

    return false;
  });

  $("#downFiles").click(function (e) {
    e.preventDefault();

    try {
      if ($("#costosSearch").val() === "-1")
        throw "Seleccione un centro de costos";

      let row = "",
        formData = new FormData();
        formData.append("cc", $("#costosSearch").val());
        formData.append("anio", $("#anioSearch").val());
        formData.append("numero", "");

      $("#esperar").css("opacity", "1").fadeIn();

      fetch(RUTA + "valorizado/adjuntosCarpeta", {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json())
        .then((data) => {
          data.ordenes.forEach((element) => {
            let tipo = element.ntipmov == 37 ? "OC" : "OS";
            row += `<li>
                          <a href="${element.id_regmov}" data-id="${element.id_regmov}" title="${element.cObservacion}"><i class="fas fa-folder" style="color: #FFD43B;"></i>
                            <p>${tipo}: ${element.cnumero}</p>
                          </a>
                    </li>`;

            $(".listaCarpetas ul").empty().append(row);
          });

          $("#esperar").css("opacity", "0").fadeOut();
          $("#vistaCarpetas").fadeIn();
        });
    } catch (error) {
      mostrarMensaje(error, "mensaje_error");
    }

    return false;
  });

  $(".listaCarpetas ul").on("click", "a", function (e) {
    e.preventDefault();

    let formData = new FormData(),
      row = "";
    formData.append("orden", $(this).attr("href"));

    fetch(RUTA + "valorizado/adjuntosArchivos", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        let archivos = data.ordenes;

        if (data.guiasalmacen.length > 0) {
          archivos = data.ordenes.concat(data.guiasalmacen);
        }

        //public/documentos/ordenes/adjuntos/

        archivos.forEach((element) => {
          let ext = getFileExtension3(element.creferencia),
            fileIcon = '<i class="far fa-file"></i>',
            ruta = "";

          switch (ext) {
            case "pdf":
              fileIcon =
                '<i class="fas fa-file-pdf" style="color: #dd5836;"></i>';
              break;
            case "msg":
              fileIcon =
                '<i class="fas fa-envelope-open-text" style="color: #63E6BE;"></i>';
              break;
            case "xls":
              fileIcon =
                '<i class="fas fa-file-excel" style="color: #04b983;"></i>';
              break;
            case "xlsx":
              fileIcon =
                '<i class="fas fa-file-excel" style="color: #04b983;"></i>';
              break;
            case "doc":
              fileIcon =
                '<i class="fas fa-file-word" style="color: #2b72ee;"></i>';
              break;
            case "docx":
              fileIcon =
                '<i class="fas fa-file-word" style="color: #2b72ee;"></i>';
              break;
            case "rar":
              fileIcon =
                '<i class="fas fa-file-archive" style="color: #f051c6;"></i>';
              break;
            case "zip":
              fileIcon =
                '<i class="fas fa-file-archive" style="color: #f051c6;"></i>';
              break;
            case "xls":
              fileIcon =
                '<i class="fas fa-file-excel" style="color: #04b983;"></i>';
              break;
            case "jpg":
              fileIcon =
                '<i class="far fa-images" style="color: #acb1b9;"></i>';
              break;
            case "jpeg":
              fileIcon =
                '<i class="far fa-images" style="color: #acb1b9;"></i>';
              break;
            case "png":
              fileIcon =
                '<i class="far fa-images" style="color: #acb1b9;"></i>';
              break;
            case "gif":
              fileIcon =
                '<i class="far fa-images" style="color: #acb1b9;"></i>';
              break;
          }

          if (element.cmodulo == "ORD") {
            ruta =
              "http://sicalsepcon.net/ibis/public/documentos/ordenes/adjuntos/" +
              element.creferencia;
          } else {
            ruta =
              "http://sicalsepcon.net/ibis/public/documentos/almacen/adjuntos/" +
              element.creferencia;
          }

          row += ` <li>
                            <a href="${ruta}" title="${element.mensaje}">
                                ${fileIcon}
                                <p>${element.documento}</p>
                            </a>
                        </li>`;

          $("#listaAdjuntos").empty().append(row);
        });

        $("#vistaAdjuntos").fadeIn();
      });

    return false;
  });

  $("#listaAdjuntos").on("click", "a", function (e) {
    e.preventDefault();

    $(".ventanaAdjuntos iframe").attr("src", $(this).attr("href"));

    return false;
  });

  $("#closeAtach").click(function (e) {
    e.preventDefault();

    $(".ventanaAdjuntos iframe").attr("src", "");
    $("#vistaCarpetas").fadeOut();

    return false;
  });

  $("#closeAtachFiles").click(function (e) {
    e.preventDefault();

    $(".ventanaAdjuntos iframe").attr("src", "");
    $("#vistaAdjuntos").fadeOut();

    return false;
  });

  $("#ordenSearch").keypress(function (e) {
    if (e.which == 13) {
      let row = "",
        formData = new FormData();
      formData.append("cc", $("#costosSearch").val());
      formData.append("anio", $("#anioSearch").val());
      formData.append("numero", $(this).val());

      $("#esperar").css("opacity", "1").fadeIn();

      fetch(RUTA + "valorizado/adjuntosCarpeta", {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json())
        .then((data) => {
          data.ordenes.forEach((element) => {
            let tipo = element.ntipmov == 37 ? "OC" : "OS";
            row += `<li>
                                <a href="${element.id_regmov}" data-id="${element.id_regmov}" title="${element.cObservacion}">
                                    <i class="fas fa-folder" style="color: #FFD43B;"></i>
                                    <p>${tipo}: ${element.cnumero}</p>
                                </a>
                            </li>`;

            $(".listaCarpetas ul").empty().append(row);
          });

          $("#esperar").css("opacity", "0").fadeOut();
          $("#vistaCarpetas").fadeIn();
        });
    }
  });

  $("downloadFiles").click(function (e) {
    e.preventDefault();

    crearCarpeta();

    return false;
  });
});

function getFileExtension3(filename) {
  return filename.slice(((filename.lastIndexOf(".") - 1) >>> 0) + 2);
}

async function seleccionarCarpeta() {
  try {
    directorioRaiz = await window.showDirectoryPicker();
    document.getElementById("ruta-carpeta").textContent =
      `📁 ${directorioRaiz.name}`;
    actualizarEstado(
      `Carpeta "${directorioRaiz.name}" seleccionada correctamente`,
    );
    //await listarContenido();
  } catch (error) {
    if (error.name !== "AbortError") {
      actualizarEstado("Error al seleccionar carpeta: " + error.message, true);
    }
  }
}

// Función para crear carpeta OPTIMIZADA para muchos registros
async function crearCarpeta() {
  if (!directorioRaiz) {
    actualizarEstado("Primero debes seleccionar una carpeta", true);
    return;
  }

  const formData = new FormData();
  formData.append("cc", $("#costosSearch").val());
  formData.append("anio", $("#anioSearch").val());
  formData.append("numero", "");

  try {
    const response = await fetch(RUTA + "valorizado/adjuntosCarpeta", {
      method: "POST",
      body: formData,
    });
    
    const data = await response.json();
    
    if (!data.ordenes || !Array.isArray(data.ordenes)) {
      throw new Error("Formato de respuesta inválido: no se encontraron órdenes");
    }

    // Configuración de optimización
    const CONFIG = {
      BATCH_SIZE: 5,           // Número de archivos a descargar en paralelo
      MAX_CONCURRENT: 3,       // Número de carpetas a procesar simultáneamente
      RETRY_ATTEMPTS: 2,       // Intentos para archivos fallidos
      TIMEOUT: 30000,          // Timeout por archivo (30 segundos)
      MIN_SIZE: 1024,          // Tamaño mínimo (1KB)
    };

    let archivosCreados = 0;
    let archivosOmitidos = 0;
    let archivosFallidos = [];
    let tiempoInicio = Date.now();

    // Función para procesar un lote de archivos en paralelo
    async function procesarLoteArchivos(archivos, carpetaOrden, batchNumber) {
      const promesas = archivos.map(async (archivo, index) => {
        try {
          const resultado = await procesarArchivoConReintento(archivo, carpetaOrden);
          if (resultado.success) {
            archivosCreados++;
          } else {
            archivosOmitidos++;
            if (resultado.requiereReintento) {
              archivosFallidos.push(archivo);
            }
          }
          
          // Actualizar progreso cada 10 archivos
          if ((archivosCreados + archivosOmitidos) % 10 === 0) {
            const porcentaje = ((archivosCreados + archivosOmitidos) / totalArchivosEsperados * 100).toFixed(1);
            const tiempoTranscurrido = ((Date.now() - tiempoInicio) / 1000).toFixed(1);
            actualizarEstado(
              `Progreso: ${porcentaje}% (${archivosCreados + archivosOmitidos}/${totalArchivosEsperados}) ` +
              `- Creados: ${archivosCreados} - Tiempo: ${tiempoTranscurrido}s`
            );
          }
        } catch (error) {
          console.error(`Error en lote ${batchNumber}, archivo ${index}:`, error);
          archivosOmitidos++;
        }
      });

      // Ejecutar todas las promesas del lote en paralelo
      await Promise.allSettled(promesas);
    }

    // Función para procesar archivo con reintentos
    async function procesarArchivoConReintento(archivo, carpetaOrden, intento = 0) {
      try {
        return await procesarArchivoIndividual(archivo, carpetaOrden);
      } catch (error) {
        if (intento < CONFIG.RETRY_ATTEMPTS) {
          console.log(`Reintentando archivo ${archivo.creferencia} (intento ${intento + 1}/${CONFIG.RETRY_ATTEMPTS})`);
          await new Promise(resolve => setTimeout(resolve, 1000 * (intento + 1))); // Espera progresiva
          return procesarArchivoConReintento(archivo, carpetaOrden, intento + 1);
        }
        return { success: false, error, requiereReintento: false };
      }
    }

    // Función optimizada para procesar archivo individual
    async function procesarArchivoIndividual(archivo, carpetaOrden) {
      const nombreUrl = archivo.creferencia;
      const nombreGuardar = archivo.documento;
      
      if (!nombreUrl) {
        return { success: false, error: "Sin creferencia" };
      }

      // Extraer extensión
      const extensionMatch = nombreUrl.match(/\.[^.]+$/);
      const extensionOriginal = extensionMatch ? extensionMatch[0] : '';

      // Generar nombre válido (versión optimizada)
      let nombreBase = nombreGuardar || nombreUrl.split('/').pop() || 'archivo';
      nombreBase = nombreBase
        .replace(/[<>:"/\\|?*]/g, '_')
        .replace(/\./g, '_')
        .replace(/\s+/g, ' ')
        .trim()
        .substring(0, 100)
        .replace(/\.+$/, '');

      const nombreValido = (nombreBase || 'archivo') + extensionOriginal;

      // Construir URL
      const url = "http://localhost/ibis/public/documentos/ordenes/adjuntos/" + encodeURIComponent(nombreUrl);

      // Verificar si ya existe
      try {
        const archivoExistente = await carpetaOrden.getFileHandle(nombreValido);
        const file = await archivoExistente.getFile();
        if (file.size > CONFIG.MIN_SIZE) {
          return { success: false, error: "Ya existe", requiereReintento: false };
        }
      } catch (error) {
        // No existe, continuar
      }

      // Descargar con timeout
      const controller = new AbortController();
      const timeoutId = setTimeout(() => controller.abort(), CONFIG.TIMEOUT);

      try {
        const fileResponse = await fetch(url, { 
          signal: controller.signal,
          cache: 'no-cache'
        });
        
        clearTimeout(timeoutId);

        if (!fileResponse.ok) {
          return { success: false, error: `HTTP ${fileResponse.status}`, requiereReintento: fileResponse.status >= 500 };
        }

        const blob = await fileResponse.blob();
        
        if (blob.size <= CONFIG.MIN_SIZE) {
          return { success: false, error: "Muy pequeño", requiereReintento: false };
        }

        // Guardar archivo
        const archivoHandle = await carpetaOrden.getFileHandle(nombreValido, { create: true });
        const writable = await archivoHandle.createWritable({ keepExistingData: false });
        await writable.write(blob);
        await writable.close();

        return { success: true, nombre: nombreValido, size: blob.size };

      } catch (fetchError) {
        clearTimeout(timeoutId);
        return { 
          success: false, 
          error: fetchError.name === 'AbortError' ? 'Timeout' : fetchError.message,
          requiereReintento: true 
        };
      }
    }

    // Contar total de archivos primero
    let totalArchivosEsperados = 0;
    const ordenesConArchivos = [];

    for (const orden of data.ordenes) {
      const archivosFormData = new FormData();
      archivosFormData.append('orden', orden.id_regmov);
      
      const archivosResponse = await fetch(RUTA + "valorizado/adjuntosArchivos", {
        method: "POST",
        body: archivosFormData,
      });
      
      const archivosData = await archivosResponse.json();
      if (archivosData.ordenes && Array.isArray(archivosData.ordenes)) {
        totalArchivosEsperados += archivosData.ordenes.length;
        ordenesConArchivos.push({
          ...orden,
          archivos: archivosData.ordenes
        });
      }
    }

    actualizarEstado(`Procesando ${totalArchivosEsperados} archivos en paralelo...`);
    console.log(`🚀 Iniciando procesamiento optimizado de ${totalArchivosEsperados} archivos`);

    // Procesar carpetas en paralelo con límite de concurrencia
    const lotesCarpetas = [];
    for (let i = 0; i < ordenesConArchivos.length; i += CONFIG.MAX_CONCURRENT) {
      lotesCarpetas.push(ordenesConArchivos.slice(i, i + CONFIG.MAX_CONCURRENT));
    }

    for (const loteCarpetas of lotesCarpetas) {
      // Procesar múltiples carpetas en paralelo
      await Promise.all(loteCarpetas.map(async (orden) => {
        const nombreCarpeta = orden.ntipmov == 37 ? 'OC' + orden.cnumero : 'OS' + orden.cnumero;
        
        console.log(`\n📁 Procesando carpeta: ${nombreCarpeta} (${orden.archivos.length} archivos)`);
        
        const carpetaOrden = await directorioRaiz.getDirectoryHandle(nombreCarpeta, { create: true });

        // Dividir archivos en lotes para procesamiento en paralelo
        for (let i = 0; i < orden.archivos.length; i += CONFIG.BATCH_SIZE) {
          const loteArchivos = orden.archivos.slice(i, i + CONFIG.BATCH_SIZE);
          const batchNumber = Math.floor(i / CONFIG.BATCH_SIZE) + 1;
          
          console.log(`   Procesando lote ${batchNumber} de ${Math.ceil(orden.archivos.length / CONFIG.BATCH_SIZE)}`);
          await procesarLoteArchivos(loteArchivos, carpetaOrden, batchNumber);
          
          // Pequeña pausa entre lotes para no saturar
          await new Promise(resolve => setTimeout(resolve, 500));
        }
      }));

      // Pausa entre lotes de carpetas
      await new Promise(resolve => setTimeout(resolve, 1000));
    }

    // Reintentar archivos fallidos
    if (archivosFallidos.length > 0) {
      console.log(`\n🔄 Reintentando ${archivosFallidos.length} archivos fallidos...`);
      actualizarEstado(`Reintentando ${archivosFallidos.length} archivos fallidos...`);
      
      // Procesar fallidos en lotes más pequeños
      for (let i = 0; i < archivosFallidos.length; i += CONFIG.BATCH_SIZE) {
        const loteFallidos = archivosFallidos.slice(i, i + CONFIG.BATCH_SIZE);
        
        await Promise.all(loteFallidos.map(async (archivo) => {
          // Encontrar la carpeta correspondiente
          for (const orden of ordenesConArchivos) {
            if (orden.archivos.includes(archivo)) {
              const nombreCarpeta = orden.ntipmov == 37 ? 'OC' + orden.cnumero : 'OS' + orden.cnumero;
              const carpetaOrden = await directorioRaiz.getDirectoryHandle(nombreCarpeta, { create: true });
              
              const resultado = await procesarArchivoConReintento(archivo, carpetaOrden, 2);
              if (resultado.success) {
                archivosCreados++;
              }
              break;
            }
          }
        }));
      }
    }

    const tiempoTotal = ((Date.now() - tiempoInicio) / 1000).toFixed(1);
    const mensaje = `✅ COMPLETADO en ${tiempoTotal}s: ${archivosCreados} creados, ${archivosOmitidos} omitidos de ${totalArchivosEsperados}`;
    
    console.log('\n' + mensaje);
    if (archivosFallidos.length > 0) {
      console.log(`⚠️ Quedaron ${archivosFallidos.length} archivos sin procesar después de reintentos`);
    }
    
    actualizarEstado(mensaje);
    
  } catch (error) {
    console.error("Error en crearCarpeta:", error);
    actualizarEstado("❌ Error: " + error.message, true);
  }
}

// Función para actualizar mensajes de estado
function actualizarEstado(mensaje, esError = false) {
  const statusDiv = document.getElementById("status-message");
  statusDiv.textContent = mensaje;
  statusDiv.style.backgroundColor = esError ? "#ffebee" : "#e8f5e8";
  statusDiv.style.color = esError ? "#c62828" : "#2e7d32";
}
