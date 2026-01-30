<?php

/**
 * Custom router for the PHP built-in server (php artisan serve).
 * When the router script lives in the project root, the PHP server's document root
 * becomes the project root, so we must load public/index.php by path (__DIR__),
 * not by getcwd() which may be project root and has no index.php.
 */

$scriptDir = __DIR__;
$publicPath = $scriptDir.'/public';
$indexPath = $publicPath.'/index.php';

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? ''
);

// Default Laravel behavior: serve static file from public/ if it exists, otherwise Laravel
if ($uri !== '/' && file_exists($publicPath.$uri)) {
    return false;
}

$formattedDateTime = date('D M j H:i:s Y');
$requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$remoteAddress = ($_SERVER['REMOTE_ADDR'] ?? 'unknown').':'.($_SERVER['REMOTE_PORT'] ?? '0');

file_put_contents('php://stdout', "[$formattedDateTime] $remoteAddress [$requestMethod] URI: $uri\n");

require_once $indexPath;
