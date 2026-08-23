<?php
// Vercel Serverless Entrypoint & Global Router
ini_set('display_errors', '0');
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING & ~E_DEPRECATED);

$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

// Si piden la raíz o index.html / index.php
if ($uri === '/' || $uri === '' || $uri === '/index.php' || $uri === '/index.html') {
    require __DIR__ . '/../index.php';
    exit;
}

$rootDir = realpath(__DIR__ . '/..');
$targetPath = $rootDir . $uri;

// 1. Si el archivo existe físicamente y NO es .php, servirlo directamente como estático
if (file_exists($targetPath) && is_file($targetPath)) {
    $realTarget = realpath($targetPath);
    if ($realTarget && strpos($realTarget, $rootDir) === 0) {
        $ext = strtolower(pathinfo($realTarget, PATHINFO_EXTENSION));
        if ($ext !== 'php') {
            $mimeTypes = [
                'css'      => 'text/css',
                'js'       => 'application/javascript',
                'descarga' => (strpos($realTarget, '.css') !== false ? 'text/css' : 'application/javascript'),
                'png'      => 'image/png',
                'jpg'      => 'image/jpeg',
                'jpeg'     => 'image/jpeg',
                'gif'      => 'image/gif',
                'svg'      => 'image/svg+xml',
                'webp'     => 'image/webp',
                'avif'     => 'image/avif',
                'ico'      => 'image/x-icon',
                'woff'     => 'font/woff',
                'woff2'    => 'font/woff2',
                'ttf'      => 'font/ttf',
                'eot'      => 'application/vnd.ms-fontobject',
                'json'     => 'application/json',
                'txt'      => 'text/plain'
            ];

            $contentType = $mimeTypes[$ext] ?? 'application/octet-stream';
            header('Content-Type: ' . $contentType);
            header('Cache-Control: public, max-age=86400');
            readfile($realTarget);
            exit;
        }
    }
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
$ext = strtolower(pathinfo($targetPath, PATHINFO_EXTENSION));
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
