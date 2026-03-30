@component('pages.user.layouts.app')
    {{-- Header Khusus Halaman Pengerjaan --}}
    @slot('header')
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-black text-xl text-gray-900 leading-tight uppercase italic">
                    Examination <span class="text-[#800000]">Module</span>
                </h2>
                <p class="text-[10px] text-gray-400 font-bold tracking-widest uppercase">Portal Pengerjaan Tugas & Ujian</p>
            </div>

            {{-- Tombol Kembali --}}
            <a href="{{ url()->previous() }}" wire:navigate class="text-[10px] font-black uppercase tracking-tighter text-gray-400 hover:text-black transition-colors">
                &larr; Kembali ke Dashboard
            </a>
        </div>
    @endslot

    {{-- Content Area --}}
    <div class="py-12 bg-[#fafafa] min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Memanggil Komponen Volt --}}
            {{-- Pastikan path filenya: resources/views/livewire/user/modul-ujian/show.blade.php --}}
            {{-- Atau sesuaikan dengan lokasi file Volt yang Bos simpan --}}
            <livewire:user.modul-ujian.show :id="$id" />

        </div>
    </div>
@endcomponent
