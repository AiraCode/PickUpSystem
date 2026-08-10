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
        Schema::table('users', function (Blueprint $table) {
            $table->string('smtp_email')->nullable()->after('email');
            $table->string('smtp_password')->nullable()->after('smtp_email');
            $table->string('smtp_host')->nullable()->after('smtp_password');
            $table->unsignedSmallInteger('smtp_port')->nullable()->after('smtp_host');
            $table->string('smtp_encryption')->nullable()->after('smtp_port');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['smtp_email', 'smtp_password', 'smtp_host', 'smtp_port', 'smtp_encryption']);
        });
    }
};
