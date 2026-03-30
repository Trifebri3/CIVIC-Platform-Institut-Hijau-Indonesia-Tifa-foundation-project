<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <title>{{ $title ?? 'CIVIC Platform' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        :root {
            --primary-red: #800000;
            --bg-neutral: #ffffff;
        }

        html { scroll-behavior: smooth; -webkit-tap-highlight-color: transparent; }

        body {
            background-color: var(--bg-neutral);
            /* Memastikan font terasa seperti aplikasi native */
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .glass-header {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        /* Transition untuk konten saat scroll */
        .fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="font-sans text-gray-900 leading-relaxed">

    <header class="fixed top-0 z-50 w-full h-16 lg:h-20 border-b border-gray-50 glass-header flex items-center justify-between px-6 lg:px-12">
        <div class="flex items-center">
            <img src="{{ asset('images/logo.png') }}" alt="CIVIC Logo" class="h-8 lg:h-10 w-auto object-contain">
        </div>

        <div class="flex items-center gap-6">
            @if (Route::has('login'))
                <a href="{{ route('login') }}" class="text-[11px] font-bold uppercase tracking-[0.2em] text-gray-500 hover:text-[#800000] transition-colors">
                    Masuk
                </a>
            @endif
            @if (Route::has('register'))
                <a href="{{ route('register') }}" class="bg-[#800000] text-white text-[10px] font-bold uppercase tracking-[0.15em] px-5 py-2.5 rounded-full shadow-sm hover:bg-black transition-all">
                    Daftar
                </a>
            @endif
        </div>
    </header>

    <main class="pt-20 lg:pt-28 pb-12 px-6 lg:px-12 fade-in">
        <div class="max-w-4xl mx-auto">
            {{ $slot }}
        </div>
    </main>

    <footer class="py-12 border-t border-gray-50">
        <div class="max-w-4xl mx-auto text-center px-6">
            <p class="text-[9px] uppercase tracking-[0.4em] text-gray-400 font-bold mb-2">
                Institut Hijau Indonesia
            </p>
            <p class="text-[10px] text-gray-300">
                &copy; {{ date('Y') }} CIVIC Education Platform. All rights reserved.
            </p>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
