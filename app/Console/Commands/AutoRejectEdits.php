<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AutoRejectEdits extends Command
{
    protected $signature = 'app:auto-reject-edits';
    protected $description = 'Auto reject order edits if customer takes more than 10 minutes to respond';

    public function handle()
    {
        $tenMinutesAgo = now()->subMinutes(10);

        $receipts = \App\Models\Receipt::with('order')
            ->where('edit_confirmed_by_user', 0)
            ->where('updated_at', '<=', $tenMinutesAgo)
            ->get();

        foreach ($receipts as $receipt) {
            $receipt->update(['edit_confirmed_by_user' => 2]);
            
            if ($receipt->order) {
                $receipt->order->update([
                    'status' => 'cancelled',
                    'cancel_reason' => 'Perubahan item pesanan otomatis ditolak (batas waktu 10 menit telah habis).'
                ]);

                \App\Models\Activity::create([
                    'type' => 'order_edit_rejected',
                    'title' => 'Auto-Reject #' . $receipt->order->id,
                    'description' => 'Batas waktu 10 menit konfirmasi perubahan habis. Pesanan dibatalkan otomatis.',
                    'related_id' => $receipt->order->id,
                    'related_type' => \App\Models\Order::class,
                ]);
                
                \Illuminate\Support\Facades\Log::info("Auto-rejected edit for Order #{$receipt->order->id}");
            }
        }
    }
}
