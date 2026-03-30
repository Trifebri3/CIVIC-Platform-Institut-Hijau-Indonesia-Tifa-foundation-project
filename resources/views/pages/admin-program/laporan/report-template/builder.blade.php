@component('pages.admin-program.layouts.app')
<div class="py-12 bg-[#F8FAFC] min-h-screen">
        <div class="max-w-[95rem] mx-auto sm:px-6 lg:px-8">

            {{-- Navigation & Breadcrumb --}}
            <div class="mb-10 flex items-center justify-between px-6">
                <div class="flex items-center gap-6">
                    <a href="{{ route('admin.program.keuangan.index') }}"
                       class="group p-4 bg-white rounded-[1.8rem] shadow-sm hover:bg-black transition-all duration-300 border border-gray-100">
                        <svg class="w-5 h-5 text-slate-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M10 19l-7-7m0 0l7-7m-7 7h18" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-3xl font-black italic uppercase tracking-tighter text-slate-800 leading-none">
                            Form <span class="text-[#800000]">Architect</span>
                        </h1>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.4em] mt-1 italic">
                            Report Configuration Tool
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="text-right hidden md:block">
                        <p class="text-[9px] font-black text-slate-400 uppercase italic">Current Status</p>
                        <p class="text-[11px] font-bold text-emerald-500 uppercase tracking-widest">● System Ready</p>
                    </div>
                </div>
            </div>

            {{-- Livewire Component Builder --}}
            <div class="relative min-h-[600px]">
                <livewire:admin-program.programkhusus.laporan.report-template-builder :period="$period" />
            </div>

            {{-- Footer Info --}}
            <div class="mt-16 border-t border-slate-200 pt-8 flex justify-between items-center px-10">
                <p class="text-[9px] font-black uppercase italic text-slate-400 tracking-widest leading-relaxed">
                    Setiap perubahan pada arsitektur form akan langsung <br> berdampak pada formulir pengisian mahasiswa.
                </p>
                <div class="flex gap-4 opacity-30 group hover:opacity-100 transition-opacity">
                    <div class="w-10 h-10 rounded-xl bg-slate-200"></div>
                    <div class="w-10 h-10 rounded-xl bg-slate-200"></div>
                </div>
            </div>

        </div>
    </div>
@endcomponent
