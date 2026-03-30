<?php

use App\Models\SubProgramTemplate;
use Livewire\Volt\Component;
use Illuminate\Support\Str;

new class extends Component {
    public $name;
    public $description;
    public $icon = 'heroicon-o-document-text';

    // State untuk Dynamic Fields
    public $fields = [];

    public function mount()
    {
        // Default: Kasih 1 field kosong biar gak sepi
        $this->addField();
    }

    public function addField()
    {
        $this->fields[] = [
            'id' => Str::random(5),
            'name' => '',
            'label' => '',
            'type' => 'text', // default type
            'placeholder' => '',
            'required' => false,
        ];
    }

    public function removeField($index)
    {
        unset($this->fields[$index]);
        $this->fields = array_values($this->fields); // Reset index
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|min:3|max:50',
            'fields' => 'required|array|min:1',
            'fields.*.label' => 'required',
            'fields.*.type' => 'required',
        ]);

        // Generate 'name' (key) otomatis dari label jika kosong
        foreach ($this->fields as $index => $field) {
            if (empty($field['name'])) {
                $this->fields[$index]['name'] = Str::slug($field['label'], '_');
            }
        }

        SubProgramTemplate::create([
            'name' => $this->name,
            'slug' => Str::slug($this->name),
            'description' => $this->description,
            'icon' => $this->icon,
            'fields_schema' => $this->fields,
        ]);

        session()->flash('success', 'Template "' . $this->name . '" Berhasil Dibuat!');
        return redirect()->route('superadmin.templates.index');
    }
}; ?>

<div class="max-w-5xl mx-auto pb-20">
    <div class="mb-10 flex justify-between items-end px-4">
        <div>
            <h1 class="text-4xl font-black text-gray-900 uppercase tracking-tighter italic leading-none">Template Architect</h1>
            <p class="text-[10px] text-[#800000] font-black uppercase tracking-[0.3em] mt-2 italic">Membangun Struktur Identitas Sub-Program</p>
        </div>
        <button wire:click="save" class="bg-[#800000] text-white px-10 py-4 rounded-2xl font-black text-[10px] uppercase tracking-[0.3em] shadow-xl hover:bg-black transition-all active:scale-95">
            Publish Template
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Sidebar: Basic Info --}}
        <div class="space-y-6">
            <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100 maroon-card">
                <style>.maroon-card { border-top: 6px solid #800000; }</style>
                <div class="space-y-5">
                    <div>
                        <label class="text-[9px] font-black text-[#800000] uppercase tracking-widest ml-1 mb-2 block">Nama Template</label>
                        <input type="text" wire:model="name" placeholder="Misal: Kelas / Project" class="w-full rounded-2xl border-gray-50 bg-gray-50/50 font-bold text-sm focus:ring-[#800000]">
                    </div>
                    <div>
                        <label class="text-[9px] font-black text-[#800000] uppercase tracking-widest ml-1 mb-2 block">Deskripsi Singkat</label>
                        <textarea wire:model="description" rows="3" class="w-full rounded-2xl border-gray-50 bg-gray-50/50 font-medium text-sm focus:ring-[#800000]"></textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main: Field Builder --}}
        <div class="lg:col-span-2 space-y-4">
            <div class="flex items-center justify-between px-6 mb-4">
                <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em]">Structure Fields</h3>
                <button wire:click="addField" class="text-[9px] font-black text-[#800000] bg-red-50 px-4 py-2 rounded-full uppercase tracking-widest hover:bg-[#800000] hover:text-white transition-all">
                    + Add New Field
                </button>
            </div>

            @foreach($fields as $index => $field)
                <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-gray-50 group relative animate-in slide-in-from-right-4 duration-300" wire:key="field-{{ $field['id'] }}">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                        {{-- Field Label --}}
                        <div class="md:col-span-5">
                            <label class="text-[8px] font-black text-gray-400 uppercase tracking-widest ml-1 mb-2 block">Label Field (Apa yang tampil di Form?)</label>
                            <input type="text" wire:model="fields.{{ $index }}.label" placeholder="Contoh: Nama Mentor / File Modul" class="w-full rounded-xl border-gray-50 bg-gray-50 text-sm font-bold focus:ring-[#800000]">
                        </div>

                        {{-- Field Type --}}
                        <div class="md:col-span-4">
                            <label class="text-[8px] font-black text-gray-400 uppercase tracking-widest ml-1 mb-2 block">Tipe Data</label>
                            <select wire:model="fields.{{ $index }}.type" class="w-full rounded-xl border-gray-50 bg-gray-50 text-[10px] font-black uppercase tracking-widest focus:ring-[#800000]">
                                <option value="text">Short Text</option>
                                <option value="textarea">Long Description</option>
                                <option value="date">Date & Time</option>
                                <option value="file">File Upload</option>
                                <option value="url">Link / Embed URL</option>
                                <option value="number">Numeric Value</option>
                            </select>
                        </div>

                        {{-- Action --}}
                        <div class="md:col-span-3 flex items-center gap-2">
                            <label class="flex items-center gap-2 cursor-pointer bg-gray-50 px-3 py-3 rounded-xl">
                                <input type="checkbox" wire:model="fields.{{ $index }}.required" class="rounded text-[#800000] focus:ring-[#800000]">
                                <span class="text-[8px] font-black text-gray-400 uppercase">Wajib</span>
                            </label>
                            <button wire:click="removeField({{ $index }})" class="p-3 bg-red-50 text-red-400 rounded-xl hover:bg-red-500 hover:text-white transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach

            @if(count($fields) === 0)
                <div class="text-center py-20 bg-gray-50/50 rounded-[3rem] border-2 border-dashed border-gray-100">
                    <p class="text-[10px] font-black text-gray-300 uppercase tracking-[0.3em]">No fields added yet. Build your structure.</p>
                </div>
            @endif
        </div>
    </div>
</div>
