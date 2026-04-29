<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>OJS Backup & Restore System</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-50">

        <!-- Sidebar Overlay (mobile) -->
        <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden hidden" onclick="toggleSidebar()"></div>

        <!-- Sidebar -->
        <aside id="sidebar" class="w-64 bg-white border-r border-gray-200 h-screen fixed left-0 top-0 z-50 transition-transform duration-300 -translate-x-full lg:translate-x-0">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="12" cy="5" rx="9" ry="3"></ellipse><path d="M3 5v14c0 1.66 4.03 3 9 3s9-1.34 9-3V5"></path><path d="M3 12c0 1.66 4.03 3 9 3s9-1.34 9-3"></path></svg>
                        </div>
                        <div>
                            <h2 class="font-semibold text-gray-900">OJS Backup</h2>
                            <p class="text-xs text-gray-500">Management System</p>
                        </div>
                    </div>
                    <button onclick="toggleSidebar()" class="lg:hidden text-gray-500 hover:text-gray-700">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                    </button>
                </div>
            </div>

            <nav class="p-4">
                <ul class="space-y-1">

                    {{-- Dashboard --}}
                    <li>
                        <a href="{{ route('dashboard') }}" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50' }}">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg>
                            <span class="font-medium">Dashboard</span>
                        </a>
                    </li>

                    {{-- Divider Label --}}
                    <li class="pt-3 pb-1 px-4">
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-widest">Backup</span>
                    </li>

                    {{-- Full Backup (Utama / Direkomendasikan) --}}
                    <li>
                        <a href="{{ route('full-backup') }}" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('full-backup*') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50' }}">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                            <span class="font-medium">Full Backup</span>
                        </a>
                    </li>

                    {{-- Backup Database --}}
                    <li>
                        <a href="{{ route('backup') }}" class="w-full flex items-center gap-3 pl-11 pr-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('backup') ? 'bg-blue-50 text-blue-600' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700' }}">
                            <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="12" cy="5" rx="9" ry="3"></ellipse><path d="M3 5v14c0 1.66 4.03 3 9 3s9-1.34 9-3V5"></path><path d="M3 12c0 1.66 4.03 3 9 3s9-1.34 9-3"></path></svg>
                            <span class="text-sm font-medium">Backup Database</span>
                        </a>
                    </li>

                    {{-- Backup File --}}
                    <li>
                        <a href="{{ route('backup-file') }}" class="w-full flex items-center gap-3 pl-11 pr-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('backup-file*') ? 'bg-blue-50 text-blue-600' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700' }}">
                            <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/></svg>
                            <span class="text-sm font-medium">Backup File OJS</span>
                        </a>
                    </li>

                    {{-- Divider Label --}}
                    <li class="pt-3 pb-1 px-4">
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-widest">Restore</span>
                    </li>

                    {{-- Full Restore (Utama) --}}
                    <li>
                        <a href="{{ route('full-restore') }}" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('full-restore*') ? 'bg-orange-50 text-orange-600' : 'text-gray-700 hover:bg-gray-50' }}">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" x2="12" y1="3" y2="15"/></svg>
                            <span class="font-medium">Full Restore</span>
                        </a>
                    </li>

                    {{-- Restore Database --}}
                    <li>
                        <a href="{{ route('restore') }}" class="w-full flex items-center gap-3 pl-11 pr-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('restore') ? 'bg-blue-50 text-blue-600' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700' }}">
                            <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
                            <span class="text-sm font-medium">Restore Database</span>
                        </a>
                    </li>

                    {{-- Restore File --}}
                    <li>
                        <a href="{{ route('restore-file') }}" class="w-full flex items-center gap-3 pl-11 pr-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('restore-file*') ? 'bg-blue-50 text-blue-600' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700' }}">
                            <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/><path d="M15 2H6a2 2 0 0 0-2 2v5"/></svg>
                            <span class="text-sm font-medium">Restore File OJS</span>
                        </a>
                    </li>

                    {{-- Divider Label --}}
                    <li class="pt-3 pb-1 px-4">
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-widest">Data OJS</span>
                    </li>

                    {{-- Data Jurnal --}}
                    <li>
                        <a href="{{ route('journal') }}" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('journal*') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50' }}">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>
                            <span class="font-medium">Data Jurnal</span>
                        </a>
                    </li>

                </ul>
            </nav>
        </aside>

        <!-- Main Content Area -->
        <div class="lg:ml-64 flex flex-col min-h-screen">

            <!-- Header -->
            <header class="bg-white border-b border-gray-200 px-4 lg:px-8 py-4 sticky top-0 z-30">
                <div class="flex items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <button onclick="toggleSidebar()" class="lg:hidden text-gray-700 hover:text-gray-900">
                            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/></svg>
                        </button>
                        <h1 class="text-lg lg:text-2xl font-bold text-gray-900">
                            {{ $header ?? 'Dashboard Backup & Restore OJS' }}
                        </h1>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="hidden sm:flex items-center gap-2 px-4 py-2 text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>
                            <span class="hidden md:inline">Logout</span>
                            <span class="md:hidden">Keluar</span>
                        </button>
                    </form>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 p-6 lg:p-8">
                {{ $slot }}
            </main>
        </div>

        <script>
            function toggleSidebar() {
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('sidebar-overlay');
                sidebar.classList.toggle('-translate-x-full');
                overlay.classList.toggle('hidden');
            }
        </script>
    </body>
</html>
