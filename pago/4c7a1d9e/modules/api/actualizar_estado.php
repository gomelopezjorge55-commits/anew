<?php
// Cargar configuración global para conexión DB y firma de seguridad
$config = require __DIR__ . '/../../../../config.php';

if (!$config || !is_array($config)) {
    die("Error de configuración");
}

$security_key = $config['security_key'];

// Verificar que los parámetros estén presentes en la URL y la firma coincida
if (isset($_GET['id'], $_GET['estado'], $_GET['key']) && $_GET['key'] === $security_key) {
    $clienteId = intval($_GET['id']);
    $nuevoEstado = intval($_GET['estado']);

    // Conectarse a la DB usando el archivo de conexion global db.php
    try {
        include __DIR__ . '/../../../../db.php'; // Esto define la variable $conn
        $pdo = $conn;
        
        // Actualizar el estado en la tabla 'pse'
        $sql = "UPDATE pse SET estado = :estado WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':estado' => $nuevoEstado, ':id' => $clienteId]);
    } catch (Exception $e) {
        error_log("DB Connection Error in actualizar_estado.php: " . $e->getMessage());
    }
} else {
    // Si no es válido o no está autorizado, redireccionar al inicio
    header('Location: ../../index.php');
    exit;
}

// Redireccionar a close.html después de la actualización
header('Location: close.html');
exit;
?>