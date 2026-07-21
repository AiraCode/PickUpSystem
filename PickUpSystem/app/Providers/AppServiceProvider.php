<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Payment;
use App\Models\PickupOrder;
use App\Models\PickupRequest;
use App\Models\Vehicle;
use App\Policies\CategoryPolicy;
use App\Policies\PaymentPolicy;
use App\Policies\PickupOrderPolicy;
use App\Policies\PickupRequestPolicy;
use App\Policies\VehiclePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // -------------------------------------------------------
        // Gates — untuk pengecekan role secara umum
        // Penggunaan: Gate::allows('admin-access')
        //             @can('admin-access') di Blade
        // -------------------------------------------------------

        Gate::define('admin-access', function ($user) {
            return $user->isAdmin();
        });

        Gate::define('driver-access', function ($user) {
            return $user->isDriver();
        });

        Gate::define('customer-access', function ($user) {
            return $user->isCustomer();
        });

        // Admin bisa bypass semua policy checks (super admin)
        Gate::before(function ($user, $ability) {
            if ($user->isAdmin()) {
                return true;
            }
        });

        // -------------------------------------------------------
        // Policy Registration
        // -------------------------------------------------------

        Gate::policy(Category::class, CategoryPolicy::class);
        Gate::policy(PickupRequest::class, PickupRequestPolicy::class);
        Gate::policy(PickupOrder::class, PickupOrderPolicy::class);
        Gate::policy(Vehicle::class, VehiclePolicy::class);
        Gate::policy(Payment::class, PaymentPolicy::class);
    }
}
