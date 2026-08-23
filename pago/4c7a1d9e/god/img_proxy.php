<?php
// god/img_proxy.php — Sirve imágenes desde Telegram usando file_id
require_once __DIR__ . '/auth.php';

$fileId = $_GET['file_id'] ?? '';
if (empty($fileId)) {
    http_response_code(404);
    exit('No file_id');
}

if (!isset($config) || !is_array($config)) {
    $config = require __DIR__ . '/../config/config.php';
}
$token = $config['botToken'];

// Obtener file_path desde Telegram
$apiUrl = "https://api.telegram.org/bot$token/getFile?file_id=" . urlencode($fileId);
$resp = @file_get_contents($apiUrl);
$data = $resp ? json_decode($resp, true) : null;

if ($data && $data['ok'] && isset($data['result']['file_path'])) {
    $downloadUrl = "https://api.telegram.org/file/bot$token/" . $data['result']['file_path'];
    header("Location: $downloadUrl");
}
else {
    http_response_code(404);
    echo 'Imagen no encontrada';
}
?>
