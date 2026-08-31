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
                $table->increments('id'); // Konsisten memakai INT AUTO_INCREMENT
                $table->unsignedInteger('order_id'); // INT UNSIGNED agar cocok dengan orders.id
                $table->unsignedInteger('user_id')->nullable(); // INT UNSIGNED agar cocok dengan users.id
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