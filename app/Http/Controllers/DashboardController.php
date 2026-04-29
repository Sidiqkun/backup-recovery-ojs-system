<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Pull real stats from dbs_ojs
        $totalJournals = DB::table('journals')->count();

        $totalIssues = DB::table('issues')->count();

        $totalArticles = DB::table('publications')->where('status', 3)->count();

        $totalFiles = DB::table('submission_files')->count();

        $dbSizeMb = DB::select("
            SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb
            FROM information_schema.tables
            WHERE table_schema = DATABASE()
        ")[0]->size_mb ?? 0;

        // Get journals with their names and article counts
        $journals = DB::table('journals as j')
            ->leftJoin('journal_settings as js', function ($join) {
                $join->on('j.journal_id', '=', 'js.journal_id')
                     ->where('js.setting_name', '=', 'name')
                     ->where('js.locale', '=', 'en_US');
            })
            ->leftJoin('journal_settings as jd', function ($join) {
                $join->on('j.journal_id', '=', 'jd.journal_id')
                     ->where('jd.setting_name', '=', 'description')
                     ->where('jd.locale', '=', 'en_US');
            })
            ->select(
                'j.journal_id',
                'j.path',
                'js.setting_value as name',
                'jd.setting_value as description'
            )
            ->orderBy('j.seq')
            ->get()
            ->map(function ($journal) {
                // Get article count per journal
                $journal->article_count = DB::table('submissions')
                    ->where('context_id', $journal->journal_id)
                    ->count();
                // Use path as fallback name
                if (empty($journal->name)) {
                    $journal->name = strtoupper($journal->path);
                }
                return $journal;
            });

        return view('dashboard', compact(
            'totalJournals',
            'totalIssues',
            'totalArticles',
            'totalFiles',
            'dbSizeMb',
            'journals'
        ));
    }
}
