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
        Schema::create('new_accus', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('brands_id');
            $table->string('name', 45);
            $table->decimal('price', 15, 2);
            $table->timestamps();
            
            $table->foreign('brands_id')->references('id')->on('brands');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('new_accus');
    }
};
