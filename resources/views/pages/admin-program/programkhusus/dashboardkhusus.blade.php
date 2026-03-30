@component('pages.admin-program.layouts.app')

<div class="py-12 bg-[#fafafa] min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- HEADER: PROGRAM IDENTITY --}}
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 bg-white p-8 rounded-[3rem] shadow-xl border border-gray-100 overflow-hidden relative">
                <div class="relative z-10">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="px-4 py-1 bg-[#800000] text-white text-[9px] font-black uppercase italic rounded-full shadow-lg shadow-[#800000]/20">Program Command Center</span>
                        <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest italic">ID: #{{ $program->id }}</span>
                    </div>
                    <h1 class="text-4xl font-black italic uppercase tracking-tighter text-slate-800 leading-none">
                        {{ $program->nama_program }}
                    </h1>
                    <p class="text-sm text-gray-400 font-medium mt-2 max-w-xl">{{ $program->deskripsi_singkat }}</p>
                </div>

                <div class="flex gap-4 relative z-10">
                    <div class="text-center px-6 py-4 bg-gray-50 rounded-[2rem] border border-gray-100">
                        <p class="text-[24px] font-black text-[#800000] leading-none">{{ $program->participants_count }}</p>
                        <p class="text-[9px] font-black uppercase text-gray-400 italic tracking-widest mt-1">Total Peserta</p>
                    </div>
                </div>

                {{-- Aksesoris Background --}}
                <div class="absolute -right-20 -top-20 w-64 h-64 bg-[#800000]/5 rounded-full blur-3xl"></div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

                {{-- KIRI: CONTENT MANAGEMENT (LIVEWIRE) --}}
                <div class="lg:col-span-8 space-y-8">
                    {{-- Komponen Livewire yang kita buat sebelumnya --}}
                    @livewire('admin-program.programkhusus.manage-content', ['program' => $program])
                </div>

                {{-- KANAN: QUICK ACTIONS & INFO --}}
                <div class="lg:col-span-4 space-y-8">

                    {{-- CARD: STATUS PROGRAM --}}
                    <div class="bg-white p-8 rounded-[3rem] shadow-xl border border-gray-100">
                        <h3 class="text-xs font-black uppercase italic tracking-widest mb-6 border-b border-gray-50 pb-4">Program Status</h3>

                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-[10px] font-bold text-gray-400 uppercase italic">Visibility</span>
                                <span class="px-3 py-1 {{ $program->is_active ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }} text-[9px] font-black uppercase italic rounded-lg">
                                    {{ $program->is_active ? 'Publicly Active' : 'Maintenance' }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-[10px] font-bold text-gray-400 uppercase italic">Quota Max</span>
                                <span class="text-xs font-black italic">{{ $program->max_quota }} Persons</span>
                            </div>
                            <div class="flex justify-between items-center border-t border-gray-50 pt-4">
                                <span class="text-[10px] font-bold text-gray-400 uppercase italic">Start Date</span>
                                <span class="text-xs font-black italic text-slate-700">{{ $program->start_at ? $program->start_at->format('d M Y') : 'Not Set' }}</span>
                            </div>
                        </div>


                    </div>

                    {{-- CARD: QUICK LINKS --}}
                    <div class="bg-black p-8 rounded-[3rem] shadow-2xl shadow-[#800000]/20 relative overflow-hidden">
                        <h3 class="text-xs font-black uppercase italic tracking-widest mb-6 text-white/50 border-b border-white/10 pb-4">Internal Links</h3>

                        <div class="space-y-3">
                            <a href="#" class="flex items-center justify-between p-4 bg-white/5 hover:bg-white/10 rounded-2xl transition-all group">
                                <span class="text-[10px] font-bold text-white uppercase italic">Daftar Peserta</span>
                                <svg class="w-4 h-4 text-[#800000] group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 8l4 4m0 0l-4 4m4-4H3" stroke-width="2.5"/></svg>
                            </a>
                            <a href="#" class="flex items-center justify-between p-4 bg-white/5 hover:bg-white/10 rounded-2xl transition-all group">
                                <span class="text-[10px] font-bold text-white uppercase italic">Export Data (CSV)</span>
                                <svg class="w-4 h-4 text-[#800000] group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 8l4 4m0 0l-4 4m4-4H3" stroke-width="2.5"/></svg>
                            </a>
                        </div>

                        {{-- Dekorasi logo transparan di bg --}}
                        <div class="absolute -bottom-10 -right-10 opacity-10">
                            <svg class="w-40 h-40 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>



    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
    {{-- Card Management Keuangan --}}
    <div class="bg-white p-8 rounded-[3.5rem] shadow-xl border border-gray-100 relative overflow-hidden group hover:shadow-2xl transition-all duration-500">
        {{-- Background Decoration --}}
        <div class="absolute -right-12 -top-12 w-40 h-40 bg-[#800000]/5 rounded-full group-hover:scale-110 transition-transform duration-700"></div>
        <div class="absolute right-10 top-10 text-4xl opacity-20 group-hover:opacity-100 transition-opacity">💰</div>

        <div class="relative z-10">
            <span class="px-4 py-1.5 bg-[#800000] text-[8px] font-black uppercase text-white rounded-xl italic tracking-widest">
                Finance Module
            </span>

            <h3 class="text-2xl font-black uppercase italic tracking-tighter text-slate-800 mt-6 leading-none">
                Budget <br> <span class="text-[#800000]">Management</span>
            </h3>

            <p class="text-[9px] font-bold text-gray-400 uppercase italic tracking-widest mt-4 max-w-[180px]">
                Atur periode RAB, tentukan plafon anggaran, dan buat template custom.
            </p>

            <div class="mt-10 flex items-center gap-3">
                {{-- Tombol Lihat Semua --}}
                <a href="{{ route('admin.program.keuangan.index') }}"
                   class="flex-1 bg-black text-white py-4 rounded-2xl text-[9px] font-black uppercase italic text-center hover:bg-[#800000] transition-all shadow-lg">
                    Manage Program —
                </a>

                {{-- Shortcut Buat Baru --}}
                <a href="{{ route('admin.program.keuangan.create') }}"
                   class="w-12 h-12 flex items-center justify-center bg-gray-50 text-slate-800 rounded-2xl hover:bg-black hover:text-white transition-all border border-gray-100 shadow-sm"
                   title="Create New Period">
                    <span class="text-xl font-light">+</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Info Stats Cepat (Opsional) --}}
    <div class="lg:col-span-2 bg-slate-900 p-8 rounded-[3.5rem] shadow-xl relative overflow-hidden flex flex-col justify-between">
        <div class="flex justify-between items-start">
            <div>
                <h4 class="text-white text-xs font-black uppercase italic tracking-[0.2em]">Financial Pulse</h4>
                @php
                    $totalPlafon = \App\Models\RabPeriod::where('is_active', true)->sum('max_total_budget');
                    $activeCount = \App\Models\RabPeriod::where('is_active', true)->where('end_at', '>', now())->count();
                @endphp
            </div>
            <span class="text-[10px] font-black text-emerald-400 uppercase italic tracking-widest bg-emerald-400/10 px-3 py-1 rounded-lg">
                {{ $activeCount }} Active Periods
            </span>
        </div>

        <div class="mt-8">
            <p class="text-[9px] font-bold text-slate-500 uppercase italic">Total Active Budget Plafon:</p>
            <h2 class="text-5xl font-black text-white italic tracking-tighter mt-1">
                <span class="text-slate-600 text-2xl uppercase">Rp</span> {{ number_format($totalPlafon, 0, ',', '.') }}
            </h2>
        </div>

        <div class="mt-8 flex gap-8 border-t border-slate-800 pt-6">
            <div>
                <p class="text-[8px] font-black text-slate-500 uppercase">Incoming Proposals</p>
                <p class="text-lg font-black text-white italic">{{ \App\Models\RabSubmission::where('status', 'pending')->count() }} <span class="text-[10px] text-amber-500">Wait</span></p>
            </div>
            <div>
                <p class="text-[8px] font-black text-slate-500 uppercase">Total Approved</p>
                <p class="text-lg font-black text-white italic">Rp {{ number_format(\App\Models\RabSubmission::where('status', 'approved')->sum('total_approved'), 0, ',', '.') }}</p>
            </div>
        </div>
    </div>
</div>
@endcomponent
