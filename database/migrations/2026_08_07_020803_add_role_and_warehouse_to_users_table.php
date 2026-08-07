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
        if (Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('role');
            });
        }
        if (Schema::hasColumn('users', 'warehouse_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('warehouse_id');
            });
        }

        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['central', 'warehouse'])->default('central')->after('password');
            $table->integer('warehouse_id')->nullable()->after('role');

            $table->foreign('warehouse_id')->references('id')->on('storages')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['warehouse_id']);
            $table->dropColumn(['role', 'warehouse_id']);
        });
    }
};
