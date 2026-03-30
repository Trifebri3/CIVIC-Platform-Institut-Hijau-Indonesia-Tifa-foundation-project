<?php

use App\Models\TorSubmission;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $search = '';
    public $filterStatus = 'pending';

    // Fungsi untuk Update Status (ACC/Reject)
    public function updateStatus($id, $status)
    {
        $submission = TorSubmission::findOrFail($id);
        $submission->update([
            'status' => $status,
            'approved_at' => $status === 'approved' ? now() : null,
        ]);

        session()->flash('message', "Status TOR berhasil diubah menjadi " . strtoupper($status));
    }

    public function with()
    {
        return [
            'submissions' => TorSubmission::where('status', 'like', "%{$this->filterStatus}%")
                ->whereHas('user', function($q) {
                    $q->where('name', 'like', "%{$this->search}%");
                })
                ->latest()
                ->paginate(10),
        ];
    }
}; ?>

<div class="space-y-6">
    {{-- Header & Filter --}}
    <div class="flex flex-col md:flex-row justify-between items-end gap-4 bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm">
        <div class="w-full md:w-1/3">
            <label class="text-[10px] font-black uppercase italic text-slate-400 ml-2">Cari Nama User</label>
            <input type="text" wire:model.live="search" placeholder="Ketik nama..."
                   class="w-full mt-1 bg-gray-50 border-none rounded-xl text-sm font-bold p-3 focus:ring-2 focus:ring-black">
        </div>
        <div class="flex gap-2">
            @foreach(['pending', 'approved', 'rejected'] as $st)
                <button wire:click="$set('filterStatus', '{{ $st }}')"
                        class="px-4 py-2 rounded-xl text-[10px] font-black uppercase italic transition-all {{ $filterStatus == $st ? 'bg-black text-white' : 'bg-gray-100 text-gray-400 hover:bg-gray-200' }}">
                    {{ $st }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50/50 border-b border-gray-100">
                <tr>
                    <th class="p-6 text-[10px] font-black uppercase italic text-slate-500">User / Pengaju</th>
                    <th class="p-6 text-[10px] font-black uppercase italic text-slate-500">Judul TOR</th>
                    <th class="p-6 text-[10px] font-black uppercase italic text-slate-500">Status</th>
                    <th class="p-6 text-right text-[10px] font-black uppercase italic text-slate-500">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($submissions as $sub)
                    <tr class="group hover:bg-gray-50/50 transition-all">
                        <td class="p-6">
                            <p class="text-sm font-black text-slate-800 italic leading-none">{{ $sub->user->name }}</p>
                            <p class="text-[9px] font-bold text-gray-400 uppercase mt-1">{{ $sub->user->department ?? 'General' }}</p>
                        </td>
                        <td class="p-6">
                            <p class="text-sm font-bold text-slate-600 line-clamp-1">{{ $sub->title }}</p>
                            <a href="{{ storage_path($sub->file_path) }}" class="text-[9px] font-black text-blue-500 uppercase italic hover:underline">Download File PDF</a>
                        </td>
                        <td class="p-6">
                            @php
                                $color = [
                                    'pending' => 'bg-amber-100 text-amber-600',
                                    'approved' => 'bg-emerald-100 text-emerald-600',
                                    'rejected' => 'bg-red-100 text-red-600'
                                ][$sub->status];
                            @endphp
                            <span class="px-3 py-1 {{ $color }} rounded-lg text-[8px] font-black uppercase italic">{{ $sub->status }}</span>
                        </td>
                        <td class="p-6 text-right">
                            <div class="flex justify-end gap-2">
                                @if($sub->status !== 'approved')
                                    <button wire:click="updateStatus({{ $sub->id }}, 'approved')"
                                            class="w-10 h-10 flex items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </button>
                                @endif

                                @if($sub->status !== 'rejected')
                                    <button wire:click="updateStatus({{ $sub->id }}, 'rejected')"
                                            wire:confirm="Tolak pengajuan ini?"
                                            class="w-10 h-10 flex items-center justify-center rounded-xl bg-red-50 text-red-400 hover:bg-red-500 hover:text-white transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-20 text-center italic text-gray-300 font-black uppercase text-[10px] tracking-widest">Tidak ada pengajuan ditemukan</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 italic">
        {{ $submissions->links() }}
    </div>
</div>
