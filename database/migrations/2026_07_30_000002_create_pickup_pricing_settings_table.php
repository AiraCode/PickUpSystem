<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pickup_pricing_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('initial_fee', 12, 2)->default(5000);
            $table->decimal('distance_rate', 12, 2)->default(2300);
            $table->decimal('time_rate', 12, 2)->default(25);
            $table->decimal('demand_multiplier', 5, 4)->default(1.0000);
            $table->decimal('weather_multiplier', 5, 4)->default(1.0000);
            $table->decimal('traffic_multiplier', 5, 4)->default(1.0000);
            $table->decimal('event_multiplier', 5, 4)->default(1.0000);
            $table->decimal('total_multiplier', 8, 4)->default(1.0000);
            $table->boolean('is_active')->default(false);
            $table->string('updated_by')->nullable();
            $table->timestamps();
        });

        // Seed initial active configuration
        \DB::table('pickup_pricing_settings')->insert([
            'initial_fee'        => 5000,
            'distance_rate'      => 2300,
            'time_rate'          => 25,
            'demand_multiplier'  => 1.0000,
            'weather_multiplier' => 1.0000,
            'traffic_multiplier' => 1.0000,
            'event_multiplier'   => 1.0000,
            'total_multiplier'   => 1.0000,
            'is_active'          => true,
            'updated_by'         => 'System',
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('pickup_pricing_settings');
    }
};
