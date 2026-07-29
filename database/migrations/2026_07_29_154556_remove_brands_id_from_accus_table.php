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
        Schema::table('accus', function (Blueprint $table) {
            $table->dropForeign(['brands_id']);
            $table->dropColumn('brands_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accus', function (Blueprint $table) {
            $table->foreignId('brands_id')->nullable()->after('img')->constrained('brands')->onDelete('cascade');
        });
    }
};
