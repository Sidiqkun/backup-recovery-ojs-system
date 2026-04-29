<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class FullBackupController extends Controller
{
    private function ojsFilesPath(): string
    {
        return env('OJS_FILES_PATH', 'C:\\laragon\\www\\ojs3\\files');
    }

    public function index()
    {
        // Calculate info
        $ojsPath = $this->ojsFilesPath();
        $exists  = is_dir($ojsPath);
        $totalFiles = 0;
        
        if ($exists) {
            $iter = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($ojsPath, \RecursiveDirectoryIterator::SKIP_DOTS)
            );
            foreach ($iter as $file) {
                if ($file->isFile()) $totalFiles++;
            }
        }

        // Get DB Size 
        $dbSizeMb = \Illuminate\Support\Facades\DB::select("
            SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb
            FROM information_schema.tables WHERE table_schema = DATABASE()
        ")[0]->size_mb ?? 0;

        // List full backups
        $backupPath = storage_path('app/full-backups');
        $backups = [];
        if (is_dir($backupPath)) {
            $files = glob($backupPath . '/*.zip');
            foreach ($files as $file) {
                $backups[] = [
                    'name'    => basename($file),
                    'size'    => round(filesize($file) / 1024 / 1024, 2) . ' MB',
                    'created' => date('d M Y H:i', filemtime($file)),
                ];
            }
            usort($backups, fn($a, $b) => strcmp($b['created'], $a['created']));
        }

        return view('full-backup', compact('ojsPath', 'exists', 'totalFiles', 'dbSizeMb', 'backups'));
    }

    public function run(Request $request)
    {
        $ojsPath = $this->ojsFilesPath();

        if (!class_exists('ZipArchive')) {
            return response()->json(['success' => false, 'message' => 'PHP ZipArchive extension tidak aktif.']);
        }

        $backupPath = storage_path('app/full-backups');
        $tempPath   = storage_path('app/temp');
        
        if (!is_dir($backupPath)) mkdir($backupPath, 0755, true);
        if (!is_dir($tempPath)) mkdir($tempPath, 0755, true);

        $timestamp = date('Y-m-d_H-i-s');
        $zipName   = "full_backup_ojs_{$timestamp}.zip";
        $zipPath   = "{$backupPath}/{$zipName}";
        $sqlPath   = "{$tempPath}/database.sql";

        // 1. Dump Database
        $dbName = config('database.connections.mysql.database');
        $dbHost = config('database.connections.mysql.host');
        $dbPort = config('database.connections.mysql.port', 3306);
        $dbUser = config('database.connections.mysql.username');
        $dbPass = config('database.connections.mysql.password');

        $mysqldump = 'mysqldump';
        $candidates = array_merge(
            glob('C:\\laragon\\bin\\mysql*\\*\\bin\\mysqldump.exe') ?: [],
            glob('C:\\laragon\\bin\\mariadb*\\*\\bin\\mysqldump.exe') ?: []
        );
        if (!empty($candidates)) $mysqldump = $candidates[0];

        $cmd = '"' . $mysqldump . '" -h ' . escapeshellarg($dbHost) . ' -P ' . $dbPort . ' -u ' . escapeshellarg($dbUser) . ($dbPass ? ' -p' . escapeshellarg($dbPass) : '') . ' ' . escapeshellarg($dbName) . ' > ' . escapeshellarg($sqlPath) . ' 2>&1';
        exec($cmd, $output, $returnCode);

        if ($returnCode !== 0 || !file_exists($sqlPath)) {
            return response()->json(['success' => false, 'message' => 'Gagal dump database.']);
        }

        // 2. Create ZIP containing DB and OJS Files
        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            return response()->json(['success' => false, 'message' => 'Gagal membuat file ZIP.']);
        }

        // Add DB
        $zip->addFile($sqlPath, 'database.sql');

        // Add Files
        $totalAdded = 1; // Count DB file
        if (is_dir($ojsPath)) {
            $iter = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($ojsPath, \RecursiveDirectoryIterator::SKIP_DOTS));
            foreach ($iter as $file) {
                if ($file->isFile()) {
                    $relFile = substr($file->getRealPath(), strlen(realpath($ojsPath)) + 1);
                    $relFile = str_replace('\\', '/', $relFile);
                    $zip->addFile($file->getRealPath(), 'files/' . $relFile);
                    $totalAdded++;
                }
            }
        }

        $zip->close();
        unlink($sqlPath); // Cleanup temp sql

        if (!file_exists($zipPath) || filesize($zipPath) === 0) {
            return response()->json(['success' => false, 'message' => 'File ZIP kosong atau gagal.']);
        }

        return response()->json([
            'success'     => true,
            'message'     => 'Full Backup berhasil!',
            'file_name'   => $zipName,
            'file_size'   => round(filesize($zipPath) / 1024 / 1024, 2) . ' MB',
            'total_files' => $totalAdded,
        ]);
    }

    public function download($filename)
    {
        if (!preg_match('/^full_backup_ojs_[\w\-]+\.zip$/', $filename)) abort(403);
        $filePath = storage_path('app/full-backups/' . $filename);
        if (!file_exists($filePath)) abort(404);
        return response()->download($filePath);
    }

    public function delete($filename)
    {
        if (!preg_match('/^full_backup_ojs_[\w\-]+\.zip$/', $filename)) abort(403);
        $filePath = storage_path('app/full-backups/' . $filename);
        if (file_exists($filePath)) unlink($filePath);
        return redirect()->route('full-backup')->with('success', "Backup dihapus.");
    }
}
