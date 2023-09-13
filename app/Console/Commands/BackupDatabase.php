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
        $databaseBackupPath = storage_path('app/backups/database.sql');
        exec("mysqldump -u " . env('DB_USERNAME') . " -p" . env('DB_PASSWORD') . " " . env('DB_DATABASE') . " > $databaseBackupPath");

        // Comprimir la carpeta storage/app/public
        $storageBackupPath = storage_path('app/backups/storage.zip');
        exec("zip -r $storageBackupPath storage/app/public");

        // Envía los archivos por correo electrónico
        Mail::send([], [], function ($message) use ($databaseBackupPath, $storageBackupPath) {
            $message->to('destinatario@correo.com')
                ->subject('Archivos de respaldo')
                ->attach($databaseBackupPath)
                ->attach($storageBackupPath);
        });

        $this->info('Backup realizado y enviado por correo.');
        Log::info('Backup realizado y enviado por correo.');
    }
}
