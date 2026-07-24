<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('settings')) {
            Schema::create('settings', function (Blueprint $table) {
                $table->string('key')->primary();
                $table->text('value')->nullable();
                $table->timestamps();
            });
        }

        Schema::table('cities', function (Blueprint $table) {
            if (!Schema::hasColumn('cities', 'percentage')) {
                $table->decimal('percentage', 5, 2)->default(80.00)->after('name');
            }
        });

        Schema::table('accus', function (Blueprint $table) {
            if (Schema::hasColumn('accus', 'img')) {
                $table->dropColumn('img');
            }
            if (!Schema::hasColumn('accus', 'berat_kering')) {
                $table->decimal('berat_kering', 10, 2)->default(0)->after('name');
            }
        });

        Schema::table('cities_has_accus', function (Blueprint $table) {
            if (Schema::hasColumn('cities_has_accus', 'price')) {
                $table->dropColumn('price');
            }
            if (Schema::hasColumn('cities_has_accus', 'percentage')) {
                $table->dropColumn('percentage');
            }
        });
    }

    public function down(): void
    {
        Schema::table('cities_has_accus', function (Blueprint $table) {
            if (!Schema::hasColumn('cities_has_accus', 'price')) {
                $table->integer('price')->default(0);
            }
        });

        Schema::table('accus', function (Blueprint $table) {
            if (Schema::hasColumn('accus', 'berat_kering')) {
                $table->dropColumn('berat_kering');
            }
            if (!Schema::hasColumn('accus', 'img')) {
                $table->string('img', 255)->nullable()->after('name');
            }
        });

        Schema::table('cities', function (Blueprint $table) {
            if (Schema::hasColumn('cities', 'percentage')) {
                $table->dropColumn('percentage');
            }
        });

        Schema::dropIfExists('settings');
    }
};
