<?php

use App\Models\SubProgramTemplate;
use Livewire\Volt\Component;
use Illuminate\Support\Str;

new class extends Component {
    public SubProgramTemplate $template;

    public $name;
    public $description;
    public $icon;
    public $fields = [];

    public function mount(SubProgramTemplate $template)
    {
        $this->template = $template;
        $this->name = $template->name;
        $this->description = $template->description;
        $this->icon = $template->icon;

        // Load data field yang sudah ada
        $this->fields = $template->fields_schema ?? [];
    }

    public function addField()
    {
        $this->fields[] = [
            'id' => Str::random(5),
            'name' => '',
            'label' => '',
            'type' => 'text',
            'placeholder' => '',
            'required' => false,
        ];
    }

    public function removeField($index)
    {
        unset($this->fields[$index]);
        $this->fields = array_values($this->fields);
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|min:3|max:50',
            'fields' => 'required|array|min:1',
            'fields.*.label' => 'required',
            'fields.*.type' => 'required',
        ]);

        // Clean up name/key field
        foreach ($this->fields as $index => $field) {
            if (empty($field['name'])) {
                $this->fields[$index]['name'] = Str::slug($field['label'], '_');
            }
        }

        $this->template->update([
            'name' => $this->name,
            'description' => $this->description,
            'icon' => $this->icon,
            'fields_schema' => $this->fields,
        ]);

        session()->flash('success', 'Template Berhasil Diperbarui!');
        return redirect()->route('superadmin.templates.index');
    }
}; ?>

<div class="max-w-5xl mx-auto pb-20 px-4">
    <div class="mb-10 flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('superadmin.templates.index') }}" class="text-gray-400 hover:text-[#800000] transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"></path></svg>
                </a>
                <span class="text-[10px] text-gray-400 font-black uppercase tracking-[0.3em]">Revision Mode</span>
            </div>
            <h1 class="text-4xl font-black text-gray-900 uppercase tracking-tighter italic leading-none">Edit Structure</h1>
            <p class="text-[10px] text-[#800000] font-black uppercase tracking-[0.3em] mt-2 italic">{{ $template->name }} Blueprint</p>
        </div>
        <button wire:click="update" class="w-full md:w-auto bg-black text-white px-10 py-4 rounded-2xl font-black text-[10px] uppercase tracking-[0.3em] shadow-xl hover:bg-[#800000] transition-all active:scale-95">
            Save Changes
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Sidebar --}}
        <div class="space-y-6">
            <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100 maroon-card sticky top-10">
                <style>.maroon-card { border-top: 6px solid #800000; }</style>
                <div class="space-y-6">
                    <div>
                        <label class="text-[9px] font-black text-[#800000] uppercase tracking-widest ml-1 mb-2 block">Template Identity</label>
                        <input type="text" wire:model="name" class="w-full rounded-2xl border-gray-50 bg-gray-50/50 font-bold text-sm focus:ring-[#800000]">
                    </div>
                    <div>
                        <label class="text-[9px] font-black text-[#800000] uppercase tracking-widest ml-1 mb-2 block">Structure Notes</label>
                        <textarea wire:model="description" rows="4" class="w-full rounded-2xl border-gray-50 bg-gray-50/50 font-medium text-sm focus:ring-[#800000]"></textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- Field Builder --}}
        <div class="lg:col-span-2 space-y-4">
            <div class="flex items-center justify-between px-6 mb-4">
                <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em]">Active Fields ({{ count($fields) }})</h3>
                <button wire:click="addField" class="text-[9px] font-black text-[#800000] bg-red-50 px-4 py-2 rounded-full uppercase tracking-widest hover:bg-[#800000] hover:text-white transition-all">
                    + Add Field
                </button>
            </div>

            @foreach($fields as $index => $field)
                <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-gray-50 group relative" wire:key="field-edit-{{ $index }}">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                        <div class="md:col-span-5">
                            <label class="text-[8px] font-black text-gray-400 uppercase tracking-widest ml-1 mb-2 block">Display Label</label>
                            <input type="text" wire:model="fields.{{ $index }}.label" class="w-full rounded-xl border-gray-50 bg-gray-50 text-sm font-bold focus:ring-[#800000]">
                        </div>

                        <div class="md:col-span-4">
                            <label class="text-[8px] font-black text-gray-400 uppercase tracking-widest ml-1 mb-2 block">Input Type</label>
                            <select wire:model="fields.{{ $index }}.type" class="w-full rounded-xl border-gray-50 bg-gray-50 text-[10px] font-black uppercase tracking-widest focus:ring-[#800000]">
                                <option value="text">Short Text</option>
                                <option value="textarea">Long Description</option>
                                <option value="date">Date & Time</option>
                                <option value="file">File Upload</option>
                                <option value="url">Link / Embed URL</option>
                                <option value="number">Numeric Value</option>
                            </select>
                        </div>

                        <div class="md:col-span-3 flex items-center gap-2">
                            <label class="flex items-center gap-2 cursor-pointer bg-gray-50 px-3 py-3 rounded-xl border border-transparent hover:border-red-100 transition-all">
                                <input type="checkbox" wire:model="fields.{{ $index }}.required" class="rounded text-[#800000] focus:ring-[#800000]">
                                <span class="text-[8px] font-black text-gray-400 uppercase">Wajib</span>
                            </label>
                            <button wire:click="removeField({{ $index }})" class="p-3 bg-red-50 text-red-400 rounded-xl hover:bg-red-500 hover:text-white transition-all shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
