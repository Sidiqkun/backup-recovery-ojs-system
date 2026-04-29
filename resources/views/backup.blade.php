<x-app-layout>
    <x-slot name="header">Backup Sistem</x-slot>

    <div class="space-y-6 max-w-4xl">

        <div>
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Backup Sistem OJS</h2>
        </div>

        @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 flex items-center gap-3">
            <svg class="w-5 h-5 text-green-600 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="m9 12 2 2 4-4"></path></svg>
            <p class="text-green-800 font-medium">{{ session('success') }}</p>
        </div>
        @endif

        {{-- Info Sistem --}}
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Informasi Sistem</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600 mb-1">Database</p>
                    <p class="font-semibold text-gray-900">MySQL / MariaDB</p>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600 mb-1">Nama Database</p>
                    <p class="font-semibold text-gray-900">{{ $dbName }}</p>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600 mb-1">Ukuran Database</p>
                    <p class="font-semibold text-gray-900">{{ $dbSizeMb }} MB</p>
                </div>
            </div>
        </div>

        {{-- Backup Options --}}
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Pilihan Backup</h3>
            <div class="space-y-4">
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" id="opt-database" checked class="mt-1 w-5 h-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                    <div>
                        <p class="font-medium text-gray-900">Backup Database OJS</p>
                        <p class="text-sm text-gray-600">Mengambil semua tabel: journals, submissions, issues, users, publications, dll.</p>
                    </div>
                </label>
            </div>
        </div>

        {{-- Action Button --}}
        <button id="btn-backup"
            onclick="startBackup()"
            class="w-full bg-blue-600 text-white py-4 rounded-lg hover:bg-blue-700 transition-colors font-semibold flex items-center justify-center gap-2">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="12" cy="5" rx="9" ry="3"></ellipse><path d="M3 5v14c0 1.66 4.03 3 9 3s9-1.34 9-3V5"></path><path d="M3 12c0 1.66 4.03 3 9 3s9-1.34 9-3"></path></svg>
            Buat Backup Sekarang
        </button>

        {{-- Progress Section (hidden by default) --}}
        <div id="backup-progress" class="hidden bg-white rounded-lg border border-gray-200 p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Progress Backup</h3>
            <div class="space-y-4">
                <div>
                    <div class="flex justify-between mb-2">
                        <span id="progress-label" class="text-sm text-gray-600">Memulai backup...</span>
                        <span id="progress-pct" class="text-sm font-semibold text-gray-900">0%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div id="progress-bar" class="bg-blue-600 h-3 rounded-full transition-all duration-500" style="width:0%"></div>
                    </div>
                </div>
                <div id="backup-result" class="hidden">
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex items-center gap-3 mb-3">
                            <svg class="w-5 h-5 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="m9 12 2 2 4-4"></path></svg>
                            <p class="font-semibold text-green-900">Backup berhasil dibuat!</p>
                        </div>
                        <a id="download-link" href="#"
                           class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                            <span id="download-label">Download Backup</span>
                        </a>
                    </div>
                </div>
                <div id="backup-error" class="hidden bg-red-50 border border-red-200 rounded-lg p-4">
                    <p class="text-red-800 font-medium">Backup gagal!</p>
                    <p id="error-message" class="text-red-700 text-sm mt-1"></p>
                </div>
            </div>
        </div>

        {{-- Existing Backups --}}
        @if(count($backups) > 0)
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Riwayat Backup</h3>
            <div class="divide-y divide-gray-100">
                @foreach($backups as $backup)
                <div class="flex items-center justify-between py-3">
                    <div>
                        <p class="font-medium text-gray-900 text-sm">{{ $backup['name'] }}</p>
                        <p class="text-xs text-gray-500">{{ $backup['created'] }} &bull; {{ $backup['size'] }}</p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('backup.download', $backup['name']) }}"
                           class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors text-sm">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                            Download
                        </a>
                        <form method="POST" action="{{ route('backup.delete', $backup['name']) }}" onsubmit="return confirm('Hapus backup ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors text-sm">
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
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
    function startBackup() {
        const btn = document.getElementById('btn-backup');
        const progress = document.getElementById('backup-progress');
        const bar = document.getElementById('progress-bar');
        const label = document.getElementById('progress-label');
        const pct = document.getElementById('progress-pct');
        const result = document.getElementById('backup-result');
        const errorDiv = document.getElementById('backup-error');

        btn.disabled = true;
        btn.classList.add('opacity-60', 'cursor-not-allowed');
        progress.classList.remove('hidden');
        result.classList.add('hidden');
        errorDiv.classList.add('hidden');

        // Animated progress simulation while waiting for server
        label.textContent = 'Mengambil database...';
        let fakeProgress = 0;
        const interval = setInterval(() => {
            if (fakeProgress < 85) {
                fakeProgress += Math.random() * 5;
                bar.style.width = Math.min(fakeProgress, 85) + '%';
                pct.textContent = Math.round(Math.min(fakeProgress, 85)) + '%';
                if (fakeProgress > 40) label.textContent = 'Mengarsip data...';
                if (fakeProgress > 70) label.textContent = 'Menyelesaikan backup...';
            }
        }, 300);

        fetch('{{ route('backup.run') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ backup_type: 'database' })
        })
        .then(r => r.json())
        .then(data => {
            clearInterval(interval);
            bar.style.width = '100%';
            pct.textContent = '100%';
            label.textContent = 'Selesai';

            if (data.success) {
                result.classList.remove('hidden');
                const dlLink = document.getElementById('download-link');
                dlLink.href = '/backup/download/' + data.file_name;
                document.getElementById('download-label').textContent = 'Download ' + data.file_name + ' (' + data.file_size + ')';
            } else {
                errorDiv.classList.remove('hidden');
                document.getElementById('error-message').textContent = data.message;
            }
            btn.disabled = false;
            btn.classList.remove('opacity-60', 'cursor-not-allowed');
        })
        .catch(err => {
            clearInterval(interval);
            errorDiv.classList.remove('hidden');
            document.getElementById('error-message').textContent = 'Terjadi kesalahan koneksi: ' + err.message;
            btn.disabled = false;
            btn.classList.remove('opacity-60', 'cursor-not-allowed');
        });
    }
    </script>
</x-app-layout>
