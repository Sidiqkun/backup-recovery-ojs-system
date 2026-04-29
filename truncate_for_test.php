<?php
/**
 * SCRIPT TES RESTORE - Hapus semua data OJS lalu bisa di-restore
 * 
 * Cara pakai:
 *   php truncate_for_test.php         -> TRUNCATE semua tabel
 *   php truncate_for_test.php verify  -> Cek jumlah data sekarang
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$mode = $argv[1] ?? 'truncate';

$keyTables = [
    'journals', 'journal_settings',
    'submissions', 'submission_settings', 'submission_files',
    'submission_file_settings', 'submission_file_revisions',
    'issues', 'issue_settings', 'issue_galleys', 'issue_galley_settings', 'issue_files',
    'publications', 'publication_settings', 'publication_galleys', 'publication_galley_settings',
    'publication_categories',
    'users', 'user_settings', 'user_groups', 'user_group_settings',
    'user_user_groups', 'user_group_stage', 'user_interests',
    'authors', 'author_settings',
    'sections', 'section_settings',
    'review_assignments', 'review_files', 'review_rounds', 'review_round_files',
    'review_forms', 'review_form_settings', 'review_form_elements',
    'review_form_element_settings', 'review_form_responses',
    'event_log', 'event_log_settings',
    'email_log', 'email_log_users',
    'notifications', 'notification_settings',
    'notes', 'queries', 'query_participants',
    'stage_assignments', 'edit_decisions',
    'plugin_settings', 'scheduled_tasks',
    'site', 'site_settings',
    'categories', 'category_settings',
    'announcements', 'announcement_settings', 'announcement_types', 'announcement_type_settings',
    'navigation_menus', 'navigation_menu_items', 'navigation_menu_item_settings',
    'navigation_menu_item_assignments', 'navigation_menu_item_assignment_settings',
    'static_pages', 'static_page_settings',
    'sessions', 'jobs',
];

if ($mode === 'verify') {
    echo "=== CEK DATA SAAT INI ===\n";
    foreach (['journals', 'submissions', 'issues', 'users', 'publications', 'plugin_settings', 'site'] as $t) {
        try {
            $count = DB::table($t)->count();
            echo str_pad($t, 25) . ": $count row(s)\n";
        } catch (\Exception $e) {
            echo str_pad($t, 25) . ": ERROR - " . $e->getMessage() . "\n";
        }
    }
    exit(0);
}

// TRUNCATE mode
echo "⚠️  PERINGATAN: Script ini akan menghapus semua data OJS!\n";
echo "Tekan CTRL+C untuk membatalkan...\n";
sleep(3);

echo "\n=== MULAI TRUNCATE ALL TABLES ===\n";
DB::statement('SET FOREIGN_KEY_CHECKS=0');

$truncated = 0;
$failed = 0;
foreach ($keyTables as $table) {
    try {
        DB::table($table)->truncate();
        echo "  ✓ TRUNCATED: $table\n";
        $truncated++;
    } catch (\Exception $e) {
        echo "  ✗ SKIP: $table (" . $e->getMessage() . ")\n";
        $failed++;
    }
}

DB::statement('SET FOREIGN_KEY_CHECKS=1');

echo "\n=== SELESAI: $truncated tabel di-truncate, $failed gagal ===\n\n";
echo "=== VERIFIKASI DATA SETELAH TRUNCATE ===\n";
foreach (['journals', 'submissions', 'issues', 'users', 'publications'] as $t) {
    $count = DB::table($t)->count();
    echo str_pad($t, 25) . ": $count row(s) " . ($count === 0 ? "✓" : "⚠️ MASIH ADA DATA") . "\n";
}

echo "\n✅ Sekarang buka http://127.0.0.1:8000/restore dan upload file .sql backup untuk tes restore!\n";
