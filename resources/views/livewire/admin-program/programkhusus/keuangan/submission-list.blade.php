<?php

use Livewire\Volt\Component;
use App\Models\RabPeriod;
use App\Models\RabSubmission;

new class extends Component {
    public RabPeriod $period;

    public function with()
    {
        return [
            'submissions' => RabSubmission::where('rab_period_id', $this->period->id)
                ->with('user')
                ->latest()
                ->get(),
        ];
    }

public function updateStatus($id, $status)
{
    $submission = RabSubmission::find($id);

    if ($submission) {
        $submission->update(['status' => $status]);

        // Catatan: Jika Anda menggunakan 'notify', pastikan di app.js/layout
        // sudah ada listener untuk window.addEventListener('notify', ...)
        $this->dispatch('notify', message: 'Status Updated!', type: 'success');
    }
}
}; ?>

<div class="space-y-8">
    <div class="flex justify-between items-center px-4">
        <div>
            <h3 class="text-2xl font-black uppercase italic text-slate-800">
                Submissions: <span class="text-[#800000]">{{ $period->name }}</span>
            </h3>
            <p class="text-[9px] font-bold text-gray-400 uppercase italic tracking-widest">Monitoring pengajuan RAB mahasiswa</p>
        </div>

        {{-- Tombol Export Semua --}}
        <a href="{{ route('admin.program.keuangan.pdf.batch', $period->id) }}"
           class="bg-[#800000] text-white px-6 py-3 rounded-xl text-[9px] font-black uppercase italic hover:bg-black transition-all shadow-lg flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
            Export All to PDF (ZIP/Batch)
        </a>
    </div>

    <div class="bg-white rounded-[3rem] shadow-xl border border-gray-50 overflow-hidden">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-gray-50/50">
                    <th class="p-6 text-[9px] font-black uppercase italic text-gray-400">Mahasiswa</th>
                    <th class="p-6 text-[9px] font-black uppercase italic text-gray-400">Total Requested</th>
                    <th class="p-6 text-[9px] font-black uppercase italic text-gray-400 text-center">Status</th>
                    <th class="p-6 text-[9px] font-black uppercase italic text-gray-400 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($submissions as $sub)
                    <tr class="group hover:bg-gray-50/30 transition-all">
                        <td class="p-6">
                            <p class="text-xs font-black text-slate-800 uppercase italic">{{ $sub->user->name }}</p>
                            <p class="text-[8px] text-gray-400 font-bold uppercase tracking-tighter">{{ $sub->created_at->format('d M Y H:i') }}</p>
                        </td>
                        <td class="p-6">
                            <span class="text-xs font-black text-slate-700 italic">Rp {{ number_format($sub->total_requested, 0, ',', '.') }}</span>
                        </td>
                        <td class="p-6 text-center">
                            <select wire:change="updateStatus({{ $sub->id }}, $event.target.value)"
                                    class="text-[9px] font-black uppercase italic border-none rounded-lg bg-gray-100 focus:ring-2 focus:ring-[#800000]">
                                <option value="pending" @selected($sub->status == 'pending')>⏳ Pending</option>
                                <option value="approved" @selected($sub->status == 'approved')>✅ Approved</option>
                                <option value="rejected" @selected($sub->status == 'rejected')>❌ Rejected</option>
                            </select>
                        </td>
                        <td class="p-6 text-right">
                            <div class="flex justify-end gap-2">
                                {{-- Tombol View Detail --}}
                                <button class="p-2 bg-slate-100 rounded-lg hover:bg-black hover:text-white transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </button>

                                {{-- Tombol PDF Single --}}
                                <a href="{{ route('admin.program.keuangan.pdf.single', $sub->id) }}"
                                   class="p-2 bg-red-50 text-[#800000] rounded-lg hover:bg-[#800000] hover:text-white transition-all shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
