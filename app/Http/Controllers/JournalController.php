<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JournalController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');

        $query = DB::table('journals as j')
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
            ->orderBy('j.seq');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('js.setting_value', 'like', "%{$search}%")
                  ->orWhere('jd.setting_value', 'like', "%{$search}%")
                  ->orWhere('j.path', 'like', "%{$search}%");
            });
        }

        $journals = $query->get()->map(function ($journal) {
            $journal->article_count = DB::table('submissions')
                ->where('context_id', $journal->journal_id)
                ->count();
            if (empty($journal->name)) {
                $journal->name = strtoupper($journal->path);
            }
            return $journal;
        });

        $total = DB::table('journals')->count();

        return view('journal', compact('journals', 'total', 'search'));
    }
}
