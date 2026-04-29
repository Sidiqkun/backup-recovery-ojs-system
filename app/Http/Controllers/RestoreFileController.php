<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RestoreFileController extends Controller
{
    private function ojsFilesPath(): string
    {
        return env('OJS_FILES_PATH', 'C:\\laragon\\www\\ojs3\\files');
    }

    public function index()
    {
        $ojsPath = $this->ojsFilesPath();
        $exists  = is_dir($ojsPath);

        return view('restore-file', compact('ojsPath', 'exists'));
    }

    public function run(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|max:512000',
        ]);

        $file    = $request->file('backup_file');
        $tmpPath = $file->getRealPath();

        // Validate it's a ZIP file
        if (!class_exists('ZipArchive')) {
            return response()->json([
                'success' => false,
                'message' => 'PHP ZipArchive extension tidak aktif.',
            ]);
        }

        $zip = new \ZipArchive();
        if ($zip->open($tmpPath) !== true) {
            return response()->json([
                'success' => false,
                'message' => 'File yang diupload bukan ZIP yang valid atau rusak.',
            ]);
        }

        $ojsPath = $this->ojsFilesPath();

        // Create destination folder if not exists
        if (!is_dir($ojsPath)) {
            mkdir($ojsPath, 0755, true);
        }

        // Security: sanitize all entry paths to prevent path traversal
        $totalFiles  = $zip->count();
        $extracted   = 0;
        $skipped     = 0;

        for ($i = 0; $i < $totalFiles; $i++) {
            $entryName = $zip->getNameIndex($i);

            // Remove any leading slashes or ".." traversals
            $safeName = ltrim(str_replace(['..', '\\'], ['', '/'], $entryName), '/');

            if (empty($safeName) || substr($safeName, -1) === '/') {
                // It's a directory entry — create it
                $dirPath = $ojsPath . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $safeName);
                if (!is_dir($dirPath)) {
                    mkdir($dirPath, 0755, true);
                }
                continue;
            }

            $destPath = $ojsPath . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $safeName);
            $destDir  = dirname($destPath);

            if (!is_dir($destDir)) {
                mkdir($destDir, 0755, true);
            }

            $data = $zip->getFromIndex($i);
            if ($data === false) {
                $skipped++;
                continue;
            }

            file_put_contents($destPath, $data);
            $extracted++;
        }

        $zip->close();

        return response()->json([
            'success'   => true,
            'message'   => "Restore file berhasil! {$extracted} file dikembalikan ke folder OJS.",
            'extracted' => $extracted,
            'skipped'   => $skipped,
        ]);
    }
}
