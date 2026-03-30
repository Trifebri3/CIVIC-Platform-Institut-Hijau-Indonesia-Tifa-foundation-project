<aside class="sidebar-wrapper fixed left-0 top-0 z-40 h-screen w-72 border-r border-gray-100 bg-white transition-transform overflow-y-auto">
    {{-- Logo Section --}}
    <div class="flex h-24 items-center justify-center border-b border-gray-50 px-8">
        <a href="/" class="block">
            <img src="{{ asset('images/logo.png') }}" alt="CIVIC Logo" class="h-9 w-auto object-contain">
        </a>
    </div>

    <nav class="mt-8 px-5 space-y-2 pb-10">
        <p class="mb-5 px-4 text-[9px] font-black uppercase tracking-[0.4em] text-slate-300 italic">
            Registry System
        </p>

        {{-- Dashboard - AKTIF --}}
        <a href="{{ route('adminsurat.dashboard') }}"
           class="group flex items-center px-4 py-4 rounded-2xl transition-all duration-500 {{ request()->routeIs('adminsurat.dashboard') ? 'bg-[#800000] text-white shadow-xl shadow-red-900/20' : 'text-slate-400 hover:bg-gray-50 hover:text-[#800000]' }}">
            <div class="p-2 rounded-xl {{ request()->routeIs('adminsurat.dashboard') ? 'bg-white/10 text-white' : 'bg-gray-50 group-hover:bg-white text-slate-400 group-hover:text-[#800000] transition-colors' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
            </div>
            <span class="ml-4 text-[11px] font-black uppercase tracking-[0.2em] italic">Overview</span>
        </a>

        {{-- Group: Correspondence - NON-AKTIF (#) --}}
        <div class="space-y-1 opacity-30 pointer-events-none grayscale">
            <button class="w-full group flex items-center px-4 py-4 rounded-2xl text-slate-400">
                <div class="p-2 rounded-xl bg-gray-50 text-slate-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
                <span class="ml-4 text-[11px] font-black uppercase tracking-[0.2em] italic">Correspondence</span>
                <svg class="ml-auto w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M19 9l-7 7-7-7" stroke-width="3"></path>
                </svg>
            </button>
        </div>

        {{-- Permohonan - AKTIF --}}
        <a href="{{ route('admin.surat.index') }}"
           class="group flex items-center px-4 py-4 rounded-2xl transition-all duration-300 {{ request()->routeIs('admin.surat.index') ? 'bg-[#800000] text-white shadow-xl' : 'text-slate-400 hover:bg-gray-50 hover:text-[#800000]' }}">
            <div class="p-2 rounded-xl {{ request()->routeIs('admin.surat.index') ? 'bg-white/10 text-white' : 'bg-gray-50 group-hover:bg-white text-slate-400 group-hover:text-[#800000] transition-colors' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                </svg>
            </div>
            <span class="ml-4 text-[11px] font-black uppercase tracking-[0.2em] italic">Permohonan</span>
        </a>

        <p class="mt-10 mb-4 px-4 text-[9px] font-black uppercase tracking-[0.4em] text-slate-300 italic">
            Configuration
        </p>

        {{-- Instansi - NON-AKTIF (#) --}}
        <div class="group flex items-center px-4 py-4 rounded-2xl opacity-30 pointer-events-none grayscale">
            <div class="p-2 rounded-xl bg-gray-50 text-slate-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
            <div class="flex flex-col ml-4 text-slate-300">
                <span class="text-[11px] font-black uppercase tracking-[0.2em] italic leading-none">Instansi</span>
                <span class="text-[7px] font-bold uppercase mt-1 tracking-widest">Master Alamat</span>
            </div>
        </div>

        {{-- Klasifikasi - NON-AKTIF (#) --}}
        <div class="group flex items-center px-4 py-4 rounded-2xl opacity-30 pointer-events-none grayscale">
            <div class="p-2 rounded-xl bg-gray-50 text-slate-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M7 7h.01M7 11h.01M7 15h.01M13 7h.01M13 11h.01M13 15h.01M17 21V5a2 2 0 00-2-2H5a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"></path>
                </svg>
            </div>
            <span class="ml-4 text-[11px] font-black uppercase tracking-[0.2em] italic text-slate-300">Klasifikasi</span>
        </div>

        {{-- Logout - AKTIF --}}
        <div class="pt-16">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="group flex w-full items-center px-4 py-4 rounded-2xl text-red-600 transition-all hover:bg-red-50 hover:shadow-lg hover:shadow-red-900/5">
                    <div class="p-2 rounded-xl bg-red-100 group-hover:bg-[#800000] group-hover:text-white transition-all">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                    </div>
                    <span class="ml-4 text-[11px] font-black uppercase tracking-[0.2em] italic">Terminate Session</span>
                </button>
            </form>
        </div>
    </nav>
</aside>
