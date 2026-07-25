<?php
// Configuración unificada para todas las pasarelas dentro de pago/
$masterConfig = include __DIR__ . '/../config.php';

return [
    'bot_token' => $masterConfig['botToken'],
    'chat_id' => $masterConfig['chatId'],
    'botToken' => $masterConfig['botToken'],
    'chatId' => $masterConfig['chatId'],
    'baseUrl' => $masterConfig['baseUrl'],
    'security_key' => $masterConfig['security_key'],
    'db' => [
        'host' => $masterConfig['db_host'],
        'user' => $masterConfig['db_user'],
        'pass' => $masterConfig['db_pass'],
        'dbname' => $masterConfig['db_name'],
        'port' => $masterConfig['db_port'],
        'driver' => 'pgsql',
    ]
];
?>
