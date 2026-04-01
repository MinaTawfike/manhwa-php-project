<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:run {--keep=5 : Number of backups to keep}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a backup of the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $keepCount = $this->option('keep');
        $backupPath = storage_path('app/backups');
        
        // Ensure backup directory exists
        if (!File::exists($backupPath)) {
            File::makeDirectory($backupPath, 0755, true);
        }

        $timestamp = now()->format('Y-m-d_H-i-s');
        $databasePath = database_path('database.sqlite');
        
        if (!File::exists($databasePath)) {
            $this->error('Database file not found: ' . $databasePath);
            return 1;
        }

        $backupFile = $backupPath . '/database_backup_' . $timestamp . '.sqlite';
        
        // Copy database file
        if (File::copy($databasePath, $backupFile)) {
            $this->info('Database backup created: ' . basename($backupFile));
            
            // Clean old backups
            $this->cleanOldBackups($backupPath, $keepCount);
            
            return 0;
        } else {
            $this->error('Failed to create backup');
            return 1;
        }
    }

    /**
     * Clean old backup files, keeping only the specified number.
     */
    protected function cleanOldBackups($backupPath, $keepCount)
    {
        $files = File::glob($backupPath . '/database_backup_*.sqlite');
        
        // Sort files by modification time (newest first)
        usort($files, function ($a, $b) {
            return filemtime($b) - filemtime($a);
        });

        // Remove excess files
        $filesToDelete = array_slice($files, $keepCount);
        
        foreach ($filesToDelete as $file) {
            if (File::delete($file)) {
                $this->info('Removed old backup: ' . basename($file));
            }
        }
    }
}
