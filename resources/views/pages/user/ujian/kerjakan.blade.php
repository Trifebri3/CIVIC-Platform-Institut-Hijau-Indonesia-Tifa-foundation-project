@component('pages.user.layouts.app')
    {{-- Header atau Breadcrumb khusus halaman ini (Opsional) --}}

    {{-- Memanggil Komponen Volt sesuai path folder --}}
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Sesuaikan dengan lokasi file: user/modul-ujian/pgujian --}}
            @livewire('user.modul-ujian.pgujian', ['id' => $id])
        </div>
    </div>
@endcomponent
