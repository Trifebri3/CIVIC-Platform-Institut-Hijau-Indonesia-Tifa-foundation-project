<?php

use Livewire\Volt\Component;
use App\Models\RabPeriod;

new class extends Component {
    public RabPeriod $period; // Menerima object model dari route/page

    // Form State
    public $name;
    public $description;
    public $max_total_budget;
    public $start_at;
    public $end_at;
    public $is_active;
    public $columns = [];

    public function mount(RabPeriod $period)
    {
        $this->period = $period;

        // Isi form dengan data yang sudah ada
        $this->name = $period->name;
        $this->description = $period->description;
        $this->max_total_budget = $period->max_total_budget;
        $this->start_at = $period->start_at->format('Y-m-d\TH:i');
        $this->end_at = $period->end_at->format('Y-m-d\TH:i');
        $this->is_active = $period->is_active;
        $this->columns = $period->form_template;
    }

    public function addColumn()
    {
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

    public function update()
    {
        $this->validate([
            'name' => 'required|min:5',
            'max_total_budget' => 'required|numeric|min:0',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after:start_at',
            'columns.*.label' => 'required',
            'columns.*.max_value' => 'nullable|numeric|min:0',
        ]);

        $this->period->update([
            'name' => $this->name,
            'description' => $this->description,
            'form_template' => $this->columns,
            'max_total_budget' => $this->max_total_budget,
            'start_at' => $this->start_at,
            'end_at' => $this->end_at,
            'is_active' => $this->is_active,
        ]);

        session()->flash('success', 'Periode Anggaran Berhasil Diperbarui!');
        return redirect()->route('admin.program.keuangan.index');
    }
}; ?>

<div class="max-w-7xl mx-auto py-10 px-4">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Sisi Kiri: Settings --}}
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white p-8 rounded-[3rem] shadow-xl border border-gray-50">
                <h3 class="text-xl font-black uppercase italic tracking-tighter text-slate-800 mb-6">
                    Edit <span class="text-[#800000]">Settings</span>
                </h3>
                <div class="space-y-4">
                    <div>
                        <label class="text-[10px] font-black uppercase italic text-gray-400">Nama Periode</label>
                        <input type="text" wire:model="name" class="w-full bg-gray-50 border-none rounded-2xl p-4 text-sm font-bold focus:ring-2 focus:ring-[#800000]">
                    </div>
                    <div>
                        <label class="text-[10px] font-black uppercase italic text-gray-400">Plafon Anggaran</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 font-black text-gray-300 text-xs">RP</span>
                            <input type="number" wire:model="max_total_budget" class="w-full bg-gray-50 border-none rounded-2xl p-4 pl-12 text-sm font-bold focus:ring-2 focus:ring-[#800000]">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-[10px] font-black uppercase italic text-gray-400">Mulai</label>
                            <input type="datetime-local" wire:model="start_at" class="w-full bg-gray-50 border-none rounded-xl p-3 text-xs font-bold">
                        </div>
                        <div>
                            <label class="text-[10px] font-black uppercase italic text-gray-400">Selesai</label>
                            <input type="datetime-local" wire:model="end_at" class="w-full bg-gray-50 border-none rounded-xl p-3 text-xs font-bold">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sisi Kanan: Template Builder --}}
        <div class="lg:col-span-2">
            <div class="bg-white p-8 rounded-[3rem] shadow-xl border border-gray-50">
                <div class="flex justify-between items-center mb-8">
                    <h3 class="text-xl font-black uppercase italic text-slate-800">Modify <span class="text-[#800000]">Template</span></h3>
                    <button wire:click="addColumn" class="bg-black text-white px-6 py-3 rounded-xl text-[9px] font-black uppercase italic hover:bg-[#800000]">
                        + Add Field
                    </button>
                </div>

                <div class="space-y-3">
                    @foreach($columns as $index => $column)
                        <div class="grid grid-cols-12 gap-4 items-center p-2 bg-gray-50/50 rounded-2xl group">
                            <div class="col-span-4">
                                <input type="text" wire:model="columns.{{ $index }}.label" class="w-full bg-white border-none rounded-xl p-3 text-xs font-bold">
                            </div>
                            <div class="col-span-3">
                                <select wire:model="columns.{{ $index }}.type" class="w-full bg-white border-none rounded-xl p-3 text-[10px] font-bold">
                                    <option value="text">Teks</option>
                                    <option value="number">Angka</option>
                                    <option value="date">Tanggal</option>
                                </select>
                            </div>
                            <div class="col-span-2">
                                @if($columns[$index]['type'] === 'number')
                                    <input type="number" wire:model="columns.{{ $index }}.max_value" placeholder="Max" class="w-full bg-white border-none rounded-xl p-3 text-xs font-black text-[#800000]">
                                @else
                                    <div class="text-center text-[8px] text-gray-300 font-bold">N/A</div>
                                @endif
                            </div>
                            <div class="col-span-2 text-center">
                                <input type="checkbox" wire:model="columns.{{ $index }}.required" class="rounded text-[#800000]">
                            </div>
                            <div class="col-span-1 text-right">
                                <button wire:click="removeColumn({{ $index }})" class="text-red-300 hover:text-red-500 transition-colors text-xl">×</button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-12">
                    <button wire:click="update" class="w-full bg-black text-white py-6 rounded-[2rem] text-xs font-black uppercase italic hover:bg-[#800000] shadow-xl">
                        Update Financial Program —
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
