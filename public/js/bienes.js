$(function () {

    $("#esperar").fadeOut();

    // =============================================
    // VARIABLES GLOBALES
    // =============================================
    const POR_PAGINA = 15;
    let paginaActual = 1;
    let totalRegistros = 0;
    let timerBusqueda = null;
    
    let accion = "n";
    let jerarquiaDatos = {};
    let carritoCodigos = [];
    let bTipo = null, bGrupo = null, bClase = null, bFamilia = null, bUnidad = null;

    // =============================================
    // ARRANQUE
    // =============================================
    consultarTabla(1);
    cargarJerarquia();

    $("#consulta").on("input", function () {
        clearTimeout(timerBusqueda);
        timerBusqueda = setTimeout(() => consultarTabla(1), 350);
    });

    // =============================================
    // 1. CARGAR JERARQUÍA
    // =============================================
    function cargarJerarquia() {
        fetch(RUTA + "bienes/jerarquia", { method: "POST" })
            .then(r => r.json())
            .then(data => {
                jerarquiaDatos = data;
                llenarSelect("tipo_item", data.tipos, "Buscar tipo...");
                llenarSelect("grupo", data.grupos, "Buscar grupo...");
                llenarSelect("unidad", data.unidades, "Buscar unidad...");

                bTipo = new SelectBuscador("#tipo_item", { placeholder: "Buscar tipo..." });
                bGrupo = new SelectBuscador("#grupo", { placeholder: "Buscar grupo..." });
                bUnidad = new SelectBuscador("#unidad", { placeholder: "Buscar unidad..." });
            })
            .catch(err => console.error("Error jerarquía:", err));
    }

    function llenarSelect(id, lista, placeholder) {
        const select = document.getElementById(id);
        let html = `<option value="">${placeholder}</option>`;
        
        if (lista) {
            lista.forEach(item => {
                // Si el código es igual al nombre (o uno de los dos está vacío), mostramos solo uno
                let textoMostrar = "";
                if (item.codigo && item.nombre && item.codigo !== item.nombre) {
                    textoMostrar = `${item.codigo} - ${item.nombre}`;
                } else {
                    textoMostrar = item.nombre || item.codigo || "";
                }

                html += `<option value="${item.id}" data-codigo="${item.codigo}">${textoMostrar}</option>`;
            });
        }
        select.innerHTML = html;
    }

    // =============================================
    // 2. EVENTOS EN CASCADA Y ACTUALIZACIÓN DE HIDDEN
    // =============================================

    // Función auxiliar para guardar el valor en el hidden correspondiente
    function guardarHidden(selectId, hiddenId) {
        const valor = document.getElementById(selectId).value;
        document.getElementById(hiddenId).value = valor;
    }

    // TIPO
    document.getElementById("tipo_item").addEventListener("change", function () {
        guardarHidden("tipo_item", "codigo_tipo");
    });

    // GRUPO
    document.getElementById("grupo").addEventListener("change", function () {
        guardarHidden("grupo", "codigo_grupo");
        
        // ... (aquí va el código que ya tienes para cargar las clases) ...
        const grupoId = this.value;
        const selectClase = document.getElementById("clase");
        const selectFamilia = document.getElementById("familia");
        
        selectClase.innerHTML = '<option value="">Seleccione...</option>';
        selectFamilia.innerHTML = '<option value="">Seleccione...</option>';
        document.getElementById("codigo_clase").value = "";
        document.getElementById("codigo_familia").value = "";

        if (bClase) { bClase.destruir(); bClase = null; }
        if (bFamilia) { bFamilia.destruir(); bFamilia = null; }

        if (!grupoId) return;

        const grupo = jerarquiaDatos.grupos.find(g => String(g.id) === String(grupoId));
        if (grupo && grupo.clases) {
            llenarSelect("clase", grupo.clases, "Seleccione...");
            bClase = new SelectBuscador("#clase", { placeholder: "Buscar clase..." });
        }
    });

    // CLASE
    document.getElementById("clase").addEventListener("change", function () {
        guardarHidden("clase", "codigo_clase");

        const claseId = this.value;
        const grupoId = document.getElementById("grupo").value;
        const selectFamilia = document.getElementById("familia");
        
        selectFamilia.innerHTML = '<option value="">Seleccione...</option>';
        document.getElementById("codigo_familia").value = "";
        if (bFamilia) { bFamilia.destruir(); bFamilia = null; }

        if (!claseId || !grupoId) return;

        const grupo = jerarquiaDatos.grupos.find(g => String(g.id) === String(grupoId));
        const clase = grupo.clases.find(c => String(c.id) === String(claseId));
        
        if (clase && clase.familias) {
            llenarSelect("familia", clase.familias, "Seleccione...");
            bFamilia = new SelectBuscador("#familia", { placeholder: "Buscar familia..." });
        }
    });

    // FAMILIA
    document.getElementById("familia").addEventListener("change", function () {
        guardarHidden("familia", "codigo_familia");
    });

    // UNIDAD (Aquí disparamos la generación del código)
    document.getElementById("unidad").addEventListener("change", function () {
        guardarHidden("unidad", "codigo_unidad");

        const unidadId = this.value;
        const familiaId = document.getElementById("familia").value;
        
        if (!unidadId || !familiaId) { 
            $("#codigo").val(""); 
            return; 
        }

        // Obtenemos el código de catálogo de la familia
        const codigoFamilia = $("#familia option:selected").data("codigo");
        if (!codigoFamilia) return;

        const formData = new FormData();
        formData.append("codigo", codigoFamilia);

        fetch(RUTA + "bienes/codigo", { method: "POST", body: formData })
            .then(r => r.text())
            .then(nuevoCodigo => {
                $("#codigo").val(nuevoCodigo);
            });
    });

    // =============================================
    // 3. LISTAR Y PAGINAR
    // =============================================
    function consultarTabla(page = 1) {
        const tbody = document.getElementById("tbodyBienes");
        const criterio = document.getElementById("consulta").value;

        tbody.innerHTML = `<tr><td colspan="6" style="text-align:center; padding: 30px;"><i class="fas fa-spinner fa-spin" style="font-size: 30px; color: #2563eb;"></i></td></tr>`;


        const formData = new FormData();
        formData.append("criterio", criterio);
        formData.append("page", page);
        formData.append("porPagina", POR_PAGINA);

        fetch(RUTA + "bienes/listaPaginada", { method: "POST", body: formData })
            .then(r => r.json())
            .then(data => {
                if (data.items && data.items.length > 0) {
                    totalRegistros = parseInt(data.total);
                    renderizarTabla(data.items, page);
                    actualizarPaginador(page);
                } else {
                    tbody.innerHTML = `<tr><td colspan="6" style="text-align:center; padding: 30px; color: #999;">No se encontraron resultados</td></tr>`;
                    actualizarPaginador(1);
                }
            })
            .catch(err => {
                tbody.innerHTML = `<tr><td colspan="6" style="text-align:center; padding: 30px; color: #ef4444;">Error al cargar datos</td></tr>`;
            });
    }

    function renderizarTabla(items, page) {
        const tbody = document.getElementById("tbodyBienes");
        let html = "";

        items.forEach((item, index) => {
            const itemNum = ((page - 1) * POR_PAGINA) + index + 1;
            const itemNumStr = String(itemNum).padStart(5, '0');
            const tipoTexto = item.tipo.toUpperCase();
            let badgeClass = tipoTexto === "BIENES" ? "badge-bienes" : "badge-servicios";

            html += `<tr data-id="${item.id_cprod}" class="pointer">
                <td class="textoCentro">${itemNumStr}</td>
                <td class="textoCentro" style="font-weight:600; color:#2563eb;">${item.ccodprod}</td>
                <td class="textoCentro"><span class="badge-tipo ${badgeClass}">${tipoTexto}</span></td>
                <td class="pl20px">${item.cdesprod}</td>
                <td class="textoCentro">${item.cabrevia}</td>
                <td class="textoCentro"><button class="btn-icon" onclick="editarItem(${item.id_cprod})"><i class="fas fa-ellipsis-v"></i></button></td>
            </tr>`;
        });
        tbody.innerHTML = html;
    }

    // =============================================
    // 4. MODAL
    // =============================================
    $("#nuevoRegistro").click(function (e) {
        e.preventDefault();
        accion = "n";
        limpiarFormulario();
        $("#proceso").fadeIn();
    });

    $("#cerrarVentana").click(function (e) {
        e.preventDefault();
        $("#proceso").fadeOut();
    });

    function limpiarFormulario() {
        $("#formProceso")[0].reset();
        $("input[type='hidden']").val("");
        $("#codigo, #descripcion, #nro_parte, #cod_pat").val("");
        
        if(bTipo) bTipo.setValor("");
        if(bGrupo) bGrupo.setValor("");
        if(bUnidad) bUnidad.setValor("");
        if (bClase) { bClase.destruir(); bClase = null; }
        if (bFamilia) { bFamilia.destruir(); bFamilia = null; }
        
        $("#clase, #familia").html('<option value="">Seleccione...</option>');

        // Desbloquear campos
        $("#codigo").prop("readonly", true).css("background", "");
        $("#tipo_item, #grupo, #clase, #familia").prop("disabled", false);
        $(".select-buscador-display").css({ "pointer-events": "auto", "background": "#fff", "opacity": "1" });
    }

        // =============================================
    // LÓGICA PARA GUARDAR Y PASAR AL CARRITO
    // =============================================
    function procesarItem() {
        // 1. Recopilar datos
        let result = {};

        $("#formProceso").serializeArray().forEach(item => {
            result[item.name] = item.value;
        });

        // 2. Validaciones básicas
        if (!result['codigo_tipo']) return mostrarMensaje("Seleccione el tipo", "mensaje_error");
        if (!result['codigo_grupo']) return mostrarMensaje("Seleccione el grupo", "mensaje_error");
        if (!result['codigo_clase']) return mostrarMensaje("Seleccione la clase", "mensaje_error");
        if (!result['codigo_familia']) return mostrarMensaje("Seleccione la familia", "mensaje_error");
        if (!result['descripcion']) return mostrarMensaje("Ingrese un nombre", "mensaje_error");
        if (!result['codigo_unidad']) return mostrarMensaje("Seleccione la unidad", "mensaje_error");

        // 3. Preparar envío al backend
        const esEdicion = (accion === "u");
        const url = esEdicion ? RUTA + "bienes/modificaItem" : RUTA + "bienes/nuevoItem";
        
        const formData = new FormData();
        formData.append("datos", JSON.stringify(result));

        $("#esperar").fadeIn();

        // 4. Enviar al servidor (aquí se verifica si el código se repite)
        fetch(url, { method: "POST", body: formData })
            .then(r => r.json())
            .then(data => {
                $("#esperar").fadeOut();
                
                if (data.respuesta) {
                    mostrarMensaje(data.mensaje, data.clase);

                    if (!esEdicion) {
                        // A) AGREGAR AL CARRITO VISUAL
                        agregarAlCarrito(result['codigo'], result['descripcion']);
                        
                        // B) LIMPIAR SOLO EL NOMBRE (mantener grupo, clase, familia)
                        $("#descripcion").val("").focus();
                        
                        // C) GENERAR EL NUEVO CÓDIGO AUTOMÁTICAMENTE
                        // Tomamos el código de catálogo de la familia seleccionada
                        const codigoFamilia = $("#familia option:selected").data("codigo");
                        if (codigoFamilia) {
                            const formCode = new FormData();
                            formCode.append("codigo", codigoFamilia);
                            
                            fetch(RUTA + "bienes/codigo", { method: "POST", body: formCode })
                                .then(res => res.text())
                                .then(nuevoCodigo => {
                                    $("#codigo").val(nuevoCodigo); // Actualizamos el input
                                });
                        }

                        // D) RECARGAR LA TABLA DE FONDO
                        consultarTabla(1); 
                    } else {
                        $("#proceso").fadeOut();
                        consultarTabla(paginaActual);
                    }
                } else {
                    // Si el backend dice que hay error (ej: código duplicado)
                    mostrarMensaje(data.mensaje, data.clase);
                    $("#descripcion").focus(); // Devolver el foco al nombre
                }
            })
            .catch(err => {
                $("#esperar").fadeOut();
                mostrarMensaje("Error de conexión al guardar", "mensaje_error");
                console.error(err);
            });
    }

    // Evento Click del botón "Grabar Registro"
    $("#grabarItem").click(function (e) {
        e.preventDefault();
        procesarItem();
    });

    function agregarAlCarrito(codigo, descripcion) {
        if (carritoCodigos.some(item => item.codigo === codigo)) return;
        carritoCodigos.push({ codigo, descripcion });
        renderizarCarrito();
    }

    function renderizarCarrito() {
        const tbody = $("#tbodyCarrito");
        if (carritoCodigos.length === 0) {
            tbody.html(`<tr><td colspan="3" style="text-align: center; padding: 15px; color: #999;">Aún no has creado códigos en esta sesión.</td></tr>`);
            return;
        }
        let html = "";
        carritoCodigos.forEach((item, index) => {
            html += `<tr>
                <td style="font-weight: 600; color: #2563eb;">${item.codigo}</td>
                <td>${item.descripcion}</td>
                <td style="text-align: center;"><button type="button" class="btn-eliminar-carrito" data-index="${index}"><i class="fas fa-times"></i></button></td>
            </tr>`;
        });
        tbody.html(html);
    }

    $("#tbodyCarrito").on("click", ".btn-eliminar-carrito", function () {
        carritoCodigos.splice($(this).data("index"), 1);
        renderizarCarrito();
    });

    $("#btnEnviarCorreo").click(function (e) {
        e.preventDefault();
        if (carritoCodigos.length === 0) return mostrarMensaje("No hay códigos para enviar", "mensaje_error");
        if (!confirm("¿Desea enviar esta lista por correo?")) return;

        const formData = new FormData();
        formData.append("codigos", JSON.stringify(carritoCodigos));

        $("#esperar").fadeIn();
        fetch(RUTA + "bienes/enviarCorreo", { method: "POST", body: formData })
            .then(r => r.json())
            .then(data => {
                $("#esperar").fadeOut();
                mostrarMensaje(data.mensaje, data.clase);
                if (data.respuesta) { carritoCodigos = []; renderizarCarrito(); }
            })
            .catch(err => { $("#esperar").fadeOut(); mostrarMensaje("Error al enviar el correo", "mensaje_error"); });
    });

    // =============================================
    // 6. PAGINADOR
    // =============================================
    function actualizarPaginador(pagina) {
        paginaActual = pagina;
        const totalPaginas = Math.ceil(totalRegistros / POR_PAGINA) || 1;
        const inicio = totalRegistros > 0 ? (pagina - 1) * POR_PAGINA + 1 : 0;
        const fin = Math.min(pagina * POR_PAGINA, totalRegistros);

        document.getElementById("infoPaginador").innerHTML = `Mostrando <strong>${inicio}</strong> - <strong>${fin}</strong> de <strong>${totalRegistros}</strong>`;

        let botones = `<button class="page-btn" data-page="${pagina - 1}" ${pagina <= 1 ? "disabled" : ""}><i class="fas fa-chevron-left"></i></button>`;
        let inicioPag = Math.max(1, pagina - 2);
        let finPag = Math.min(totalPaginas, pagina + 2);

        if (inicioPag > 1) {
            botones += `<button class="page-btn" data-page="1">1</button>`;
            if (inicioPag > 2) botones += `<button class="page-btn" disabled>...</button>`;
        }
        for (let i = inicioPag; i <= finPag; i++) {
            botones += `<button class="page-btn ${i === pagina ? "active" : ""}" data-page="${i}">${i}</button>`;
        }
        if (finPag < totalPaginas) {
            if (finPag < totalPaginas - 1) botones += `<button class="page-btn" disabled>...</button>`;
            botones += `<button class="page-btn" data-page="${totalPaginas}">${totalPaginas}</button>`;
        }
        botones += `<button class="page-btn" data-page="${pagina + 1}" ${pagina >= totalPaginas ? "disabled" : ""}><i class="fas fa-chevron-right"></i></button>`;

        document.getElementById("botonesPaginador").innerHTML = botones;

        document.querySelectorAll(".page-btn").forEach(btn => {
            btn.addEventListener("click", function () {
                if (this.disabled) return;
                const page = parseInt(this.dataset.page);
                if (page >= 1 && page <= totalPaginas) consultarTabla(page);
            });
        });
    }

    // =============================================
    // 7. EDITAR ITEM
    // =============================================
    window.editarItem = function (id) {
        accion = "u";
        limpiarFormulario();
        $("#proceso").fadeIn();
        $("#esperar").fadeIn();

        const formData = new FormData();
        formData.append("id", id);

        fetch(RUTA + "bienes/itemsId", { method: "POST", body: formData })
            .then(r => r.json())
            .then(data => {
                $("#esperar").fadeOut();
                if (!data.item || data.item.length === 0) {
                    mostrarMensaje("No se encontró el registro", "mensaje_error");
                    return;
                }

                const item = data.item[0];

                $("#codigo_item").val(item.id_cprod);
                $("#codigo").val(item.ccodprod);
                $("#descripcion").val(item.cdesprod);
                $("#nro_parte").val(item.cnparte || "");
                $("#cod_pat").val(item.cCodPat || "");

                $("#serie").prop("checked", item.flgSerie == 1);
                $("#detraccion").prop("checked", item.flgDetrac == 1);
                $("#actfij").prop("checked", item.flgActFij == 1);

                if(bTipo) bTipo.setValor(item.ntipo);
                if(bGrupo) bGrupo.setValor(item.ngrupo);
                if(bUnidad) bUnidad.setValor(item.nund);

                document.getElementById("grupo").dispatchEvent(new Event("change"));
                
                setTimeout(() => {
                    if(bClase) bClase.setValor(item.nclase);
                    document.getElementById("clase").dispatchEvent(new Event("change"));
                    
                    setTimeout(() => {
                        if(bFamilia) bFamilia.setValor(item.nfam);
                    }, 100);
                }, 100);

                // Bloquear campos en edición
                $("#codigo").prop("readonly", true).css("background", "#f3f4f6");
                $("#tipo_item, #grupo, #clase, #familia").prop("disabled", true);
                $(".select-buscador-display").css({ "pointer-events": "none", "background": "#f3f4f6", "opacity": "0.7" });
            })
            .catch(err => {
                $("#esperar").fadeOut();
                mostrarMensaje("Error al cargar el registro", "mensaje_error");
            });
    };
});