// Variables globales
let currentFilter = "all";
let datosProximos = [];

// Función principal para obtener datos
function obtenerProximos() {
    // Mostrar loader
    mostrarLoader(true);
    
    let formData = new FormData();
    formData.append("funcion", "obtenerProximos");

    fetch('app.php', {
        method: 'POST',
        body: formData,
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        mostrarLoader(false);
        
        // Verificar si hay error
        if (data.error) {
            console.error('Error del servidor:', data.mensaje || data.error);
            mostrarError(data.mensaje || 'Error al obtener los datos');
            return;
        }
        
        // Verificar si hay datos
        if (!data || data.length === 0) {
            console.log('No se encontraron mantenimientos próximos');
            datosProximos = [];
            renderCards(datosProximos);
            mostrarMensajeSinDatos();
            return;
        }
        
        // Guardar datos y renderizar
        console.log('Mantenimientos próximos encontrados:', data.length);
        datosProximos = data;
        renderCards(datosProximos);
        
    })
    .catch(error => {
        mostrarLoader(false);
        console.error('Error en la petición:', error);
        mostrarError('Error de conexión al servidor');
    });
}

// Funciones auxiliares
function formatearFecha(fecha) {
    if (!fecha || fecha === '0000-00-00') return "-";
    const partes = fecha.split("-");
    if (partes.length !== 3) return fecha;
    return `${partes[2]}/${partes[1]}/${partes[0]}`;
}

function getTipoUrgencia(dias) {
    if (dias === 0) return "urgente";
    if (dias <= 7) return "urgente";
    if (dias <= 15) return "proximo";
    if (dias <= 30) return "normal";
    return "normal";
}

function getBadgeEstado(dias) {
    if (dias === 0)
        return '<span class="badge-estado badge-hoy">🔴 VENCE HOY</span>';
    if (dias <= 7)
        return '<span class="badge-estado badge-urgente">⚠️ URGENTE (7 días)</span>';
    if (dias <= 15)
        return '<span class="badge-estado badge-proximo">🔄 PRÓXIMO (15 días)</span>';
    if (dias <= 30)
        return '<span class="badge-estado badge-normal">✅ NORMAL</span>';
    return '<span class="badge-estado badge-normal">✅ NORMAL</span>';
}

function renderCards(data) {
    const container = document.getElementById("mantenimientosContainer");
    const totalCount = document.getElementById("totalCount");

    if (!container) {
        console.error("No se encontró el contenedor 'mantenimientosContainer'");
        return;
    }

    // Validar datos
    if (!data || data.length === 0) {
        if (totalCount) totalCount.innerText = "0";
        container.innerHTML = `
            <div class="empty-state">
                <span>✅</span>
                <p>No hay mantenimientos próximos</p>
                <p style="font-size: 0.7rem; margin-top: 8px;">Todos los mantenimientos están al día</p>
            </div>
        `;
        return;
    }

    if (totalCount) totalCount.innerText = data.length;

    // Aplicar filtro
    let filteredData = data;
    if (currentFilter === "urgente") {
        filteredData = data.filter(item => getTipoUrgencia(item.dias_diferencia) === "urgente");
    } else if (currentFilter === "proximo") {
        filteredData = data.filter(item => getTipoUrgencia(item.dias_diferencia) === "proximo");
    } else if (currentFilter === "normal") {
        filteredData = data.filter(item => getTipoUrgencia(item.dias_diferencia) === "normal");
    }

    if (filteredData.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <span>📭</span>
                <p>No hay mantenimientos en esta categoría</p>
            </div>
        `;
        return;
    }

    // Renderizar tarjetas
    container.innerHTML = filteredData.map((item) => {
        const tipo = getTipoUrgencia(item.dias_diferencia);
        const dias = item.dias_diferencia;
        const cardId = `card_${item.idreg}`;
        
        // Manejar texto largo
        const descripcionProducto = item.cdesprod ? 
            (item.cdesprod.length > 40 ? item.cdesprod.substring(0, 40) + "..." : item.cdesprod) : 
            "Sin descripción";
        
        const nombreTrabajador = item.nombre || "No asignado";
        const serie = item.cserie || "Sin serie";

        return `
            <div class="mantenimiento-card ${tipo}" id="${cardId}">
                <div class="card-header" onclick="toggleCard('${cardId}')">
                    <div class="serie-info">
                        <div class="serie-name">🔧 ${escapeHtml(serie)}</div>
                        <div class="trabajador-name">👤 ${escapeHtml(nombreTrabajador)}</div>
                        <div class="producto-name">📦 ${escapeHtml(descripcionProducto)}</div>
                    </div>
                    <div class="dias-badge">
                        <div class="dias-number ${dias <= 7 ? "negativo" : "positivo"}">${dias}</div>
                        <div class="dias-label">días</div>
                    </div>
                </div>
                <div class="card-details" id="details-${cardId}" style="display: none;">
                    <div class="details-content">
                        <div class="detail-row">
                            <div class="detail-label">📅 Fecha Mantenimiento:</div>
                            <div class="detail-value">${formatearFecha(item.fmtto)}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">📋 Fecha Entrega:</div>
                            <div class="detail-value">${formatearFecha(item.fentrega)}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">🆔 Documento:</div>
                            <div class="detail-value">${escapeHtml(item.nrodoc || "-")}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">📊 Estado:</div>
                            <div class="detail-value">${getBadgeEstado(dias)}</div>
                        </div>
                        ${item.cobserva ? `
                        <div class="observacion-text">
                            📝 ${escapeHtml(item.cobserva)}
                        </div>
                        ` : ""}
                    </div>
                </div>
            </div>
        `;
    }).join("");
}

// Función para prevenir XSS
function escapeHtml(text) {
    if (!text) return "";
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Función para mostrar/ocultar detalles
function toggleCard(cardId) {
    const details = document.getElementById(`details-${cardId}`);
    const card = document.getElementById(cardId);
    const indicator = card?.querySelector(".toggle-indicator");
    
    if (!details) return;

    if (details.style.display === "none" || details.style.display === "") {
        details.style.display = "block";
        if (indicator) indicator.innerHTML = "▲ Ocultar detalles";
    } else {
        details.style.display = "none";
        if (indicator) indicator.innerHTML = "▼ Ver detalles";
    }
}

// Función para filtrar por tipo
function filtrarPorTipo(tipo) {
    currentFilter = tipo;
    
    // Actualizar estilos de botones
    document.querySelectorAll(".filter-btn").forEach((btn) => {
        btn.classList.remove("active");
        if (btn.getAttribute("data-filter") === tipo) {
            btn.classList.add("active");
        }
    });
    
    renderCards(datosProximos);
}

// Funciones UI
function mostrarLoader(mostrar) {
    const loader = document.getElementById('loader');
    if (loader) {
        loader.style.display = mostrar ? 'flex' : 'none';
    }
}

function mostrarError(mensaje) {
    const errorDiv = document.getElementById('mensaje-error');
    if (errorDiv) {
        errorDiv.textContent = mensaje;
        errorDiv.style.display = 'block';
        setTimeout(() => {
            errorDiv.style.display = 'none';
        }, 5000);
    } else {
        console.error(mensaje);
    }
}

function mostrarMensajeSinDatos() {
    // Ya se maneja en renderCards
    console.log('No hay datos para mostrar');
}

// Función para refrescar datos manualmente
function refrescarDatos() {
    obtenerProximos();
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    console.log("Inicializando aplicación...");
    obtenerProximos();
    
    // Configurar botón de refresco si existe
    const btnRefrescar = document.getElementById('btnRefrescar');
    if (btnRefrescar) {
        btnRefrescar.addEventListener('click', refrescarDatos);
    }
});

// Exponer funciones globalmente
window.toggleCard = toggleCard;
window.filtrarPorTipo = filtrarPorTipo;
window.refrescarDatos = refrescarDatos;