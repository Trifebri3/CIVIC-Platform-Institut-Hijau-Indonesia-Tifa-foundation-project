<?php

use Livewire\Volt\Component;
use App\Models\SubProgram;
use App\Models\SubProgramContent;

new class extends Component {
    // Menangkap data dari route/view luar
    public SubProgram $subProgram;

    public function mount(SubProgram $subProgram)
    {
        $this->subProgram = $subProgram;
    }

    // Fungsi hapus materi
    public function deleteContent($id)
    {
        $content = SubProgramContent::find($id);
        if ($content) {
            $content->delete();
            // Refresh model agar list di blade terupdate otomatis
            $this->subProgram->load('contents');
            session()->flash('success', 'Materi berhasil dihapus.');
        }
    }
}; ?>

<div class="max-w-5xl mx-auto py-10 px-4">
    {{-- Notifikasi Sukses --}}
    @if (session()->has('success'))
        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 text-[10px] font-black uppercase italic">
            {{ session('success') }}
        </div>
    @endif

    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-12 gap-6">
        <div>
            <a href="{{ route('admin-program.subprogram.index') }}" wire:navigate class="text-[9px] font-black uppercase tracking-widest text-gray-400 hover:text-black mb-4 block transition-colors">
                ← Kembali ke Index
            </a>
            <h1 class="text-4xl font-black uppercase italic tracking-tighter text-gray-900 leading-none">
                {{ $subProgram->title }}
            </h1>
            <p class="text-[10px] font-bold uppercase tracking-[0.4em] text-[#800000] mt-3">
                Manajemen Konten Kurikulum
            </p>
        </div>

        <a href="{{ route('admin-program.subprogram.builder', $subProgram->id) }}" wire:navigate
           class="px-8 py-4 bg-black text-white rounded-2xl font-black uppercase italic text-[10px] tracking-widest hover:bg-[#800000] shadow-xl shadow-black/10 transition-all active:scale-95">
            + Tambah Materi Baru
        </a>
    </div>

    {{-- List Materi --}}
    <div class="space-y-4">
        {{-- Menggunakan relasi contents() yang sudah kita buat di Model SubProgram --}}
        @forelse($subProgram->contents()->orderBy('order_position', 'asc')->get() as $index => $content)
            <div class="bg-white border border-gray-100 rounded-[2rem] p-6 flex items-center justify-between group hover:border-[#800000]/20 hover:shadow-xl hover:shadow-gray-200/50 transition-all duration-500">
                <div class="flex items-center gap-6">
                    <span class="text-2xl font-black italic text-gray-100 group-hover:text-[#800000]/10 transition-colors duration-500">
                        {{ sprintf('%02d', $index + 1) }}
                    </span>
                    <div>
                        <h4 class="text-sm font-black uppercase italic tracking-tight text-gray-800">{{ $content->title }}</h4>
                        <div class="flex items-center gap-3 mt-1">
                            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">
                                {{ is_array($content->modules) ? count($content->modules) : 0 }} Modul Tersemat
                            </p>
                            <span class="text-gray-200 text-[8px]">•</span>
                            <span class="text-[9px] font-black text-[#800000] uppercase italic tracking-tighter">
                                Order: {{ $content->order_position }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                    {{-- Tombol Edit (Arahkan ke Builder dengan mode edit jika nanti sudah dibuat) --}}
                    <button class="p-3 bg-gray-50 rounded-xl text-gray-400 hover:text-black hover:bg-gray-100 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>

                    {{-- Tombol Hapus --}}
                    <button wire:click="deleteContent({{ $content->id }})"
                            wire:confirm="Hapus materi ini? Data progres user juga akan ikut terhapus."
                            class="p-3 bg-gray-50 rounded-xl text-gray-400 hover:text-red-600 hover:bg-red-50 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </div>
            </div>
        @empty
            <div class="flex flex-col items-center justify-center py-24 bg-gray-50 rounded-[4rem] border-2 border-dashed border-gray-100 opacity-60">
                <svg class="w-12 h-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 6v6m0 0v6m0-6h6m-6 0H6" stroke-width="2" stroke-linecap="round"/></svg>
                <p class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-400 italic text-center">
                    Kurikulum masih kosong.<br>Klik tombol di atas untuk membangun materi.
                </p>
            </div>
        @endforelse
    </div>
</div>
