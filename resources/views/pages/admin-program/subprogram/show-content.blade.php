@component('pages.admin-program.layouts.app')

<div class="py-12">
        {{-- Memanggil Livewire Show Detail dengan Passing Parameter ID --}}
        @livewire('admin-program.content.show-by-sub', ['subProgram' => $subProgram])
        @livewire('admin-program.content.showisimodul', ['subProgram' => $subProgram])
    </div>

@endcomponent
