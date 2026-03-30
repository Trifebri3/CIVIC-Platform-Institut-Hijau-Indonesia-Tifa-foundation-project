@component('pages.admin-program.layouts.app')
    <div class="mb-6 px-4">
        <a href="{{ route('admin.program.keuangan.index') }}" class="text-[10px] font-black uppercase italic text-gray-400 hover:text-[#800000]">← Back to List</a>
    </div>

    {{-- Memanggil Livewire Volt Component untuk Form Create --}}
    @livewire('admin-program.programkhusus.keuangan.create')
@endcomponent
