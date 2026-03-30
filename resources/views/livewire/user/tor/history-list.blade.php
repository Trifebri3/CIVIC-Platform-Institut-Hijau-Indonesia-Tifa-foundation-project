<?php

use Livewire\Volt\Component;
use App\Models\TorSubmission;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public function with()
    {
        return [
            'submissions' => TorSubmission::where('user_id', auth()->id())
                ->with('period')
                ->latest()
                ->paginate(10)
        ];
    }
}; ?>

<div class="space-y-8">
    {{-- List Card --}}
    <div class="bg-white rounded-[3rem] shadow-xl border border-gray-50 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-gray-50 bg-[#FAFAFA]">
                    <th class="p-8 text-[10px] font-black uppercase italic text-gray-400 tracking-widest">Periode / Program</th>
                    <th class="p-8 text-[10px] font-black uppercase italic text-gray-400 tracking-widest text-center">Tanggal Kirim</th>
                    <th class="p-8 text-[10px] font-black uppercase italic text-gray-400 tracking-widest text-center">Status</th>
                    <th class="p-8 text-[10px] font-black uppercase italic text-gray-400 tracking-widest text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($submissions as $sub)
                    <tr class="hover:bg-gray-50/50 transition-all group">
                        <td class="p-8">
                            <p class="text-xs font-black uppercase italic text-slate-700 leading-none group-hover:text-[#800000] transition-colors">
                                {{ $sub->period->name }}
                            </p>
                            <p class="text-[9px] font-bold text-gray-300 uppercase mt-1 italic tracking-tighter">
                                ID Pengajuan: #TOR-{{ str_pad($sub->id, 5, '0', STR_PAD_LEFT) }}
                            </p>
                        </td>
                        <td class="p-8 text-center text-[10px] font-bold text-slate-500 italic uppercase">
                            {{ $sub->created_at->format('d M Y') }}
                            <span class="block text-[8px] text-gray-300">{{ $sub->created_at->format('H:i') }} WIB</span>
                        </td>
                        <td class="p-8 text-center">
                            @php
                                $statusMap = [
                                    'pending' => 'bg-amber-50 text-amber-500 border-amber-100',
                                    'approved' => 'bg-emerald-50 text-emerald-500 border-emerald-100',
                                    'rejected' => 'bg-red-50 text-red-500 border-red-100',
                                    'revision' => 'bg-blue-50 text-blue-500 border-blue-100',
                                ];
                            @endphp
                            <span class="px-5 py-2 border {{ $statusMap[$sub->status] ?? 'bg-gray-50 text-gray-400' }} rounded-xl text-[9px] font-black uppercase italic tracking-widest">
                                {{ $sub->status }}
                            </span>
                        </td>
                        <td class="p-8 text-right">
                            <div class="flex justify-end items-center gap-4">
                                {{-- Tombol Download PDF --}}
                                <a href="{{ route('admin.tor.download', $sub->id) }}" target="_blank"
                                   class="px-6 py-3 bg-black text-white text-[9px] font-black uppercase italic rounded-2xl hover:bg-[#800000] transition-all shadow-lg shadow-black/5">
                                    Unduh PDF —
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-20 text-center">
                            <div class="flex flex-col items-center">
                                <span class="text-4xl mb-4">📂</span>
                                <p class="text-[11px] font-black uppercase italic text-gray-300 tracking-[0.2em]">Belum Ada Riwayat Pengajuan</p>
                                <a href="{{ route('user.dashboard') }}" class="mt-6 text-[9px] font-black uppercase italic text-[#800000] underline underline-offset-4">Kembali ke Beranda</a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        <div class="p-8 border-t border-gray-50">
            {{ $submissions->links() }}
        </div>
    </div>
</div>
