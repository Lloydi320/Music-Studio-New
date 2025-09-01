<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Carbon\Carbon;

class CreateBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:create {--auto : Indicates this is an automated backup}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a system backup including database and files';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting backup process...');
        
        try {
            $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
            $backupName = $this->option('auto') ? 'auto_backup_' . $timestamp : 'manual_backup_' . $timestamp;
            
            // Create backup directory
            $backupPath = storage_path('app\\backups\\' . $backupName);
            if (!\File::exists($backupPath)) {
                \File::makeDirectory($backupPath, 0755, true);
            }

            $this->info('Creating database backup...');
            $this->createDatabaseBackup($backupPath, $backupName);
            
            $this->info('Creating files backup...');
            $this->createFilesBackup($backupPath);
            
            $this->info('Backup created successfully in directory: ' . $backupName);
            $this->info('Backup location: ' . $backupPath);
            
            // Calculate backup size
            $backupSize = $this->getDirectorySize($backupPath);
            $this->info('Backup size: ' . $this->formatBytes($backupSize));
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error('Backup failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
    
    /**
     * Create database backup
     */
    private function createDatabaseBackup($backupPath, $backupName)
    {
        $databaseName = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port', 3306);

        $sqlFile = $backupPath . '/database.sql';

        // Try mysqldump first
        $command = sprintf(
            'mysqldump --user=%s --password=%s --host=%s --port=%s --single-transaction --routines --triggers %s > %s',
            escapeshellarg($username),
            escapeshellarg($password),
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($databaseName),
            escapeshellarg($sqlFile)
        );

        $process = \Symfony\Component\Process\Process::fromShellCommandline($command);
        $process->run();

        if (!$process->isSuccessful()) {
            // Fallback to Laravel's database export
            $this->createLaravelDatabaseBackup($backupPath);
        }
    }

    /**
     * Laravel-based database backup (fallback)
     */
    private function createLaravelDatabaseBackup($backupPath)
    {
        $tables = \DB::select('SHOW TABLES');
        $sqlContent = "-- Database Backup\n-- Generated: " . Carbon::now() . "\n\n";

        foreach ($tables as $table) {
            $tableName = array_values((array) $table)[0];
            
            // Get table structure
            $createTable = \DB::select("SHOW CREATE TABLE `{$tableName}`")[0];
            $sqlContent .= "-- Table: {$tableName}\n";
            $sqlContent .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
            $sqlContent .= $createTable->{'Create Table'} . ";\n\n";

            // Get table data
            $rows = \DB::table($tableName)->get();
            if ($rows->count() > 0) {
                $sqlContent .= "-- Data for table: {$tableName}\n";
                foreach ($rows as $row) {
                    $values = array_map(function($value) {
                        return is_null($value) ? 'NULL' : "'" . addslashes($value) . "'";
                    }, (array) $row);
                    $sqlContent .= "INSERT INTO `{$tableName}` VALUES (" . implode(', ', $values) . ");\n";
                }
                $sqlContent .= "\n";
            }
        }

        \File::put($backupPath . '/database.sql', $sqlContent);
    }

    /**
     * Create files backup
     */
    private function createFilesBackup($backupPath)
    {
        $filesToBackup = [
            'storage/app/public' => 'storage',
            'public/images' => 'public_images',
            '.env' => 'env_file'
        ];

        foreach ($filesToBackup as $source => $destination) {
            $sourcePath = base_path($source);
            $destPath = $backupPath . '/' . $destination;

            if (\File::exists($sourcePath)) {
                if (\File::isDirectory($sourcePath)) {
                    \File::copyDirectory($sourcePath, $destPath);
                } else {
                    \File::copy($sourcePath, $destPath);
                }
            }
        }
    }

    /**
     * Get directory size recursively
     */
    private function getDirectorySize($directory)
    {
        $size = 0;
        if (is_dir($directory)) {
            foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory)) as $file) {
                if ($file->isFile()) {
                    $size += $file->getSize();
                }
            }
        }
        return $size;
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}