<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_pickup_pricings', function (Blueprint $table) {
            // Full pricing configuration snapshot — captured at order creation time, never updated.
            $table->decimal('initial_fee', 12, 2)->nullable()->after('storages_id');
            $table->decimal('distance_rate', 12, 2)->nullable()->after('initial_fee');
            $table->decimal('time_rate', 12, 2)->nullable()->after('distance_rate');
            $table->decimal('demand_multiplier', 5, 4)->default(1.0000)->after('time_rate');
            $table->decimal('weather_multiplier', 5, 4)->default(1.0000)->after('demand_multiplier');
            $table->decimal('traffic_multiplier', 5, 4)->default(1.0000)->after('weather_multiplier');
            $table->decimal('event_multiplier', 5, 4)->default(1.0000)->after('traffic_multiplier');
        });
    }

    public function down(): void
    {
        Schema::table('order_pickup_pricings', function (Blueprint $table) {
            $table->dropColumn([
                'initial_fee',
                'distance_rate',
                'time_rate',
                'demand_multiplier',
                'weather_multiplier',
                'traffic_multiplier',
                'event_multiplier',
            ]);
        });
    }
};
