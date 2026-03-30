<header class="glass-nav sticky top-0 z-50 w-full h-20 flex items-center">
    <div class="max-w-7xl mx-auto px-6 lg:px-12 w-full flex items-center justify-between">

        {{-- Logo Section --}}
        <div class="flex items-center gap-8">
    <div class="flex items-center gap-6 lg:gap-10">
            <a href="/" class="flex items-center gap-3 sm:gap-4 transition-transform active:scale-95 group">
                {{-- Logo DDI --}}
                <img src="{{ asset('images/logoddi.png') }}"
                     alt="DDI Logo"
                     class="h-7 sm:h-9 w-auto object-contain transition-opacity group-hover:opacity-80">

                {{-- Vertical Divider (Opsional, untuk kesan mewah) --}}
                <div class="h-6 w-[1.5px] bg-gray-200 rotate-[15deg]"></div>

                {{-- Logo CIVIC --}}
                <img src="{{ asset('images/logo.png') }}"
                     alt="CIVIC Logo"
                     class="h-7 sm:h-9 w-auto object-contain transition-opacity group-hover:opacity-80">
            </a>


        </div>

            {{-- Navigasi Desktop --}}
            <nav class="hidden md:flex items-center space-x-8">
                <a href="{{ route('welcome') }}" class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-800 hover:text-[#800000] transition-colors italic">Home</a>
                <a href="{{ route('public.about') }}" class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 hover:text-[#800000] transition-colors italic">About</a>
                <a href="{{ route('public.map') }}" class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 hover:text-[#800000] transition-colors italic">Distribution</a>
            </nav>
        </div>

        {{-- Action Buttons --}}
        <div class="flex items-center gap-4">
            @auth
                <a href="{{ route('user.dashboard') }}"
                   class="group flex items-center gap-3 bg-white border border-gray-100 px-4 py-2 rounded-full shadow-sm hover:shadow-md transition-all">
                    <span class="text-[10px] font-black uppercase tracking-widest text-[#800000]">Go to App</span>
                    <div class="h-6 w-6 rounded-full bg-[#800000] flex items-center justify-center text-white transition-transform group-hover:rotate-12">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 7l5 5-5 5M6 7l5 5-5 5" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                </a>
            @else
                <a href="{{ route('login') }}" class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 hover:text-black transition-colors">Login</a>
                <a href="{{ route('register') }}"
                   class="bg-[#800000] text-white text-[10px] font-black uppercase tracking-[0.2em] px-6 py-3 rounded-full shadow-lg shadow-red-900/20 hover:scale-105 transition-all active:scale-95 italic">
                    Get Started
                </a>
            @endauth

            {{-- Mobile Menu Icon (Opsional) --}}
            <button class="md:hidden text-slate-800">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16m-7 6h7" stroke-width="2" stroke-linecap="round"/></svg>
            </button>
        </div>
    </div>
</header>
