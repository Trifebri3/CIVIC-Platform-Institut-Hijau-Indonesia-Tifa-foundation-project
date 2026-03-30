<div class="max-w-7xl mx-auto pb-24 antialiased relative px-4">

    {{-- BREADCRUMB & TITLE --}}
    <div class="mb-12">
        <p class="text-[10px] font-black text-[#800000] uppercase tracking-[0.5em] mb-2 italic">Management System v1.0</p>
        <h1 class="text-6xl font-black text-gray-900 uppercase italic tracking-tighter leading-none">
            Program <span class="text-[#800000]">Analytics</span>
        </h1>
    </div>

    {{-- STATS CARDS (GLOBAL INDEX) --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-12">
        {{-- Total Peserta --}}
        <div class="bg-black p-8 rounded-[2.5rem] shadow-xl shadow-gray-200 relative overflow-hidden group">
            <div class="relative z-10">
                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-4">Total Students</p>
                <h3 class="text-4xl font-black text-white italic">{{ $stats['totalPeserta'] }}</h3>
            </div>
            <div class="absolute -right-4 -bottom-4 text-white/5 text-8xl font-black italic group-hover:scale-110 transition-transform">#01</div>
        </div>

        {{-- Avg Absensi --}}
        <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm relative overflow-hidden group">
            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-4">Avg Attendance</p>
            <h3 class="text-4xl font-black text-gray-900 italic">{{ $stats['avgAbsensi'] }}%</h3>
            <div class="w-full h-1.5 bg-gray-100 rounded-full mt-4 overflow-hidden">
                <div class="h-full bg-[#800000]" style="width: {{ $stats['avgAbsensi'] }}%"></div>
            </div>
        </div>

        {{-- Total Materi --}}
        <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm">
            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-4">Course Assets</p>
            <h3 class="text-4xl font-black text-gray-900 italic">{{ $stats['totalMateri'] }} <span class="text-xs uppercase not-italic text-gray-300">Moduls</span></h3>
        </div>

        {{-- Total Pertemuan --}}
        <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm">
            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-4">Sessions</p>
            <h3 class="text-4xl font-black text-gray-900 italic">{{ $stats['totalPertemuan'] }} <span class="text-xs uppercase not-italic text-gray-300">Slots</span></h3>
        </div>
    </div>

    {{-- SEARCH & FILTER --}}
    <div class="flex justify-between items-center mb-6 px-4">
        <h3 class="text-[11px] font-black uppercase italic tracking-[0.2em] text-gray-400">Detailed Student List</h3>
        <input wire:model.live="search" type="text" placeholder="FILTER BY NAME..."
            class="bg-gray-100 border-none rounded-2xl px-6 py-3 text-[10px] font-black uppercase tracking-widest focus:ring-2 focus:ring-[#800000] w-72 transition-all">
    </div>

    {{-- TABLE SECTION --}}
    <div class="bg-white rounded-[3rem] shadow-2xl shadow-gray-200/50 border border-gray-50 overflow-hidden">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-gray-50/50 border-b border-gray-100">
                    <th class="px-10 py-8 text-[10px] font-black uppercase text-gray-400 tracking-widest italic">Student</th>
                    <th class="px-6 py-8 text-[10px] font-black uppercase text-gray-400 tracking-widest italic text-center">Absensi</th>
                    <th class="px-6 py-8 text-[10px] font-black uppercase text-gray-400 tracking-widest italic text-center">Materi</th>
                    <th class="px-10 py-8 text-[10px] font-black uppercase text-gray-400 tracking-widest italic text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($participants as $user)
                    @php
                        $hadirCount = $subProgram->absensis->map(fn($a) => $a->kehadirans->where('user_id', $user->id)->count())->sum();
                        $absenRate = $stats['totalPertemuan'] > 0 ? round(($hadirCount / $stats['totalPertemuan']) * 100) : 0;
                    @endphp
                    <tr class="hover:bg-gray-50/80 transition-all group">
                        <td class="px-10 py-6">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center text-gray-400 font-black italic group-hover:bg-[#800000] group-hover:text-white transition-colors">
                                    {{ substr($user->name, 0, 2) }}
                                </div>
                                <div>
                                    <p class="text-xs font-black text-gray-800 uppercase italic">{{ $user->name }}</p>
                                    <p class="text-[8px] font-bold text-gray-400 uppercase tracking-tighter">{{ $user->nim ?? 'NO-NIM' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-6 text-center">
                            <span class="text-sm font-black italic {{ $absenRate < 70 ? 'text-red-500' : 'text-gray-900' }}">{{ $absenRate }}%</span>
                        </td>
                        <td class="px-6 py-6 text-center">
                             <span class="text-[9px] font-black px-3 py-1 bg-gray-100 rounded-full text-gray-500 italic uppercase">Tracked</span>
                        </td>
                        <td class="px-10 py-6 text-right">
                            <button wire:click="showUser({{ $user->id }})" class="text-[9px] font-black uppercase italic tracking-widest text-[#800000] hover:underline">
                                View Detail →
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-8 border-t border-gray-50">
            {{ $participants->links() }}
        </div>
    </div>

    {{-- PANEL DETAIL (SIDE MODAL) --}}
    @if($selectedUser)
        @include('livewire.admin-program.tracking.partials.detail-panel')
    @endif
</div>
