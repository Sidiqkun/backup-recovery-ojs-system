<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BackupController extends Controller
{
    public function index()
    {
        $dbSizeMb = DB::select("
            SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb
            FROM information_schema.tables WHERE table_schema = DATABASE()
        ")[0]->size_mb ?? 0;

        $dbName = config('database.connections.mysql.database');
        $dbHost = config('database.connections.mysql.host');

        // List existing backup files
        $backupPath = storage_path('app/backups');
        $backups = [];
        if (is_dir($backupPath)) {
            $files = glob($backupPath . '/*.sql');
            foreach ($files as $file) {
                $backups[] = [
                    'name'    => basename($file),
                    'size'    => round(filesize($file) / 1024, 1) . ' KB',
                    'created' => date('d M Y H:i', filemtime($file)),
                ];
            }
            usort($backups, fn($a, $b) => strcmp($b['created'], $a['created']));
        }

        return view('backup', compact('dbSizeMb', 'dbName', 'dbHost', 'backups'));
    }

    public function run(Request $request)
    {
        $request->validate([
            'backup_type' => 'required|in:database,full',
        ]);

        $backupPath = storage_path('app/backups');
        if (!is_dir($backupPath)) {
            mkdir($backupPath, 0755, true);
        }

        $dbName     = config('database.connections.mysql.database');
        $dbHost     = config('database.connections.mysql.host');
        $dbPort     = config('database.connections.mysql.port', 3306);
        $dbUser     = config('database.connections.mysql.username');
        $dbPass     = config('database.connections.mysql.password');
        $timestamp  = date('Y-m-d_H-i-s');
        $fileName   = "backup_ojs_{$timestamp}.sql";
        $filePath   = "{$backupPath}/{$fileName}";

        // Find mysqldump in Laragon
        $mysqldump = 'mysqldump'; // fallback
        $candidates = array_merge(
            glob('C:\\laragon\\bin\\mysql*\\*\\bin\\mysqldump.exe') ?: [],
            glob('C:\\laragon\\bin\\mariadb*\\*\\bin\\mysqldump.exe') ?: []
        );
        if (!empty($candidates)) {
            $mysqldump = $candidates[0];
        }

        // Build command
        $passArg = $dbPass ? '-p' . $dbPass : '';
        $cmd = '"' . $mysqldump . '"'
            . ' -h ' . escapeshellarg($dbHost)
            . ' -P ' . $dbPort
            . ' -u ' . escapeshellarg($dbUser)
            . ($dbPass ? ' -p' . escapeshellarg($dbPass) : '')
            . ' ' . escapeshellarg($dbName)
            . ' > ' . escapeshellarg($filePath)
            . ' 2>&1';

        exec($cmd, $output, $returnCode);

        if ($returnCode !== 0 || !file_exists($filePath) || filesize($filePath) === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Backup gagal: ' . implode("\n", $output),
            ]);
        }

        $sizeKb = round(filesize($filePath) / 1024, 1);

        return response()->json([
            'success'   => true,
            'message'   => 'Backup berhasil dibuat!',
            'file_name' => $fileName,
            'file_size' => $sizeKb . ' KB',
        ]);
    }

    public function download($filename)
    {
        $filePath = storage_path('app/backups/' . $filename);

        if (!file_exists($filePath)) {
            abort(404, 'File backup tidak ditemukan.');
        }

        // Validate filename (security: only allow safe names)
        if (!preg_match('/^backup_ojs_[\w\-]+\.sql$/', $filename)) {
            abort(403);
        }

        return response()->download($filePath);
    }

    public function delete($filename)
    {
        $filePath = storage_path('app/backups/' . $filename);

        if (!preg_match('/^backup_ojs_[\w\-]+\.sql$/', $filename)) {
            abort(403);
        }

        if (file_exists($filePath)) {
            unlink($filePath);
        }

        return redirect()->route('backup')->with('success', "File {$filename} berhasil dihapus.");
    }
}
