<?php
/**
 * Factura de Venta (Imprimible)
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Venta.php';
require_once __DIR__ . '/../../models/Configuracion.php';

$id = intval($_GET['id'] ?? 0);
$auto = ($_GET['auto'] ?? '') === '1';
$size = $_GET['size'] ?? 'carta'; // carta | pos
$isPos = $size === 'pos';

$ventaModel = new Venta();
$venta = $ventaModel->obtenerPorId($id);

if (!$venta) {
    setFlashMessage('error', 'Venta no encontrada');
    redirect('/views/ventas/lista.php');
}

$detalle = $ventaModel->obtenerDetalle($id);

$configModel = new Configuracion();
$empresa = [
    'nombre' => $configModel->obtener('empresa_nombre') ?? EMPRESA_NOMBRE,
    'email' => $configModel->obtener('empresa_email') ?? EMPRESA_EMAIL,
    'telefono' => $configModel->obtener('empresa_telefono') ?? EMPRESA_TELEFONO,
    'direccion' => $configModel->obtener('empresa_direccion') ?? EMPRESA_DIRECCION,
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura <?php echo htmlspecialchars($venta['numero_venta']); ?> - <?php echo APP_NAME; ?></title>
    <style>
        :root {
            --gray-900: #111827;
            --gray-700: #374151;
            --gray-600: #4b5563;
            --gray-200: #e5e7eb;
            --primary: #0071e3;
        }
        body {
            font-family: 'Inter', Arial, sans-serif;
            color: var(--gray-900);
            margin: 0;
            padding: <?php echo $isPos ? '8px' : '24px'; ?>;
            background: #fff;
        }
        .invoice {
            max-width: <?php echo $isPos ? '300px' : '800px'; ?>;
            margin: 0 auto;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 24px;
        }
        .brand {
            font-size: 20px;
            font-weight: 700;
        }
        .meta {
            text-align: right;
        }
        .meta .label {
            color: var(--gray-600);
            font-size: 12px;
        }
        .section {
            border-top: 1px solid var(--gray-200);
            padding-top: 16px;
            margin-top: 16px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }
        th, td {
            text-align: left;
            padding: 8px 6px;
            border-bottom: 1px solid var(--gray-200);
            font-size: 13px;
        }
        th {
            color: var(--gray-600);
            font-weight: 600;
        }
        .totals {
            margin-top: 12px;
            display: flex;
            justify-content: flex-end;
        }
        .totals table {
            width: 300px;
        }
        .totals td {
            border: none;
            padding: 6px;
        }
        .totals .total {
            font-size: 16px;
            font-weight: 700;
            color: var(--primary);
        }
        .footer {
            margin-top: 24px;
            color: var(--gray-600);
            font-size: 12px;
            text-align: center;
        }
        @media print {
            body { padding: 0; }
            .print-actions { display: none; }
        }
        @page {
            size: <?php echo $isPos ? '80mm auto' : 'letter'; ?>;
            margin: <?php echo $isPos ? '6mm' : '12mm'; ?>;
        }
        .print-actions {
            text-align: right;
            margin-bottom: 16px;
        }
        .btn {
            padding: 8px 12px;
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
        }
        .btn-outline {
            background: transparent;
            color: var(--primary);
            border: 1px solid var(--primary);
        }
        .print-actions .btn + .btn { margin-left: 8px; }
    </style>
</head>
<body>
    <div class="invoice">
        <div class="print-actions">
            <button class="btn" onclick="window.print()">Imprimir</button>
            <a class="btn btn-outline" href="<?php echo BASE_URL; ?>/views/ventas/factura.php?id=<?php echo $id; ?>&size=pos" >POS</a>
            <a class="btn btn-outline" href="<?php echo BASE_URL; ?>/views/ventas/factura.php?id=<?php echo $id; ?>&size=carta" >Carta</a>
        </div>

        <div class="header">
            <div>
                <div class="brand"><?php echo htmlspecialchars($empresa['nombre']); ?></div>
                <div style="color: var(--gray-600); font-size: 13px;">
                    <?php echo htmlspecialchars($empresa['direccion']); ?><br>
                    <?php echo htmlspecialchars($empresa['telefono']); ?> | <?php echo htmlspecialchars($empresa['email']); ?>
                </div>
            </div>
            <div class="meta">
                <div class="label">Factura</div>
                <div style="font-weight: 700; font-size: 16px;">
                    <?php echo htmlspecialchars($venta['numero_venta']); ?>
                </div>
                <div class="label">Fecha</div>
                <div><?php echo formatearFechaHora($venta['fecha_venta']); ?></div>
            </div>
        </div>

        <div class="section">
            <div style="font-weight: 600; margin-bottom: 6px;">Cliente</div>
            <div style="font-size: 13px; color: var(--gray-700);">
                <?php echo htmlspecialchars($venta['cliente_nombre']); ?>
            </div>
        </div>

        <div class="section">
            <div style="font-weight: 600; margin-bottom: 6px;">Detalle de Productos</div>
            <table>
                <thead>
                    <tr>
                        <th>Modelo</th>
                        <th>Capacidad</th>
                        <th>Color</th>
                        <th>IMEI</th>
                        <th>Precio</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($detalle as $d): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($d['modelo']); ?></td>
                            <td><?php echo htmlspecialchars($d['capacidad']); ?></td>
                            <td><?php echo htmlspecialchars($d['color']); ?></td>
                            <td><?php echo htmlspecialchars($d['imei']); ?></td>
                            <td><?php echo formatearMoneda($d['precio_unitario']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="totals">
            <table>
                <tr>
                    <td>Subtotal</td>
                    <td style="text-align:right;"><?php echo formatearMoneda($venta['subtotal']); ?></td>
                </tr>
                <tr>
                    <td>Descuento</td>
                    <td style="text-align:right;"><?php echo formatearMoneda($venta['descuento']); ?></td>
                </tr>
                <tr>
                    <td class="total">Total</td>
                    <td class="total" style="text-align:right;"><?php echo formatearMoneda($venta['total']); ?></td>
                </tr>
            </table>
        </div>

        <div class="footer">
            Gracias por su compra.
        </div>
    </div>

    <?php if ($auto): ?>
    <script>
        window.addEventListener('load', () => {
            window.print();
        });
    </script>
    <?php endif; ?>
</body>
</html>
