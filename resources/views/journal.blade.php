<x-app-layout>
    <x-slot name="header">Data Jurnal</x-slot>

    <div class="space-y-6">
        <div>
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Data Jurnal</h2>
        </div>

        {{-- Search --}}
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <form method="GET" action="{{ route('journal') }}">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Cari jurnal berdasarkan nama atau deskripsi..."
                        class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    />
                </div>
            </form>
        </div>

        {{-- Count --}}
        <p class="text-sm text-gray-600">
            Menampilkan {{ count($journals) }} dari {{ $total }} jurnal
        </p>

        {{-- Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($journals as $journal)
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow">
                <div class="h-48 bg-gradient-to-br from-blue-400 to-indigo-600 flex items-center justify-center">
                    <svg class="w-16 h-16 text-white opacity-70" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>
                </div>
                <div class="p-6">
                    <p class="text-xs font-medium text-blue-600 uppercase tracking-wide mb-1">{{ $journal->path }}</p>
                    <h3 class="font-semibold text-lg text-gray-900 mb-2">{{ $journal->name }}</h3>
                    <p class="text-sm text-gray-600 mb-4 line-clamp-2">
                        {{ $journal->description ? strip_tags(substr($journal->description, 0, 150)) : 'Tidak ada deskripsi.' }}
                    </p>
                    <p class="text-sm text-gray-500 mb-4">{{ $journal->article_count }} artikel / submission</p>
                    <div class="flex gap-2">
                        <a href="{{ route('backup') }}"
                           class="flex-1 flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="12" cy="5" rx="9" ry="3"></ellipse><path d="M3 5v14c0 1.66 4.03 3 9 3s9-1.34 9-3V5"></path><path d="M3 12c0 1.66 4.03 3 9 3s9-1.34 9-3"></path></svg>
                            <span>Backup Jurnal</span>
                        </a>
                        <a href="{{ route('restore') }}"
                           class="flex-1 flex items-center justify-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
                            <span>Restore</span>
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-3 text-center py-12">
                <p class="text-gray-500">Tidak ada jurnal yang sesuai dengan pencarian "<strong>{{ $search }}</strong>"</p>
                <a href="{{ route('journal') }}" class="mt-2 inline-block text-sm text-blue-600 hover:underline">Tampilkan semua</a>
            </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
