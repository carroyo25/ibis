
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
      mostrarMensaje(error.message, "mensaje_error");
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
              "https://sicalsepcon.net/ibis/public/documentos/ordenes/adjuntos/" +
              element.creferencia;
          } else {
            ruta =
              "https://sicalsepcon.net/ibis/public/documentos/almacen/adjuntos/" +
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

// Función para actualizar estado en la interfaz
function actualizarEstado(mensaje, esError = false) {
    console.log(mensaje);
    const elementoEstado = document.getElementById('estadoProceso');
    if (elementoEstado) {
        elementoEstado.textContent = mensaje;
        elementoEstado.style.color = esError ? 'red' : 'green';
    }
}

// Función para seleccionar carpeta (llamada por botón)
async function seleccionarCarpeta() {
    try {
        actualizarEstado("Seleccionando carpeta...");
        directorioRaiz = await window.showDirectoryPicker();
        
        // Solicitar permisos de lectura/escritura
        const permiso = await directorioRaiz.requestPermission({ mode: 'readwrite' });
        
        if (permiso === 'granted') {
            actualizarEstado("✅ Carpeta seleccionada y permisos concedidos");
            // Habilitar botón de procesar
            const btnProcesar = document.getElementById('btnProcesar');
            if (btnProcesar) btnProcesar.disabled = false;
            return true;
        } else {
            actualizarEstado("❌ No se concedieron permisos para la carpeta", true);
            return false;
        }
    } catch (error) {
        console.error("Error al seleccionar carpeta:", error);
        actualizarEstado("❌ Error al seleccionar carpeta: " + error.message, true);
        return false;
    }
}

// Función para verificar permisos antes de procesar
async function verificarPermisos() {
    if (!directorioRaiz) {
        actualizarEstado("❌ Primero debes seleccionar una carpeta", true);
        return false;
    }

    try {
        const estadoPermiso = await directorioRaiz.queryPermission({ mode: 'readwrite' });
        
        if (estadoPermiso === 'granted') {
            return true;
        }
        
        // Si no tenemos permiso, intentamos solicitarlo
        actualizarEstado("⚠️ Se necesita permiso adicional. Haz clic en 'Conceder permiso'");
        
        // Crear botón para conceder permiso
        const botonPermiso = document.createElement('button');
        botonPermiso.id = 'btnConcederPermiso';
        botonPermiso.textContent = "🔑 Conceder permiso para guardar archivos";
        botonPermiso.style.margin = "10px";
        botonPermiso.style.padding = "10px 20px";
        botonPermiso.style.backgroundColor = "#007bff";
        botonPermiso.style.color = "white";
        botonPermiso.style.border = "none";
        botonPermiso.style.borderRadius = "5px";
        botonPermiso.style.cursor = "pointer";
        
        // Remover botón anterior si existe
        const botonAnterior = document.getElementById('btnConcederPermiso');
        if (botonAnterior) botonAnterior.remove();
        
        // Agregar al DOM
        const contenedor = document.getElementById('controles') || document.body;
        contenedor.appendChild(botonPermiso);
        
        // Esperar clic en el botón
        return new Promise((resolve) => {
            botonPermiso.onclick = async () => {
                try {
                    const nuevoPermiso = await directorioRaiz.requestPermission({ mode: 'readwrite' });
                    botonPermiso.remove();
                    
                    if (nuevoPermiso === 'granted') {
                        actualizarEstado("✅ Permiso concedido");
                        resolve(true);
                    } else {
                        actualizarEstado("❌ Permiso denegado", true);
                        resolve(false);
                    }
                } catch (error) {
                    console.error("Error al solicitar permiso:", error);
                    actualizarEstado("❌ Error al solicitar permiso", true);
                    botonPermiso.remove();
                    resolve(false);
                }
            };
        });
        
    } catch (error) {
        console.error("Error verificando permisos:", error);
        actualizarEstado("❌ Error al verificar permisos", true);
        return false;
    }
}

// Función principal optimizada para crear carpeta
async function crearCarpeta() {
    // Verificar permisos primero
    const tienePermisos = await verificarPermisos();
    if (!tienePermisos) {
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
        
        if ((!data.ordenes || !Array.isArray(data.ordenes))) {
            throw new Error("Formato de respuesta inválido: no se encontraron órdenes");
        }

        // Configuración de optimización
        const CONFIG = {
            BATCH_SIZE: 5,                    // Archivos por lote
            ORDENES_BATCH_SIZE: 20,            // Órdenes a procesar simultáneamente (nuevo)
            MAX_CONCURRENT_ORDENES: 3,         // Máximo de órdenes en paralelo
            RETRY_ATTEMPTS: 2,
            TIMEOUT: 30000,
            MIN_SIZE: 1024,
        };

        let archivosCreados = 0;
        let archivosOmitidos = 0;
        let archivosFallidos = [];
        let totalArchivosGlobal = 0;
        let tiempoInicio = Date.now();

        // Función para procesar archivo individual (se mantiene igual)
        async function procesarArchivoIndividual(archivo, carpetaOrden) {
            const nombreUrl = archivo.creferencia;
            const nombreGuardar = archivo.documento;
            const tipoModulo = archivo.cmodulo;
            
            if (!nombreUrl) {
                return { success: false, error: "Sin creferencia" };
            }

            // Extraer extensión
            const extensionMatch = nombreUrl.match(/\.[^.]+$/);
            const extensionOriginal = extensionMatch ? extensionMatch[0] : '';

            // Generar nombre válido
            let nombreBase = nombreGuardar || nombreUrl.split('/').pop() || 'archivo';
            nombreBase = nombreBase
                .replace(/[<>:"/\\|?*]/g, '_')
                .replace(/\./g, '_')
                .replace(/\s+/g, ' ')
                .trim()
                .substring(0, 100)
                .replace(/\.+$/, '');

            const nombreValido = (nombreBase || 'archivo') + extensionOriginal;

            // Construir URL según el tipo de módulo
            let url = "";
            
            if (tipoModulo === "ORD") {
                url = "http://localhost/ibis/public/documentos/ordenes/adjuntos/" + encodeURIComponent(nombreUrl);
            } else if (tipoModulo === "GA") {
                url = "http://localhost/ibis/public/documentos/almacen/adjuntos/" + encodeURIComponent(nombreUrl);
            } else {
                return { success: false, error: `Tipo desconocido: ${tipoModulo}`, requiereReintento: false };
            }

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

        // Función para procesar archivo con reintento (se mantiene igual)
        async function procesarArchivoConReintento(archivo, carpetaOrden, intento = 0) {
            try {
                return await procesarArchivoIndividual(archivo, carpetaOrden);
            } catch (error) {
                if (intento < CONFIG.RETRY_ATTEMPTS) {
                    console.log(`Reintentando archivo ${archivo.creferencia} (intento ${intento + 1}/${CONFIG.RETRY_ATTEMPTS})`);
                    await new Promise(resolve => setTimeout(resolve, 1000 * (intento + 1)));
                    return procesarArchivoConReintento(archivo, carpetaOrden, intento + 1);
                }
                return { success: false, error, requiereReintento: false };
            }
        }

        // Función para procesar una orden completa
        async function procesarOrden(orden, esReintento = false) {
            try {
                const archivosFormData = new FormData();
                archivosFormData.append('orden', orden.id_regmov);
                
                const archivosResponse = await fetch(RUTA + "valorizado/adjuntosArchivos", {
                    method: "POST",
                    body: archivosFormData,
                });
                
                const archivosData = await archivosResponse.json();

                // Recolectar todos los archivos de la orden (órdenes y guías)
                let archivosOrden = [];
                
                if (archivosData.ordenes && Array.isArray(archivosData.ordenes)) {
                    archivosOrden = archivosOrden.concat(
                        archivosData.ordenes.map(archivo => ({
                            ...archivo,
                            cmodulo: 'ORD'
                        }))
                    );
                }
                
                if (archivosData.guiasalmacen && Array.isArray(archivosData.guiasalmacen)) {
                    archivosOrden = archivosOrden.concat(
                        archivosData.guiasalmacen.map(archivo => ({
                            ...archivo,
                            cmodulo: 'GA'
                        }))
                    );
                }

                if (archivosOrden.length === 0) {
                    console.log(`📁 Orden ${orden.id_regmov} no tiene archivos`);
                    return { orden, archivosProcesados: 0, archivosFallidos: [] };
                }

                // Actualizar contador global si no es reintento
                if (!esReintento) {
                    totalArchivosGlobal += archivosOrden.length;
                }

                const nombreCarpeta = orden.ntipmov == 37 ? 'OC' + orden.cnumero : 'OS' + orden.cnumero;
                console.log(`\n📁 Procesando carpeta: ${nombreCarpeta} (${archivosOrden.length} archivos)`);
                
                const carpetaOrden = await directorioRaiz.getDirectoryHandle(nombreCarpeta, { create: true });

                let ordenArchivosCreados = 0;
                let ordenArchivosOmitidos = 0;
                let ordenArchivosFallidos = [];

                // Procesar archivos en lotes
                for (let j = 0; j < archivosOrden.length; j += CONFIG.BATCH_SIZE) {
                    const loteArchivos = archivosOrden.slice(j, j + CONFIG.BATCH_SIZE);
                    const batchNumber = Math.floor(j / CONFIG.BATCH_SIZE) + 1;
                    
                    console.log(`  Procesando lote ${batchNumber} de ${Math.ceil(archivosOrden.length / CONFIG.BATCH_SIZE)}`);
                    
                    const promesas = loteArchivos.map(async (archivo) => {
                        const resultado = await procesarArchivoConReintento(archivo, carpetaOrden);
                        if (resultado.success) {
                            ordenArchivosCreados++;
                            archivosCreados++;
                        } else {
                            ordenArchivosOmitidos++;
                            archivosOmitidos++;
                            if (resultado.requiereReintento) {
                                ordenArchivosFallidos.push(archivo);
                                archivosFallidos.push(archivo);
                            }
                        }
                        
                        // Actualizar progreso global
                        if ((archivosCreados + archivosOmitidos) % 10 === 0) {
                            const porcentaje = totalArchivosGlobal > 0 
                                ? ((archivosCreados + archivosOmitidos) / totalArchivosGlobal * 100).toFixed(1)
                                : "0.0";
                            const tiempoTranscurrido = ((Date.now() - tiempoInicio) / 1000).toFixed(1);
                            actualizarEstado(
                                `Progreso: ${porcentaje}% (${archivosCreados + archivosOmitidos}/${totalArchivosGlobal}) ` +
                                `- Creados: ${archivosCreados} - Tiempo: ${tiempoTranscurrido}s`
                            );
                        }
                    });

                    await Promise.allSettled(promesas);
                    
                    // Pequeña pausa entre lotes de archivos
                    await new Promise(resolve => setTimeout(resolve, 300));
                }

                console.log(`  ✅ ${nombreCarpeta}: ${ordenArchivosCreados} creados, ${ordenArchivosOmitidos} omitidos`);
                
                return { 
                    orden, 
                    archivosProcesados: ordenArchivosCreados,
                    archivosFallidos: ordenArchivosFallidos 
                };

            } catch (error) {
                console.error(`Error procesando orden ${orden.id_regmov}:`, error);
                return { orden, archivosProcesados: 0, archivosFallidos: [], error: error.message };
            }
        }

        // Función para procesar órdenes en lotes
        async function procesarOrdenesEnLotes(ordenes, tamanoLote) {
            const resultados = [];
            
            for (let i = 0; i < ordenes.length; i += tamanoLote) {
                const lote = ordenes.slice(i, i + tamanoLote);
                const numeroLote = Math.floor(i / tamanoLote) + 1;
                const totalLotes = Math.ceil(ordenes.length / tamanoLote);
                
                console.log(`\n🔵 LOTE ${numeroLote} de ${totalLotes} (${lote.length} órdenes)`);
                actualizarEstado(`Procesando lote ${numeroLote}/${totalLotes} de órdenes...`);
                
                // Procesar las órdenes del lote en paralelo (con límite)
                for (let j = 0; j < lote.length; j += CONFIG.MAX_CONCURRENT_ORDENES) {
                    const subLote = lote.slice(j, j + CONFIG.MAX_CONCURRENT_ORDENES);
                    
                    const resultadosSubLote = await Promise.all(
                        subLote.map(orden => procesarOrden(orden, false))
                    );
                    
                    resultados.push(...resultadosSubLote);
                    
                    // Pequeña pausa entre sub-lotes de órdenes
                    await new Promise(resolve => setTimeout(resolve, 500));
                }
                
                // Actualizar estado después de cada lote de órdenes
                const porcentaje = totalArchivosGlobal > 0 
                    ? ((archivosCreados + archivosOmitidos) / totalArchivosGlobal * 100).toFixed(1)
                    : "0.0";
                const tiempoTranscurrido = ((Date.now() - tiempoInicio) / 1000).toFixed(1);
                actualizarEstado(
                    `Lote ${numeroLote}/${totalLotes} completado. ` +
                    `Progreso global: ${porcentaje}% (${archivosCreados + archivosOmitidos}/${totalArchivosGlobal}) - Tiempo: ${tiempoTranscurrido}s`
                );
                
                // Pausa entre lotes de órdenes
                if (i + tamanoLote < ordenes.length) {
                    console.log(`\n⏳ Pausa de 2 segundos antes del siguiente lote...`);
                    await new Promise(resolve => setTimeout(resolve, 2000));
                }
            }
            
            return resultados;
        }

        // Iniciar procesamiento
        actualizarEstado(`Iniciando procesamiento de ${data.ordenes.length} órdenes...`);
        console.log(`🚀 Iniciando procesamiento de ${data.ordenes.length} órdenes en lotes de ${CONFIG.ORDENES_BATCH_SIZE}`);

        // Procesar órdenes en lotes
        const resultados = await procesarOrdenesEnLotes(data.ordenes, CONFIG.ORDENES_BATCH_SIZE);

        // Reintentar archivos fallidos
        if (archivosFallidos.length > 0) {
            console.log(`\n🔄 Reintentando ${archivosFallidos.length} archivos fallidos...`);
            actualizarEstado(`Reintentando ${archivosFallidos.length} archivos fallidos...`);
            
            // Agrupar archivos fallidos por orden
            const archivosPorOrden = {};
            archivosFallidos.forEach(archivo => {
                for (const resultado of resultados) {
                    if (resultado.archivosFallidos && resultado.archivosFallidos.includes(archivo)) {
                        if (!archivosPorOrden[resultado.orden.id_regmov]) {
                            archivosPorOrden[resultado.orden.id_regmov] = {
                                orden: resultado.orden,
                                archivos: []
                            };
                        }
                        archivosPorOrden[resultado.orden.id_regmov].archivos.push(archivo);
                        break;
                    }
                }
            });

            // Reintentar por orden
            for (const ordenId in archivosPorOrden) {
                const { orden, archivos } = archivosPorOrden[ordenId];
                console.log(`Reintentando ${archivos.length} archivos de la orden ${orden.id_regmov}`);
                
                const nombreCarpeta = orden.ntipmov == 37 ? 'OC' + orden.cnumero : 'OS' + orden.cnumero;
                const carpetaOrden = await directorioRaiz.getDirectoryHandle(nombreCarpeta, { create: true });
                
                for (let i = 0; i < archivos.length; i += CONFIG.BATCH_SIZE) {
                    const loteFallidos = archivos.slice(i, i + CONFIG.BATCH_SIZE);
                    
                    const promesas = loteFallidos.map(async (archivo) => {
                        const resultado = await procesarArchivoConReintento(archivo, carpetaOrden, 2);
                        if (resultado.success) {
                            archivosCreados++;
                        }
                    });
                    
                    await Promise.allSettled(promesas);
                }
            }
        }

        const tiempoTotal = ((Date.now() - tiempoInicio) / 1000).toFixed(1);
        const mensaje = `✅ COMPLETADO en ${tiempoTotal}s: ${archivosCreados} creados, ${archivosOmitidos} omitidos de ${totalArchivosGlobal}`;
        
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
