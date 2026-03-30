<?php

use App\Models\Validasinilai;
use Livewire\Volt\Component;

new class extends Component {
    public $template_name, $description;
    public $schema = []; // Struktur kriteria: [['label' => '', 'key' => '']]

    public $selectedId, $isEdit = false, $isOpen = false;

    public function with()
    {
        return [
            'templates' => Validasinilai::latest()->get(),
        ];
    }

    public function addKriteria()
    {
        $this->schema[] = ['label' => '', 'key' => '', 'type' => 'number'];
    }

    public function removeKriteria($index)
    {
        unset($this->schema[$index]);
        $this->schema = array_values($this->schema);
    }

    public function openModal()
    {
        $this->reset(['template_name', 'description', 'schema', 'isEdit']);
        $this->schema = [['label' => 'Nilai Utama', 'key' => 'nilai_utama', 'type' => 'number']];
        $this->isOpen = true;
    }

    public function save()
    {
        $this->validate([
            'template_name' => 'required|string|max:255',
            'schema.*.label' => 'required',
            'schema.*.key' => 'required|alpha_dash',
        ]);

        $data = [
            'template_name' => $this->template_name,
            'description' => $this->description,
            'schema' => ['kriteria' => $this->schema],
        ];

        if ($this->isEdit) {
            Validasinilai::find($this->selectedId)->update($data);
        } else {
            Validasinilai::create($data);
        }

        $this->isOpen = false;
        $this->dispatch('swal', title: 'Template Berhasil Disimpan!', icon: 'success');
    }

    public function edit($id)
    {
        $val = Validasinilai::find($id);
        $this->selectedId = $id;
        $this->template_name = $val->template_name;
        $this->description = $val->description;
        $this->schema = $val->schema['kriteria'] ?? [];

        $this->isEdit = true;
        $this->isOpen = true;
    }

    public function delete($id)
    {
        Validasinilai::find($id)->delete();
    }
}; ?>

<div class="p-8 max-w-7xl mx-auto">
    <div class="flex justify-between items-end mb-10">
        <div>
            <h1 class="text-4xl font-black uppercase italic tracking-tighter">Master <span class="text-[#800000]">Template</span></h1>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-2">Buat struktur penilaian JSON yang akan digunakan Admin Program.</p>
        </div>
        <button wire:click="openModal" class="bg-black text-white px-8 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-[#800000] transition-all shadow-xl shadow-black/10">
            + New Master Template
        </button>
    </div>

    {{-- Grid Cards untuk Master Template --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($templates as $temp)
        <div class="bg-white border border-gray-100 rounded-3xl p-6 shadow-sm hover:shadow-md transition-all group">
            <div class="flex justify-between items-start mb-4">
                <div class="bg-gray-50 p-3 rounded-2xl group-hover:bg-[#800000]/5 transition-colors">
                    <svg class="w-6 h-6 text-gray-400 group-hover:text-[#800000]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <div class="flex gap-2">
                    <button wire:click="edit({{ $temp->id }})" class="text-[10px] font-black uppercase text-blue-500 hover:underline">Edit</button>
                    <button wire:click="delete({{ $temp->id }})" class="text-[10px] font-black uppercase text-red-500 hover:underline">Delete</button>
                </div>
            </div>

            <h3 class="text-lg font-black uppercase italic tracking-tight text-slate-800">{{ $temp->template_name }}</h3>
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mt-1">{{ count($temp->schema['kriteria'] ?? []) }} Penilaian Terdaftar</p>

            <div class="mt-4 flex flex-wrap gap-2">
                @foreach(array_slice($temp->schema['kriteria'] ?? [], 0, 3) as $k)
                    <span class="text-[8px] font-black uppercase bg-gray-50 px-2 py-1 rounded text-gray-500 border border-gray-100">{{ $k['label'] }}</span>
                @endforeach
                @if(count($temp->schema['kriteria'] ?? []) > 3)
                    <span class="text-[8px] font-black uppercase bg-gray-50 px-2 py-1 rounded text-gray-400">+{{ count($temp->schema['kriteria']) - 3 }}</span>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    {{-- MODAL BUILDER --}}
    @if($isOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4 text-slate-800">
        <div class="bg-white w-full max-w-2xl rounded-3xl shadow-2xl p-8 overflow-hidden">
            <h2 class="text-2xl font-black uppercase italic tracking-tighter mb-6">{{ $isEdit ? 'Update' : 'Create' }} <span class="text-[#800000]">Master Template</span></h2>

            <form wire:submit="save" class="space-y-5">
                <div>
                    <label class="text-[9px] font-black uppercase tracking-widest text-gray-400">Nama Template (Misal: E-Raport Magang)</label>
                    <input type="text" wire:model="template_name" class="w-full border-gray-100 rounded-xl font-bold focus:border-[#800000] focus:ring-0">
                </div>

                <div>
                    <label class="text-[9px] font-black uppercase tracking-widest text-gray-400">Deskripsi Singkat</label>
                    <textarea wire:model="description" rows="2" class="w-full border-gray-100 rounded-xl text-sm"></textarea>
                </div>

                <div class="bg-gray-50 p-5 rounded-2xl border border-dashed">
                    <div class="flex justify-between items-center mb-4">
                        <label class="text-[9px] font-black uppercase text-gray-400">Daftar Field Kriteria</label>
                        <button type="button" wire:click="addKriteria" class="bg-black text-white text-[8px] px-3 py-1 rounded-lg uppercase font-black">+ Add Field</button>
                    </div>

                    <div class="space-y-3 max-h-60 overflow-y-auto pr-2">
                        @foreach($schema as $index => $crit)
                        <div class="flex gap-2 items-center bg-white p-3 rounded-xl border border-gray-100 shadow-sm">
                            <input type="text" wire:model="schema.{{ $index }}.label" placeholder="Label (Ex: Etika)" class="flex-1 border-0 bg-gray-50 rounded-lg text-[10px] font-bold">
                            <input type="text" wire:model="schema.{{ $index }}.key" placeholder="Key (Ex: etika)" class="flex-1 border-0 bg-gray-50 rounded-lg text-[10px] font-bold">
                            <button type="button" wire:click="removeKriteria({{ $index }})" class="text-red-500 p-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="button" wire:click="$set('isOpen', false)" class="flex-1 px-6 py-4 rounded-xl text-[10px] font-black uppercase border border-gray-100 hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="flex-1 bg-[#800000] text-white px-6 py-4 rounded-xl text-[10px] font-black uppercase hover:bg-black transition-all">Save Master Template</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
