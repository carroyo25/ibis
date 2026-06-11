$(async function () {
  $("#esperar").fadeOut();

  let nextId = 0;

  const upload_file = document.getElementById("upload_file");
  const excel_input = document.getElementById("excelInput");

  upload_file.addEventListener("click", (e) => {
    e.preventDefault();

    excel_input.click();

    return false;
  });

  excelInput.addEventListener("change", (e) => {
    if (e.target.files?.length) handleFile(e.target.files[0]);
  });

  function handleFile(file) {
    if (!file) return;
    if (!file.name.match(/\.(xlsx|xls|xlsm)$/i)) {
      alert("Por favor selecciona un archivo Excel válido (.xlsx o .xls)");
      return;
    }
    const reader = new FileReader();
    reader.onload = (e) => {
      const data = new Uint8Array(e.target.result);
      processExcel(data);
    };
    reader.onerror = () => alert("Error al leer el archivo.");
    reader.readAsArrayBuffer(file);
  }

  function processExcel(dataBinary) {
        try {
            const workbook = XLSX.read(dataBinary, { type: 'array', cellDates: false, defval: "" });
            const firstSheet = workbook.Sheets[workbook.SheetNames[0]];
            const rawRows = XLSX.utils.sheet_to_json(firstSheet, { header: 1, defval: "" });
            
            if (!rawRows || rawRows.length < 2) {
                alert("El archivo no contiene suficientes filas.");
                return false;
            }

            let headerRowIndex = -1;
            for (let i = 0; i < Math.min(rawRows.length, 20); i++) {
                const row = rawRows[i];
                if (row && row.length > 0) {
                    const firstCell = row[0] ? String(row[0]).toUpperCase() : '';
                    if (firstCell.includes('NUMERO') || firstCell.includes('ITEM')) {
                        headerRowIndex = i;
                        break;
                    }
                }
            }
            
            if (headerRowIndex === -1) {
                headerRowIndex = 0;
            }
            
            const items = [];
            for (let i = headerRowIndex + 1; i < rawRows.length; i++) {
                const row = rawRows[i];
                if (!row || row.length === 0) continue;
                
                const numero = row[0] !== undefined && row[0] !== null ? String(row[0]).trim() : "";
                const codigo = row[1] !== undefined && row[1] !== null ? String(row[1]).trim() : "";
                const descripcion = row[2] !== undefined && row[1] !== null ? String(row[2]).trim() : "";
                const oc = row[3] !== undefined && row[3] !== null ? String(row[3]).trim() : "";
                
                if (numero === "" && descripcion === "") continue;

                if (isNotFound(oc)) continue;
                
                items.push({
                    id: nextId++,
                    numero: numero,
                    codigo: codigo,
                    descripcion: descripcion,
                    oc: oc,
                    selected: false
                });
            }
            
            if (items.length === 0) {
                alert("No se encontraron items con OC válido.");
                return false;
            }
            
            allItems = items;
            renderTable();
            return true;
            
        } catch (err) {
            console.error(err);
            alert("Error al procesar el archivo: " + err.message);
            return false;
        }
    }

    function isNotFound(oc) {
        const ocStr = String(oc || '').toUpperCase().trim();
        return ocStr === 'NO ENCONTRADO' || ocStr === 'NOT FOUND' || ocStr === '';
    }

    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/[&<>]/g, function(m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        });
    }

    function limpiarTexto(str) {
        if (!str) return '';
        return String(str).replace(/[\\\/:*?"<>|]/g, '').substring(0, 50);
    }

    function renderTable() {
        if (!allItems.length) {
            tableBody.innerHTML = '<tr><td colspan="5" class="no-data">No hay datos cargados. Sube un archivo Excel.</td></tr>';
            //statsInfo.innerText = '📄 Registros: 0';
            return;
        }

        //statsInfo.innerHTML = `📄 Total: ${allItems.length} items`;

        tableBody.innerHTML = '';
        allItems.forEach(item => {
            const row = document.createElement('tr');
            
            const tdCheck = document.createElement('td');
            tdCheck.className = 'checkbox-col';
            tdCheck.style.textAlign = 'center';
            const chk = document.createElement('input');
            chk.type = 'checkbox';
            chk.checked = item.selected || false;
            chk.addEventListener('change', (e) => {
                item.selected = e.target.checked;
            });
            tdCheck.appendChild(chk);
            
            const tdNumero = document.createElement('td');
            tdNumero.style.textAlign = 'center';
            tdNumero.innerHTML = `<span class="numero-cell">${escapeHtml(String(item.numero))}</span>`;

            const tdCodigo = document.createElement('td');
            tdCodigo.className = 'codigo-cell';
            tdCodigo.innerHTML = escapeHtml(item.codigo) || '—';
            
            const tdDesc = document.createElement('td');
            tdDesc.className = 'descripcion-cell';
            tdDesc.innerHTML = escapeHtml(item.descripcion) || '—';
            
            const tdOc = document.createElement('td');
            tdOc.style.textAlign = 'center';
            tdOc.innerHTML = `<span class="oc-cell">${escapeHtml(item.oc)}</span>`;
            
            const tdPdf = document.createElement('td');
            tdPdf.className = 'pdf-col';
            tdPdf.style.textAlign = 'center';
            const pdfBtn = document.createElement('button');
            pdfBtn.innerHTML = '📄 PDF';
            pdfBtn.className = 'btn-pdf-row';
            pdfBtn.title = `Descargar PDF para Item #${item.numero}`;
            pdfBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                downloadSinglePDF(item);
            });
            tdPdf.appendChild(pdfBtn);
            
            row.appendChild(tdCheck);
            row.appendChild(tdNumero);
            row.appendChild(tdCodigo);
            row.appendChild(tdDesc);
            row.appendChild(tdOc);
            row.appendChild(tdPdf);
            tableBody.appendChild(row);
        });
    }
    
    function downloadSinglePDF(item){
        try {
            const formData = new FormData();
            
            formData.append("codigo",item.codigo);
            formData.append("oc",item.oc);

            fetch(RUTA+'ordendescarga/pdf',{
                method:'POST',
                body:formData
            })
            .then(repsonse => repsonse.json())
            .then(data => {
                // Crear link invisible
                if (data.existe == 1){
                    const link = document.createElement('a');
                    link.href = data.archivo.ruta;
                    link.download = data.archivo.archivo;
                    document.body.appendChild(link);
                    
                    // Forzar clic
                    link.click();
                    
                    // Limpiar
                    document.body.removeChild(link);
                }else{
                    mostrarMensaje("No exste la OC refererida, revise el codigo del item","mensaje_error");
                }
                
            })

        } catch (error) {
            console.error('Error:', error);
        }  
    }

    function downloadMultiplePDF(){
        
    }
});
