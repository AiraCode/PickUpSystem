<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->text('account_number')->change();
            $table->text('account_name')->change();
            $table->text('phone_number')->change();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->text('smtp_password')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('account_number', 45)->change();
            $table->string('account_name', 100)->change();
            $table->string('phone_number', 45)->change();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('smtp_password', 255)->nullable()->change();
        });
    }
};
