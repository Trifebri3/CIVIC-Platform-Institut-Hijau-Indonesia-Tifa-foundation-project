<?php

use App\Models\Program;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $search = '';

    // Reset halaman saat mencari agar tidak stuck di page tinggi
    public function updatedSearch() { $this->resetPage(); }

    public function rendering($view)
    {
        $programs = Program::where('status', 'active')
            ->where('name', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(10); // Naikkan jumlah per halaman karena tampilan sekarang lebih compact

        return $view->with(['programs' => $programs]);
    }
}; ?>

<div class="space-y-4 md:space-y-6">
    {{-- Floating Search Bar - Lebih Slim --}}
    <div class="sticky top-0 z-20 bg-[#f9fafb]/80 backdrop-blur-md py-2">
        <div class="relative max-w-lg mx-auto group">
            <div class="absolute inset-y-0 left-5 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-[#800000] opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input type="text" wire:model.live="search"
                   class="w-full bg-white border-none shadow-sm ring-1 ring-gray-100 rounded-2xl py-4 pl-12 pr-6 text-xs font-bold text-gray-700 focus:ring-2 focus:ring-[#800000] transition-all placeholder:text-gray-300 uppercase tracking-widest"
                   placeholder="CARI PROGRAM...">
        </div>
    </div>

    {{-- Grid Program: 1 Kolom di HP, 2 di Tablet, 3 di Desktop --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 md:gap-4">
        @forelse($programs as $program)
            <a href="{{ route('user.programs.show', $program->slug) }}"
               class="group relative bg-white rounded-2xl p-3 flex gap-4 items-center border border-gray-50 shadow-sm hover:shadow-md transition-all active:scale-[0.98]">

                {{-- Thumbnail: Square & Compact --}}
                <div class="relative h-16 w-16 sm:h-20 sm:w-20 flex-none rounded-xl overflow-hidden shadow-inner bg-gray-100">
                    @if($program->banner)
                        <img src="{{ asset('storage/'.$program->banner) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-[#800000] to-black flex items-center justify-center">
                            <span class="text-[8px] font-black text-white/30 tracking-tighter italic">CIVIC</span>
                        </div>
                    @endif

                    {{-- Status Overlay (Hanya muncul jika penuh) --}}
                    @php $isFull = $program->quota > 0 && $program->participants()->count() >= $program->quota; @endphp
                    @if($isFull)
                        <div class="absolute inset-0 bg-red-600/80 backdrop-blur-[1px] flex items-center justify-center">
                            <span class="text-[7px] text-white font-black uppercase">FULL</span>
                        </div>
                    @endif
                </div>

                {{-- Content: Fokus pada Judul dan Kapasitas --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-0.5">
                        <span class="text-[7px] font-black uppercase tracking-[0.2em] {{ $program->is_open ? 'text-green-600' : 'text-orange-500' }}">
                            {{ $program->is_open ? 'Terbuka' : 'Redeem' }}
                        </span>
                        <span class="text-gray-200 text-[8px]">•</span>
                        <span class="text-[7px] font-bold text-gray-400 uppercase tracking-tighter">
                            {{ $program->participants()->count() }}/{{ $program->quota ?: '∞' }} Peserta
                        </span>
                    </div>

                    <h3 class="text-xs sm:text-sm font-black text-gray-800 uppercase tracking-tight truncate group-hover:text-[#800000] transition-colors">
                        {{ $program->name }}
                    </h3>

                    <p class="text-[10px] text-gray-400 line-clamp-1 mt-0.5 italic leading-none">
                        {{ $program->description }}
                    </p>
                </div>

                {{-- Ikon Navigasi --}}
                <div class="flex-none pr-1">
                    <div class="w-7 h-7 rounded-full bg-gray-50 flex items-center justify-center group-hover:bg-[#800000] transition-colors">
                        <svg class="w-3 h-3 text-gray-300 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </div>
            </a>
        @empty
            <div class="col-span-full py-16 text-center">
                <p class="text-gray-300 font-black uppercase tracking-[0.3em] italic text-[10px]">Tidak ada hasil ditemukan</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination Simple --}}
    <div class="mt-6">
        {{ $programs->links() }}
    </div>
</div>
