<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth; // Tambahkan ini
use App\Models\User; // Tambahkan ini
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    public function login(): void
    {
        $this->validate();
        $this->form->authenticate();
        Session::regenerate();
        $this->handleRedirect(Auth::user());
    }

    // FUNGSI BARU: Login sebagai Tamu
public function loginAsGuest(): void
{
    $guest = User::where('email', 'tamu@ihi.id')->first();

    if ($guest) {
        // Force Verify (Jalur Tol)
        if (!$guest->hasVerifiedEmail()) {
            $guest->forceFill([
                'email_verified_at' => now(),
            ])->save();
        }

        Auth::login($guest);
        Session::regenerate();

        $this->handleRedirect($guest);
    } else {
        $this->addError('guest', 'Akun tamu belum dibuat di database.');
    }
}

    // Helper untuk redirect agar kode tidak double
    private function handleRedirect($user): void
    {
        $dashboard = match ($user->role) {
            'superadmin'   => route('superadmin.dashboard', absolute: false),
            'adminprogram' => route('adminprogram.dashboard', absolute: false),
            'adminsurat'   => route('adminsurat.dashboard', absolute: false),
            'user'         => route('user.dashboard', absolute: false),
            default        => route('user.dashboard', absolute: false),
        };

        $this->redirectIntended(default: $dashboard, navigate: true);
    }
}; ?>

<div class="w-full max-w-md mx-auto">
    <div class="text-center mb-10">
        <h2 class="text-2xl font-extrabold tracking-tight text-gray-900">Selamat Datang</h2>
        <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#800000] mt-2">Silahkan masuk ke akun Anda</p>
    </div>

    <x-auth-session-status class="mb-6" :status="session('status')" />

    <div class="bg-white p-8 lg:p-10 rounded-3xl shadow-xl shadow-gray-200/50 border border-gray-50">
        <form wire:submit="login" class="space-y-6">
            <div class="space-y-1">
                <label for="email" class="text-[10px] font-bold uppercase tracking-widest text-gray-400 ml-1">Alamat Email</label>
                <input wire:model="form.email" id="email"
                       class="block w-full px-4 py-3 rounded-xl border-gray-100 bg-gray-50 text-sm focus:border-[#800000] focus:ring-[#800000] transition-all duration-200"
                       type="email" name="email" required autofocus autocomplete="username"
                       placeholder="nama@email.com" />
                <x-input-error :messages="$errors->get('form.email')" class="mt-1 ml-1" />
            </div>

            <div class="space-y-1">
                <div class="flex justify-between items-center px-1">
                    <label for="password" class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Kata Sandi</label>
                    @if (Route::has('password.request'))
                        <a class="text-[10px] font-bold uppercase tracking-widest text-[#800000] hover:underline" href="{{ route('password.request') }}" wire:navigate>
                            Lupa?
                        </a>
                    @endif
                </div>
                <input wire:model="form.password" id="password"
                       class="block w-full px-4 py-3 rounded-xl border-gray-100 bg-gray-50 text-sm focus:border-[#800000] focus:ring-[#800000] transition-all duration-200"
                       type="password" name="password" required autocomplete="current-password"
                       placeholder="••••••••" />
                <x-input-error :messages="$errors->get('form.password')" class="mt-1 ml-1" />
            </div>

            <div class="flex items-center justify-between px-1">
                <label for="remember" class="inline-flex items-center cursor-pointer">
                    <input wire:model="form.remember" id="remember" type="checkbox"
                           class="rounded-md border-gray-200 text-[#800000] shadow-sm focus:ring-[#800000] w-4 h-4" name="remember">
                    <span class="ms-2 text-[11px] font-bold uppercase tracking-wider text-gray-500 italic">{{ __('Ingat saya') }}</span>
                </label>
            </div>

            <div class="pt-2">
                <button type="submit" wire:loading.attr="disabled"
                        class="w-full bg-[#800000] text-white py-4 rounded-xl font-bold text-xs uppercase tracking-[0.2em] hover:bg-black transition-all duration-300 transform active:scale-[0.98] shadow-lg shadow-red-900/20 disabled:opacity-70">
                    <span wire:loading.remove>Masuk ke Platform</span>
                    <span wire:loading class="flex items-center justify-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        Otentikasi...
                    </span>
                </button>
            </div>
        </form>
        <div class="mt-6 border-t border-gray-100 pt-6">
    <button wire:click="loginAsGuest" type="button"
            class="w-full flex items-center justify-center gap-3 py-4 bg-slate-50 hover:bg-slate-100 text-slate-600 rounded-xl transition-all duration-300 border-2 border-dashed border-gray-200 group">
        <svg class="w-5 h-5 text-[#800000] opacity-70 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
        </svg>
        <div class="text-left">
            <p class="text-[10px] font-black uppercase tracking-widest leading-none">Mode Kunjungan</p>
            <p class="text-[8px] font-bold text-gray-400 uppercase mt-1 tracking-tighter italic">Lihat Platform Tanpa Login</p>
        </div>
    </button>
    <x-input-error :messages="$errors->get('guest')" class="mt-2 text-center" />
</div>
    </div>

    @if (Route::has('register'))
        <p class="text-center mt-8 text-[11px] font-bold uppercase tracking-widest text-gray-400">
            Belum punya akun?
            <a href="{{ route('register') }}" class="text-[#800000] hover:underline ml-1">Daftar Sekarang</a>
        </p>
    @endif
</div>
