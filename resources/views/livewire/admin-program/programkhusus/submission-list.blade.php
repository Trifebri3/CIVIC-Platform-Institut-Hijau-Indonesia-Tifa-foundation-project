<?php

use Livewire\Volt\Component;
use App\Models\TorSubmission; // Asumsi nama model submission
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $search = '';
    public $selectedPeriod = '';

    public function with()
    {
        return [
            'submissions' => TorSubmission::with(['user', 'period'])
                ->when($this->selectedPeriod, fn($q) => $q->where('tor_period_id', $this->selectedPeriod))
                ->whereHas('user', fn($q) => $q->where('name', 'like', "%{$this->search}%"))
                ->latest()
                ->paginate(10),
            'periods' => \App\Models\TorPeriod::all()
        ];
    }
}; ?>

<div class="p-8 bg-[#FAFAFA] min-h-screen">
    <div class="max-w-7xl mx-auto space-y-10">
        {{-- HEADER --}}
        <div class="flex justify-between items-end">
            <div>
                <h1 class="text-4xl font-black uppercase italic tracking-tighter text-slate-800">User <span class="text-[#800000]">Submissions</span></h1>
                <p class="text-[10px] font-bold text-gray-400 uppercase italic tracking-widest mt-2">Review & Download TOR Documents</p>
            </div>
        </div>

        {{-- FILTER --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100">
            <input type="text" wire:model.live="search" placeholder="Search Student Name..." class="bg-gray-50 border-none rounded-xl text-[10px] font-bold p-4">
            <select wire:model.live="selectedPeriod" class="bg-gray-50 border-none rounded-xl text-[10px] font-bold">
                <option value="">All Periods</option>
                @foreach($periods as $p)
                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- TABLE --}}
        <div class="bg-white rounded-[3rem] shadow-xl border border-gray-50 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-50">
                        <th class="p-6 text-[10px] font-black uppercase italic text-gray-400">Student / User</th>
                        <th class="p-6 text-[10px] font-black uppercase italic text-gray-400">Period</th>
                        <th class="p-6 text-[10px] font-black uppercase italic text-gray-400">Submitted At</th>
                        <th class="p-6 text-[10px] font-black uppercase italic text-gray-400 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($submissions as $s)
                        <tr class="hover:bg-gray-50/50 transition-all">
                            <td class="p-6">
                                <p class="text-xs font-black uppercase italic text-slate-700">{{ $s->user->name }}</p>
                                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter italic">{{ $s->user->email }}</p>
                            </td>
                            <td class="p-6 text-[10px] font-bold text-slate-500 italic uppercase">{{ $s->period->name }}</td>
                            <td class="p-6 text-[10px] font-bold text-slate-400 uppercase">{{ $s->created_at->format('d M Y, H:i') }}</td>
                            <td class="p-6 text-right">
                                <div class="flex justify-end gap-2">
                                    {{-- TOMBOL DOWNLOAD KE CONTROLLER --}}
                                    <a href="{{ route('admin.tor.download', $s->id) }}" target="_blank"
                                       class="px-5 py-2 bg-black text-white text-[9px] font-black uppercase italic rounded-xl hover:bg-[#800000] transition-all">
                                        Download PDF —
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="p-6 border-t border-gray-50">
                {{ $submissions->links() }}
            </div>
        </div>
    </div>
</div>
