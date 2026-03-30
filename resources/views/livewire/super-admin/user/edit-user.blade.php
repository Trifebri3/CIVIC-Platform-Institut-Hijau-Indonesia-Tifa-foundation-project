<?php

use App\Models\User;
use App\Models\ProfileTemplate;
use function Livewire\Volt\{state, mount, usesFileUploads};
use Illuminate\Support\Facades\Storage;

usesFileUploads();

state([
    'user' => null,
    'name' => '',
    'email' => '',
    'role' => '',
    'avatar' => null, // Untuk menampung file baru
    'current_avatar' => '', // Untuk menampilkan foto lama
    'custom_fields' => []
]);

mount(function (User $user) {
    $this->user = $user;
    $this->name = $user->name;
    $this->email = $user->email;
    $this->role = $user->role;
    $this->current_avatar = $user->avatar;
    $this->custom_fields = $user->profile->custom_fields_values ?? [];
});

$save = function () {
    $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $this->user->id,
        'role' => 'required|in:superadmin,adminprogram,user',
    ];

    if ($this->avatar) {
        $rules['avatar'] = 'image|max:2048'; // Max 2MB
    }

    $validated = $this->validate($rules);

    // Handle Upload Avatar
    if ($this->avatar) {
        // Hapus foto lama jika ada
        if ($this->user->avatar && Storage::disk('public')->exists($this->user->avatar)) {
            Storage::disk('public')->delete($this->user->avatar);
        }
        $path = $this->avatar->store('avatars', 'public');
        $this->user->avatar = $path;
    }

    $this->user->name = $this->name;
    $this->user->email = $this->email;
    $this->user->role = $this->role;
    $this->user->save();

    $this->user->profile->update([
        'custom_fields_values' => $this->custom_fields
    ]);

    session()->flash('success', 'Data & Avatar user berhasil diperbarui.');
    return redirect()->route('superadmin.users.index');
};

?>

<div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
    {{-- CSS INLINE DI DALAM DIV --}}
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #800000; }
        .avatar-gradient { background: linear-gradient(135deg, #800000 0%, #a52a2a 100%); }
    </style>

    <div class="bg-gradient-to-r from-[#800000] to-[#a52a2a] p-10">
        <div class="flex flex-col md:flex-row items-center gap-8">
            <div class="relative group">
                <div class="h-32 w-32 rounded-[2rem] bg-white p-1 shadow-2xl transition-transform group-hover:scale-105 duration-500">
                    @if($avatar)
                        {{-- Preview Foto Baru --}}
                        <img src="{{ $avatar->temporaryUrl() }}" class="h-full w-full rounded-[1.8rem] object-cover">
                    @elseif($current_avatar && Storage::disk('public')->exists($current_avatar))
                        {{-- Foto Lama --}}
                        <img src="{{ asset('storage/'.$current_avatar) }}" class="h-full w-full rounded-[1.8rem] object-cover">
                    @else
                        {{-- Inisial Jika Kosong --}}
                        <div class="h-full w-full rounded-[1.8rem] avatar-gradient flex items-center justify-center text-white text-4xl font-black">
                            {{ substr($name, 0, 1) ?: '?' }}
                        </div>
                    @endif
                </div>

                <label class="absolute -bottom-2 -right-2 h-10 w-10 bg-white rounded-xl shadow-xl flex items-center justify-center cursor-pointer hover:bg-gray-50 transition-all border border-gray-100">
                    <input type="file" wire:model="avatar" class="hidden">
                    <svg class="w-5 h-5 text-[#800000]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </label>

                <div wire:loading wire:target="avatar" class="absolute inset-0 bg-black/50 rounded-[2rem] flex items-center justify-center">
                    <div class="w-6 h-6 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                </div>
            </div>

            <div class="text-center md:text-left">
                <h3 class="text-3xl font-black text-white uppercase tracking-tighter italic">Edit Profil Peserta</h3>
                <div class="flex items-center justify-center md:justify-start gap-2 mt-2">
                    <span class="px-3 py-1 bg-white/10 backdrop-blur-md rounded-full text-[10px] font-black text-red-100 uppercase tracking-widest border border-white/20">
                        UID: #{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}
                    </span>
                    <span class="px-3 py-1 bg-green-500/20 backdrop-blur-md rounded-full text-[10px] font-black text-green-200 uppercase tracking-widest border border-green-500/30">
                        {{ $user->is_activated ? 'Terverifikasi' : 'Pending' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <form wire:submit="save" class="p-10 space-y-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">

            <div class="space-y-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="h-6 w-1 bg-[#800000] rounded-full"></div>
                    <h4 class="text-xs font-black text-gray-800 uppercase tracking-[0.2em]">Kredensial Akun</h4>
                </div>

                <div class="space-y-4">
                    <div class="group">
                        <label class="block text-[10px] font-black text-[#800000] uppercase mb-2 tracking-widest ml-1">Nama Lengkap</label>
                        <input type="text" wire:model="name"
                               class="w-full rounded-2xl border-gray-100 bg-gray-50/50 focus:bg-white focus:border-[#800000] focus:ring-[#800000] transition-all font-semibold text-gray-700">
                        @error('name') <span class="text-red-600 text-[10px] font-bold mt-2 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="group">
                        <label class="block text-[10px] font-black text-[#800000] uppercase mb-2 tracking-widest ml-1">Email Address</label>
                        <input type="email" wire:model="email"
                               class="w-full rounded-2xl border-gray-100 bg-gray-50/50 focus:bg-white focus:border-[#800000] focus:ring-[#800000] transition-all font-semibold text-gray-700">
                        @error('email') <span class="text-red-600 text-[10px] font-bold mt-2 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="group">
                        <label class="block text-[10px] font-black text-[#800000] uppercase mb-2 tracking-widest ml-1">Level Akses</label>
                        <select wire:model="role"
                                class="w-full rounded-2xl border-gray-100 bg-gray-50/50 focus:bg-white focus:border-[#800000] focus:ring-[#800000] transition-all font-bold text-gray-700 uppercase text-xs">
                            <option value="user">User / Peserta</option>
                            <option value="adminprogram">Admin Program</option>
                            <option value="superadmin">Super Admin</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="h-6 w-1 bg-[#800000] rounded-full"></div>
                    <h4 class="text-xs font-black text-gray-800 uppercase tracking-[0.2em]">Atribut Tambahan</h4>
                </div>

                <div class="max-h-[380px] overflow-y-auto pr-4 space-y-5 custom-scrollbar">
                    @php $templates = \App\Models\ProfileTemplate::orderBy('order')->get(); @endphp
                    @foreach($templates as $template)
                    <div class="group">
                        <label class="block text-[9px] font-black text-gray-400 uppercase mb-2 tracking-widest ml-1 group-hover:text-[#800000] transition-colors">
                            {{ $template->field_label }}
                        </label>
                        <input type="text"
                               wire:model="custom_fields.{{ $template->field_name }}"
                               class="w-full rounded-2xl border-gray-50 bg-gray-50/30 focus:bg-white focus:border-[#800000] focus:ring-[#800000] text-sm font-medium transition-all shadow-sm">
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between pt-10 border-t border-gray-50">
            <button type="button" onclick="history.back()" class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] hover:text-[#800000] transition-all italic">
                &larr; Batalkan Perubahan
            </button>

            <button type="submit"
                    class="bg-[#800000] hover:bg-[#600000] text-white px-12 py-5 rounded-[1.5rem] font-black text-[10px] uppercase tracking-[0.3em] shadow-2xl shadow-red-900/40 transition-all active:scale-95 flex items-center gap-4 group">
                <span class="group-hover:translate-x-1 transition-transform">Update Data User</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </button>
        </div>
    </form>
</div>
