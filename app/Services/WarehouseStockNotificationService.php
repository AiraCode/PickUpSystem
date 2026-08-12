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
            ->map(fn(User $user) => trim((string) $user->email))
            ->filter(fn($email) => $email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL))
            ->unique()
            ->values()
            ->all();

        $centralEmails = User::where('role', 'central')
            ->whereNotNull('email')
            ->pluck('email')
            ->map(fn($email) => trim((string) $email))
            ->filter(fn($email) => $email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL))
            ->unique()
            ->values()
            ->all();

        if (empty($centralEmails)) {
            $centralEmails = collect(explode(',', (string) env('MAIL_NOTIFICATION_RECIPIENTS', '')))
                ->map(fn($email) => trim((string) $email))
                ->filter(fn($email) => $email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL))
                ->unique()
                ->values()
                ->all();
        }

        if (empty($warehouseAdminEmails) || empty($centralEmails)) {
            return;
        }

        $pdfBinary = null;
        try {
            $pdfService = new WarehouseStockPdfService();
            $pdfBinary = $pdfService->generatePdfReport($warehouse);
        } catch (\Throwable $pdfEx) {
            Log::error('Gagal membuat PDF Struk Laporan Stok Gudang: ' . $pdfEx->getMessage(), [
                'warehouse_id' => $warehouse->id,
                'error' => $pdfEx->getMessage(),
            ]);
        }

        try {
            $sender = $warehouseAdmins->first(function (User $user) {
                return ! empty($user->smtp_email) && filter_var($user->smtp_email, FILTER_VALIDATE_EMAIL);
            });

            if ($sender) {
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

                $safeWarehouseName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $warehouse->name);
                $pdfFileName = "Struk_Laporan_Stok_Gudang_{$safeWarehouseName}_" . date('Ymd_His') . ".pdf";

                $pdfPath = null;
                if (!empty($pdfBinary)) {
                    $reportsDir = storage_path('app/public/reports');
                    if (!file_exists($reportsDir)) {
                        mkdir($reportsDir, 0755, true);
                    }
                    $pdfPath = $reportsDir . '/' . $pdfFileName;
                    file_put_contents($pdfPath, $pdfBinary);
                }

                $htmlContent = "Yth. Tim Operasional Pusat,<br><br>"
                    . "Gudang cabang <strong>{$warehouse->name}</strong> saat ini telah memiliki stok aki <strong>" . number_format($untaken, 0, ',', '.') . " unit</strong> (Total: <strong>{$untaken} unit</strong>) yang siap diambil.<br><br>"
                    . "Terlampir berkas PDF <strong>Struk Laporan Stok Aki Gudang</strong> yang berisi rincian jenis aki, merek, jumlah unit, dan estimasi berat.<br><br>"
                    . "<a href='" . url('/admin/gudang') . "' style='display:inline-block; padding:10px 18px; background-color:#2563eb; color:#ffffff; text-decoration:none; border-radius:6px; font-weight:bold;'>Buka Halaman Gudang Admin</a><br><br>"
                    . "Salam,<br>"
                    . "<strong>Tim Admin Gudang {$warehouse->name}</strong>";

                Mail::mailer('dynamic_smtp')->send([], [], function ($message) use ($warehouseAdminEmails, $centralEmails, $warehouse, $untaken, $sender, $htmlContent, $pdfPath) {
                    $message->from($sender->smtp_email, $sender->name ?: 'Admin Gudang');
                    $message->to($centralEmails);
                    $message->replyTo($warehouseAdminEmails);
                    $message->subject("Struk Laporan Stok Gudang {$warehouse->name} Siap Diambil - {$untaken} Aki");
                    $message->html($htmlContent);

                    if (!empty($pdfPath) && file_exists($pdfPath)) {
                        $message->attach($pdfPath, [
                            'mime' => 'application/pdf',
                        ]);
                    }
                });
            }
        } catch (\Throwable $e) {
            Log::error('SMTP Error Surabaya: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
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
            Log::error('Gagal mencatat aktivitas notifikasi stok gudang: ' . $e->getMessage());
        }
    }

    public function sendPickupReceiptEmail(Warehouse $warehouse, int $totalQty): void
    {
        $warehouseAdmins = User::where('warehouse_id', $warehouse->id)
            ->where('role', 'warehouse')
            ->get();

        $warehouseAdminEmails = $warehouseAdmins
            ->map(fn(User $user) => trim((string) $user->email))
            ->filter(fn($email) => $email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL))
            ->unique()
            ->values()
            ->all();

        $centralEmails = User::where('role', 'central')
            ->whereNotNull('email')
            ->pluck('email')
            ->map(fn($email) => trim((string) $email))
            ->filter(fn($email) => $email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL))
            ->unique()
            ->values()
            ->all();

        $allRecipients = array_values(array_unique(array_merge($centralEmails, $warehouseAdminEmails)));

        if (empty($allRecipients)) {
            return;
        }

        $pdfBinary = null;
        try {
            $pdfService = new WarehousePickupReceiptPdfService();
            $pdfBinary = $pdfService->generateReceiptPdf($warehouse);
        } catch (\Throwable $pdfEx) {
            Log::error('Gagal membuat PDF Tanda Terima Pengambilan Aki: ' . $pdfEx->getMessage(), [
                'warehouse_id' => $warehouse->id,
                'error' => $pdfEx->getMessage(),
            ]);
        }

        try {
            // Pengirim Tanda Terima adalah Admin Pusat (Central)
            $centralSender = User::where('role', 'central')
                ->whereNotNull('smtp_email')
                ->where('smtp_email', '!=', '')
                ->first();

            if (!$centralSender) {
                $centralSender = $warehouseAdmins->first(fn($u) => !empty($u->smtp_email))
                    ?: User::where('role', 'central')->first();
            }

            if ($centralSender) {
                config([
                    'mail.mailers.dynamic_smtp' => [
                        'transport' => 'smtp',
                        'host' => $centralSender->smtp_host ?: env('MAIL_HOST', 'smtp.gmail.com'),
                        'port' => (int) ($centralSender->smtp_port ?: env('MAIL_PORT', 587)),
                        'encryption' => $centralSender->smtp_encryption ?: env('MAIL_ENCRYPTION', 'tls'),
                        'username' => $centralSender->smtp_email,
                        'password' => $centralSender->smtp_password,
                        'timeout' => null,
                        'local_domain' => env('MAIL_EHLO_DOMAIN'),
                    ],
                    'mail.from.address' => $centralSender->smtp_email,
                    'mail.from.name' => $centralSender->name ?: 'Admin Utama (Pusat)',
                ]);

                $safeWarehouseName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $warehouse->name);
                $pdfFileName = "Struk_Tanda_Terima_Pengambilan_{$safeWarehouseName}_" . date('Ymd_His') . ".pdf";

                $pdfPath = null;
                if (!empty($pdfBinary)) {
                    $reportsDir = storage_path('app/public/reports');
                    if (!file_exists($reportsDir)) {
                        mkdir($reportsDir, 0755, true);
                    }
                    $pdfPath = $reportsDir . '/' . $pdfFileName;
                    file_put_contents($pdfPath, $pdfBinary);
                }

                $htmlContent = "Yth. Tim Admin Gudang <strong>{$warehouse->name}</strong>,<br><br>"
                    . "Pengambilan stok aki dari Gudang cabang <strong>{$warehouse->name}</strong> telah berhasil dikonfirmasi dan diselesaikan oleh Tim Pusat.<br><br>"
                    . "<strong>Total Aki Diterima Pusat:</strong> <strong>" . number_format($totalQty, 0, ',', '.') . " unit</strong>.<br><br>"
                    . "Terlampir berkas PDF resmi <strong>Struk Tanda Terima Pengambilan Aki</strong> sebagai bukti sah serah terima barang.<br><br>"
                    . "<a href='" . url('/admin/gudang') . "' style='display:inline-block; padding:10px 18px; background-color:#16a34a; color:#ffffff; text-decoration:none; border-radius:6px; font-weight:bold;'>Buka Halaman Gudang Admin</a><br><br>"
                    . "Salam,<br>"
                    . "<strong>Tim Operasional Pusat Akiku</strong>";

                Mail::mailer('dynamic_smtp')->send([], [], function ($message) use ($warehouseAdminEmails, $warehouse, $totalQty, $centralSender, $htmlContent, $pdfPath) {
                    $message->from($centralSender->smtp_email, $centralSender->name ?: 'Admin Utama (Pusat)');
                    // Dikirimkan dari Admin Pusat KE Admin Gudang Cabang
                    $message->to($warehouseAdminEmails);
                    $message->subject("Tanda Terima Pengambilan Aki Gudang {$warehouse->name} - {$totalQty} Unit");
                    $message->html($htmlContent);

                    if (!empty($pdfPath) && file_exists($pdfPath)) {
                        $message->attach($pdfPath, [
                            'mime' => 'application/pdf',
                        ]);
                    }
                });
            }
        } catch (\Throwable $e) {
            Log::error('SMTP Error Tanda Terima Pengambilan: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
