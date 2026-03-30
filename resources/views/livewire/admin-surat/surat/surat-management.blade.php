<?php

use App\Models\SuratSubmission;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $selectedSurat;
    public $nomor_surat, $tanggal_surat, $admin_note;
    public $isModalOpen = false;

    public function openApproveModal($id)
    {
        $this->selectedSurat = SuratSubmission::findOrFail($id);
        $this->nomor_surat = $this->selectedSurat->nomor_surat;
        $this->tanggal_surat = now()->format('Y-m-d');
        $this->isModalOpen = true;
    }

    public function approve()
    {
        $this->validate([
            'nomor_surat' => 'required|string',
            'tanggal_surat' => 'required|date',
        ]);

        $this->selectedSurat->update([
            'nomor_surat' => $this->nomor_surat,
            'tanggal_surat' => $this->tanggal_surat,
            'status' => 'approved',
            'processed_by' => auth()->id(),
        ]);

        $this->isModalOpen = false;
        session()->flash('message', 'Surat Berhasil Disetujui!');
    }

    public function reject($id)
    {
        $surat = SuratSubmission::findOrFail($id);
        $surat->update(['status' => 'rejected', 'processed_by' => auth()->id()]);
        session()->flash('error', 'Surat Ditolak.');
    }

    public function with()
    {
        return [
            'submissions' => SuratSubmission::with('user')->latest()->paginate(10),
        ];
    }
}; ?>

<div class="max-w-7xl mx-auto py-10 px-4">
<div class="flex justify-between items-center mb-10 bg-black p-8 rounded-[3rem] text-white shadow-2xl">
    <div>
        <h1 class="text-3xl font-black italic uppercase leading-none">Manajemen Surat</h1>
        <p class="text-[10px] font-bold opacity-60 mt-2 uppercase tracking-[0.3em]">Institut Hijau Indonesia • Admin Panel</p>
    </div>
    <div class="flex gap-2">
        {{-- TOMBOL REKAP --}}
        <a href="{{ route('admin.surat.export.excel') }}" class="bg-emerald-600 hover:bg-emerald-700 px-6 py-3 rounded-2xl text-[10px] font-black uppercase italic transition-all">
            Export Excel
        </a>
        <a href="{{ route('admin.surat.export.pdf') }}" target="_blank" class="bg-[#800000] hover:bg-red-900 px-6 py-3 rounded-2xl text-[10px] font-black uppercase italic transition-all">
            Export PDF
        </a>
    </div>
</div>

    <div class="bg-white rounded-[3rem] shadow-sm border border-slate-100 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-100">
                    <th class="p-6 text-[10px] font-black uppercase text-slate-400 italic">Pengaju / Wilayah</th>
                    <th class="p-6 text-[10px] font-black uppercase text-slate-400 italic">Detail Agenda</th>
                    <th class="p-6 text-[10px] font-black uppercase text-slate-400 italic">Lampiran TOR</th>
                    <th class="p-6 text-[10px] font-black uppercase text-slate-400 italic text-center">Status</th>
                    <th class="p-6 text-[10px] font-black uppercase text-slate-400 italic text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @foreach($submissions as $s)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="p-6">
                        <div class="font-black text-sm text-slate-800 uppercase italic">{{ $s->user->name }}</div>
                        <div class="text-[10px] font-bold text-[#800000] uppercase">FGD: {{ $s->wilayah_kegiatan }}</div>
                    </td>
                    <td class="p-6">
                        <div class="text-xs font-bold text-slate-600">{{ $s->hari_tanggal }}</div>
                        <div class="text-[10px] font-medium text-slate-400 uppercase italic">{{ $s->tempat_pelaksanaan }}</div>
                    </td>
                    <td class="p-6">
                        @if($s->admin_note) {{-- Kita pakai admin_note untuk simpan path file --}}
                        <a href="{{ asset('storage/' . $s->admin_note) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 rounded-xl text-[10px] font-black uppercase text-slate-600 hover:bg-black hover:text-white transition-all">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" stroke-width="2.5"></path></svg>
                            Lihat Dokumen
                        </a>
                        @else
                        <span class="text-[10px] font-bold text-slate-300 italic">Tanpa Lampiran</span>
                        @endif
                    </td>
                    <td class="p-6 text-center">
                        <span class="px-4 py-2 rounded-full border-2 {{ $s->status_badge }} text-[9px] font-black uppercase italic">
                            {{ $s->status }}
                        </span>
                    </td>
                    <td class="p-6 text-right space-x-2">
                        @if($s->status === 'pending')
                            <button wire:click="openApproveModal({{ $s->id }})" class="bg-[#800000] text-white px-5 py-2 rounded-xl text-[10px] font-black uppercase italic shadow-lg shadow-red-900/20 hover:scale-105 transition-all">Approve</button>
                            <button onclick="confirm('Tolak surat ini?') || event.stopImmediatePropagation()" wire:click="reject({{ $s->id }})" class="bg-slate-200 text-slate-500 px-5 py-2 rounded-xl text-[10px] font-black uppercase italic hover:bg-red-500 hover:text-white transition-all">Reject</button>
                        @else
                            <a href="{{ route('user.surat.download', $s->id) }}" target="_blank" class="bg-black text-white px-5 py-2 rounded-xl text-[10px] font-black uppercase italic">Preview PDF</a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-6 bg-slate-50">
            {{ $submissions->links() }}
        </div>
    </div>

    @if($isModalOpen)
    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
        <div class="bg-white w-full max-w-md rounded-[3rem] p-10 shadow-2xl relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-2 bg-[#800000]"></div>

            <h2 class="text-2xl font-black italic uppercase text-slate-800 mb-6">Penomoran Surat</h2>

            <div class="space-y-4">
                <div>
                    <label class="text-[10px] font-black uppercase text-slate-400 ml-2 italic">Input Nomor Surat Resmi</label>
                    <input type="text" wire:model="nomor_surat" placeholder="Contoh: CE/001/UND/III/2026" class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl p-4 text-sm font-bold focus:border-[#800000] outline-none">
                    @error('nomor_surat') <span class="text-[9px] text-red-500 font-bold ml-2">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="text-[10px] font-black uppercase text-slate-400 ml-2 italic">Tanggal Surat</label>
                    <input type="date" wire:model="tanggal_surat" class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl p-4 text-sm font-bold focus:border-[#800000] outline-none">
                </div>

                <div class="flex gap-3 pt-4">
                    <button wire:click="$set('isModalOpen', false)" class="flex-1 py-4 bg-slate-100 text-slate-500 rounded-2xl font-black uppercase italic text-xs">Batal</button>
                    <button wire:click="approve" class="flex-1 py-4 bg-[#800000] text-white rounded-2xl font-black uppercase italic text-xs shadow-lg shadow-red-900/20">Simpan & Approve</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
