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
            $table->integer('storages_id')->nullable()->after('cities_id');
            $table->string('warehouse_proof', 255)->nullable()->after('accu_ktp');

            $table->foreign('storages_id')->references('id')->on('storages')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['storages_id']);
            $table->dropColumn(['storages_id', 'warehouse_proof']);
        });
    }
};

