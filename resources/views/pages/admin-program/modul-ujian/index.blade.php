@component('pages.admin-program.layouts.app')
    <div class="py-12 bg-[#fafafa] min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Biarkan Livewire yang bekerja di sini --}}
            @livewire('admin-program.modul-ujian.index', ['sub_program_id' => $subProgram->id])
        </div>
    </div>
@endcomponent
