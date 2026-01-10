<?php
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
$apiUrl = "https://caribesol.facture.co/DesktopModules/Gateway.Commons/API/Documento/getDocumentoPago?cdPoliza={$nic}";

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
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
    ]);

    $xmlResponse = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        throw new Exception('Error al conectar con Facture: ' . curl_error($ch));
    }

    curl_close($ch);

    if ($httpCode !== 200) {
        throw new Exception('Error HTTP: ' . $httpCode);
    }

    if (empty($xmlResponse)) {
        throw new Exception('No se recibió respuesta del servidor');
    }

    // Parse JSON response
    $data = json_decode($xmlResponse, true);

    if ($data === null) {
        echo json_encode([
            'error' => 'Error al parsear respuesta JSON',
            'debug' => substr($xmlResponse, 0, 500)
        ]);
        exit;
    }

    // Check if we got an array of documents
    if (!is_array($data) || empty($data)) {
        echo json_encode([
            'error' => 'No se encontraron documentos para este NIC',
            'nic' => $nic
        ]);
        exit;
    }

    // Get first document
    $documento = $data[0];

    // Extract values
    $valorMes = isset($documento['amt_Valor']) ? $documento['amt_Valor'] : 0;
    $deudaTotal = isset($documento['amt_DeudaTotal']) ? $documento['amt_DeudaTotal'] : 0;
    $numeroDocumento = isset($documento['cd_NumeroDocumento']) ? $documento['cd_NumeroDocumento'] : '';
    $periodo = isset($documento['cd_Periodo']) ? $documento['cd_Periodo'] : '';
    $vencimiento = isset($documento['dt_Vencimiento']) ? $documento['dt_Vencimiento'] : '';
    $estado = isset($documento['Codigo_EstadoPagoDocumento']) ? $documento['Codigo_EstadoPagoDocumento'] : '';

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