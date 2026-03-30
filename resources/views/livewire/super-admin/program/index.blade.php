<?php

use App\Models\Program;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $search = '';

    public function delete($id)
    {
        $program = Program::findOrFail($id);
        $program->delete();
        session()->flash('success', 'Program berhasil dihapus.');
    }

    public function rendering($view)
    {
        $programs = Program::where('name', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(10);

        return $view->with(['programs' => $programs]);
    }
}; ?>

<div class="space-y-6">
    {{-- Header & Search --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
        <div>
            <h2 class="text-2xl font-black text-gray-800 uppercase tracking-tighter italic">Manajemen Program</h2>
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">Total Program Terdaftar: {{ \App\Models\Program::count() }}</p>
        </div>
        <div class="flex items-center gap-4">
            <input type="text" wire:model.live="search" placeholder="Cari nama program..."
                   class="rounded-2xl border-gray-100 bg-gray-50 focus:ring-[#800000] focus:border-[#800000] text-sm px-6 py-3 w-64">
            <a href="{{ route('superadmin.programs.create') }}"
               class="bg-[#800000] hover:bg-black text-white px-6 py-3 rounded-2xl font-black text-[10px] uppercase tracking-widest transition-all">
                + Program Baru
            </a>
        </div>
    </div>

    {{-- Program Cards Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($programs as $program)
            <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden flex flex-col group hover:shadow-xl transition-all duration-500">
                {{-- Banner Preview --}}
                <div class="h-40 bg-gray-100 relative overflow-hidden">
                    @if($program->banner)
                        <img src="{{ asset('storage/'.$program->banner) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200">
                            <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                    @endif
                    <div class="absolute top-4 right-4">
                        <span class="px-3 py-1 rounded-full bg-white/90 backdrop-blur-md text-[9px] font-black uppercase tracking-widest {{ $program->status == 'active' ? 'text-green-600' : 'text-red-600' }}">
                            {{ $program->status }}
                        </span>
                    </div>
                </div>


                {{-- Tombol Delegasi Admin --}}
<a href="{{ route('superadmin.programs.delegate', $program->id) }}"
   wire:navigate
   title="Delegasikan Admin"
   class="p-3 text-gray-400 hover:text-[#800000] hover:bg-red-50 rounded-xl transition-all duration-300 group/btn">

    <div class="flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
        </svg>
        <span class="text-[9px] font-black uppercase tracking-widest hidden group-hover/btn:block animate-in fade-in slide-in-from-left-2">
            Delegate
        </span>
    </div>
</a>




                <div class="p-8 flex-1 flex flex-col">
                    <h3 class="font-black text-gray-800 uppercase tracking-tighter text-lg leading-tight mb-2">{{ $program->name }}</h3>
                    <p class="text-xs text-gray-400 line-clamp-2 mb-6 italic">{{ $program->description }}</p>

                    <div class="mt-auto space-y-4">
                        <div class="flex justify-between items-center border-t border-gray-50 pt-4">
                            <div class="text-left">
                                <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest">Pendaftar</p>
                                <p class="text-sm font-bold text-[#800000]">{{ $program->participants()->count() }} <span class="text-gray-300 font-medium">/ {{ $program->quota ?: '∞' }}</span></p>
                            </div>
                            <div class="text-right">
                                <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest">Metode</p>
                                <p class="text-[10px] font-bold text-gray-600 uppercase">{{ $program->is_open ? 'Terbuka' : 'Kode Redeem' }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <a href="{{ route('superadmin.programs.edit', $program->id) }}" class="flex-1 bg-gray-50 hover:bg-[#800000] hover:text-white text-center py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                                Edit
                            </a>





{{-- Ubah dari programs.enroll-manual menjadi superadmin.programs.enroll-manual --}}
<a href="{{ route('superadmin.programs.enroll-manual') }}"
   class="inline-flex items-center gap-3 bg-[#800000] hover:bg-black text-white px-8 py-4 rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] transition-all duration-300 shadow-2xl shadow-red-900/40 active:scale-95 group">

    {{-- Icon Plus dengan Box Putih Kecil --}}
    <div class="bg-white/10 group-hover:bg-white/20 p-2 rounded-lg transition-colors">
        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path>
        </svg>
    </div>

    <span>Enroll Peserta Manual</span>

    {{-- Arrow Icon yang muncul saat hover --}}
    <svg class="w-3 h-3 opacity-0 -translate-x-2 group-hover:opacity-100 group-hover:translate-x-0 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
    </svg>
</a>



                            <button wire:click="delete({{ $program->id }})"
                                    wire:confirm="Hapus program ini? Semua data pendaftar di dalamnya akan ikut terhapus!"
                                    class="p-3 bg-red-50 text-red-500 hover:bg-red-500 hover:text-white rounded-xl transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-8">
        {{ $programs->links() }}
    </div>
</div>
