@component('pages.admin-program.layouts.app')

<div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-3xl mx-auto px-4">
            {{-- Memanggil Volt Component --}}
            @livewire('admin-program.content.edit-konten-kelas', ['content' => $content])
        </div>
    </div>
@endcomponent
