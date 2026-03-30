<?php

use App\Models\{Program, User, SubProgram};
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public Program $program;
    public $search = '';

    public function mount(Program $program)
    {
        // Load Induk Program beserta semua anak-anaknya (Sub-Program)
        $this->program = $program->load(['subPrograms.absensis', 'subPrograms.contents', 'users']);
    }

    public function with()
    {
        $users = $this->program->users()
            ->where('name', 'like', '%' . $this->search . '%')
            ->paginate(15);

        return [
            'participants' => $users,
            'subPrograms' => $this->program->subPrograms
        ];
    }
}; ?>

<div class="max-w-7xl mx-auto pb-24 antialiased px-6 lg:px-8">
    {{-- 1. CLEAN HEADER --}}
    <div class="mb-12 flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <div class="flex items-center gap-2 mb-3">
                <span class="w-10 h-[2px] bg-[#800000]"></span>
                <p class="text-[10px] font-black text-[#800000] uppercase tracking-[0.4em] italic">Master Analytics Dashboard</p>
            </div>
            <h1 class="text-4xl lg:text-5xl font-black text-slate-900 uppercase italic tracking-tighter leading-none">
                {{ $program->title }} <span class="text-slate-400">Tracker</span>
            </h1>
        </div>

        {{-- Global Search - Moved to Header for better flow --}}
        <div class="relative group">
            <input wire:model.live="search" type="text" placeholder="Search participant name..."
                class="bg-white border border-slate-200 rounded-2xl px-6 py-4 text-[10px] text-slate-900 font-bold uppercase tracking-widest focus:ring-4 focus:ring-[#800000]/5 focus:border-[#800000] w-full md:w-80 transition-all shadow-sm placeholder:text-slate-300">
            <div class="absolute right-5 top-1/2 -translate-y-1/2 text-slate-300 group-hover:text-[#800000] transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
        </div>
    </div>

    {{-- 2. HORIZONTAL STATS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-12">
        @foreach($subPrograms as $sub)
            <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-md transition-all flex flex-col justify-between min-h-[140px]">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[8px] font-black text-slate-300 uppercase tracking-widest italic">Phase {{ $loop->iteration }}</span>
                        <div class="w-2 h-2 rounded-full bg-[#800000]/20"></div>
                    </div>
                    <h4 class="text-sm font-black text-slate-800 uppercase italic leading-tight line-clamp-2">{{ $sub->title }}</h4>
                </div>

                <div class="flex items-center gap-4 pt-4 border-t border-slate-50">
                    <div class="flex flex-col">
                        <span class="text-[7px] font-black text-slate-400 uppercase tracking-tighter">Contents</span>
                        <span class="text-xs font-black italic text-slate-700">{{ $sub->contents->count() }}</span>
                    </div>
                    <div class="w-[1px] h-4 bg-slate-100"></div>
                    <div class="flex flex-col">
                        <span class="text-[7px] font-black text-slate-400 uppercase tracking-tighter">Attendance</span>
                        <span class="text-xs font-black italic text-slate-700">{{ $sub->absensis->count() }}</span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- 3. GLOBAL MATRIX TABLE --}}
    <div class="bg-white rounded-[3rem] border border-slate-100 shadow-xl shadow-slate-200/50 overflow-hidden">
        {{-- Table Header Info --}}
        <div class="px-10 py-8 border-b border-slate-50 flex items-center justify-between bg-slate-50/30">
            <h3 class="text-sm font-black text-slate-800 uppercase italic tracking-widest">Participant Attendance Matrix</h3>
            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Showing {{ $participants->count() }} Results</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-[8px] font-black uppercase text-slate-400 tracking-[0.2em] italic bg-white">
                        <th class="py-6 px-10 border-b border-slate-50">Identity</th>
                        @foreach($subPrograms as $sub)
                            <th class="py-6 px-4 text-center border-b border-slate-50">
                                <span class="bg-slate-50 px-3 py-1 rounded-full">{{ Str::limit($sub->title, 12) }}</span>
                            </th>
                        @endforeach
                        <th class="py-6 px-10 text-right border-b border-slate-50">Avg</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($participants as $user)
                        <tr class="hover:bg-slate-50/80 transition-all group">
                            <td class="py-6 px-10">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center font-black text-slate-400 text-xs uppercase italic group-hover:bg-[#800000] group-hover:text-white transition-all">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-[11px] font-black text-slate-800 uppercase italic leading-none mb-1">{{ $user->name }}</p>
                                        <p class="text-[9px] font-bold text-slate-400 tracking-tighter">{{ $user->nim ?? 'No Identity Number' }}</p>
                                    </div>
                                </div>
                            </td>



                            @php $globalSum = 0; @endphp
                            @foreach($subPrograms as $sub)
                                <td class="py-6 px-4 text-center">
                                    @php
                                        $totalSubAbsen = $sub->absensis->count();
                                        $userHadir = $sub->absensis->map(fn($a) => $a->kehadirans->where('user_id', $user->id)->count())->sum();
                                        $rate = $totalSubAbsen > 0 ? round(($userHadir / $totalSubAbsen) * 100) : 0;
                                        $globalSum += $rate;
                                    @endphp
                                    <div class="flex flex-col items-center">
                                        <span class="text-[10px] font-black italic {{ $rate < 70 ? 'text-rose-500' : 'text-emerald-500' }}">
                                            {{ $rate }}%
                                        </span>
                                        <div class="w-10 h-1 bg-slate-100 rounded-full mt-1.5 overflow-hidden">
                                            <div class="h-full {{ $rate < 70 ? 'bg-rose-500' : 'bg-emerald-500' }}" style="width: {{ $rate }}%"></div>
                                        </div>
                                    </div>
                                </td>
                            @endforeach

                            <td class="py-6 px-10 text-right">
                                @php $avg = round($globalSum / max(count($subPrograms), 1)); @endphp
                                <span class="px-4 py-2 rounded-xl {{ $avg < 70 ? 'bg-rose-50 text-rose-600' : 'bg-emerald-50 text-emerald-600' }} text-[10px] font-black italic">
                                    {{ $avg }}%
                                </span>





                                <a href="{{ route('admin.program.tracking.user-detail', ['program' => $program->slug, 'user' => $user->id]) }}"
   class="group">
    <p class="text-xs font-black text-white uppercase italic group-hover:text-[#800000] transition-colors">{{ $user->name }}</p>
    <p class="text-[8px] font-bold text-gray-500 uppercase tracking-widest mt-1">{{ $user->nim }}</p>
</a>
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination Area --}}
        <div class="px-10 py-8 bg-slate-50/30 border-t border-slate-50">
            {{ $participants->links() }}
        </div>
    </div>
</div>
