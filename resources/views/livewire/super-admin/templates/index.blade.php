<?php

use App\Models\SubProgramTemplate;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $search = '';

    public function delete($id)
    {
        $template = SubProgramTemplate::findOrFail($id);

        // Proteksi: Jangan hapus jika sudah dipakai di SubProgram
        if ($template->subPrograms()->exists()) {
            session()->flash('error', 'Template "' . $template->name . '" tidak bisa dihapus karena sudah digunakan oleh Sub-Program aktif.');
            return;
        }

        $template->delete();
        session()->flash('success', 'Template berhasil dimusnahkan.');
    }

    public function with()
    {
        return [
            'templates' => SubProgramTemplate::where('name', 'like', '%' . $this->search . '%')
                ->withCount('subPrograms')
                ->latest()
                ->paginate(10),
        ];
    }
}; ?>

<div class="max-w-6xl mx-auto pb-20 px-4">
    {{-- Header Section --}}
    <div class="mb-12 flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <h1 class="text-4xl md:text-5xl font-black text-gray-900 uppercase tracking-tighter italic leading-none">Template Assets</h1>
            <p class="text-[10px] text-[#800000] font-black uppercase tracking-[0.3em] mt-3 italic">Manajemen Struktur Identitas Digital</p>
        </div>
        <div class="flex flex-col md:flex-row gap-4">
            <input type="text" wire:model.live="search" placeholder="Cari Template..."
                   class="rounded-2xl border-gray-100 bg-white shadow-sm px-6 py-4 text-xs font-bold focus:ring-[#800000] w-full md:w-64">

            <a href="{{ route('superadmin.templates.create') }}"
               class="bg-[#800000] text-white px-8 py-4 rounded-2xl font-black text-[10px] uppercase tracking-[0.3em] shadow-xl hover:bg-black transition-all text-center">
               + Create New Template
            </a>
        </div>
    </div>

    @if(session()->has('error'))
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-2xl animate-in fade-in slide-in-from-top-2">
            <p class="text-[10px] font-black text-red-600 uppercase tracking-widest">{{ session('error') }}</p>
        </div>
    @endif

    {{-- Table / Card Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($templates as $template)
            <div class="bg-white rounded-[2.5rem] p-8 border border-gray-100 shadow-sm hover:shadow-2xl transition-all duration-500 group relative overflow-hidden">
                {{-- Decorative Background Icon --}}
                <div class="absolute -right-4 -top-4 text-gray-50 group-hover:text-red-50 transition-colors duration-500">
                    <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
                </div>

                <div class="relative z-10">
                    <div class="flex items-start justify-between mb-6">
                        <div class="h-14 w-14 bg-gray-50 group-hover:bg-[#800000] rounded-2xl flex items-center justify-center transition-all duration-500">
                            <svg class="w-6 h-6 text-[#800000] group-hover:text-white transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] font-black text-gray-300 uppercase tracking-widest block">Usage</span>
                            <span class="text-2xl font-black text-gray-900 italic leading-none">{{ $template->sub_programs_count }}</span>
                        </div>
                    </div>

                    <h3 class="text-xl font-black text-gray-800 uppercase tracking-tighter mb-2">{{ $template->name }}</h3>
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest leading-relaxed line-clamp-2 min-h-[32px]">
                        {{ $template->description ?? 'Tidak ada deskripsi struktur.' }}
                    </p>

                    <div class="mt-8 pt-6 border-t border-gray-50 flex items-center justify-between">
                        <div class="flex -space-x-2">
                            @foreach(array_slice($template->fields_schema, 0, 3) as $f)
                                <div class="h-6 w-6 rounded-full bg-white border-2 border-gray-100 flex items-center justify-center" title="{{ $f['label'] }}">
                                    <span class="text-[7px] font-black text-[#800000]">{{ strtoupper(substr($f['type'], 0, 1)) }}</span>
                                </div>
                            @endforeach
                            @if(count($template->fields_schema) > 3)
                                <div class="h-6 w-6 rounded-full bg-gray-900 flex items-center justify-center border-2 border-white">
                                    <span class="text-[7px] font-black text-white">+{{ count($template->fields_schema) - 3 }}</span>
                                </div>
                            @endif
                        </div>

                        <div class="flex items-center gap-2">
                            <button wire:click="delete({{ $template->id }})"
                                    wire:confirm="Hapus template ini? Pastikan tidak ada sub-program yang memakainya."
                                    class="p-3 text-gray-300 hover:text-red-600 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
{{-- MENJADI INI --}}
<a href="{{ route('superadmin.templates.edit', $template->id) }}"
   wire:navigate
   class="p-3 text-gray-300 hover:text-black transition-colors">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
    </svg>
</a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-20 bg-white rounded-[3rem] border-2 border-dashed border-gray-100 flex flex-col items-center justify-center text-center">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-6">
                    <svg class="w-10 h-10 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <h4 class="text-sm font-black text-gray-400 uppercase tracking-[0.3em]">No Blueprint Found</h4>
                <p class="text-[10px] text-gray-300 font-bold mt-2 uppercase tracking-widest">Mulai dengan membuat template pertamamu.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-12 px-4">
        {{ $templates->links() }}
    </div>
</div>
