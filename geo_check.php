<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function getRealClientIP() {
    $ip = '';
    if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $ip = trim($ips[0]);
    } elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

function isPrivateOrLocalIP($ip) {
    if (in_array($ip, ['127.0.0.1', '::1', 'localhost'])) {
        return true;
    }
    // Filter out private IP ranges (10.0.0.0/8, 172.16.0.0/12, 192.168.0.0/16, etc.)
    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
        return true;
    }
    return false;
}

function detectCountryCode($ip) {
    // 1. Direct Cloudflare Header
    if (!empty($_SERVER['HTTP_CF_IPCOUNTRY'])) {
        return strtoupper($_SERVER['HTTP_CF_IPCOUNTRY']);
    }

    // 2. Check local/private IP
    if (isPrivateOrLocalIP($ip)) {
        return 'CO'; // Allow local development environment
    }

    // 3. Check Session cache
    if (isset($_SESSION['user_country_code']) && !empty($_SESSION['user_country_code'])) {
        return $_SESSION['user_country_code'];
    }

    // 4. Query GeoIP API (ip-api.com)
    $countryCode = 'UNKNOWN';
    try {
        $ch = curl_init("http://ip-api.com/json/{$ip}?fields=status,countryCode");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        $response = curl_exec($ch);
        curl_close($ch);

        if ($response) {
            $data = json_decode($response, true);
            if (isset($data['status']) && $data['status'] === 'success' && !empty($data['countryCode'])) {
                $countryCode = strtoupper($data['countryCode']);
            }
        }
    } catch (Exception $e) {
        // Fallback silently if API fails
    }

    // Fallback GeoIP API (ipapi.co) if primary failed
    if ($countryCode === 'UNKNOWN') {
        try {
            $ch = curl_init("https://ipapi.co/{$ip}/country/");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 3);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            $res = curl_exec($ch);
            curl_close($ch);
            if ($res && strlen(trim($res)) === 2) {
                $countryCode = strtoupper(trim($res));
            }
        } catch (Exception $e) {
            // Keep UNKNOWN
        }
    }

    // Default to 'CO' if service failed to prevent locking out legitimate users on network glitches
    if ($countryCode === 'UNKNOWN') {
        $countryCode = 'CO';
    }

    $_SESSION['user_country_code'] = $countryCode;
    return $countryCode;
}

// Perform Check
$clientIP = getRealClientIP();
$countryCode = detectCountryCode($clientIP);

// Restrict access if NOT Colombia (CO)
if ($countryCode !== 'CO') {
    $isJsonRequest = (
        (!empty($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) ||
        (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && stristr($_SERVER['HTTP_X_REQUESTED_WITH'], 'xmlhttprequest')) ||
        (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false)
    );

    if ($isJsonRequest) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'error',
            'error' => 'Servicio no disponible en tu ubicación.',
            'message' => 'Estamos preparando algo nuevo para ti, pronto estaremos en tu ubicación.'
        ]);
        exit();
    }

    include __DIR__ . '/bloqueado.php';
    exit();
}
?>
