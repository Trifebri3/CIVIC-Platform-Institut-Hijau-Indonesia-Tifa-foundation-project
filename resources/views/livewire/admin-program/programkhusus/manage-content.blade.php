<?php

use Livewire\Volt\Component;
use App\Models\ProgramKhusus;
use App\Models\ProgramContentKhusus;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;

    public ProgramKhusus $program;

    // Form States
    public $type = 'timeline';
    public $title = '';
    public $order = 0;

    // JSON Data States
    public $date, $time, $location, $description; // Timeline
    public $link, $label; // Resource
    public $content_text, $priority = 'normal'; // Informasi

    public $editingId = null;

    public function mount(ProgramKhusus $program)
    {
        $this->program = $program;
    }

    public function save()
    {
        $this->validate([
            'title' => 'required|min:3',
            'type' => 'required',
        ]);

        $data = [];
        if ($this->type === 'timeline') {
            $data = ['date' => $this->date, 'time' => $this->time, 'location' => $this->location, 'desc' => $this->description];
        } elseif ($this->type === 'resource') {
            $data = ['link' => $this->link, 'label' => $this->label];
        } else {
            $data = ['body' => $this->content_text, 'priority' => $this->priority];
        }

        ProgramContentKhusus::updateOrCreate(
            ['id' => $this->editingId],
            [
                'program_khusus_id' => $this->program->id,
                'type' => $this->type,
                'title' => $this->title,
                'data' => $data,
                'order' => $this->order,
            ]
        );

        $this->resetForm();
        session()->flash('success', 'Data berhasil diperbarui');
    }

    public function edit($id)
    {
        $content = ProgramContentKhusus::find($id);
        $this->editingId = $content->id;
        $this->type = $content->type;
        $this->title = $content->title;
        $this->order = $content->order;

        if ($this->type === 'timeline') {
            $this->date = $content->data['date'] ?? '';
            $this->time = $content->data['time'] ?? '';
            $this->location = $content->data['location'] ?? '';
            $this->description = $content->data['desc'] ?? '';
        } elseif ($this->type === 'resource') {
            $this->link = $content->data['link'] ?? '';
            $this->label = $content->data['label'] ?? '';
        } else {
            $this->content_text = $content->data['body'] ?? '';
            $this->priority = $content->data['priority'] ?? 'normal';
        }
    }

    public function delete($id)
    {
        ProgramContentKhusus::destroy($id);
        session()->flash('success', 'Konten dihapus');
    }

    public function resetForm()
    {
        $this->reset(['title', 'date', 'time', 'location', 'description', 'link', 'label', 'content_text', 'priority', 'editingId', 'order']);
    }

    public function with()
    {
        return [
            'contents' => ProgramContentKhusus::where('program_khusus_id', $this->program->id)
                ->orderBy('order')
                ->get()
        ];
    }
}; ?>

<div class="p-6 space-y-8 bg-white rounded-[3rem] shadow-xl border border-gray-100">

    {{-- HEADER --}}
    <div class="flex justify-between items-center border-b border-gray-50 pb-6">
        <div>
            <h2 class="text-2xl font-black italic uppercase tracking-tighter text-slate-800">
                Manage <span class="text-[#800000]">Content</span>
            </h2>
            <p class="text-[10px] font-bold text-gray-400 uppercase italic">{{ $program->nama_program }}</p>
        </div>
        <button wire:click="resetForm" class="text-[9px] font-black uppercase italic text-gray-400 hover:text-[#800000]">Clear Form</button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">

        {{-- FORM SECTION --}}
        <div class="lg:col-span-5 space-y-4">
            <form wire:submit.prevent="save" class="space-y-4 bg-gray-50 p-6 rounded-[2rem]">

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-[9px] font-black uppercase text-gray-400 mb-2 block italic">Content Type</label>
                        <select wire:model.live="type" class="w-full bg-white border-none rounded-xl text-xs font-bold focus:ring-2 focus:ring-[#800000]">
                            <option value="timeline">Timeline</option>
                            <option value="resource">Resource</option>
                            <option value="information">Information</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-[9px] font-black uppercase text-gray-400 mb-2 block italic">Order</label>
                        <input type="number" wire:model="order" class="w-full bg-white border-none rounded-xl text-xs font-bold focus:ring-2 focus:ring-[#800000]">
                    </div>
                </div>

                <div>
                    <label class="text-[9px] font-black uppercase text-gray-400 mb-2 block italic">Title</label>
                    <input type="text" wire:model="title" placeholder="Entry Title" class="w-full bg-white border-none rounded-xl text-xs font-bold focus:ring-2 focus:ring-[#800000]">
                </div>

                {{-- DINAMIS FORM BERDASARKAN TIPE --}}
                @if($type === 'timeline')
                    <div class="grid grid-cols-2 gap-4 animate-fadeIn">
                        <input type="date" wire:model="date" class="w-full bg-white border-none rounded-xl text-xs font-bold">
                        <input type="time" wire:model="time" class="w-full bg-white border-none rounded-xl text-xs font-bold">
                    </div>
                    <input type="text" wire:model="location" placeholder="Location" class="w-full bg-white border-none rounded-xl text-xs font-bold">
                    <textarea wire:model="description" placeholder="Description" class="w-full bg-white border-none rounded-xl text-xs font-bold h-20"></textarea>

                @elseif($type === 'resource')
                    <input type="text" wire:model="link" placeholder="URL Link (https://...)" class="w-full bg-white border-none rounded-xl text-xs font-bold">
                    <input type="text" wire:model="label" placeholder="Button Label (e.g. Download)" class="w-full bg-white border-none rounded-xl text-xs font-bold">

                @else
                    <select wire:model="priority" class="w-full bg-white border-none rounded-xl text-xs font-bold">
                        <option value="normal">Normal Priority</option>
                        <option value="high">High Priority / Alert</option>
                    </select>
                    <textarea wire:model="content_text" placeholder="Information Body Content" class="w-full bg-white border-none rounded-xl text-xs font-bold h-32"></textarea>
                @endif

                <button type="submit" class="w-full bg-black text-white py-4 rounded-2xl text-[10px] font-black uppercase italic hover:bg-[#800000] transition-all shadow-lg">
                    {{ $editingId ? 'Update Content' : 'Add Content' }}
                </button>
            </form>
        </div>

        {{-- LIST SECTION --}}
        <div class="lg:col-span-7 space-y-3 h-[500px] overflow-y-auto pr-2 custom-scrollbar">
            @foreach($contents as $item)
                <div class="p-4 bg-white border border-gray-100 rounded-2xl flex justify-between items-center group hover:border-[#800000]/20 transition-all shadow-sm">
                    <div class="flex items-center gap-4">
                        <div class="text-[8px] font-black uppercase px-2 py-1 rounded bg-gray-100 text-gray-500 italic">
                            {{ $item->type }}
                        </div>
                        <div>
                            <h4 class="text-[11px] font-black uppercase italic text-slate-800 leading-none">{{ $item->title }}</h4>
                            <p class="text-[8px] font-bold text-gray-400 uppercase tracking-widest mt-1">Order: {{ $item->order }}</p>
                        </div>
                    </div>
                    <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-all">
                        <button wire:click="edit({{ $item->id }})" class="px-3 py-1 bg-blue-50 text-blue-600 text-[9px] font-black uppercase italic rounded-md">Edit</button>
                        <button wire:click="delete({{ $item->id }})" wire:confirm="Delete this item?" class="px-3 py-1 bg-red-50 text-red-600 text-[9px] font-black uppercase italic rounded-md">Delete</button>
                    </div>
                </div>
            @endforeach
        </div>









    </div>


    <div class="relative group overflow-hidden bg-white p-8 rounded-[3rem] shadow-xl border border-gray-100 hover:border-[#800000]/30 transition-all duration-500">
        {{-- Background Decoration --}}
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-gray-50 rounded-full group-hover:bg-red-50 transition-colors duration-500"></div>

        <div class="relative z-10">
            <div class="w-12 h-12 bg-black text-white rounded-2xl flex items-center justify-center mb-6 shadow-lg shadow-gray-200 group-hover:bg-[#800000] transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>

            <h3 class="text-sm font-black uppercase italic tracking-widest text-slate-800">TOR Management</h3>
            <p class="text-[10px] font-bold text-gray-400 uppercase italic mt-2 leading-relaxed">
                Atur periode pengajuan, kuota submit, dan buat dynamic form template untuk mahasiswa.
            </p>

            <div class="mt-8">
                <a href="{{ route('admin.tor.manage') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gray-50 text-[10px] font-black uppercase italic text-slate-600 rounded-xl hover:bg-black hover:text-white transition-all shadow-sm">
                    Open Settings
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 8l4 4m0 0l-4 4m4-4H3" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </a>
            </div>
        </div>

        {{-- Indicator Status --}}
        <div class="mt-6 pt-6 border-t border-gray-50 flex items-center justify-between">
            <span class="text-[8px] font-black uppercase tracking-[0.2em] text-gray-300">System Status</span>
            <div class="flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span>
                <span class="text-[9px] font-bold text-green-600 uppercase italic">Ready</span>
            </div>
        </div>
    </div>
















    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
    <div class="relative group overflow-hidden bg-white rounded-[2.5rem] p-8 border border-gray-100 shadow-sm hover:shadow-2xl transition-all duration-500">
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-50 rounded-full group-hover:scale-150 transition-transform duration-700"></div>

        <div class="relative z-10">
            <div class="flex justify-between items-start mb-6">
                <div class="p-3 bg-emerald-100 text-emerald-600 rounded-2xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                @php
                    $pendingTor = \App\Models\TorSubmission::where('status', 'pending')->count();
                @endphp
                <span class="text-2xl font-black italic text-emerald-600 leading-none">{{ $pendingTor }}</span>
            </div>

            <h3 class="text-xl font-black text-slate-800 uppercase italic tracking-tighter mb-1">
                Persetujuan <span class="text-emerald-500">TOR</span>
            </h3>
            <p class="text-[10px] font-bold text-gray-400 uppercase italic mb-6">Ada {{ $pendingTor }} pengajuan menunggu review Anda</p>

            <a href="{{ route('admin.tor.approval') }}"
               class="inline-flex items-center gap-2 px-6 py-3 bg-black text-white rounded-xl text-[10px] font-black uppercase italic tracking-widest hover:bg-emerald-600 shadow-lg shadow-emerald-100 transition-all active:scale-95">
                Buka Panel ACC
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 8l4 4m0 0l-4 4m4-4H3" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"></path></svg>
            </a>
        </div>
    </div>

    </div>
<a href="{{ route('admin.program.reports.index') }}"
   class="group flex items-center gap-4 p-4 rounded-2xl transition-all {{ request()->routeIs('admin.program.reports.*') ? 'bg-[#800000] text-white shadow-lg shadow-red-200' : 'text-slate-400 hover:bg-slate-50 hover:text-slate-900' }}">
    <svg class="w-5 h-5 {{ request()->routeIs('admin.program.reports.*') ? 'text-white' : 'text-slate-400 group-hover:text-slate-900' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" stroke-width="2"></path>
    </svg>
    <span class="text-[11px] font-black uppercase italic tracking-widest">Verify Reports</span>
</a>




</div>










