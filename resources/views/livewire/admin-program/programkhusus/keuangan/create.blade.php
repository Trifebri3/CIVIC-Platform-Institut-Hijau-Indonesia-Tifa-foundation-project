<?php

use Livewire\Volt\Component;
use App\Models\RabPeriod;

new class extends Component {
    public $name;
    public $description;
    public $max_total_budget = 0;
    public $start_at;
    public $end_at;
    public $is_active = true;

    // Tambahkan field 'max_value' di default columns
    public $columns = [
        ['label' => 'Uraian Kegiatan', 'type' => 'text', 'required' => true, 'max_value' => null],
        ['label' => 'Volume', 'type' => 'number', 'required' => true, 'max_value' => null],
        ['label' => 'Satuan', 'type' => 'text', 'required' => true, 'max_value' => null],
        ['label' => 'Harga Satuan', 'type' => 'number', 'required' => true, 'max_value' => null],
    ];

    public function addColumn()
    {
        // Masukkan ke dalam array columns dengan field max_value
        $this->columns[] = [
            'label' => '',
            'type' => 'text',
            'required' => false,
            'max_value' => null
        ];
    }

    public function removeColumn($index)
    {
        unset($this->columns[$index]);
        $this->columns = array_values($this->columns);
    }

    public function save()
    {
        // VALIDASI HARUS DI DALAM SINI BOS!
        $this->validate([
            'name' => 'required|min:5',
            'max_total_budget' => 'required|numeric|min:0',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after:start_at',
            'columns.*.label' => 'required',
            'columns.*.max_value' => 'nullable|numeric|min:0', // <-- Pindah ke sini
        ]);

        RabPeriod::create([
            'name' => $this->name,
            'description' => $this->description,
            'form_template' => $this->columns,
            'max_total_budget' => $this->max_total_budget,
            'start_at' => $this->start_at,
            'end_at' => $this->end_at,
            'is_active' => $this->is_active,
        ]);

        session()->flash('success', 'Periode Anggaran Berhasil Dibuat!');
        return redirect()->route('admin.program.keuangan.index');
    }
}; ?>

<div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- Kiri: Basic Info & Timeline --}}
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white p-8 rounded-[3rem] shadow-xl border border-gray-50">
                <h3 class="text-xl font-black uppercase italic tracking-tighter text-slate-800 mb-6">
                    Program <span class="text-[#800000]">Settings</span>
                </h3>

                <div class="space-y-4">
                    <div>
                        <label class="text-[10px] font-black uppercase italic text-gray-400">Nama Periode RAB</label>
                        <input type="text" wire:model="name" class="w-full bg-gray-50 border-none rounded-2xl p-4 text-sm font-bold focus:ring-2 focus:ring-[#800000]">
                    </div>

                    <div>
                        <label class="text-[10px] font-black uppercase italic text-gray-400">Plafon Anggaran (Vakum)</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 font-black text-gray-300 text-xs">RP</span>
                            <input type="number" wire:model="max_total_budget" class="w-full bg-gray-50 border-none rounded-2xl p-4 pl-12 text-sm font-bold focus:ring-2 focus:ring-[#800000]">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-[10px] font-black uppercase italic text-gray-400">Mulai</label>
                            <input type="datetime-local" wire:model="start_at" class="w-full bg-gray-50 border-none rounded-xl p-3 text-xs font-bold focus:ring-2 focus:ring-[#800000]">
                        </div>
                        <div>
                            <label class="text-[10px] font-black uppercase italic text-gray-400">Selesai</label>
                            <input type="datetime-local" wire:model="end_at" class="w-full bg-gray-50 border-none rounded-xl p-3 text-xs font-bold focus:ring-2 focus:ring-[#800000]">
                        </div>
                    </div>

                    <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-2xl mt-4">
                        <input type="checkbox" wire:model="is_active" class="rounded text-[#800000] focus:ring-[#800000]">
                        <span class="text-[10px] font-black uppercase italic text-slate-600">Publikasikan Sekarang</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kanan: Custom Template Builder --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white p-8 rounded-[3rem] shadow-xl border border-gray-50">
                <div class="flex justify-between items-center mb-8">
                    <div>
                        <h3 class="text-xl font-black uppercase italic tracking-tighter text-slate-800">
                            RAB <span class="text-[#800000]">Template Builder</span>
                        </h3>
                        <p class="text-[9px] font-bold text-gray-400 uppercase italic">Tentukan kolom apa saja yang harus diisi oleh mahasiswa</p>
                    </div>
                    <button wire:click="addColumn" class="bg-black text-white px-6 py-3 rounded-xl text-[9px] font-black uppercase italic hover:bg-[#800000] transition-all">
                        + Tambah Kolom
                    </button>
                </div>

                <div class="space-y-3">
                    <div class="grid grid-cols-12 gap-4 px-4 py-2 bg-[#FAFAFA] rounded-xl">
   <div class="col-span-4 text-[9px] font-black uppercase italic text-gray-400">Label Kolom</div>
    <div class="col-span-3 text-[9px] font-black uppercase italic text-gray-400">Tipe Data</div>
    <div class="col-span-2 text-[9px] font-black uppercase italic text-gray-400">Max Limit</div> {{-- Kolom Baru --}}
    <div class="col-span-2 text-[9px] font-black uppercase italic text-gray-400 text-center">Wajib?</div>
    <div class="col-span-1"></div>
                    </div>

                    @foreach($columns as $index => $column)
    <div class="grid grid-cols-12 gap-4 items-center p-2 group hover:bg-gray-50 rounded-2xl transition-all">
        {{-- Label --}}
        <div class="col-span-4">
            <input type="text" wire:model="columns.{{ $index }}.label" placeholder="Nama Kolom..."
                   class="w-full bg-white border-gray-100 rounded-xl p-3 text-xs font-bold focus:ring-1 focus:ring-[#800000]">
        </div>

        {{-- Type --}}
        <div class="col-span-3">
            <select wire:model="columns.{{ $index }}.type" class="w-full bg-white border-gray-100 rounded-xl p-3 text-[10px] font-bold focus:ring-1 focus:ring-[#800000]">
                <option value="text">Teks Biasa</option>
                <option value="number">Angka / Nominal</option>
                <option value="date">Tanggal</option>
            </select>
        </div>

        {{-- Max Limit (Hanya Muncul/Aktif jika tipe data adalah Number) --}}
        <div class="col-span-2">
            @if($columns[$index]['type'] === 'number')
                <input type="number" wire:model="columns.{{ $index }}.max_value" placeholder="Limit..."
                       class="w-full bg-red-50/50 border-dashed border-red-100 rounded-xl p-3 text-xs font-black text-[#800000] focus:ring-1 focus:ring-[#800000]">
            @else
                <div class="w-full p-3 text-[8px] text-gray-300 italic uppercase font-bold">N/A</div>
            @endif
        </div>

        {{-- Required --}}
        <div class="col-span-2 text-center">
            <input type="checkbox" wire:model="columns.{{ $index }}.required" class="rounded text-[#800000]">
        </div>

        {{-- Action --}}
        <div class="col-span-1 text-right">
            <button wire:click="removeColumn({{ $index }})" class="text-gray-300 hover:text-red-500 transition-colors text-xl">×</button>
        </div>
    </div>
@endforeach
                </div>

                <div class="mt-12 pt-8 border-t border-gray-50">
                    <button wire:click="save" class="w-full bg-black text-white py-6 rounded-[2rem] text-xs font-black uppercase italic hover:bg-[#800000] transition-all shadow-2xl shadow-red-900/10">
                        Create Financial Program —
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
