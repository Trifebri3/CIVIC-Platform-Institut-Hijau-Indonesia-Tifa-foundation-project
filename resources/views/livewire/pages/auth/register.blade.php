<?php

use App\Models\User;
use App\Models\Profile;
use App\Models\SettingBuatAkun;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function register(): void
    {
        // 1. Keamanan: Cek apakah pendaftaran dibuka
        $settings = SettingBuatAkun::first();
        if (!$settings || !$settings->is_open) {
            $this->addError('email', 'Mohon maaf, pendaftaran saat ini sedang ditutup oleh Admin.');
            return;
        }

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        // 2. Simpan User dengan Role 'user'
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => 'user',
            'is_profile_completed' => 0,
        ]);

        // 3. Otomatis buatkan record Profile
        Profile::create([
            'user_id' => $user->id,
            'custom_fields_values' => []
        ]);

        event(new Registered($user));

        Auth::login($user);

        // 4. Redirect ke Dashboard User yang sudah kita buat tadi
        $this->redirect(route('user.dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="w-full">
    @php
        $settings = \App\Models\SettingBuatAkun::first();
    @endphp

    @if(!$settings || !$settings->is_open)
        {{-- Tampilan Mewah Jika Pendaftaran Ditutup --}}
        <div class="text-center p-8 bg-red-50 rounded-3xl border-2 border-dashed border-[#800000]/20 animate-fadeIn">
            <div class="mb-4 inline-flex p-4 bg-red-100 text-[#800000] rounded-full">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-black text-gray-800 uppercase italic">Pendaftaran Ditutup</h2>
            <p class="text-sm text-gray-500 mt-2 font-medium">
                {{ $settings->pesan_tutup ?? 'Mohon maaf, saat ini pendaftaran belum dibuka kembali.' }}
            </p>
            <div class="mt-8">
                <a href="/" class="text-xs font-black uppercase tracking-[0.2em] text-[#800000] border-b-2 border-[#800000] pb-1 hover:text-black hover:border-black transition-all">
                    &larr; Kembali ke Beranda
                </a>
            </div>
        </div>
    @else
        {{-- Form Registrasi Asli --}}
        <form wire:submit="register" class="space-y-4">
            <div>
                <x-input-label for="name" :value="__('Nama Lengkap')" class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-1" />
                <x-text-input wire:model="name" id="name" class="block mt-1 w-full !rounded-xl !border-gray-100 !bg-gray-50 shadow-sm" type="text" name="name" required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="email" :value="__('Email Address')" class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-1" />
                <x-text-input wire:model="email" id="email" class="block mt-1 w-full !rounded-xl !border-gray-100 !bg-gray-50 shadow-sm" type="email" name="email" required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="password" :value="__('Password')" class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-1" />
                    <x-text-input wire:model="password" id="password" class="block mt-1 w-full !rounded-xl !border-gray-100 !bg-gray-50 shadow-sm" type="password" name="password" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="password_confirmation" :value="__('Konfirmasi')" class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-1" />
                    <x-text-input wire:model="password_confirmation" id="password_confirmation" class="block mt-1 w-full !rounded-xl !border-gray-100 !bg-gray-50 shadow-sm" type="password" name="password_confirmation" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>
            </div>

            <div class="flex flex-col gap-4 mt-6">
                <button type="submit" class="w-full bg-[#800000] text-white py-3 rounded-xl font-bold text-xs uppercase tracking-widest shadow-lg shadow-red-900/20 hover:bg-black transition-all transform active:scale-[0.98]">
                    Daftar Sekarang
                </button>

                <div class="text-center">
                    <a class="text-[10px] font-bold uppercase tracking-widest text-gray-400 hover:text-[#800000] transition" href="{{ route('login') }}" wire:navigate>
                        Sudah punya akun? <span class="text-[#800000]">Login</span>
                    </a>
                </div>
            </div>
        </form>
    @endif
</div>
