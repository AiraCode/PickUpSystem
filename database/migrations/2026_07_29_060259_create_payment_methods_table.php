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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
        
        // Insert defaults
        DB::table('payment_methods')->insert([
            ['code' => 'cod', 'name' => 'COD (Bayar di Tempat)', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'transfer', 'name' => 'Transfer Bank', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'qris', 'name' => 'QRIS', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
