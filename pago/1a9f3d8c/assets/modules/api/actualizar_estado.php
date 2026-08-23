<?php
// actualizar_estado.php
// Asumimos que este archivo está en assets/config/ para que 'conexion.php' funcione.

$config = require '../../config/conexion.php'; // Cambié la ruta a 'conexion.php' ya que ambos están en 'assets/config'

$transaction_id = $_REQUEST['id'] ?? null;
$new_estado = $_REQUEST['estado'] ?? null;
$movil = $_POST['movil'] ?? null;

if (!$transaction_id || !is_numeric($new_estado)) {
    // Si faltan datos, redirige a una página de error, pero no intentamos cerrar la ventana.
    die("Error: Se requiere un ID de transacción y un estado numérico.");
}

$db_config = $config['db'];
$driver = $db_config['driver'];
$dsn = ($driver === 'mysql')
    ? "mysql:host={$db_config['host']};dbname={$db_config['dbname']};charset=utf8mb4"
    : "pgsql:host={$db_config['host']};dbname={$db_config['dbname']}";
$message = '';
$alert_type = 'error';

try {
    $pdo = new PDO($dsn, $db_config['user'], $db_config['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Determinar la consulta y los parámetros
    if ($new_estado == 4 && $movil) {
        $sql = "UPDATE a_confirmar SET estado = :estado, movil = :movil WHERE id = :id";
        $params = [':estado' => $new_estado, ':movil' => $movil, ':id' => $transaction_id];
    }
    else {
        $sql = "UPDATE a_confirmar SET estado = :estado WHERE id = :id";
        $params = [':estado' => $new_estado, ':id' => $transaction_id];
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    // ======================= CORRECCIÓN PARA CERRAR LA VENTANA =======================
    // Si la operación fue exitosa, cerramos la ventana del navegador (Telegram)
    $message = "¡Éxito! Estado actualizado. Cerrando ventana...";
    echo "<script>window.close();</script>";
    exit; // Aseguramos que el script termine aquí
// =================================================================================

}
catch (PDOException $e) {
    // Manejo de errores graves de la base de datos (mostrar al administrador el error)
    $message = "Error de base de datos: " . $e->getMessage();
    $alert_type = 'error';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Actualización de Estado</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Estilos de la página de error (solo si el try-catch falla) -->
    <style>
        body { font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #f4f4f4; text-align: center; }
        .alert { padding: 20px; border-radius: 8px; font-size: 18px; max-width: 90%; }
        .alert.error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
    <?php if ($alert_type === 'error'): ?>
        <div class="alert <?php echo $alert_type; ?>">
            <?php echo $message; ?>
        </div>
    <?php
endif; ?>
</body>
</html>
