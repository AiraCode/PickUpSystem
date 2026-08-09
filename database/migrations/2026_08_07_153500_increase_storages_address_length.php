<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('storages') && Schema::hasColumn('storages', 'address')) {
            Schema::table('storages', function (Blueprint $table) {
                $table->string('address', 255)->nullable(false)->change();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('storages') && Schema::hasColumn('storages', 'address')) {
            Schema::table('storages', function (Blueprint $table) {
                $table->string('address', 45)->nullable(false)->change();
            });
        }
    }
};
