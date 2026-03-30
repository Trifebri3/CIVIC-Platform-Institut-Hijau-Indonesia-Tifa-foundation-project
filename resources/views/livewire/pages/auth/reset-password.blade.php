<?php

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    #[Locked]
    public string $token = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function mount(string $token): void
    {
        $this->token = $token;
        $this->email = request()->string('email');
    }

    public function resetPassword(): void
    {
        $this->validate([
            'token' => ['required'],
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $status = Password::reset(
            $this->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) {
                $user->forceFill([
                    'password' => Hash::make($this->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status != Password::PASSWORD_RESET) {
            $this->addError('email', __($status));
            return;
        }

        Session::flash('status', __($status));
        $this->redirectRoute('login', navigate: true);
    }
}; ?>

<div class="w-full max-w-md mx-auto">
    <div class="text-center mb-10">
        <h2 class="text-2xl font-extrabold tracking-tight text-gray-900">Kata Sandi Baru</h2>
        <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#800000] mt-2">Perbarui keamanan akun Anda</p>
    </div>

    <div class="bg-white p-8 lg:p-10 rounded-3xl shadow-xl shadow-gray-200/50 border border-gray-50">
        <form wire:submit="resetPassword" class="space-y-5">
            <div class="space-y-1">
                <label for="email" class="text-[10px] font-bold uppercase tracking-widest text-gray-400 ml-1">Konfirmasi Email</label>
                <input wire:model="email" id="email"
                       class="block w-full px-4 py-3 rounded-xl border-gray-100 bg-gray-50 text-sm focus:border-[#800000] focus:ring-[#800000] transition-all duration-200"
                       type="email" name="email" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-1 ml-1" />
            </div>

            <div class="space-y-1">
                <label for="password" class="text-[10px] font-bold uppercase tracking-widest text-gray-400 ml-1">Kata Sandi Baru</label>
                <input wire:model="password" id="password"
                       class="block w-full px-4 py-3 rounded-xl border-gray-100 bg-gray-50 text-sm focus:border-[#800000] focus:ring-[#800000] transition-all duration-200"
                       type="password" name="password" required autocomplete="new-password"
                       placeholder="••••••••" />
                <x-input-error :messages="$errors->get('password')" class="mt-1 ml-1" />
            </div>

            <div class="space-y-1">
                <label for="password_confirmation" class="text-[10px] font-bold uppercase tracking-widest text-gray-400 ml-1">Ulangi Kata Sandi</label>
                <input wire:model="password_confirmation" id="password_confirmation"
                       class="block w-full px-4 py-3 rounded-xl border-gray-100 bg-gray-50 text-sm focus:border-[#800000] focus:ring-[#800000] transition-all duration-200"
                       type="password" name="password_confirmation" required autocomplete="new-password"
                       placeholder="••••••••" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 ml-1" />
            </div>

            <div class="pt-4">
                <button type="submit" wire:loading.attr="disabled"
                        class="w-full bg-[#800000] text-white py-4 rounded-xl font-bold text-xs uppercase tracking-[0.2em] hover:bg-black transition-all duration-300 transform active:scale-[0.98] shadow-lg shadow-red-900/20 disabled:opacity-70">
                    <span wire:loading.remove>Perbarui Kata Sandi</span>
                    <span wire:loading class="flex items-center justify-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        Menyimpan...
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
