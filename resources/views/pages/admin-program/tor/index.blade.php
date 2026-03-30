@component('pages.admin-program.layouts.app') {{-- Gunakan layout admin Anda --}}
    @slot('header')
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-black uppercase italic tracking-tighter text-slate-800">
                    TOR <span class="text-emerald-600">Approval Center</span>
                </h2>
                <p class="text-[10px] font-bold text-gray-400 uppercase italic mt-1">Review dan setujui proposal kegiatan user</p>
            </div>
        </div>
    @endslot

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @livewire('admin-program.programkhusus.approval-list')
        </div>
    </div>
@endcomponent
