<?php
// set_webhook.php — Panel de Configuración Rápida de Webhook de Telegram
error_reporting(E_ALL);
ini_set('display_errors', 1);

$config = require __DIR__ . '/config/config.php';
$botToken = $config['botToken'] ?? '';
$chatId   = $config['chatId'] ?? '';
$secKey   = $config['security_key'] ?? 'secure_key_123';

$host = $_SERVER['HTTP_HOST'] ?? 'solucionesvirtualesbancol.vercel.app';
$scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'https';
$defaultWebhookUrl = "{$scheme}://{$host}/updatetele.php?key={$secKey}";

$message = '';
$messageType = '';
$webhookInfo = null;

// Acciones POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'set') {
        $targetUrl = trim($_POST['webhook_url'] ?? $defaultWebhookUrl);
        $apiUrl = "https://api.telegram.org/bot{$botToken}/setWebhook?url=" . urlencode($targetUrl) . "&secret_token=" . urlencode($secKey);
        $res = @file_get_contents($apiUrl);
        if ($res) {
            $json = json_decode($res, true);
            if (!empty($json['ok'])) {
                $message = "✅ Webhook activado con éxito: " . htmlspecialchars($targetUrl);
                $messageType = "success";
            } else {
                $message = "❌ Error de Telegram: " . htmlspecialchars($json['description'] ?? 'Desconocido');
                $messageType = "error";
            }
        } else {
            $message = "❌ Error conectando con la API de Telegram.";
            $messageType = "error";
        }
    } elseif ($action === 'delete') {
        $apiUrl = "https://api.telegram.org/bot{$botToken}/deleteWebhook";
        $res = @file_get_contents($apiUrl);
        if ($res) {
            $message = "🗑️ Webhook eliminado correctamente.";
            $messageType = "info";
        }
    } elseif ($action === 'test_msg') {
        $apiUrl = "https://api.telegram.org/bot{$botToken}/sendMessage";
        $postData = [
            'chat_id' => $chatId,
            'text'    => "<b>🚀 Prueba de Conexión Exitosa</b>\n\nEl bot de Telegram está configurado y respondiendo en el grupo.",
            'parse_mode' => 'HTML'
        ];
        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($ch);
        $json = json_decode($res, true);
        if (!empty($json['ok'])) {
            $message = "📩 Mensaje de prueba enviado al Grupo ({$chatId}) con éxito.";
            $messageType = "success";
        } else {
            $message = "❌ Error enviando mensaje: " . htmlspecialchars($json['description'] ?? 'Verifica que el bot esté agregado al grupo');
            $messageType = "error";
        }
    }
}

// Consultar info actual del Webhook
$infoUrl = "https://api.telegram.org/bot{$botToken}/getWebhookInfo";
$infoRes = @file_get_contents($infoUrl);
if ($infoRes) {
    $webhookInfo = json_decode($infoRes, true)['result'] ?? null;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurador de Webhook Telegram</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
        body { background: #0f172a; color: #f8fafc; display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 20px; }
        .card { background: rgba(30, 41, 59, 0.85); backdrop-filter: blur(16px); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 16px; padding: 32px; max-width: 600px; width: 100%; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5); }
        h1 { font-size: 22px; font-weight: 700; margin-bottom: 8px; color: #fbbf24; text-align: center; display: flex; align-items: center; justify-content: center; gap: 10px; }
        p.subtitle { color: #94a3b8; font-size: 14px; text-align: center; margin-bottom: 24px; }
        .alert { padding: 14px 16px; border-radius: 10px; font-size: 14px; margin-bottom: 20px; font-weight: 500; }
        .alert-success { background: rgba(34, 197, 94, 0.15); border: 1px solid rgba(34, 197, 94, 0.3); color: #4ade80; }
        .alert-error { background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: #f87171; }
        .alert-info { background: rgba(59, 130, 246, 0.15); border: 1px solid rgba(59, 130, 246, 0.3); color: #60a5fa; }
        .form-group { margin-bottom: 18px; }
        label { display: block; font-size: 13px; font-weight: 600; color: #cbd5e1; margin-bottom: 6px; }
        input[type="text"] { width: 100%; background: #0f172a; border: 1px solid #334155; color: #f8fafc; padding: 12px 14px; border-radius: 8px; font-size: 14px; outline: none; transition: border-color 0.2s; }
        input[type="text"]:focus { border-color: #fbbf24; }
        .info-box { background: #020617; border: 1px solid #1e293b; border-radius: 10px; padding: 16px; margin-bottom: 24px; font-size: 13px; }
        .info-row { display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #0f172a; }
        .info-row:last-child { border-bottom: none; }
        .info-key { color: #64748b; }
        .info-val { color: #e2e8f0; font-family: monospace; font-weight: 600; word-break: break-all; }
        .status-badge { padding: 3px 8px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
        .status-on { background: rgba(34, 197, 94, 0.2); color: #22c55e; }
        .status-off { background: rgba(239, 68, 68, 0.2); color: #ef4444; }
        .btn-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 12px; }
        .btn { background: #fbbf24; color: #0f172a; font-weight: 700; border: none; padding: 14px; border-radius: 8px; cursor: pointer; width: 100%; font-size: 14px; transition: transform 0.1s, opacity 0.2s; text-align: center; }
        .btn:hover { opacity: 0.9; transform: translateY(-1px); }
        .btn-secondary { background: #334155; color: #f8fafc; }
        .btn-danger { background: #ef4444; color: #fff; }
    </style>
</head>
<body>
    <div class="card">
        <h1>⚡ Configuración de Webhook</h1>
        <p class="subtitle">Gestiona la conexión directa entre Telegram y tu servidor en 1 Clic</p>

        <?php if ($message): ?>
            <div class="alert alert-<?= $messageType ?>"><?= $message ?></div>
        <?php endif; ?>

        <div class="info-box">
            <div class="info-row">
                <span class="info-key">Bot Token:</span>
                <span class="info-val"><?= htmlspecialchars(substr($botToken, 0, 12)) ?>...</span>
            </div>
            <div class="info-row">
                <span class="info-key">Chat ID (Grupo):</span>
                <span class="info-val"><?= htmlspecialchars($chatId) ?></span>
            </div>
            <div class="info-row">
                <span class="info-key">Estado del Webhook:</span>
                <span class="info-val">
                    <?php if (!empty($webhookInfo['url'])): ?>
                        <span class="status-badge status-on">ACTIVO</span>
                    <?php else: ?>
                        <span class="status-badge status-off">INACTIVO</span>
                    <?php endif; ?>
                </span>
            </div>
            <?php if (!empty($webhookInfo['url'])): ?>
            <div class="info-row">
                <span class="info-key">URL Vinculada:</span>
                <span class="info-val" style="font-size:11px;"><?= htmlspecialchars($webhookInfo['url']) ?></span>
            </div>
            <?php endif; ?>
            <?php if (!empty($webhookInfo['last_error_message'])): ?>
            <div class="info-row">
                <span class="info-key">Último Reporte:</span>
                <span class="info-val" style="color:#f87171;font-size:11px;"><?= htmlspecialchars($webhookInfo['last_error_message']) ?></span>
            </div>
            <?php endif; ?>
        </div>

        <form method="POST">
            <div class="form-group">
                <label>URL del Webhook Destino</label>
                <input type="text" name="webhook_url" value="<?= htmlspecialchars($defaultWebhookUrl) ?>" required>
            </div>
            <button type="submit" name="action" value="set" class="btn">🚀 Activar / Vincular Webhook</button>
            <div class="btn-grid">
                <button type="submit" name="action" value="test_msg" class="btn btn-secondary">📩 Probar Grupo Telegram</button>
                <button type="submit" name="action" value="delete" class="btn btn-danger" onclick="return confirm('¿Desactivar Webhook?')">🗑️ Eliminar Webhook</button>
            </div>
        </form>
    </div>
</body>
</html>
