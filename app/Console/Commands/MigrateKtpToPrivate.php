<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class MigrateKtpToPrivate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ktp:migrate-to-private
                            {--dry-run : Show what would be moved without actually moving anything}
                            {--delete-source : Delete files from public disk after copying to private}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrates KTP and Accu-KTP image files from the public disk (storage/app/public/) to the private local disk (storage/app/private/). Run once to secure existing uploads.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $isDryRun     = $this->option('dry-run');
        $deleteSource = $this->option('delete-source');

        $folders = ['ktp', 'accu_ktp'];

        $totalCopied  = 0;
        $totalSkipped = 0;
        $totalErrors  = 0;

        if ($isDryRun) {
            $this->warn('--- DRY RUN MODE — No files will actually be moved ---');
        }

        foreach ($folders as $folder) {
            $this->info("Processing folder: {$folder}/");

            $files = Storage::disk('public')->files($folder);

            if (empty($files)) {
                $this->line("  [SKIP] No files found in public/{$folder}/");
                continue;
            }

            $this->line('  Found ' . count($files) . ' file(s).');

            foreach ($files as $filePath) {
                // e.g. "ktp/6a94e9d82aeb6.jpeg"
                try {
                    // Check if already exists in private disk
                    if (Storage::disk('local')->exists($filePath)) {
                        $this->line("  [SKIP] Already in private: {$filePath}");
                        $totalSkipped++;
                        continue;
                    }

                    if ($isDryRun) {
                        $this->line("  [DRY-RUN] Would copy: public/{$filePath} → private/{$filePath}");
                        $totalCopied++;
                        continue;
                    }

                    // Read from public disk, write to local (private) disk
                    $contents = Storage::disk('public')->get($filePath);
                    Storage::disk('local')->put($filePath, $contents);

                    $this->line("  [OK] Copied: {$filePath}");
                    $totalCopied++;

                    // Optionally remove from public disk after successful copy
                    if ($deleteSource) {
                        Storage::disk('public')->delete($filePath);
                        $this->line("       └─ Deleted from public: {$filePath}");
                    }
                } catch (\Throwable $e) {
                    $this->error("  [ERROR] {$filePath}: " . $e->getMessage());
                    $totalErrors++;
                }
            }
        }

        $this->newLine();
        $this->info('--- Migration Summary ---');
        $this->line("  Copied:  {$totalCopied}");
        $this->line("  Skipped: {$totalSkipped}");
        $this->line("  Errors:  {$totalErrors}");

        if ($isDryRun) {
            $this->warn('Dry run complete. Re-run without --dry-run to apply changes.');
        } elseif ($totalErrors === 0) {
            $this->info('Migration complete!');
            if (!$deleteSource) {
                $this->warn('Note: Source files in storage/app/public/ were NOT deleted.');
                $this->warn('Re-run with --delete-source to remove them (only after verifying admin panel works correctly).');
            }
        } else {
            $this->error("Migration finished with {$totalErrors} error(s). Review output above.");
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
