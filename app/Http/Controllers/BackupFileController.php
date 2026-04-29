<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BackupFileController extends Controller
{
    /**
     * The OJS files directory path (configurable via .env)
     */
    private function ojsFilesPath(): string
    {
        return env('OJS_FILES_PATH', 'C:\\laragon\\www\\ojs3\\files');
    }

    public function index()
    {
        $ojsPath = $this->ojsFilesPath();
        $exists  = is_dir($ojsPath);

        $info = ['total_files' => 0, 'total_size_mb' => 0, 'by_type' => []];

        if ($exists) {
            $iter = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($ojsPath, \RecursiveDirectoryIterator::SKIP_DOTS)
            );
            $totalSize = 0;
            foreach ($iter as $file) {
                if ($file->isFile()) {
                    $size = $file->getSize();
                    $ext  = strtolower($file->getExtension()) ?: 'other';
                    $totalSize += $size;
                    $info['total_files']++;
                    $info['by_type'][$ext] = ($info['by_type'][$ext] ?? 0) + 1;
                }
            }
            $info['total_size_mb'] = round($totalSize / 1024 / 1024, 2);
            arsort($info['by_type']);
        }

        // List existing file backups
        $backupPath = storage_path('app/file-backups');
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

        return view('backup-file', compact('ojsPath', 'exists', 'info', 'backups'));
    }

    public function run(Request $request)
    {
        $ojsPath = $this->ojsFilesPath();

        if (!is_dir($ojsPath)) {
            return response()->json([
                'success' => false,
                'message' => "Folder OJS tidak ditemukan: {$ojsPath}",
            ]);
        }

        if (!class_exists('ZipArchive')) {
            return response()->json([
                'success' => false,
                'message' => 'PHP ZipArchive extension tidak aktif. Aktifkan extension=zip di php.ini.',
            ]);
        }

        $backupPath = storage_path('app/file-backups');
        if (!is_dir($backupPath)) {
            mkdir($backupPath, 0755, true);
        }

        $timestamp = date('Y-m-d_H-i-s');
        $zipName   = "backup_ojs_files_{$timestamp}.zip";
        $zipPath   = "{$backupPath}/{$zipName}";

        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat file ZIP. Periksa izin folder storage.',
            ]);
        }

        // Add all files from ojs3/files recursively
        $iter = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($ojsPath, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        $totalAdded = 0;
        foreach ($iter as $file) {
            if ($file->isFile()) {
                // relative path inside ZIP
                $relativePath = substr($file->getRealPath(), strlen(realpath($ojsPath)) + 1);
                $relativePath = str_replace('\\', '/', $relativePath);
                $zip->addFile($file->getRealPath(), $relativePath);
                $totalAdded++;
            }
        }

        $zip->close();

        if (!file_exists($zipPath) || filesize($zipPath) === 0) {
            return response()->json([
                'success' => false,
                'message' => 'File ZIP kosong atau gagal dibuat.',
            ]);
        }

        $sizeMb = round(filesize($zipPath) / 1024 / 1024, 2);

        return response()->json([
            'success'     => true,
            'message'     => "Backup file berhasil! {$totalAdded} file dikompres.",
            'file_name'   => $zipName,
            'file_size'   => $sizeMb . ' MB',
            'total_files' => $totalAdded,
        ]);
    }

    public function download($filename)
    {
        if (!preg_match('/^backup_ojs_files_[\w\-]+\.zip$/', $filename)) {
            abort(403);
        }

        $filePath = storage_path('app/file-backups/' . $filename);
        if (!file_exists($filePath)) {
            abort(404, 'File backup tidak ditemukan.');
        }

        return response()->download($filePath);
    }

    public function delete($filename)
    {
        if (!preg_match('/^backup_ojs_files_[\w\-]+\.zip$/', $filename)) {
            abort(403);
        }

        $filePath = storage_path('app/file-backups/' . $filename);
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        return redirect()->route('backup-file')->with('success', "File {$filename} berhasil dihapus.");
    }
}
