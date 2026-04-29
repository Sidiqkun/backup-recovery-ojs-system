<x-app-layout>
    <x-slot name="header">Backup File OJS</x-slot>

    <div class="space-y-6 max-w-4xl">

        <div>
            <h2 class="text-xl font-semibold text-gray-900 mb-2">Backup File OJS</h2>
            <p class="text-sm text-gray-500">Mengarsip seluruh file jurnal OJS (cover, submission, galley PDF/Word) ke dalam satu file ZIP.</p>
        </div>

        @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 flex items-center gap-3">
            <svg class="w-5 h-5 text-green-600 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
            <p class="text-green-800 font-medium">{{ session('success') }}</p>
        </div>
        @endif

        {{-- Folder Info --}}
        @if($exists)
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Informasi Folder OJS</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600 mb-1">Lokasi Folder</p>
                    <p class="font-semibold text-gray-900 text-xs break-all">{{ $ojsPath }}</p>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600 mb-1">Total File</p>
                    <p class="font-semibold text-gray-900">{{ $info['total_files'] }} file</p>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600 mb-1">Total Ukuran</p>
                    <p class="font-semibold text-gray-900">{{ $info['total_size_mb'] }} MB</p>
                </div>
            </div>

            @if(!empty($info['by_type']))
            <div>
                <p class="text-sm font-medium text-gray-700 mb-2">Jenis File:</p>
                <div class="flex flex-wrap gap-2">
                    @foreach($info['by_type'] as $ext => $count)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        @if(in_array($ext, ['pdf'])) bg-red-100 text-red-700
                        @elseif(in_array($ext, ['doc','docx'])) bg-blue-100 text-blue-700
                        @elseif(in_array($ext, ['jpg','jpeg','png','gif','webp'])) bg-green-100 text-green-700
                        @elseif($ext === 'log') bg-gray-100 text-gray-600
                        @else bg-purple-100 text-purple-700
                        @endif">
                        .{{ $ext ?: 'other' }} ({{ $count }})
                    </span>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        @else
        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 flex gap-3">
            <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>
            <div>
                <p class="font-semibold text-amber-900">Folder OJS tidak ditemukan!</p>
                <p class="text-sm text-amber-800">Path: <code class="font-mono">{{ $ojsPath }}</code></p>
                <p class="text-sm text-amber-800 mt-1">Sesuaikan variabel <code class="font-mono">OJS_FILES_PATH</code> di file <code class="font-mono">.env</code>.</p>
            </div>
        </div>
        @endif

        {{-- Backup Button --}}
        <button id="btn-backup-file"
            onclick="startFileBackup()"
            @if(!$exists) disabled @endif
            class="w-full bg-blue-600 text-white py-4 rounded-lg hover:bg-blue-700 transition-colors disabled:bg-gray-300 disabled:cursor-not-allowed font-semibold flex items-center justify-center gap-2">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/></svg>
            Buat Backup File Sekarang
        </button>

        {{-- Progress --}}
        <div id="file-backup-progress" class="hidden bg-white rounded-lg border border-gray-200 p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Progress Backup File</h3>
            <div class="space-y-4">
                <div>
                    <div class="flex justify-between mb-2">
                        <span id="file-progress-label" class="text-sm text-gray-600">Memulai...</span>
                        <span id="file-progress-pct" class="text-sm font-semibold text-gray-900">0%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div id="file-progress-bar" class="bg-blue-600 h-3 rounded-full transition-all duration-500" style="width:0%"></div>
                    </div>
                </div>
                <div id="file-backup-result" class="hidden bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center gap-3 mb-3">
                        <svg class="w-5 h-5 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
                        <p class="font-semibold text-green-900">Backup file berhasil!</p>
                    </div>
                    <p id="file-backup-detail" class="text-sm text-green-800 mb-3"></p>
                    <a id="file-download-link" href="#"
                       class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                        <span id="file-download-label">Download ZIP</span>
                    </a>
                </div>
                <div id="file-backup-error" class="hidden bg-red-50 border border-red-200 rounded-lg p-4">
                    <p class="text-red-800 font-medium">Backup gagal!</p>
                    <p id="file-error-msg" class="text-red-700 text-sm mt-1"></p>
                </div>
            </div>
        </div>

        {{-- History --}}
        @if(count($backups) > 0)
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Riwayat Backup File</h3>
            <div class="divide-y divide-gray-100">
                @foreach($backups as $backup)
                <div class="flex items-center justify-between py-3">
                    <div>
                        <p class="font-medium text-gray-900 text-sm">{{ $backup['name'] }}</p>
                        <p class="text-xs text-gray-500">{{ $backup['created'] }} &bull; {{ $backup['size'] }}</p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('backup-file.download', $backup['name']) }}"
                           class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors text-sm">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                            Download
                        </a>
                        <form method="POST" action="{{ route('backup-file.delete', $backup['name']) }}" onsubmit="return confirm('Hapus backup ini?')">
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
    function startFileBackup() {
        const btn   = document.getElementById('btn-backup-file');
        const prog  = document.getElementById('file-backup-progress');
        const bar   = document.getElementById('file-progress-bar');
        const label = document.getElementById('file-progress-label');
        const pct   = document.getElementById('file-progress-pct');
        const res   = document.getElementById('file-backup-result');
        const err   = document.getElementById('file-backup-error');

        btn.disabled = true;
        btn.classList.add('opacity-60', 'cursor-not-allowed');
        prog.classList.remove('hidden');
        res.classList.add('hidden');
        err.classList.add('hidden');

        const steps = ['Memindai folder OJS...', 'Mengompres file jurnal...', 'Mengompres file cover...', 'Mengarsip submission...', 'Menyelesaikan ZIP...'];
        let fakeProgress = 0;
        let stepIdx = 0;
        label.textContent = steps[0];

        const interval = setInterval(() => {
            fakeProgress += Math.random() * 4;
            if (fakeProgress >= 85) fakeProgress = 85;
            bar.style.width = fakeProgress + '%';
            pct.textContent = Math.round(fakeProgress) + '%';
            if (fakeProgress > 20 && stepIdx < 1) { label.textContent = steps[1]; stepIdx = 1; }
            if (fakeProgress > 40 && stepIdx < 2) { label.textContent = steps[2]; stepIdx = 2; }
            if (fakeProgress > 60 && stepIdx < 3) { label.textContent = steps[3]; stepIdx = 3; }
            if (fakeProgress > 75 && stepIdx < 4) { label.textContent = steps[4]; stepIdx = 4; }
        }, 300);

        fetch('{{ route('backup-file.run') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({})
        })
        .then(r => r.json())
        .then(data => {
            clearInterval(interval);
            bar.style.width = '100%';
            pct.textContent = '100%';
            label.textContent = 'Selesai';

            if (data.success) {
                res.classList.remove('hidden');
                document.getElementById('file-backup-detail').textContent =
                    data.total_files + ' file dikompres menjadi ' + data.file_size;
                const dlLink = document.getElementById('file-download-link');
                dlLink.href = '/backup-file/download/' + data.file_name;
                document.getElementById('file-download-label').textContent = 'Download ' + data.file_name;
            } else {
                err.classList.remove('hidden');
                document.getElementById('file-error-msg').textContent = data.message;
            }
            btn.disabled = false;
            btn.classList.remove('opacity-60', 'cursor-not-allowed');
        })
        .catch(e => {
            clearInterval(interval);
            err.classList.remove('hidden');
            document.getElementById('file-error-msg').textContent = e.message;
            btn.disabled = false;
            btn.classList.remove('opacity-60', 'cursor-not-allowed');
        });
    }
    </script>
</x-app-layout>
