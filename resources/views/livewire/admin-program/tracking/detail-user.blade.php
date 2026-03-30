<?php

use App\Models\{Program, User, SubProgram};
use Livewire\Volt\Component;

new class extends Component {
    public Program $program;
    public User $user;

    public function mount(Program $program, User $user)
    {
        $this->program = $program;
        // Load relasi dengan nama kolom pivot yang sudah kita perbaiki tadi
        $this->user = $user->load(['progress', 'jawaban_ujians']);
    }

    public function with()
    {
        $subProgramStats = $this->program->subPrograms->map(function($sub) {
            $contentIds = $sub->contents->pluck('id');
            $totalMateri = $contentIds->count();

            // Hitung materi selesai berdasarkan tabel pivot user_progress
            $materiSelesai = $this->user->progress()
                ->whereIn('sub_program_content_id', $contentIds)
                ->count();

            $totalAbsen = $sub->absensis->count();
            $hadirCount = $sub->absensis->map(fn($a) => $a->kehadirans->where('user_id', $this->user->id)->count())->sum();

            return [
                'title' => $sub->title,
                'total_materi' => $totalMateri,
                'materi_selesai' => $materiSelesai,
                'materi_percent' => $totalMateri > 0 ? round(($materiSelesai / $totalMateri) * 100) : 0,
                'total_absen' => $totalAbsen,
                'hadir_count' => $hadirCount,
                'absen_percent' => $totalAbsen > 0 ? round(($hadirCount / $totalAbsen) * 100) : 0,
            ];
        });

        return [
            'subProgramStats' => $subProgramStats,
            'avgScore' => $this->user->jawaban_ujians->avg('score') ?? 0
        ];
    }
}; ?>

<div class="max-w-7xl mx-auto px-4 py-8 antialiased text-slate-900">

    {{-- Breadcrumb & Quick Actions --}}
    <div class="flex justify-between items-center mb-8 pb-4 border-b border-gray-200">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.program.tracking.global', $program->slug) }}" class="text-gray-400 hover:text-black transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M10 19l-7-7m0 0l7-7m-7 7h18" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </a>
            <div>
                <h1 class="text-xl font-bold tracking-tight uppercase">{{ $user->name }}</h1>
                <p class="text-[10px] text-gray-400 font-mono tracking-widest uppercase">{{ $user->nim ?? 'N/A' }} — {{ $user->email }}</p>
            </div>
        </div>
        <div class="flex items-center gap-6">
            <div class="text-right">
                <p class="text-[9px] font-black text-gray-400 uppercase tracking-tighter">Avg. Exam Score</p>
                <p class="text-2xl font-black {{ $avgScore < 75 ? 'text-[#800000]' : 'text-black' }}">{{ round($avgScore) }}<span class="text-xs text-gray-300">/100</span></p>
            </div>
        </div>
    </div>

    {{-- Main Performance Table --}}
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200 text-[10px] uppercase tracking-widest font-bold text-gray-500">
                    <th class="px-6 py-4">Sub-Program Title</th>
                    <th class="px-6 py-4 text-center">Learning Progress</th>
                    <th class="px-6 py-4 text-center">Attendance</th>
                    <th class="px-6 py-4 text-right">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($subProgramStats as $stat)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    {{-- Title --}}
                    <td class="px-6 py-5">
                        <div class="font-bold text-sm uppercase tracking-tight text-slate-800">{{ $stat['title'] }}</div>
                        <div class="text-[9px] text-gray-400 mt-1 uppercase">ID: {{ Str::slug($stat['title']) }}</div>
                    </td>

                    {{-- Learning Progress --}}
                    <td class="px-6 py-5">
                        <div class="flex items-center gap-4 justify-center">
                            <div class="w-32 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full bg-black rounded-full" style="width: {{ $stat['materi_percent'] }}%"></div>
                            </div>
                            <span class="text-[11px] font-mono font-bold w-10 text-right">{{ $stat['materi_percent'] }}%</span>
                        </div>
                        <p class="text-[9px] text-center text-gray-400 mt-2 uppercase font-medium">{{ $stat['materi_selesai'] }} / {{ $stat['total_materi'] }} Materials</p>
                    </td>

                    {{-- Attendance --}}
                    <td class="px-6 py-5">
                        <div class="flex items-center gap-4 justify-center">
                            <div class="w-32 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full bg-[#800000] rounded-full" style="width: {{ $stat['absen_percent'] }}%"></div>
                            </div>
                            <span class="text-[11px] font-mono font-bold w-10 text-right {{ $stat['absen_percent'] < 75 ? 'text-[#800000]' : '' }}">{{ $stat['absen_percent'] }}%</span>
                        </div>
                        <p class="text-[9px] text-center text-gray-400 mt-2 uppercase font-medium">{{ $stat['hadir_count'] }} / {{ $stat['total_absen'] }} Sessions</p>
                    </td>

                    {{-- Status Indicator --}}
                    <td class="px-6 py-5 text-right">
                        @if($stat['absen_percent'] >= 75 && $stat['materi_percent'] >= 80)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest bg-green-50 text-green-600 border border-green-100">
                                Excellent
                            </span>
                        @elseif($stat['absen_percent'] < 50)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest bg-red-50 text-red-600 border border-red-100">
                                Critical
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest bg-orange-50 text-orange-600 border border-orange-100">
                                Monitoring
                            </span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Footer Info --}}
    <div class="mt-8 flex justify-between items-center bg-gray-50 p-6 rounded-xl border border-gray-100">
        <div class="flex gap-10">
            <div>
                <p class="text-[9px] text-gray-400 font-bold uppercase tracking-widest">Total Materials Read</p>
                <p class="text-lg font-bold">{{ $subProgramStats->sum('materi_selesai') }}</p>
            </div>
            <div class="border-l border-gray-200 pl-10">
                <p class="text-[9px] text-gray-400 font-bold uppercase tracking-widest">Total Presence</p>
                <p class="text-lg font-bold">{{ $subProgramStats->sum('hadir_count') }}</p>
            </div>
        </div>
        <button class="bg-black text-white px-6 py-2.5 rounded-lg text-[10px] font-black uppercase tracking-widest hover:bg-[#800000] transition-all">
            Generate Report
        </button>
    </div>
</div>
