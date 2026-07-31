<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::create('/api/admin/pengiriman', 'GET');
// bypass auth by setting actingAs
$user = App\Models\User::first();
if($user) {
    Auth::login($user);
}
$response = $kernel->handle($request);
echo $response->getContent();
