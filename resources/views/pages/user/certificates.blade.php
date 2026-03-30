@component('pages.user.layouts.app')
    {{-- Header atau Breadcrumb khusus halaman ini (Opsional) --}}
    @slot('header')
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Sertifikat
        </h2>
    @endslot

    {{-- Memanggil Komponen Volt --}}
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Nama komponen sesuai path di folder livewire/participant --}}
            <livewire:user.penilaian.index />
        </div>
    </div>
@endcomponent

