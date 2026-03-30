@component('pages.super-admin.layouts.app')
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <div class="w-10 h-10 bg-[#800000] rounded-xl flex items-center justify-center shadow-lg shadow-red-900/20">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </div>
            <h2 class="font-black text-2xl text-gray-900 leading-tight uppercase italic tracking-tighter">
                {{ __('Manual Enrollment System') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{--
                PANGGIL KOMPONEN VOLT DISINI
                Pastikan nama komponen sesuai dengan letak file:
                super-admin.program.bulk-enroll
            --}}

            <livewire:super-admin.program.bulk-enroll />

        </div>
    </div>
@endcomponent
