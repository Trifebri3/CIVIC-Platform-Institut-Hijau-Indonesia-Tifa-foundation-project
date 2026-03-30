@component('pages.user.layouts.app')
    {{-- Header --}}
    @slot('header')
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-black uppercase italic tracking-tighter text-slate-800">
                    Riwayat <span class="text-[#800000]">Pengajuan TOR</span>
                </h2>
                <p class="text-[10px] font-bold text-gray-400 uppercase italic tracking-widest mt-1">Lacak status dan unduh dokumen proposal Anda</p>
            </div>
            <a href="{{ route('user.dashboard') }}" class="text-[10px] font-black uppercase italic text-gray-400 hover:text-black transition-colors">
                ← Kembali ke Dashboard
            </a>
        </div>
    @endslot

    {{-- Memanggil Komponen Volt --}}
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @livewire('user.tor.history-list')
        </div>
    </div>
@endcomponent
