<?php

use App\Models\RabPeriod;
use App\Models\ReportTemplate;
use Livewire\Volt\Component;
use Livewire\Attributes\Validate;

new class extends Component {
    public RabPeriod $period;

    // Properti harus didefinisikan dengan jelas untuk binding
    public $title = '';
    public $is_active = true;
    public $fields = [];

    public function mount(RabPeriod $period)
    {
        $this->period = $period;

        $template = ReportTemplate::where('rab_period_id', $this->period->id)->first();

        if ($template) {
            $this->title = $template->title;
            // Pastikan fields selalu array
            $this->fields = is_array($template->fields) ? $template->fields : [];
            $this->is_active = (bool) $template->is_active;
        } else {
            // Default fields
            $this->fields = [
                ['name' => 'deskripsi_' . str()->random(4), 'label' => 'Deskripsi Pelaksanaan Kegiatan', 'type' => 'textarea', 'required' => true],
                ['name' => 'foto_' . str()->random(4), 'label' => 'Foto Dokumentasi (JPG/PNG)', 'type' => 'image', 'required' => true],
            ];
        }
    }

    public function addField()
    {
        $this->fields[] = [
            'name' => 'field_' . bin2hex(random_bytes(2)),
            'label' => '',
            'type' => 'text',
            'required' => true
        ];
    }

    public function removeField($index)
    {
        unset($this->fields[$index]);
        $this->fields = array_values($this->fields);
    }

    public function save()
    {
        // Validasi yang lebih ketat untuk array
        $this->validate([
            'title' => 'required|min:3',
            'fields' => 'required|array|min:1',
            'fields.*.label' => 'required|string|min:3',
            'fields.*.type' => 'required',
        ], [
            'fields.*.label.required' => 'Label tidak boleh kosong!',
            'fields.*.label.min' => 'Label minimal 3 karakter!',
        ]);

        try {
            ReportTemplate::updateOrCreate(
                ['rab_period_id' => $this->period->id],
                [
                    'title' => $this->title,
                    'fields' => $this->fields, // Disimpan sebagai JSON array
                    'is_active' => $this->is_active,
                ]
            );

            $this->dispatch('notify', [
                'message' => 'Template Berhasil Dipublikasikan!',
                'type' => 'success'
            ]);

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => 'Gagal menyimpan: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }
}; ?>

<div class="max-w-6xl mx-auto space-y-8 pb-20">
    {{-- Header --}}
    <div class="bg-slate-900 rounded-[3rem] p-12 text-white shadow-2xl relative overflow-hidden">
        <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-6">
            <div>
                <h2 class="text-4xl font-black italic uppercase tracking-tighter">Report <span class="text-[#800000]">Architect</span></h2>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.4em] italic mt-2">Design your form structure</p>
            </div>
            <div class="bg-white/5 p-4 rounded-3xl border border-white/10 flex items-center gap-4">
                <div class="text-right">
                    <p class="text-[8px] font-black uppercase text-slate-500">Periode</p>
                    <p class="text-sm font-bold italic">{{ $period->name }}</p>
                </div>
                <div class="w-10 h-10 bg-[#800000] rounded-2xl flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" stroke-width="2"></path></svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <div class="lg:col-span-3 space-y-4">
            <div class="bg-white rounded-[3.5rem] shadow-sm border border-gray-100 p-10">
                <div class="flex justify-between items-center mb-10 px-4">
                    <h3 class="text-xs font-black uppercase italic text-slate-400 tracking-widest">Input Components</h3>
                    <button wire:click="addField" class="bg-black text-white px-6 py-3 rounded-2xl text-[10px] font-black uppercase italic hover:bg-[#800000] transition-all">
                        + Add New Field
                    </button>
                </div>

                <div class="space-y-6">
                    @foreach($fields as $index => $field)
                        {{-- WIRE:KEY sangat krusial agar input tidak error saat dihapus --}}
                        <div wire:key="field-{{ $index }}" class="group bg-gray-50/50 rounded-[2.5rem] p-8 border border-transparent hover:border-gray-200 transition-all flex items-start gap-6">
                            <div class="bg-slate-200 text-slate-500 w-10 h-10 rounded-2xl flex items-center justify-center text-xs font-black italic">
                                {{ $index + 1 }}
                            </div>

                            <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="text-[9px] font-black uppercase italic text-slate-400 ml-2 mb-1 block">Question Label</label>
                                    <input type="text" wire:model.defer="fields.{{ $index }}.label"
                                           class="w-full border-none bg-white rounded-2xl p-4 text-sm font-bold shadow-sm focus:ring-2 focus:ring-[#800000]">
                                    @error("fields.$index.label") <span class="text-[8px] text-red-500 font-bold ml-2">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="text-[9px] font-black uppercase italic text-slate-400 ml-2 mb-1 block">Input Type</label>
                                    <select wire:model="fields.{{ $index }}.type" class="w-full border-none bg-white rounded-2xl p-4 text-sm font-bold shadow-sm focus:ring-2 focus:ring-[#800000]">
                                        <option value="text">Short Text</option>
                                        <option value="textarea">Long Paragraf</option>
                                        <option value="image">Image Attachment</option>
                                        <option value="file">PDF Document</option>
                                    </select>
                                </div>
                            </div>

                            <button wire:click="removeField({{ $index }})" class="p-3 text-gray-300 hover:text-red-500 transition-colors mt-6">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2"></path></svg>
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Settings --}}
        <div class="space-y-6">
            <div class="bg-white rounded-[3rem] p-10 shadow-sm border border-gray-100 sticky top-10">
                <div class="space-y-6">
                    <div>
                        <label class="text-[9px] font-black uppercase italic text-slate-400 ml-2 mb-1 block">Form Title</label>
                        <input type="text" wire:model="title" class="w-full border-none bg-gray-50 rounded-2xl p-4 text-sm font-bold">
                        @error('title') <span class="text-[8px] text-red-500 font-bold ml-2">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center justify-between bg-slate-50 p-5 rounded-2xl border border-gray-100">
                        <span class="text-[10px] font-black uppercase italic">Active Status</span>
                        <input type="checkbox" wire:model="is_active" class="rounded text-[#800000] focus:ring-[#800000]">
                    </div>

                    <button wire:click="save"
                            wire:loading.attr="disabled"
                            class="w-full bg-[#800000] text-white py-6 rounded-[2rem] font-black italic uppercase tracking-[0.2em] shadow-xl shadow-red-100 hover:bg-black transition-all">
                        <span wire:loading.remove>Publish Template</span>
                        <span wire:loading>Processing...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
