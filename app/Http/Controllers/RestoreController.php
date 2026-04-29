<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RestoreController extends Controller
{
    public function index()
    {
        return view('restore');
    }

    public function run(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|mimes:sql,txt|max:512000', // max 512MB
        ]);

        $file = $request->file('backup_file');
        $tmpPath = $file->getRealPath();

        // Basic validation: check if it looks like a SQL dump
        $firstLine = fgets(fopen($tmpPath, 'r'));
        if (stripos($firstLine, 'mysql') === false && stripos($firstLine, 'mariadb') === false && stripos($firstLine, '--') === false) {
            return response()->json([
                'success' => false,
                'message' => 'File bukan merupakan file SQL dump yang valid.',
            ]);
        }

        $dbName  = config('database.connections.mysql.database');
        $dbHost  = config('database.connections.mysql.host');
        $dbPort  = config('database.connections.mysql.port', 3306);
        $dbUser  = config('database.connections.mysql.username');
        $dbPass  = config('database.connections.mysql.password');

        // Find mysql client binary in Laragon
        $mysql = 'mysql'; // fallback
        $candidates = array_merge(
            glob('C:\\laragon\\bin\\mysql*\\*\\bin\\mysql.exe') ?: [],
            glob('C:\\laragon\\bin\\mariadb*\\*\\bin\\mysql.exe') ?: []
        );
        if (!empty($candidates)) {
            $mysql = $candidates[0];
        }

        $cmd = '"' . $mysql . '"'
            . ' -h ' . escapeshellarg($dbHost)
            . ' -P ' . $dbPort
            . ' -u ' . escapeshellarg($dbUser)
            . ($dbPass ? ' -p' . escapeshellarg($dbPass) : '')
            . ' ' . escapeshellarg($dbName)
            . ' < ' . escapeshellarg($tmpPath)
            . ' 2>&1';

        exec($cmd, $output, $returnCode);

        if ($returnCode !== 0) {
            return response()->json([
                'success' => false,
                'message' => 'Restore gagal: ' . implode("\n", $output),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Restore database berhasil! Data OJS telah dikembalikan.',
        ]);
    }
}
