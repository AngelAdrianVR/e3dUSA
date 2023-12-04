<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BackupMedia extends Command
{
    protected $signature = 'app:backup-media';

    protected $description = 'Backup media in storage/app/public folder';

    public function handle()
    {
        $backupDate = now()->format('j_F_Y'); // Ejemplo: 13_septiembre_2023

        // Comprimir cada subdirectorio por separado
        $publicPath = storage_path('app/public');
        $storageFolderPath = storage_path("app/backups/storage_$backupDate");

        // Asegurarse de que la carpeta de respaldo exista
        if (!file_exists($storageFolderPath)) {
            mkdir($storageFolderPath, 0755, true);
        }

        $directoriesToCompress = scandir($publicPath);

        foreach ($directoriesToCompress as $directory) {
            if ($directory !== '.' && $directory !== '..' && is_dir("$publicPath/$directory")) {
                $zipFileName = "storage_$directory.zip";
                $zipFilePath = "$storageFolderPath/$zipFileName";

                exec("zip -r $zipFilePath $publicPath/$directory");
            }
        }

        $this->info('Backup media realizado.');
        Log::info("Backup media realizado.");
    }
}
