<?php

$tmpStorage = '/tmp/storage';
$directories = [
    $tmpStorage . '/framework/views',
    $tmpStorage . '/framework/cache/data',
    $tmpStorage . '/framework/sessions',
    $tmpStorage . '/logs',
    $tmpStorage . '/bootstrap/cache',
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

$_ENV['APP_STORAGE_PATH'] = $tmpStorage;
putenv("APP_STORAGE_PATH={$tmpStorage}");

$_ENV['APP_CONFIG_CACHE'] = $tmpStorage . '/bootstrap/cache/config.php';
$_ENV['APP_SERVICES_CACHE'] = $tmpStorage . '/bootstrap/cache/services.php';
$_ENV['APP_PACKAGES_CACHE'] = $tmpStorage . '/bootstrap/cache/packages.php';
$_ENV['APP_ROUTES_CACHE'] = $tmpStorage . '/bootstrap/cache/routes.php';
$_ENV['VIEW_COMPILED_PATH'] = $tmpStorage . '/framework/views';

if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $_SERVER['HTTPS'] = 'on';
}

require __DIR__ . '/../public/index.php';