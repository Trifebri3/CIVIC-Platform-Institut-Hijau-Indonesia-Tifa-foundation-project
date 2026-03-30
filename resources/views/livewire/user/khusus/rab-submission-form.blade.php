<?php

use App\Models\RabSubmission;
use App\Models\RabPeriod;
use Livewire\Volt\Component;

new class extends Component {
    public RabPeriod $period;
    public $submission;

    public $items = [];
    public $general_note = '';
    public $isPreview = false;
    public $total_requested = 0;

    public function mount(RabPeriod $period)
    {
        $this->period = $period;

        $this->submission = RabSubmission::where('user_id', auth()->id())
            ->where('rab_period_id', $this->period->id)
            ->first();

        if ($this->submission) {
            $this->items = is_array($this->submission->items)
                ? $this->submission->items
                : json_decode($this->submission->items, true);

            $this->general_note = $this->submission->general_note;
            $this->calculateTotal();

            if ($this->submission->status !== 'draft') {
                $this->isPreview = true;
            }
        } else {
            $this->addItem();
        }
    }

    public function addItem()
    {
        $newItem = [];
        foreach ($this->period->form_template as $column) {
            // Normalisasi key agar selalu string
            $key = is_array($column) ? ($column['name'] ?? array_values($column)[0]) : $column;
            $newItem[$key] = '';
        }
        $newItem['amount'] = 0;
        $this->items[] = $newItem;
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->calculateTotal();
    }

    public function calculateTotal()
    {
        $this->total_requested = array_sum(array_column($this->items, 'amount'));
    }

public function saveDraft()
{
    $this->calculateTotal();

    $this->submission = RabSubmission::updateOrCreate(
        ['user_id' => auth()->id(), 'rab_period_id' => $this->period->id],
        [
            'items' => $this->items,
            'total_requested' => $this->total_requested,
            'general_note' => $this->general_note,
            'status' => 'draft',
            // UBAH 0 MENJADI null
            'tor_submission_id' => null
        ]
    );

    session()->flash('message', 'Draft berhasil disimpan.');
}

    public function goToPreview()
    {
        $rules = [];
        foreach($this->period->form_template as $col) {
            $key = is_array($col) ? ($col['name'] ?? array_values($col)[0]) : $col;
            $rules["items.*.$key"] = 'required';
        }
        $rules["items.*.amount"] = 'required|numeric|min:1';

        $this->validate($rules, [
            'items.*.required' => 'Wajib diisi.',
            'items.*.amount.min' => 'Nominal minimal 1.'
        ]);

        $this->saveDraft();
        $this->isPreview = true;
    }

    public function submitFinal()
    {
        if (!$this->submission || $this->submission->status !== 'draft') return;
        $this->submission->update(['status' => 'pending']);
        session()->flash('message', 'RAB Berhasil dikirim!');
        return redirect()->route('user.dashboard');
    }
}; ?>

<div class="max-w-6xl mx-auto p-6">
    {{-- Alert --}}
    @if (session()->has('message'))
        <div class="mb-6 p-4 bg-emerald-50 text-emerald-700 rounded-2xl border border-emerald-100 font-black italic text-xs uppercase tracking-widest">
            {{ session('message') }}
        </div>
    @endif

    {{-- Header --}}
    <div class="mb-10 flex justify-between items-end">
        <div>
            <h1 class="text-3xl font-black italic uppercase text-slate-800 tracking-tighter">{{ $period->name }}</h1>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] mt-1">
                Status: <span class="{{ ($submission->status ?? '') === 'pending' ? 'text-emerald-500' : 'text-amber-500' }}">{{ $submission->status ?? 'NEW' }}</span>
            </p>
        </div>
        <div class="text-right">
            <p class="text-[9px] font-black text-slate-400 uppercase italic">Max Budget Limit</p>
            <p class="text-lg font-black text-slate-800 italic">Rp {{ number_format($period->max_total_budget, 0, ',', '.') }}</p>
        </div>
    </div>

    @if(!$isPreview)
        <div class="space-y-8">
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50/50 border-b border-gray-100">
                            <tr>
                                @foreach($period->form_template as $column)
                                    <th class="p-5 text-[10px] font-black uppercase italic text-slate-500 tracking-widest">
                                        @php
                                            $columnName = is_array($column) ? ($column['name'] ?? array_values($column)[0]) : $column;
                                        @endphp
                                        {{ str_replace('_', ' ', $columnName) }}
                                    </th>
                                @endforeach
                                <th class="p-5 text-[10px] font-black uppercase italic text-slate-500 tracking-widest w-44">Amount (Rp)</th>
                                <th class="p-5 w-16"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($items as $index => $item)
                                <tr class="group hover:bg-gray-50/30 transition-colors">
                                    @foreach($period->form_template as $column)
                                        @php
                                            // KUNCI PERBAIKAN: Ambil string key untuk wire:model
                                            $colKey = is_array($column) ? ($column['name'] ?? array_values($column)[0]) : $column;
                                        @endphp
                                        <td class="p-4">
                                            <input type="text"
                                                   wire:model.blur="items.{{ $index }}.{{ $colKey }}"
                                                   class="w-full border-none focus:ring-0 text-sm font-bold text-slate-700 placeholder:text-slate-300 placeholder:italic"
                                                   placeholder="...">
                                        </td>
                                    @endforeach
                                    <td class="p-4">
                                        <input type="number"
                                               wire:model.blur="items.{{ $index }}.amount"
                                               wire:change="calculateTotal"
                                               class="w-full border-none focus:ring-0 text-sm font-black text-[#800000]"
                                               placeholder="0">
                                    </td>
                                    <td class="p-4 text-center">
                                        @if(count($items) > 1)
                                        <button wire:click="removeItem({{ $index }})" class="text-gray-300 hover:text-red-500 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                                        </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <button wire:click="addItem" class="w-full py-5 bg-gray-50/50 text-[10px] font-black uppercase italic text-slate-400 hover:bg-gray-100 hover:text-slate-600 transition-all tracking-[0.3em]">
                    + Add New Row
                </button>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
                <div class="lg:col-span-2">
                    <label class="text-[10px] font-black uppercase italic text-slate-400 ml-4 mb-2 block">Catatan Tambahan</label>
                    <textarea wire:model.blur="general_note"
                              class="w-full rounded-[2rem] border-gray-100 focus:border-black focus:ring-0 text-sm p-6 shadow-sm"
                              rows="4" placeholder="Tulis catatan di sini..."></textarea>
                </div>

                <div class="bg-slate-900 rounded-[2.5rem] p-8 shadow-2xl shadow-slate-200 text-white">
                    <p class="text-[10px] font-black uppercase italic text-slate-400 tracking-widest mb-2">Total Pengajuan</p>
                    <p class="text-3xl font-black italic mb-8">Rp {{ number_format($total_requested, 0, ',', '.') }}</p>

                    <div class="space-y-3">
                        <button wire:click="goToPreview" class="w-full py-4 bg-white text-slate-900 rounded-2xl text-[10px] font-black uppercase italic tracking-widest hover:bg-emerald-400 transition-all">
                            Preview & Kirim
                        </button>
                        <button wire:click="saveDraft" class="w-full py-4 border border-slate-700 rounded-2xl text-[10px] font-black uppercase italic tracking-widest hover:bg-slate-800 transition-all">
                            Simpan Draft
                        </button>
                    </div>
                </div>
            </div>
        </div>

    @else
        {{-- PREVIEW MODE --}}
        <div class="bg-emerald-50 rounded-[3rem] p-10 border-2 border-emerald-100 relative overflow-hidden">
            <h2 class="text-2xl font-black italic text-emerald-900 uppercase mb-8 tracking-tighter">Ringkasan Pengajuan</h2>

            <div class="space-y-6 mb-10">
                @foreach($items as $item)
                    <div class="bg-white/50 p-6 rounded-[2rem] border border-emerald-100/50">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            @foreach($period->form_template as $col)
                                @php
                                    $colKey = is_array($col) ? ($col['name'] ?? array_values($col)[0]) : $col;
                                @endphp
                                <div>
                                    <p class="text-[8px] font-black uppercase text-emerald-600/60 tracking-widest mb-1">{{ str_replace('_', ' ', $colKey) }}</p>
                                    <p class="text-sm font-bold text-slate-800 italic">{{ $item[$colKey] ?: '-' }}</p>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4 pt-4 border-t border-emerald-100 flex justify-between items-center">
                            <span class="text-[9px] font-black text-emerald-600 uppercase italic">Subtotal</span>
                            <span class="text-sm font-black text-slate-900">Rp {{ number_format($item['amount'], 0, ',', '.') }}</span>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="flex flex-col md:flex-row justify-between items-center gap-6 border-t border-emerald-200 pt-8">
                <div>
                    <p class="text-[10px] font-black uppercase italic text-emerald-600">Total Akumulasi</p>
                    <p class="text-3xl font-black text-emerald-950 italic leading-none">Rp {{ number_format($total_requested, 0, ',', '.') }}</p>
                </div>

                @if(($submission->status ?? 'draft') === 'draft')
                    <div class="flex items-center gap-4">
                        <button wire:click="$set('isPreview', false)" class="text-[10px] font-black text-emerald-700 uppercase italic underline tracking-widest">
                            Edit Kembali
                        </button>
                        <button wire:click="submitFinal"
                                wire:confirm="Data yang sudah dikirim tidak bisa diubah kembali. Lanjutkan?"
                                class="px-10 py-5 bg-emerald-600 text-white rounded-[1.5rem] text-[10px] font-black uppercase italic tracking-[0.2em] shadow-xl shadow-emerald-200 hover:bg-emerald-700 transition-all">
                            Kirim ke Admin
                        </button>
                    </div>
                @else
                    <div class="px-8 py-4 bg-white/50 rounded-2xl border border-emerald-200">
                        <p class="text-[10px] font-black uppercase italic text-emerald-600 tracking-widest">Dokumen Terkunci & Terkirim</p>
                    </div>
                @endif
            </div>
        </div>
    @endif


    {{-- SECTION AKSES LAPORAN --}}

    @if($submission && $submission->status === 'approved')
    <div class="mb-10 bg-gradient-to-br from-[#800000] to-black rounded-[3rem] p-8 shadow-xl shadow-red-100 flex flex-col md:flex-row justify-between items-center gap-6 border-4 border-white">
        <div class="flex items-center gap-5">
            <div class="w-16 h-16 bg-white/10 rounded-[1.5rem] flex items-center justify-center backdrop-blur-md border border-white/20">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
            </div>
            <div>
                <h3 class="text-xl font-black italic uppercase text-white tracking-tighter">Laporan Pertanggungjawaban</h3>
                <p class="text-[9px] font-bold text-red-200 uppercase tracking-[0.2em] italic mt-1">Anggaran telah disetujui. Silakan lengkapi laporan kegiatan Anda.</p>
            </div>
        </div>

        <a href="{{ route('user.report.submit', $period->id) }}"
           class="px-10 py-5 bg-white text-slate-900 rounded-[1.5rem] text-[10px] font-black uppercase italic tracking-[0.2em] shadow-xl hover:bg-emerald-400 hover:text-white transition-all transform hover:scale-105">
            Isi Laporan Sekarang
        </a>
    </div>
@endif
</div>
