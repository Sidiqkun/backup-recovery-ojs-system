<x-app-layout>
    <x-slot name="header">Full Restore Sistem OJS</x-slot>

    <div class="space-y-6 max-w-4xl">

        <div>
            <h2 class="text-xl font-semibold text-gray-900 mb-2">Full Restore (Database + Files)</h2>
            <p class="text-sm text-gray-500">Memulihkan sistem secara keseluruhan dari 1 buah file ZIP ekspor Full Backup.</p>
        </div>

        {{-- Warning --}}
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex gap-3">
                <svg class="w-6 h-6 text-red-600 flex-shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>
                <div>
                    <p class="font-semibold text-red-900 mb-1">Peringatan Sangat Keras!</p>
                    <p class="text-sm text-red-800">Proses ini akan me-replace (menimpa) database yang sedang berjalan, dan seluruh file dalam folder <code>ojs3/files/</code>. Tindakan ini tidak bisa dibatalkan!</p>
                </div>
            </div>
        </div>

        {{-- Upload ZIP --}}
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Upload File "Full Backup (ZIP)"</h3>
            <div id="drop-zone" ondragover="event.preventDefault(); this.classList.add('border-red-400','bg-red-50')"
                 ondragleave="this.classList.remove('border-red-400','bg-red-50')"
                 ondrop="handleDrop(event)"
                 class="border-2 border-dashed border-gray-300 rounded-lg p-12 text-center hover:border-red-400 transition-colors cursor-pointer"
                 onclick="document.getElementById('file-input').click()">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" x2="12" y1="3" y2="15"/></svg>
                <p class="text-gray-700 mb-2">Drag & drop file backup <strong class="text-red-600">.ZIP</strong> atau <span class="text-blue-600 font-medium">pilih file</span></p>
                <p class="text-sm text-gray-500">Maksimal 1GB</p>
                <input id="file-input" type="file" accept=".zip" class="hidden" onchange="onFileSelect(this)">
                <div id="file-selected" class="hidden mt-4 inline-flex items-center gap-2 bg-blue-50 px-4 py-2 rounded-lg">
                    <svg class="w-4 h-4 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
                    <span id="file-name" class="text-sm text-blue-900 font-medium"></span>
                </div>
            </div>
        </div>

        {{-- Action Button --}}
        <button id="btn-full-restore" onclick="startFullRestore()" disabled
            class="w-full bg-red-600 text-white py-4 rounded-lg hover:bg-red-700 transition-colors disabled:bg-gray-300 disabled:cursor-not-allowed font-semibold flex items-center justify-center gap-2">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
            Jalankan Full Restore Sekarang
        </button>

        {{-- Status Log --}}
        <div id="fr-log-section" class="hidden bg-white rounded-lg border border-gray-200 p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Konsol Restore</h3>
            <div id="fr-log" class="bg-gray-900 rounded-lg p-4 font-mono text-sm text-gray-300 space-y-1 min-h-[150px] max-h-[300px] overflow-y-auto"></div>
            
            <div id="fr-success" class="hidden mt-4 bg-green-50 border border-green-200 rounded-lg p-4">
                <p class="font-bold text-green-900 text-lg">Berhasil!</p>
                <p id="fr-success-msg" class="text-green-800 text-sm"></p>
            </div>
            <div id="fr-error" class="hidden mt-4 bg-red-50 border border-red-200 rounded-lg p-4">
                <p class="text-red-800 font-medium">Gagal!</p>
                <p id="fr-error-msg" class="text-red-700 text-sm mt-1"></p>
            </div>
        </div>
    </div>

    <script>
    let currentFile = null;
    function onFileSelect(input) {
        if(input.files && input.files[0]) { currentFile = input.files[0]; updateUI(); }
    }
    function handleDrop(e) {
        e.preventDefault();
        document.getElementById('drop-zone').classList.remove('border-red-400','bg-red-50');
        if(e.dataTransfer.files[0]) { currentFile = e.dataTransfer.files[0]; updateUI(); }
    }
    function updateUI() {
        document.getElementById('file-name').textContent = currentFile.name;
        document.getElementById('file-selected').classList.remove('hidden');
        document.getElementById('btn-full-restore').disabled = false;
    }
    function addLog(text, flag='info') {
        const d = document.createElement('div');
        d.className = flag==='ok' ? 'text-green-400' : flag==='err' ? 'text-red-500' : 'text-blue-300';
        d.textContent = text;
        const box = document.getElementById('fr-log');
        box.appendChild(d);
        box.scrollTop = box.scrollHeight;
    }

    function startFullRestore() {
        if (!currentFile || !confirm('Yakin akan menimpa seluruh sistem OJS saat ini?')) return;
        
        const btn = document.getElementById('btn-full-restore');
        const logBox = document.getElementById('fr-log-section');
        const box = document.getElementById('fr-log');
        const s = document.getElementById('fr-success');
        const e = document.getElementById('fr-error');

        btn.disabled = true; btn.classList.add('opacity-60');
        box.innerHTML=''; logBox.classList.remove('hidden'); s.classList.add('hidden'); e.classList.add('hidden');

        addLog('=> Uploading ' + currentFile.name + '...', 'info');

        const fd = new FormData();
        fd.append('backup_file', currentFile);
        fd.append('_token', '{{ csrf_token() }}');

        // Fake steps animation
        const steps = ['Mengekstrak SQL & File...', 'Menghubungkan Database...', 'Menimpa struktur tabel...', 'Mengembalikan physical files...'];
        let idx = 0;
        const timer = setInterval(() => {
            if(idx < steps.length) { addLog('=> ' + steps[idx], 'info'); idx++; } else clearInterval(timer);
        }, 1000);

        fetch('{{ route('full-restore.run') }}', { method:'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            clearInterval(timer);
            if(data.success) {
                addLog('=> ✅ ' + data.message, 'ok');
                document.getElementById('fr-success-msg').textContent = data.message;
                s.classList.remove('hidden');
            } else {
                addLog('=> ❌ Error: ' + data.message, 'err');
                document.getElementById('fr-error-msg').textContent = data.message;
                e.classList.remove('hidden');
            }
            btn.disabled = false; btn.classList.remove('opacity-60');
        }).catch(err => {
            clearInterval(timer);
            addLog('=> ❌ Koneksi terputus: ' + err.message, 'err');
            document.getElementById('fr-error-msg').textContent = err.message;
            e.classList.remove('hidden');
            btn.disabled = false; btn.classList.remove('opacity-60');
        });
    }
    </script>
</x-app-layout>
