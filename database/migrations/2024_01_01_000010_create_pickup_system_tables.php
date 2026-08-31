<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
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

        Schema::create('cities', function (Blueprint $table) {
            $table->increments('id'); // INT AUTO_INCREMENT PRIMARY KEY
            $table->string('name', 45);
            $table->timestamps();
        });

        Schema::create('accus', function (Blueprint $table) {
            $table->increments('id');
            $table->string('brand', 45);
            $table->string('name', 45);
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 45);
            $table->string('password', 255);
            $table->timestamps();
        });

        Schema::create('banks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 45);
            $table->timestamps();
        });

        Schema::create('customers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->longText('address');
            $table->longText('address_note')->nullable();
            $table->decimal('lat', 11, 8);
            $table->decimal('long', 11, 8);
            $table->string('ktp', 45);
            $table->string('account_name', 100);
            $table->string('account_number', 45);
            $table->string('phone_number', 45);
            $table->tinyInteger('flag');
            $table->unsignedInteger('banks_id'); // INT UNSIGNED (cocok dengan increments)
            $table->timestamps();

            $table->foreign('banks_id')->references('id')->on('banks');
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('cities_id');
            $table->string('pickup_address', 45);
            $table->string('pickup_address_note', 45);
            $table->decimal('pickup_lat', 11, 8);
            $table->decimal('pickup_long', 11, 8);
            $table->string('status', 45);
            $table->unsignedInteger('customers_id');
            $table->timestamps();

            $table->foreign('cities_id')->references('id')->on('cities');
            $table->foreign('customers_id')->references('id')->on('customers');
        });

        Schema::create('receipts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('receipt_number', 45);
            $table->dateTime('date');
            $table->string('status', 45);
            $table->integer('price_received');
            $table->integer('price_owed')->nullable();
            $table->unsignedInteger('users_id');
            $table->unsignedInteger('orders_id')->unique();
            $table->timestamps();

            $table->foreign('users_id')->references('id')->on('users');
            $table->foreign('orders_id')->references('id')->on('orders');
        });

        Schema::create('accus_has_receipts', function (Blueprint $table) {
            $table->unsignedInteger('accus_id');
            $table->unsignedInteger('receipts_id');
            $table->integer('amount');
            $table->timestamps();

            $table->primary(['accus_id', 'receipts_id']);
            $table->foreign('accus_id')->references('id')->on('accus');
            $table->foreign('receipts_id')->references('id')->on('receipts');
        });

        Schema::create('storages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 45);
            $table->string('address', 45);
            $table->decimal('lat', 11, 8);
            $table->decimal('long', 11, 8);
            $table->timestamps();
        });

        Schema::create('shipments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('storages_id');
            $table->string('status', 45);
            $table->dateTime('pickup_date');
            $table->dateTime('received_date');
            $table->unsignedInteger('receipts_id')->unique();
            $table->timestamps();

            $table->foreign('storages_id')->references('id')->on('storages');
            $table->foreign('receipts_id')->references('id')->on('receipts');
        });

        Schema::create('cities_has_accus', function (Blueprint $table) {
            $table->unsignedInteger('cities_id');
            $table->unsignedInteger('accus_id');
            $table->integer('price');
            $table->timestamps();

            $table->primary(['cities_id', 'accus_id']);
            $table->foreign('cities_id')->references('id')->on('cities');
            $table->foreign('accus_id')->references('id')->on('accus');
        });

        Schema::create('transfers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('receipts_id')->unique();
            $table->unsignedInteger('users_id');
            $table->decimal('amount', 15, 2);
            $table->dateTime('transfer_date');
            $table->string('status', 45);
            $table->string('proof_image', 45);
            $table->timestamps();

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