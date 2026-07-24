<?php
// Credenciales y configuraciones principales del proyecto

// 1. Obtener credenciales de Telegram dinámicamente desde botmaster2.php (búsqueda ascendente)
$botToken = '';
$chatId = '';
$currentDir = __DIR__;
$botmasterPath = null;

for ($i = 0; $i < 6; $i++) {
    $candidate1 = $currentDir . '/botmaster2.php';
    $candidate2 = $currentDir . '/dinadatos/botmaster2.php';
    $candidate3 = $currentDir . '/api/php_sources/dinadatos/botmaster2.php';
    
    if (file_exists($candidate1)) {
        $botmasterPath = $candidate1;
        break;
    } elseif (file_exists($candidate2)) {
        $botmasterPath = $candidate2;
        break;
    } elseif (file_exists($candidate3)) {
        $botmasterPath = $candidate3;
        break;
    }
    $currentDir = dirname($currentDir);
}

if ($botmasterPath && file_exists($botmasterPath)) {
    $content = file_get_contents($botmasterPath);
    if (preg_match('/\{.*\}/', $content, $matches)) {
        $data = json_decode($matches[0], true);
        $botToken = $data['token'] ?? '';
        $chatId = $data['chat_id'] ?? '';
    }
}

// 2. Credenciales y configuración de la Base de Datos
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'aire';
$db_port = '3306';
$db_driver = 'mysql'; // Por defecto MySQL en desarrollo local

if (getenv('DATABASE_URL')) {
    $url = parse_url(getenv('DATABASE_URL'));
    $db_host = $url['host'] ?? null;
    $db_user = $url['user'] ?? null;
    $db_pass = $url['pass'] ?? null;
    $db_name = ltrim($url['path'] ?? '', '/');
    $db_port = $url['port'] ?? 5432;
    $db_driver = 'pgsql'; // Render utiliza PostgreSQL en producción
} else {
    // Credenciales para Neon PostgreSQL por defecto
    $db_host = 'ep-orange-sound-ap0dmyn4-pooler.c-7.us-east-1.aws.neon.tech';
    $db_user = 'neondb_owner';
    $db_pass = 'npg_oP4YtHqOF6WM';
    $db_name = 'neondb';
    $db_port = '5432';
    $db_driver = 'pgsql';
}

return [
    'telegram' => [
        'bot_token' => $botToken,
        'chat_id' => $chatId,
    ],
    'db' => [
        'host' => $db_host,
        'user' => $db_user,
        'pass' => $db_pass,
        'dbname' => $db_name,
        'port' => $db_port,
        'driver' => $db_driver,
    ],
];
