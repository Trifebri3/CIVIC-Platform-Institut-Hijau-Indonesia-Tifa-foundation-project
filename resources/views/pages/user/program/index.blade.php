@component('pages.user.layouts.app')
    <div class="max-w-7xl mx-auto px-6 py-12">
        <div class="mb-16">
            <h1 class="text-5xl font-black text-gray-900 uppercase tracking-tighter italic">Eksplorasi Program</h1>
            <p class="text-sm text-gray-400 font-bold uppercase tracking-[0.4em] mt-3">Mulai langkah besar kamu di sini</p>
        </div>

        @livewire('user.program.index')
    </div>
@endcomponent
