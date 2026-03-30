<aside id="mobile-sidebar" class="fixed left-0 top-0 z-[60] h-screen w-72 border-r border-gray-100 bg-white transition-transform duration-500 ease-in-out -translate-x-full lg:translate-x-0 lg:z-40 flex flex-col">

    {{-- Header Sidebar: Fixed --}}
    <div class="flex h-20 shrink-0 items-center justify-between px-8 border-b border-gray-50 bg-white">
        <a href="{{ route('user.dashboard') }}" class="block transition-transform active:scale-95">
            <img src="{{ asset('images/logo.png') }}" alt="CIVIC Logo" class="h-9 w-auto object-contain">
        </a>
        <button onclick="toggleSidebar()" class="lg:hidden p-2 rounded-xl bg-gray-50 text-gray-400 hover:text-[#800000]">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
@if(auth()->user()?->email === 'tamu@ihi.id')
    <div class="bg-[#800000] text-white text-[9px] font-black uppercase tracking-[0.3em] py-2 text-center">
        ⚡ Anda sedang dalam Mode Kunjungan - Akses Interaksi Dibatasi
    </div>
@endif
    {{-- Content Section: Scrollable --}}
    <div class="flex-1 overflow-y-auto custom-scrollbar pt-6 pb-32 px-5">
        <nav class="space-y-1">
            <p class="mb-3 px-4 text-[9px] font-black uppercase tracking-[0.3em] text-gray-300 italic">Menu Utama</p>

            {{-- Dashboard --}}
            <a href="{{ route('user.dashboard') }}"
               class="group flex items-center gap-3 rounded-2xl px-4 py-3 transition-all duration-300 {{ Request::routeIs('user.dashboard') ? 'bg-[#800000] text-white shadow-lg shadow-red-900/20' : 'text-gray-500 hover:bg-gray-50 hover:text-[#800000]' }}">
                <svg class="h-5 w-5 shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                <span class="text-xs font-extrabold tracking-tight uppercase italic">Dashboard</span>
            </a>

            {{-- Program Saya (Dropdown) --}}
            <div x-data="{ open: {{ Request::routeIs('user.my-programs', 'user.programs.index') ? 'true' : 'false' }} }" class="space-y-1">
                <button @click="open = !open"
                        class="w-full group flex items-center justify-between rounded-2xl px-4 py-3 transition-all duration-300 {{ Request::routeIs('user.my-programs', 'user.programs.index') ? 'bg-gray-50 text-[#800000]' : 'text-gray-500 hover:bg-gray-50 hover:text-[#800000]' }}">
                    <div class="flex items-center gap-3">
                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        <span class="text-xs font-extrabold tracking-tight uppercase italic">Program Saya</span>
                    </div>
                    <svg class="h-3 w-3 transition-transform duration-300" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div x-show="open" x-transition class="pl-12 pr-4 space-y-1">
                    <a href="{{ route('user.my-programs') }}" class="block py-1.5 text-[10px] font-bold {{ Request::routeIs('user.my-programs') ? 'text-[#800000]' : 'text-gray-400 hover:text-[#800000]' }}">
                        • Program Aktif
                    </a>
                    <a href="{{ route('user.programs.index') }}" class="block py-1.5 text-[10px] font-bold {{ Request::routeIs('user.programs.index') ? 'text-[#800000]' : 'text-gray-400 hover:text-[#800000]' }}">
                        • Daftar Program Baru
                    </a>
                </div>
            </div>

            {{-- Kelas Saya (Card Style) --}}
            <a href="{{ route('user.subprogram.index') }}"
               class="group relative flex items-center gap-3.5 rounded-2xl px-4 py-3 transition-all duration-500 {{ Request::routeIs('user.subprogram.index') ? 'bg-black text-white shadow-lg' : 'text-gray-500 hover:bg-white hover:text-[#800000]' }}">
                @if(Request::routeIs('user.subprogram.index'))
                    <div class="absolute inset-0 bg-gradient-to-r from-[#800000] to-black rounded-2xl -z-10"></div>
                @endif
                <div class="relative flex h-8 w-8 shrink-0 items-center justify-center rounded-xl {{ Request::routeIs('user.subprogram.index') ? 'bg-white/10' : 'bg-gray-100 group-hover:bg-[#800000]/10' }}">
                    <svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path></svg>
                </div>
                <div class="flex flex-col overflow-hidden">
                    <span class="text-[10px] font-black uppercase tracking-widest italic truncate leading-none {{ Request::routeIs('user.subprogram.index') ? 'text-white' : 'text-gray-900' }}">Kelas Saya</span>
                    <span class="text-[7px] font-bold uppercase tracking-tighter opacity-50">My Courses</span>
                </div>
            </a>

            <div class="pt-4 pb-2 px-4 italic border-t border-gray-50 mt-4">
                <p class="text-[9px] font-black text-gray-300 uppercase tracking-[0.3em]">Personal Tracking</p>
            </div>

            {{-- Academic Record --}}
            <a href="{{ route('user.progress') }}"
               class="group flex items-center gap-4 rounded-2xl px-4 py-3 transition-all {{ request()->routeIs('user.progress') ? 'bg-black text-white' : 'text-gray-400 hover:bg-gray-50' }}">
                <div class="relative">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" stroke-width="2.5" stroke-linecap="round"/></svg>
                    @if(request()->routeIs('user.progress')) <span class="absolute -right-1 -top-1 w-2 h-2 bg-[#800000] rounded-full animate-pulse"></span> @endif
                </div>
                <div class="flex flex-col">
                    <span class="text-[10px] font-black uppercase tracking-widest italic leading-none">Academic Record</span>
                    <span class="text-[7px] font-bold opacity-50 uppercase mt-1 italic">Track Progress</span>
                </div>
            </a>

            <div class="pt-4 pb-2 px-4 italic border-t border-gray-50 mt-4">
                <p class="text-[9px] font-black text-gray-300 uppercase tracking-[0.3em]">Services & Records</p>
            </div>

            {{-- Sertifikat --}}
            <a href="{{ route('user.certificates') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-2xl transition-all {{ request()->routeIs('user.certificates') ? 'bg-[#800000] text-white shadow-lg' : 'text-gray-500 hover:bg-gray-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" stroke-width="2" stroke-linecap="round"/></svg>
                <span class="text-[10px] font-black uppercase tracking-widest italic">Sertifikat</span>
            </a>

            {{-- Program Khusus & Layanan Surat (Merged Logics) --}}
            @php
                $activeProg = \App\Models\ProgramKhusus::whereHas('participants', fn($q) => $q->where('user_id', auth()->id())->where('is_active', 1))->first();
            @endphp

            @if($activeProg)
                <a href="{{ route('user.programkhusus.view', ['id' => $activeProg->id]) }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-2xl transition-all {{ request()->is('program-khusus/*') ? 'bg-[#800000] text-white' : 'text-gray-500 hover:bg-gray-50' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" stroke-width="2"/></svg>
                    <span class="text-[10px] font-black uppercase tracking-widest italic">Detail Program</span>
                </a>

                <a href="{{ route('user.surat.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-2xl transition-all {{ request()->routeIs('user.surat.*') ? 'bg-[#800000] text-white shadow-lg' : 'text-gray-500 hover:bg-gray-50' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <div class="flex flex-col">
                        <span class="text-[10px] font-black uppercase tracking-widest italic">Layanan Surat</span>
                        <span class="text-[7px] font-bold opacity-60 uppercase italic -mt-1 leading-none">Legal Document</span>
                    </div>
                </a>
            @else
                <div class="px-4 py-3 border border-dashed border-gray-200 rounded-2xl flex items-center gap-3 opacity-40">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" stroke-width="2"/></svg>
                    <p class="text-[7px] font-black text-gray-400 uppercase italic leading-tight">No active services. <br>Enroll now.</p>
                </div>
            @endif

            {{-- Settings --}}
            <a href="{{ route('user.settings') }}"
               class="group flex items-center gap-3 px-4 py-3 rounded-2xl transition-all {{ Request::routeIs('user.settings') ? 'bg-[#800000] text-white shadow-lg' : 'text-gray-500 hover:bg-gray-50 hover:text-[#800000]' }}">
                <svg class="h-5 w-5 transition-transform group-hover:rotate-45" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-2.572 1.065c-.94 1.543-3.31.826-2.37 2.37a1.724 1.724 0 00-1.066 2.573c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.572-1.065c-.94-1.543-.826-3.31-.826-3.31"></path>
                    <circle cx="12" cy="12" r="3" stroke-width="2"></circle>
                </svg>
                <span class="text-xs font-extrabold tracking-tight uppercase italic">Pengaturan</span>
            </a>

            <div class="py-2 border-t border-gray-50 my-2"></div>

            {{-- Logout --}}
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="group flex w-full items-center gap-3.5 rounded-2xl px-4 py-3 text-xs font-black italic tracking-widest text-red-500 hover:bg-red-50 transition-all duration-300">
                    <svg class="h-5 w-5 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    <span class="uppercase">Keluar Sesi</span>
                </button>
            </form>
        </nav>
    </div>

    {{-- Footer Sidebar: Fixed --}}
    <div class="p-5 border-t border-gray-50 bg-white shrink-0">
        <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-[8px] font-black uppercase tracking-widest text-gray-400">Account Status</p>
                <p class="text-[10px] font-bold text-[#800000] uppercase italic">Peserta Aktif</p>
            </div>
            <div class="h-2 w-2 rounded-full bg-green-500 animate-pulse"></div>
        </div>
    </div>
</aside>


{{-- Custom Scrollbar Style --}}
<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #f1f1f1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #e2e2e2; }
</style>
