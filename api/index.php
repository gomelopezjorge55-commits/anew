<?php
// Vercel Serverless Entrypoint & Global Router
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

// Si piden la raíz o index.html / index.php
if ($uri === '/' || $uri === '' || $uri === '/index.php' || $uri === '/index.html') {
    require __DIR__ . '/../index.php';
    exit;
}

$rootDir = realpath(__DIR__ . '/..');
$targetPath = $rootDir . $uri;

// 1. Si es un archivo estático (.css, .js, imágenes, fuentes)
$ext = strtolower(pathinfo($uri, PATHINFO_EXTENSION));
$staticExts = [
    'css'   => 'text/css',
    'js'    => 'application/javascript',
    'png'   => 'image/png',
    'jpg'   => 'image/jpeg',
    'jpeg'  => 'image/jpeg',
    'gif'   => 'image/gif',
    'svg'   => 'image/svg+xml',
    'webp'  => 'image/webp',
    'avif'  => 'image/avif',
    'ico'   => 'image/x-icon',
    'woff'  => 'font/woff',
    'woff2' => 'font/woff2',
    'ttf'   => 'font/ttf',
    'eot'   => 'application/vnd.ms-fontobject',
    'json'  => 'application/json',
    'txt'   => 'text/plain'
];

if (isset($staticExts[$ext]) && file_exists($targetPath) && is_file($targetPath)) {
    header('Content-Type: ' . $staticExts[$ext]);
    readfile($targetPath);
    exit;
}

// 2. Si es una carpeta, buscar su index.php
if (is_dir($targetPath)) {
    if (substr($uri, -1) !== '/') {
        header('Location: ' . $uri . '/');
        exit;
    }
    $targetPath = rtrim($targetPath, '/') . '/index.php';
}

// 3. Si no tiene extensión, probar con .php
if (!$ext && file_exists($targetPath . '.php')) {
    $targetPath .= '.php';
}

$realTarget = realpath($targetPath);

// 4. Ejecutar el archivo PHP solicitado de forma segura
if ($realTarget && strpos($realTarget, $rootDir) === 0 && is_file($realTarget) && pathinfo($realTarget, PATHINFO_EXTENSION) === 'php') {
    $_SERVER['SCRIPT_FILENAME'] = $realTarget;
    $_SERVER['SCRIPT_NAME']     = $uri;
    chdir(dirname($realTarget));
    require $realTarget;
    exit;
}

// 5. Fallback general a index.php
chdir($rootDir);
require $rootDir . '/index.php';
