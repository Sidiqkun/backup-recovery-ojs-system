<x-app-layout>
    <x-slot name="header">Restore Sistem</x-slot>

    <div class="space-y-6 max-w-4xl">

        <div>
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Restore Sistem OJS</h2>
        </div>

        {{-- Warning --}}
        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
            <div class="flex gap-3">
                <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>
                <div>
                    <p class="font-semibold text-amber-900 mb-1">Peringatan!</p>
                    <p class="text-sm text-amber-800">Restore akan mengganti data OJS saat ini. Pastikan Anda telah membuat backup terlebih dahulu sebelum melakukan restore.</p>
                </div>
            </div>
        </div>

        {{-- Upload File --}}
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Upload File Backup SQL</h3>

            <div id="drop-zone"
                 ondragover="event.preventDefault(); this.classList.add('border-blue-400','bg-blue-50')"
                 ondragleave="this.classList.remove('border-blue-400','bg-blue-50')"
                 ondrop="handleDrop(event)"
                 class="border-2 border-dashed border-gray-300 rounded-lg p-12 text-center hover:border-blue-400 transition-colors cursor-pointer"
                 onclick="document.getElementById('file-input').click()">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" x2="12" y1="3" y2="15"/></svg>
                <p class="text-gray-700 mb-2">Drag & drop file backup atau <span class="text-blue-600 font-medium">pilih file</span></p>
                <p class="text-sm text-gray-500">Format: .sql (Maksimal 512MB)</p>
                <input id="file-input" type="file" accept=".sql,.txt" class="hidden" onchange="onFileSelect(this)">
                <div id="file-selected" class="hidden mt-4 inline-flex items-center gap-2 bg-blue-50 px-4 py-2 rounded-lg">
                    <svg class="w-4 h-4 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="m9 12 2 2 4-4"></path></svg>
                    <span id="file-name" class="text-sm text-blue-900 font-medium"></span>
                </div>
            </div>
        </div>

        {{-- Opsi Restore --}}
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Opsi Restore</h3>
            <div class="space-y-4">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" id="opt-db" checked class="w-5 h-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                    <span class="font-medium text-gray-900">Restore Database</span>
                </label>
            </div>
        </div>

        {{-- Action Button --}}
        <button id="btn-restore" onclick="startRestore()" disabled
            class="w-full bg-blue-600 text-white py-4 rounded-lg hover:bg-blue-700 transition-colors disabled:bg-gray-300 disabled:cursor-not-allowed font-semibold flex items-center justify-center gap-2">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
            Mulai Restore
        </button>

        {{-- Log / Status --}}
        <div id="restore-log-section" class="hidden bg-white rounded-lg border border-gray-200 p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Status Restore</h3>
            <div id="restore-log" class="bg-gray-900 rounded-lg p-4 font-mono text-sm text-gray-300 space-y-1 min-h-24"></div>

            <div id="restore-success" class="hidden mt-4 bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="m9 12 2 2 4-4"></path></svg>
                    <p class="font-semibold text-green-900">Restore selesai! Sistem OJS telah dikembalikan.</p>
                </div>
            </div>

            <div id="restore-error" class="hidden mt-4 bg-red-50 border border-red-200 rounded-lg p-4">
                <p class="text-red-800 font-medium">Restore gagal!</p>
                <p id="restore-error-msg" class="text-red-700 text-sm mt-1"></p>
            </div>
        </div>

    </div>

    <script>
    let selectedFile = null;

    function onFileSelect(input) {
        if (input.files && input.files[0]) {
            selectedFile = input.files[0];
            updateFileUI(selectedFile.name);
        }
    }

    function handleDrop(event) {
        event.preventDefault();
        document.getElementById('drop-zone').classList.remove('border-blue-400', 'bg-blue-50');
        const file = event.dataTransfer.files[0];
        if (file) {
            selectedFile = file;
            updateFileUI(file.name);
        }
    }

    function updateFileUI(name) {
        document.getElementById('file-name').textContent = name;
        document.getElementById('file-selected').classList.remove('hidden');
        document.getElementById('btn-restore').disabled = false;
    }

    function appendLog(text, type = 'normal') {
        const log = document.getElementById('restore-log');
        const div = document.createElement('div');
        div.className = type === 'success' ? 'text-green-400' : type === 'error' ? 'text-red-400' : 'text-gray-300';
        div.textContent = (type === 'success' ? '✓ ' : '') + text;
        log.appendChild(div);
        log.scrollTop = log.scrollHeight;
    }

    function startRestore() {
        if (!selectedFile) return;

        const btn = document.getElementById('btn-restore');
        const logSection = document.getElementById('restore-log-section');
        const logDiv = document.getElementById('restore-log');
        const successDiv = document.getElementById('restore-success');
        const errorDiv = document.getElementById('restore-error');

        btn.disabled = true;
        btn.classList.add('opacity-60');
        logSection.classList.remove('hidden');
        logDiv.innerHTML = '';
        successDiv.classList.add('hidden');
        errorDiv.classList.add('hidden');

        const steps = [
            'Memvalidasi file backup...',
            'Ekstraksi file backup...',
            'Memproses database...',
            'Mengimport tabel users...',
            'Mengimport tabel submissions...',
            'Mengimport tabel journals...',
            'Memverifikasi integritas data...',
        ];

        let i = 0;
        const fakeLogs = setInterval(() => {
            if (i < steps.length) {
                appendLog(steps[i]);
                i++;
            } else {
                clearInterval(fakeLogs);
            }
        }, 400);

        const formData = new FormData();
        formData.append('backup_file', selectedFile);
        formData.append('_token', '{{ csrf_token() }}');

        fetch('{{ route('restore.run') }}', {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            clearInterval(fakeLogs);
            if (data.success) {
                appendLog('Restore berhasil!', 'success');
                successDiv.classList.remove('hidden');
            } else {
                appendLog('Error: ' + data.message, 'error');
                errorDiv.classList.remove('hidden');
                document.getElementById('restore-error-msg').textContent = data.message;
            }
            btn.disabled = false;
            btn.classList.remove('opacity-60');
        })
        .catch(err => {
            clearInterval(fakeLogs);
            appendLog('Koneksi error: ' + err.message, 'error');
            errorDiv.classList.remove('hidden');
            document.getElementById('restore-error-msg').textContent = err.message;
            btn.disabled = false;
            btn.classList.remove('opacity-60');
        });
    }
    </script>
</x-app-layout>
