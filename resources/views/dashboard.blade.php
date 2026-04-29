<x-app-layout>
    <x-slot name="header">Dashboard Backup & Restore OJS</x-slot>

    <div class="space-y-8">

        {{-- ===== STAT CARDS ===== --}}
        <div>
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Statistik Sistem</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Total Jurnal</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $totalJournals }}</p>
                        </div>
                        <div class="p-3 rounded-lg bg-blue-100 text-blue-600">
                            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Total Issue Terbit</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $totalIssues }}</p>
                        </div>
                        <div class="p-3 rounded-lg bg-green-100 text-green-600">
                            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/></svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Total Artikel</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $totalArticles }}</p>
                        </div>
                        <div class="p-3 rounded-lg bg-purple-100 text-purple-600">
                            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Total File Sistem</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $totalFiles }}</p>
                        </div>
                        <div class="p-3 rounded-lg bg-orange-100 text-orange-600">
                            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- ===== JOURNAL LIST ===== --}}
        <div>
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Daftar Jurnal</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($journals as $journal)
                <div class="bg-white rounded-lg border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow">
                    <div class="h-48 bg-gradient-to-br from-blue-400 to-indigo-600 flex items-center justify-center">
                        <svg class="w-16 h-16 text-white opacity-70" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>
                    </div>
                    <div class="p-6">
                        <h3 class="font-semibold text-lg text-gray-900 mb-2">{{ $journal->name }}</h3>
                        <p class="text-sm text-gray-600 mb-4 line-clamp-2">
                            {{ $journal->description ? strip_tags(substr($journal->description, 0, 150)) : 'Tidak ada deskripsi.' }}
                        </p>
                        <p class="text-sm text-gray-500 mb-4">{{ $journal->article_count }} artikel / submission</p>
                        <div class="flex gap-2">
                            <a href="{{ route('journal') }}?search={{ urlencode($journal->name) }}"
                               class="flex-1 flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                <span>Lihat Detail</span>
                            </a>
                            <a href="{{ route('backup') }}"
                               class="flex-1 flex items-center justify-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="12" cy="5" rx="9" ry="3"></ellipse><path d="M3 5v14c0 1.66 4.03 3 9 3s9-1.34 9-3V5"></path><path d="M3 12c0 1.66 4.03 3 9 3s9-1.34 9-3"></path></svg>
                                <span>Backup</span>
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-3 text-center py-12 text-gray-500">
                    Tidak ada data jurnal yang ditemukan.
                </div>
                @endforelse
            </div>
        </div>

    </div>
</x-app-layout>
