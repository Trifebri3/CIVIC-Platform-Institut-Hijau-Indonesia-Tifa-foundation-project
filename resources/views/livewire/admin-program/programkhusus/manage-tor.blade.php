<?php

use Livewire\Volt\Component;
use App\Models\TorPeriod;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    // Form Period States
    public $name, $start_at, $end_at, $max_submissions = 2;
    public $is_active = true;
    public $editingId = null;

    // Form Builder States (JSON Template)
    public $form_template = []; // Array of fields

    // Temp Field State (Untuk nambah field baru)
    public $new_label, $new_type = 'text', $new_required = false;

    public function mount()
    {
        // Default template jika baru buat
        if (empty($this->form_template)) {
            $this->form_template = [
                ['id' => uniqid(), 'label' => 'Judul Proposal', 'type' => 'text', 'required' => true],
                ['id' => uniqid(), 'label' => 'Latar Belakang', 'type' => 'richtext', 'required' => true],
            ];
        }
    }

    // --- LOGIC FORM BUILDER ---

    public function addField()
    {
        $this->validate([
            'new_label' => 'required|min:2',
            'new_type' => 'required'
        ]);

        $this->form_template[] = [
            'id' => uniqid(),
            'label' => $this->new_label,
            'type' => $this->new_type,
            'required' => $this->new_required,
            // Jika tipe tabel, kita kasih kolom default
            'columns' => ($this->new_type === 'table') ? ['Item', 'Volume', 'Satuan', 'Total'] : null
        ];

        $this->reset(['new_label', 'new_type', 'new_required']);
    }

    public function removeField($index)
    {
        unset($this->form_template[$index]);
        $this->form_template = array_values($this->form_template);
    }

    public function moveUp($index)
    {
        if ($index > 0) {
            $temp = $this->form_template[$index - 1];
            $this->form_template[$index - 1] = $this->form_template[$index];
            $this->form_template[$index] = $temp;
        }
    }

    // --- LOGIC CRUD PERIOD ---



    public function edit($id)
    {
        $period = TorPeriod::find($id);
        $this->editingId = $period->id;
        $this->name = $period->name;
        $this->start_at = $period->start_at->format('Y-m-d\TH:i');
        $this->end_at = $period->end_at->format('Y-m-d\TH:i');
        $this->max_submissions = $period->max_submissions_per_user;
        $this->form_template = $period->form_template;
        $this->is_active = $period->is_active;
    }

    public function resetForm()
    {
        $this->reset(['name', 'start_at', 'end_at', 'max_submissions', 'editingId', 'is_active']);
        $this->mount();
    }

    public function with()
    {
        return [
            'periods' => TorPeriod::orderBy('created_at', 'desc')->paginate(5)
        ];
    }







    // ... di dalam logic Form Builder ...

// State baru untuk menampung input nama kolom sementara
public $new_column_name;

public function addColumn($fieldIndex)
{
    $this->validate([
        'new_column_name' => 'required|min:1'
    ]);

    // Tambahkan kolom ke field tabel yang dipilih
    $this->form_template[$fieldIndex]['columns'][] = $this->new_column_name;

    $this->reset('new_column_name');
}

public function removeColumn($fieldIndex, $columnIndex)
{
    unset($this->form_template[$fieldIndex]['columns'][$columnIndex]);
    // Reset index array agar tetap berurutan
    $this->form_template[$fieldIndex]['columns'] = array_values($this->form_template[$fieldIndex]['columns']);
}

// ... di dalam logic Volt ...

public function save()
{
    // 1. Validasi
    $validated = $this->validate([
        'name' => 'required|min:5',
        'start_at' => 'required',
        'end_at' => 'required|after:start_at',
        'max_submissions' => 'required|integer',
    ]);

    try {
        // 2. Eksekusi Simpan
        $period = \App\Models\TorPeriod::updateOrCreate(
            ['id' => $this->editingId],
            [
                'name' => $this->name,
                'start_at' => $this->start_at,
                'end_at' => $this->end_at,
                'max_submissions_per_user' => $this->max_submissions,
                'form_template' => $this->form_template, // Pastikan array ini isinya benar
                'is_active' => $this->is_active,
            ]
        );

        // 3. Feedback & Reset
        $this->resetForm();
        $this->dispatch('swal', ['title' => 'Berhasil!', 'text' => 'Data TOR Tersimpan', 'icon' => 'success']);

    } catch (\Exception $e) {
        // Debugging jika gagal
        dd($e->getMessage());
    }
}


public function deletePeriod($id)
{
    $period = TorPeriod::find($id);
    if ($period) {
        $period->delete();
        $this->resetForm(); // Reset jika yang dihapus sedang di-edit
        $this->dispatch('swal', ['title' => 'Deleted!', 'text' => 'Periode berhasil dihapus', 'icon' => 'warning']);
    }
}

}; ?>

<div class="p-8 bg-[#FAFAFA] min-h-screen">
    <div class="max-w-7xl mx-auto space-y-10">

        <div class="flex justify-between items-end">
            <div>
                <h1 class="text-4xl font-black uppercase italic tracking-tighter text-slate-800">Manage <span class="text-[#800000]">TOR Template</span></h1>
                <p class="text-[10px] font-bold text-gray-400 uppercase italic tracking-widest mt-2">Setting Periode & Dynamic Form Builder</p>
            </div>
            <button wire:click="resetForm" class="text-[10px] font-black uppercase italic text-gray-400 hover:text-[#800000]">Create New Period —</button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">

            {{-- KIRI: FORM CONFIG --}}
            <div class="lg:col-span-4 space-y-6">
                <div class="bg-white p-8 rounded-[3rem] shadow-xl border border-gray-100 space-y-6">
                    <h3 class="text-xs font-black uppercase italic tracking-widest text-slate-800 border-b pb-4">Period Settings</h3>

                    <div class="space-y-4">
                        <div>
                            <label class="text-[9px] font-black uppercase text-gray-400 mb-2 block italic">Nama Periode</label>
                            <input type="text" wire:model="name" class="w-full bg-gray-50 border-none rounded-2xl text-xs font-bold focus:ring-2 focus:ring-[#800000]">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-[9px] font-black uppercase text-gray-400 mb-2 block italic">Mulai</label>
                                <input type="datetime-local" wire:model="start_at" class="w-full bg-gray-50 border-none rounded-2xl text-[10px] font-bold">
                            </div>
                            <div>
                                <label class="text-[9px] font-black uppercase text-gray-400 mb-2 block italic">Selesai</label>
                                <input type="datetime-local" wire:model="end_at" class="w-full bg-gray-50 border-none rounded-2xl text-[10px] font-bold">
                            </div>
                        </div>

                        <div>
                            <label class="text-[9px] font-black uppercase text-gray-400 mb-2 block italic">Kuota TOR per User</label>
                            <input type="number" wire:model="max_submissions" class="w-full bg-gray-50 border-none rounded-2xl text-xs font-bold">
                        </div>

                        <div class="flex items-center gap-3 pt-2">
                            <input type="checkbox" wire:model="is_active" class="rounded text-[#800000] focus:ring-[#800000]">
                            <span class="text-[10px] font-black uppercase italic text-slate-600">Aktifkan Periode</span>
                        </div>
                    </div>

                    <button wire:click="save" class="w-full bg-black text-white py-4 rounded-2xl text-[10px] font-black uppercase italic hover:bg-[#800000] transition-all shadow-lg shadow-red-900/10">
                        {{ $editingId ? 'Update Period' : 'Publish Period' }}
                    </button>
                </div>

                {{-- LIST PERIODE SINGKAT --}}
                <div class="space-y-3">
@foreach($periods as $p)
    <div class="p-4 bg-white rounded-2xl border border-gray-100 flex justify-between items-center group">
        <div>
            <p class="text-[10px] font-black uppercase italic text-slate-700 leading-none">{{ $p->name }}</p>
            <p class="text-[8px] font-bold text-gray-400 uppercase mt-1">{{ $p->start_at->format('d M') }} - {{ $p->end_at->format('d M Y') }}</p>
        </div>
        <div class="flex gap-3 opacity-0 group-hover:opacity-100 transition-all">
            {{-- Tombol Edit --}}
            <button wire:click="edit({{ $p->id }})" class="text-[9px] font-black uppercase text-blue-600 italic">Edit</button>

            {{-- Tombol Delete --}}
            <button wire:click="deletePeriod({{ $p->id }})"
                    wire:confirm="Yakin mau hapus periode ini? Semua data terkait akan hilang!"
                    class="text-[9px] font-black uppercase text-red-600 italic">Delete</button>
        </div>
    </div>
@endforeach
                </div>
            </div>

            {{-- KANAN: DYNAMIC FORM BUILDER --}}
            <div class="lg:col-span-8">
                <div class="bg-white p-10 rounded-[4rem] shadow-2xl border border-gray-50">
                    <div class="flex justify-between items-center mb-10 border-b border-gray-50 pb-6">
                        <h3 class="text-sm font-black uppercase italic tracking-widest text-slate-800">Dynamic <span class="text-[#800000]">Form Builder</span></h3>
                        <span class="text-[9px] font-bold text-gray-300 uppercase italic">Drag & drop disabled (Use Arrows)</span>
                    </div>

                    {{-- ADD FIELD TOOLBAR --}}
                    <div class="flex flex-wrap gap-3 bg-gray-50 p-6 rounded-[2rem] mb-10 border border-gray-100">
                        <div class="flex-1 min-w-[200px]">
                            <input type="text" wire:model="new_label" placeholder="Field Label (e.g. Latar Belakang)" class="w-full bg-white border-none rounded-xl text-[10px] font-bold">
                        </div>
                        <select wire:model="new_type" class="bg-white border-none rounded-xl text-[10px] font-bold">
                            <option value="text">Short Text</option>
                            <option value="richtext">Long Text (Rich Editor)</option>
                            <option value="table">Table (Dynamic Row)</option>
                            <option value="file">Documents / Photo</option>
                            <option value="link">Multiple Link</option>
                            <option value="date">Date & Time</option>
                        </select>
                        <button wire:click="addField" class="bg-[#800000] text-white px-6 py-2 rounded-xl text-[10px] font-black uppercase italic hover:bg-black transition-all">Add Field +</button>
                    </div>

                    {{-- RENDER TEMPLATE FIELDS --}}
                    <div class="space-y-4">
                        @foreach($form_template as $index => $field)
                            <div class="flex items-center gap-4 p-5 bg-[#FAFAFA] rounded-2xl border border-gray-100 group">
                                <div class="flex flex-col gap-1">
                                    <button wire:click="moveUp({{ $index }})" class="text-gray-300 hover:text-black">▲</button>
                                    <span class="text-[10px] font-black text-gray-200">#{{ $index + 1 }}</span>
                                </div>

                                <div class="flex-1">
                                    <div class="flex items-center gap-3">
                                        <input type="text" wire:model="form_template.{{ $index }}.label" class="bg-transparent border-none p-0 text-xs font-black uppercase italic text-slate-700 focus:ring-0">
                                        <span class="px-2 py-0.5 bg-gray-200 text-[7px] font-black uppercase rounded text-gray-500">{{ $field['type'] }}</span>
                                    </div>
@if($field['type'] === 'table')
    <div class="mt-4 p-4 bg-white rounded-xl border border-dashed border-gray-200">
        <label class="text-[8px] font-black uppercase italic text-[#800000] mb-3 block">Konfigurasi Kolom Tabel:</label>

        {{-- List Kolom yang sudah ada --}}
        <div class="flex flex-wrap gap-2 mb-4">
            @foreach($field['columns'] as $colIndex => $colName)
                <div class="flex items-center gap-2 px-3 py-1 bg-gray-100 rounded-lg group/col">
                    <span class="text-[9px] font-bold text-slate-600">{{ $colName }}</span>
                    <button wire:click="removeColumn({{ $index }}, {{ $colIndex }})" class="text-gray-300 hover:text-red-500">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"/></svg>
                    </button>
                </div>
            @endforeach
        </div>

        {{-- Input Tambah Kolom Baru --}}
        <div class="flex gap-2">
            <input type="text"
                   wire:model="new_column_name"
                   placeholder="Nama Kolom Baru..."
                   class="flex-1 bg-gray-50 border-none rounded-lg text-[9px] font-bold focus:ring-1 focus:ring-[#800000]">
            <button wire:click="addColumn({{ $index }})"
                    class="px-3 py-1 bg-black text-white rounded-lg text-[9px] font-black uppercase italic hover:bg-[#800000]">
                Add Col +
            </button>
        </div>
    </div>
@endif
                                </div>

                                <div class="flex items-center gap-4 opacity-0 group-hover:opacity-100 transition-all">
                                    <label class="flex items-center gap-2 cursor-pointer">
<input type="text"
       wire:model.defer="name"
       class="w-full bg-gray-50 border-none rounded-2xl text-xs font-bold focus:ring-2 focus:ring-[#800000]">

<button type="button"
        wire:click.prevent="save"
        class="w-full bg-black text-white py-4 rounded-2xl text-[10px] font-black uppercase italic hover:bg-[#800000] transition-all shadow-lg">
    {{ $editingId ? 'Update Period' : 'Publish Period' }}
</button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
