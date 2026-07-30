<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('new_accus_orders', function (Blueprint $table) {
            $table->id();
            $table->integer('orders_id')->index();
            $table->integer('new_accus_id')->index();
            $table->integer('quantity')->default(1);
            $table->integer('price')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('new_accus_orders');
    }
};
