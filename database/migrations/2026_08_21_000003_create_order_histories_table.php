<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('order_histories')) {
            Schema::create('order_histories', function (Blueprint $table) {
                $table->id();
                $table->integer('order_id');
                $table->integer('user_id')->nullable();
                $table->string('actor_type', 45)->default('admin'); // 'admin', 'customer', 'system'
                $table->string('action_type', 45); // 'status_change', 'items_change'
                $table->json('old_values')->nullable();
                $table->json('new_values')->nullable();
                $table->text('description')->nullable();
                $table->timestamps();

                $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('order_histories');
    }
};
