@component('pages.user.layouts.app')
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Layanan Persuratan IHI') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-slate-50 min-h-screen">
        @livewire('user.administrasi.surat-pengajuan')
    </div>
@endcomponent
