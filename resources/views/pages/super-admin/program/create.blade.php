@component('pages.super-admin.layouts.app')
    <x-slot name="title">Launch Program Baru</x-slot>

    <div class="space-y-8">
        {{-- Back Button Luxury --}}
        <div class="flex items-center justify-between">
            <a href="{{ route('superadmin.programs.index') }}"
               class="inline-flex items-center text-[10px] font-black uppercase tracking-[0.3em] text-gray-400 hover:text-[#800000] transition group">
                <svg class="w-4 h-4 mr-2 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke List
            </a>
            <div class="h-px flex-1 bg-gray-100 mx-8"></div>
            <span class="text-[10px] font-black text-[#800000] uppercase tracking-[0.4em] italic">New Deployment</span>
        </div>

        {{-- Memanggil Volt Component Create --}}
        @livewire('super-admin.program.create')
    </div>
@endcomponent
