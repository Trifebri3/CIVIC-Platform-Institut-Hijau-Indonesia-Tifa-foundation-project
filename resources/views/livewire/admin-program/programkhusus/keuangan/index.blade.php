<?php

use Livewire\Volt\Component;
use App\Models\RabPeriod;

new class extends Component {
    public function delete($id)
    {
        RabPeriod::find($id)->delete();
        session()->flash('success', 'Program berhasil dihapus!');
    }

    public function with()
    {
        return [
            'periods' => RabPeriod::latest()->get(),
        ];
    }
}; ?>

<div class="max-w-7xl mx-auto py-10 px-4">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-12 px-4">
        <div>
            <h1 class="text-4xl font-black uppercase italic tracking-tighter text-slate-800">
                Financial <span class="text-[#800000]">Programs</span>
            </h3>
            <p class="text-[10px] font-bold text-gray-400 uppercase italic tracking-widest mt-2">
                Manajemen Periode Anggaran & Template RAB Mahasiswa
            </p>
        </div>

        <a href="{{ route('admin.program.keuangan.create') }}"
           class="bg-black text-white px-8 py-4 rounded-2xl text-[10px] font-black uppercase italic hover:bg-[#800000] transition-all shadow-xl shadow-red-900/10 flex items-center gap-3">
            <span>+ Create New Program</span>
        </a>
    </div>

    {{-- Stats Mini --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm">
            <p class="text-[9px] font-black text-gray-400 uppercase italic">Total Program</p>
            <p class="text-2xl font-black text-slate-800 italic">{{ $periods->count() }}</p>
        </div>
        <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm">
            <p class="text-[9px] font-black text-gray-400 uppercase italic">Active Now</p>
            <p class="text-2xl font-black text-emerald-500 italic">{{ $periods->where('is_active', true)->count() }}</p>
        </div>
        <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm">
            <p class="text-[9px] font-black text-gray-400 uppercase italic">Total Budget Plafon</p>
            <p class="text-2xl font-black text-[#800000] italic">Rp {{ number_format($periods->sum('max_total_budget'), 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Table Section --}}
    <div class="bg-white rounded-[3rem] shadow-xl border border-gray-50 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/50">
                    <th class="p-6 text-[10px] font-black uppercase italic text-gray-400 tracking-widest">Program Name</th>
                    <th class="p-6 text-[10px] font-black uppercase italic text-gray-400 tracking-widest">Timeline</th>
                    <th class="p-6 text-[10px] font-black uppercase italic text-gray-400 tracking-widest">Max Budget</th>
                    <th class="p-6 text-[10px] font-black uppercase italic text-gray-400 tracking-widest">Status</th>
                    <th class="p-6 text-right"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($periods as $period)
                    <tr class="group hover:bg-gray-50/50 transition-colors">
                        <td class="p-6">
                            <p class="text-sm font-black text-slate-800 uppercase italic leading-none">{{ $period->name }}</p>
                            <p class="text-[9px] font-bold text-gray-400 uppercase mt-2 tracking-tighter line-clamp-1 italic">
                                {{ count($period->form_template) }} Custom Columns Defined
                            </p>
                        </td>
                        <td class="p-6">
                            <div class="flex flex-col gap-1">
                                <span class="text-[9px] font-black text-slate-600 italic uppercase">S: {{ $period->start_at->format('d/m/y H:i') }}</span>
                                <span class="text-[9px] font-black text-red-400 italic uppercase">E: {{ $period->end_at->format('d/m/y H:i') }}</span>
                            </div>
                        </td>
                        <td class="p-6">
                            <span class="text-sm font-black text-slate-800 italic">Rp {{ number_format($period->max_total_budget, 0, ',', '.') }}</span>
                        </td>
                        <td class="p-6">
                            @if($period->isOpen())
                                <span class="px-4 py-1.5 bg-emerald-100 text-emerald-600 rounded-lg text-[8px] font-black uppercase italic tracking-widest">Open</span>
                            @else
                                <span class="px-4 py-1.5 bg-gray-100 text-gray-400 rounded-lg text-[8px] font-black uppercase italic tracking-widest">Closed</span>
                            @endif
                        </td>
                        <td class="p-6 text-right">
                            <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
{{-- Tombol Edit di Index Table --}}
<a href="{{ route('admin.program.keuangan.edit', $period->id) }}"
   title="Edit Program"
   class="w-10 h-10 flex items-center justify-center rounded-2xl bg-gray-50 text-slate-400 hover:bg-black hover:text-white transition-all duration-300 shadow-sm">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
    </svg>
</a>

<a href="{{ route('admin.program.keuangan.submissions', $period->id) }}"
   title="View Details"
   class="w-10 h-10 flex items-center justify-center rounded-2xl bg-gray-50 text-slate-400 hover:bg-blue-600 hover:text-white transition-all duration-300 shadow-sm">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
    </svg>
</a>

{{-- Tombol untuk Mengelola Template Laporan per Periode --}}
<a href="{{ route('admin.report-template.builder', $period->id) }}"
   class="group relative flex items-center justify-center w-10 h-10 bg-slate-100 rounded-xl hover:bg-[#800000] transition-all duration-300 shadow-sm"
   title="Configure Report Template">

    {{-- Icon Form/Template --}}
    <svg class="w-5 h-5 text-slate-500 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
    </svg>

    {{-- Tooltip (Opsional) --}}
    <span class="absolute -top-10 scale-0 group-hover:scale-100 transition-all bg-black text-white text-[9px] font-black uppercase italic px-3 py-1 rounded-lg">
        Set Template
    </span>
</a>
                                <button wire:click="delete({{ $period->id }})"
                                        wire:confirm="Yakin ingin menghapus program ini?"
                                        title="Delete"
                                        class="w-8 h-8 flex items-center justify-center rounded-xl bg-gray-100 text-red-300 hover:bg-red-500 hover:text-white transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-20 text-center">
                            <p class="text-[10px] font-black uppercase italic text-gray-300 tracking-[0.3em]">No Financial Program Found</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
