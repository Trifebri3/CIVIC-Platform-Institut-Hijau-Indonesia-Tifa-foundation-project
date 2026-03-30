<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- Update Title khusus Admin Program --}}
    <title>{{ $title ?? 'Admin Program' }} | CIVIC Platform</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        :root {
            --primary-dark-red: #800000; /* Maroon khas CIVIC */
            --accent-gold: #b8860b; /* Tambahan aksen emas tipis untuk kesan luxury */
            --bg-light: #fdfdfd;
            --sidebar-width: 280px;
        }

        body {
            background-color: var(--bg-light);
            overflow-x: hidden;
            color: #1a1a1a;
        }

        .sidebar-wrapper {
            width: var(--sidebar-width);
            background: #ffffff;
            border-right: 1px solid #f1f1f1;
            position: fixed;
            height: 100vh;
            z-index: 50;
            box-shadow: 10px 0 30px rgba(0,0,0,0.02);
        }

        .brand-logo-container {
            padding: 2.5rem 1.5rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            border-bottom: 1px solid #fafafa;
        }

        .brand-logo {
            max-height: 45px;
            width: auto;
            object-fit: contain;
            margin-bottom: 1rem;
        }

        /* Label Khusus Role di Sidebar */
        .role-badge {
            font-size: 9px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: var(--primary-dark-red);
            background: rgba(128, 0, 0, 0.05);
            padding: 4px 12px;
            border-radius: 50px;
            border: 1px solid rgba(128, 0, 0, 0.1);
        }

        .main-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
        }

        header {
            border-bottom: 1px solid #f3f4f6;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            position: sticky;
            top: 0;
            z-index: 40;
        }

        /* Aksen garis merah tipis di atas header */
        .header-top-line {
            height: 3px;
            background: var(--primary-dark-red);
            width: 100%;
        }

        .luxury-text {
            color: var(--primary-dark-red);
            font-weight: 700;
            letter-spacing: -0.01em;
        }

        footer {
            background: transparent;
            padding: 3rem 0;
        }

        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-thumb {
            background: #e5e7eb;
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb:hover { background: var(--primary-dark-red); }

        @media (max-width: 1024px) {
            .sidebar-wrapper { transform: translateX(-100%); }
            .main-wrapper { margin-left: 0; }
        }
    </style>
</head>
<body class="font-sans antialiased">

    <aside class="sidebar-wrapper">
        <div class="brand-logo-container">
            <img src="{{ asset('images/logo.png') }}" alt="CIVIC Logo" class="brand-logo">
            {{-- Badge Penanda Role --}}
            <span class="role-badge">Program Administrator</span>
        </div>

        {{-- Pastikan mengarah ke folder admin-program --}}
        @include('pages.admin-program.layouts.sidebar')
    </aside>

    <div class="main-wrapper">
        <div class="header-top-line"></div>

        {{-- Header khusus admin program --}}
        @include('pages.admin-program.layouts.header')

        <main class="flex-1 p-6 lg:p-12">
            {{-- Tambahkan animasi fade-in halus --}}
            <div class="max-w-7xl mx-auto animate-in fade-in duration-700">
                {{ $slot }}
            </div>
        </main>

        <footer class="text-center">
            <p class="text-[10px] text-gray-400 uppercase tracking-[0.3em]">
                &copy; {{ date('Y') }} <span class="luxury-text">CIVIC Platform</span>
                <span class="mx-2">•</span>
                <span class="italic opacity-60">Management Console v1.0</span>
            </p>
        </footer>
    </div>

    @livewireScripts
</body>
</html>
