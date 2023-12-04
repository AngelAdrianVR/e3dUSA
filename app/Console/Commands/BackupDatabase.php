<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BackupDatabase extends Command
{
    protected $signature = 'app:backup-database';

    protected $description = 'Backup the database and storage/app/public folder and email them';

    public function handle()
    {
        // Hacer una copia de seguridad de la base de datos
        $databaseName = env('DB_DATABASE');
        $databaseUser = env('DB_USERNAME');
        $databasePassword = env('DB_PASSWORD');
        $backupDate = now()->format('j_F_Y'); // Ejemplo: 13_septiembre_2023
        $databaseBackupPath = storage_path("app/backups/database_$backupDate.sql");

        exec("mysqldump -u$databaseUser -p$databasePassword $databaseName > $databaseBackupPath");

        $this->info('Backup realizado.');
        Log::info("Backup realizado.");
    }
}
