/**
 * JavaScript principal del sistema GoApple POS
 */

// Función para formatear moneda
function formatearMoneda(valor) {
    return new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: 'COP',
        minimumFractionDigits: 0
    }).format(valor);
}

// Función para formatear fechas
function formatearFecha(fecha) {
    return new Date(fecha).toLocaleDateString('es-CO', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit'
    });
}

// Confirmación de eliminación con SweetAlert2
function confirmarEliminacion(mensaje = '¿Estás seguro de eliminar este elemento?') {
    return Swal.fire({
        title: '¿Estás seguro?',
        text: mensaje,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    });
}

// Mostrar alerta de éxito
function mostrarExito(mensaje, titulo = '¡Éxito!') {
    Swal.fire({
        icon: 'success',
        title: titulo,
        text: mensaje,
        timer: 3000,
        showConfirmButton: false
    });
}

// Mostrar alerta de error
function mostrarError(mensaje, titulo = 'Error') {
    Swal.fire({
        icon: 'error',
        title: titulo,
        text: mensaje
    });
}

// Mostrar loading
function mostrarLoading(mensaje = 'Cargando...') {
    Swal.fire({
        title: mensaje,
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
}

// Ocultar loading
function ocultarLoading() {
    Swal.close();
}

// Calcular crédito con interés compuesto
function calcularCredito(montoTotal, cuotaInicial, tasaInteres, numeroCuotas) {
    const montoFinanciado = montoTotal - cuotaInicial;
    const tasaMensual = tasaInteres / 100;

    // Fórmula de cuota fija con interés compuesto
    const valorCuota = montoFinanciado * 
        (tasaMensual * Math.pow(1 + tasaMensual, numeroCuotas)) / 
        (Math.pow(1 + tasaMensual, numeroCuotas) - 1);

    const totalIntereses = (valorCuota * numeroCuotas) - montoFinanciado;
    const totalAPagar = valorCuota * numeroCuotas;

    return {
        montoFinanciado: Math.round(montoFinanciado),
        valorCuota: Math.round(valorCuota),
        totalIntereses: Math.round(totalIntereses),
        totalAPagar: Math.round(totalAPagar)
    };
}

// Validar IMEI (15 dígitos)
function validarIMEI(imei) {
    return /^\d{15}$/.test(imei);
}

// Validar cédula colombiana
function validarCedula(cedula) {
    return /^\d{7,10}$/.test(cedula);
}

// Validar NIT colombiano
function validarNIT(nit) {
    return /^\d{9,10}-\d$/.test(nit);
}

// Validar teléfono colombiano
function validarTelefono(telefono) {
    return /^(\+57)?3\d{9}$/.test(telefono.replace(/\s/g, ''));
}

// Autocompletar con Select2
$(document).ready(function() {
    // Inicializar tooltips de Bootstrap
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Inicializar popovers de Bootstrap
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    // Auto-cerrar alertas después de 5 segundos
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);

    // Formato de moneda en inputs
    $('.currency-input').on('input', function() {
        let value = $(this).val().replace(/[^0-9]/g, '');
        if (value) {
            $(this).val(formatearMoneda(value));
        }
    });

    // Validación de formularios de Bootstrap
    var forms = document.querySelectorAll('.needs-validation');
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
});

// Imprimir página
function imprimirPagina() {
    window.print();
}

// Exportar tabla a CSV
function exportarTablaCSV(tablaId, nombreArchivo) {
    let csv = [];
    let rows = document.querySelectorAll('#' + tablaId + ' tr');

    for (let i = 0; i < rows.length; i++) {
        let row = [], cols = rows[i].querySelectorAll('td, th');
        for (let j = 0; j < cols.length; j++) {
            row.push(cols[j].innerText);
        }
        csv.push(row.join(','));
    }

    let csvFile = new Blob([csv.join('\n')], { type: 'text/csv' });
    let downloadLink = document.createElement('a');
    downloadLink.download = nombreArchivo + '.csv';
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = 'none';
    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
}

// Debounce para búsquedas
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Copiar al portapapeles
function copiarAlPortapapeles(texto) {
    navigator.clipboard.writeText(texto).then(function() {
        mostrarExito('Copiado al portapapeles', '¡Copiado!');
    }, function() {
        mostrarError('No se pudo copiar');
    });
}

// Validar archivo
function validarArchivo(input, tiposPermitidos, tamanoMaxMB) {
    const archivo = input.files[0];
    
    if (!archivo) {
        return { valido: false, mensaje: 'No se seleccionó ningún archivo' };
    }

    const extension = archivo.name.split('.').pop().toLowerCase();
    if (!tiposPermitidos.includes(extension)) {
        return { 
            valido: false, 
            mensaje: 'Tipo de archivo no permitido. Tipos permitidos: ' + tiposPermitidos.join(', ') 
        };
    }

    const tamanoMB = archivo.size / (1024 * 1024);
    if (tamanoMB > tamanoMaxMB) {
        return { 
            valido: false, 
            mensaje: 'El archivo es demasiado grande. Tamaño máximo: ' + tamanoMaxMB + 'MB' 
        };
    }

    return { valido: true };
}

// Generar reporte PDF (llamada AJAX)
function generarReportePDF(url, datos) {
    mostrarLoading('Generando reporte...');
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(datos)
    })
    .then(response => response.blob())
    .then(blob => {
        ocultarLoading();
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'reporte_' + Date.now() + '.pdf';
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
    })
    .catch(error => {
        ocultarLoading();
        mostrarError('Error al generar el reporte: ' + error.message);
    });
}

// Actualizar notificaciones
function actualizarNotificaciones() {
    fetch('../controllers/NotificacionController.php?action=obtener')
        .then(response => response.json())
        .then(data => {
            if (data.count > 0) {
                document.getElementById('notif-count').textContent = data.count;
                document.getElementById('notif-count').style.display = 'inline';
            } else {
                document.getElementById('notif-count').style.display = 'none';
            }
        })
        .catch(error => console.error('Error al actualizar notificaciones:', error));
}

// Actualizar notificaciones cada 5 minutos
setInterval(actualizarNotificaciones, 300000);

// Sidebar functionality removed - no longer used

// ==================== BÚSQUEDA GLOBAL ====================
document.addEventListener('DOMContentLoaded', function() {
    const globalSearch = document.getElementById('globalSearch');
    const mobileGlobalSearch = document.getElementById('mobileGlobalSearch');
    const searchResults = document.getElementById('searchResults');
    const mobileSearchResults = document.getElementById('mobileSearchResults');
    
    let searchTimeout = null;
    
    // Función para realizar búsqueda
    function realizarBusqueda(query, resultsContainer) {
        if (query.length < 2) {
            resultsContainer.classList.remove('show');
            resultsContainer.innerHTML = '';
            return;
        }
        
        // Simulación de búsqueda (reemplazar con llamada AJAX real)
        clearTimeout(searchTimeout);
        
        // Mostrar placeholder de carga
        resultsContainer.innerHTML = `
            <div style="padding: 2rem 1.5rem; text-align: center;">
                <div class="spinner-border spinner-border-sm text-primary" role="status">
                    <span class="visually-hidden">Buscando...</span>
                </div>
                <p style="margin-top: 1rem; font-size: 0.9rem; color: var(--gray-600);">Buscando resultados...</p>
            </div>
        `;
        resultsContainer.classList.add('show');
        
        searchTimeout = setTimeout(() => {
            // Aquí iría la llamada AJAX a tu API de búsqueda
            // Por ahora, mostramos resultados de ejemplo
            const resultados = buscarResultadosEjemplo(query);
            mostrarResultados(resultados, resultsContainer);
        }, 300);
    }
    
    // Función de ejemplo para simular resultados
    function buscarResultadosEjemplo(query) {
        const ejemplos = [
            { tipo: 'producto', nombre: 'iPhone 13 Pro 256GB', categoria: 'Productos', icono: 'phone', url: '/views/inventario/ver.php?id=1' },
            { tipo: 'producto', nombre: 'iPhone 14 128GB', categoria: 'Productos', icono: 'phone', url: '/views/inventario/ver.php?id=2' },
            { tipo: 'cliente', nombre: 'Juan Pérez', categoria: 'Clientes', icono: 'person', url: '/views/clientes/ver.php?id=1' },
            { tipo: 'cliente', nombre: 'María García', categoria: 'Clientes', icono: 'person', url: '/views/clientes/ver.php?id=2' },
            { tipo: 'venta', nombre: 'Venta #00123', categoria: 'Ventas', icono: 'cart', url: '/views/ventas/detalle.php?id=123' },
        ];
        
        return ejemplos.filter(item => 
            item.nombre.toLowerCase().includes(query.toLowerCase())
        );
    }
    
    // Mostrar resultados en el contenedor
    function mostrarResultados(resultados, container) {
        if (resultados.length === 0) {
            container.innerHTML = `
                <div style="padding: 1.5rem; text-align: center;">
                    <i class="bi bi-search" style="font-size: 2rem; color: var(--gray-400);"></i>
                    <p style="margin-top: 0.5rem; color: var(--gray-600); font-size: 0.9rem;">No se encontraron resultados</p>
                </div>
            `;
        } else {
            let html = '<div style="padding: 0.5rem;">';
            
            // Agrupar por categoría
            const grouped = {};
            resultados.forEach(item => {
                if (!grouped[item.categoria]) {
                    grouped[item.categoria] = [];
                }
                grouped[item.categoria].push(item);
            });
            
            // Mostrar resultados agrupados
            Object.keys(grouped).forEach(categoria => {
                html += `
                    <div style="padding: 0.5rem 0.75rem;">
                        <h6 style="color: var(--gray-600); font-size: 0.75rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px; margin-bottom: 0.5rem;">${categoria}</h6>
                    </div>
                `;
                
                grouped[categoria].forEach(item => {
                    const iconColor = item.tipo === 'producto' ? 'text-primary' : 
                                    item.tipo === 'cliente' ? 'text-success' : 'text-info';
                    html += `
                        <a href="${item.url}" class="search-result-item d-flex align-items-center gap-2" style="text-decoration: none; color: inherit; display: block;">
                            <div class="flex-shrink-0">
                                <div class="rounded-circle bg-${item.tipo === 'producto' ? 'primary' : item.tipo === 'cliente' ? 'success' : 'info'} bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                    <i class="bi bi-${item.icono} ${iconColor}"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div style="font-weight: 600; font-size: 0.9rem; color: var(--gray-900);">${item.nombre}</div>
                                <div style="font-size: 0.75rem; color: var(--gray-600);">${item.categoria}</div>
                            </div>
                        </a>
                    `;
                });
            });
            
            html += '</div>';
            container.innerHTML = html;
        }
        
        container.classList.add('show');
    }
    
    // Event listeners para búsqueda (Desktop)
    if (globalSearch) {
        globalSearch.addEventListener('input', function(e) {
            realizarBusqueda(e.target.value, searchResults);
        });
        
        globalSearch.addEventListener('focus', function(e) {
            // Mostrar resultados si hay valor
            if (e.target.value.length >= 2) {
                searchResults.classList.add('show');
            } else {
                // Mostrar placeholder si está vacío
                searchResults.innerHTML = `
                    <div style="padding: 2rem 1.5rem; text-align: center; color: var(--gray-500);">
                        <i class="bi bi-search" style="font-size: 2rem; color: var(--gray-400); margin-bottom: 0.5rem;"></i>
                        <p style="margin: 0.5rem 0; font-size: 0.9rem;">Escribe al menos 2 caracteres para buscar</p>
                    </div>
                `;
                searchResults.classList.add('show');
            }
        });
        
        // Mostrar resultados al escribir sin necesidad de tener que hacer focus
        globalSearch.addEventListener('keydown', function(e) {
            if (searchResults && searchResults.innerHTML && e.target.value.length >= 2) {
                setTimeout(() => {
                    searchResults.classList.add('show');
                }, 100);
            }
        });
        
        // Cerrar resultados al presionar Escape
        globalSearch.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                searchResults.classList.remove('show');
            }
        });
        
        // Cerrar resultados al hacer click fuera
        document.addEventListener('click', function(e) {
            const searchContainer = globalSearch.closest('.header-search');
            if (searchContainer && !searchContainer.contains(e.target)) {
                searchResults.classList.remove('show');
            }
        });
    }
    
    // Event listeners para búsqueda (Mobile)
    if (mobileGlobalSearch) {
        mobileGlobalSearch.addEventListener('input', function(e) {
            realizarBusqueda(e.target.value, mobileSearchResults);
        });
        
        // Cerrar modal al seleccionar un resultado
        const mobileSearchModal = document.getElementById('mobileSearchModal');
        if (mobileSearchModal) {
            mobileSearchModal.addEventListener('click', function(e) {
                if (e.target.classList.contains('search-result-item')) {
                    const modal = bootstrap.Modal.getInstance(mobileSearchModal);
                    if (modal) {
                        modal.hide();
                    }
                }
            });
        }
    }
});
