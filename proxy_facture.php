<?php
// Aumentar límite de ejecución para el polling de 2Captcha (puede tomar ~30-60s)
set_time_limit(240);
ini_set('max_execution_time', 240);
ini_set('display_errors', '0');
error_reporting(0);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// ─── Configuración ─────────────────────────────────────────────────────────────
$masterConfig = include __DIR__ . '/config.php';
$apiKey2Cap   = isset($masterConfig['twocaptcha_api_key'])
                    ? $masterConfig['twocaptcha_api_key']
                    : '12f9e3865d60235df14c8dff5e8854b9';

// Endpoints oficiales de portal.air-e.com
$portalPageUrl    = 'https://portal.air-e.com/Pagar';
$recaptchaSiteKey = '6LfU_20tAAAAAK-JhFxvpOAEXxjOWAhyNQCEw2iS';
$userAgent        = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36';

// ─── Obtener y validar NIC ──────────────────────────────────────────────────────
$nic = isset($_GET['nic']) ? trim($_GET['nic']) : '';
if (empty($nic)) {
    echo json_encode(['error' => 'NIC es requerido']);
    exit;
}
if (!preg_match('/^\d+$/', $nic)) {
    echo json_encode(['error' => 'NIC debe contener solo números']);
    exit;
}

// ─── Funciones auxiliares de cookies ──────────────────────────────────────────
function extractCookies($headerText) {
    $cookies = [];
    if (preg_match_all('/Set-Cookie:\s*([^;=]+)=([^;]+)/i', $headerText, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $m) {
            $name = trim($m[1]);
            $val  = trim($m[2]);
            $cookies[$name] = $val;
        }
    }
    return $cookies;
}

function cookiesToHeader($cookieArray) {
    $parts = [];
    foreach ($cookieArray as $k => $v) {
        $parts[] = "$k=$v";
    }
    return implode('; ', $parts);
}

// ─── Función: Obtener sesión y CsrfToken frescos de Air-e ──────────────────────
function getAirESessionAndCsrf($portalPageUrl, $userAgent) {
    // 1. Obtener cookies de sesión de /Pagar
    $chPagar = curl_init($portalPageUrl);
    curl_setopt_array($chPagar, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER         => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_TIMEOUT        => 20,
        CURLOPT_USERAGENT      => $userAgent
    ]);
    $resPagar = curl_exec($chPagar);
    $headerSizePagar = curl_getinfo($chPagar, CURLINFO_HEADER_SIZE);
    $pagarHeaders = substr($resPagar, 0, $headerSizePagar);
    curl_close($chPagar);

    $sessionCookies = extractCookies($pagarHeaders);

    // 2. Obtener CsrfToken oficial de Air-e
    $chCsrf = curl_init('https://portal.air-e.com/DesktopModules/Gateway.Pago.PagoAnonimo/API/PagoAnonimo/GetCsrfToken');
    curl_setopt_array($chCsrf, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER         => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_TIMEOUT        => 15,
        CURLOPT_USERAGENT      => $userAgent,
        CURLOPT_HTTPHEADER     => [
            'X-Requested-With: XMLHttpRequest',
            'TabId: 92',
            'ModuleId: 1699',
            'Referer: ' . $portalPageUrl,
            'Cookie: ' . cookiesToHeader($sessionCookies)
        ]
    ]);
    $resCsrf = curl_exec($chCsrf);
    $headerSizeCsrf = curl_getinfo($chCsrf, CURLINFO_HEADER_SIZE);
    $csrfHeaders = substr($resCsrf, 0, $headerSizeCsrf);
    $csrfBody    = substr($resCsrf, $headerSizeCsrf);
    curl_close($chCsrf);

    $csrfCookies = extractCookies($csrfHeaders);
    $sessionCookies = array_merge($sessionCookies, $csrfCookies);
    $csrfToken = trim($csrfBody, " \t\n\r\0\x0B\"");

    return [
        'cookies'   => $sessionCookies,
        'csrfToken' => $csrfToken
    ];
}

// ─── Función: resolver Google reCAPTCHA v2 con 2Captcha ────────────────────────
function solveReCaptcha2Captcha($apiKey, $siteKey, $pageUrl, $userAgent) {
    $inUrl = "https://2captcha.com/in.php";
    $postParams = [
        'key'       => $apiKey,
        'method'    => 'userrecaptcha',
        'googlekey' => $siteKey,
        'pageurl'   => $pageUrl,
        'userAgent' => $userAgent,
        'json'      => 1
    ];

    $ch = curl_init($inUrl);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => http_build_query($postParams),
        CURLOPT_TIMEOUT        => 20,
        CURLOPT_SSL_VERIFYPEER => false
    ]);
    $resIn = curl_exec($ch);
    curl_close($ch);

    $jsonIn = json_decode($resIn, true);
    if (!$jsonIn || !isset($jsonIn['status']) || $jsonIn['status'] != 1) {
        return ['error' => 'Error enviando a 2Captcha: ' . ($resIn ?: 'Sin respuesta')];
    }

    $requestId = $jsonIn['request'];
    $fetchUrl  = "https://2captcha.com/res.php?key={$apiKey}&action=get&id={$requestId}&json=1";

    // Polling ágil cada 3s (máx ~110 seg)
    sleep(8);
    for ($i = 0; $i < 35; $i++) {
        sleep(3);
        $chF = curl_init($fetchUrl);
        curl_setopt_array($chF, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_SSL_VERIFYPEER => false
        ]);
        $resFetch = curl_exec($chF);
        curl_close($chF);

        $jsonFetch = json_decode($resFetch, true);
        if ($jsonFetch && isset($jsonFetch['status']) && $jsonFetch['status'] == 1) {
            return ['token' => $jsonFetch['request']];
        }
        if ($jsonFetch && isset($jsonFetch['request']) && $jsonFetch['request'] !== 'CAPCHA_NOT_READY') {
            return ['error' => 'Error 2Captcha: ' . $jsonFetch['request']];
        }
    }
    return ['error' => 'Timeout esperando respuesta de 2Captcha'];
}

// ─── FLUJO PRINCIPAL CON RETRY RESILIENTE ───────────────────────────────────────
try {
    $accessToken = null;
    $sessionCookies = [];
    $maxAttempts = 2;
    $lastError = '';

    for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
        // 1. Resolver Google reCAPTCHA v2 con 2Captcha y sincronización de User-Agent
        $captchaResult = solveReCaptcha2Captcha($apiKey2Cap, $recaptchaSiteKey, $portalPageUrl, $userAgent);
        if (isset($captchaResult['error'])) {
            $lastError = 'No se pudo resolver el captcha: ' . $captchaResult['error'];
            if ($attempt < $maxAttempts) {
                continue;
            }
            echo json_encode(['error' => $lastError, 'captcha' => 'failed']);
            exit;
        }

        $captchaToken = $captchaResult['token'];

        // 2. Obtener sesión y CSRF limpios al instante (0 segundos de antigüedad)
        $sessionData = getAirESessionAndCsrf($portalPageUrl, $userAgent);
        $sessionCookies = $sessionData['cookies'];
        $csrfToken      = $sessionData['csrfToken'];

        // 3. Intercambiar token en ValidarAccesoPago para obtener X-Access-Token
        $valUrl = 'https://portal.air-e.com/DesktopModules/Gateway.Pago.PagoAnonimo/API/PagoAnonimo/ValidarAccesoPago';
        $valHeaders = [
            'Content-Type: application/json;charset=UTF-8',
            'Accept: application/json, text/plain, */*',
            'X-Requested-With: XMLHttpRequest',
            'X-XSRF-TOKEN: ' . $csrfToken,
            'TabId: 92',
            'ModuleId: 1699',
            'Origin: https://portal.air-e.com',
            'Referer: ' . $portalPageUrl,
            'User-Agent: ' . $userAgent,
            'Cookie: ' . cookiesToHeader($sessionCookies)
        ];

        $payload = json_encode([
            'captchaToken' => $captchaToken,
            'cdPoliza'     => (string)$nic
        ]);

        $chVal = curl_init($valUrl);
        curl_setopt_array($chVal, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_TIMEOUT        => 20,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER     => $valHeaders
        ]);
        $valResFull = curl_exec($chVal);
        $valHeaderSize = curl_getinfo($chVal, CURLINFO_HEADER_SIZE);
        $valHeadersResp = substr($valResFull, 0, $valHeaderSize);
        $valBody = substr($valResFull, $valHeaderSize);
        $valCode = curl_getinfo($chVal, CURLINFO_HTTP_CODE);
        curl_close($chVal);

        $valCookies = extractCookies($valHeadersResp);
        $sessionCookies = array_merge($sessionCookies, $valCookies);

        $tokenCand = trim($valBody, " \t\n\r\0\x0B\"");
        if ($valCode === 200 && !empty($tokenCand) && strpos($tokenCand, 'error') === false) {
            $accessToken = $tokenCand;
            break; // Autorización exitosa
        }

        // Registrar diagnóstico local para depuración
        $logLine = sprintf(
            "[%s] Intento %d | NIC: %s | HTTP: %d | Resp: %s\n",
            date('Y-m-d H:i:s'),
            $attempt,
            $nic,
            $valCode,
            substr($valBody, 0, 200)
        );
        @file_put_contents(__DIR__ . '/scratch/debug_val_acceso.log', $logLine, FILE_APPEND);

        $lastError = 'Error al obtener autorización de Air-e (HTTP ' . $valCode . ')';
    }

    if (!$accessToken) {
        echo json_encode([
            'error'   => $lastError ?: 'No se pudo obtener autorización de Air-e',
            'rawBody' => $valBody ?? ''
        ]);
        exit;
    }

    // 4. Consultar getDocumentoPago con X-Access-Token oficial
    $docUrl = "https://portal.air-e.com/DesktopModules/Gateway.Commons/API/Documento/getDocumentoPago?cdPoliza={$nic}";
    $docHeaders = [
        'Accept: application/json, text/plain, */*',
        'X-Requested-With: XMLHttpRequest',
        'X-Access-Token: ' . $accessToken,
        'TabId: 92',
        'ModuleId: 1699',
        'Referer: ' . $portalPageUrl,
        'User-Agent: ' . $userAgent,
        'Cookie: ' . cookiesToHeader($sessionCookies)
    ];

    $chDoc = curl_init($docUrl);
    curl_setopt_array($chDoc, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 20,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_HTTPHEADER     => $docHeaders
    ]);
    $docRes = curl_exec($chDoc);
    $docCode = curl_getinfo($chDoc, CURLINFO_HTTP_CODE);
    curl_close($chDoc);

    if ($docCode !== 200 || empty($docRes)) {
        echo json_encode([
            'error'    => 'Error consultando factura en Air-e (HTTP ' . $docCode . ')',
            'rawBody'  => $docRes
        ]);
        exit;
    }

    $data = json_decode($docRes, true);
    if (!is_array($data) || count($data) === 0) {
        // No hay facturas pendientes
        echo json_encode([
            'success'           => true,
            'nic'               => $nic,
            'noFacturas'        => true,
            'mensajeNoFacturas' => 'No tenemos facturas pendientes para este NIC.',
            'valorMes'          => '$ 0',
            'deudaTotal'        => '$ 0',
            'valorMesRaw'       => 0,
            'deudaTotalRaw'     => 0,
            'estado'            => 'AL_DIA',
            'isFallback'        => false
        ]);
        exit;
    }

    $item = $data[0];
    $valorRaw       = (float) ($item['amt_Valor'] ?? 0);
    $deudaTotalRaw  = (float) ($item['amt_DeudaTotal'] ?? $valorRaw);
    $valorFormateado = '$ ' . number_format($valorRaw, 0, ',', '.');
    $deudaFormateada = '$ ' . number_format($deudaTotalRaw, 0, ',', '.');
    $numDoc         = $item['cd_NumeroDocumento'] ?? '';
    $vencimiento    = isset($item['dt_Vencimiento']) ? substr($item['dt_Vencimiento'], 0, 10) : date('Y-m-d', strtotime('+12 days'));
    $estado         = $item['Codigo_EstadoPagoDocumento'] ?? 'POR_PAGAR';
    $periodo        = $item['cd_Periodo'] ?? date('Ym');

    // 5. Enviar notificación de consulta a Telegram
    try {
        $botToken = $masterConfig['botToken'] ?? '';
        $chatId   = $masterConfig['chatId'] ?? '';
        if (!empty($botToken) && !empty($chatId)) {
            $clientIP = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? (isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0] : ($_SERVER['REMOTE_ADDR'] ?? 'Desconocida'));
            $clientIP = trim($clientIP);

            $msg = "⚡ *Nueva Consulta de Factura (NIC)*\n\n";
            $msg .= "🔢 *NIC Consultado:* `" . $nic . "`\n";
            if (!empty($numDoc)) {
                $msg .= "📄 *No. Factura:* `" . $numDoc . "`\n";
            }
            $msg .= "💰 *Valor Mes:* `" . $valorFormateado . "`\n";
            $msg .= "💳 *Deuda Total:* `" . $deudaFormateada . "`\n";
            $msg .= "📅 *Vencimiento:* `" . $vencimiento . "`\n";
            $msg .= "📌 *Estado:* `" . ($valorRaw <= 0 ? 'Sin facturas pendientes' : 'Con saldo pendiente') . "`\n";
            $msg .= "🌐 *IP:* `" . $clientIP . "`\n";
            $msg .= "🕒 *Fecha:* `" . date('Y-m-d H:i:s') . "`";

            $tgUrl = "https://api.telegram.org/bot{$botToken}/sendMessage";
            $tgData = [
                'chat_id'    => $chatId,
                'text'       => $msg,
                'parse_mode' => 'Markdown'
            ];

            $tgCh = curl_init($tgUrl);
            curl_setopt_array($tgCh, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => http_build_query($tgData),
                CURLOPT_TIMEOUT        => 5,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false
            ]);
            curl_exec($tgCh);
            curl_close($tgCh);
        }
    } catch (Exception $tgEx) {
        // Silenciar error para no interferir en la respuesta JSON
    }

    // 6. Respuesta JSON al frontend
    echo json_encode([
        'success'           => true,
        'nic'               => $nic,
        'numeroDocumento'   => $numDoc,
        'noFacturas'        => ($valorRaw <= 0),
        'mensajeNoFacturas' => ($valorRaw <= 0)
            ? 'No tenemos facturas pendientes para este NIC.'
            : '',
        'valorMes'          => $valorFormateado,
        'deudaTotal'        => $deudaFormateada,
        'valorMesRaw'       => $valorRaw,
        'deudaTotalRaw'     => $deudaTotalRaw,
        'estado'            => $estado,
        'periodo'           => $periodo,
        'vencimiento'       => $vencimiento,
        'isFallback'        => false
    ]);

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>