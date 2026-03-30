<aside class="sidebar-wrapper fixed left-0 top-0 z-40 h-screen w-72 border-r border-gray-100 bg-white transition-transform overflow-y-auto">
    {{-- 1. Logo Section --}}
    <div class="flex h-24 items-center justify-center border-b border-gray-50 px-8">
        <a href="{{ route('superadmin.dashboard') }}" class="block">
            <img src="{{ asset('images/logo.png') }}" alt="CIVIC Logo" class="h-9 w-auto object-contain">
        </a>
    </div>

    <nav class="mt-8 px-5 space-y-2 pb-10">
        <p class="mb-5 px-4 text-[9px] font-black uppercase tracking-[0.4em] text-slate-300 italic">
            Main Navigation
        </p>

        {{-- 2. Dashboard --}}
        <a href="{{ route('superadmin.dashboard') }}"
           class="group flex items-center px-4 py-4 rounded-2xl transition-all duration-500 {{ Request::routeIs('superadmin.dashboard') ? 'bg-[#800000] text-white shadow-xl shadow-red-900/20' : 'text-slate-400 hover:bg-gray-50 hover:text-[#800000]' }}">
            <div class="p-2 rounded-xl {{ Request::routeIs('superadmin.dashboard') ? 'bg-white/10 text-white' : 'bg-gray-50 group-hover:bg-white text-slate-400 group-hover:text-[#800000] transition-colors' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
            </div>
            <span class="ml-4 text-[11px] font-black uppercase tracking-[0.2em] italic">Dashboard</span>
        </a>

        {{-- 3. Manajemen Pengguna --}}
        <a href="{{ route('superadmin.users.index') }}"
           class="group flex items-center px-4 py-4 rounded-2xl transition-all duration-300 {{ Request::routeIs('superadmin.users.*') ? 'bg-slate-900 text-white shadow-xl' : 'text-slate-400 hover:bg-gray-50 hover:text-black' }}">
            <div class="p-2 rounded-xl {{ Request::routeIs('superadmin.users.*') ? 'bg-white/10 text-white' : 'bg-gray-50 group-hover:bg-white text-slate-400 transition-colors' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
            <span class="ml-4 text-[11px] font-black uppercase tracking-[0.2em] italic">User Management</span>
        </a>

        {{-- 4. Manajemen Aktivasi Awal --}}
        <a href="{{ route('superadmin.activation.index') }}"
           class="group flex items-center px-4 py-4 rounded-2xl transition-all duration-300 {{ Request::routeIs('superadmin.activation.*') ? 'bg-slate-900 text-white shadow-xl' : 'text-slate-400 hover:bg-gray-50 hover:text-black' }}">
            <div class="p-2 rounded-xl {{ Request::routeIs('superadmin.activation.*') ? 'bg-white/10 text-white' : 'bg-gray-50 group-hover:bg-white text-slate-400 transition-colors' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                </svg>
            </div>
            <span class="ml-4 text-[11px] font-black uppercase tracking-[0.2em] italic">Activation Control</span>
        </a>

        {{-- 5. Program --}}
        <a href="{{ route('superadmin.programs.index') }}"
           class="group flex items-center px-4 py-4 rounded-2xl transition-all duration-300 {{ request()->routeIs('superadmin.programs.*') ? 'bg-red-50 text-[#800000]' : 'text-slate-400 hover:bg-gray-50' }}">
            <div class="p-2 rounded-xl {{ request()->routeIs('superadmin.programs.*') ? 'bg-white shadow-sm text-[#800000]' : 'bg-gray-50 group-hover:bg-white text-slate-400 transition-colors' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
            </div>
            <span class="ml-4 text-[11px] font-black uppercase tracking-[0.2em] italic">Program List</span>
            @if(request()->routeIs('superadmin.programs.*'))
                <div class="ml-auto w-1.5 h-1.5 rounded-full bg-[#800000] shadow-[0_0_8px_rgba(128,0,0,0.5)]"></div>
            @endif
        </a>

        {{-- 6. Template --}}
        <a href="{{ route('superadmin.templates.index') }}"
           class="group flex items-center px-4 py-4 rounded-2xl transition-all duration-300 {{ request()->routeIs('superadmin.templates.*') ? 'bg-red-50 text-[#800000]' : 'text-slate-400 hover:bg-gray-50' }}">
            <div class="p-2 rounded-xl {{ request()->routeIs('superadmin.templates.*') ? 'bg-white shadow-sm text-[#800000]' : 'bg-gray-50 group-hover:bg-white text-slate-400 transition-colors' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>
                </svg>
            </div>
            <span class="ml-4 text-[11px] font-black uppercase tracking-[0.2em] italic">Doc Template</span>
        </a>

        {{-- 7. Mass Invite --}}
        <a href="{{ route('admin.invite') }}"
           class="group flex items-center px-4 py-4 rounded-2xl transition-all duration-300 {{ request()->routeIs('admin.invite') ? 'bg-black text-white shadow-xl' : 'text-slate-400 hover:bg-gray-50' }}">
            <div class="p-2 rounded-xl {{ request()->routeIs('admin.invite') ? 'bg-[#800000] text-white' : 'bg-gray-50 group-hover:bg-white text-slate-400 transition-colors' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </div>
            <span class="ml-4 text-[11px] font-black uppercase tracking-[0.2em] italic">Mass Invite</span>
            @if(request()->routeIs('admin.invite'))
                <span class="ml-auto w-2 h-2 bg-[#800000] rounded-full animate-ping"></span>
            @endif
        </a>

        <p class="mt-10 mb-4 px-4 text-[9px] font-black uppercase tracking-[0.4em] text-slate-300 italic">Communication Hub</p>

        {{-- 8. Broadcast Center (Announcements) --}}
        <a href="{{ route('superadmin.announcements') }}"
           class="group flex items-center px-4 py-4 rounded-2xl transition-all duration-300 {{ request()->routeIs('superadmin.announcements') ? 'bg-[#800000] text-white shadow-xl' : 'text-slate-400 hover:bg-gray-50' }}">
            <div class="p-2 rounded-xl {{ request()->routeIs('superadmin.announcements') ? 'bg-white/10 text-white' : 'bg-gray-50 group-hover:bg-white text-slate-400 transition-colors' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                </svg>
            </div>
            <div class="flex flex-col ml-4">
                <span class="text-[11px] font-black uppercase tracking-[0.2em] italic leading-none">Broadcast</span>
                <span class="text-[7px] font-bold {{ request()->routeIs('superadmin.announcements') ? 'text-white/60' : 'text-slate-300' }} uppercase tracking-widest mt-1">Announcements</span>
            </div>
        </a>

        {{-- 9. Validasi Nilai (E-Raport) --}}
        <a href="{{ route('superadmin.validasi-nilai') }}"
           class="group flex items-center px-4 py-4 rounded-2xl transition-all duration-300 {{ request()->routeIs('superadmin.validasi-nilai') ? 'bg-[#800000] text-white shadow-xl' : 'text-slate-400 hover:bg-gray-50' }}">
            <div class="p-2 rounded-xl {{ request()->routeIs('superadmin.validasi-nilai') ? 'bg-white/10 text-white' : 'bg-gray-50 group-hover:bg-white text-slate-400 transition-colors' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="flex flex-col ml-4">
                <span class="text-[11px] font-black uppercase tracking-[0.2em] italic leading-none">Validation</span>
                <span class="text-[7px] font-bold {{ request()->routeIs('superadmin.validasi-nilai') ? 'text-white/60' : 'text-slate-300' }} uppercase tracking-widest mt-1">E-Raport System</span>
            </div>
        </a>

        {{-- 10. Master Program Khusus --}}
        <a href="{{ route('superadmin.programkhusus') }}"
           class="group flex items-center px-4 py-4 rounded-2xl transition-all duration-300 {{ request()->routeIs('superadmin.programkhusus') ? 'bg-black text-white shadow-xl' : 'text-slate-400 hover:bg-gray-50' }}">
            <div class="p-2 rounded-xl {{ request()->routeIs('superadmin.programkhusus') ? 'bg-[#800000] text-white' : 'bg-gray-50 group-hover:bg-white text-slate-400 transition-colors' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </div>
            <span class="ml-4 text-[11px] font-black uppercase tracking-[0.2em] italic">Master Data</span>
        </a>

        {{-- Logout Section --}}
        <div class="pt-10 border-t border-gray-50 mt-4">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="group flex w-full items-center px-4 py-4 rounded-2xl text-red-600 transition-all hover:bg-red-50">
                    <div class="p-2 rounded-xl bg-red-100 group-hover:bg-red-600 group-hover:text-white transition-all">
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
