<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <title>{{ $title ?? 'CIVIC' }} | User Platform</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        :root {
            --primary-red: #800000;
            --sidebar-width: 280px;
        }

        /* Prevent overscroll bounce on mobile for app feel */
        html, body {
            overscroll-behavior-y: none;
            scroll-behavior: smooth;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            background-color: #fcfcfc;
            /* Padding bottom agar konten tidak tertutup bottom nav di mobile */
            padding-bottom: 4.5rem;
        }

        @media (min-width: 1024px) {
            body { padding-bottom: 0; }
            .main-content { margin-left: var(--sidebar-width); }
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
        }

        .active-nav {
            color: var(--primary-red) !important;
            font-weight: 800;
        }

        /* Custom scrollbar for desktop luxury feel */
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--primary-red); }
    </style>
</head>
<body class="font-sans antialiased text-gray-900 selection:bg-red-100 selection:text-red-900">

    {{-- Sidebar khusus User --}}
    @include('pages.user.layouts.sidebar')

    <div class="main-content min-h-screen flex flex-col transition-all duration-300">

        {{-- Header Sticky --}}
        <header class="sticky top-0 z-40 w-full border-b border-gray-100 glass-effect">
            @include('pages.user.layouts.header')
        </header>

        {{-- Content Area --}}
        <main class="flex-1 p-5 lg:p-12">
            <div class="max-w-5xl mx-auto">
                {{ $slot }}
            </div>
        </main>

        {{-- Footer Desktop --}}
        <footer class="hidden lg:block py-10 border-t border-gray-50 text-center">
            <p class="text-[10px] uppercase tracking-[0.4em] text-gray-400 font-bold">
                &copy; {{ date('Y') }} CIVIC Platform — Institut Hijau Indonesia
            </p>
        </footer>
    </div>

    {{-- Bottom Navigation Mobile (App Style) --}}
    <div class="lg:hidden fixed bottom-0 left-0 z-50 w-full h-18 bg-white/90 border-t border-gray-100 flex items-center justify-around px-4 pb-safe glass-effect shadow-[0_-5px_20px_rgba(0,0,0,0.05)]">

        <a href="{{ route('user.dashboard') }}" class="flex flex-col items-center justify-center w-full py-2 transition-all {{ Request::routeIs('user.dashboard') ? 'active-nav scale-110' : 'text-gray-400' }}">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            <span class="text-[9px] mt-1 uppercase tracking-wider">Beranda</span>
        </a>

        <a href="{{ route('user.settings') }}" class="flex flex-col items-center justify-center w-full py-2 transition-all {{ Request::routeIs('user.profile') ? 'active-nav scale-110' : 'text-gray-400' }}">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            <span class="text-[9px] mt-1 uppercase tracking-wider">Profil</span>
        </a>

        <button onclick="toggleSidebar()" class="flex flex-col items-center justify-center w-full py-2 text-gray-400 active:text-[#800000] active:scale-90 transition-all">
            <div class="bg-gray-50 p-2 rounded-xl border border-gray-100">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                </svg>
            </div>
            <span class="text-[9px] mt-1 uppercase tracking-wider">Menu</span>
        </button>
    </div>

    @livewireScripts
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('mobile-sidebar');
            if(sidebar) {
                sidebar.classList.toggle('-translate-x-full');
                // Optional: add body overlay if sidebar is open
            }
        }

        public function markAllAsRead()
{
    auth()->user()->unreadNotifications->markAsRead();
    // Opsional: kirim browser event buat ngasih tau user
    $this->dispatch('notify', message: 'Semua notifikasi dibaca');
}
    </script>
</body>
</html>
