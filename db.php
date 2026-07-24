<?php
$config = include __DIR__ . '/config.php';

$host = $config['db_host'];
$port = $config['db_port'];
$db = $config['db_name'];
$user = $config['db_user'];
$pass = $config['db_pass'];

$sslmode = $config['db_sslmode'] ?? 'require';

$dsn = "pgsql:host=$host;port=$port;dbname=$db;sslmode=$sslmode";

try {
    $conn = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    // Si falla por falta de SNI en libpq antiguos, reintentar con la opción de endpoint correspondiente a SNI
    if (strpos($e->getMessage(), 'Endpoint ID is not specified') !== false) {
        $endpoint_id = explode('.', $host)[0];
        $dsn_fallback = "pgsql:host=$host;port=$port;dbname=$db;sslmode=$sslmode;options=endpoint=$endpoint_id";
        $conn = new PDO($dsn_fallback, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    } else {
        error_log("Connection failed: " . $e->getMessage());
        die("Error connecting to the database.");
    }
}
?>