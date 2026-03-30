@component('pages.user.layouts.app') {{-- Sesuaikan dengan nama layout user Bos, misal 'layouts.user' --}}
    <div class="min-h-screen bg-[#F9FAFB]">

        {{-- Header Section: Minimalist & Clean --}}
        <div class="bg-white border-b border-gray-200">
            <div class="max-w-5xl mx-auto px-6 py-8">
                <div class="flex items-center gap-2 text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-2">
                    <a href="/" class="hover:text-black transition-colors">Dashboard</a>
                    <span>/</span>
                    <span class="text-[#800000]">Learning Progress</span>
                </div>
                <h1 class="text-3xl font-black text-slate-900 uppercase italic tracking-tighter">
                    Academic <span class="text-[#800000]">Record</span>
                </h1>
            </div>
        </div>

        {{-- Livewire Component Wrapper --}}
        <div class="py-10">
            {{-- Manggil Livewire Volt yang kita buat di livewire/user/progress/index.blade.php --}}
            @livewire('user.progress.index')
        </div>

    </div>

    <style>
    /* Styling tambahan biar scrollbar-nya tipis & modern ala Civic Platform */
    ::-webkit-scrollbar {
        width: 5px;
    }
    ::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    ::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 10px;
    }
    ::-webkit-scrollbar-thumb:hover {
        background: #800000;
    }
</style>

@endcomponent

