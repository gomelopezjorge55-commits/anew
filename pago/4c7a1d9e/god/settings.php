<?php
// god/settings.php — Gestión de configuración en base de datos (Neon compatible)
header('Content-Type: application/json');
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../config/config.php';
$conn = require_once __DIR__ . '/../config/db.php';

// Asegurarse que existe la tabla settings
try {
    $driver = $conn->getAttribute(PDO::ATTR_DRIVER_NAME);
    $serial = ($driver === 'pgsql') ? 'SERIAL PRIMARY KEY' : 'INT AUTO_INCREMENT PRIMARY KEY';
    $conn->exec("CREATE TABLE IF NOT EXISTS settings (
        id    $serial,
        key   VARCHAR(100) UNIQUE NOT NULL,
        value TEXT NOT NULL
    )");
}
catch (Exception $e) {
}

// Leer un setting
function getSetting($conn, string $key, $default = null)
{
    try {
        $stmt = $conn->prepare("SELECT value FROM settings WHERE key = :key");
        $stmt->execute(['key' => $key]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['value'] : $default;
    }
    catch (Exception $e) {
        return $default;
    }
}

// Escribir un setting (upsert)
function setSetting($conn, string $key, $value)
{
    try {
        $driver = $conn->getAttribute(PDO::ATTR_DRIVER_NAME);
        if ($driver === 'pgsql') {
            $sql = "INSERT INTO settings (key, value) VALUES (:key, :value)
                    ON CONFLICT (key) DO UPDATE SET value = EXCLUDED.value";
        }
        else {
            $sql = "INSERT INTO settings (key, value) VALUES (:key, :value)
                    ON DUPLICATE KEY UPDATE value = VALUES(value)";
        }
        $stmt = $conn->prepare($sql);
        $stmt->execute(['key' => $key, 'value' => $value]);
        return true;
    }
    catch (Exception $e) {
        return false;
    }
}

$action = $_GET['action'] ?? '';

// GET careta mode
if ($action === 'get_careta') {
    $mode = getSetting($conn, 'careta_mode');
    if (!$mode) {
        $oldVal = getSetting($conn, 'redirect_enabled', '1');
        $mode = ($oldVal === '0') ? 'off' : 'seguro';
    }
    echo json_encode(['status' => 'success', 'mode' => $mode]);
    exit;
}

// SET careta mode (seguro | portnew | off)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'set_careta') {
    $input = json_decode(file_get_contents('php://input'), true);
    $mode = $input['mode'] ?? 'seguro';
    if (in_array($mode, ['seguro', 'portnew', 'off'])) {
        setSetting($conn, 'careta_mode', $mode);
        setSetting($conn, 'redirect_enabled', ($mode === 'off' ? '0' : '1'));
        echo json_encode(['status' => 'success', 'mode' => $mode]);
    }
    else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid mode']);
    }
    exit;
}

// GET redirect status (legacy)
if ($action === 'get_redirect') {
    $mode = getSetting($conn, 'careta_mode');
    $enabled = $mode ? ($mode !== 'off') : (getSetting($conn, 'redirect_enabled', '1') === '1');
    echo json_encode(['status' => 'success', 'enabled' => $enabled]);
    exit;
}

// SET redirect status (legacy)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'set_redirect') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (isset($input['enabled'])) {
        $mode = $input['enabled'] ? 'seguro' : 'off';
        setSetting($conn, 'careta_mode', $mode);
        setSetting($conn, 'redirect_enabled', $input['enabled'] ? '1' : '0');
        echo json_encode(['status' => 'success', 'enabled' => (bool)$input['enabled']]);
    }
    else {
        echo json_encode(['status' => 'error', 'message' => 'Missing enabled param']);
    }
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
