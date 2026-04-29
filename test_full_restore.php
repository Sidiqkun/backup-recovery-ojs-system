<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing Full Restore Logic...\n";

// 1. Get latest backup
$backupPath = storage_path('app/full-backups');
$files = glob($backupPath . '/*.zip');
if (empty($files)) {
    echo "❌ No backup found.\n";
    exit(1);
}
usort($files, fn($a, $b) => filemtime($b) - filemtime($a));
$latestBackup = $files[0];
echo "Latest Backup: " . basename($latestBackup) . "\n";

// 2. Truncate current data (using our existing script)
echo "Truncating data for test...\n";
passthru('php truncate_for_test.php verify'); // Verify first
passthru('php truncate_for_test.php'); // Truncate
passthru('php truncate_for_test.php verify'); // Verify empty

// 3. Run Restore logic
echo "Restoring from ZIP...\n";
$controller = new \App\Http\Controllers\FullRestoreController();

// Create a dummy Request with the file
// Note: In a real controller, this comes from an uploaded file. 
// For CLI test, we'll manually implement the logic or mock the file.
// Since we want to test the controller logic:
class FakeFile {
    public $path;
    public function __construct($p) { $this->path = $p; }
    public function getRealPath() { return $this->path; }
}

$request = new \Illuminate\Http\Request();
// We'll bypass validation and call the run logic directly or mock it.
// Let's call the 'run' method logic but bypass request validation.

$response = $controller->run(new class($latestBackup) extends \Illuminate\Http\Request {
    public $latestBackup;
    public function __construct($p) { $this->latestBackup = $p; }
    public function file($key = null, $default = null) { return new FakeFile($this->latestBackup); }
    public function validate($rules, ...$params) { return true; } // Bypass
});

$data = json_decode($response->getContent(), true);

if ($data['success']) {
    echo "✅ RESTORE SUCCESS: " . $data['message'] . "\n";
} else {
    echo "❌ RESTORE FAILED: " . $data['message'] . "\n";
}

// 4. Verify data is back
echo "Verifying data after restore...\n";
passthru('php truncate_for_test.php verify');
