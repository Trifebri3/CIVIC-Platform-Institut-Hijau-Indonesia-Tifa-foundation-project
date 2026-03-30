<aside class="sidebar-wrapper fixed left-0 top-0 z-40 h-screen w-64 border-r border-gray-100 bg-white transition-transform">
    {{-- Logo Section --}}
    <div class="flex h-24 items-center justify-center border-b border-gray-50 px-6">
        <a href="#" class="block">
            <img src="{{ asset('images/logo.png') }}" alt="CIVIC Logo" class="h-10 w-auto object-contain">
        </a>
    </div>

    <nav class="mt-8 px-4 space-y-2">
        <p class="mb-4 px-4 text-[9px] font-black uppercase tracking-[0.3em] text-gray-400 opacity-70">
            Program Management
        </p>

        {{-- Dashboard --}}
        <a href="{{ route('adminprogram.dashboard') }}"
           class="group flex items-center px-4 py-3.5 rounded-2xl transition-all duration-300 {{ request()->routeIs('adminprogram.dashboard') ? 'bg-red-50 text-[#800000]' : 'text-gray-400 hover:bg-gray-50 hover:text-[#800000]' }}">
            <div class="p-2 rounded-xl {{ request()->routeIs('adminprogram.dashboard') ? 'bg-white shadow-sm text-[#800000]' : 'group-hover:bg-white transition-colors' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
            </div>
            <span class="ml-3 text-[11px] font-black uppercase tracking-[0.15em]">Dashboard</span>
            @if(request()->routeIs('admin-program.dashboard'))
                <div class="ml-auto w-1.5 h-1.5 rounded-full bg-[#800000] shadow-[0_0_8px_rgba(128,0,0,0.5)]"></div>
            @endif
        </a>

        {{-- Program Utama --}}
<div x-data="{ open: {{ request()->routeIs('admin-program.*') ? 'true' : 'false' }} }" class="space-y-1">
    {{-- Dropdown Trigger --}}
    <button @click="open = !open"
            class="w-full group flex items-center px-4 py-3.5 rounded-2xl transition-all duration-300 {{ request()->routeIs('admin-program.*') ? 'bg-red-50/50 text-[#800000]' : 'text-gray-400 hover:bg-gray-50 hover:text-[#800000]' }}">

        <div class="p-2 rounded-xl {{ request()->routeIs('admin-program.*') ? 'bg-white shadow-sm text-[#800000]' : 'group-hover:bg-white transition-colors' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
        </div>

        <span class="ml-3 text-[11px] font-black uppercase tracking-[0.15em]">Agenda Program</span>

        {{-- Arrow Icon --}}
        <div class="ml-auto transition-transform duration-300" :class="open ? 'rotate-180' : ''">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path>
            </svg>
        </div>
    </button>

    {{-- Dropdown Content --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="pl-14 pr-2 space-y-1 mt-1">

        {{-- Item 1: Data Program --}}
        <a href="{{ route('admin-program.content.index') }}"
           class="block py-2 px-3 text-[10px] font-black uppercase tracking-widest rounded-lg transition-colors {{ request()->routeIs('admin-program.content.index') ? 'text-[#800000] bg-red-50/50' : 'text-gray-400 hover:text-[#800000] hover:bg-gray-50' }}">
            <span class="flex items-center gap-2">
                <div class="w-1 h-1 rounded-full {{ request()->routeIs('admin-program.content.index') ? 'bg-[#800000]' : 'bg-gray-300' }}"></div>
                Data Program
            </span>
        </a>

        {{-- Item 2: Management Kelas --}}
        <a href="{{ route('admin-program.kelas.pertemuan') }}"
           class="block py-2 px-3 text-[10px] font-black uppercase tracking-widest rounded-lg transition-colors {{ request()->routeIs('admin-program.kelas.pertemuan') ? 'text-[#800000] bg-red-50/50' : 'text-gray-400 hover:text-[#800000] hover:bg-gray-50' }}">
            <span class="flex items-center gap-2">
                <div class="w-1 h-1 rounded-full {{ request()->routeIs('admin-program.kelas.pertemuan') ? 'bg-[#800000]' : 'bg-gray-300' }}"></div>
                Management Kelas
            </span>
        </a>





    </div>
</div>

        {{-- Laporan Rekapitulasi --}}
        <a href="#"
           class="group flex items-center px-4 py-3.5 rounded-2xl transition-all duration-300 {{ request()->routeIs('admin-program.reports.*') ? 'bg-red-50 text-[#800000]' : 'text-gray-400 hover:bg-gray-50 hover:text-[#800000]' }}">
            <div class="p-2 rounded-xl {{ request()->routeIs('admin-program.reports.*') ? 'bg-white shadow-sm text-[#800000]' : 'group-hover:bg-white transition-colors' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 01-2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <span class="ml-3 text-[11px] font-black uppercase tracking-[0.15em]">Laporan</span>
            @if(request()->routeIs('admin-program.reports.*'))
                <div class="ml-auto w-1.5 h-1.5 rounded-full bg-[#800000] shadow-[0_0_8px_rgba(128,0,0,0.5)]"></div>
            @endif
        </a>


{{-- Di resources/views/pages/admin-program/layouts/sidebar.blade.php baris 104 --}}

@php
    // Logic: Ambil program dari route, kalau gak ada cek dari subProgram, kalau gak ada ambil yang pertama dikelola admin
    $currentProgram = request()->route('program')
        ?? (isset($subProgram) ? $subProgram->program : \App\Models\Program::first());
@endphp

@if($currentProgram)
    <a href="{{ route('admin.program.tracking.global', $currentProgram->slug) }}"
       class="flex items-center gap-4 px-6 py-4 transition-all {{ request()->routeIs('admin.program.tracking.global') ? 'bg-[#800000] text-white shadow-lg shadow-red-900/20' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
        <div class="relative">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" stroke-width="2.5"/>
            </svg>
            @if(request()->routeIs('admin.program.tracking.global'))
                <span class="absolute -right-1 -top-1 w-2 h-2 bg-white rounded-full animate-pulse"></span>
            @endif
        </div>
        <span class="text-[10px] font-black uppercase tracking-[0.2em] italic">Global Analytics</span>
    </a>
@endif


<a href="{{ route('admin.penilaian.index') }}"
   class="group flex items-center justify-between px-6 py-4 transition-all duration-300 {{ request()->routeIs('admin.penilaian.*') ? 'bg-[#800000] text-white shadow-2xl shadow-[#800000]/20' : 'text-slate-400 hover:text-black hover:bg-gray-50' }}">

    <div class="flex items-center gap-4">
        <div class="relative">
            <svg class="w-5 h-5 transition-transform duration-500 group-hover:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>

            @if(request()->routeIs('admin.penilaian.*'))
                <span class="absolute -right-1 -top-1 flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-white"></span>
                </span>
            @endif
        </div>

        <div class="flex flex-col">
            <span class="text-[10px] font-black uppercase tracking-[0.2em] italic leading-none">Input Nilai</span>
            <span class="text-[7px] font-bold {{ request()->routeIs('admin.penilaian.*') ? 'text-white/60' : 'text-gray-300' }} uppercase tracking-widest mt-1 group-hover:text-[#800000] transition-colors">Program Execution & PDF</span>
        </div>
    </div>

    {{-- Badge Count (Opsional: Menampilkan jumlah penilaian hari ini) --}}
    <div class="flex items-center">
        <svg class="w-3 h-3 opacity-0 group-hover:opacity-100 transition-all transform translate-x-2 group-hover:translate-x-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path d="M13 7l5 5-5 5M6 7l5 5-5 5" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </div>
</a>



<a href="{{ route('admin.program.management') }}"
   class="flex items-center gap-3 px-6 py-4 rounded-2xl {{ request()->routeIs('admin.program.management') ? 'bg-[#800000] text-white' : 'text-gray-400 hover:bg-gray-50' }}">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
    <span class="text-[10px] font-black uppercase tracking-widest italic">Whitelist Program</span>
</a>



{{-- Ambil semua program yang aktif --}}
@php $allPrograms = \App\Models\ProgramKhusus::where('is_active', true)->get(); @endphp

@foreach($allPrograms as $sideProg)
    <a href="{{ route('admin.program.dashboard-khusus', ['id' => $sideProg->id]) }}"
       class="flex items-center gap-3 px-4 py-3 {{ request()->is('admin/program-khusus/'.$sideProg->id.'*') ? 'text-[#800000] bg-gray-50' : 'text-gray-400' }} hover:text-[#800000] transition-colors group">
        <div class="w-2 h-2 rounded-full {{ request()->is('admin/program-khusus/'.$sideProg->id.'*') ? 'bg-[#800000]' : 'bg-gray-300' }}"></div>
        <span class="text-[9px] font-black uppercase italic">{{ $sideProg->nama_program }}</span>
    </a>
@endforeach

<hr class="my-2 border-gray-100">

{{-- Link ke Index (Daftar Utama) --}}
<a href="{{ url('/admin/program-management') }}" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-[#800000] transition-colors group">
    <div class="w-5 h-5 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center group-hover:border-[#800000]">
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="3"/></svg>
    </div>
    <span class="text-[9px] font-black uppercase italic">Manage All Programs</span>
</a>





        {{-- Logout --}}
        <div class="pt-10">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="group flex w-full items-center px-4 py-3.5 rounded-2xl text-red-600 transition-all hover:bg-red-50">
                    <div class="p-2 rounded-xl bg-red-100 group-hover:bg-red-600 group-hover:text-white transition-all shadow-sm">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                    </div>
                    <span class="ml-3 text-[11px] font-black uppercase tracking-[0.15em]">Keluar</span>
                </button>
            </form>
        </div>
    </nav>
</aside>
