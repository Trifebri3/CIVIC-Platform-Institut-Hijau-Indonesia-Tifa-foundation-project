@component('pages.user.layouts.app')
    <div class="py-12 bg-[#F8FAFC] min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Breadcrumb --}}
            <nav class="mb-8 flex items-center gap-3">
                <a href="{{ route('user.dashboard') }}" class="text-[10px] font-black uppercase italic text-slate-400 hover:text-black transition-all tracking-widest">Dashboard</a>
                <span class="text-slate-300">/</span>
                <span class="text-[10px] font-black uppercase italic text-[#800000] tracking-widest">Submit Period Report</span>
            </nav>

            <livewire:user.khusus.report-submission :period="$period" />

        </div>
    </div>
@endcomponent
