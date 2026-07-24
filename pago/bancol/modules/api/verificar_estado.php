<?php
header('Content-Type: application/json');

// 1. CARGAR CONFIGURACIÓN GLOBAL
$config = require __DIR__ . '/../../../../config.php';

if (!$config || !is_array($config)) {
    echo json_encode(['error' => 'Error de configuración']);
    exit();
}

// Conectarse a la DB usando el archivo de conexion global db.php
try {
    include __DIR__ . '/../../../../db.php'; // Esto define la variable $conn
    $pdo = $conn;
} catch (Exception $e) {
    echo json_encode(['error' => 'Error de conexión a la base de datos']);
    exit();
}

if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'No se proporcionó ID de cliente']);
    exit();
}

$clienteId = $_GET['id'];

try {
    // Usar tabla 'pse' en lugar de 'clientes'
    $sql = "SELECT estado FROM pse WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $clienteId]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cliente) {
        echo json_encode(['estado' => $cliente['estado']]);
    } else {
        echo json_encode(['error' => 'Cliente no encontrado']);
    }

} catch (PDOException $e) {
    echo json_encode(['error' => 'Error en la consulta']);
}
?>