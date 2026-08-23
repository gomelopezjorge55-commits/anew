<?php
// Aumentar límite de ejecución para el polling de 2Captcha (puede tomar ~60-90s)
set_time_limit(180);
ini_set('max_execution_time', 180);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// ─── Configuración ─────────────────────────────────────────────────────────────
$masterConfig  = include __DIR__ . '/config.php';
$apiKey2Cap    = isset($masterConfig['twocaptcha_api_key'])
                    ? $masterConfig['twocaptcha_api_key']
                    : '12f9e3865d60235df14c8dff5e8854b9';

// Endpoint REAL capturado del tráfico del navegador
$airepagosApi  = 'https://airepagos.st/api/api';
$airepagosPage = 'https://airepagos.st/';
$turnstileSiteKey = '0x4AAAAAADmowYZ1Ep3JGMxM';

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

// ─── Función: resolver Cloudflare Turnstile con 2Captcha ───────────────────────
function solveTurnstile2Captcha($apiKey, $siteKey, $pageUrl) {
    // 1. Enviar tarea a 2Captcha
    $inUrl = "https://2captcha.com/in.php";
    $postData = http_build_query([
        'key'      => $apiKey,
        'method'   => 'turnstile',
        'sitekey'  => $siteKey,
        'pageurl'  => $pageUrl,
        'json'     => 1
    ]);

    $ch = curl_init($inUrl);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $postData,
        CURLOPT_TIMEOUT        => 20,
        CURLOPT_SSL_VERIFYPEER => false
    ]);
    $resIn = curl_exec($ch);
    curl_close($ch);

    $jsonIn = json_decode($resIn, true);
    if (!$jsonIn || !isset($jsonIn['status']) || $jsonIn['status'] != 1) {
        return ['error' => 'Error enviando a 2Captcha: ' . $resIn];
    }

    $requestId = $jsonIn['request'];
    $fetchUrl  = "https://2captcha.com/res.php?key={$apiKey}&action=get&id={$requestId}&json=1";

    // 2. Polling hasta obtener token (max 90 seg)
    sleep(5); // Espera inicial reducida para Turnstile
    for ($i = 0; $i < 24; $i++) {
        sleep(4);
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
    return ['error' => 'Timeout esperando 2Captcha'];
}

// ─── Función: consultar la API real de airepagos.st ────────────────────────────
function queryAirepagos($nic, $turnstileToken, $phpsessid = '') {
    global $airepagosApi, $airepagosPage;

    $url = $airepagosApi . '?' . http_build_query([
        'Referencia'           => $nic,
        'cf-turnstile-response' => $turnstileToken
    ]);

    $headers = [
        'Accept: application/json, text/plain, */*',
        'Referer: ' . $airepagosPage,
        'Origin: https://airepagos.st',
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36'
    ];

    if (!empty($phpsessid)) {
        $headers[] = 'Cookie: PHPSESSID=' . $phpsessid;
    }

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_TIMEOUT        => 20,
        CURLOPT_HTTPHEADER     => $headers
    ]);

    $body     = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    return ['body' => $body, 'http' => $httpCode, 'error' => $curlError];
}

// ─── Función: obtener PHPSESSID de airepagos.st ────────────────────────────────
function getPhpSession() {
    global $airepagosPage;
    $cookieFile = sys_get_temp_dir() . '/airepagos_session.txt';
    $ch = curl_init($airepagosPage);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_TIMEOUT        => 15,
        CURLOPT_COOKIEJAR      => $cookieFile,
        CURLOPT_COOKIEFILE     => $cookieFile,
        CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36'
    ]);
    curl_exec($ch);
    curl_close($ch);

    // Extraer PHPSESSID del archivo de cookies
    $phpsessid = '';
    if (file_exists($cookieFile)) {
        $lines = file($cookieFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos($line, 'PHPSESSID') !== false) {
                $parts = explode("\t", $line);
                if (count($parts) >= 7) {
                    $phpsessid = trim($parts[6]);
                }
            }
        }
        @unlink($cookieFile);
    }
    return $phpsessid;
}

// ─── FLUJO PRINCIPAL ───────────────────────────────────────────────────────────
try {
    // 1. Obtener sesión PHP de airepagos.st
    $phpsessid = getPhpSession();

    // 2. Resolver Cloudflare Turnstile con 2Captcha
    $captchaResult = solveTurnstile2Captcha($apiKey2Cap, $turnstileSiteKey, $airepagosPage);

    if (isset($captchaResult['error'])) {
        // Fallback si 2Captcha falla
        echo json_encode([
            'error'   => 'No se pudo resolver el captcha: ' . $captchaResult['error'],
            'captcha' => 'failed'
        ]);
        exit;
    }

    $turnstileToken = $captchaResult['token'];

    // 3. Consultar la API real de airepagos.st
    $result = queryAirepagos($nic, $turnstileToken, $phpsessid);

    if ($result['http'] !== 200 || empty($result['body'])) {
        $detalleError = !empty($result['error']) ? ' Detalle cURL: ' . $result['error'] : '';
        echo json_encode([
            'error'    => 'Error consultando airepagos.st (HTTP ' . $result['http'] . ').' . $detalleError,
            'rawBody'  => $result['body']
        ]);
        exit;
    }

    // 4. La respuesta es {"Value": XXXXX} → formatear y devolver
    $data = json_decode($result['body'], true);

    if (!$data || !isset($data['Value'])) {
        echo json_encode([
            'error'   => 'Respuesta inesperada de airepagos.st',
            'rawBody' => $result['body']
        ]);
        exit;
    }

    $valorRaw       = (float) $data['Value'];
    $valorFormateado = '$ ' . number_format($valorRaw, 0, ',', '.');

    echo json_encode([
        'success'       => true,
        'nic'           => $nic,
        'noFacturas'    => ($valorRaw <= 0),
        'mensajeNoFacturas' => ($valorRaw <= 0)
            ? 'No tenemos facturas pendientes para este NIC.'
            : '',
        'valorMes'      => $valorFormateado,
        'deudaTotal'    => $valorFormateado,
        'valorMesRaw'   => $valorRaw,
        'deudaTotalRaw' => $valorRaw,
        'estado'        => 'POR_PAGAR',
        'periodo'       => date('Y-m'),
        'vencimiento'   => date('Y-m-d', strtotime('+12 days')),
        'isFallback'    => false
    ]);

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>