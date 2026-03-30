@component('pages.admin-program.layouts.app')
    {{-- Header Slot (Jika layout app Bos mendukung slot header) --}}
<div class="min-h-screen bg-[#fafafa] py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Panggil Volt Component --}}
            {{-- Pastikan titik-titiknya sesuai folder di resources/views/livewire/... --}}
            <livewire:admin-program.modul-ujian.grading :id="$id" />

        </div>
    </div>
@endcomponent
