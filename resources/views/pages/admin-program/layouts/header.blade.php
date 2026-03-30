<header class="sticky top-0 z-30 flex h-20 w-full items-center justify-between border-b border-gray-100 bg-white/80 px-6 lg:px-10 backdrop-blur-md shadow-sm">
    <div class="flex items-center">
        {{-- Mobile Menu Button --}}
        <button class="mr-4 text-[#800000] lg:hidden transition-transform active:scale-95">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16m-7 6h7"></path>
            </svg>
        </button>

        {{-- Breadcrumb Navigation --}}
        <nav class="flex items-center space-x-2 text-[10px] sm:text-[11px] font-bold uppercase tracking-[0.15em]">
            <span class="text-gray-400">Admin Console</span>
            <svg class="h-3 w-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path>
            </svg>
            <span class="text-[#800000] decoration-[#800000]/20 underline-offset-4 underline italic">
                {{-- Mengambil segment ke-2 atau ke-3 tergantung struktur route admin program Anda --}}
                {{ str_replace('-', ' ', Request::segment(2) ?? 'Dashboard') }}
            </span>
        </nav>
    </div>

    <div class="flex items-center gap-4 sm:gap-6">
        {{-- User Info --}}
        <div class="hidden flex-col items-end sm:flex">
            <p class="text-sm font-black tracking-tight text-gray-900 uppercase">
                {{ Auth::user()->name }}
            </p>
            <div class="flex items-center gap-1.5">
                {{-- Status Indicator: Menggunakan warna Maroon yang solid --}}
                <span class="h-1.5 w-1.5 rounded-full bg-[#800000] shadow-[0_0_8px_#800000]"></span>
                <p class="text-[9px] font-black uppercase tracking-[0.2em] text-[#800000]/70">
                    Program Manager
                </p>
            </div>
        </div>

        {{-- Profile Avatar dengan Efek Luxury --}}
        <div class="relative group cursor-pointer">
            {{-- Glow effect yang lebih soft (Maroon ke Bronze) --}}
            <div class="absolute -inset-1 rounded-full bg-gradient-to-tr from-[#800000] to-[#b8860b] opacity-20 group-hover:opacity-100 transition duration-500 blur-[4px]"></div>

            <div class="relative">
                <img class="h-10 w-10 sm:h-11 sm:w-11 rounded-full border-2 border-white object-cover shadow-md"
                     src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=800000&color=fff&bold=true"
                     alt="Admin Avatar">

                {{-- Online Indicator Badge --}}
                <div class="absolute bottom-0 right-0 h-3 w-3 rounded-full border-2 border-white bg-green-500"></div>
            </div>
        </div>
    </div>
</header>
