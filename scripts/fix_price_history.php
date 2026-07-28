<?php
// Temporary script to normalize LME values in price_histories
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
// bootstrap finished

use Illuminate\Support\Facades\DB;
use App\Models\Setting;

$currentLme = Setting::getValue('lme', 2100);
$rows = DB::table('price_histories')->whereNull('LME')->orWhere('LME', 0)->get();
$count = 0;
foreach ($rows as $r) {
    if ($r->type === 'lme') {
        DB::table('price_histories')->where('id', $r->id)->update(['LME' => $r->new_value]);
    } else {
        DB::table('price_histories')->where('id', $r->id)->update(['LME' => $currentLme]);
    }
    $count++;
}
echo "Updated {$count} rows\n";
