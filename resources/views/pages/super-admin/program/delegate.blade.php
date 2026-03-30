@component('pages.super-admin.layouts.app')
    <div class="py-12 bg-[#fafafa] min-h-screen">
        <div class="max-w-4xl mx-auto px-4 mb-8">
            <nav class="flex items-center gap-3 mb-4">
                <a href="{{ route('superadmin.programs.index') }}" class="text-[10px] font-black text-gray-400 uppercase tracking-widest hover:text-black">Programs</a>
                <span class="text-gray-300">/</span>
                <span class="text-[10px] font-black text-[#800000] uppercase tracking-widest italic">Delegation Area</span>
            </nav>
            <h1 class="text-3xl font-black text-gray-900 uppercase italic tracking-tighter">Otoritas Program</h1>
        </div>

        {{-- Memanggil Component Volt Delegasi --}}
        <livewire:super-admin.program.delegation :program="$program" />
    </div>
@endcomponent
