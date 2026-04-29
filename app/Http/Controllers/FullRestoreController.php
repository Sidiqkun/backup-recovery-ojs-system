<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FullRestoreController extends Controller
{
    private function ojsFilesPath(): string
    {
        return env('OJS_FILES_PATH', 'C:\\laragon\\www\\ojs3\\files');
    }

    public function index()
    {
        return view('full-restore');
    }

    public function run(Request $request)
    {
        $request->validate(['backup_file' => 'required|file|max:1024000', // max 1GB
        ]);

        $file    = $request->file('backup_file');
        $tmpPath = $file->getRealPath();

        if (!class_exists('ZipArchive')) {
            return response()->json(['success' => false, 'message' => 'ZipArchive tidak aktif.']);
        }

        $zip = new \ZipArchive();
        if ($zip->open($tmpPath) !== true) {
            return response()->json(['success' => false, 'message' => 'File tidak valid.']);
        }

        // Verify if database.sql exists in ZIP
        if ($zip->locateName('database.sql') === false) {
            $zip->close();
            return response()->json(['success' => false, 'message' => 'File database.sql tidak ditemukan dalam ZIP Full Backup.']);
        }

        $ojsPath = $this->ojsFilesPath();
        if (!is_dir($ojsPath)) mkdir($ojsPath, 0755, true);

        // Extract DB
        $tempPath = storage_path('app/temp');
        if (!is_dir($tempPath)) mkdir($tempPath, 0755, true);
        
        $sqlPath = $tempPath . '/database_restore.sql';
        file_put_contents($sqlPath, $zip->getFromName('database.sql'));

        // 1. Restore Database
        $dbName = config('database.connections.mysql.database');
        $dbHost = config('database.connections.mysql.host');
        $dbPort = config('database.connections.mysql.port', 3306);
        $dbUser = config('database.connections.mysql.username');
        $dbPass = config('database.connections.mysql.password');

        $mysql = 'mysql';
        $candidates = array_merge(
            glob('C:\\laragon\\bin\\mysql*\\*\\bin\\mysql.exe') ?: [],
            glob('C:\\laragon\\bin\\mariadb*\\*\\bin\\mysql.exe') ?: []
        );
        if (!empty($candidates)) $mysql = $candidates[0];

        $cmd = '"' . $mysql . '" -h ' . escapeshellarg($dbHost) . ' -P ' . $dbPort . ' -u ' . escapeshellarg($dbUser) . ($dbPass ? ' -p' . escapeshellarg($dbPass) : '') . ' ' . escapeshellarg($dbName) . ' < ' . escapeshellarg($sqlPath) . ' 2>&1';
        exec($cmd, $output, $returnCode);

        unlink($sqlPath); // Cleanup

        if ($returnCode !== 0) {
            $zip->close();
            return response()->json(['success' => false, 'message' => 'Gagal restore DB: ' . implode(" ", $output)]);
        }

        // 2. Restore Files (Extract from ZIP's "files/" directory to OJS path)
        $extracted = 0;
        for ($i = 0; $i < $zip->count(); $i++) {
            $entryName = $zip->getNameIndex($i);
            
            // Only process items in "files/" folder
            if (strpos($entryName, 'files/') === 0) {
                $relativePath = substr($entryName, 6); // remove 'files/'
                if (empty($relativePath)) continue;

                $destPath = $ojsPath . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);

                if (substr($entryName, -1) === '/') {
                    if (!is_dir($destPath)) mkdir($destPath, 0755, true);
                    continue;
                }

                $destDir = dirname($destPath);
                if (!is_dir($destDir)) mkdir($destDir, 0755, true);

                $data = $zip->getFromIndex($i);
                if ($data !== false) {
                    file_put_contents($destPath, $data);
                    $extracted++;
                }
            }
        }
        $zip->close();

        return response()->json([
            'success' => true,
            'message' => "Keseluruhan sistem berhasil direstore (Database + {$extracted} file)!",
        ]);
    }
}
