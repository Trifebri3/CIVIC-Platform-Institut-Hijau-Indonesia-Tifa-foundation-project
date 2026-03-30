@component('pages.admin-program.layouts.app')
    <div class="relative min-h-[600px] py-10">
        {{-- Memanggil Component Tracking yang kita buat tadi --}}
        <livewire:admin-program.tracking.index :subProgram="$subProgram" />
    </div>
@endcomponent
