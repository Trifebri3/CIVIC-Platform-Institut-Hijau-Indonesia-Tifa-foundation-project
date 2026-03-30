@component('pages.admin-program.layouts.app')
    <div class="py-12 bg-[#F8FAFC] min-h-screen">
        <div class="max-w-[90rem] mx-auto sm:px-6 lg:px-8">

            {{-- Header Navigation --}}
            <div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6 px-4">
                <div class="flex items-center gap-5">
                    <div class="p-4 bg-slate-900 rounded-[1.5rem] shadow-xl shadow-slate-200">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-3xl font-black italic uppercase text-slate-800 tracking-tighter leading-none">
                            Monitoring <span class="text-[#800000]">Reports</span>
                        </h2>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.3em] mt-1 italic">
                            Verification & Approval Center
                        </p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    {{-- TOMBOL BARU: Create/Manage Template --}}
                    {{-- Tombol ini muncul jika ada period_id, langsung gas ke Builder --}}
                    @if($period_id)
                        <a href="{{ route('admin.report-template.builder', $period_id) }}"
                           class="inline-flex items-center gap-2 px-6 py-3 bg-black text-white text-[10px] font-black uppercase italic rounded-2xl hover:bg-[#800000] transition-all tracking-[0.15em] shadow-xl shadow-slate-200">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M12 4v16m8-8H4" stroke-width="3" stroke-linecap="round"></path>
                            </svg>
                            Configure Form Template
                        </a>
                    @endif

                    <a href="{{ route('admin.program.keuangan.index') }}"
                       class="px-6 py-3 bg-white border border-gray-200 rounded-2xl text-[10px] font-black uppercase italic text-slate-500 hover:bg-slate-50 transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M10 19l-7-7m0 0l7-7m-7 7h18" stroke-width="2.5"></path></svg>
                        Back to Periods
                    </a>
                </div>
            </div>

            {{-- Livewire Component --}}
            <div class="relative">
                <livewire:admin-program.programkhusus.laporan.report-index :selectedPeriod="$period_id" />
            </div>

            {{-- Info Footer --}}
            <div class="mt-12 px-10 py-6 bg-slate-100/50 rounded-[2.5rem] border border-slate-200/50">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                    <p class="text-[9px] font-bold text-slate-400 uppercase italic tracking-widest leading-loose">
                        Laporan wajib diisi oleh mahasiswa jika RAB telah disetujui.<br>
                        Gunakan tombol <span class="text-black font-black italic underline">Configure Form Template</span> untuk mengatur apa saja yang harus dilaporkan.
                    </p>
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                        <span class="text-[9px] font-black uppercase italic text-slate-500">Live System Active</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endcomponent
