<?php
// resources/views/livewire/admin-program/content/index.blade.php

use App\Models\{SubProgramContent, Program};
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $search = '';

    // Reset pagination saat nyari biar gak error "Page not found"
    public function updatedSearch() {
        $this->resetPage();
    }

    public function delete($id) {
        $content = SubProgramContent::findOrFail($id);

        // Security check: Pastikan admin memang mengelola program ini
        if (auth()->user()->managedPrograms->contains($content->subProgram->program_id)) {
            $content->delete();
            session()->flash('success', 'Konten berhasil dihapus! 🗑️');
        }
    }

public function with() {
    $user = auth()->user();

    return [
        'contents' => SubProgramContent::query()
            ->with(['subProgram.program', 'subProgram.template'])
            // 1. Logika Hak Akses
            ->when($user->role !== 'superadmin', function($q) use ($user) {
                // Ambil ID program yang dikelola dari relasi managedPrograms
                $myProgramIds = $user->managedPrograms->pluck('id');

                $q->whereHas('subProgram', function($sq) use ($myProgramIds) {
                    $sq->whereIn('program_id', $myProgramIds);
                });
            })
            // 2. Logika Pencarian (Dibungkus agar tidak merusak filter role)
            ->when($this->search, function($q) {
                $q->where(function($query) {
                    $query->where('title', 'like', '%' . $this->search . '%')
                          ->orWhereHas('subProgram.program', function($p) {
                              $p->where('name', 'like', '%' . $this->search . '%');
                          })
                          ->orWhereHas('subProgram', function($sp) {
                              $sp->where('title', 'like', '%' . $this->search . '%');
                          });
                });
            })
            ->latest()
            ->paginate(10),
    ];
}
}; ?>

<div class="max-w-7xl mx-auto px-4 py-10 pb-32">
    {{-- 1. NOTIFICATION SYSTEM --}}
    @if (session()->has('success'))
        <div class="mb-8 p-4 bg-[#800000] text-white rounded-2xl flex items-center justify-between shadow-lg shadow-[#800000]/20 animate-in fade-in slide-in-from-top-4 duration-500">
            <div class="flex items-center gap-3">
                <span class="text-lg">✅</span>
                <p class="text-[10px] font-black uppercase tracking-widest italic">{{ session('success') }}</p>
            </div>
            <button @click="show = false" class="opacity-50 hover:opacity-100 transition-opacity">×</button>
        </div>
    @endif

    {{-- 2. HEADER SECTION --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-12 gap-8">
        <div class="space-y-2">
            <h1 class="text-6xl font-black text-gray-900 uppercase italic tracking-tighter leading-none">
                Content <span class="text-[#800000]">Vault</span>
            </h1>
            <div class="flex items-center gap-3">
                <div class="h-[2px] w-12 bg-[#800000]"></div>
                <p class="text-[10px] text-gray-400 font-black uppercase tracking-[0.4em] italic">
                    Curriculum & Resource Management
                </p>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row items-center gap-4 w-full md:w-auto">
            {{-- Search Bar --}}
            <div class="relative w-full sm:w-72 group">
                <div class="absolute inset-y-0 left-5 flex items-center pointer-events-none text-gray-400 group-focus-within:text-[#800000] transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" wire:model.live="search" placeholder="Filter materi/program..."
                       class="w-full bg-white border-2 border-gray-100 rounded-2xl pl-12 pr-5 py-4 text-[11px] font-bold focus:ring-0 focus:border-[#800000]/30 transition-all shadow-sm group-hover:border-gray-200">
            </div>

            {{-- Create Button --}}
            <a href="{{ route('admin-program.content.create') }}" wire:navigate
               class="w-full sm:w-auto bg-black text-white px-8 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-[#800000] transition-all shadow-2xl shadow-black/20 active:scale-95 whitespace-nowrap flex items-center justify-center gap-3">
                <span>NEW ASSET</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
            </a>
        </div>
    </div>

    {{-- 3. DATA TABLE --}}
    <div class="bg-white rounded-[3rem] shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="px-10 py-7 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] italic">Primary Identity</th>
                        <th class="px-10 py-7 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] italic text-center">Format</th>
                        <th class="px-10 py-7 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] italic text-right">Control</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($contents as $item)
                        <tr class="group hover:bg-gray-50/40 transition-all">
                            <td class="px-10 py-8">
                                <div class="flex flex-col gap-3">
                                    {{-- Contextual Breadcrumb --}}
                                    <div class="flex items-center gap-2">
<span class="bg-black text-white text-[7px] font-black px-2 py-0.5 rounded uppercase tracking-widest italic">
    {{ $item->subProgram->program->name ?? 'N/A' }}
</span>
                                        <span class="text-gray-300">/</span>
                                        <span class="text-[8px] font-bold text-gray-400 uppercase tracking-widest italic">
                                            {{ $item->subProgram->title ?? 'GENERAL' }}
                                        </span>
                                    </div>

                                    {{-- Title & Timestamp --}}
                                    <div class="space-y-1">
                                        <h3 class="text-xl font-black text-gray-900 uppercase italic leading-none tracking-tight group-hover:text-[#800000] transition-colors">
                                            {{ $item->title }}
                                        </h3>
                                        <div class="flex items-center gap-2">
                                            <div class="w-1 h-1 rounded-full bg-gray-300"></div>
                                            <p class="text-[9px] text-gray-400 font-bold uppercase italic tracking-tighter">
                                                Active Since {{ $item->created_at->format('M d, Y') }} ({{ $item->created_at->diffForHumans() }})
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-10 py-8">
                                <div class="flex justify-center">
                                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-white rounded-xl border border-gray-100 shadow-sm group-hover:border-[#800000]/20 transition-all">
                                        <div class="w-1.5 h-1.5 rounded-full bg-[#800000] shadow-[0_0_8px_rgba(128,0,0,0.4)] animate-pulse"></div>
                                        <span class="text-[9px] font-black text-gray-600 uppercase italic tracking-widest">
                                            {{ $item->subProgram->template->name ?? 'Standard' }}
                                        </span>
                                    </div>
                                </div>
                            </td>

<td class="px-10 py-8">
    <div class="flex justify-end items-center gap-2">

        {{-- 1. MODULE BUILDER (Black) --}}
        <a href="{{ route('admin-program.subprogram.builder', $item->sub_program_id) }}"
           wire:navigate title="Manage Modules"
           class="p-3 bg-gray-100 text-gray-500 hover:text-white hover:bg-black rounded-xl transition-all hover:-translate-y-1 shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
        </a>

        {{-- 2. ISI CONTENTS (Blue) --}}
        <a href="{{ route('admin-program.subprogram.isicontents', $item->sub_program_id) }}"
           wire:navigate title="Fill Contents"
           class="p-3 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white rounded-xl transition-all hover:-translate-y-1 shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
            </svg>
        </a>

        {{-- 3. EDIT KELAS (Text Button) --}}
        <a href="{{ route('editkontenkelas', ['content' => $item->id]) }}"
           wire:navigate
           class="bg-gray-100 text-gray-600 px-4 py-3 rounded-xl font-black text-[9px] uppercase tracking-widest hover:bg-black hover:text-white transition-all shadow-sm">
            KELAS
        </a>

        {{-- 4. SETTINGS META (Text Button) --}}
        <a href="{{ route('modulkelas.edit', ['content' => $item->id]) }}"
           wire:navigate
           class="bg-gray-100 text-gray-600 px-4 py-3 rounded-xl font-black text-[9px] uppercase tracking-widest hover:bg-black hover:text-white transition-all shadow-sm">
            META
        </a>

        {{-- 5. ATTENDANCE CORE (Maroon) --}}
        <a href="{{ route('admin-program.absensi.manage', $item->slug) }}"
           wire:navigate title="Manage Attendance"
           class="p-3 bg-red-50 text-[#800000] hover:bg-[#800000] hover:text-white rounded-xl transition-all hover:-translate-y-1 shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </a>

        {{-- 6. CONTENT SETTINGS (Gear) --}}
        <a href="{{ route('admin-program.subprogram.content.edit', ['subProgram' => $item->sub_program_id, 'content' => $item->id]) }}"
           wire:navigate title="General Settings"
           class="p-3 bg-gray-100 text-gray-500 hover:text-black hover:bg-gray-200 rounded-xl transition-all hover:-translate-y-1 shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" stroke-width="2.5" stroke-linecap="round"/><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" stroke-width="2.5"/></svg>
        </a>

        {{-- 7. TRASH (Red) --}}
        <button wire:click="delete({{ $item->id }})"
                wire:confirm="Permanent delete this asset?"
                class="p-3 bg-white text-gray-300 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all border border-gray-100 hover:border-red-100 hover:-translate-y-1 shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>

    </div>
</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-10 py-32 text-center group">
                                <div class="flex flex-col items-center justify-center space-y-4">
                                    <div class="relative">
                                        <svg class="w-20 h-20 text-gray-100 group-hover:scale-110 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        <div class="absolute -top-1 -right-1 w-4 h-4 bg-[#800000] rounded-full border-4 border-white"></div>
                                    </div>
                                    <div class="space-y-1">
                                        <p class="text-[11px] font-black uppercase tracking-[0.3em] text-gray-300 italic">No assets found in your archive</p>
                                        <p class="text-[9px] font-bold text-gray-200 uppercase tracking-widest italic leading-none text-center">Try adjusting your search filters</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- 4. PAGINATION FOOTER --}}
        <div class="px-10 py-8 bg-gray-50/50 border-t border-gray-100">
            {{ $contents->links() }}
        </div>
    </div>
</div>
