<?php

use App\Models\SubProgram;
use App\Models\JawabanUjian;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

new class extends Component {
    use WithPagination;

    public $search = '';
    public $tab = 'active'; // Default: Hanya yang aktif

    public function with()
    {
        $user_id = auth()->id();
        $now = Carbon::now();
        $limitSelesai = $now->copy()->subDay(); // H-1 dari sekarang (untuk filter H+1 deadline)

        $query = SubProgram::with(['program', 'template', 'modulUjians'])
            ->where('status', 'active')
            ->when($this->search, function($q) {
                $q->where('title', 'like', '%' . $this->search . '%');
            });

        if ($this->tab == 'active') {
            // TAMPILKAN: Yang belum lewat H+1 deadline DAN belum dijawab
            $query->where(function($q) use ($user_id, $limitSelesai) {
                $q->where('deadline', '>=', $limitSelesai)
                  ->whereDoesntHave('modulUjians.jawaban', function($sq) use ($user_id) {
                      $sq->where('user_id', $user_id);
                  });
            });
        } elseif ($this->tab == 'completed') {
            // TAMPILKAN: Yang sudah lewat H+1 deadline ATAU sudah dijawab
            $query->where(function($q) use ($user_id, $limitSelesai) {
                $q->where('deadline', '<', $limitSelesai)
                  ->orWhereHas('modulUjians.jawaban', function($sq) use ($user_id) {
                      $sq->where('user_id', $user_id);
                  });
            });
        } elseif ($this->tab == 'today') {
            $query->whereDate('deadline', $now->toDateString())
                  ->where('deadline', '>', $now);
        }

        return [
            // Sorting: Deadline terdekat di atas, yang jauh di bawah
            'allContents' => $query->orderBy('deadline', 'asc')->paginate(12),
            'now' => $now
        ];
    }
}; ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 antialiased bg-[#fcfcfc]">

    {{-- Header Mini & Tab Switcher --}}
    <div class="flex flex-col gap-6 mb-8">
        <div class="flex items-center justify-between">
            <h1 class="text-3xl font-black text-gray-900 uppercase italic tracking-tighter">
                Feed<span class="text-[#800000]">.</span>
            </h1>
            <div class="relative group">
                <input type="text" wire:model.live="search" placeholder="Cari..."
                       class="w-32 sm:w-64 bg-white border-gray-100 rounded-xl py-2 px-4 text-[10px] font-bold shadow-sm focus:ring-2 focus:ring-[#800000]/20 transition-all">
            </div>
        </div>

        {{-- Folder Style Tabs --}}
        <div class="flex items-center gap-2 overflow-x-auto pb-2 no-scrollbar">
            @foreach(['active' => 'Materi Aktif', 'today' => 'Hari Ini', 'completed' => 'Arsip Selesai'] as $key => $label)
                <button wire:click="$set('tab', '{{ $key }}')"
                    class="flex-none px-5 py-2 rounded-full text-[9px] font-black uppercase tracking-widest transition-all border
                    {{ $tab == $key ? 'bg-black text-white border-black shadow-lg shadow-black/20' : 'bg-white text-gray-400 border-gray-100 hover:border-gray-300' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- Grid System --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        @forelse($allContents as $content)
            @php
                $deadline = $content->deadline ? \Carbon\Carbon::parse($content->deadline) : null;
                $isDone = $content->modulUjians->isNotEmpty() && $content->modulUjians->every(fn($m) => $m->jawaban()->where('user_id', auth()->id())->exists());

                $data = is_array($content->content_data) ? $content->content_data : json_decode($content->content_data, true);
                $banner = $data['banner'] ?? 'https://ui-avatars.com/api/?name='.urlencode($content->title).'&background=800000&color=fff';

                // Logika Status untuk Label
                $statusLabel = 'Materi';
                $statusColor = 'bg-black';
                $isOngoing = false;

                if ($deadline) {
                    if ($isDone || $now->gt($deadline->copy()->addDay())) {
                        $statusLabel = 'Closed';
                        $statusColor = 'bg-gray-400';
                    } elseif ($deadline->isToday() && $deadline->isFuture()) {
                        $statusLabel = 'Live';
                        $statusColor = 'bg-red-600 animate-pulse';
                        $isOngoing = true;
                    } elseif ($deadline->isFuture() && $deadline->diffInDays($now) <= 3) {
                        $statusLabel = 'Soon';
                        $statusColor = 'bg-orange-500';
                    }
                }
            @endphp

            <div class="group relative flex flex-col bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden transition-all duration-500 hover:shadow-xl hover:-translate-y-1">

                {{-- Banner & Overlay --}}
                <div class="relative h-36 overflow-hidden bg-gray-100">
                    <img src="{{ str_starts_with($banner, 'http') ? $banner : Storage::url($banner) }}"
                         class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">

                    {{-- Status Tag --}}
                    <div class="absolute top-3 left-3">
                        <span class="{{ $statusColor }} text-white text-[7px] font-black px-3 py-1.5 rounded-lg uppercase italic tracking-tighter shadow-xl">
                            {{ $statusLabel }}
                        </span>
                    </div>

                    {{-- Countdown Mini (Hanya jika Live) --}}
                    @if($isOngoing && $deadline)
                        <div class="absolute bottom-0 inset-x-0 bg-gradient-to-t from-black/80 p-3"
                             x-data="{
                                timer: '',
                                target: {{ $deadline->timestamp * 1000 }},
                                update() {
                                    let d = this.target - new Date().getTime();
                                    if(d<0){ this.timer = 'CLOSED'; return; }
                                    let h = Math.floor(d/3600000);
                                    let m = Math.floor((d%3600000)/60000);
                                    this.timer = h + 'h ' + m + 'm';
                                }
                             }" x-init="update(); setInterval(()=>update(), 60000)">
                            <p class="text-[7px] font-black text-red-400 uppercase tracking-widest leading-none">Sisa Waktu</p>
                            <p class="text-white font-black italic text-xs tracking-tighter" x-text="timer"></p>
                        </div>
                    @endif
                </div>

                {{-- Body Card --}}
                <div class="p-4 flex flex-col flex-1">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[8px] font-black text-[#800000] uppercase truncate max-w-[60%]">
                            {{ $content->program?->name }}
                        </span>
                        <span class="text-[8px] font-bold text-gray-400">
                            {{ $deadline ? $deadline->format('d/m H:i') : '-' }}
                        </span>
                    </div>

                    <h3 class="text-[11px] font-black text-gray-900 uppercase italic leading-tight mb-4 line-clamp-2 group-hover:text-[#800000]">
                        {{ $content->title }}
                    </h3>

                    <div class="mt-auto">
                        <a href="{{ route('user.subprogram.show', $content->slug) }}" wire:navigate
                           class="flex items-center justify-center w-full py-2.5 rounded-xl text-[8px] font-black uppercase tracking-widest italic transition-all
                           {{ $tab == 'completed' ? 'bg-gray-100 text-gray-500' : 'bg-black text-white hover:bg-[#800000]' }}">
                            {{ $tab == 'completed' ? 'Lihat Hasil' : 'Buka' }}
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-20 text-center bg-gray-50 rounded-[2rem] border-2 border-dashed border-gray-100">
                <p class="text-[9px] font-black text-gray-300 uppercase tracking-widest">Tidak ada konten di folder ini</p>
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $allContents->links() }}
    </div>
    <style>
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
</div>



