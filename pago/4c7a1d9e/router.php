<?php
// Single Entrypoint Router for Vercel Serverless (Hobby Plan Optimization)
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

// Serve static assets cleanly if requested
$ext = strtolower(pathinfo($uri, PATHINFO_EXTENSION));
if (in_array($ext, ['js', 'css', 'png', 'jpg', 'jpeg', 'svg', 'gif', 'ttf', 'woff', 'woff2', 'ico', 'map', 'crswap'])) {
    $targetFile = __DIR__ . $uri;
    if (file_exists($targetFile) && is_file($targetFile)) {
        $mimeTypes = [
            'css' => 'text/css',
            'js' => 'application/javascript',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'svg' => 'image/svg+xml',
            'gif' => 'image/gif',
            'ttf' => 'font/ttf',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2'
        ];
        if (isset($mimeTypes[$ext])) {
            header('Content-Type: ' . $mimeTypes[$ext]);
        }
        readfile($targetFile);
        exit;
    }
    http_response_code(404);
    exit;
}

if ($uri === '/' || $uri === '' || $uri === '/index.php') {
    require __DIR__ . '/index.php';
    exit;
}

$filePath = __DIR__ . $uri;

// Directory index fallback
if (is_dir($filePath)) {
    if (substr($uri, -1) !== '/') {
        header('Location: ' . $uri . '/');
        exit;
    }
    $filePath = rtrim($filePath, '/') . '/index.php';
}

// Append .php if omitted
if (!$ext && file_exists($filePath . '.php')) {
    $filePath .= '.php';
}

$realBase = realpath(__DIR__);
$realFile = realpath($filePath);

// Security check: ensure target file is within project directory and is a PHP file
if ($realFile && strpos($realFile, $realBase) === 0 && is_file($realFile) && pathinfo($realFile, PATHINFO_EXTENSION) === 'php') {
    $_SERVER['SCRIPT_FILENAME'] = $realFile;
    $_SERVER['SCRIPT_NAME']     = $uri;
    chdir(dirname($realFile));
    require $realFile;
    exit;
}

// Fallback to index.php
require __DIR__ . '/index.php';
