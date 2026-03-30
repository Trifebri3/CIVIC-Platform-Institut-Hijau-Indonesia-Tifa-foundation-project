<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- Title Khusus Admin Surat --}}
    <title>{{ $title ?? 'Admin Surat' }} | CIVIC Platform</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        :root {
            --primary-maroon: #800000;
            --deep-slate: #1e293b; /* Warna aksen profesional untuk administrasi */
            --soft-ivory: #fafafa;
            --sidebar-width: 300px; /* Sedikit lebih lebar untuk daftar menu surat yang biasanya panjang */
        }

        body {
            background-color: #fcfcfc;
            color: #1a1a1a;
            overflow-x: hidden;
        }

        /* Sidebar Glassmorphism Style */
        .sidebar-wrapper {
            width: var(--sidebar-width);
            background: #ffffff;
            border-right: 1px solid rgba(0,0,0,0.04);
            position: fixed;
            height: 100vh;
            z-index: 50;
            transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .brand-section {
            padding: 3rem 2rem;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .brand-logo {
            max-height: 40px;
            margin-bottom: 1.5rem;
            filter: grayscale(0.2);
        }

        /* Role Badge dengan gaya "Document Stamp" */
        .role-badge {
            font-size: 8px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.25em;
            color: #ffffff;
            background: var(--deep-slate);
            padding: 6px 16px;
            border-radius: 4px;
            box-shadow: 4px 4px 0px rgba(128, 0, 0, 0.1);
        }

        .main-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
        }

        /* Header Modern & Minimalis */
        header {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid #f1f1f1;
            position: sticky;
            top: 0;
            z-index: 40;
        }

        .header-accent {
            height: 4px;
            background: linear-gradient(90deg, var(--primary-maroon), var(--deep-slate));
            width: 100%;
        }

        .content-container {
            padding: 2.5rem;
        }

        .footer-branding {
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.4em;
            color: #cbd5e1;
            padding: 4rem 0;
        }

        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--primary-maroon); }

        @media (max-width: 1024px) {
            .sidebar-wrapper { transform: translateX(-100%); }
            .main-wrapper { margin-left: 0; }
        }
    </style>
</head>
<body class="font-sans antialiased">

    <aside class="sidebar-wrapper">
        <div class="brand-section">
            <img src="{{ asset('images/logo.png') }}" alt="CIVIC Logo" class="brand-logo">
            <div class="mt-2">
                <span class="role-badge italic">Registry & Correspondence</span>
            </div>
            <p class="text-[9px] font-bold text-gray-300 uppercase tracking-widest mt-4 italic">Admin Surat System</p>
        </div>

        {{-- Folder: pages/admin-surat/layouts/sidebar.blade.php --}}
        <div class="px-4">
            @include('pages.admin-surat.layouts.sidebar')
        </div>
    </aside>

    <div class="main-wrapper">
        <div class="header-accent"></div>

        {{-- Folder: pages/admin-surat/layouts/header.blade.php --}}
        @include('pages.admin-surat.layouts.header')

        <main class="flex-1 content-container lg:px-16">
            {{-- Fade-in Animation --}}
            <div class="animate-in fade-in slide-in-from-bottom-4 duration-1000 ease-out">
                {{ $slot }}
            </div>
        </main>

        <footer class="text-center footer-branding">
            &copy; {{ date('Y') }} <span class="text-slate-400">CIVIC Correspondence</span>
            <span class="mx-3 text-red-800 opacity-30">/</span>
            Internal Archive System v2.0
        </footer>
    </div>

    @livewireScripts
</body>
</html>
