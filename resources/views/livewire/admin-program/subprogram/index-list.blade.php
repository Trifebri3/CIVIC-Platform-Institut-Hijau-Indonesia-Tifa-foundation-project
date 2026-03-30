<?php

use Livewire\Volt\Component;
use App\Models\SubProgram;

new class extends Component {
    // Fungsi untuk mengambil data dengan Eager Loading
    public function with(): array
    {
        return [
            'subprograms' => SubProgram::with(['program']) // Load program sekaligus
                ->withCount('contents') // Hitung jumlah konten tanpa query berulang
                ->latest()
                ->get(),
        ];
    }
}; ?>

<div class="max-w-7xl mx-auto py-10 px-4">
    {{-- Header --}}
    <div class="mb-12 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h1 class="text-4xl font-black uppercase italic tracking-tighter text-gray-900 leading-none">
                Program Management
            </h1>
            <p class="text-[10px] font-bold uppercase tracking-[0.4em] text-[#800000] mt-3">
                Daftar Sub-Infrastruktur Digital
            </p>
        </div>

        {{-- Pencarian atau Statistik Singkat (Luxury Touch) --}}
        <div class="px-6 py-3 bg-gray-50 rounded-2xl border border-gray-100">
            <span class="text-[9px] font-black uppercase tracking-widest text-gray-400 italic">Total Infrastruktur:</span>
            <span class="text-sm font-black italic ml-2">{{ $subprograms->count() }}</span>
        </div>
    </div>

    {{-- Grid List --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach($subprograms as $item)
            <div class="bg-white rounded-[2.5rem] border border-gray-100 p-8 shadow-sm hover:shadow-2xl hover:shadow-gray-200/50 transition-all duration-500 group relative overflow-hidden">

                {{-- Decorative Background Element --}}
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-gray-50 rounded-full group-hover:bg-[#800000]/5 transition-colors duration-500"></div>

                <div class="relative">
                    <div class="flex justify-between items-start mb-6">
                        <span class="px-4 py-1.5 bg-gray-50 text-[9px] font-black uppercase tracking-widest rounded-full italic text-gray-400 group-hover:text-[#800000] group-hover:bg-[#800000]/5 transition-all">
                            {{ $item->program->title ?? 'General' }}
                        </span>
                    </div>

                    <h3 class="text-xl font-black uppercase italic tracking-tight text-gray-900 mb-2 group-hover:text-[#800000] transition-colors leading-tight">
                        {{ $item->title }}
                    </h3>

                    <div class="flex items-center gap-2 mb-8">
                        <div class="w-1.5 h-1.5 rounded-full bg-[#800000]"></div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                            {{ $item->contents_count }} Materi Terdaftar
                        </p>
                    </div>

                    <div class="flex gap-3">
                        <a href="{{ route('admin-program.subprogram.show', $item->id) }}" wire:navigate
                           class="flex-1 text-center py-4 bg-black text-white rounded-2xl text-[9px] font-black uppercase italic tracking-[0.2em] hover:bg-[#800000] shadow-xl shadow-black/10 transition-all active:scale-95">
                            Buka Kurikulum
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Empty State --}}
    @if($subprograms->isEmpty())
        <div class="py-24 text-center bg-gray-50 rounded-[4rem] border-2 border-dashed border-gray-100">
            <p class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-300 italic">Belum ada data subprogram, Bos!</p>
        </div>
    @endif
</div>
