@component('pages.admin-surat.layouts.app')
    {{-- Header Section --}}
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-[#800000] rounded-lg">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <h2 class="font-black italic uppercase text-sm tracking-widest text-slate-700">
                {{ __('Administrasi Persuratan IHI') }}
            </h2>
        </div>
    </x-slot>

    {{-- Main Content --}}
    <div class="py-12 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Pastikan path pemanggilan volt sudah sesuai folder baru --}}
            @livewire('admin-surat.surat.surat-management')

        </div>
    </div>

    {{-- Script Tambahan --}}
    @push('scripts')
    <script>
        window.addEventListener('swal:modal', event => {
            // Logika SweetAlert
        });
    </script>
    @endpush
@endcomponent
