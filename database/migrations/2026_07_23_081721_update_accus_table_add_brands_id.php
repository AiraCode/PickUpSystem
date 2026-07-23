<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('accus', function (Blueprint $table) {
            $table->foreignId('brands_id')->nullable()->after('img')->constrained('brands')->onDelete('cascade');
        });

        $existingBrands = DB::table('accus')
            ->whereNotNull('brand')
            ->where('brand', '!=', '')
            ->distinct()
            ->pluck('brand');

        foreach ($existingBrands as $brandName) {
            $brandId = DB::table('brands')->insertGetId([
                'name' => $brandName,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('accus')
                ->where('brand', $brandName)
                ->update(['brands_id' => $brandId]);
        }

        Schema::table('accus', function (Blueprint $table) {
            $table->dropColumn('brand');
        });
    }

    public function down(): void
    {
        Schema::table('accus', function (Blueprint $table) {
            $table->string('brand', 45)->nullable()->after('name');
        });

        $accus = DB::table('accus')
            ->join('brands', 'accus.brands_id', '=', 'brands.id')
            ->select('accus.id', 'brands.name as brand_name')
            ->get();

        foreach ($accus as $accu) {
            DB::table('accus')
                ->where('id', $accu->id)
                ->update(['brand' => $accu->brand_name]);
        }

        Schema::table('accus', function (Blueprint $table) {
            $table->dropForeign(['brands_id']);
            $table->dropColumn('brands_id');
        });
    }
};