@component('pages.super-admin.layouts.app')
<x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight uppercase italic tracking-tighter">
            {{ __('Manual Enrollment System') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- PANGGIL KOMPONEN VOLT DISINI --}}
            @livewire('super-admin.program.manual-enroll')
        </div>
    </div>
@endcomponent
