$(function(){
    $("#espera").fadeOut();

    llenarListadoTransportes()
})

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && document.getElementById("vistaAdjuntos").style.display === 'block') {
        fadeOut(document.getElementById("vistaAdjuntos"));
    }
});

document.addEventListener("click",(e)=>{
    if (e.target.id == 'btnConsulta'){
        e.preventDefault();

        llenarListadoTransportes();

        return false;
    }else if(e.target.matches(".pointer *")){
        e.preventDefault();

        const parentRq = e.target.closest('.pointer').dataset.pedido;
        const parentOrd = e.target.closest('.pointer').dataset.orden;

        verAdjuntos(parentRq);

        fadeIn(document.getElementById("vistaAdjuntos"));

        return false;
    }else if (e.target.id == "closeAtach"){
        e.preventDefault();
        fadeOut(document.getElementById("vistaAdjuntos"));

        return false;
    }else if (e.target.matches('.icono_archivo')){
        e.preventDefault();

        let adjunto = e.target.closest('.icono_archivo').getAttribute('href');

        document.getElementById("pdfPreview").setAttribute('src',adjunto);
        
        return false;
    }else if (e.target.id == "reporteExcel"){
        e.preventDefault();

        try {
            // Validación básica de datos
            if (!Array.isArray(datos)) {
                throw new Error("Los datos deben ser un array");
            }

        } catch (error) {
            
        }

        $("#esperar").fadeIn();

        reporteExcel();

        $("#esperar").fadeOut();

        return false;
    }
})

llenarListadoTransportes = async () => {
    try {
        let formData = new FormData();
        formData.append("orden", document.getElementById("ordenSearch").value);
        formData.append("proyecto", document.getElementById("costosSearch").value);
        formData.append("descripcion", document.getElementById("descripSearch").value);
        formData.append("pedido", document.getElementById("nroPedido").value);
        formData.append("anio", document.getElementById("anioSearch").value);

        $("#esperar").fadeIn();

        const response = await fetch(RUTA + "repotransporte/transportes", {
            method: 'POST',
            body: formData
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

        data.datos.forEach(element => {
            const tr = document.createElement("tr");
            
            tr.classList.add("pointer");
            tr.dataset.orden = element.id_regmov;
            tr.dataset.pedido = element.idreg;
            tr.innerHTML = `<td class="textoCentro">${element.orden}</td>
                            <td class="textoCentro">${element.pedido || ''}</td>
                            <td class="textoCentro">${element.cper || ''}</td>
                            <td class="textoCentro">${element.ccodprod || ''}</td>
                            <td class="textoCentro">${element.ccodproy || ''}</td>
                            <td class="pl20px">${element.cobserva || ''}</td>`;

            fragment.appendChild(tr);
        });

        tablaCuerpo.appendChild(fragment);
        $("#esperar").fadeOut();

    } catch (error) {
        //console.error("Error en llenarListado:", error.messagge);
        mostrarMensaje('No se entontraron guias de transporte','mensaje_error');
        $("#esperar").fadeOut();
    }
}

function verAdjuntos(orden){
    try {
        let formData = new FormData();
        formData.append("orden",orden);

        $("#esperar").css({"display":"block"});

        fetch(RUTA + "repotransporte/adjuntos", {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data =>{

            document.getElementById("pdfPreview").setAttribute('src','');
            document.getElementById('listaAdjuntos').innerHTML = "";

            data.datos.forEach(element => {
                const li = document.createElement("li");
                const a = document.createElement('a');
                a.href = 'http://sicalsepcon.net/ibis/public/documentos/pedidos/adjuntos/'+element.creferencia;
                a.target = "pdfPreview";
                a.classList.add('icono_archivo');
                
                a.innerHTML = `<i class="fas fa-file" style="pointer-events:none"></i>
                                <p style="pointer-events:none">${element.cdocumento}</p>`; 

                li.appendChild(a);
                document.getElementById('listaAdjuntos').appendChild(li);
            });
        })
        .catch(error => {
            console.log(error.message);
        })

        $("#esperar").css({"display":"none"});

    } catch (error) {
        console.error("Error en llenarListado:", error.message);
        $("#esperar").css({"display":"none"});
    }
}

async function reporteExcel(){
    try {
        $("#esperar").fadeIn();
        let datos = getElements();

        if (datos.length === 0) throw new Error("No hay datos para generar el reporte");

         // Crear instancia del workbook
        const workbook = new ExcelJS.Workbook();

        // Configuración del libro
        workbook.creator = 'Sical';
        workbook.lastModifiedBy = 'Sical';
        workbook.created = new Date();
        workbook.modified = new Date();

        // Crear hoja de trabajo
        const worksheet = workbook.addWorksheet('Reporte de Guias de Transporte');

        // Configuración de columnas con headers y mapeo a propiedades de los datos
        const columnConfigs = [
            { header: 'Orden', key: 'orden', width: 10 },
            { header: 'Pedido', key: 'Pedido', width: 10 },
            { header: 'Año', key: 'anio', width: 10 },
            { header: 'Codigo', key: 'codigo', width: 15 },
            { header: 'Codigo Costos', key: 'proyecto', width: 15 },
            { header: 'Descripcion', key: 'descripcion', width: 80 }
        ]

        // Configurar columnas con headers
        worksheet.columns = columnConfigs;

        // Configuración del título
        worksheet.mergeCells('A1:F1');
        const titleCell = worksheet.getCell('A1');
        titleCell.value = 'REPORTE DE GUIAS DE TRANSPORTE';
        titleCell.alignment = { 
            horizontal: 'center', 
            vertical: 'middle',
            wrapText: true
        };
        titleCell.font = {
            bold: true,
            size: 14
        };

        // Aplicar estilos a la fila de cabecera (fila 2)
        const headerRow = worksheet.getRow(2);
        headerRow.values = columnConfigs.map(col => col.header);
        headerRow.height = 40; // Reducido de 80 para mejor visualización

        headerRow.eachCell((cell) => {
            cell.font = {
                bold: true,
                color: { argb: 'FFFFFFFF' }
            };
            cell.fill = {
                type: 'pattern',
                pattern: 'solid',
                fgColor: { argb: 'FF0070C0' } // Azul corporativo
            };
            cell.alignment = {
                vertical: 'middle',
                horizontal: 'center',
                wrapText: true
            };
            cell.border = {
                top: { style: 'thin' },
                left: { style: 'thin' },
                bottom: { style: 'thin' },
                right: { style: 'thin' }
            };
        });

        if (datos && datos.length > 0) {
            datos.forEach((dato) =>{
                worksheet.addRow([
                    dato.orden,
                    dato.pedido,
                    dato.anio,
                    dato.codigo,
                    dato.costos,
                    dato.descripcion
                ])
            })
        }

        // Ajustar automáticamente el ancho de las columnas según contenido
        worksheet.columns.forEach(column => {
            let maxLength = 0;
            column.eachCell({ includeEmpty: true }, cell => {
                let columnLength = cell.value ? cell.value.toString().length : 0;
                if (columnLength > maxLength) {
                    maxLength = columnLength;
                }
            });
            column.width = Math.min(Math.max(maxLength + 2, column.width || 0), 70);
        });

        // Generar el archivo
        const buffer = await workbook.xlsx.writeBuffer();
        const blob = new Blob([buffer], { 
            type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" 
        });

        // Descargar archivo
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `reporte_transporte_${new Date().toISOString().slice(0,10)}.xlsx`;
        document.body.appendChild(a);
        a.click();

        // Limpieza
        setTimeout(() => {
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
            $("#esperar").css({"display": "none", "opacity": "0"});
        }, 100);


        $("#esperar").fadeOut();

    } catch (error) {
        mostrarMensaje(error.message,"mensaje_error");
    }
}

// Función para fade in
function fadeIn(element) {
    element.style.display = 'block';
    // Timeout para permitir el cambio de display antes de la transición
    setTimeout(() => {
        element.style.opacity = '1';
    }, 10);
}

// Función para fade out
function fadeOut(element) {
    element.style.opacity = '0';
    // Esperar a que termine la transición antes de ocultar
    setTimeout(() => {
        element.style.display = 'none';
    }, 300); // Debe coincidir con la duración de la transición en CSS (0.3s = 300ms)
}


function getElements() {
    try {
        const cuerpo_tabla = document.getElementById("tablaPrincipalCuerpo");
        if (!cuerpo_tabla) {
            console.error("Table body not found");
            return [];
        }
        
        const filas = cuerpo_tabla.getElementsByTagName("tr");
        const DATOS = [];

        for (let i = 0; i < filas.length; i++) {
            const cells = filas[i].cells;
            
            // Skip rows that don't have enough cells
            if (cells.length < 6) {
                console.warn(`Row ${i} has insufficient cells (${cells.length})`);
                continue;
            }

            const dato = {
                orden: cells[0].textContent.trim(),
                pedido: cells[1].textContent.trim(),
                anio: cells[2].textContent.trim(),
                codigo: cells[3].textContent.trim(),
                costos: cells[4].textContent.trim(),
                descripcion: cells[5].textContent.trim()
            };

            DATOS.push(dato);
        }

        return DATOS;

    } catch (error) {
        console.error("Error extracting table data:", error.message);
        return [];
    }
}