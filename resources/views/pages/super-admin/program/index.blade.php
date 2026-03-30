@component('pages.super-admin.layouts.app')
    <x-slot name="title">Daftar Program</x-slot>

    <div class="space-y-6">
        {{-- Breadcrumb Luxury --}}
        <div class="flex items-center justify-between">
            <nav class="flex text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">
                <a href="{{ route('superadmin.dashboard') }}" class="hover:text-[#800000] transition">Dashboard</a>
                <span class="mx-3 text-gray-300">/</span>
                <span class="text-[#800000]">Manajemen Program</span>
            </nav>
        </div>
        

        {{-- Memanggil Volt Component Index --}}
        @livewire('super-admin.program.index')
    </div>
@endcomponent
