<x-guest-layout>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center px-4">
      <div class="max-w-md w-full">
        <div class="bg-white rounded-2xl shadow-xl p-8">
          <!-- Back Button -->
          <a href="{{ route('login') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 mb-6 transition-colors">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
            <span>Kembali</span>
          </a>

          <!-- Header -->
          <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-green-600 rounded-xl mb-4">
              <svg class="w-8 h-8 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">
              Verifikasi Email
            </h1>
            <p class="text-gray-600">
              Kode verifikasi telah dikirim ke
            </p>
            <p class="font-semibold text-gray-900 mt-1">{{ $email }}</p>
          </div>

          <!-- Verification Form -->
          <form method="POST" action="{{ route('otp.verify') }}" class="space-y-6">
            @csrf
            
            <div class="space-y-2">
              <label for="otp" class="text-center block text-sm font-medium text-gray-700">
                Masukkan Kode Verifikasi
              </label>

              @error('otp')
                <div class="text-sm text-red-600 text-center mb-4">
                  {{ $message }}
                </div>
              @enderror
              
              <div class="flex justify-center mt-4">
                <input 
                    type="text" 
                    name="otp" 
                    id="otp" 
                    maxlength="6" 
                    autocomplete="one-time-code"
                    autofocus
                    class="block text-center text-2xl tracking-widest font-mono font-bold w-full max-w-[240px] border-b-2 border-gray-300 focus:border-green-600 focus:ring-0 px-2 py-3 bg-transparent placeholder-gray-300 outline-none transition-colors"
                    placeholder="------"
                    pattern="\d{6}"
                    title="Masukkan 6 digit angka"
                    required
                />
              </div>
            </div>

            <button
              type="submit"
              class="w-full flex justify-center items-center bg-green-600 hover:bg-green-700 text-white font-medium h-10 px-4 py-2 rounded-md transition-colors"
            >
              <svg class="mr-2 w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="m9 12 2 2 4-4"></path></svg>
              Verifikasi
            </button>
            
            <div class="text-center mt-4">
              <p class="text-sm text-gray-600">
                Belum menerima kode? <a href="{{ route('login') }}" class="text-blue-600 font-medium hover:underline">Kirim Ulang</a>
              </p>
            </div>
          </form>

          <!-- Info -->
          <div class="mt-6 p-4 bg-green-50 rounded-lg">
            <p class="text-sm text-green-800">
              <span class="font-semibold">Bantuan:</span> Jika Anda di mode pengembangan lokal, perhatikan log <code class="font-mono text-sm">storage/logs/laravel.log</code> untuk melihat kode OTP 6-digit.
            </p>
          </div>
        </div>

        <!-- Footer -->
        <p class="text-center text-sm text-gray-600 mt-6">
          © 2026 OJS Backup & Restore System
        </p>
      </div>
    </div>
</x-guest-layout>
