<?php
// config/chat_config.php
$config = require __DIR__ . '/config.php';
return [
    'chatId'   => $config['chatId'],
    'botToken' => $config['botToken']
];
?>
