/**
 * Responsive Navigation Script - GoApple POS
 * Maneja el menú móvil y algunas utilidades responsivas
 */

document.addEventListener('DOMContentLoaded', function() {
    // ============================================
    // MENÚ HAMBURGUESA MÓVIL
    // ============================================
    
    const navToggle = document.querySelector('.navbar-toggle');
    const nav = document.querySelector('nav');
    const navLinks = document.querySelectorAll('.nav-link');
    
    if (navToggle) {
        navToggle.addEventListener('click', function() {
            this.classList.toggle('active');
            nav.classList.toggle('active');
            document.body.style.overflow = nav.classList.contains('active') ? 'hidden' : 'auto';
        });
    }
    
    // Cerrar menú al hacer clic en un link
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (navToggle) {
                navToggle.classList.remove('active');
            }
            if (nav) {
                nav.classList.remove('active');
            }
            document.body.style.overflow = 'auto';
        });
    });
    
    // Cerrar menú al hacer clic fuera
    document.addEventListener('click', function(event) {
        if (nav && nav.classList.contains('active')) {
            if (!event.target.closest('nav') && !event.target.closest('.navbar-toggle')) {
                navToggle.classList.remove('active');
                nav.classList.remove('active');
                document.body.style.overflow = 'auto';
            }
        }
    });
    
    // ============================================
    // MODAL RESPONSIVO
    // ============================================
    
    const modals = document.querySelectorAll('.modal');
    
    modals.forEach(modal => {
        // Cerrar con botón X
        const closeBtn = modal.querySelector('.btn-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', function() {
                modal.classList.remove('show');
            });
        }
        
        // Cerrar al hacer clic fuera
        modal.addEventListener('click', function(event) {
            if (event.target === modal) {
                modal.classList.remove('show');
            }
        });
        
        // Cerrar con tecla Escape
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && modal.classList.contains('show')) {
                modal.classList.remove('show');
            }
        });
    });
    
    // Función global para abrir modal
    window.openModal = function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('show');
        }
    };
    
    // Función global para cerrar modal
    window.closeModal = function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('show');
        }
    };
    
    // ============================================
    // BOTONES CON LOADING STATE
    // ============================================
    
    const buttons = document.querySelectorAll('[data-loading]');
    
    buttons.forEach(btn => {
        btn.addEventListener('click', function() {
            if (this.getAttribute('data-loading') === 'true') {
                this.classList.add('loading');
                this.disabled = true;
            }
        });
    });
    
    // Función para resetear estado del botón
    window.resetButtonLoading = function(btnSelector) {
        const btn = document.querySelector(btnSelector);
        if (btn) {
            btn.classList.remove('loading');
            btn.disabled = false;
        }
    };
    
    // ============================================
    // ALTURA RESPONSIVA PARA TEXTAREA
    // ============================================
    
    const textareas = document.querySelectorAll('textarea');
    
    textareas.forEach(textarea => {
        // Ajustar altura automáticamente
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 300) + 'px';
        });
    });
    
    // ============================================
    // TEMA OSCURO (BONUS)
    // ============================================
    
    const darkModeToggle = document.getElementById('darkModeToggle');
    
    if (darkModeToggle) {
        // Cargar preferencia guardada
        const isDarkMode = localStorage.getItem('darkMode') === 'true';
        if (isDarkMode) {
            document.body.classList.add('dark-mode');
            darkModeToggle.checked = true;
        }
        
        // Escuchar cambios
        darkModeToggle.addEventListener('change', function() {
            document.body.classList.toggle('dark-mode');
            localStorage.setItem('darkMode', this.checked);
        });
    }
    
    // ============================================
    // VALIDACIÓN VISUAL DE FORMULARIOS
    // ============================================
    
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        // Marcar campos inválidos en rojo
        const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
        
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                if (!this.value.trim()) {
                    this.style.borderColor = 'var(--color-danger)';
                } else {
                    this.style.borderColor = '';
                }
            });
            
            input.addEventListener('focus', function() {
                this.style.borderColor = '';
            });
        });
    });
    
    // ============================================
    // DEBOUNCE PARA BÚSQUEDAS
    // ============================================
    
    function debounce(func, delay) {
        let timeoutId;
        return function(...args) {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => func(...args), delay);
        };
    }
    
    // Aplicar a campos de búsqueda
    const searchInputs = document.querySelectorAll('[data-search]');
    
    searchInputs.forEach(input => {
        const debouncedSearch = debounce(function() {
            // Aquí va la lógica de búsqueda
            const event = new CustomEvent('search', { detail: input.value });
            input.dispatchEvent(event);
        }, 500);
        
        input.addEventListener('input', debouncedSearch);
    });
    
    // ============================================
    // TOOLTIPS SIMPLES
    // ============================================
    
    const tooltips = document.querySelectorAll('[data-tooltip]');
    
    tooltips.forEach(element => {
        element.addEventListener('mouseenter', function() {
            const tooltipText = this.getAttribute('data-tooltip');
            const tooltip = document.createElement('div');
            tooltip.className = 'tooltip-popup';
            tooltip.textContent = tooltipText;
            tooltip.style.cssText = `
                position: absolute;
                background: rgba(0,0,0,0.8);
                color: white;
                padding: 0.5rem 0.75rem;
                border-radius: 4px;
                font-size: 0.85rem;
                white-space: nowrap;
                z-index: 1000;
                pointer-events: none;
            `;
            document.body.appendChild(tooltip);
            
            const rect = this.getBoundingClientRect();
            tooltip.style.top = (rect.top - tooltip.offsetHeight - 10) + 'px';
            tooltip.style.left = (rect.left + rect.width / 2 - tooltip.offsetWidth / 2) + 'px';
            
            setTimeout(() => tooltip.remove(), 3000);
        });
    });
    
    // ============================================
    // LAZY LOADING DE IMÁGENES
    // ============================================
    
    if ('IntersectionObserver' in window) {
        const images = document.querySelectorAll('img[data-src]');
        
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.getAttribute('data-src');
                    img.removeAttribute('data-src');
                    observer.unobserve(img);
                }
            });
        });
        
        images.forEach(img => imageObserver.observe(img));
    }
    
    // ============================================
    // SOPORTE PARA BOOTSTRAP MODALS
    // ============================================
    
    // Si está usando Bootstrap, crear adaptador para modals
    if (typeof bootstrap !== 'undefined') {
        window.showBootstrapModal = function(modalSelector) {
            const modal = new bootstrap.Modal(document.querySelector(modalSelector));
            modal.show();
        };
        
        window.hideBootstrapModal = function(modalSelector) {
            const modal = bootstrap.Modal.getInstance(document.querySelector(modalSelector));
            if (modal) modal.hide();
        };
    }
});

/**
 * UTILIDADES GLOBALES
 */

// Función para mostrar alertas bonitas
window.showAlert = function(message, type = 'info', duration = 5000) {
    const alertContainer = document.createElement('div');
    alertContainer.className = `alert alert-${type}`;
    alertContainer.innerHTML = `
        <span class="alert-icon">
            ${type === 'success' ? '✓' : type === 'danger' ? '✕' : 'ℹ'}
        </span>
        <span>${message}</span>
        <button class="btn-close">×</button>
    `;
    
    // Insertar al inicio del contenedor principal
    const container = document.querySelector('main') || document.body;
    container.insertBefore(alertContainer, container.firstChild);
    
    // Cerrar con botón
    alertContainer.querySelector('.btn-close').addEventListener('click', function() {
        alertContainer.remove();
    });
    
    // Auto-cerrar
    if (duration > 0) {
        setTimeout(() => {
            if (alertContainer.parentNode) {
                alertContainer.remove();
            }
        }, duration);
    }
};

// Función para formatear moneda
window.formatMoney = function(amount) {
    return new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: 'COP',
        minimumFractionDigits: 0
    }).format(amount);
};

// Función para confirmar acciones
window.confirmAction = function(message) {
    return confirm(message);
};

// Función para copiar al portapapeles
window.copyToClipboard = function(text, message = 'Copiado') {
    navigator.clipboard.writeText(text).then(() => {
        showAlert(message, 'success', 2000);
    }).catch(err => {
        console.error('Error al copiar:', err);
    });
};
