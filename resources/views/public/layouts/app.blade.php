<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Welcome' }} | CIVIC Platform</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        :root {
            --primary-maroon: #800000;
            --luxury-gold: #b8860b;
            --soft-bg: #f8f9fa;
        }

        body {
            background-color: var(--soft-bg);
            color: #1a1a1a;
            scroll-behavior: smooth;
        }

        .glass-nav {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        /* Custom Scrollbar agar tetap minimalis */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--primary-maroon); }
    </style>
</head>
<body class="font-sans antialiased">

    {{-- Kita panggil Header --}}
    @include('public.layouts.header')

    <main>
        {{ $slot }}
    </main>

    {{-- Kita panggil Footer --}}
    @include('public.layouts.footer')

    @livewireScripts
</body>
</html>
