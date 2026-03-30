@component('pages.admin-program.layouts.app', ['program' => $program])
    <div class="relative min-h-[600px] py-10">
        {{-- Panggil Livewire Volt-nya di sini --}}
        @livewire('admin-program.tracking.global-index', ['program' => $program])
    </div>
@endcomponent
