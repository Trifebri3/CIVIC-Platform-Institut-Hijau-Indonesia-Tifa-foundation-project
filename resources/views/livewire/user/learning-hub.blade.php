<?php

use Livewire\Volt\Component;
use Carbon\Carbon;
use App\Models\Absensi;
use App\Models\ModulUjian;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    public $currentMonth, $currentYear, $daysInMonth = [], $selectedDay;
    public $filter = 'semua'; // Penting: Ditambahkan agar tidak error undefined property

    public function mount() {
        $this->currentMonth = date('m');
        $this->currentYear = date('Y');
        $this->selectedDay = (int)date('d');
        $this->generateCalendar();
    }

    public function generateCalendar() {
        $date = Carbon::createFromDate($this->currentYear, $this->currentMonth, 1);
        $days = [];
        for ($i = 0; $i < $date->dayOfWeek; $i++) { $days[] = null; }
        for ($i = 1; $i <= $date->daysInMonth; $i++) { $days[] = $i; }
        $this->daysInMonth = $days;
    }

    public function getDayActivity($day) {
        $date = Carbon::create($this->currentYear, $this->currentMonth, $day)->format('Y-m-d');
        $hasKelas = Absensi::whereDate('open_at', $date)->exists();
        $hasTugas = ModulUjian::whereDate('deadline', $date)->exists();

        if ($hasKelas && $hasTugas) return 'both';
        if ($hasKelas) return 'kelas';
        if ($hasTugas) return 'tugas';
        return null;
    }

    public function getSchedulesProperty() {
        $dateString = Carbon::create($this->currentYear, $this->currentMonth, $this->selectedDay)->format('Y-m-d');
        $items = collect();

        // 1. Ambil Kelas
        if ($this->filter == 'semua' || $this->filter == 'kelas') {
            $kelas = Absensi::whereDate('open_at', $dateString)
                ->get()
                ->map(fn($item) => [
                    'type' => 'kelas',
                    'title' => $item->title,
                    'time' => Carbon::parse($item->open_at)->format('H:i'),
                    'info' => "Tipe: " . strtoupper($item->type) . " • " . ($item->duration_minutes ?? '60') . " Menit",
                    'status' => $item->is_active ? 'Active' : 'Closed',
                ]);
            $items = $items->concat($kelas);
        }

        // 2. Ambil Tugas
        if ($this->filter == 'semua' || $this->filter == 'tugas') {
            $tugas = ModulUjian::whereDate('deadline', $dateString)
                ->get()
                ->map(fn($item) => [
                    'type' => 'tugas',
                    'title' => $item->judul,
                    'time' => 'DL: ' . Carbon::parse($item->deadline)->format('H:i'),
                    'info' => strtoupper(str_replace('_', ' ', $item->tipe_ujian)),
                    'status' => 'Submit',
                ]);
            $items = $items->concat($tugas);
        }

        return $items->sortBy('time');
    }

    public function selectDay($day) { $this->selectedDay = $day; }

    public function nextMonth() {
        $date = Carbon::create($this->currentYear, $this->currentMonth, 1)->addMonth();
        $this->currentMonth = $date->month; $this->currentYear = $date->year;
        $this->generateCalendar();
    }

    public function prevMonth() {
        $date = Carbon::create($this->currentYear, $this->currentMonth, 1)->subMonth();
        $this->currentMonth = $date->month; $this->currentYear = $date->year;
        $this->generateCalendar();
    }
}; ?>

<div class="p-6 lg:p-10 grid grid-cols-1 lg:grid-cols-12 gap-10 bg-[#fafafa] min-h-screen">

    {{-- SISI KIRI: KALENDER & STATS --}}
    <div class="lg:col-span-4 space-y-8">
        <div class="bg-white p-8 rounded-[3rem] border border-gray-100 shadow-xl shadow-gray-200/40">
            <div class="flex justify-between items-center mb-10">
                <button wire:click="prevMonth" class="p-3 hover:bg-gray-50 rounded-2xl transition-all border border-gray-50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <div class="text-center">
                    <h2 class="text-xs font-black uppercase tracking-[0.2em] text-[#800000]">
                        {{ Carbon::create($currentYear, $currentMonth)->format('F') }}
                    </h2>
                    <p class="text-[10px] font-bold text-gray-300">{{ $currentYear }}</p>
                </div>
                <button wire:click="nextMonth" class="p-3 hover:bg-gray-50 rounded-2xl transition-all border border-gray-50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>

            <div class="grid grid-cols-7 gap-3 mb-6">
                @foreach(['S','M','T','W','T','F','S'] as $dayName)
                    <div class="text-[9px] font-black uppercase text-gray-300 text-center tracking-tighter">{{ $dayName }}</div>
                @endforeach
            </div>

            <div class="grid grid-cols-7 gap-3">
                @foreach($daysInMonth as $day)
                    @if($day)
                        @php $activity = $this->getDayActivity($day); @endphp
                        <button wire:click="selectDay({{ $day }})"
                                class="relative h-12 w-full flex flex-col items-center justify-center rounded-2xl text-[11px] font-black transition-all
                                {{ $selectedDay == $day ? 'bg-[#800000] text-white shadow-lg shadow-red-900/20 rotate-3 scale-110' : 'hover:bg-gray-50 text-slate-700' }}">
                            {{ $day }}

                            {{-- DOT INDICATORS --}}
                            <div class="flex gap-1 mt-1">
                                @if($activity == 'kelas' || $activity == 'both')
                                    <span class="w-1 h-1 rounded-full {{ $selectedDay == $day ? 'bg-white' : 'bg-[#800000]' }}"></span>
                                @endif
                                @if($activity == 'tugas' || $activity == 'both')
                                    <span class="w-1 h-1 rounded-full {{ $selectedDay == $day ? 'bg-red-300' : 'bg-black' }}"></span>
                                @endif
                            </div>
                        </button>
                    @else
                        <div class="h-12 w-full"></div>
                    @endif
                @endforeach
            </div>

            <div class="mt-8 pt-6 border-t border-gray-50 flex justify-center gap-6">
                <div class="flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#800000]"></span>
                    <span class="text-[8px] font-black uppercase text-gray-400 italic">Kelas</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-black"></span>
                    <span class="text-[8px] font-black uppercase text-gray-400 italic">Tugas</span>
                </div>
            </div>
        </div>
    </div>

    {{-- SISI KANAN: LIST JADWAL --}}
    <div class="lg:col-span-8 space-y-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-3xl font-black uppercase italic tracking-tighter text-slate-800">
                    Schedule <span class="text-gray-300">/ {{ $selectedDay }} {{ Carbon::create($currentYear, $currentMonth)->format('M') }}</span>
                </h2>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Manage your academic activities</p>
            </div>

            <div class="flex bg-white p-1.5 rounded-[1.5rem] shadow-sm border border-gray-100">
                @foreach(['semua', 'kelas', 'tugas'] as $f)
                    <button wire:click="$set('filter', '{{ $f }}')"
                            class="px-6 py-2.5 rounded-2xl text-[9px] font-black uppercase transition-all {{ $filter == $f ? 'bg-black text-white shadow-lg' : 'text-gray-400 hover:text-slate-600' }}">
                        {{ $f }}
                    </button>
                @endforeach
            </div>
        </div>

        <div class="space-y-6">
            @forelse($this->schedules as $item)
                @if($item['type'] == 'kelas')
                    <div class="group bg-white p-8 rounded-[2.5rem] border border-gray-100 flex flex-col md:flex-row md:items-center justify-between hover:border-[#800000]/30 transition-all shadow-sm">
                        <div class="flex items-center gap-8">
                            <div class="w-20 h-20 bg-gray-50 rounded-[2rem] flex flex-col items-center justify-center border border-gray-100 group-hover:bg-[#800000] group-hover:border-[#800000] transition-all">
                                <span class="text-[9px] font-black text-gray-300 group-hover:text-red-200 uppercase tracking-widest">At</span>
                                <span class="text-lg font-black italic text-[#800000] group-hover:text-white">{{ $item['time'] }}</span>
                            </div>
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="w-2 h-2 rounded-full bg-[#800000]"></span>
                                    <span class="text-[9px] font-black uppercase text-[#800000] tracking-[0.2em]">Academic Session</span>
                                </div>
                                <h4 class="text-xl font-black uppercase italic text-slate-800 tracking-tight leading-none mb-2">{{ $item['title'] }}</h4>
                                <p class="text-[11px] text-gray-400 font-bold uppercase italic">{{ $item['info'] }}</p>
                            </div>
                        </div>
                        <div class="mt-4 md:mt-0 flex items-center gap-4">
                            @if($item['status'] == 'Active')
                                <span class="px-5 py-2.5 bg-green-50 text-green-600 rounded-full text-[10px] font-black uppercase italic tracking-widest animate-pulse border border-green-100">Live Now</span>
                            @else
                                <span class="px-5 py-2.5 bg-gray-50 text-gray-400 rounded-full text-[10px] font-black uppercase italic tracking-widest border border-gray-100">Closed</span>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="group bg-black p-8 rounded-[2.5rem] flex flex-col md:flex-row md:items-center justify-between hover:bg-[#800000] transition-all shadow-2xl shadow-black/10">
                        <div class="flex items-center gap-8 text-white">
                            <div class="w-20 h-20 bg-white/10 rounded-[2rem] flex items-center justify-center backdrop-blur-md group-hover:rotate-12 transition-all">
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.168.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </div>
                            <div>
                                <span class="text-[9px] font-black uppercase text-red-400 tracking-[0.3em] opacity-80">{{ $item['info'] }}</span>
                                <h4 class="text-xl font-black uppercase italic text-white tracking-tight leading-none mb-2">{{ $item['title'] }}</h4>
                                <p class="text-[11px] text-white/50 font-bold uppercase italic">{{ $item['time'] }}</p>
                            </div>
                        </div>
                        <button class="mt-4 md:mt-0 px-8 py-4 bg-white text-black rounded-2xl text-[10px] font-black uppercase hover:scale-105 transition-all shadow-xl">Submit Now</button>
                    </div>
                @endif
            @empty
                <div class="py-24 text-center bg-white rounded-[4rem] border border-gray-50 shadow-inner flex flex-col items-center">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                    <p class="text-[11px] font-black uppercase text-gray-300 italic tracking-[0.4em]">Relax, Nothing scheduled.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
