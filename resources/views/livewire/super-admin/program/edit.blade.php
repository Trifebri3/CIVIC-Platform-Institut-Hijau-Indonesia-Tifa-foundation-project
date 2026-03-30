<?php

use App\Models\Program;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

new class extends Component {
    use WithFileUploads;

    public $program;

    // State Fields (Lengkap sesuai database)
    public $name, $slug, $description, $quota, $registration_start, $registration_end;
    public $is_open, $redeem_code, $banner, $current_banner;
    public $id_number_format, $use_global_id, $status;

    public function mount(Program $program)
    {
        $this->program = $program;
        $this->name = $program->name;
        $this->slug = $program->slug;
        $this->description = $program->description;
        $this->quota = $program->quota;

        // Format untuk input datetime-local
        $this->registration_start = $program->registration_start->format('Y-m-d\TH:i');
        $this->registration_end = $program->registration_end->format('Y-m-d\TH:i');

        $this->is_open = (bool) $program->is_open;
        $this->redeem_code = $program->redeem_code;
        $this->id_number_format = $program->id_number_format;
        $this->use_global_id = (bool) $program->use_global_id;
        $this->status = $program->status;
        $this->current_banner = $program->banner;
    }

    public function update()
    {
        $validated = $this->validate([
            'name' => 'required|min:5|max:255',
            'description' => 'required',
            'quota' => 'required|integer|min:0',
            'registration_start' => 'required|date',
            'registration_end' => 'required|date|after:registration_start',
            'id_number_format' => 'required|string',
            'status' => 'required|in:draft,active,closed',
            'banner' => 'nullable|image|max:2048',
        ]);

        // Auto Update Slug jika nama berubah
        $validated['slug'] = Str::slug($this->name);

        // Handle Banner: Hapus yang lama, simpan yang baru
        if ($this->banner) {
            if ($this->current_banner) {
                Storage::disk('public')->delete($this->current_banner);
            }
            $validated['banner'] = $this->banner->store('program-banners', 'public');
        }

        // Pastikan Data Boolean masuk dengan benar
        $validated['is_open'] = (bool) $this->is_open;
        $validated['use_global_id'] = (bool) $this->use_global_id;
        $validated['redeem_code'] = $this->is_open ? null : $this->redeem_code;

        $this->program->update($validated);

        session()->flash('success', 'Konfigurasi Program "' . $this->name . '" Berhasil Diperbarui!');
        return redirect()->route('superadmin.programs.index');
    }
}; ?>

<div class="max-w-5xl mx-auto pb-20">
    <style>
        .maroon-card { border-top: 5px solid #800000; }
        .input-luxury { @apply w-full rounded-2xl border-gray-100 bg-gray-50/50 focus:bg-white focus:border-[#800000] focus:ring-[#800000] transition-all font-bold text-sm text-gray-700; }
        .label-luxury { @apply text-[10px] font-black text-[#800000] uppercase tracking-[0.2em] mb-2 block ml-1; }
    </style>

    <form wire:submit="update" class="space-y-8">
        {{-- Header & Banner Section --}}
        <div class="bg-white rounded-[3rem] p-10 shadow-2xl shadow-gray-200/50 border border-gray-100 maroon-card flex flex-col md:flex-row gap-10">
            <div class="w-full md:w-1/3">
                <label class="label-luxury">Update Banner</label>
                <label class="block group cursor-pointer">
                    <div class="relative h-56 w-full rounded-[2.5rem] bg-gray-50 border-2 border-dashed border-gray-200 flex flex-col items-center justify-center overflow-hidden hover:border-[#800000] transition-all duration-500">
                        @if ($banner)
                            <img src="{{ $banner->temporaryUrl() }}" class="absolute inset-0 h-full w-full object-cover">
                        @elseif($current_banner)
                            <img src="{{ asset('storage/'.$current_banner) }}" class="absolute inset-0 h-full w-full object-cover">
                        @else
                            <div class="text-center p-6">
                                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                        @endif

                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center backdrop-blur-sm">
                            <svg class="w-8 h-8 text-white mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path></svg>
                            <span class="text-[9px] font-black text-white uppercase tracking-widest">Ganti Gambar</span>
                        </div>
                        <input type="file" wire:model="banner" class="hidden">
                    </div>
                </label>
            </div>

            <div class="flex-1 space-y-6">
                <div>
                    <h2 class="text-4xl font-black text-gray-900 uppercase tracking-tighter italic leading-none mb-2">Master Edit</h2>
                    <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">Modifikasi parameter program: {{ $slug }}</p>
                </div>

                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label class="label-luxury">Nama Program</label>
                        <input type="text" wire:model="name" class="input-luxury py-4 px-6 text-lg">
                        @error('name') <span class="text-red-600 text-[10px] font-bold">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="label-luxury">Status Saat Ini</label>
                        <select wire:model="status" class="input-luxury uppercase tracking-[0.2em] font-black text-[10px]">
                            <option value="draft">DRAFT (Hidden)</option>
                            <option value="active">ACTIVE (Published)</option>
                            <option value="closed">CLOSED (Archived)</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- Row 2: Logic Settings --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            {{-- Box 1: Timeline --}}
            <div class="bg-white rounded-[3rem] p-10 shadow-sm border border-gray-100 space-y-6">
                <div class="flex items-center gap-3 border-b border-gray-50 pb-6">
                    <div class="h-8 w-1.5 bg-[#800000] rounded-full"></div>
                    <h4 class="text-sm font-black text-gray-800 uppercase tracking-[0.2em]">Timeline Control</h4>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="label-luxury">Start Date</label>
                        <input type="datetime-local" wire:model="registration_start" class="input-luxury">
                    </div>
                    <div>
                        <label class="label-luxury">End Date</label>
                        <input type="datetime-local" wire:model="registration_end" class="input-luxury">
                    </div>
                </div>

                <div>
                    <label class="label-luxury">Limit Kuota</label>
                    <input type="number" wire:model="quota" class="input-luxury py-4 px-6">
                </div>
            </div>

            {{-- Box 2: Enrollment System --}}
            <div class="bg-white rounded-[3rem] p-10 shadow-sm border border-gray-100 space-y-6">
                <div class="flex items-center gap-3 border-b border-gray-50 pb-6">
                    <div class="h-8 w-1.5 bg-[#800000] rounded-full"></div>
                    <h4 class="text-sm font-black text-gray-800 uppercase tracking-[0.2em]">Enrollment Logic</h4>
                </div>

                <div class="flex flex-wrap gap-8 bg-gray-50 p-6 rounded-2xl">
                    <label class="flex items-center gap-4 cursor-pointer">
                        <input type="checkbox" wire:model.live="is_open" class="w-5 h-5 rounded border-gray-300 text-[#800000] focus:ring-[#800000]">
                        <span class="text-[11px] font-black text-gray-600 uppercase tracking-widest">Akses Terbuka</span>
                    </label>
                    <label class="flex items-center gap-4 cursor-pointer">
                        <input type="checkbox" wire:model.live="use_global_id" class="w-5 h-5 rounded border-gray-300 text-[#800000] focus:ring-[#800000]">
                        <span class="text-[11px] font-black text-gray-600 uppercase tracking-widest">Pakai ID Global</span>
                    </label>
                </div>

                @if(!$is_open)
                <div class="animate-in slide-in-from-top-4 duration-300">
                    <label class="label-luxury">Kode Redeem Aktif</label>
                    <input type="text" wire:model="redeem_code" class="input-luxury text-center uppercase tracking-[0.4em] font-black border-red-200">
                </div>
                @endif

                @if(!$use_global_id)
                <div class="animate-in slide-in-from-top-4 duration-300">
                    <label class="label-luxury">Format Nomor Peserta</label>
                    <input type="text" wire:model="id_number_format" class="input-luxury font-mono text-center">
                </div>
                @endif
            </div>
        </div>

        {{-- Description --}}
        <div class="bg-white rounded-[3rem] p-10 shadow-sm border border-gray-100">
            <label class="label-luxury">Update Deskripsi Program</label>
            <textarea wire:model="description" rows="6" class="input-luxury p-8"></textarea>
        </div>

        {{-- Footer Actions --}}
        <div class="flex flex-col md:flex-row items-center justify-between gap-6 pt-10">
            <button type="button" onclick="history.back()" class="text-[11px] font-black text-gray-400 uppercase tracking-[0.5em] hover:text-red-700 transition-all">
                &larr; Discard Changes
            </button>

            <button type="submit" class="w-full md:w-auto bg-[#800000] hover:bg-black text-white px-20 py-6 rounded-[2rem] font-black text-xs uppercase tracking-[0.4em] shadow-2xl shadow-red-900/40 transition-all active:scale-95 flex items-center justify-center gap-4">
                <span>Save All Changes</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
            </button>
        </div>
    </form>
</div>
