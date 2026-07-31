<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

$user = \App\Models\User::first();
$token = $user->createToken('test-token')->plainTextToken;

$response = \Illuminate\Support\Facades\Http::withToken($token)
    ->acceptJson()
    ->get('http://pickupsystem.test/api/admin/pengiriman');

echo "Status: " . $response->status() . "\n";
echo "Body: " . $response->body() . "\n";
