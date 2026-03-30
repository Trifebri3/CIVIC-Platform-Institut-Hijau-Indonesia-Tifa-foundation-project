@component('pages.admin-program.layouts.app')
    {{-- Breadcrumbs atau Header Simple --}}
    <div class="mb-6 px-4">
        <h2 class="text-2xl font-black uppercase italic text-slate-800">Finance <span class="text-[#800000]">Management</span></h2>
    </div>

    {{-- Memanggil Livewire Volt Component untuk List --}}
    @livewire('admin-program.programkhusus.keuangan.index')
@endcomponent
