@component('pages.user.layouts.app')
    {{-- Header --}}
    @slot('header')
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-black uppercase italic tracking-tighter text-slate-800">
                    Pengajuan <span class="text-[#800000]">RAB Program</span>
                </h2>
                <p class="text-[10px] font-bold text-gray-400 uppercase italic tracking-widest mt-1">
                    Periode: {{ $period->name }}
                </p>
            </div>
            <a href="{{ route('user.tor.history') }}" class="text-[10px] font-black uppercase italic text-gray-400 hover:text-black transition-colors">
                ← Batal & Kembali
            </a>
        </div>
    @endslot

    {{-- Memanggil Komponen Volt RAB --}}
    <div class="py-12">
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Perhatikan path: user.khusus.rab-submission-form --}}
            @livewire('user.khusus.rab-submission-form', ['period' => $period])
        </div>
    </div>
@endcomponent
