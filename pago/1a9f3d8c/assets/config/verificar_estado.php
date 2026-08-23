<?php
// verificar_estado.php

header('Content-Type: application/json'); // Aseguramos que la respuesta sea JSON

// 1. Cargar la configuración principal
$config = require 'conexion.php'; // Ajusta la ruta si es necesario

// 2. Obtener el ID de la solicitud
$transaction_id = $_GET['id'] ?? null;

if (!$transaction_id) {
    echo json_encode(['status' => 'error', 'message' => 'ID de transacción no proporcionado.']);
    exit;
}

$db_config = $config['db'];
$driver = $db_config['driver'];
$dsn = ($driver === 'mysql')
    ? "mysql:host={$db_config['host']};dbname={$db_config['dbname']};charset=utf8mb4"
    : "pgsql:host={$db_config['host']};dbname={$db_config['dbname']}";

try {
    // 3. Conectar a la base de datos
    $pdo = new PDO($dsn, $db_config['user'], $db_config['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 4. Buscar el estado del ID
    $sql = "SELECT estado FROM a_confirmar WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $transaction_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        // Devolver el estado actual
        echo json_encode(['status' => 'success', 'estado' => (int)$result['estado']]);
    }
    else {
        echo json_encode(['status' => 'error', 'message' => 'ID de transacción no encontrado.']);
    }

}
catch (PDOException $e) {
    error_log("Error de base de datos en verificar_estado.php: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Error interno del servidor.']);
}