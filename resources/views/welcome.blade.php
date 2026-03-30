@component('public.layouts.appumum')
    @slot('title', 'Selamat Datang')

    <div class="bg-white">
        {{-- 1. HERO SECTION: Split Screen Branding --}}
        <section class="relative w-full min-h-screen lg:h-screen flex flex-col lg:flex-row overflow-hidden border-b border-gray-50">
            {{-- Sisi Kiri: Branding & Tombol Utama --}}
            <div class="w-full lg:w-1/2 flex items-center justify-center p-12 md:p-20 lg:p-24 bg-white z-10">
                <div class="max-w-xl flex flex-col items-center lg:items-start text-center lg:text-left">

                    {{-- Logo & Organization --}}
                    <div class="mb-12 lg:mb-16">
                        <img src="{{ asset('images/logo.png') }}" alt="CIVIC Logo" class="h-16 w-auto mx-auto lg:mx-0 mb-6 object-contain">
                        <p class="text-[10px] font-black uppercase tracking-[0.6em] text-gray-300 italic">
                            Institut Hijau Indonesia
                        </p>
                    </div>

                    {{-- Title --}}
                    <h1 class="text-6xl md:text-7xl lg:text-8xl font-black tracking-tighter text-gray-900 uppercase italic leading-none mb-8">
                        CIVIC <br> <span class="text-[#800000] not-italic">Platform</span>
                    </h1>

                    {{-- Tagline --}}
                    <p class="text-sm md:text-base text-gray-400 uppercase tracking-[0.25em] font-medium leading-relaxed mb-12 max-w-md lg:max-w-none">
                        Digital Ecosystem for Democracy & Ecological Coordination.
                    </p>

                    {{-- CTA Utama: Langsung Masuk Apps --}}
                    <div class="flex flex-col sm:flex-row gap-4 w-full justify-center lg:justify-start">
                        @auth
                            <a href="{{ route('user.dashboard') }}"
                               class="group relative inline-flex items-center gap-6 px-12 py-6 bg-black text-white rounded-full shadow-2xl transition-all duration-500 hover:bg-[#800000] hover:-translate-y-1">
                                <div class="flex flex-col items-start leading-none text-left">
                                    <span class="text-[8px] uppercase tracking-widest text-gray-400 group-hover:text-white/60 mb-1">Authenticated</span>
                                    <span class="text-xs font-black uppercase tracking-widest italic text-white">Go to Dashboard</span>
                                </div>
                                <svg class="w-5 h-5 transition-transform group-hover:translate-x-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                </svg>
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                               class="group inline-flex items-center gap-8 px-12 py-6 bg-[#800000] text-white rounded-full shadow-[0_20px_40px_rgba(128,0,0,0.3)] transition-all duration-500 hover:bg-black hover:-translate-y-1">
                                <span class="text-xs font-black uppercase tracking-[0.2em] italic">Enter Application</span>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                </svg>
                            </a>
                            <a href="{{ route('register') }}"
                               class="group inline-flex items-center px-12 py-6 bg-white border border-gray-100 text-gray-400 rounded-full hover:text-black hover:border-gray-300 transition-all duration-500">
                                <span class="text-xs font-black uppercase tracking-[0.2em] italic">Create Account</span>
                            </a>
                        @endauth
                    </div>
                </div>
            </div>

            {{-- Sisi Kanan: Banner Image --}}
            <div class="w-full lg:w-1/2 h-80 lg:h-full relative overflow-hidden bg-gray-50 border-l border-gray-100">
                <img src="{{ asset('images/banner.png') }}" alt="Banner" class="absolute inset-0 w-full h-full object-cover object-center">
                <div class="absolute inset-0 bg-gradient-to-r from-white/20 to-transparent"></div>
            </div>
        </section>

        {{-- 2. TRANSITION SECTION: Welcome Message --}}
        <section class="py-24 bg-white text-center">
            <div class="max-w-2xl mx-auto px-8">
                <div class="inline-block w-12 h-[1px] bg-[#800000] mb-8"></div>
                <p class="text-lg md:text-xl text-gray-500 font-medium italic leading-relaxed">
                    "Selamat datang di ekosistem digital koordinasi dan edukasi peserta Institut Hijau Indonesia."
                </p>
                {{-- Scroll Indicator --}}
                <div class="mt-12 flex flex-col items-center opacity-20 group">
                    <div class="w-[1px] h-16 bg-gradient-to-b from-[#800000] to-transparent animate-bounce"></div>
                    <span class="mt-4 text-[8px] font-black uppercase tracking-[0.6em] text-gray-600">Featured Program</span>
                </div>
            </div>
        </section>

        {{-- 3. PROGRAM SECTION: Speak Justice --}}
        <section class="max-w-7xl mx-auto px-6 pb-32">
            <div class="relative w-full bg-white rounded-[3rem] p-8 md:p-20 overflow-hidden border border-gray-100 shadow-sm">
                {{-- BG Accents --}}
                <div class="absolute top-0 right-0 w-1/2 h-full pointer-events-none">
                    <div class="absolute top-[-20%] right-[-10%] w-[500px] h-[500px] bg-[#800000]/5 rounded-full blur-[100px]"></div>
                </div>

                <div class="relative z-10 flex flex-col items-center text-center">
                    {{-- Partner Logo --}}
                    <div class="flex items-center gap-6 mb-16 opacity-80">
                        <img src="{{ asset('images/logoddi.png') }}" alt="DDI" class="h-14 md:h-16 w-auto object-contain grayscale hover:grayscale-0 transition-all duration-700">
                        <div class="h-10 w-[1px] bg-gray-200 rotate-[20deg]"></div>
                    </div>

                    <div class="max-w-3xl">
                        <h4 class="text-[10px] font-black uppercase tracking-[0.6em] text-[#800000] mb-6 italic">Strategic Program 2026</h4>
                        <h1 class="text-5xl md:text-7xl font-black tracking-tighter uppercase leading-none italic text-gray-900 mb-10">
                            SPEAK <span class="text-[#800000] not-italic">Justice</span>
                        </h1>

                        <p class="text-sm md:text-base text-gray-500 font-bold uppercase tracking-[0.2em] leading-relaxed max-w-xl mx-auto mb-16">
                            Strengthening Democracy Participation to Promote <br class="hidden md:block"> Social and Ecological Justice
                        </p>

                        {{-- Info Bar --}}
                        <div class="flex flex-wrap items-center justify-center gap-8 border-t border-b border-gray-50 py-8 mb-16">
                            <div class="flex flex-col">
                                <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1">Current Status</span>
                                <span class="text-xs font-black text-[#800000] uppercase tracking-widest italic">Active Phase</span>
                            </div>
                            <div class="h-6 w-[1px] bg-gray-100 hidden md:block"></div>
                            <div class="flex flex-col">
                                <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1">Timeline</span>
                                <span class="text-xs font-black text-gray-800 uppercase tracking-widest italic">Mar – Mei 2026</span>
                            </div>
                        </div>

                        {{-- CTA ToR --}}
                        <a href="{{ route('public.speak-justice') }}"
                           class="group relative inline-flex items-center gap-6 px-12 py-5 bg-black text-white rounded-full transition-all duration-500 hover:bg-[#800000] shadow-xl hover:shadow-[0_20px_40px_rgba(128,0,0,0.2)]">
                            <span class="text-[10px] font-black uppercase tracking-[0.3em] italic text-white">Read Term of Reference</span>
                            <div class="p-1 rounded-full bg-white/10 group-hover:bg-white/20 transition-colors">
                                <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                </svg>
                            </div>
                        </a>
                    </div>

                    {{-- Support Footer --}}
                    <div class="mt-24 pt-10 border-t border-gray-50 w-full max-w-xs flex flex-col items-center opacity-40 hover:opacity-100 transition-opacity">
                        <p class="text-[8px] font-black text-gray-400 uppercase tracking-[0.4em] mb-4">Supported By</p>
                        <img src="{{ asset('images/logobesar.png') }}" alt="TIFA" class="h-8 md:h-10 w-auto grayscale contrast-125">
                    </div>
                </div>
            </div>
        </section>
    </div>
@endcomponent
