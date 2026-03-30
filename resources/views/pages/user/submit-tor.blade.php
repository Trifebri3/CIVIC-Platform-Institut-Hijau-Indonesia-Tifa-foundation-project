@component('pages.user.layouts.app')
    {{-- Header / Breadcrumb --}}
    @slot('header')
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-black uppercase italic tracking-tighter text-slate-800">
                    Pengajuan <span class="text-[#800000]">TOR Baru</span>
                </h2>
                <p class="text-[10px] font-bold text-gray-400 uppercase italic tracking-widest mt-1">Lengkapi dokumen proposal Anda di bawah ini</p>
            </div>
            <a href="{{ route('user.tor.history') }}" class="text-[10px] font-black uppercase italic text-gray-400 hover:text-black">
                ← Kembali ke Riwayat
            </a>
        </div>
    @endslot

    {{-- Memanggil Komponen Volt Form Pengisian --}}
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Pastikan path komponen ini sesuai dengan lokasi file Volt Bos --}}
            @livewire('user.tor.submission-form', ['period' => $period])
        </div>
    </div>
@endcomponent
