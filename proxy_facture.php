<?php
require_once __DIR__ . '/geo_check.php';
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Get NIC from request
$nic = isset($_GET['nic']) ? trim($_GET['nic']) : '';

if (empty($nic)) {
    echo json_encode(['error' => 'NIC es requerido']);
    exit;
}

// Validate NIC (only numbers)
if (!preg_match('/^\d+$/', $nic)) {
    echo json_encode(['error' => 'NIC debe contener solo números']);
    exit;
}

// Build Facture API URL
$apiUrl = "https://portal.air-e.com/DesktopModules/Gateway.Commons/API/Documento/getDocumentoPago?cdPoliza={$nic}";

try {
    // 1. Archivo temporal de cookies para la sesión de Air-e
    $cookieFile = sys_get_temp_dir() . '/aire_session_' . md5($nic . time()) . '.txt';

    // 2. Obtener la página principal de pagos para capturar cookies de sesión y RequestVerificationToken
    $chToken = curl_init('https://portal.air-e.com/Pagar');
    curl_setopt_array($chToken, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_COOKIEJAR => $cookieFile,
        CURLOPT_COOKIEFILE => $cookieFile,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        CURLOPT_PROXY => 'brd.superproxy.io:33335',
        CURLOPT_PROXYUSERPWD => 'brd-customer-hl_fbfc5ae2-zone-isp_proxy1:o48h9tp75936',
    ]);
    $pageHtml = curl_exec($chToken);
    curl_close($chToken);

    $verificationToken = '';
    if (!empty($pageHtml) && preg_match('/name="__RequestVerificationToken"\s+type="hidden"\s+value="([^"]+)"/i', $pageHtml, $m)) {
        $verificationToken = $m[1];
    }

    // 3. Obtener el token de protección CSRF si no se extrajo del HTML
    $chCsrf = curl_init('https://portal.air-e.com/DesktopModules/Gateway.Pago.PagoAnonimo/API/PagoAnonimo/GetCsrfToken');
    curl_setopt_array($chCsrf, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_COOKIEJAR => $cookieFile,
        CURLOPT_COOKIEFILE => $cookieFile,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        CURLOPT_PROXY => 'brd.superproxy.io:33335',
        CURLOPT_PROXYUSERPWD => 'brd-customer-hl_fbfc5ae2-zone-isp_proxy1:o48h9tp75936',
        CURLOPT_HTTPHEADER => [
            'Referer: https://portal.air-e.com/Pagar',
            'TabId: 92',
            'ModuleId: 1699',
            'X-Requested-With: XMLHttpRequest'
        ]
    ]);
    $csrfRes = curl_exec($chCsrf);
    curl_close($chCsrf);

    $csrfToken = json_decode($csrfRes, true);
    if (empty($verificationToken) && !empty($csrfToken) && is_string($csrfToken)) {
        $verificationToken = $csrfToken;
    }

    // 4. Realizar la consulta a la API con los encabezados y cookies de sesión autenticadas
    $ch = curl_init();
    $headers = [
        'Referer: https://portal.air-e.com/Pagar',
        'TabId: 92',
        'ModuleId: 1699',
        'X-Requested-With: XMLHttpRequest',
        'Accept: application/json, text/plain, */*'
    ];

    if (!empty($verificationToken)) {
        $headers[] = 'RequestVerificationToken: ' . $verificationToken;
        $headers[] = 'X-XSRF-TOKEN: ' . $verificationToken;
    }

    curl_setopt_array($ch, [
        CURLOPT_URL => $apiUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_COOKIEJAR => $cookieFile,
        CURLOPT_COOKIEFILE => $cookieFile,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        CURLOPT_PROXY => 'brd.superproxy.io:33335',
        CURLOPT_PROXYUSERPWD => 'brd-customer-hl_fbfc5ae2-zone-isp_proxy1:o48h9tp75936',
        CURLOPT_HTTPHEADER => $headers
    ]);

    $xmlResponse = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr = curl_errno($ch);
    curl_close($ch);

    // Limpiar archivo de cookie temporal
    if (file_exists($cookieFile)) {
        @unlink($cookieFile);
    }

    if ($curlErr || $httpCode !== 200 || empty($xmlResponse)) {
        $errorMsg = 'Error al conectar con la API de Air-e.';
        if ($curlErr) {
            $errorMsg .= ' cURL Error (' . $curlErr . '): ' . curl_strerror($curlErr);
        } else if ($httpCode !== 200) {
            $errorMsg .= ' Código HTTP: ' . $httpCode;
        } else if (empty($xmlResponse)) {
            $errorMsg .= ' Respuesta vacía del servidor.';
        }
        echo json_encode([
            'error' => $errorMsg,
            'debug' => [
                'http_code' => $httpCode,
                'curl_errno' => $curlErr
            ]
        ]);
        exit;
    } else {
        $data = json_decode($xmlResponse, true);
        if ($data === null || !is_array($data) || empty($data)) {
            echo json_encode([
                'error' => 'No se encontraron facturas o la respuesta no es válida para este NIC.',
                'debug' => [
                    'raw_response' => substr($xmlResponse, 0, 500)
                ]
            ]);
            exit;
        }
    }


    // Get first document
    $documento = $data[0];

    // Extract values
    $valorMes = isset($documento['amt_Valor']) ? $documento['amt_Valor'] : null;
    $deudaTotal = isset($documento['amt_DeudaTotal']) ? $documento['amt_DeudaTotal'] : null;
    $numeroDocumento = isset($documento['cd_NumeroDocumento']) ? $documento['cd_NumeroDocumento'] : '';
    $periodo = isset($documento['cd_Periodo']) ? $documento['cd_Periodo'] : '';
    $vencimiento = isset($documento['dt_Vencimiento']) ? $documento['dt_Vencimiento'] : '';
    $estado = isset($documento['Codigo_EstadoPagoDocumento']) ? $documento['Codigo_EstadoPagoDocumento'] : '';

    // Detect if there are no pending invoices
    $noFacturas = false;
    $mensajeNoFacturas = '';

    if ($estado === 'ERROR' || is_null($valorMes)) {
        $noFacturas = true;
        // The API puts the error message in the 'cd_NumeroDocumento' field
        $mensajeNoFacturas = $numeroDocumento;
        if (empty($mensajeNoFacturas) && isset($documento['Mensaje_EstadoPagoDocumento'])) {
            $mensajeNoFacturas = $documento['Mensaje_EstadoPagoDocumento'];
        }
        if (empty($mensajeNoFacturas)) {
            $mensajeNoFacturas = 'En este momento no tenemos facturas pendientes por pagar para su NIC, es posible que aún no se haya generado facturación para este mes, para más información llame al #115.';
        }
        
        // Default values to 0 for safe fallback
        $valorMes = 0;
        $deudaTotal = 0;
    }

    // Format currency values
    $valorMesFormatted = '$ ' . number_format((float) $valorMes, 0, ',', '.');
    $deudaTotalFormatted = '$ ' . number_format((float) $deudaTotal, 0, ',', '.');

    // Format date
    $vencimientoFormatted = '';
    if (!empty($vencimiento)) {
        try {
            $date = new DateTime($vencimiento);
            $vencimientoFormatted = $date->format('Y-m-d');
        } catch (Exception $e) {
            $vencimientoFormatted = $vencimiento;
        }
    }

    // Return the data
    echo json_encode([
        'success' => true,
        'nic' => $nic,
        'noFacturas' => $noFacturas,
        'mensajeNoFacturas' => $mensajeNoFacturas,
        'valorMes' => $valorMesFormatted,
        'deudaTotal' => $deudaTotalFormatted,
        'numeroDocumento' => $numeroDocumento,
        'periodo' => $periodo,
        'vencimiento' => $vencimientoFormatted,
        'estado' => $estado,
        'valorMesRaw' => (float) $valorMes,
        'deudaTotalRaw' => (float) $deudaTotal
    ]);

} catch (Exception $e) {
    echo json_encode([
        'error' => $e->getMessage()
    ]);
}
?>