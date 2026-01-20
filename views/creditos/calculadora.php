<?php
/**
 * Calculadora de Créditos - Simular pagos y calcular intereses
 */

require_once __DIR__ . '/../../config/config.php';

$page_title = 'Calculadora de Créditos - ' . APP_NAME;

include __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-lg-8">
            <h1 class="mb-0">
                <i class="bi bi-calculator-fill" style="color: #0071e3;"></i> Calculadora de Créditos
            </h1>
            <p class="text-muted">Simula el pago de un crédito y visualiza cómo afectan los intereses</p>
        </div>
        <div class="col-lg-4 text-end">
            <a href="<?php echo BASE_URL; ?>/views/creditos/lista.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Volver a Créditos
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Panel de Entrada -->
        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header" style="background: linear-gradient(135deg, #0071e3 0%, #34c759 100%); color: white; border: none;">
                    <h5 class="mb-0"><i class="bi bi-sliders"></i> Parámetros del Crédito</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-600">Monto del Crédito ($) <span style="color: #ff3b30;">*</span></label>
                        <input type="number" class="form-control form-control-lg" id="monto_credito" value="1000000" min="0" step="50000">
                        <small class="text-muted">Capital total del crédito</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-600">Cuota Inicial ($)</label>
                        <input type="number" class="form-control" id="cuota_inicial" value="0" min="0" step="50000">
                        <small class="text-muted">Dinero pagado al inicio</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-600">Tasa de Interés Mensual (%)</label>
                        <input type="number" class="form-control" id="tasa_interes" value="2.5" min="0.1" max="50" step="0.1">
                        <small class="text-muted">Porcentaje mensual a cobrar</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-600">Número de Meses</label>
                        <input type="number" class="form-control" id="numero_meses" value="12" min="1" max="60">
                        <small class="text-muted">Duración del crédito</small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-600">Abono Mensual Extra ($)</label>
                        <input type="number" class="form-control" id="abono_extra" value="0" min="0" step="50000">
                        <small class="text-muted">Dinero adicional cada mes al capital</small>
                    </div>

                    <button class="btn btn-primary w-100 btn-lg" id="btnCalcular">
                        <i class="bi bi-play-circle"></i> Simular Crédito
                    </button>
                </div>
            </div>

            <!-- Resumen Rápido -->
            <div class="card shadow mt-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="bi bi-receipt"></i> Resumen</h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <small class="text-muted">Monto Financiado</small>
                        <div class="fw-bold" id="resumen_financiado">$0</div>
                    </div>
                    <hr>
                    <div class="mb-2">
                        <small class="text-muted">Cuota Mensual Aprox</small>
                        <div class="fw-bold" id="resumen_cuota" style="color: #0071e3;">$0</div>
                    </div>
                    <hr>
                    <div class="mb-2">
                        <small class="text-muted">Total Intereses</small>
                        <div class="fw-bold" id="resumen_intereses" style="color: #ff9500;">$0</div>
                    </div>
                    <hr>
                    <div>
                        <small class="text-muted">Total a Pagar</small>
                        <div class="fw-bold" id="resumen_total" style="color: #34c759; font-size: 1.2rem;">$0</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de Amortización -->
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-table"></i> Tabla de Amortización</h5>
                        <button class="btn btn-sm btn-outline-secondary" id="btnExportCSV">
                            <i class="bi bi-download"></i> Exportar
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover" id="tablaAmortizacion">
                            <thead style="background-color: #f8f9fa;">
                                <tr>
                                    <th class="text-center" style="width: 60px;">Mes</th>
                                    <th class="text-end">Saldo Inicial</th>
                                    <th class="text-end">Interés Mes</th>
                                    <th class="text-end">Capital</th>
                                    <th class="text-end">Abono Extra</th>
                                    <th class="text-end">Pago Total</th>
                                    <th class="text-end">Saldo Final</th>
                                </tr>
                            </thead>
                            <tbody id="tbody_amortizacion">
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        Haz clic en "Simular Crédito" para ver la tabla
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Gráfico -->
            <div class="card shadow mt-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-graph-up"></i> Gráfico de Deuda</h5>
                </div>
                <div class="card-body">
                    <canvas id="graficoDeuda" style="max-height: 250px;"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
let graficoActual = null;

document.getElementById('btnCalcular').addEventListener('click', function() {
    const montoCredito = parseFloat(document.getElementById('monto_credito').value) || 0;
    const cuotaInicial = parseFloat(document.getElementById('cuota_inicial').value) || 0;
    const tasaInteres = parseFloat(document.getElementById('tasa_interes').value) || 2.5;
    const numeroMeses = parseInt(document.getElementById('numero_meses').value) || 12;
    const abonoExtra = parseFloat(document.getElementById('abono_extra').value) || 0;

    if (montoCredito <= 0) {
        Swal.fire('Error', 'El monto del crédito debe ser mayor a 0', 'error');
        return;
    }

    // Calcular amortización
    const montoFinanciado = montoCredito - cuotaInicial;
    const saldoInicial = montoFinanciado;
    
    // Calcular cuota fija usando fórmula de anualidad
    const tasa = tasaInteres / 100;
    const cuotaFija = (montoFinanciado * tasa * Math.pow(1 + tasa, numeroMeses)) / 
                      (Math.pow(1 + tasa, numeroMeses) - 1);

    let filas = [];
    let saldoActual = montoFinanciado;
    let totalIntereses = 0;
    let totalPagado = 0;
    let mesesRestantes = numeroMeses;

    for (let mes = 1; mes <= numeroMeses && saldoActual > 0; mes++) {
        const saldoInicio = saldoActual;
        const interesMes = saldoActual * tasa;
        totalIntereses += interesMes;

        // El capital es cuota fija - intereses
        const capitalMes = Math.min(cuotaFija - interesMes, saldoActual);
        const pagoTotal = interesMes + capitalMes + abonoExtra;
        const nuevoSaldo = Math.max(0, saldoActual - capitalMes - abonoExtra);

        totalPagado += pagoTotal;

        filas.push({
            mes: mes,
            saldo_inicio: saldoInicio,
            interes: interesMes,
            capital: capitalMes,
            abono_extra: abonoExtra,
            pago_total: pagoTotal,
            saldo_fin: nuevoSaldo
        });

        saldoActual = nuevoSaldo;
        if (saldoActual <= 0) break;
    }

    // Actualizar tabla
    generarTabla(filas);

    // Actualizar resumen
    document.getElementById('resumen_financiado').textContent = 
        '$' + montoFinanciado.toLocaleString('es-CO', {maximumFractionDigits: 0});
    document.getElementById('resumen_cuota').textContent = 
        '$' + cuotaFija.toLocaleString('es-CO', {maximumFractionDigits: 0});
    document.getElementById('resumen_intereses').textContent = 
        '$' + totalIntereses.toLocaleString('es-CO', {maximumFractionDigits: 0});
    document.getElementById('resumen_total').textContent = 
        '$' + (cuotaInicial + montoFinanciado + totalIntereses).toLocaleString('es-CO', {maximumFractionDigits: 0});

    // Generar gráfico
    generarGrafico(filas);
});

function generarTabla(filas) {
    const tbody = document.getElementById('tbody_amortizacion');
    tbody.innerHTML = '';

    filas.forEach(fila => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="text-center"><strong>${fila.mes}</strong></td>
            <td class="text-end">$${fila.saldo_inicio.toLocaleString('es-CO', {maximumFractionDigits: 0})}</td>
            <td class="text-end" style="color: #ff9500;"><strong>$${fila.interes.toLocaleString('es-CO', {maximumFractionDigits: 0})}</strong></td>
            <td class="text-end">$${fila.capital.toLocaleString('es-CO', {maximumFractionDigits: 0})}</td>
            <td class="text-end" style="color: #34c759;">${fila.abono_extra > 0 ? '$' + fila.abono_extra.toLocaleString('es-CO', {maximumFractionDigits: 0}) : '-'}</td>
            <td class="text-end"><strong style="color: #0071e3;">$${fila.pago_total.toLocaleString('es-CO', {maximumFractionDigits: 0})}</strong></td>
            <td class="text-end"><strong>$${fila.saldo_fin.toLocaleString('es-CO', {maximumFractionDigits: 0})}</strong></td>
        `;
        tbody.appendChild(tr);
    });
}

function generarGrafico(filas) {
    const ctx = document.getElementById('graficoDeuda').getContext('2d');
    
    if (graficoActual) {
        graficoActual.destroy();
    }

    graficoActual = new Chart(ctx, {
        type: 'line',
        data: {
            labels: filas.map(f => 'Mes ' + f.mes),
            datasets: [
                {
                    label: 'Saldo Pendiente',
                    data: filas.map(f => f.saldo_fin),
                    borderColor: '#0071e3',
                    backgroundColor: 'rgba(0, 113, 227, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#0071e3'
                },
                {
                    label: 'Interés Mensual',
                    data: filas.map(f => f.interes),
                    borderColor: '#ff9500',
                    backgroundColor: 'rgba(255, 149, 0, 0.1)',
                    borderWidth: 2,
                    tension: 0.4,
                    pointRadius: 3,
                    pointBackgroundColor: '#ff9500'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString('es-CO');
                        }
                    }
                }
            }
        }
    });
}

// Exportar a CSV
document.getElementById('btnExportCSV').addEventListener('click', function() {
    const rows = document.querySelectorAll('#tablaAmortizacion tbody tr');
    if (rows.length === 0 || rows[0].textContent.includes('Haz clic')) {
        Swal.fire('Atención', 'Primero debes simular un crédito', 'warning');
        return;
    }

    let csv = 'Mes,Saldo Inicial,Interés,Capital,Abono Extra,Pago Total,Saldo Final\n';
    
    rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        const rowData = Array.from(cells).map(cell => {
            return cell.textContent.trim().replace(/[$,]/g, '');
        });
        csv += rowData.join(',') + '\n';
    });

    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'amortizacion-credito.csv';
    a.click();
});

// Recalcular al cambiar valores
document.querySelectorAll('#monto_credito, #cuota_inicial, #tasa_interes, #numero_meses, #abono_extra').forEach(input => {
    input.addEventListener('change', function() {
        document.getElementById('btnCalcular').click();
    });
});

// Calcular al cargar
window.addEventListener('load', function() {
    document.getElementById('btnCalcular').click();
});
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
