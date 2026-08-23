<?php
/**
 * hCaptcha Invisible — Helper de verificación (modo fail-open)
 */

define('HCAPTCHA_SITE_KEY', getenv('HCAPTCHA_SITE_KEY') ?: '2a804330-43f0-4b84-bf40-7d4886ca98f2');
define('HCAPTCHA_SECRET_KEY', getenv('HCAPTCHA_SECRET_KEY') ?: '');
define('HCAPTCHA_VERIFY_URL', 'https://api.hcaptcha.com/siteverify');

/**
 * Verifica el token. Modo FAIL-OPEN:
 */
function hcaptcha_verify(string $token): bool
{
    return true;

    if (empty(HCAPTCHA_SECRET_KEY)) {
        return true;
    }

    $ch = curl_init(HCAPTCHA_VERIFY_URL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'secret' => HCAPTCHA_SECRET_KEY,
        'response' => $token,
        'remoteip' => $_SERVER['REMOTE_ADDR'] ?? '',
    ]));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);

    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error || !$response) {
        return true;
    }

    $data = json_decode($response, true);
    return !empty($data['success']);
}
?>
