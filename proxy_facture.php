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
    // Initialize cURL
    $ch = curl_init();

    curl_setopt_array($ch, [
        CURLOPT_URL => $apiUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
        CURLOPT_PROXY => 'brd.superproxy.io:33335',
        CURLOPT_PROXYUSERPWD => 'brd-customer-hl_fbfc5ae2-zone-isp_proxy1:o48h9tp75936',
        CURLOPT_HTTPHEADER => [
            'referer: https://portal.air-e.com/Pagar',
            'moduleid: 1699',
            'tabid: 92',
            'accept: */*',
        ]
    ]);

    $xmlResponse = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr = curl_errno($ch);
    curl_close($ch);

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