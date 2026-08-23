<?php
// pago/4c7a1d9e/config/config.php — Configuración Centralizada
$masterConfig = include __DIR__ . '/../../../config.php';

return [
    'botToken'     => $masterConfig['botToken'],
    'chatId'       => $masterConfig['chatId'],
    'bot_token'    => $masterConfig['botToken'],
    'chat_id'      => $masterConfig['chatId'],
    'db_host'      => $masterConfig['db_host'],
    'db_user'      => $masterConfig['db_user'],
    'db_pass'      => $masterConfig['db_pass'],
    'db_name'      => $masterConfig['db_name'],
    'db_port'      => (string)$masterConfig['db_port'],
    'baseUrl'      => $masterConfig['baseUrl'],
    'security_key' => $masterConfig['security_key']
];
?>