<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public function sendVerification(): void
    {
        if (Auth::user()->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('user.dashboard', absolute: false), navigate: true);
            return;
        }

        Auth::user()->sendEmailVerificationNotification();
        Session::flash('status', 'verification-link-sent');
    }

    public function logout(Logout $logout): void
    {
        $logout();
        $this->redirect('/', navigate: true);
    }
}; ?>

<div class="w-full max-w-md mx-auto">
    <div class="text-center mb-10">
        <h2 class="text-2xl font-extrabold tracking-tight text-gray-900">Verifikasi Email</h2>
        <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#800000] mt-2">Satu langkah lagi menuju platform</p>
    </div>

    <div class="bg-white p-8 lg:p-10 rounded-3xl shadow-xl shadow-gray-200/50 border border-gray-50">
        <div class="mb-8 text-sm leading-relaxed text-gray-500 text-center">
            {{ __('Terima kasih telah bergabung! Silakan verifikasi alamat email Anda dengan mengklik tautan yang baru saja kami kirimkan. Jika Anda tidak menerima email tersebut, kami akan dengan senang hati mengirimkan kembali.') }}
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="mb-8 p-4 bg-green-50 border-l-4 border-green-500 rounded-r-xl">
                <p class="text-[11px] font-bold text-green-700 leading-tight uppercase tracking-wide">
                    {{ __('Tautan verifikasi baru telah dikirimkan ke alamat email yang Anda gunakan saat pendaftaran.') }}
                </p>
            </div>
        @endif

        <div class="space-y-4">
            <button wire:click="sendVerification" wire:loading.attr="disabled"
                    class="w-full bg-[#800000] text-white py-4 rounded-xl font-bold text-xs uppercase tracking-[0.2em] hover:bg-black transition-all duration-300 transform active:scale-[0.98] shadow-lg shadow-red-900/20 disabled:opacity-70">
                <span wire:loading.remove>{{ __('Kirim Ulang Email Verifikasi') }}</span>
                <span wire:loading class="flex items-center justify-center gap-2">
                    <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    Mengirim...
                </span>
            </button>

            <div class="text-center pt-2">
                <button wire:click="logout" type="submit"
                        class="text-[10px] font-bold uppercase tracking-[0.2em] text-gray-400 hover:text-red-600 transition-colors underline underline-offset-4">
                    {{ __('Keluar dari Sesi') }}
                </button>
            </div>
        </div>
    </div>
</div>
