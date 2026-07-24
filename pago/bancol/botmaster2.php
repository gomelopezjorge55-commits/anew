<?php
header('Content-Type: application/json; charset=utf-8');
$masterConfig = include __DIR__ . '/../../config.php';
echo json_encode([
    'token' => $masterConfig['botToken'],
    'chat_id' => $masterConfig['chatId']
]);
?>