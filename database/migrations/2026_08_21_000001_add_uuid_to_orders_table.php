<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('orders', 'uuid')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->uuid('uuid')->nullable()->after('id')->unique();
            });

            // Populate existing records with UUIDs
            $orders = DB::table('orders')->whereNull('uuid')->get();
            foreach ($orders as $order) {
                DB::table('orders')->where('id', $order->id)->update([
                    'uuid' => (string) Str::uuid(),
                ]);
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('orders', 'uuid')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('uuid');
            });
        }
    }
};
