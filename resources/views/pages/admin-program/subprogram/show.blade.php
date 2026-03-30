@component('pages.admin-program.layouts.app')

<div class="py-12">
        {{-- Memanggil Livewire Show Detail dengan Passing Parameter ID --}}
        @livewire('admin-program.subprogram.show-detail', ['subProgram' => $subProgram])
    </div>

@endcomponent
