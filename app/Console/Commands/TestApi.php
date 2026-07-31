<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Admin\PickupPricingController;

class TestApi extends Command {
    protected $signature = 'test:api';
    public function handle() {
        $controller = new PickupPricingController();
        $request = Request::create('/api/admin/pengiriman', 'GET');
        // act as admin
        $user = \App\Models\User::first();
        \Auth::login($user);
        
        try {
            $response = $controller->index($request);
            $this->info("Response Content: " . $response->getContent());
        } catch (\Exception $e) {
            $this->error("Exception: " . $e->getMessage() . "\n" . $e->getTraceAsString());
        }
    }
}
