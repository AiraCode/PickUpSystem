<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pickup_order_costs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pickup_order_id')->constrained('pickup_orders')->cascadeOnDelete();
            $table->enum('cost_type', ['fuel', 'toll', 'driver_fee', 'other'])->default('other');
            $table->string('description')->nullable();
            $table->decimal('amount', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pickup_order_costs');
    }
};
