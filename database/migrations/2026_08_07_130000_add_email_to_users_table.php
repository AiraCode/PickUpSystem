<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'email')) {
                $table->string('email')->nullable()->after('name');
                $table->unique('email');
            }
        });

        $users = DB::table('users')->get(['id', 'name', 'email']);
        foreach ($users as $user) {
            if (empty($user->email)) {
                $slug = preg_replace('/[^a-z0-9]+/i', '.', strtolower($user->name));
                $email = trim($slug, '.') . '.' . $user->id . '@example.com';
                DB::table('users')->where('id', $user->id)->update(['email' => $email]);
            }
        }

        DB::statement('ALTER TABLE `users` MODIFY `email` VARCHAR(255) NOT NULL');
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'email')) {
                $table->dropUnique(['email']);
                $table->dropColumn('email');
            }
        });
    }
};
