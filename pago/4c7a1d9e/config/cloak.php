<?php
/**
 * cloak.php — Módulo de Cloaking Anti-Bot y Defensa Perimetral 360°
 * Incluir al inicio de cualquier página pública y procesamiento de formularios.
 */

// ── Iniciar sesión si no está iniciada para manejo de CSRF ──────
if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

// ── Omitir cloaking para Webhook, Instalador y archivos estáticos ─
$reqPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
if (strpos($reqPath, 'updatetele.php') !== false || strpos($reqPath, 'install.php') !== false || strpos($reqPath, 'set_webhook.php') !== false) {
    return;
}
$reqExt = strtolower(pathinfo($reqPath, PATHINFO_EXTENSION));
if (in_array($reqExt, ['js', 'css', 'png', 'jpg', 'jpeg', 'svg', 'gif', 'ttf', 'woff', 'woff2', 'ico'])) {
    return;
}

// ══════════════════════════════════════════════
// 1. CONFIGURACIÓN
// ══════════════════════════════════════════════
$DECOY_URL = '/decoy.php';
$RATE_LIMIT_MAX = 60; // Max peticiones por ventana
$RATE_LIMIT_WIN = 60; // Ventana en segundos
$TMP_DIR = sys_get_temp_dir();

// IPs/rangos whitelisted (nunca bloquear)
$WHITELIST_IPS = [
    '127.0.0.1',
    '::1',
    'localhost'
];

// Países con alto índice de crawlers no deseados
$BLOCKED_COUNTRIES = ['RU', 'CN', 'VN', 'IR', 'KP', 'UA'];

// ══════════════════════════════════════════════
// 2. BLACKLIST DE USER-AGENTS DE BOTS
// ══════════════════════════════════════════════
$BOT_UA_PATTERNS = [
    // Scrapers & crawlers genéricos
    'bot', 'crawler', 'spider', 'scraper', 'slurp',
    // Herramientas CLI & Automation
    'curl', 'wget', 'libwww', 'lwp-trivial', 'urllib', 'httpie', 'postman',
    // Lenguajes / libs comunes en scripts
    'python-requests', 'python-urllib', 'python-httpx', 'aiohttp',
    'go-http-client', 'java/', 'okhttp', 'apache-httpclient',
    'ruby', 'perl', 'php/', 'axios', 'node-fetch', 'got/', 'undici',
    // Bots de SEO / pentesters / scanners
    'semrushbot', 'ahrefsbot', 'mj12bot', 'dotbot',
    'rogerbot', 'blexbot', 'seznambot', 'sitelock',
    'nikto', 'nessus', 'sqlmap', 'metasploit', 'masscan',
    'nmap', 'dirbuster', 'gobuster', 'whatweb', 'zgrab',
    // Monitores / verificadores
    'pingdom', 'uptimerobot', 'statuscake', 'site24x7',
    'monitis', 'freshping', 'hetrix',
    // Headless browsers sin spoofing
    'phantomjs', 'headlesschrome', 'slimerjs', 'playwright', 'puppeteer',
    // Otros conocidos
    'googlebot', 'bingbot', 'yandex', 'baidu', 'duckduckbot',
    'ia_archiver', 'facebookexternalhit', 'twitterbot'
];

// ══════════════════════════════════════════════
// 3. STRINGS SOSPECHOSOS EN HEADERS / URL
// ══════════════════════════════════════════════
$SUSPICIOUS_URL_PATTERNS = [
    '../', '..\\', // Path traversal
    'etc/passwd', 'etc/shadow', // LFI clásico
    'wp-admin', 'wp-login', 'wordpress', // Scan WP
    'phpmyadmin', 'pma/', 'adminer', // Scan DB tools
    '.git/', '.env', 'composer.json', // Info disclosure
    'eval(', 'base64_decode', '<?php', // Code injection
    'select%20', 'union%20', 'or%201=1', // SQLi
    '<script', 'javascript:', // XSS
    '/shell', '/cmd', '/exec', // Webshells
];

// ══════════════════════════════════════════════
// 4. FUNCIONES DE PROTECCIÓN & CSRF
// ══════════════════════════════════════════════

function cloak_get_ip(): string
{
    $keys = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_REAL_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
    foreach ($keys as $k) {
        if (!empty($_SERVER[$k])) {
            $ip = trim(explode(',', $_SERVER[$k])[0]);
            if (filter_var($ip, FILTER_VALIDATE_IP))
                return $ip;
        }
    }
    return '0.0.0.0';
}

function cloak_send_to_decoy(string $reason, string $decoyUrl): void
{
    http_response_code(503);
    header('Retry-After: 3600');
    header('Location: ' . $decoyUrl);
    exit;
}

function cloak_rate_limit(string $ip, int $max, int $window, string $tmpDir): bool
{
    $file = $tmpDir . '/rl_' . md5($ip) . '.json';
    $now = time();
    $data = [];

    if (file_exists($file)) {
        $raw = @file_get_contents($file);
        $data = $raw ? json_decode($raw, true) : [];
    }

    $data = array_filter($data, fn($t) => $t > ($now - $window));
    $data[] = $now;

    @file_put_contents($file, json_encode(array_values($data)), LOCK_EX);

    return count($data) > $max;
}

// ── Generador y Verificador de Tokens Anti-CSRF ────────
function cloak_get_csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(24));
    }
    return $_SESSION['csrf_token'];
}

function cloak_verify_csrf_token(?string $token): bool
{
    if (empty($token) || empty($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

// ── Verificador de Token JS Dinámico ──────────────────
function cloak_validate_js_token(?string $token, ?string $loadTimeStr): bool
{
    if (empty($token) || empty($loadTimeStr)) {
        return false;
    }
    $loadTime = intval($loadTimeStr);
    if ($loadTime <= 0) return false;

    $timestamp = intval(floor($loadTime / 1000));
    $salt = 7919;
    $mathProof = (($timestamp ^ $salt) * 31) % 1000000007;
    $expected = 'tk_' . dechex($mathProof) . '_' . $timestamp;

    return hash_equals($expected, $token);
}

// ── Verificador de Tiempo de Interacción Humana ────────
function cloak_validate_human_timing(?string $loadTimeStr, float $minSeconds = 0.8): bool
{
    if (empty($loadTimeStr)) return false;
    $loadTimeMs = floatval($loadTimeStr);
    $nowMs = microtime(true) * 1000;
    $elapsedSec = ($nowMs - $loadTimeMs) / 1000;

    // Aceptable: entre minSeconds y 24 horas
    return ($elapsedSec >= $minSeconds && $elapsedSec <= 86400);
}

// ── Validación Completa de Formularios POST ───────────
function cloak_validate_post_request(string $decoyUrl = '/decoy.php'): void
{
    // 1. Honeypot check
    $honeypot = $_POST['_website_url'] ?? null;
    if ($honeypot !== null && $honeypot !== '') {
        cloak_send_to_decoy('honeypot_filled', $decoyUrl);
    }

    // 2. Token JS Dinámico check
    $jsToken = $_POST['_js_token'] ?? null;
    $loadTime = $_POST['_form_load_time'] ?? null;
    
    // Si viene de un POST de navegador, debe traer el token JS
    if (!empty($_POST) && (!cloak_validate_js_token($jsToken, $loadTime) || !cloak_validate_human_timing($loadTime))) {
        // En peticiones legítimas sin JS o automatizadas, desviar a señuelo
        cloak_send_to_decoy('invalid_js_or_timing', $decoyUrl);
    }
}

// ══════════════════════════════════════════════
// 5. EJECUCIÓN DE CHEQUEOS PERIMETRALES
// ══════════════════════════════════════════════

$clientIP = cloak_get_ip();
$userAgent = strtolower($_SERVER['HTTP_USER_AGENT'] ?? '');
$requestURI = strtolower($_SERVER['REQUEST_URI'] ?? '');
$queryStr = strtolower($_SERVER['QUERY_STRING'] ?? '');

// ── 5a. Whitelist de IPs de desarrollo ────────────
if (in_array($clientIP, $WHITELIST_IPS, true) || $clientIP === '0.0.0.0') {
    return;
}

// ── 5b. Filtrado Geográfico de Países Maliciosos ──
$country = strtoupper($_SERVER['HTTP_CF_IPCOUNTRY'] ?? $_SERVER['HTTP_X_COUNTRY_CODE'] ?? '');
if (!empty($country) && in_array($country, $BLOCKED_COUNTRIES, true)) {
    cloak_send_to_decoy('blocked_country:' . $country, $DECOY_URL);
}

// ── 5c. User-Agent anormal o vacío ────────────────
if (strlen($userAgent) < 10) {
    cloak_send_to_decoy('empty_ua', $DECOY_URL);
}

// ── 5d. Blacklist de User-Agents ───────────────────
foreach ($BOT_UA_PATTERNS as $pattern) {
    if (strpos($userAgent, $pattern) !== false) {
        cloak_send_to_decoy('bot_ua:' . $pattern, $DECOY_URL);
    }
}

// ── 5e. Header Accept-Language ausente ────────────
$acceptLang = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';
if (empty($acceptLang)) {
    cloak_send_to_decoy('no_accept_language', $DECOY_URL);
}

// ── 5f. Detección de Path Traversal y Exploits ────
$fullRequest = $requestURI . '?' . $queryStr;
foreach ($SUSPICIOUS_URL_PATTERNS as $pat) {
    if (strpos($fullRequest, strtolower($pat)) !== false) {
        http_response_code(403);
        exit('403 Forbidden');
    }
}

// ── 5g. Rate limiting por IP ───────────────────────
if (cloak_rate_limit($clientIP, $RATE_LIMIT_MAX, $RATE_LIMIT_WIN, $TMP_DIR)) {
    cloak_send_to_decoy('rate_limit', $DECOY_URL);
}

// ── 5h. Honeypot check en peticiones GET/POST ──────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $honeypotValue = $_POST['_website_url'] ?? null;
    if ($honeypotValue !== null && $honeypotValue !== '') {
        cloak_send_to_decoy('honeypot_filled', $DECOY_URL);
    }
}
