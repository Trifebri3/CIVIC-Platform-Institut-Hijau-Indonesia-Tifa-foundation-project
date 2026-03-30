<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $email = '';

    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        $status = Password::sendResetLink(
            $this->only('email')
        );

        if ($status != Password::RESET_LINK_SENT) {
            $this->addError('email', __($status));
            return;
        }

        $this->reset('email');
        session()->flash('status', __($status));
    }
}; ?>

<div class="w-full max-w-md mx-auto">
    <div class="text-center mb-10">
        <h2 class="text-2xl font-extrabold tracking-tight text-gray-900">Pemulihan Akun</h2>
        <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#800000] mt-2">Atur ulang kata sandi Anda</p>
    </div>

    <div class="bg-white p-8 lg:p-10 rounded-3xl shadow-xl shadow-gray-200/50 border border-gray-50">
        <div class="mb-8 text-sm leading-relaxed text-gray-500 text-center px-2">
            {{ __('Jangan khawatir. Cukup masukkan alamat email Anda dan kami akan mengirimkan tautan untuk memilih kata sandi baru.') }}
        </div>

        <x-auth-session-status class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-r-xl text-[11px] font-bold text-green-700 uppercase tracking-wide" :status="session('status')" />

        <form wire:submit="sendPasswordResetLink" class="space-y-6">
            <div class="space-y-1">
                <label for="email" class="text-[10px] font-bold uppercase tracking-widest text-gray-400 ml-1">Alamat Email</label>
                <input wire:model="email" id="email"
                       class="block w-full px-4 py-3 rounded-xl border-gray-100 bg-gray-50 text-sm focus:border-[#800000] focus:ring-[#800000] transition-all duration-200"
                       type="email" name="email" required autofocus
                       placeholder="nama@email.com" />
                <x-input-error :messages="$errors->get('email')" class="mt-1 ml-1" />
            </div>

            <div class="pt-2">
                <button type="submit" wire:loading.attr="disabled"
                        class="w-full bg-[#800000] text-white py-4 rounded-xl font-bold text-xs uppercase tracking-[0.2em] hover:bg-black transition-all duration-300 transform active:scale-[0.98] shadow-lg shadow-red-900/20 disabled:opacity-70">
                    <span wire:loading.remove>Kirim Tautan Pemulihan</span>
                    <span wire:loading class="flex items-center justify-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        Memproses...
                    </span>
                </button>
            </div>
        </form>
    </div>

    <div class="text-center mt-8">
        <a href="{{ route('login') }}" class="text-[11px] font-bold uppercase tracking-widest text-gray-400 hover:text-[#800000] transition-colors inline-flex items-center gap-2">
            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"></path></svg>
            Kembali ke Halaman Masuk
        </a>
    </div>
</div>
