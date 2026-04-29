<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== SYSTEM DEBUG REPORT ===\n";

// 1. Env Check
echo "\n[1] Environment Check:\n";
echo "APP_URL: " . env('APP_URL') . "\n";
echo "ADMIN_EMAIL: " . env('ADMIN_EMAIL') . "\n";
echo "OJS_FILES_PATH: " . env('OJS_FILES_PATH') . "\n";
echo "DB_DATABASE: " . config('database.connections.mysql.database') . "\n";

// 2. Database Check
echo "\n[2] Database Check:\n";
try {
    $tables = DB::select("SHOW TABLES");
    echo "Connection: OK\n";
    echo "Total Tables: " . count($tables) . "\n";
} catch (\Exception $e) {
    echo "Connection: FAIL - " . $e->getMessage() . "\n";
}

// 3. Binary Check
echo "\n[3] Binary Check:\n";
$mysql = 'mysql'; 
$mysqldump = 'mysqldump';

$mysqlCandidates = array_merge(
    glob('C:\\laragon\\bin\\mysql*\\*\\bin\\mysql.exe') ?: [],
    glob('C:\\laragon\\bin\\mariadb*\\*\\bin\\mysql.exe') ?: []
);
$dumpCandidates = array_merge(
    glob('C:\\laragon\\bin\\mysql*\\*\\bin\\mysqldump.exe') ?: [],
    glob('C:\\laragon\\bin\\mariadb*\\*\\bin\\mysqldump.exe') ?: []
);

if (!empty($mysqlCandidates)) {
    echo "MySQL Binary: " . $mysqlCandidates[0] . " (FOUND)\n";
} else {
    echo "MySQL Binary: NOT FOUND IN LARAGON BIN\n";
}

if (!empty($dumpCandidates)) {
    echo "MySQLDump Binary: " . $dumpCandidates[0] . " (FOUND)\n";
} else {
    echo "MySQLDump Binary: NOT FOUND IN LARAGON BIN\n";
}

// 4. Extensions Check
echo "\n[4] Extension Check:\n";
echo "ZipArchive: " . (class_exists('ZipArchive') ? 'OK' : 'MISSING') . "\n";

// 5. Directory Check
echo "\n[5] Directory Check:\n";
$ojsPath = env('OJS_FILES_PATH', 'C:\\laragon\\www\\ojs3\\files');
echo "OJS Files Path ($ojsPath): " . (is_dir($ojsPath) ? 'EXISTS' : 'NOT FOUND') . "\n";

$backupPaths = [
    storage_path('app/backups'),
    storage_path('app/file-backups'),
    storage_path('app/full-backups'),
    storage_path('app/temp')
];
foreach ($backupPaths as $path) {
    echo "Backup Path (" . basename($path) . "): " . (is_dir($path) ? 'OK' : 'NOT FOUND (Will be created)') . "\n";
}

echo "\n=== DEBUG COMPLETE ===\n";
