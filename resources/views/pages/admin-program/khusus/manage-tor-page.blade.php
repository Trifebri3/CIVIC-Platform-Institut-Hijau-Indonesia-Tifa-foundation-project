@component('pages.admin-program.layouts.app') {{-- Atau pakai <x-admin-layout> sesuai tema Bos --}}


    {{-- Memanggil Livewire Volt Component --}}
    @livewire('admin-program.programkhusus.manage-tor')
<div class="flex justify-between items-end mb-10">
    <div>
        <h1 class="text-4xl font-black uppercase italic tracking-tighter text-slate-800">Manage <span class="text-[#800000]">TOR Template</span></h1>
        <p class="text-[10px] font-bold text-gray-400 uppercase italic tracking-widest mt-2">Setting Periode & Dynamic Form Builder</p>
    </div>

    <div class="flex items-center gap-6">
        {{-- TOMBOL BARU: LIHAT JAWABAN USER --}}
        <a href="{{ route('admin.program.submissions') }}" class="group flex flex-col items-end">
            <span class="text-[10px] font-black uppercase italic text-slate-800 group-hover:text-[#800000] transition-colors">View Submissions —</span>
            <span class="text-[8px] font-bold text-gray-300 uppercase tracking-widest">Check Student Answers</span>
        </a>

        <button wire:click="resetForm" class="bg-black text-white px-6 py-3 rounded-2xl text-[10px] font-black uppercase italic hover:bg-[#800000] transition-all shadow-lg shadow-red-900/10">
            Create New Period +
        </button>
    </div>
</div>
@endcomponent
