<?php
// god/auth.php — Verificación de autenticación via cookie HMAC
require_once __DIR__ . '/config_admin.php';

$cookieToken = $_COOKIE['god_auth'] ?? '';
$validToken = hash_hmac('sha256', ADMIN_USER, ADMIN_SECRET);

if ($cookieToken !== $validToken) {
    header('Location: /god/index.php');
    exit();
}

// Logout
if (isset($_GET['logout'])) {
    $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || ($_SERVER['SERVER_PORT'] ?? '') == 443 || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
    setcookie('god_auth', '', ['expires' => time() - 3600, 'path' => '/', 'secure' => $isSecure, 'httponly' => true, 'samesite' => 'Lax']);
    header('Location: /god/index.php');
    exit();
}