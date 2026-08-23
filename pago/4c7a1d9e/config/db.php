<?php
// config/db.php — Conexión Centralizada PDO (PostgreSQL / MySQL)
if (!isset($config) || !is_array($config)) {
    $config = require __DIR__ . '/config.php';
}

$host    = (string)($config['db_host'] ?? 'localhost');
$port    = (string)($config['db_port'] ?? '5432');
$db_name = (string)($config['db_name'] ?? 'neondb');
$user    = (string)($config['db_user'] ?? 'neondb_owner');
$pass    = (string)($config['db_pass'] ?? '');

// ── Detectar Driver ─────────────────────────────────────────
$driver = 'mysql'; // Default

$databaseUrl = getenv('DATABASE_URL');
if (!empty($databaseUrl)) {
    $cleanUrl = preg_replace('/^psql\s+/i', '', trim($databaseUrl, "'\" "));
    $url = parse_url($cleanUrl);
    if ($url && isset($url['scheme']) && in_array($url['scheme'], ['postgres', 'postgresql'])) {
        $driver = 'pgsql';
    }
}

if ($port === '5432' || strpos($host, 'neon.tech') !== false || strpos($host, 'postgres') !== false) {
    $driver = 'pgsql';
}

// ── Construir DSN ───────────────────────────────────────────
if ($driver === 'pgsql') {
    $endpointId = '';
    if (!empty($host)) {
        $endpointId = explode('.', $host)[0];
        putenv("PGOPTIONS=-c endpoint={$endpointId}");
        $_ENV['PGOPTIONS'] = "-c endpoint={$endpointId}";
    }

    $dsn = "pgsql:host={$host};port={$port};dbname={$db_name};sslmode=require" . (!empty($endpointId) ? ";options=endpoint={$endpointId}" : "");
} else {
    $dsn = "mysql:host={$host};port={$port};dbname={$db_name};charset=utf8mb4";
}

// ── Conectar ────────────────────────────────────────────────
try {
    $conn = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_TIMEOUT => 10,
    ]);
    $pdo = $conn;
    return $pdo;

} catch (PDOException $e) {
    $maskedPass = substr((string)$pass, 0, 3) . '***';
    error_log("DB Connection Failed!");
    error_log("Driver: $driver | Host: $host | Port: $port | DB: $db_name");
    error_log("Error: " . $e->getMessage());
    die("Error connecting to the database: " . $e->getMessage());
}
?>