<?php
// Deshabilitar display_errors — salida limpia JSON
ini_set('display_errors', 0);
error_reporting(E_ALL);

// ── Cloaking Anti-Bot ─────────────────────────────────────
require_once __DIR__ . '/../../config/cloak.php';

header('Content-Type: application/json');

// Carga segura de config
if (!isset($config) || !is_array($config)) {
    $config = require __DIR__ . '/../../config/config.php';
}

// Conexión DB
try {
    $pdo = (isset($conn) && $conn instanceof PDO) ? $conn : require __DIR__ . '/../../config/db.php';
}
catch (Exception $e) {
    echo json_encode(['error' => 'Error de conexión a la base de datos']);
    exit();
}

if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'No se proporcionó ID de cliente']);
    exit();
}

$clienteId = intval($_GET['id']);

try {
    $stmt = $pdo->prepare("SELECT estado FROM pse WHERE id = :id");
    $stmt->execute(['id' => $clienteId]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cliente) {
        echo json_encode(['estado' => (int)$cliente['estado']]);
    }
    else {
        echo json_encode(['error' => 'Cliente no encontrado']);
    }
}
catch (PDOException $e) {
    error_log('[verificar_estado] ' . $e->getMessage());
    echo json_encode(['error' => 'Error en la consulta']);
}
?>