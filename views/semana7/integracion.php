<?php
/* Asignatura: Arquitectura y Diseño de Software | Autor: James y Giorgi Julian Ordoñez | Guía: 7 */
/**
 * Vista de Integración de Web Services
 * Guía Práctica N° 7
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/iPhone.php';

// Título de la página
$titulo_pagina = "Integración Web Services - Semana 7";

// Incluir header
include __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/views/dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Sincronización Web Service</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0 text-gray-800">Capa de Interoperabilidad: Web Services</h1>
            <p class="text-muted">Integración con proveedores externos mediante protocolos REST/JSON.</p>
        </div>
    </div>

    <div class="row">
        <!-- Panel de Control de Sincronización -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4 border-left-primary">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Estado del Servicio Externo</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="mb-3">
                            <i class="fas fa-plug fa-3x text-success"></i>
                        </div>
                        <h5>DummyJSON API</h5>
                        <p class="small text-muted">Status: <span class="badge badge-success">Online</span></p>
                        <p class="small">Endpoint: <code>/products/category/smartphones</code></p>
                    </div>
                    <hr>
                    <button id="btnSync" class="btn btn-primary btn-block btn-lg shadow-sm">
                        <i class="fas fa-sync-alt mr-2"></i> Iniciar Sincronización
                    </button>
                    <div id="syncStatus" class="mt-3 d-none">
                        <div class="progress progress-sm mb-2">
                            <div id="syncProgress" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                        </div>
                        <p id="syncText" class="small text-center italic"></p>
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">Manejo de Errores Documentado</h6>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush small">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Conexión Timeout
                            <span class="badge badge-warning badge-pill">Capturado</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Código 404 (Not Found)
                            <span class="badge badge-danger badge-pill">Gestionado</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Código 500 (Server Error)
                            <span class="badge badge-danger badge-pill">Gestionado</span>
                        </li>
                    </ul>
                    <p class="mt-3 mb-0 x-small text-muted">Asegurado mediante la clase <code>ServiceConnector</code> usando cURL.</p>
                </div>
            </div>
        </div>

        <!-- Resultados de la Integración -->
        <div class="col-xl-8 col-lg-7">
            <div id="resultsCard" class="card shadow mb-4 d-none">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-success">Resultados de Sincronización Real-Time</h6>
                    <span id="httpStatusBadge" class="badge"></span>
                </div>
                <div class="card-body">
                    <div class="row no-gutters align-items-center mb-4">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Items Importados Exitosamente</div>
                            <div id="totalImported" class="h5 mb-0 font-weight-bold text-gray-800">0</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-database fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-sm table-hover" id="syncTable">
                            <thead class="thead-light">
                                <tr>
                                    <th>Modelo</th>
                                    <th>Costo (Simulado)</th>
                                    <th>Venta</th>
                                    <th>Estado BD</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Dinámico -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Guía de la Actividad -->
            <div id="welcomeCard" class="card shadow mb-4">
                <div class="card-body text-center py-5">
                    <i class="fas fa-cloud-download-alt fa-4x text-gray-200 mb-3"></i>
                    <h4>Listo para sincronizar</h4>
                    <p class="text-muted">Presiona el botón para consumir los servicios externos y persistir los datos en MySQL.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnSync = document.getElementById('btnSync');
    const syncStatus = document.getElementById('syncStatus');
    const syncProgress = document.getElementById('syncProgress');
    const syncText = document.getElementById('syncText');
    const resultsCard = document.getElementById('resultsCard');
    const welcomeCard = document.getElementById('welcomeCard');
    const totalImported = document.getElementById('totalImported');
    const httpStatusBadge = document.getElementById('httpStatusBadge');
    const tableBody = document.querySelector('#syncTable tbody');

    btnSync.addEventListener('click', function() {
        // Reset UI
        btnSync.disabled = true;
        syncStatus.classList.remove('d-none');
        welcomeCard.classList.add('d-none');
        syncProgress.style.width = '30%';
        syncText.innerText = 'Conectando con el Web Service...';
        
        // Llamada al controlador
        fetch('<?php echo BASE_URL; ?>/controllers/IntegrationController.php?action=sync')
            .then(response => {
                syncProgress.style.width = '60%';
                syncText.innerText = 'Mapeando datos a objetos de negocio...';
                return response.json();
            })
            .then(data => {
                syncProgress.style.width = '100%';
                syncText.innerText = 'Persistencia en MySQL completada.';
                
                if (data.success) {
                    resultsCard.classList.remove('d-none');
                    totalImported.innerText = data.stats.insertados + data.stats.actualizados;
                    httpStatusBadge.innerText = 'HTTP ' + data.status_code;
                    httpStatusBadge.className = 'badge badge-success';
                    
                    // Mostrar mensaje de éxito
                    alert('Sincronización exitosa: ' + data.stats.insertados + ' nuevos, ' + data.stats.actualizados + ' actualizados.');
                    
                    // Redirigir o recargar después de unos segundos si se desea
                    // window.location.reload();
                } else {
                    alert('Error: ' + data.error);
                    httpStatusBadge.innerText = 'ERROR';
                    httpStatusBadge.className = 'badge badge-danger';
                }
                
                setTimeout(() => {
                    syncStatus.classList.add('d-none');
                    btnSync.disabled = false;
                }, 2000);
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Ocurrió un error en la comunicación con el controlador.');
                btnSync.disabled = false;
            });
    });
});
</script>

<?php
// Incluir footer
include __DIR__ . '/../layouts/footer.php';
?>
