<?php

use Livewire\Volt\Component;
use App\Models\ProgramKhusus;
use Livewire\WithPagination;
use Illuminate\Support\Str;

new class extends Component {
    use WithPagination;

    // Form Properties (Sinkron dengan Skema Tabel Bos)
    public $programId = null;
    public $nama_program = '';
    public $deskripsi_singkat = '';
    public $konten_eksklusif = '';
    public $warna_tema = '#800000';
    public $max_quota = 0;
    public $is_active = true;

    // UI State
    public $isOpen = false;
    public $search = '';

    protected $rules = [
        'nama_program' => 'required|min:5',
        'deskripsi_singkat' => 'nullable|max:255',
        'warna_tema' => 'required',
        'max_quota' => 'required|numeric|min:0',
    ];

    public function create()
    {
        $this->reset(['programId', 'nama_program', 'deskripsi_singkat', 'konten_eksklusif', 'warna_tema', 'max_quota', 'is_active']);
        $this->isOpen = true;
    }

    public function store()
    {
        $this->validate();

        ProgramKhusus::updateOrCreate(['id' => $this->programId], [
            'nama_program' => $this->nama_program,
            'slug' => Str::slug($this->nama_program),
            'deskripsi_singkat' => $this->deskripsi_singkat,
            'konten_eksklusif' => $this->konten_eksklusif,
            'warna_tema' => $this->warna_tema,
            'max_quota' => $this->max_quota,
            'is_active' => $this->is_active,
        ]);

        session()->flash('success', $this->programId ? 'Program Updated! 🚀' : 'Program Launched! 🔥');
        $this->isOpen = false;
    }

    public function edit($id)
    {
        $program = ProgramKhusus::findOrFail($id);
        $this->programId = $id;
        $this->nama_program = $program->nama_program;
        $this->deskripsi_singkat = $program->deskripsi_singkat;
        $this->konten_eksklusif = $program->konten_eksklusif;
        $this->warna_tema = $program->warna_tema;
        $this->max_quota = $program->max_quota;
        $this->is_active = $program->is_active;
        $this->isOpen = true;
    }

    public function delete($id)
    {
        ProgramKhusus::find($id)->delete();
        session()->flash('success', 'Program Deleted! 🗑️');
    }

    public function getProgramsProperty()
    {
        return ProgramKhusus::where('nama_program', 'like', '%'.$this->search.'%')
            ->latest()
            ->paginate(10);
    }
}; ?>

<div class="p-8 bg-[#fafafa] min-h-screen">
    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-12">
        <div>
            <h1 class="text-4xl font-black uppercase italic tracking-tighter text-slate-800">Master <span class="text-[#800000]">Program Khusus</span></h1>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.4em] mt-2 italic">Superadmin Exclusive Control</p>
        </div>
        <button wire:click="create" class="px-8 py-4 bg-black text-white rounded-2xl text-[10px] font-black uppercase italic shadow-2xl hover:bg-[#800000] transition-all">
            + Create New Program
        </button>
    </div>

    {{-- MODAL FORM --}}
    @if($isOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 overflow-y-auto">
        <div class="bg-white w-full max-w-2xl rounded-[3rem] shadow-2xl my-8 overflow-hidden border border-gray-100">
            <div class="p-10">
                <h2 class="text-2xl font-black uppercase italic tracking-tight mb-8">{{ $programId ? 'Update' : 'New' }} Program</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Nama Program --}}
                    <div class="md:col-span-2">
                        <label class="text-[9px] font-black uppercase text-gray-400 ml-4 mb-2 block italic">Nama Program</label>
                        <input type="text" wire:model="nama_program" class="w-full bg-gray-50 border-none rounded-2xl px-6 py-4 text-sm font-bold focus:ring-2 focus:ring-[#800000]">
                    </div>

                    {{-- Warna Tema --}}
                    <div>
                        <label class="text-[9px] font-black uppercase text-gray-400 ml-4 mb-2 block italic">Warna Identitas</label>
                        <input type="color" wire:model="warna_tema" class="w-full h-14 bg-gray-50 border-none rounded-2xl p-2 cursor-pointer">
                    </div>

                    {{-- Max Quota --}}
                    <div>
                        <label class="text-[9px] font-black uppercase text-gray-400 ml-4 mb-2 block italic">Max Quota (0 = Unlimited)</label>
                        <input type="number" wire:model="max_quota" class="w-full bg-gray-50 border-none rounded-2xl px-6 py-4 text-sm font-bold focus:ring-2 focus:ring-[#800000]">
                    </div>

                    {{-- Deskripsi Singkat --}}
                    <div class="md:col-span-2">
                        <label class="text-[9px] font-black uppercase text-gray-400 ml-4 mb-2 block italic">Deskripsi Singkat</label>
                        <textarea wire:model="deskripsi_singkat" rows="2" class="w-full bg-gray-50 border-none rounded-3xl px-6 py-4 text-sm font-bold focus:ring-2 focus:ring-[#800000]"></textarea>
                    </div>

                    {{-- Konten Eksklusif --}}
                    <div class="md:col-span-2">
                        <label class="text-[9px] font-black uppercase text-gray-400 ml-4 mb-2 block italic">Konten Eksklusif (Timeline/HTML)</label>
                        <textarea wire:model="konten_eksklusif" rows="4" placeholder="Masukkan detail program atau timeline di sini..." class="w-full bg-gray-50 border-none rounded-3xl px-6 py-4 text-sm font-bold focus:ring-2 focus:ring-[#800000]"></textarea>
                    </div>

                    {{-- Status Aktif --}}
                    <div class="flex items-center gap-3 ml-4">
                        <input type="checkbox" wire:model="is_active" class="w-5 h-5 rounded border-gray-300 text-[#800000] focus:ring-[#800000]">
                        <span class="text-[10px] font-black uppercase text-slate-600 italic">Aktifkan Program</span>
                    </div>
                </div>

                <div class="flex justify-end gap-4 mt-10">
                    <button wire:click="$set('isOpen', false)" class="px-8 py-4 text-[10px] font-black uppercase italic text-gray-400">Cancel</button>
                    <button wire:click="store" class="px-10 py-4 bg-black text-white rounded-2xl text-[10px] font-black uppercase italic shadow-lg hover:bg-[#800000] transition-all">
                        {{ $programId ? 'Save Changes' : 'Launch Program' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- DATA TABLE --}}
    <div class="bg-white rounded-[3rem] border border-gray-100 shadow-xl overflow-hidden">
        <div class="p-8 border-b border-gray-50 flex flex-col md:flex-row justify-between gap-4">
            <h3 class="text-lg font-black uppercase italic text-slate-800">Program Directories</h3>
            <div class="relative w-full md:w-80">
                <input type="text" wire:model.live="search" placeholder="Search program..." class="bg-gray-50 border-none rounded-2xl px-8 py-3 text-[10px] font-bold w-full italic">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-8 py-5 text-[9px] font-black uppercase text-gray-400 italic">Branding</th>
                        <th class="px-8 py-5 text-[9px] font-black uppercase text-gray-400 italic">Program Info</th>
                        <th class="px-8 py-5 text-[9px] font-black uppercase text-gray-400 italic">Quota</th>
                        <th class="px-8 py-5 text-[9px] font-black uppercase text-gray-400 italic text-center">Status</th>
                        <th class="px-8 py-5 text-[9px] font-black uppercase text-gray-400 italic text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($this->programs as $item)
                    <tr class="hover:bg-gray-50 transition-all group">
                        <td class="px-8 py-6">
                            <div class="w-12 h-12 rounded-2xl shadow-inner border-2 border-white flex items-center justify-center text-white font-black text-[10px]" style="background-color: {{ $item->warna_tema }}">
                                {{ substr($item->nama_program, 0, 2) }}
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <h4 class="text-sm font-black uppercase italic text-slate-800">{{ $item->nama_program }}</h4>
                            <p class="text-[9px] font-bold text-gray-400 italic tracking-tighter">{{ Str::limit($item->deskripsi_singkat, 50) }}</p>
                        </td>
                        <td class="px-8 py-6">
                            <span class="text-[10px] font-black italic text-slate-600 uppercase">{{ $item->max_quota == 0 ? 'Unlimited' : $item->max_quota . ' Seats' }}</span>
                        </td>
                        <td class="px-8 py-6 text-center">
                            @if($item->is_active)
                                <span class="px-5 py-1.5 bg-green-50 text-green-600 rounded-full text-[8px] font-black uppercase italic border border-green-100">Live</span>
                            @else
                                <span class="px-5 py-1.5 bg-gray-50 text-gray-300 rounded-full text-[8px] font-black uppercase italic border border-gray-100">Draft</span>
                            @endif
                        </td>
                        <td class="px-8 py-6 text-right space-x-2">
                            <button wire:click="edit({{ $item->id }})" class="p-3 bg-gray-100 text-slate-400 rounded-xl hover:bg-black hover:text-white transition-all shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                            <button wire:confirm="Yakin hapus program ini, Bos?" wire:click="delete({{ $item->id }})" class="p-3 bg-red-50 text-red-400 rounded-xl hover:bg-red-500 hover:text-white transition-all shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-24 text-center">
                            <p class="text-[10px] font-black uppercase text-gray-300 italic tracking-[0.5em]">No Programs Launched Yet.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-8 border-t border-gray-50">
            {{ $this->programs->links() }}
        </div>
    </div>
</div>
