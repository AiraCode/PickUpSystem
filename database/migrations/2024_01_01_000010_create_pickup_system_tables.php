<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');

        Schema::create('cities', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('name', 45);
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
        });

        Schema::create('accus', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('brand', 45);
            $table->string('name', 45);
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
        });

        Schema::create('users', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('name', 45);
            $table->string('password', 255);
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
        });

        Schema::create('banks', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('name', 45);
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
        });

        Schema::create('customers', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('name', 100);
            $table->string('address', 500);
            $table->string('address_note', 500)->nullable();
            $table->decimal('lat', 11, 8); 
            $table->decimal('long', 11, 8);
            $table->string('ktp', 45);
            $table->string('account_name', 100);
            $table->string('account_number', 45);
            $table->string('phone_number', 45);
            $table->tinyInteger('flag');
            $table->integer('banks_id');
            $table->timestamp('created_at');
            $table->timestamp('updated_at');

            $table->foreign('banks_id')->references('id')->on('banks');
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->integer('cities_id');
            $table->string('pickup_address', 45);
            $table->string('pickup_address_note', 45);
            $table->decimal('pickup_lat', 11, 8);
            $table->decimal('pickup_long', 11, 8);
            $table->string('status', 45);
            $table->integer('customers_id');
            $table->timestamp('created_at');
            $table->timestamp('updated_at');

            $table->foreign('cities_id')->references('id')->on('cities');
            $table->foreign('customers_id')->references('id')->on('customers');
        });

        Schema::create('receipts', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('receipt_number', 45);
            $table->dateTime('date');
            $table->string('status', 45);
            $table->integer('price_received');
            $table->integer('price_owed')->nullable();
            $table->integer('users_id');
            $table->integer('orders_id')->unique();
            $table->timestamp('created_at');
            $table->timestamp('updated_at');

            $table->foreign('users_id')->references('id')->on('users');
            $table->foreign('orders_id')->references('id')->on('orders');
        });

        Schema::create('accus_has_receipts', function (Blueprint $table) {
            $table->integer('accus_id');
            $table->integer('receipts_id');
            $table->integer('amount');
            $table->timestamp('created_at');
            $table->timestamp('updated_at');

            $table->primary(['accus_id', 'receipts_id']);
            $table->foreign('accus_id')->references('id')->on('accus');
            $table->foreign('receipts_id')->references('id')->on('receipts');
        });

        Schema::create('storages', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('name', 45);
            $table->string('address', 45);
            $table->decimal('lat', 11, 8);
            $table->decimal('long', 11, 8);
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
        });

        Schema::create('shipments', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->integer('storages_id');
            $table->string('status', 45);
            $table->dateTime('pickup_date');
            $table->dateTime('received_date');
            $table->integer('receipts_id')->unique();
            $table->timestamp('created_at');
            $table->timestamp('updated_at');

            $table->foreign('storages_id')->references('id')->on('storages');
            $table->foreign('receipts_id')->references('id')->on('receipts');
        });

        Schema::create('cities_has_accus', function (Blueprint $table) {
            $table->integer('cities_id');
            $table->integer('accus_id');
            $table->integer('price');
            $table->timestamp('created_at');
            $table->timestamp('updated_at');

            $table->primary(['cities_id', 'accus_id']);
            $table->foreign('cities_id')->references('id')->on('cities');
            $table->foreign('accus_id')->references('id')->on('accus');
        });

        Schema::create('transfers', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->integer('receipts_id')->unique();
            $table->integer('users_id');
            $table->decimal('amount', 15, 2);
            $table->dateTime('transfer_date');
            $table->string('status', 45);
            $table->string('proof_image', 45);
            $table->timestamp('created_at');
            $table->timestamp('updated_at');

            $table->foreign('receipts_id')->references('id')->on('receipts');
            $table->foreign('users_id')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transfers');
        Schema::dropIfExists('cities_has_accus');
        Schema::dropIfExists('shipments');
        Schema::dropIfExists('storages');
        Schema::dropIfExists('accus_has_receipts');
        Schema::dropIfExists('receipts');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('banks');
        Schema::dropIfExists('users');
        Schema::dropIfExists('accus');
        Schema::dropIfExists('cities');
    }
};
