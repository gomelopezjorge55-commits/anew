<?php
// test_telegram.php - Usa este archivo para probar si tu Bot de Telegram está bien configurado en Render.

$config = require 'assets/config/conexion.php';

$botToken = $config['telegram']['bot_token'];
$chatId = $config['telegram']['chat_id'];

echo "<h1>Depuración de Telegram</h1>";
echo "› <b>Token:</b> " . ($botToken ? "Configurado (..." . substr($botToken, -5) . ")" : "VACÍO") . "<br>";
echo "› <b>Chat ID:</b> " . ($chatId ?: "VACÍO") . "<br><br>";

if (!$botToken || !$chatId) {
    die("<span style='color:red'>ERROR: Faltan variables de entorno en Render. Revisa la pestaña 'Environment'.</span>");
}

$message = "🧪 *Prueba de conexión desde Render*\nSi recibes este mensaje, la configuración es CORRECTA.";

$url = "https://api.telegram.org/bot{$botToken}/sendMessage";
$post_fields = [
    'chat_id' => $chatId,
    'text' => $message,
    'parse_mode' => 'Markdown'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$result = curl_exec($ch);
$err = curl_error($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($err) {
    echo "<span style='color:red'><b>ERROR de CURL:</b> $err</span>";
}
else {
    echo "<b>Respuesta de Telegram (HTTP $http_code):</b><br>";
    echo "<pre>" . htmlspecialchars($result) . "</pre>";

    $json = json_decode($result, true);
    if ($json && $json['ok']) {
        echo "<h2 style='color:green'>¡EXITO! El bot funciona correctamente.</h2>";
    }
    else {
        echo "<h2 style='color:red'>FALLÓ: Telegram devolvió un error.</h2>";
        echo "Causas comunes:<br>1. El Bot Token es inválido.<br>2. El Bot NO está en el grupo/chat.<br>3. El Chat ID es incorrecto.";
    }
}
?>
