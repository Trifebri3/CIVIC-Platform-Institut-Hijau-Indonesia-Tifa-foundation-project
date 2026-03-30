<?php

use App\Models\SubProgram;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $search = '';

    public function delete($id)
    {
        SubProgram::find($id)?->delete();
    }

    public function with()
    {
        $user = auth()->user();

        return [
            'daftarKonten' => SubProgram::query()
                // Hapus filter template_id agar SEMUA (Kelas & Project) muncul
                ->with(['program'])
                ->when($user->role !== 'superadmin', function($q) use ($user) {
                    $q->whereIn('program_id', $user->managedPrograms->pluck('id'));
                })
                ->when($this->search, function($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                      ->orWhereHas('program', fn($p) => $p->where('name', 'like', '%' . $this->search . '%'));
                })
                ->latest()
                ->paginate(10),
        ];
    }
}; ?>

<div class="max-w-7xl mx-auto px-4 py-8 antialiased">
    {{-- Minimalist Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4 border-b border-gray-100 pb-6">
        <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tighter italic uppercase">
                ALL<span class="text-[#800000]">CONTENT</span>MODULES
            </h1>
            <p class="text-[10px] text-gray-400 font-bold tracking-widest uppercase mt-1">Unified Program Asset Manager</p>
        </div>

        <div class="relative group">
            <input type="text" wire:model.live="search" placeholder="Cari judul atau program..."
                   class="w-full md:w-80 bg-gray-50 border-none rounded-xl px-4 py-3 text-xs font-medium focus:ring-2 focus:ring-[#800000]/10 transition-all shadow-inner">
            <div class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
        </div>
    </div>

    {{-- Compact List Container --}}
    <div class="bg-white rounded-[2rem] border border-gray-100 shadow-xl shadow-black/5 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="text-left py-5 px-8 text-[10px] font-black text-gray-400 uppercase tracking-widest">General Info</th>
                        <th class="text-left py-5 px-6 text-[10px] font-black text-gray-400 uppercase tracking-widest hidden md:table-cell">Asset Type</th>
                        <th class="text-left py-5 px-6 text-[10px] font-black text-gray-400 uppercase tracking-widest hidden lg:table-cell">Program Parent</th>
                        <th class="text-center py-5 px-8 text-[10px] font-black text-gray-400 uppercase tracking-widest">Master Control</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($daftarKonten as $item)
                        @php
                            $data = is_array($item->content_data) ? $item->content_data : (json_decode($item->content_data, true) ?? []);
                            // Warna dinamis berdasarkan Template ID
                            $typeColor = $item->template_id == 1 ? 'bg-blue-50 text-blue-600' : 'bg-purple-50 text-purple-600';
                            $typeName = $item->template_id == 1 ? 'KELAS' : 'PROJECT';
                        @endphp
                        <tr class="hover:bg-gray-50/80 transition-all group">
                            {{-- Info Section --}}
                            <td class="py-5 px-8">
                                <div class="flex items-center gap-5">
                                    <div class="h-14 w-14 rounded-2xl overflow-hidden bg-gray-100 flex-shrink-0 border-2 border-white shadow-sm group-hover:rotate-3 transition-transform">
                                        @if(isset($data['banner']) || isset($data['thumbnail']))
                                            <img src="{{ asset('storage/' . ($data['banner'] ?? $data['thumbnail'])) }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-[10px] font-black text-gray-300 bg-gray-50">NOIMG</div>
                                        @endif
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-black text-gray-900 group-hover:text-[#800000] transition-colors leading-tight mb-1 uppercase tracking-tight">
                                            {{ $item->title }}
                                        </h3>
                                        <div class="flex items-center gap-3">
                                            <span class="text-[9px] font-bold text-gray-400 uppercase italic">ID: #{{ $item->id }}</span>
                                            <span class="h-1 w-1 bg-gray-200 rounded-full"></span>
                                            <span class="text-[9px] font-bold text-gray-300 uppercase italic">{{ $item->created_at->format('d M Y') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Type Tag --}}
                            <td class="py-5 px-6 hidden md:table-cell">
                                <span class="{{ $typeColor }} text-[8px] font-black px-3 py-1.5 rounded-lg uppercase tracking-[0.1em] italic shadow-sm">
                                    {{ $typeName }}
                                </span>
                            </td>

                            {{-- Program Tag --}}
                            <td class="py-5 px-6 hidden lg:table-cell">
                                <span class="bg-gray-100 text-gray-500 text-[8px] font-black px-3 py-1.5 rounded-lg uppercase tracking-tight italic">
                                    {{ $item->program->name ?? 'N/A' }}
                                </span>
                            </td>

                            {{-- Action Section (TOMBOL TETAP SAMA) --}}
                            <td class="py-5 px-8">
                                <div class="flex items-center justify-center gap-2">
                                    {{-- 1. Builder --}}
                                    <a href="{{ route('admin-program.subprogram.builder', $item->id) }}"
                                       wire:navigate title="Manage Modules"
                                       class="p-2.5 text-gray-400 hover:text-black hover:bg-white rounded-xl transition-all border border-transparent hover:border-gray-200 hover:shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                    </a>

                                    {{-- 2. Isi Content --}}
                                    <a href="{{ route('admin-program.subprogram.isicontents', $item->id) }}"
                                       wire:navigate title="View Data Folder"
                                       class="p-2.5 text-blue-400 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-all border border-transparent hover:border-blue-100">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" /></svg>
                                    </a>

                                    {{-- 3. Edit Kelas/Project Label --}}
                                    <a href="{{ route('editkontenkelas', ['content' => $item->id]) }}"
                                       wire:navigate title="View/Edit Detail"
                                       class="px-3 py-2 bg-gray-50 text-gray-500 hover:text-white hover:bg-[#800000] rounded-xl font-black text-[8px] uppercase tracking-widest transition-all italic border border-gray-100 hover:border-[#800000]">
                                        {{ $typeName }}
                                    </a>

                                    {{-- 4. Meta Settings --}}
                                    <a href="{{ route('modulkelas.edit', ['content' => $item->id]) }}"
                                       title="System Meta"
                                       class="px-3 py-2 bg-gray-50 text-gray-500 hover:text-white hover:bg-black rounded-xl font-black text-[8px] uppercase tracking-widest transition-all italic border border-gray-100 hover:border-black">
                                        META
                                    </a>

                                    {{-- 5. Advance Settings --}}
                                    <a href="{{ route('admin-program.subprogram.content.edit', ['subProgram' => $item->id, 'content' => $item->id]) }}"
                                       wire:navigate title="Advance Config"
                                       class="p-2.5 text-gray-400 hover:text-black hover:bg-gray-100 rounded-xl transition-all border border-transparent">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" stroke-width="2" stroke-linecap="round"/><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" stroke-width="2"/></svg>
                                    </a>
{{-- Tombol Manajemen Modul Ujian Baru --}}
<a href="{{ route('admin.modul-ujian.index', $item->id) }}"
   wire:navigate
   title="Ujian & Submission"
   class="p-2.5 text-indigo-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all border border-transparent hover:border-indigo-100">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
    </svg>
</a>

<a href="{{ route('admin.modul-ujian.grading', ['id' => $item->id]) }}"
   wire:navigate
   class="p-2.5 text-[#800000] hover:text-white hover:bg-[#800000] rounded-xl transition-all border border-transparent hover:border-[#800000]/20 group shadow-sm bg-red-50/50">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
    </svg>
</a>




<a href="{{ route('admin-program.absensi.manage', $item->slug) }}"
           wire:navigate title="Manage Attendance"
           class="p-3 bg-red-50 text-[#800000] hover:bg-[#800000] hover:text-white rounded-xl transition-all hover:-translate-y-1 shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
</a>


{{-- 8. REKAP BUTTON (Green) --}}
<a href="{{ route('admin-program.absensi.rekap', $item->slug) }}"
   wire:navigate title="View Attendance Report"
   class="p-3 bg-green-50 text-green-600 hover:bg-green-600 hover:text-white rounded-xl transition-all hover:-translate-y-1 shadow-sm">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
</a>


                                    {{-- 6. Delete --}}
                                    <button wire:click="delete({{ $item->id }})"
                                            wire:confirm="Hapus aset secara permanen?"
                                            class="p-2.5 text-gray-300 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all border border-transparent">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-24 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="mb-4 text-gray-200">
                                        <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </div>
                                    <span class="text-[10px] font-black text-gray-300 uppercase tracking-[0.4em] italic leading-relaxed">System Database Empty<br>No Assets Found</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Minimal Pagination --}}
    <div class="mt-10 flex justify-center">
        <div class="inline-block px-4 py-2 bg-white rounded-2xl border border-gray-100 shadow-sm scale-90">
            {{ $daftarKonten->links() }}
        </div>
    </div>
</div>
