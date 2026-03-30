<?php

use App\Models\SubProgram;
use Livewire\Volt\Component;
use Carbon\Carbon;

new class extends Component {
    public SubProgram $subProgram;
    public $meetingLink = null;
    public $statusLabel = 'Scheduled';

    public function mount(SubProgram $subProgram) {
        $this->subProgram = $subProgram->load(['program', 'template']);
        $now = Carbon::now();

        // Deteksi Link Meeting
        $data = $this->subProgram->content_data;
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if (preg_match('/(link|meet|zoom|url)/i', $key) && filter_var($value, FILTER_VALIDATE_URL)) {
                    $this->meetingLink = $value;
                    break;
                }
            }
        }

        // Logic Status & Label
        $deadline = $this->subProgram->deadline ? Carbon::parse($this->subProgram->deadline) : null;

        if ($deadline) {
            if ($now->gt($deadline->copy()->addDay())) {
                $this->statusLabel = 'Selesai';
            } elseif ($deadline->isToday()) {
                $this->statusLabel = 'Berlangsung';
            } else {
                $this->statusLabel = 'Terjadwal';
            }
        }
    }
}; ?>

<div class="max-w-7xl mx-auto px-4 py-8 lg:py-12 antialiased">
    @php
        // 1. DATA LOGIC (AS IS)
        $deadline = $subProgram->deadline ? \Carbon\Carbon::parse($subProgram->deadline) : null;
        $now = \Carbon\Carbon::now();
        $isExpired = $deadline && $now->gt($deadline->copy()->addDay());
        $isLive = $deadline && $deadline->isToday() && $now->between($deadline->copy()->subMinutes(30), $deadline->copy()->addHours(3));

        $statusDisplay = $statusLabel ?? 'Scheduled';
        if ($isExpired) { $statusDisplay = 'Closed/Archive'; }
        elseif ($isLive) { $statusDisplay = 'Live Now'; }

        $bannerImage = collect($subProgram->content_data)->first(fn($v, $k) =>
            preg_match('/(image|banner|thumbnail|foto)/i', $k) && !empty($v)
        );

        $finalBannerUrl = null;
        if ($bannerImage) {
            $finalBannerUrl = str_starts_with($bannerImage, 'http') ? $bannerImage : Storage::url($bannerImage);
        }
    @endphp

    {{-- HEADER SECTION: BANNER & IDENTITY --}}
    <div class="relative mb-12 group">
        <div class="bg-white rounded-[3rem] border border-gray-100 shadow-2xl overflow-hidden flex flex-col lg:flex-row min-h-[480px]">

            {{-- Left: Visual Banner --}}
            <div class="w-full lg:w-1/2 relative bg-gray-900 overflow-hidden">
                @if($finalBannerUrl)
                    <img src="{{ $finalBannerUrl }}" class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110 opacity-80 group-hover:opacity-100">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                @else
                    <div class="w-full h-full flex flex-col items-center justify-center bg-black">
                        <span class="text-[9px] font-black text-gray-700 uppercase italic tracking-[1em]">Environment</span>
                    </div>
                @endif

                {{-- Status Floating Badge --}}
                <div class="absolute top-8 left-8 z-20">
                    <span class="inline-block {{ $isLive ? 'bg-red-600 animate-pulse' : ($isExpired ? 'bg-gray-600' : 'bg-[#800000]') }} text-white text-[9px] font-black px-5 py-2.5 rounded-2xl uppercase italic tracking-widest shadow-2xl">
                        {{ $statusDisplay }}
                    </span>
                </div>
            </div>

            {{-- Right: Identity Content --}}
            <div class="w-full lg:w-1/2 p-8 lg:p-16 flex flex-col justify-center bg-white relative">
                <div class="flex items-center gap-3 mb-6">
                    <div class="h-1 w-10 bg-[#800000]"></div>
                    <p class="text-[11px] font-black text-[#800000] uppercase tracking-[0.4em] italic">{{ $subProgram->program->name }}</p>
                </div>

                <h1 class="text-4xl lg:text-6xl font-black text-gray-900 uppercase italic tracking-tighter leading-[0.9] mb-8">
                    {{ $subProgram->title }}
                </h1>

                <div class="grid grid-cols-2 gap-8 border-y border-gray-50 py-8 mb-10">
                    <div>
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 italic">Pelaksanaan</p>
                        <p class="text-sm font-black text-gray-800 italic uppercase">
                        {{ $subProgram->deadline ? \Carbon\Carbon::parse($subProgram->deadline)->format('M d, Y') : 'No Date Set' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 italic">Resource Type</p>
                        <p class="text-sm font-black text-gray-800 italic uppercase">{{ $subProgram->template->name ?? 'Material' }}</p>
                    </div>
                </div>

                @if($meetingLink)
                    <a href="{{ $meetingLink }}" target="_blank"
                       class="w-full lg:w-max flex items-center justify-center gap-4 bg-black text-white px-12 py-5 rounded-[2rem] font-black text-[10px] uppercase tracking-[0.2em] italic hover:bg-[#800000] hover:-translate-y-1 transition-all shadow-xl shadow-black/10">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                        </span>
                        Masuk Ruang Virtual
                    </a>
                @endif
            </div>
        </div>
    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-12">
    @foreach($subProgram->absensis as $absen)
        @if($absen->is_active)
            <livewire:user.absensi.submit :absensi="$absen" :key="'absen-'.$absen->id" />
        @endif
    @endforeach
</div>
    {{-- MAIN CONTENT GRID --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">

        {{-- LEFT COLUMN: CONTENT DATA --}}
        <div class="lg:col-span-8 space-y-6">
            @foreach($subProgram->content_data as $key => $value)
                @if(!empty($value) &&
                    !preg_match('/(image|banner|thumbnail|foto)/i', $key) &&
                    !filter_var($value, FILTER_VALIDATE_URL))

                    <div class="bg-white rounded-[2.5rem] p-10 border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-500 group">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-2 h-8 bg-black group-hover:bg-[#800000] transition-colors"></div>
                            <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] italic">{{ str_replace('_', ' ', $key) }}</h4>
                        </div>
                        <div class="prose prose-sm max-w-none text-gray-600 font-medium leading-relaxed italic text-lg">
                            {!! nl2br(e($value)) !!}
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        {{-- RIGHT COLUMN: ASSETS & EXAMS --}}
        <div class="lg:col-span-4 space-y-8">

            {{-- Learning Assets Section --}}
            <div class="bg-gray-50/50 rounded-[3rem] p-8 border border-gray-100 shadow-inner">
                <h5 class="text-[10px] font-black text-black uppercase tracking-[0.4em] mb-8 px-2 italic flex items-center gap-3">
                    <svg class="w-4 h-4 text-[#800000]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Learning Assets
                </h5>

                <div class="space-y-4">
                    @foreach($subProgram->content_data as $key => $value)
                        @if(!empty($value) && (str_contains($value, 'uploads/') || filter_var($value, FILTER_VALIDATE_URL)))
                            @php
                                $isUrl = filter_var($value, FILTER_VALIDATE_URL);
                                $isMeeting = $isUrl && (str_contains($value, 'zoom') || str_contains($value, 'meet'));
                            @endphp

                            @if($isMeeting) @continue @endif

                            <div class="bg-white p-5 rounded-[2rem] border border-gray-100 group transition-all hover:border-[#800000]/30 shadow-sm">
                                <div class="flex items-center gap-4 mb-4">
                                    <div class="w-12 h-12 flex items-center justify-center rounded-2xl {{ $isUrl ? 'bg-blue-50 text-blue-600' : 'bg-green-50 text-green-600' }}">
                                        @if($isUrl)
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101" stroke-width="2.5"/></svg>
                                        @else
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-width="2.5"/></svg>
                                        @endif
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-[8px] font-black text-gray-300 uppercase tracking-widest leading-none mb-1">{{ $isUrl ? 'Resource Link' : 'Attachment' }}</p>
                                        <p class="text-[11px] font-black text-gray-800 uppercase italic truncate tracking-tighter">{{ str_replace('_', ' ', $key) }}</p>
                                    </div>
                                </div>
                                <a href="{{ $isUrl ? $value : asset('storage/' . $value) }}" {{ $isUrl ? 'target="_blank"' : 'download' }}
                                   class="block w-full text-center py-3 bg-gray-50 rounded-2xl text-[9px] font-black uppercase tracking-widest text-gray-500 hover:bg-[#800000] hover:text-white transition-all duration-300">
                                    {{ $isUrl ? 'Open Link' : 'Download File' }}
                                </a>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            {{-- Examinations Section --}}
            <div class="space-y-4">
                <div class="flex items-center justify-between px-4">
                    <h3 class="text-[11px] font-black uppercase tracking-[0.4em] text-[#800000] italic">Examinations</h3>
                    <span class="text-[8px] font-bold text-gray-300 uppercase tracking-widest italic">{{ count($subProgram->modulUjians) }} Modules</span>
                </div>

                @forelse($subProgram->modulUjians as $ujian)
                    @php
                        $jawaban = $ujian->jawaban()->where('user_id', auth()->id())->first();
                        $isSubmitted = $jawaban ? true : false;
                        $isGraded = $jawaban && !is_null($jawaban->nilai);
                        $isClosed = $ujian->isExpired();
                    @endphp

                    <div class="bg-white p-6 rounded-[2.5rem] border border-gray-100 shadow-sm group hover:shadow-2xl hover:border-black transition-all duration-500">
                        <div class="flex justify-between items-center mb-4">
                            <div class="flex gap-2">
                                <span class="px-3 py-1 bg-gray-50 border border-gray-100 rounded-lg text-[8px] font-black uppercase tracking-wider italic text-gray-400 group-hover:bg-black group-hover:text-white transition-colors">
                                    {{ $ujian->tipe_ujian }}
                                </span>
                                @if($isGraded)
                                    <span class="px-3 py-1 bg-green-50 text-green-600 rounded-lg text-[8px] font-black uppercase italic tracking-wider border border-green-100">Graded</span>
                                @elseif($isSubmitted)
                                    <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-lg text-[8px] font-black uppercase italic tracking-wider border border-blue-100">Submitted</span>
                                @endif
                            </div>
                            <span class="flex items-center gap-1.5 text-[8px] font-black {{ $isClosed ? 'text-red-500' : 'text-green-500' }} uppercase italic tracking-widest">
                                <span class="w-1.5 h-1.5 {{ $isClosed ? 'bg-red-500 animate-pulse' : 'bg-green-500' }} rounded-full"></span>
                                {{ $isClosed ? 'Closed' : 'Active' }}
                            </span>
                        </div>

                        <div class="mb-6 px-1">
                            <h4 class="text-base font-black uppercase italic leading-tight text-gray-900 mb-2 group-hover:text-[#800000] transition-colors">
                                {{ $ujian->judul }}
                            </h4>
                            <div class="flex items-center gap-3 text-[10px]">
                                <span class="font-bold text-gray-400 uppercase italic">{{ count($ujian->konfigurasi_soal) }} Questions</span>
                                @if($isGraded)
                                    <span class="w-1.5 h-1.5 bg-gray-100 rounded-full"></span>
                                    <span class="font-black text-[#800000] uppercase italic">Score: {{ $jawaban->nilai }}</span>
                                @endif
                            </div>
                        </div>

                        <a href="{{ route('user.ujian.show', $ujian->id) }}" wire:navigate
                           class="flex items-center justify-center gap-3 w-full py-4 rounded-2xl text-[9px] font-black uppercase tracking-[0.2em] italic transition-all duration-300
                           {{ $isClosed || $isSubmitted ? 'bg-gray-50 text-gray-400 hover:bg-black hover:text-white' : 'bg-[#800000] text-white shadow-xl shadow-[#800000]/20 hover:bg-black' }}">
                            <span>{{ $isGraded ? 'View Result' : ($isSubmitted ? 'Review Work' : ($isClosed ? 'Times Up' : 'Start Now')) }}</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                        </a>
                    </div>
                @empty
                    <div class="py-16 border-2 border-dashed border-gray-100 rounded-[3rem] text-center bg-gray-50/30">
                        <p class="text-[10px] font-black text-gray-300 uppercase italic tracking-[0.4em]">No Modules Available</p>
                    </div>
                @endforelse
            </div>

        </div>


    </div>
</div>
