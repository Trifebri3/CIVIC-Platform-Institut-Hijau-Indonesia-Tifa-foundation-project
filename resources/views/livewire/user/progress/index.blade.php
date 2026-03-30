<?php

use App\Models\{SubProgram, SubProgramContent, JawabanUjian};
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    public function with()
    {
        $user = Auth::user();

        // Ambil semua program yang diikuti user
        $programs = $user->programs()->with(['subPrograms.contents', 'subPrograms.absensis'])->get();

        $stats = $programs->map(function($program) use ($user) {
            return [
                'program_title' => $program->title,
                'sub_programs' => $program->subPrograms->map(function($sub) use ($user) {
                    $contentIds = $sub->contents->pluck('id');
                    $materiSelesai = $user->progress()->whereIn('sub_program_content_id', $contentIds)->count();
                    $totalMateri = $contentIds->count();

                    $totalAbsen = $sub->absensis->count();
                    $hadirCount = $sub->absensis->map(fn($a) => $a->kehadirans->where('user_id', $user->id)->count())->sum();

                    return [
                        'id' => $sub->id,
                        'title' => $sub->title,
                        'materi_percent' => $totalMateri > 0 ? round(($materiSelesai / $totalMateri) * 100) : 0,
                        'absen_percent' => $totalAbsen > 0 ? round(($hadirCount / $totalAbsen) * 100) : 0,
                        'is_complete' => ($totalMateri > 0 && $materiSelesai == $totalMateri) && ($totalAbsen > 0 && $hadirCount == $totalAbsen),
                        'uncompleted_contents' => $sub->contents->filter(function($c) use ($user) {
                            return !$user->progress->contains($c->id);
                        })
                    ];
                }),
   'avg_score' => $user->jawaban_ujians()
    ->whereHas('ujian', function($q) use ($program) {
        $q->whereIn('sub_program_id', $program->subPrograms->pluck('id'));
    })
    ->avg('nilai') ?? 0 // <--- Ganti 'score' jadi 'nilai' sesuai hasil Tinker tadi
            ];
        });

        return [
            'stats' => $stats
        ];
    }
}; ?>

<div class="max-w-5xl mx-auto py-10 px-6 antialiased">
    {{-- Welcome Header --}}
    <div class="flex justify-between items-end mb-12">
        <div>
            <h1 class="text-4xl font-black uppercase italic tracking-tighter">My Learning <span class="text-[#800000]">Journey</span></h1>
            <p class="text-xs font-bold text-gray-400 mt-2 uppercase tracking-widest">Pantau progres belajar dan capaian nilaimu di sini.</p>
        </div>
        <a href="{{ route('user.progress.pdf') }}" class="flex items-center gap-3 bg-black text-white px-6 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-[#800000] transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-width="2.5"/></svg>
            Download E-Raport (PDF)
        </a>
    </div>

    @foreach($stats as $programStat)
    <div class="mb-16">
        <div class="flex items-center gap-4 mb-6">
            <h2 class="text-lg font-black uppercase italic text-gray-800">{{ $programStat['program_title'] }}</h2>
            <div class="h-px flex-1 bg-gray-100"></div>
            <span class="text-[10px] font-bold py-1 px-3 bg-gray-50 border border-gray-200 rounded-full uppercase">Avg Score: {{ round($programStat['avg_score']) }}</span>
        </div>

        <div class="grid gap-4">
            @foreach($programStat['sub_programs'] as $sub)
            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm hover:border-[#800000]/30 transition-all">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h3 class="font-bold text-slate-900 uppercase tracking-tight">{{ $sub['title'] }}</h3>
                        <p class="text-[9px] font-bold text-gray-400 uppercase mt-1">Status: {{ $sub['is_complete'] ? 'Completed' : 'On Progress' }}</p>
                    </div>
                    @if($sub['is_complete'])
                        <span class="bg-green-50 text-green-600 text-[8px] font-black px-2 py-1 rounded uppercase border border-green-100 italic">Selesai</span>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    {{-- Progress Bar Materi --}}
                    <div>
                        <div class="flex justify-between text-[9px] font-black uppercase mb-2 italic">
                            <span class="text-gray-400">Materi Terbaca</span>
                            <span>{{ $sub['materi_percent'] }}%</span>
                        </div>
                        <div class="h-1.5 w-full bg-gray-50 rounded-full overflow-hidden">
                            <div class="h-full bg-black rounded-full transition-all duration-1000" style="width: {{ $sub['materi_percent'] }}%"></div>
                        </div>

                        {{-- List Materi Belum (Drop-down style or small list) --}}
                        @if($sub['uncompleted_contents']->count() > 0)
                            <div class="mt-4 p-3 bg-red-50/50 rounded-lg border border-red-100/50">
                                <p class="text-[8px] font-black text-red-400 uppercase mb-2 tracking-widest">Belum Diselesaikan:</p>
                                <ul class="space-y-1">
                                    @foreach($sub['uncompleted_contents']->take(3) as $uc)
                                        <li class="text-[10px] font-bold text-red-700 flex items-center gap-2 italic">
                                            <div class="w-1 h-1 bg-red-400 rounded-full"></div>
                                            {{ $uc->title }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>

                    {{-- Progress Bar Absen --}}
                    <div>
                        <div class="flex justify-between text-[9px] font-black uppercase mb-2 italic">
                            <span class="text-gray-400">Kehadiran Sesi</span>
                            <span class="{{ $sub['absen_percent'] < 75 ? 'text-[#800000]' : '' }}">{{ $sub['absen_percent'] }}%</span>
                        </div>
                        <div class="h-1.5 w-full bg-gray-50 rounded-full overflow-hidden">
                            <div class="h-full bg-[#800000] rounded-full transition-all duration-1000" style="width: {{ $sub['absen_percent'] }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
</div>
