@component('pages.admin-program.layouts.app')
    <div class="py-12">
        <div class="max-w-3xl mx-auto px-4">
            {{-- Panggil Volt Component untuk Form Edit --}}
            @livewire('admin-program.content.edit-meta', ['content' => $content])
        </div>
    </div>

@endcomponent
