<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class ExportMigrationPackage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export:migration-package 
                            {--output= : Output directory for the zip file}
                            {--filename= : Custom filename for the zip file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export database and storage files into a migration package (zip) for sifaisalv2';

    /**
     * Tables to export (skip system tables)
     */
    private $tablesToExport = [
        'users',
        'admins',
        'service_providers',
        'k_p_a_s',
        'p_p_k_s',
        's_p_m_s',
        'treasurers',
        'work_packages',
        'contracts',
        'role_has_work_packages',
        'payment_requests',
        'documents',
        'termint_spp_ppks',
        'termint_spp_ppk_files',
        's_p_m_requests',
        'notifications',
    ];

    /**
     * Tables to skip
     */
    private $skipTables = [
        'migrations',
        'cache',
        'cache_locks',
        'sessions',
        'jobs',
        'job_batches',
        'failed_jobs',
        'personal_access_tokens',
        'password_reset_tokens',
        'filament_exceptions',
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('╔══════════════════════════════════════════════════════════╗');
        $this->info('║     SIFAISAL V1 - EXPORT MIGRATION PACKAGE               ║');
        $this->info('╚══════════════════════════════════════════════════════════╝');
        $this->newLine();

        $startTime = microtime(true);
        
        // Prepare output paths
        $timestamp = now()->format('Ymd_His');
        $outputDir = $this->option('output') ?: storage_path('app/exports');
        $filename = $this->option('filename') ?: "sifaisalv1_migration_{$timestamp}.zip";
        
        if (!str_ends_with($filename, '.zip')) {
            $filename .= '.zip';
        }

        $zipPath = rtrim($outputDir, '/') . '/' . $filename;
        $tempDir = storage_path("app/temp/export_{$timestamp}");

        // Ensure directories exist
        if (!File::exists($outputDir)) {
            File::makeDirectory($outputDir, 0755, true);
        }
        if (!File::exists($tempDir)) {
            File::makeDirectory($tempDir, 0755, true);
        }

        try {
            // Step 1: Export Database
            $this->info('📦 Step 1/4: Exporting database...');
            $dbStats = $this->exportDatabase($tempDir);
            $this->info("   ✓ Exported {$dbStats['tables_count']} tables with {$dbStats['total_records']} total records");

            // Step 2: Export Storage Files
            $this->info('📁 Step 2/4: Copying storage files...');
            $storageStats = $this->exportStorage($tempDir);
            $this->info("   ✓ Copied {$storageStats['files_count']} files ({$this->formatBytes($storageStats['total_size'])})");

            // Step 3: Generate Manifest
            $this->info('📝 Step 3/4: Generating manifest...');
            $manifest = $this->generateManifest($dbStats, $storageStats, $tempDir);
            $this->info("   ✓ Manifest created with checksum");

            // Step 4: Create Zip
            $this->info('🗜️  Step 4/4: Creating zip archive...');
            $this->createZipArchive($tempDir, $zipPath);
            $zipSize = File::size($zipPath);
            $this->info("   ✓ Created zip file ({$this->formatBytes($zipSize)})");

            // Cleanup
            File::deleteDirectory($tempDir);

            $duration = round(microtime(true) - $startTime, 2);

            $this->newLine();
            $this->info('══════════════════════════════════════════════════════════');
            $this->info('✅ EXPORT COMPLETED SUCCESSFULLY');
            $this->info('══════════════════════════════════════════════════════════');
            $this->table(
                ['Property', 'Value'],
                [
                    ['Output File', $zipPath],
                    ['File Size', $this->formatBytes($zipSize)],
                    ['Tables Exported', $dbStats['tables_count']],
                    ['Total Records', $dbStats['total_records']],
                    ['Storage Files', $storageStats['files_count']],
                    ['Duration', "{$duration}s"],
                ]
            );

            return 0;
        } catch (\Exception $e) {
            // Cleanup on error
            if (File::exists($tempDir)) {
                File::deleteDirectory($tempDir);
            }
            if (File::exists($zipPath)) {
                File::delete($zipPath);
            }

            $this->error("Export failed: " . $e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
        }
    }

    /**
     * Export database tables to SQL file
     */
    private function exportDatabase(string $tempDir): array
    {
        $sqlPath = $tempDir . '/database.sql';
        $stats = [
            'tables_count' => 0,
            'total_records' => 0,
            'table_records' => [],
        ];

        $handle = fopen($sqlPath, 'w');
        
        // Write header
        fwrite($handle, "-- SIFAISAL V1 Database Export\n");
        fwrite($handle, "-- Generated: " . now()->toIso8601String() . "\n");
        fwrite($handle, "-- Source: sifaisalv1\n\n");

        foreach ($this->tablesToExport as $table) {
            // Check if table exists
            if (!$this->tableExists($table)) {
                $this->line("   - Skipping {$table} (table not found)");
                continue;
            }

            $records = DB::table($table)->get();
            $recordCount = $records->count();

            if ($recordCount === 0) {
                $this->line("   - Skipping {$table} (empty)");
                continue;
            }

            $stats['tables_count']++;
            $stats['total_records'] += $recordCount;
            $stats['table_records'][$table] = $recordCount;

            $this->line("   - Exporting {$table}: {$recordCount} records");

            // Get column names
            $columns = array_keys((array) $records->first());
            $columnList = '`' . implode('`, `', $columns) . '`';

            fwrite($handle, "\n-- Table: {$table}\n");
            fwrite($handle, "-- Records: {$recordCount}\n");

            // Write INSERT statements in batches
            $batchSize = 100;
            $batches = $records->chunk($batchSize);

            foreach ($batches as $batch) {
                $values = [];
                foreach ($batch as $row) {
                    $rowValues = [];
                    foreach ((array) $row as $value) {
                        if (is_null($value)) {
                            $rowValues[] = 'NULL';
                        } else {
                            $rowValues[] = "'" . addslashes($value) . "'";
                        }
                    }
                    $values[] = '(' . implode(', ', $rowValues) . ')';
                }

                $sql = "INSERT INTO `{$table}` ({$columnList}) VALUES\n" . implode(",\n", $values) . ";\n";
                fwrite($handle, $sql);
            }
        }

        fclose($handle);
        return $stats;
    }

    /**
     * Check if table exists
     */
    private function tableExists(string $table): bool
    {
        $driver = DB::connection()->getDriverName();
        
        if ($driver === 'sqlite') {
            $result = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name=?", [$table]);
            return count($result) > 0;
        } else {
            $result = DB::select("SHOW TABLES LIKE ?", [$table]);
            return count($result) > 0;
        }
    }

    /**
     * Export storage files
     */
    private function exportStorage(string $tempDir): array
    {
        $stats = [
            'files_count' => 0,
            'total_size' => 0,
        ];

        $storageTargetDir = $tempDir . '/storage';
        File::makeDirectory($storageTargetDir, 0755, true);

        // Export public storage
        $publicPath = storage_path('app/public');
        if (File::exists($publicPath)) {
            $publicStats = $this->copyDirectory($publicPath, $storageTargetDir . '/public');
            $stats['files_count'] += $publicStats['files'];
            $stats['total_size'] += $publicStats['size'];
            $this->line("   - Public storage: {$publicStats['files']} files");
        }

        // Export private storage
        $privatePath = storage_path('app/private');
        if (File::exists($privatePath)) {
            $privateStats = $this->copyDirectory($privatePath, $storageTargetDir . '/private');
            $stats['files_count'] += $privateStats['files'];
            $stats['total_size'] += $privateStats['size'];
            $this->line("   - Private storage: {$privateStats['files']} files");
        }

        return $stats;
    }

    /**
     * Recursively copy directory and count files/size
     */
    private function copyDirectory(string $source, string $destination): array
    {
        $stats = ['files' => 0, 'size' => 0];

        if (!File::exists($source)) {
            return $stats;
        }

        File::makeDirectory($destination, 0755, true);

        $files = File::allFiles($source);
        foreach ($files as $file) {
            $relativePath = $file->getRelativePathname();
            $targetPath = $destination . '/' . $relativePath;
            
            // Ensure target directory exists
            $targetDir = dirname($targetPath);
            if (!File::exists($targetDir)) {
                File::makeDirectory($targetDir, 0755, true);
            }

            File::copy($file->getPathname(), $targetPath);
            $stats['files']++;
            $stats['size'] += $file->getSize();
        }

        return $stats;
    }

    /**
     * Generate manifest.json
     */
    private function generateManifest(array $dbStats, array $storageStats, string $tempDir): array
    {
        $manifest = [
            'version' => '1.0',
            'source' => 'sifaisalv1',
            'target' => 'sifaisalv2',
            'exported_at' => now()->toIso8601String(),
            'database' => [
                'type' => config('database.default'),
                'file' => 'database.sql',
                'tables' => array_keys($dbStats['table_records']),
                'record_counts' => $dbStats['table_records'],
                'total_records' => $dbStats['total_records'],
            ],
            'storage' => [
                'files_count' => $storageStats['files_count'],
                'total_size_bytes' => $storageStats['total_size'],
            ],
        ];

        // Calculate checksum of database.sql
        $sqlPath = $tempDir . '/database.sql';
        if (File::exists($sqlPath)) {
            $manifest['checksum'] = [
                'algorithm' => 'sha256',
                'database_sql' => hash_file('sha256', $sqlPath),
            ];
        }

        // Write manifest
        $manifestPath = $tempDir . '/manifest.json';
        File::put($manifestPath, json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return $manifest;
    }

    /**
     * Create zip archive from temp directory
     */
    private function createZipArchive(string $sourceDir, string $zipPath): void
    {
        $zip = new ZipArchive();
        
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \Exception("Cannot create zip file: {$zipPath}");
        }

        $this->addDirectoryToZip($zip, $sourceDir, '');
        $zip->close();
    }

    /**
     * Recursively add directory contents to zip
     */
    private function addDirectoryToZip(ZipArchive $zip, string $dir, string $relativePath): void
    {
        $files = File::files($dir);
        foreach ($files as $file) {
            $entryName = $relativePath ? $relativePath . '/' . $file->getFilename() : $file->getFilename();
            $zip->addFile($file->getPathname(), $entryName);
        }

        $directories = File::directories($dir);
        foreach ($directories as $subDir) {
            $dirName = basename($subDir);
            $newRelativePath = $relativePath ? $relativePath . '/' . $dirName : $dirName;
            $zip->addEmptyDir($newRelativePath);
            $this->addDirectoryToZip($zip, $subDir, $newRelativePath);
        }
    }

    /**
     * Format bytes to human readable
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $unitIndex = 0;
        
        while ($bytes >= 1024 && $unitIndex < count($units) - 1) {
            $bytes /= 1024;
            $unitIndex++;
        }

        return round($bytes, 2) . ' ' . $units[$unitIndex];
    }
}
