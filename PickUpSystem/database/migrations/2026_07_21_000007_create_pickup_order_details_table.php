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
        Schema::create('pickup_order_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pickup_order_id')->constrained('pickup_orders')->cascadeOnDelete();
            $table->foreignId('pickup_request_id')->constrained('pickup_requests')->restrictOnDelete();
            $table->integer('sequence_order');
            $table->enum('status', ['pending', 'picked_up', 'skipped'])->default('pending');
            $table->timestamp('picked_up_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pickup_order_details');
    }
};
