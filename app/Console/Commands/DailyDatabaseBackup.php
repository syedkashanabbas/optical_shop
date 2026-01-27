<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;

class DailyDatabaseBackup extends Command
{
    protected $signature = 'backup:daily';
    protected $description = 'Create a daily database backup with clear naming';

    public function handle()
    {
        $db = env('DB_DATABASE');
        $user = env('DB_USERNAME');
        $pass = env('DB_PASSWORD');
        $host = env('DB_HOST', '127.0.0.1');

        $backupPath = storage_path('app/backups');

        if (!is_dir($backupPath)) {
            mkdir($backupPath, 0755, true);
        }

        // File name like: backup_2025-08-12.sql
        $fileName = $backupPath . '/backup_' . Carbon::now()->format('Y-m-d') . '.sql';

        // Create or overwrite daily backup
        $command = "mysqldump --user={$user} --password={$pass} --host={$host} {$db} > {$fileName}";
        system($command);

        $this->info("✅ Daily database backup created: {$fileName}");
    }
}
