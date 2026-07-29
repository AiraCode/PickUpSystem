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
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('order_type', ['sell', 'trade_in'])->default('sell')->after('status');
            $table->unsignedBigInteger('new_accus_id')->nullable()->after('order_type');
            $table->string('payment_method', 45)->nullable()->after('new_accus_id');
            
            $table->foreign('new_accus_id')->references('id')->on('new_accus');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['new_accus_id']);
            $table->dropColumn(['order_type', 'new_accus_id', 'payment_method']);
        });
    }
};
