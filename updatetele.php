<?php
// Incluir el archivo de conexión a la base de datos
// Incluir el archivo de conexión a la base de datos y configuración
include 'db.php';
$config = include 'config.php';

// Clave de seguridad para validar solicitudes
$security_key = $config['security_key']; // Usar clave de config

// Verificar los parámetros enviados
if (isset($_GET['id'], $_GET['estado'], $_GET['key']) && $_GET['key'] === $security_key) {
    $id = intval($_GET['id']);
    $estado = intval($_GET['estado']);

    // Actualizar el estado en la base de datos
    // Actualizar el estado en la base de datos
    $sql = "UPDATE pse SET estado = :estado WHERE id = :id";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        if ($stmt->execute(['estado' => $estado, 'id' => $id])) {
            // Redirigir a la página de cierre
            header("Location: close.html");
            exit();
        } else {
            // Error de la base de datos
            // En producción, es mejor usar error_log que mostrar detalles con $stmt->errorInfo()
            echo "Error al actualizar el estado.";
        }

        // $stmt->closeCursor(); // Opcional en PDO
    } else {
        echo "Error al preparar la consulta.";
    }
} else {
    // Mensaje para solicitudes inválidas o no autorizadas
    echo "Acceso no autorizado o parámetros inválidos.";
}

// Cerrar la conexión (opcional en PDO, ocurre al final del script)
//$conn = null;
?>