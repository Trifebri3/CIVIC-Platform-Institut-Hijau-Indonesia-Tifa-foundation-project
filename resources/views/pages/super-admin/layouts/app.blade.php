<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'SuperAdmin' }} | CIVIC Platform</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        :root {
            --primary-dark-red: #800000; /* Maroon */
            --accent-red: #a52a2a; /* Crimson-ish */
            --bg-light: #f9fafb;
            --sidebar-width: 280px;
        }

        body {
            background-color: var(--bg-light);
            overflow-x: hidden;
        }

        /* Sidebar Container Adjustment */
        .sidebar-wrapper {
            width: var(--sidebar-width);
            background: #ffffff;
            border-right: 1px solid #e5e7eb;
            position: fixed;
            height: 100vh;
            z-index: 50;
        }

        /* Logo Styling */
        .brand-logo-container {
            padding: 2rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 1px solid #f3f4f6;
        }

        .brand-logo {
            max-height: 50px;
            width: auto;
            object-fit: contain;
        }

        /* Main Content Wrapper */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
        }

        /* Header Accent */
        header {
            border-bottom: 2px solid var(--primary-dark-red);
            background: #fff;
        }

        /* Typography & Luxury Accents */
        .luxury-text {
            color: var(--primary-dark-red);
            font-weight: 600;
            letter-spacing: 0.025em;
        }

        footer {
            background: #fff;
            border-top: 1px solid #eeeeee;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }
        ::-webkit-scrollbar-thumb {
            background: var(--primary-dark-red);
            border-radius: 10px;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .sidebar-wrapper { transform: translateX(-100%); }
            .main-wrapper { margin-left: 0; }
        }
    </style>
</head>
<body class="font-sans antialiased">

    <aside class="sidebar-wrapper shadow-sm">
        <div class="brand-logo-container">
            <img src="{{ asset('images/logo.png') }}" alt="CIVIC Logo" class="brand-logo">
        </div>
        @include('pages.super-admin.layouts.sidebar')
    </aside>

    <div class="main-wrapper">
        @include('pages.super-admin.layouts.header')

        <main class="flex-1 p-6 lg:p-10">
            <div class="max-w-7xl mx-auto">
                {{ $slot }}
            </div>
        </main>

        <footer class="py-6 text-center">
            <p class="text-xs text-gray-500 uppercase tracking-widest">
                &copy; {{ date('Y') }} <span class="luxury-text">CIVIC Education Platform</span>.
                <span class="ml-2 italic opacity-75">Institut Hijau Indonesia.</span>
            </p>
        </footer>
    </div>

    @livewireScripts
</body>
</html>
