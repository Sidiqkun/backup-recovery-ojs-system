<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing Full Backup Logic...\n";

$controller = new \App\Http\Controllers\FullBackupController();
$request = new \Illuminate\Http\Request();

$response = $controller->run($request);
$data = json_decode($response->getContent(), true);

if ($data['success']) {
    echo "✅ SUCCESS: " . $data['message'] . "\n";
    echo "Filename: " . $data['file_name'] . "\n";
    echo "Size: " . $data['file_size'] . "\n";
    echo "Files included: " . $data['total_files'] . "\n";
    
    $fullPath = storage_path('app/full-backups/' . $data['file_name']);
    if (file_exists($fullPath)) {
        echo "File exists on disk: YES\n";
    }
} else {
    echo "❌ FAILED: " . $data['message'] . "\n";
}
