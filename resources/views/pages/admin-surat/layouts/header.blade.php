<header class="sticky top-0 z-30 flex h-20 w-full items-center justify-between border-b border-gray-100 bg-white/80 px-6 lg:px-10 backdrop-blur-md shadow-sm">
    <div class="flex items-center">
        {{-- Mobile Menu Button --}}
        <button class="mr-4 text-[#800000] lg:hidden transition-transform active:scale-95">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16m-7 6h7"></path>
            </svg>
        </button>

        {{-- Breadcrumb Navigation --}}
        <nav class="flex items-center space-x-2 text-[10px] sm:text-[11px] font-black uppercase tracking-[0.2em]">
            <span class="text-gray-300">Registry</span>
            <svg class="h-3 w-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path>
            </svg>
            <span class="text-[#800000] decoration-[#800000]/20 underline-offset-4 underline italic">
                {{-- Logika pengambilan segment route untuk admin surat --}}
                {{ str_replace('-', ' ', Request::segment(2) ?? 'Dashboard') }}
            </span>
        </nav>
    </div>

    <div class="flex items-center gap-4 sm:gap-8">
        {{-- Date Info (Opsional, sangat berguna untuk Admin Surat) --}}
        <div class="hidden lg:flex flex-col items-end border-r border-gray-100 pr-6">
            <p class="text-[9px] font-black text-gray-300 uppercase tracking-widest">Waktu Server</p>
            <p class="text-[11px] font-bold text-slate-700 italic uppercase leading-none mt-1">{{ now()->translatedFormat('d F Y') }}</p>
        </div>

        {{-- User Info --}}
        <div class="hidden flex-col items-end sm:flex">
            <p class="text-sm font-black tracking-tighter text-slate-900 uppercase italic">
                {{ Auth::user()->name }}
            </p>
            <div class="flex items-center gap-2">
                {{-- Status Indicator: Deep Slate untuk Admin Surat --}}
                <span class="h-1.5 w-1.5 rounded-full bg-[#1e293b] shadow-[0_0_8px_rgba(30,41,59,0.5)] animate-pulse"></span>
                <p class="text-[9px] font-black uppercase tracking-[0.25em] text-slate-400">
                    Chief of Correspondence
                </p>
            </div>
        </div>

        {{-- Profile Avatar dengan Efek Luxury --}}
        <div class="relative group cursor-pointer">
            {{-- Glow effect: Maroon ke Slate --}}
            <div class="absolute -inset-1 rounded-full bg-gradient-to-tr from-[#800000] to-[#1e293b] opacity-10 group-hover:opacity-100 transition duration-500 blur-[6px]"></div>

            <div class="relative">
                <img class="h-10 w-10 sm:h-12 sm:w-12 rounded-full border-2 border-white object-cover shadow-sm transition-transform duration-500 group-hover:scale-105"
                     src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=1e293b&color=fff&bold=true"
                     alt="Admin Avatar">

                {{-- Status Badge (Official Look) --}}
                <div class="absolute -bottom-0.5 -right-0.5 h-3.5 w-3.5 rounded-full border-2 border-white bg-emerald-500 shadow-sm"></div>
            </div>
        </div>
    </div>
</header>
