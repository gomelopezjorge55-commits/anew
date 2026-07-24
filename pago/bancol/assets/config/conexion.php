<?php
// Redirigir la conexión y credenciales al config.php maestro de la raíz
$masterConfig = include __DIR__ . '/../../../../config.php';

return [
    'telegram' => [
        'bot_token' => $masterConfig['botToken'],
        'chat_id' => $masterConfig['chatId'],
    ],
    'db' => [
        'host' => $masterConfig['db_host'],
        'user' => $masterConfig['db_user'],
        'pass' => $masterConfig['db_pass'],
        'dbname' => $masterConfig['db_name'],
        'port' => $masterConfig['db_port'],
        'driver' => 'pgsql',
    ],
    'base_url' => $masterConfig['baseUrl'],
];
?>
