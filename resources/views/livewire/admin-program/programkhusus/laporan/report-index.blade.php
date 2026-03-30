<?php

use App\Models\ProgramReport;
use App\Models\RabPeriod;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $selectedPeriod;
    public $search = '';

    // State untuk Modal Approval
    public $editingReportId;
    public $adminNote; // Tetap pakai camelCase untuk variable Livewire tidak apa-apa
    public $showModal = false;

    public function mount()
    {
        $activePeriod = RabPeriod::where('is_active', true)->first();
        $this->selectedPeriod = $activePeriod ? $activePeriod->id : null;
    }

    public function openApproval($id)
    {
        // Gunakan findOrFail supaya kalau ID tidak ada, tidak null pointer
        $report = ProgramReport::findOrFail($id);
        $this->editingReportId = $id;

        // PASTI KAN panggil sesuai nama kolom di DB: admin_note
        $this->adminNote = $report->admin_note;
        $this->showModal = true;
    }

    public function updateStatus($status)
    {
        $report = ProgramReport::findOrFail($this->editingReportId);

        $report->update([
            'status' => $status,
            'admin_note' => $this->adminNote, // Simpan kembali ke kolom admin_note
            // Submit date diupdate hanya jika approved
            'submitted_at' => $status == 'approved' ? now() : $report->submitted_at
        ]);

        $this->showModal = false;
        $this->dispatch('notify', message: 'Status Laporan Diperbarui!', type: 'success');
    }

    public function with()
    {
        return [
            'periods' => RabPeriod::latest()->get(),
            // Eager load user.programs karena kita butuh nama program dari sana
            'reports' => ProgramReport::with(['user.programs', 'period'])
                ->when($this->selectedPeriod, fn($q) => $q->where('rab_period_id', $this->selectedPeriod))
                ->whereHas('user', fn($q) => $q->where('name', 'like', "%{$this->search}%"))
                ->latest()
                ->paginate(10)
        ];
    }
}; ?>

<div class="space-y-6">
    {{-- Header & Filter --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 px-4">
        <div>
            <h3 class="text-2xl font-black uppercase italic text-slate-800 tracking-tighter">
                Program <span class="text-[#800000]">Reports</span>
            </h3>
            <p class="text-[9px] font-bold text-gray-400 uppercase italic tracking-widest">Verifikasi Laporan Pertanggungjawaban Mahasiswa</p>
        </div>

        <div class="flex flex-wrap gap-3">
            <select wire:model.live="selectedPeriod" class="text-[10px] font-black uppercase italic border-none rounded-xl bg-white shadow-sm focus:ring-2 focus:ring-[#800000]">
                <option value="">Semua Periode</option>
                @foreach($periods as $p)
                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                @endforeach
            </select>
            <input type="text" wire:model.live="search" placeholder="Cari Mahasiswa..."
                   class="text-[10px] font-black uppercase italic border-none rounded-xl bg-white shadow-sm focus:ring-2 focus:ring-[#800000]">
        </div>
    </div>

    {{-- Table --}}
 {{-- Table --}}
    <div class="bg-white rounded-[3rem] shadow-xl border border-gray-50 overflow-hidden">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-gray-50/50 text-[9px] font-black uppercase italic text-gray-400">
                    <th class="p-6">Mahasiswa & Program</th>
                    <th class="p-6">Periode</th>
                    <th class="p-6 text-center">Status</th>
                    <th class="p-6 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($reports as $report)
                    <tr class="group hover:bg-gray-50/30 transition-all">
                        <td class="p-6">
                            <p class="text-xs font-black text-slate-800 uppercase italic">{{ $report->user->name }}</p>
                            {{-- FIX: Ambil program dari relasi user, bukan profile --}}
                            <p class="text-[8px] text-[#800000] font-bold uppercase">
                                {{ $report->user->programs->first()->name ?? 'PROGRAM UMUM' }}
                            </p>
                        </td>
                        <td class="p-6">
                            <span class="text-[10px] font-bold text-slate-500 uppercase italic">{{ $report->period->name }}</span>
                        </td>
                        <td class="p-6 text-center">
                            <span class="px-4 py-1 rounded-full text-[8px] font-black uppercase italic
                                {{ $report->status == 'approved' ? 'bg-emerald-100 text-emerald-600' : ($report->status == 'rejected' ? 'bg-red-100 text-red-600' : 'bg-amber-100 text-amber-600') }}">
                                {{ $report->status }}
                            </span>
                        </td>
                        <td class="p-6 text-right">
                            <div class="flex justify-end gap-2">
                                {{-- Preview PDF --}}
                                <a href="{{ route('admin.program-report.pdf', $report->id) }}" target="_blank"
                                   class="p-2 bg-slate-100 rounded-lg hover:bg-black hover:text-white transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-width="2"></path></svg>
                                </a>
                                <button wire:click="openApproval({{ $report->id }})"
                                        class="px-4 py-2 bg-[#800000] text-white rounded-xl text-[9px] font-black uppercase italic hover:bg-black transition-all">
                                    Review
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-6">{{ $reports->links() }}</div>
    </div>

    {{-- Modal Approval --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
            <div class="bg-white rounded-[3rem] w-full max-w-lg overflow-hidden shadow-2xl">
                <div class="p-10 space-y-6">
                    <h3 class="text-xl font-black uppercase italic text-slate-800">Action <span class="text-[#800000]">Required</span></h3>

                    <div>
                        <label class="text-[10px] font-black uppercase italic text-slate-400 ml-2">Admin Note / Revision Reason</label>
                        <textarea wire:model="adminNote" class="w-full mt-2 rounded-2xl border-gray-100 bg-gray-50 p-4 text-sm font-bold text-slate-700" rows="4" placeholder="Tulis catatan di sini..."></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4 pt-4">
                        <button wire:click="updateStatus('rejected')" class="py-4 bg-red-50 text-red-600 rounded-2xl font-black uppercase italic text-[10px] hover:bg-red-600 hover:text-white transition-all">
                            ❌ Reject / Revision
                        </button>
                        <button wire:click="updateStatus('approved')" class="py-4 bg-emerald-500 text-white rounded-2xl font-black uppercase italic text-[10px] hover:bg-black transition-all shadow-lg shadow-emerald-100">
                            ✅ Approve Report
                        </button>
                    </div>
                    <button wire:click="$set('showModal', false)" class="w-full text-[9px] font-black uppercase italic text-slate-300 hover:text-slate-500 transition-all">Close Window</button>
                </div>
            </div>
        </div>
    @endif
</div>
