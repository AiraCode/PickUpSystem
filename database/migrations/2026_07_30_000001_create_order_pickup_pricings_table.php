<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_pickup_pricings', function (Blueprint $table) {
            $table->integer('id')->primary()->autoIncrement(false);
            $table->integer('orders_id')->unique();
            $table->integer('storages_id')->nullable();

            // Route data
            $table->decimal('route_distance_km', 8, 2)->comment('Road distance in km (OSRM or Haversine*1.35 fallback), rounded up to 1 decimal');
            $table->integer('travel_time_seconds')->comment('Internally calculated from distance ÷ courier_speed; never shown to users');

            // Pricing breakdown
            $table->decimal('base_price', 12, 2)->comment('initial_fee + (distance * distance_rate) + (time * time_rate)');
            $table->decimal('multiplier', 5, 4)->default(1.0000)->comment('DynamicPricingService multiplier: demand × weather × traffic × event');
            $table->integer('final_pickup_fee')->comment('base_price * multiplier, rounded up to nearest Rp1.000');

            $table->timestamps();

            $table->foreign('orders_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('storages_id')->references('id')->on('storages')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_pickup_pricings');
    }
};
