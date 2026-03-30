@component('pages.admin-program.layouts.app')
    <div class="mb-6 px-4">
        <a href="{{ route('admin.program.keuangan.index') }}" class="text-[10px] font-black uppercase italic text-gray-400 hover:text-[#800000]">← Back</a>
    </div>

    {{-- Ambil $period dari Controller/Route --}}
    @livewire('admin-program.programkhusus.keuangan.edit-form', ['period' => $period])
@endcomponent
