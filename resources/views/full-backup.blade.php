<x-app-layout>
    <x-slot name="header">Full Backup Sistem OJS</x-slot>

    <div class="space-y-6 max-w-4xl">

        <div>
            <h2 class="text-xl font-semibold text-gray-900 mb-2">Full Backup (Database + Files)</h2>
            <p class="text-sm text-gray-500">Membungkus seluruh database dan folder file fisik (cover, pdf, dll) ke dalam satu file ZIP tunggal.</p>
        </div>

        @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 flex items-center gap-3">
            <svg class="w-5 h-5 text-green-600 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
            <p class="text-green-800 font-medium">{{ session('success') }}</p>
        </div>
        @endif

        {{-- Info Panel --}}
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Informasi Sistem</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <p class="text-xs font-semibold text-blue-800 tracking-wide uppercase mb-1">Database Component</p>
                    <p class="text-sm text-gray-700">Size: <span class="font-bold text-gray-900">{{ $dbSizeMb }} MB</span></p>
                </div>
                <div class="bg-blue-50 p-4 rounded-lg">
                    <p class="text-xs font-semibold text-blue-800 tracking-wide uppercase mb-1">File Component</p>
                    <p class="text-sm text-gray-700">Folder: <span class="font-mono text-xs">{{ $ojsPath }}</span></p>
                    <p class="text-sm text-gray-700 mt-1">Total: <span class="font-bold text-gray-900">{{ $totalFiles }} files</span></p>
                </div>
            </div>
        </div>

        {{-- Action Button --}}
        <button id="btn-full-backup" onclick="startFullBackup()" @if(!$exists) disabled @endif
            class="w-full bg-blue-600 text-white py-4 rounded-lg hover:bg-blue-700 transition-colors disabled:bg-gray-300 disabled:cursor-not-allowed font-semibold flex items-center justify-center gap-2">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
            Jalankan Full Backup
        </button>

        {{-- Progress Section --}}
        <div id="full-backup-progress" class="hidden bg-white rounded-lg border border-gray-200 p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Progress Eksekusi</h3>
            <div class="space-y-4">
                <div>
                    <div class="flex justify-between mb-2">
                        <span id="fb-label" class="text-sm text-gray-600">Menyatukan file...</span>
                        <span id="fb-pct" class="text-sm font-semibold text-gray-900">0%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div id="fb-bar" class="bg-blue-600 h-3 rounded-full transition-all duration-500" style="width:0%"></div>
                    </div>
                </div>

                {{-- Success State --}}
                <div id="fb-result" class="hidden bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center gap-3 mb-3">
                        <svg class="w-5 h-5 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
                        <p class="font-semibold text-green-900">Full Backup berhasil diselesaikan!</p>
                    </div>
                    <p id="fb-detail" class="text-sm text-green-800 mb-3"></p>
                    <a id="fb-download-link" href="#" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                        <span id="fb-download-label">Download ZIP File</span>
                    </a>
                </div>

                {{-- Error State --}}
                <div id="fb-error" class="hidden bg-red-50 border border-red-200 rounded-lg p-4">
                    <p class="text-red-800 font-medium">Backup gagal!</p>
                    <p id="fb-error-msg" class="text-red-700 text-sm mt-1"></p>
                </div>
            </div>
        </div>

        {{-- History Table --}}
        @if(count($backups) > 0)
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Riwayat Full Backup</h3>
            <div class="divide-y divide-gray-100">
                @foreach($backups as $backup)
                <div class="flex items-center justify-between py-3">
                    <div>
                        <p class="font-medium text-gray-900 text-sm">{{ $backup['name'] }}</p>
                        <p class="text-xs text-gray-500">{{ $backup['created'] }} &bull; {{ $backup['size'] }}</p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('full-backup.download', $backup['name']) }}"
                           class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors text-sm">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                            Download
                        </a>
                        <form method="POST" action="{{ route('full-backup.delete', $backup['name']) }}" onsubmit="return confirm('Hapus backup ini secara permanen?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors text-sm">
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <script>
    function startFullBackup() {
        const btn = document.getElementById('btn-full-backup');
        const prog= document.getElementById('full-backup-progress');
        const bar = document.getElementById('fb-bar');
        const lbl = document.getElementById('fb-label');
        const pct = document.getElementById('fb-pct');
        const res = document.getElementById('fb-result');
        const err = document.getElementById('fb-error');

        btn.disabled = true; btn.classList.add('opacity-60');
        prog.classList.remove('hidden'); res.classList.add('hidden'); err.classList.add('hidden');

        let fakeP = 0, state = 0;
        const msg = ['Dumping Database (.sql)...', 'Zipping Files...', 'Finalizing ZIP...'];
        lbl.textContent = msg[0];

        const timer = setInterval(() => {
            fakeP += Math.random() * 3;
            if(fakeP >= 90) fakeP = 90;
            bar.style.width = fakeP + '%'; pct.textContent = Math.round(fakeP) + '%';
            if(fakeP > 30 && state == 0) { lbl.textContent = msg[1]; state = 1; }
            if(fakeP > 70 && state == 1) { lbl.textContent = msg[2]; state = 2; }
        }, 500);

        fetch('{{ route('full-backup.run') }}', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'}
        }).then(r => r.json()).then(data => {
            clearInterval(timer);
            bar.style.width = '100%'; pct.textContent = '100%'; lbl.textContent = 'Selesai';
            if(data.success) {
                res.classList.remove('hidden');
                document.getElementById('fb-detail').textContent = `Total terkompres: ${data.total_files} file (${data.file_size})`;
                document.getElementById('fb-download-link').href = '/full-backup/download/' + data.file_name;
                document.getElementById('fb-download-label').textContent = 'Download ' + data.file_name;
            } else {
                err.classList.remove('hidden');
                document.getElementById('fb-error-msg').textContent = data.message;
            }
            btn.disabled = false; btn.classList.remove('opacity-60');
        }).catch(e => {
            clearInterval(timer);
            err.classList.remove('hidden'); document.getElementById('fb-error-msg').textContent = e.message;
            btn.disabled = false; btn.classList.remove('opacity-60');
        });
    }
    </script>
</x-app-layout>
