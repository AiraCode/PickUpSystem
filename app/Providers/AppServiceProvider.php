<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (request()->hasHeader('x-forwarded-proto') || config('app.env') !== 'local') {
            URL::forceScheme('https');
        }
    }
}
