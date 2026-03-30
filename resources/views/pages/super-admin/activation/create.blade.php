@component('pages.super-admin.layouts.app')
    <x-slot name="title">Create New Activation Task</x-slot>

    <x-slot name="header">
        <h2 class="text-2xl font-black text-gray-800 uppercase italic">Build <span class="text-[#800000]">Task</span></h2>
        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Desain pertanyaan interaktif untuk peserta</p>
    </x-slot>

    <div class="py-8">
        @livewire('super-admin.question-builder', ['question' => $question ?? null])
    </div>
@endcomponent
