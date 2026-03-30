<header class="sticky top-0 z-30 flex h-20 w-full items-center justify-between border-b border-gray-100 bg-white/80 px-10 backdrop-blur-md shadow-sm">
    <div class="flex items-center">
        <button class="mr-4 text-[#800000] lg:hidden transition-transform active:scale-95">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16m-7 6h7"></path>
            </svg>
        </button>

        <nav class="flex items-center space-x-2 text-[11px] font-bold uppercase tracking-[0.15em]">
            <span class="text-gray-400">Dashboard</span>
            <svg class="h-3 w-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path>
            </svg>
            <span class="text-[#800000] decoration-[#800000]/20 underline-offset-4 underline italic">
                {{ str_replace('-', ' ', Request::segment(2)) }}
            </span>
        </nav>
    </div>

    <div class="flex items-center gap-5">
        <div class="hidden flex-col items-end sm:flex">
            <p class="text-sm font-extrabold tracking-tight text-gray-900">
                {{ Auth::user()->name }}
            </p>
            <div class="flex items-center gap-1.5">
                <span class="h-1.5 w-1.5 rounded-full bg-[#800000] animate-pulse"></span>
                <p class="text-[10px] font-bold uppercase tracking-widest text-[#a52a2a]">
                    Super Administrator
                </p>
            </div>
        </div>

        <div class="relative group cursor-pointer">
            <div class="absolute -inset-0.5 rounded-full bg-gradient-to-tr from-[#800000] to-[#eab308] opacity-30 group-hover:opacity-100 transition duration-300 blur-[2px]"></div>
            <img class="relative h-11 w-11 rounded-full border-2 border-white object-cover shadow-sm"
                 src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}&background=800000&color=fff&bold=true"
                 alt="Avatar">
        </div>
    </div>
</header>
