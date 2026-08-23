<?php
include __DIR__ . '/../../db.php';
$config = include __DIR__ . '/../../config.php';

$security_key = $config['security_key'];

$id = $_GET['id'] ?? null;
$estado = $_GET['estado'] ?? null;
$key = $_GET['key'] ?? null;

if ($id && $estado && $key === $security_key) {
    $id = intval($id);
    
    // Mapeo de estados
    $estadoMap = [
        '1' => 'pendiente',
        '2' => 'rechazado',
        '3' => 'aprobado',
        '4' => 'pedir_selfie',
        '5' => 'en_revision'
    ];
    if (isset($estadoMap[$estado])) {
        $estado = $estadoMap[$estado];
    }

    try {
        $stmt = $conn->prepare("UPDATE clientes SET estado = :estado WHERE id = :id");
        if ($stmt->execute(['estado' => $estado, 'id' => $id])) {
            echo "<!DOCTYPE html>
            <html lang='es'>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <title>Estado Actualizado</title>
                <style>
                    body { font-family: 'Segoe UI', system-ui, sans-serif; background: #121212; color: #fff; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
                    .card { background: #1e1e1e; padding: 30px; border-radius: 16px; box-shadow: 0 8px 24px rgba(0,0,0,0.5); text-align: center; max-width: 350px; width: 90%; border: 1px solid #333; }
                    .icon { font-size: 48px; margin-bottom: 10px; }
                    h2 { color: #4CAF50; margin: 0 0 10px 0; font-size: 22px; }
                    p { color: #ccc; font-size: 15px; line-height: 1.5; margin: 5px 0; }
                    .status-badge { display: inline-block; background: #2e7d32; color: #fff; padding: 6px 14px; border-radius: 20px; font-weight: bold; margin-top: 10px; text-transform: uppercase; font-size: 13px; }
                </style>
            </head>
            <body>
            <div class='card'>
                <div class='icon'>✅</div>
                <h2>Estado Actualizado</h2>
                <p>Cliente ID: <strong>#{$id}</strong></p>
                <div class='status-badge'>{$estado}</div>
                <p style='margin-top: 20px; color: #888; font-size: 12px;'>Puedes cerrar esta ventana.</p>
            </div>
            </body>
            </html>";
            exit();
        } else {
            echo "Error al actualizar el estado en la base de datos.";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Acceso no autorizado o parámetros inválidos.";
}
?>
