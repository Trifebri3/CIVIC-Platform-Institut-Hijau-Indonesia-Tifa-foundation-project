<?php

use App\Models\Program;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;

new class extends Component {
    use WithFileUploads;

    // State Fields (Sesuai kolom di Database)
    public $name;
    public $slug;
    public $description;
    public $banner;
    public $registration_start;
    public $registration_end;
    public $quota = 0;
    public $is_open = 1;
    public $redeem_code;
    public $id_number_format = 'REG-{YEAR}-{ID}';
    public $use_global_id = 0;
    public $status = 'draft';

    public function save()
    {
        // 1. Validasi Ketat
        $validated = $this->validate([
            'name' => 'required|min:5|max:255',
            'description' => 'nullable',
            'banner' => 'nullable|image|max:2048', // Max 2MB
            'registration_start' => 'required|date',
            'registration_end' => 'required|date|after:registration_start',
            'quota' => 'required|integer|min:0',
            'is_open' => 'required|boolean',
            'redeem_code' => 'nullable|required_if:is_open,0|min:4',
            'id_number_format' => 'required|string',
            'use_global_id' => 'required|boolean',
            'status' => 'required|in:draft,active,closed',
        ]);

        // 2. Handle Auto-Slug
        $validated['slug'] = Str::slug($this->name);

        // 3. Handle Banner Upload
        if ($this->banner) {
            $validated['banner'] = $this->banner->store('program-banners', 'public');
        }

        // 4. Pastikan Data Boolean & Enum Masuk
        $validated['is_open'] = (bool) $this->is_open;
        $validated['use_global_id'] = (bool) $this->use_global_id;
        $validated['status'] = $this->status;

        // 5. Insert ke Database (LENGKAP ANJAY)
        Program::create($validated);

        // 6. Flash & Redirect
        session()->flash('success', 'Program "' . $this->name . '" Berhasil Diluncurkan!');
        return redirect()->route('superadmin.programs.index');
    }
}; ?>

<div class="max-w-5xl mx-auto pb-20">
    <style>
        .maroon-card { border-top: 5px solid #800000; }
        .input-luxury { @apply w-full rounded-2xl border-gray-100 bg-gray-50/50 focus:bg-white focus:border-[#800000] focus:ring-[#800000] transition-all font-bold text-sm text-gray-700; }
        .label-luxury { @apply text-[10px] font-black text-[#800000] uppercase tracking-[0.2em] mb-2 block ml-1; }
    </style>

    <form wire:submit="save" class="space-y-8">
        {{-- Header Section: Banner & Main Identity --}}
        <div class="bg-white rounded-[3rem] p-10 shadow-2xl shadow-gray-200/50 border border-gray-100 maroon-card flex flex-col md:flex-row gap-10">
            <div class="w-full md:w-1/3">
                <label class="label-luxury">Cover Program</label>
                <label class="block group cursor-pointer">
                    <div class="relative h-56 w-full rounded-[2.5rem] bg-gray-50 border-2 border-dashed border-gray-200 flex flex-col items-center justify-center overflow-hidden hover:border-[#800000] transition-all duration-500">
                        @if ($banner)
                            <img src="{{ $banner->temporaryUrl() }}" class="absolute inset-0 h-full w-full object-cover">
                        @else
                            <div class="text-center p-6">
                                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Klik untuk Upload Banner</span>
                            </div>
                        @endif
                        <input type="file" wire:model="banner" class="hidden">
                    </div>
                </label>
                @error('banner') <span class="text-red-600 text-[10px] font-bold mt-2 block">{{ $message }}</span> @enderror
            </div>

            <div class="flex-1 space-y-6">
                <div>
                    <h2 class="text-4xl font-black text-gray-900 uppercase tracking-tighter italic leading-none mb-2">Deploy Program</h2>
                    <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">Pastikan data konfigurasi sudah benar.</p>
                </div>

                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label class="label-luxury">Judul Program</label>
                        <input type="text" wire:model="name" placeholder="Masukan Nama Program..." class="input-luxury py-4 px-6 text-lg">
                        @error('name') <span class="text-red-600 text-[10px] font-bold">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="label-luxury">Status Publikasi</label>
                        <select wire:model="status" class="input-luxury uppercase tracking-widest font-black text-[10px]">
                            <option value="draft">DRAFT (Sembunyikan)</option>
                            <option value="active">ACTIVE (Terbitkan)</option>
                            <option value="closed">CLOSED (Selesai/Tutup)</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- Row 2: Logic & Access --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            {{-- Timeline Box --}}
            <div class="bg-white rounded-[3rem] p-10 shadow-sm border border-gray-100 space-y-6">
                <div class="flex items-center gap-3 border-b border-gray-50 pb-6">
                    <div class="h-8 w-1.5 bg-[#800000] rounded-full"></div>
                    <h4 class="text-sm font-black text-gray-800 uppercase tracking-[0.2em]">Timeline & Quota</h4>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="label-luxury">Open Registration</label>
                        <input type="datetime-local" wire:model="registration_start" class="input-luxury">
                    </div>
                    <div>
                        <label class="label-luxury">Close Registration</label>
                        <input type="datetime-local" wire:model="registration_end" class="input-luxury">
                    </div>
                </div>

                <div>
                    <label class="label-luxury">Maksimal Peserta (0 = Tanpa Batas)</label>
                    <input type="number" wire:model="quota" class="input-luxury py-4 px-6">
                </div>
            </div>

            {{-- Security & ID Box --}}
            <div class="bg-white rounded-[3rem] p-10 shadow-sm border border-gray-100 space-y-6">
                <div class="flex items-center gap-3 border-b border-gray-50 pb-6">
                    <div class="h-8 w-1.5 bg-[#800000] rounded-full"></div>
                    <h4 class="text-sm font-black text-gray-800 uppercase tracking-[0.2em]">Access & Identity</h4>
                </div>

                <div class="flex flex-wrap gap-8 bg-gray-50 p-6 rounded-2xl">
                    <label class="flex items-center gap-4 cursor-pointer">
                        <input type="checkbox" wire:model.live="is_open" value="1" class="w-5 h-5 rounded border-gray-300 text-[#800000] focus:ring-[#800000]">
                        <span class="text-[11px] font-black text-gray-600 uppercase tracking-widest">Public Access</span>
                    </label>
                    <label class="flex items-center gap-4 cursor-pointer">
                        <input type="checkbox" wire:model.live="use_global_id" value="1" class="w-5 h-5 rounded border-gray-300 text-[#800000] focus:ring-[#800000]">
                        <span class="text-[11px] font-black text-gray-600 uppercase tracking-widest">Global ID</span>
                    </label>
                </div>

                @if(!$is_open)
                <div class="animate-in slide-in-from-top-4 duration-300">
                    <label class="label-luxury">Kode Akses / Redeem</label>
                    <input type="text" wire:model="redeem_code" placeholder="CONTOH: YOTA2026" class="input-luxury text-center uppercase tracking-[0.4em] font-black border-red-200">
                </div>
                @endif

                @if(!$use_global_id)
                <div class="animate-in slide-in-from-top-4 duration-300">
                    <label class="label-luxury">Pattern Nomor Induk</label>
                    <input type="text" wire:model="id_number_format" class="input-luxury font-mono text-center">
                    <p class="text-[9px] text-gray-400 mt-3 italic font-bold text-center">TAGS: {YEAR} = Tahun, {MM} = Bulan, {ID} = Urutan</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Description Box --}}
        <div class="bg-white rounded-[3rem] p-10 shadow-sm border border-gray-100">
            <label class="label-luxury">Deskripsi Lengkap Program</label>
            <textarea wire:model="description" rows="6" class="input-luxury p-8" placeholder="Tuliskan detail program, syarat, dan ketentuan..."></textarea>
        </div>

        {{-- Action Buttons --}}
        <div class="flex flex-col md:flex-row items-center justify-between gap-6 pt-10">
            <button type="button" onclick="history.back()" class="text-[11px] font-black text-gray-400 uppercase tracking-[0.5em] hover:text-[#800000] transition-all">
                &larr; Cancel Process
            </button>

            <button type="submit" class="w-full md:w-auto bg-[#800000] hover:bg-black text-white px-20 py-6 rounded-[2rem] font-black text-xs uppercase tracking-[0.4em] shadow-2xl shadow-red-900/40 transition-all active:scale-95 flex items-center justify-center gap-4">
                <span>Deploy Program Now</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
            </button>
        </div>
    </form>
</div>
