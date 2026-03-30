@component('pages.super-admin.layouts.app')
    <x-slot name="title">Edit Program: {{ $program->name }}</x-slot>

    <div class="space-y-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-red-50 rounded-2xl">
                    <svg class="w-6 h-6 text-[#800000]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-black text-gray-900 uppercase tracking-tighter italic">Master Configuration</h1>
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Update parameters for {{ $program->slug }}</p>
                </div>
            </div>

            <a href="{{ route('superadmin.programs.index') }}" class="text-[10px] font-black text-gray-400 uppercase tracking-widest hover:text-red-700 transition">
                Cancel & Close
            </a>
        </div>

        {{-- Memanggil Volt Component Edit dengan Passing Model --}}
        @livewire('super-admin.program.edit', ['program' => $program])
    </div>
@endcomponent
