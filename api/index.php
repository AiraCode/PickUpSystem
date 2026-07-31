<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

$storagePath = '/tmp/storage';
$viewsPath = '/tmp/views';

if (!is_dir($viewsPath)) {
    mkdir($viewsPath, 0755, true);
}

if (!is_dir($storagePath)) {
    mkdir($storagePath . '/framework/cache/data', 0755, true);
    mkdir($storagePath . '/framework/sessions', 0755, true);
    mkdir($storagePath . '/logs', 0755, true);
}

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$app->useStoragePath($storagePath);

$app->booted(function () use ($app, $viewsPath) {
    $app['config']->set('view.compiled', $viewsPath);
});

if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $_SERVER['HTTPS'] = 'on';
}

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
);

$response->send();

$kernel->terminate($request, $response);