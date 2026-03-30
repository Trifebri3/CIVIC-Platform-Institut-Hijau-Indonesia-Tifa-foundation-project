<?php

use App\Models\ProgramProfile;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;

    public $profile;

    // Form States
    public $program_name, $address, $province, $city_regency, $district, $village;
    public $latitude, $longitude, $coordinator_name, $coordinator_phone, $main_photo;
    public $existing_photo;

    public function mount()
    {
        // Cari profil global user
        $this->profile = ProgramProfile::where('user_id', auth()->id())->first();

        if ($this->profile) {
            $this->program_name = $this->profile->program_name;
            $this->address = $this->profile->address;
            $this->province = $this->profile->province;
            $this->city_regency = $this->profile->city_regency;
            $this->district = $this->profile->district;
            $this->village = $this->profile->village;
            $this->latitude = $this->profile->latitude;
            $this->longitude = $this->profile->longitude;
            $this->coordinator_name = $this->profile->coordinator_name;
            $this->coordinator_phone = $this->profile->coordinator_phone;
            $this->existing_photo = $this->profile->main_photo;
        }
    }

    public function saveProfile()
    {
        $this->validate([
            'program_name' => 'required|min:5|string',
            'address' => 'required|string',
            'province' => 'required|string',
            'city_regency' => 'required|string',
            'district' => 'required|string',
            'village' => 'required|string',
            'coordinator_name' => 'required|string',
            'coordinator_phone' => 'required|numeric',
            'main_photo' => $this->profile ? 'nullable|image|max:2048' : 'required|image|max:2048',
        ]);

        $data = [
            'program_name' => $this->program_name,
            'address' => $this->address,
            'province' => $this->province,
            'city_regency' => $this->city_regency,
            'district' => $this->district,
            'village' => $this->village,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'coordinator_name' => $this->coordinator_name,
            'coordinator_phone' => $this->coordinator_phone,
            'is_completed' => true,
        ];

        // Jika ada foto baru yang diupload
        if ($this->main_photo) {
            $data['main_photo'] = $this->main_photo->store('program-photos', 'public');
        }

        $this->profile = ProgramProfile::updateOrCreate(
            ['user_id' => auth()->id()],
            $data
        );

        session()->flash('message', 'Profil Program Berhasil Disimpan!');

        // Opsional: Redirect jika ini pengisian pertama kali
        // return redirect()->route('user.dashboard');
    }
}; ?>

<div class="max-w-5xl mx-auto">
    {{-- Alert --}}
    @if (session()->has('message'))
        <div class="mb-8 p-5 bg-emerald-50 text-emerald-700 rounded-[2rem] border border-emerald-100 font-black italic text-xs uppercase tracking-[0.2em] flex items-center gap-3 shadow-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"></path></svg>
            {{ session('message') }}
        </div>
    @endif

    <div class="bg-white rounded-[3.5rem] shadow-2xl shadow-slate-200/50 border border-gray-50 overflow-hidden">
        {{-- Header Form --}}
        <div class="bg-slate-900 p-12 text-white relative overflow-hidden">
            <div class="relative z-10">
                <h2 class="text-4xl font-black italic uppercase tracking-tighter mb-2">Registration</h2>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.3em] italic">Official Program Identity & Profile</p>
            </div>
            {{-- Aksesoris Desain --}}
            <div class="absolute top-0 right-0 p-8 opacity-10">
                <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path></svg>
            </div>
        </div>

        <form wire:submit.prevent="saveProfile" class="p-12 space-y-10">
            {{-- Section 1: Basic Info --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                <div class="space-y-6">
                    <h3 class="text-[11px] font-black uppercase italic text-[#800000] tracking-[0.2em] border-b border-gray-100 pb-2">Program Identity</h3>

                    <div>
                        <label class="text-[10px] font-black uppercase italic text-slate-400 ml-2 mb-1 block">Nama Program / Kelompok</label>
                        <input type="text" wire:model="program_name" class="w-full rounded-2xl border-gray-100 focus:border-black focus:ring-0 text-sm p-4 bg-gray-50/50 font-bold text-slate-700" placeholder="Contoh: Pemberdayaan Desa Sejahtera">
                        @error('program_name') <span class="text-[9px] text-red-500 font-bold uppercase mt-1 ml-2">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-[10px] font-black uppercase italic text-slate-400 ml-2 mb-1 block">Koordinator</label>
                            <input type="text" wire:model="coordinator_name" class="w-full rounded-2xl border-gray-100 focus:border-black focus:ring-0 text-sm p-4 bg-gray-50/50 font-bold text-slate-700">
                        </div>
                        <div>
                            <label class="text-[10px] font-black uppercase italic text-slate-400 ml-2 mb-1 block">WhatsApp No.</label>
                            <input type="text" wire:model="coordinator_phone" class="w-full rounded-2xl border-gray-100 focus:border-black focus:ring-0 text-sm p-4 bg-gray-50/50 font-bold text-slate-700">
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <h3 class="text-[11px] font-black uppercase italic text-[#800000] tracking-[0.2em] border-b border-gray-100 pb-2">Documentation</h3>

                    <div>
                        <label class="text-[10px] font-black uppercase italic text-slate-400 ml-2 mb-1 block">Main Activity Photo</label>
                        <div class="relative group">
                            <div class="w-full h-44 rounded-[2rem] border-2 border-dashed border-gray-200 flex flex-col items-center justify-center bg-gray-50/50 hover:bg-gray-100 transition-all overflow-hidden">
                                @if ($main_photo)
                                    <img src="{{ $main_photo->temporaryUrl() }}" class="w-full h-full object-cover">
                                @elseif ($existing_photo)
                                    <img src="{{ asset('storage/' . $existing_photo) }}" class="w-full h-full object-cover">
                                @else
                                    <svg class="w-8 h-8 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                                    <span class="text-[9px] font-black uppercase text-gray-400 italic">Click or Drag Image</span>
                                @endif
                                <input type="file" wire:model="main_photo" class="absolute inset-0 opacity-0 cursor-pointer">
                            </div>
                        </div>
                        @error('main_photo') <span class="text-[9px] text-red-500 font-bold uppercase mt-1 ml-2">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- Section 2: Location --}}
            <div class="space-y-6 pt-6">
                <h3 class="text-[11px] font-black uppercase italic text-[#800000] tracking-[0.2em] border-b border-gray-100 pb-2">Location Details</h3>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="text-[10px] font-black uppercase italic text-slate-400 ml-2 mb-1 block">Provinsi</label>
                        <input type="text" wire:model="province" class="w-full rounded-2xl border-gray-100 focus:border-black focus:ring-0 text-sm p-4 bg-gray-50/50 font-bold text-slate-700">
                    </div>
                    <div>
                        <label class="text-[10px] font-black uppercase italic text-slate-400 ml-2 mb-1 block">Kota / Kab.</label>
                        <input type="text" wire:model="city_regency" class="w-full rounded-2xl border-gray-100 focus:border-black focus:ring-0 text-sm p-4 bg-gray-50/50 font-bold text-slate-700">
                    </div>
                    <div>
                        <label class="text-[10px] font-black uppercase italic text-slate-400 ml-2 mb-1 block">Kecamatan</label>
                        <input type="text" wire:model="district" class="w-full rounded-2xl border-gray-100 focus:border-black focus:ring-0 text-sm p-4 bg-gray-50/50 font-bold text-slate-700">
                    </div>
                    <div>
                        <label class="text-[10px] font-black uppercase italic text-slate-400 ml-2 mb-1 block">Desa / Kelurahan</label>
                        <input type="text" wire:model="village" class="w-full rounded-2xl border-gray-100 focus:border-black focus:ring-0 text-sm p-4 bg-gray-50/50 font-bold text-slate-700">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-2">
                        <label class="text-[10px] font-black uppercase italic text-slate-400 ml-2 mb-1 block">Alamat Lengkap</label>
                        <textarea wire:model="address" class="w-full rounded-2xl border-gray-100 focus:border-black focus:ring-0 text-sm p-4 bg-gray-50/50 font-bold text-slate-700" rows="3"></textarea>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="text-[10px] font-black uppercase italic text-slate-400 ml-2 mb-1 block">Latitude</label>
                            <input type="text" wire:model="latitude" class="w-full rounded-2xl border-gray-100 focus:border-black focus:ring-0 text-sm p-4 bg-gray-50/50 font-bold text-slate-700" placeholder="-6.1234">
                        </div>
                        <div>
                            <label class="text-[10px] font-black uppercase italic text-slate-400 ml-2 mb-1 block">Longitude</label>
                            <input type="text" wire:model="longitude" class="w-full rounded-2xl border-gray-100 focus:border-black focus:ring-0 text-sm p-4 bg-gray-50/50 font-bold text-slate-700" placeholder="106.1234">
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-10">
                <button type="submit" wire:loading.attr="disabled" class="group w-full bg-slate-900 text-white py-6 rounded-[2.5rem] font-black italic uppercase tracking-[0.3em] hover:bg-black transition-all shadow-2xl shadow-slate-200 flex items-center justify-center gap-4">
                    <span wire:loading.remove>Update Program Profile</span>
                    <span wire:loading>Processing...</span>
                    <svg class="w-5 h-5 group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M14 5l7 7m0 0l-7 7m7-7H3" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                </button>
            </div>
        </form>
    </div>
</div>
