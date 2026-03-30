<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $password = '';

    /**
     * Confirm the current user's password.
     */
    public function confirmPassword(): void
    {
        $this->validate([
            'password' => ['required', 'string'],
        ]);

        if (! Auth::guard('web')->validate([
            'email' => Auth::user()->email,
            'password' => $this->password,
        ])) {
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }

        session(['auth.password_confirmed_at' => time()]);

        $this->redirectIntended(default: route('user.dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="w-full max-w-md mx-auto">
    <div class="text-center mb-10">
        <h2 class="text-2xl font-extrabold tracking-tight text-gray-900">Area Keamanan</h2>
        <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#800000] mt-2">Konfirmasi kata sandi Anda</p>
    </div>

    <div class="bg-white p-8 lg:p-10 rounded-3xl shadow-xl shadow-gray-200/50 border border-gray-50">
        <div class="mb-8 p-4 bg-gray-50 rounded-2xl flex items-start gap-3">
            <svg class="h-5 w-5 text-[#800000] mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
            </svg>
            <p class="text-xs leading-relaxed text-gray-500 italic">
                {{ __('Ini adalah area aplikasi yang aman. Silakan konfirmasi kata sandi Anda untuk memverifikasi identitas sebelum melanjutkan.') }}
            </p>
        </div>

        <form wire:submit="confirmPassword" class="space-y-6">
            <div class="space-y-1">
                <label for="password" class="text-[10px] font-bold uppercase tracking-widest text-gray-400 ml-1">Kata Sandi</label>
                <input wire:model="password" id="password"
                       class="block w-full px-4 py-3 rounded-xl border-gray-100 bg-gray-50 text-sm focus:border-[#800000] focus:ring-[#800000] transition-all duration-200"
                       type="password" name="password" required autocomplete="current-password"
                       placeholder="••••••••" autofocus />
                <x-input-error :messages="$errors->get('password')" class="mt-1 ml-1" />
            </div>

            <div class="pt-2">
                <button type="submit" wire:loading.attr="disabled"
                        class="w-full bg-[#800000] text-white py-4 rounded-xl font-bold text-xs uppercase tracking-[0.2em] hover:bg-black transition-all duration-300 transform active:scale-[0.98] shadow-lg shadow-red-900/20 disabled:opacity-70">
                    <span wire:loading.remove>Konfirmasi Identitas</span>
                    <span wire:loading class="flex items-center justify-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        Memverifikasi...
                    </span>
                </button>
            </div>
        </form>
    </div>

    <div class="text-center mt-8">
        <a href="javascript:history.back()" class="text-[10px] font-bold uppercase tracking-widest text-gray-400 hover:text-gray-900 transition-colors">
            Kembali ke Halaman Sebelumnya
        </a>
    </div>
</div>
