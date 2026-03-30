@component('pages.admin-program.layouts.app')
<div class="py-12 bg-[#fafafa] min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Breadcrumbs Mini --}}


            {{-- Pemanggilan Komponen Volt --}}
            @livewire('admin-program.modul-ujian.kelola-pg', ['id' => $id])
        </div>
    </div>
@endcomponent
