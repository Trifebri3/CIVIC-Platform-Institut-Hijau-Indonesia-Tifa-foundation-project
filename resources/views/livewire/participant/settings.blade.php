<?php

use App\Models\ProfileTemplate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;

    // State untuk User Dasar
    public $name;
    public $email;
    public $avatar;
    public $current_avatar_url;

    // State untuk Password
    public $current_password;
    public $new_password;
    public $new_password_confirmation;

    // State untuk Custom Fields
    public $custom_fields = [];
    public $templates;

    public function mount()
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->current_avatar_url = $user->avatar ? asset('storage/' . $user->avatar) : null;

        // Ambil Template & Data Profile
        $this->templates = ProfileTemplate::orderBy('order', 'asc')->get();
        $profileData = $user->profile->custom_fields_values ?? [];

        foreach ($this->templates as $template) {
            $this->custom_fields[$template->field_name] = $profileData[$template->field_name] ?? '';
        }
    }

public function updateProfile()
{
    // Ambil data user terbaru dari database
    $user = \App\Models\User::find(Auth::id());

    $rules = [
        'name' => 'required|string|max:255',
        'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
        'avatar' => 'nullable|image|max:2048',
    ];

    // ... (looping validasi custom fields tetap sama)
    $this->validate($rules);

    // 1. Logika Update Avatar
    if ($this->avatar) {
        // Hapus avatar lama jika ada (opsional tapi bagus untuk hemat storage)
        if ($user->avatar && \Storage::disk('public')->exists($user->avatar)) {
            \Storage::disk('public')->delete($user->avatar);
        }

        // Simpan file baru
        $path = $this->avatar->store('avatars', 'public');

        // Update kolom avatar di TABEL USERS
        $user->avatar = $path;
        $this->current_avatar_url = asset('storage/' . $path);
    }

    // 2. Simpan Data User Utama
    $user->name = $this->name;
    $user->email = $this->email;
    $user->save(); // Simpan perubahan ke tabel USERS

    // 3. Simpan Data Profile (Custom Fields)
    $user->profile()->updateOrCreate(
        ['user_id' => $user->id],
        ['custom_fields_values' => $this->custom_fields]
    );

    $this->avatar = null; // Reset input file
    session()->flash('success', 'Profil dan Foto berhasil diperbarui!');
}

    public function updatePassword()
    {
        $this->validate([
            'current_password' => 'required|current_password',
            'new_password' => 'required|min:8|confirmed',
        ]);

        Auth::user()->update([
            'password' => Hash::make($this->new_password)
        ]);

        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
        session()->flash('success_password', 'Password berhasil diubah!');
    }
}; ?>

<div class="max-w-5xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- Sisi Kiri: Navigasi/Status --}}
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-slate-100 text-center">
                <div class="relative inline-block group">
                    <div class="w-32 h-32 rounded-full overflow-hidden bg-slate-100 border-4 border-white shadow-xl">
                        @if($avatar)
                            <img src="{{ $avatar->temporaryUrl() }}" class="w-full h-full object-cover">
                        @elseif($current_avatar_url)
                            <img src="{{ $current_avatar_url }}" class="w-full h-full object-cover">
                        @else
                            <div class="flex items-center justify-center h-full text-slate-300 bg-slate-50">
                                <i class="fa-solid fa-user text-4xl"></i>
                            </div>
                        @endif
                    </div>
                    <label class="absolute bottom-0 right-0 bg-[#800000] text-white p-2 rounded-full cursor-pointer shadow-lg hover:scale-110 transition-transform border-2 border-white">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <input type="file" wire:model="avatar" class="hidden">
                    </label>
                </div>
                <h3 class="mt-4 font-black text-xl text-slate-900 tracking-tighter">{{ $name }}</h3>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">{{ Auth::user()->role }}</p>
            </div>

            <div class="bg-[#800000] rounded-[2rem] p-8 text-white">
                <h4 class="font-black uppercase text-[10px] tracking-[0.3em] mb-4 opacity-60">Status Akun</h4>
                <div class="space-y-4">
                    <div class="flex justify-between text-sm">
                        <span class="opacity-70">Terverifikasi</span>
                        <i class="fa-solid fa-circle-check {{ Auth::user()->email_verified_at ? 'text-green-400' : 'text-slate-400' }}"></i>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="opacity-70">Profil</span>
                        <span class="font-bold text-[10px] uppercase">Lengkap</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sisi Kanan: Form --}}
        <div class="lg:col-span-2 space-y-8">

            {{-- Form Profil Utama --}}
            <div class="bg-white rounded-[2.5rem] p-10 shadow-sm border border-slate-100">
                <div class="flex justify-between items-center mb-10">
                    <h3 class="font-black text-2xl text-slate-900 tracking-tighter italic uppercase">Informasi Profil</h3>
                    @if(session('success'))
                        <span class="text-[10px] font-black text-green-600 bg-green-50 px-4 py-2 rounded-full uppercase animate-pulse">Berhasil Disimpan!</span>
                    @endif
                </div>

                <form wire:submit.prevent="updateProfile" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">Nama Lengkap</label>
                            <input type="text" wire:model="name" class="w-full p-4 bg-slate-50 rounded-2xl border-none focus:ring-2 focus:ring-[#800000] transition-all">
                            @error('name') <span class="text-red-500 text-[10px] font-bold uppercase">{{ $message }}</span> @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">Alamat Email</label>
                            <input type="email" wire:model="email" class="w-full p-4 bg-slate-50 rounded-2xl border-none focus:ring-2 focus:ring-[#800000] transition-all">
                            @error('email') <span class="text-red-500 text-[10px] font-bold uppercase">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6 pt-4 border-t border-slate-50">
                        @foreach($templates as $template)
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">{{ $template->field_label }}</label>
                                @if($template->field_type === 'textarea')
                                    <textarea wire:model="custom_fields.{{ $template->field_name }}" class="w-full p-4 bg-slate-50 rounded-2xl border-none focus:ring-2 focus:ring-[#800000] transition-all min-h-[100px]"></textarea>
                                @else
                                    <input type="{{ $template->field_type }}" wire:model="custom_fields.{{ $template->field_name }}" class="w-full p-4 bg-slate-50 rounded-2xl border-none focus:ring-2 focus:ring-[#800000] transition-all">
                                @endif
                                @error('custom_fields.'.$template->field_name) <span class="text-red-500 text-[10px] font-bold uppercase">{{ $message }}</span> @enderror
                            </div>
                        @endforeach
                    </div>

                    <div class="pt-6">
                        <button type="submit" wire:loading.attr="disabled" class="bg-[#800000] text-white px-10 py-4 rounded-full font-black uppercase text-[10px] tracking-[0.3em] shadow-xl shadow-red-900/20 hover:scale-105 active:scale-95 transition-all">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

            {{-- Form Password --}}
            <div class="bg-white rounded-[2.5rem] p-10 shadow-sm border border-slate-100">
                <h3 class="font-black text-2xl text-slate-900 tracking-tighter italic uppercase mb-8">Keamanan</h3>

                <form wire:submit.prevent="updatePassword" class="space-y-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">Password Saat Ini</label>
                        <input type="password" wire:model="current_password" class="w-full p-4 bg-slate-50 rounded-2xl border-none focus:ring-2 focus:ring-slate-900 transition-all">
                        @error('current_password') <span class="text-red-500 text-[10px] font-bold uppercase">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">Password Baru</label>
                            <input type="password" wire:model="new_password" class="w-full p-4 bg-slate-50 rounded-2xl border-none focus:ring-2 focus:ring-slate-900 transition-all">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">Konfirmasi Password</label>
                            <input type="password" wire:model="new_password_confirmation" class="w-full p-4 bg-slate-50 rounded-2xl border-none focus:ring-2 focus:ring-slate-900 transition-all">
                        </div>
                    </div>
                    @error('new_password') <span class="text-red-500 text-[10px] font-bold uppercase">{{ $message }}</span> @enderror

                    <div class="pt-4 flex items-center justify-between">
                        <button type="submit" class="bg-slate-900 text-white px-10 py-4 rounded-full font-black uppercase text-[10px] tracking-[0.3em] shadow-xl shadow-slate-900/20 hover:scale-105 active:scale-95 transition-all">
                            Ganti Password
                        </button>
                        @if(session('success_password'))
                            <span class="text-[10px] font-black text-green-600 uppercase">Berhasil Diperbarui</span>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
