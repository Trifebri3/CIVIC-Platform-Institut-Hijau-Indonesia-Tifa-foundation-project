@component('pages.user.layouts.app')
<div class="min-h-screen bg-[#F8FAFC] py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Header & Navigation --}}
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-10">
                <div class="flex items-center gap-4">
                    <a href="{{ route('user.dashboard') }}"
                       class="group p-3 bg-white rounded-2xl shadow-sm hover:bg-black transition-all duration-300">
                        <svg class="w-5 h-5 text-slate-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M15 19l-7-7 7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl font-black italic uppercase tracking-tighter text-slate-800">Program Profile</h1>
                        <p class="text-[9px] font-bold text-[#800000] uppercase tracking-[0.3em] italic">Identity & Documentation System</p>
                    </div>
                </div>

                {{-- Status Badge (Opsional) --}}
                <div class="hidden md:block">
                    <div class="px-6 py-2 bg-slate-200/50 rounded-full border border-slate-200">
                        <span class="text-[10px] font-black uppercase italic text-slate-500 tracking-widest">Global Identity</span>
                    </div>
                </div>
            </div>

            {{-- Main Component --}}
            <div class="relative">
                {{-- Komponen Livewire yang kita buat sebelumnya --}}
                <livewire:user.program-profile-form />
            </div>

            {{-- Footer Note --}}
            <div class="mt-12 text-center">
                <p class="text-[10px] font-bold text-slate-400 uppercase italic tracking-widest leading-loose">
                    Pastikan data yang diinput sudah sesuai dengan kondisi lapangan.<br>
                    Perubahan profil akan berdampak pada seluruh laporan kegiatan Anda.
                </p>
            </div>
        </div>
    </div>
@endcomponent
