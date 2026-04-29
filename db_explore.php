<?php
$ojsFilesPath = 'C:\\laragon\\www\\ojs3\\files';

if (!is_dir($ojsFilesPath)) {
    echo "❌ Folder tidak ditemukan: $ojsFilesPath\n";
    exit(1);
}

echo "=== FOLDER OJS FILES ===\n";
echo "Path: $ojsFilesPath\n\n";

// Count files and size recursively
$totalSize  = 0;
$totalFiles = 0;
$byType     = [];

$iter = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($ojsFilesPath, RecursiveDirectoryIterator::SKIP_DOTS)
);

foreach ($iter as $file) {
    if ($file->isFile()) {
        $size = $file->getSize();
        $ext  = strtolower($file->getExtension());
        $totalSize += $size;
        $totalFiles++;
        $byType[$ext] = ($byType[$ext] ?? 0) + 1;
    }
}

echo "Total file  : $totalFiles\n";
echo "Total ukuran: " . round($totalSize / 1024 / 1024, 2) . " MB\n\n";

echo "=== JENIS FILE ===\n";
arsort($byType);
foreach ($byType as $ext => $count) {
    echo "  .$ext : $count file\n";
}

echo "\n=== 5 FOLDER TERATAS ===\n";
$dirs = glob($ojsFilesPath . '/*', GLOB_ONLYDIR);
foreach (array_slice($dirs, 0, 5) as $dir) {
    echo "  " . basename($dir) . "/\n";
}
