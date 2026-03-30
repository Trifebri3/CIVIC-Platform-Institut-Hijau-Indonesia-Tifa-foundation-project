@component('pages.user.layouts.app')
    <x-slot name="title">{{ $program->name }}</x-slot>

    <div class="min-h-screen bg-[#fafafa]">
        {{-- Breadcrumb Navigation --}}
        <div class="max-w-7xl mx-auto px-6 pt-8">
            <nav class="flex items-center gap-3 text-[10px] font-black uppercase tracking-[0.3em] text-gray-400">
                <a href="/" class="hover:text-[#800000] transition">Beranda</a>
                <div class="w-1 h-1 rounded-full bg-gray-300"></div>
                <a href="/programs" class="hover:text-[#800000] transition">Jelajahi Program</a>
                <div class="w-1 h-1 rounded-full bg-gray-300"></div>
                <span class="text-[#800000] italic">{{ $program->slug }}</span>
            </nav>
        </div>

        {{-- Memanggil Volt Component Detail & Enroll --}}
        @livewire('user.program.show', ['program' => $program])

        {{-- Footer Simple --}}

    </div>
@endcomponent
