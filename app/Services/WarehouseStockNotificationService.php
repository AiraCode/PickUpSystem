<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class WarehouseStockNotificationService
{
    public function notifyWarehouseReady(Warehouse $warehouse): void
    {
        $untaken = DB::table('orders')
            ->join('receipts', 'orders.id', '=', 'receipts.orders_id')
            ->join('accus_has_receipts', 'receipts.id', '=', 'accus_has_receipts.receipts_id')
            ->whereIn('orders.status', ['arrived_at_warehouse', 'completed'])
            ->where('orders.storages_id', $warehouse->id)
            ->where('orders.taken_by_central', false)
            ->select(DB::raw('SUM(accus_has_receipts.amount) as total_untaken'))
            ->value('total_untaken');

        $untaken = (int) $untaken;
        if ($untaken < 20) {
            return;
        }

        $alreadyNotified = Activity::where('type', 'warehouse_ready')
            ->where('related_type', Warehouse::class)
            ->where('related_id', $warehouse->id)
            ->exists();

        if ($alreadyNotified) {
            return;
        }

        $warehouseAdmins = User::where('warehouse_id', $warehouse->id)
            ->where('role', 'warehouse')
            ->get();

        $warehouseAdminEmails = $warehouseAdmins
            ->map(fn (User $user) => trim((string) $user->email))
            ->filter(fn ($email) => $email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL))
            ->unique()
            ->values()
            ->all();

        $centralEmails = User::where('role', 'central')
            ->whereNotNull('email')
            ->pluck('email')
            ->map(fn ($email) => trim((string) $email))
            ->filter(fn ($email) => $email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL))
            ->unique()
            ->values()
            ->all();

        if (empty($centralEmails)) {
            $centralEmails = collect(explode(',', (string) env('MAIL_NOTIFICATION_RECIPIENTS', '')))
                ->map(fn ($email) => trim((string) $email))
                ->filter(fn ($email) => $email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL))
                ->unique()
                ->values()
                ->all();
        }

        if (empty($warehouseAdminEmails) || empty($centralEmails)) {
            return;
        }

        try {
            $sender = $warehouseAdmins->first(function (User $user) {
                return ! empty($user->smtp_email) && filter_var($user->smtp_email, FILTER_VALIDATE_EMAIL);
            });

            if ($sender) {
                // Buat konfigurasi mailer kustom secara langsung tanpa mengganggu mailer global
                config([
                    'mail.mailers.dynamic_smtp' => [
                        'transport' => 'smtp',
                        'host' => $sender->smtp_host ?: env('MAIL_HOST', 'smtp.gmail.com'),
                        'port' => (int) ($sender->smtp_port ?: env('MAIL_PORT', 587)),
                        'encryption' => $sender->smtp_encryption ?: env('MAIL_ENCRYPTION', 'tls'),
                        'username' => $sender->smtp_email,
                        'password' => $sender->smtp_password,
                        'timeout' => null,
                        'local_domain' => env('MAIL_EHLO_DOMAIN'),
                    ],
                    'mail.from.address' => $sender->smtp_email,
                    'mail.from.name' => $sender->name ?: 'AKIKU',
                ]);

                // Pakai mailer 'dynamic_smtp' secara eksplisit!
                Mail::mailer('dynamic_smtp')->raw(
                    "Gudang cabang {$warehouse->name} memiliki stok aki \u2265 20 unit.\n\nSilakan cek daftar gudang cabang yang siap diambil dan tekan OK untuk mengurangi stok aki pada gudang.\n\nLink: ".url('/admin/gudang'),
                    function ($message) use ($warehouseAdminEmails, $centralEmails, $warehouse, $untaken, $sender) {
                        $message->from($sender->smtp_email, $sender->name ?: 'Admin Gudang');
                        $message->to($centralEmails);
                        $message->replyTo($warehouseAdminEmails);
                        $message->subject("Gudang {$warehouse->name} Siap Diambil - Stok {$untaken} Aki");
                    }
                );
            }
        } catch (\Throwable $e) { // <-- DITARUH DI SINI
            // Tangkap error secara detail
            Log::error('SMTP Error Surabaya: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Un-comment baris di bawah ini jika ingin melempar error langsung ke browser/terminal saat testing:
            // throw $e; 
            return;
        }

        try {
            Activity::create([
                'type' => 'warehouse_ready',
                'title' => 'Daftar gudang cabang dengan jumlah stok ≥ 20 unit.',
                'description' => "Gudang {$warehouse->name} memiliki {$untaken} aki yang siap diambil.",
                'related_id' => $warehouse->id,
                'related_type' => Warehouse::class,
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal mencatat aktivitas notifikasi stok gudang: '.$e->getMessage());
        }
    }
}
