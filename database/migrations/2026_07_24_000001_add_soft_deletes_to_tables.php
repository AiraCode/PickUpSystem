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
        Schema::table('cities', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('accus', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('storages', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('cities_has_accus', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('accus', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('storages', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('cities_has_accus', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
