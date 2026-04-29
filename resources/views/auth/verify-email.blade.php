<x-guest-layout>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center px-4">
      <div class="max-w-md w-full my-8">
        <div class="bg-white rounded-2xl shadow-xl p-8">
          <!-- Back Button (Logout) -->
          <form method="POST" action="{{ route('logout') }}" class="mb-6">
            @csrf
            <button type="submit" class="flex items-center gap-2 text-gray-600 hover:text-gray-900 transition-colors">
              <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
              <span>Logout / Kembali</span>
            </button>
          </form>

          <!-- Header -->
          <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-600 rounded-xl mb-4 shadow-lg">
              <svg class="w-8 h-8 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">
              Verifikasi Email
            </h1>
            <p class="text-gray-600 text-sm">
              Tautan verifikasi telah dikirim ke alamat email Anda. Silakan periksa inbox atau spam.
            </p>
          </div>

          @if (session('status') == 'verification-link-sent')
              <div class="mb-6 text-sm font-medium text-green-600 text-center">
                  Tautan verifikasi email yang baru telah dikirimkan ke alamat email yang Anda berikan saat registrasi.
              </div>
          @endif

          <!-- Verification Form -->
          <div class="space-y-6">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button
                type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white flex h-10 items-center justify-center rounded-md px-4 py-2 text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 disabled:pointer-events-none disabled:opacity-50"
                >
                <svg class="mr-2 w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                Kirim Ulang Email Verifikasi
                </button>
            </form>
          </div>

          <!-- Info -->
          <div class="mt-6 p-4 bg-blue-50 rounded-lg">
            <p class="text-sm text-blue-800">
              <span class="font-semibold">Info:</span> Jika Anda menggunakan local environment (Laragon), periksa log lokal untuk melihat tautannya.
            </p>
          </div>
        </div>

        <!-- Footer -->
        <p class="text-center text-sm text-gray-600 mt-6">
          &copy; 2026 OJS Backup &amp; Restore System
        </p>
      </div>
    </div>
</x-guest-layout>
