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
