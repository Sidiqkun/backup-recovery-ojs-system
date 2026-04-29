<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OtpAuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\BackupFileController;
use App\Http\Controllers\FullBackupController;
use App\Http\Controllers\RestoreController;
use App\Http\Controllers\RestoreFileController;
use App\Http\Controllers\FullRestoreController;
use App\Http\Controllers\JournalController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Authentication Routes (no middleware)
Route::get('/login',       [OtpAuthController::class, 'showLogin'])->name('login');
Route::post('/login',      [OtpAuthController::class, 'processLogin']);
Route::get('/verify-otp',  [OtpAuthController::class, 'showVerify'])->name('otp.verify');
Route::post('/verify-otp', [OtpAuthController::class, 'processVerify']);
Route::post('/logout',     [OtpAuthController::class, 'logout'])->name('logout');

// ============================================================
// DEV ONLY: Bypass login untuk testing (JANGAN gunakan di production)
// Aktifkan dengan menambahkan DEV_SKIP_LOGIN=true di .env
// Akses: http://localhost:8000/dev-login
// ============================================================
if (app()->environment('local') && env('DEV_SKIP_LOGIN', false)) {
    Route::get('/dev-login', function () {
        session(['admin_logged_in' => true]);
        return redirect()->route('dashboard')->with('success', '[DEV] Login bypass aktif.');
    })->name('dev.login');
}


// Protected Admin Routes
Route::middleware(['admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Backup DB
    Route::get('/backup',              [BackupController::class, 'index'])->name('backup');
    Route::post('/backup/run',         [BackupController::class, 'run'])->name('backup.run');
    Route::get('/backup/download/{filename}', [BackupController::class, 'download'])->name('backup.download');
    Route::delete('/backup/{filename}', [BackupController::class, 'delete'])->name('backup.delete');

    // Backup File OJS
    Route::get('/backup-file',                          [BackupFileController::class, 'index'])->name('backup-file');
    Route::post('/backup-file/run',                     [BackupFileController::class, 'run'])->name('backup-file.run');
    Route::get('/backup-file/download/{filename}',      [BackupFileController::class, 'download'])->name('backup-file.download');
    Route::delete('/backup-file/{filename}',            [BackupFileController::class, 'delete'])->name('backup-file.delete');

    // Full Backup
    Route::get('/full-backup',                          [FullBackupController::class, 'index'])->name('full-backup');
    Route::post('/full-backup/run',                     [FullBackupController::class, 'run'])->name('full-backup.run');
    Route::get('/full-backup/download/{filename}',      [FullBackupController::class, 'download'])->name('full-backup.download');
    Route::delete('/full-backup/{filename}',            [FullBackupController::class, 'delete'])->name('full-backup.delete');

    // Restore DB
    Route::get('/restore',  [RestoreController::class, 'index'])->name('restore');
    Route::post('/restore', [RestoreController::class, 'run'])->name('restore.run');

    // Restore File OJS
    Route::get('/restore-file',  [RestoreFileController::class, 'index'])->name('restore-file');
    Route::post('/restore-file', [RestoreFileController::class, 'run'])->name('restore-file.run');

    // Full Restore
    Route::get('/full-restore',  [FullRestoreController::class, 'index'])->name('full-restore');
    Route::post('/full-restore', [FullRestoreController::class, 'run'])->name('full-restore.run');

    // Journal Data
    Route::get('/journal', [JournalController::class, 'index'])->name('journal');
});
