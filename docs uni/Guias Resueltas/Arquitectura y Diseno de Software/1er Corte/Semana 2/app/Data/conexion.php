<?php
require_once __DIR__ . '/../../../../../../../config/database.php';

try {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->query("SELECT COUNT(*) AS total FROM iphones");
    $iphones = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<span style='color:green;font-weight:bold;'>✅ Capa de Datos conectada exitosamente a goapple_pos</span><br>";
    echo "📱 iPhones en inventario: " . $iphones['total'] . "<br>";
} catch (Exception $e) {
    echo "<span style='color:red;font-weight:bold;'>❌ Error de conexión: " . htmlspecialchars($e->getMessage()) . "</span>";
}
