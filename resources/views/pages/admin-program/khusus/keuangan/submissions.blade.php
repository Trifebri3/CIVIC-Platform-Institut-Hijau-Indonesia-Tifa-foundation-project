@component('pages.admin-program.layouts.app') {{-- Atau pakai <x-admin-layout> sesuai tema Bos --}}

<div class="min-h-screen bg-[#FAFAFA] pb-20">

    {{-- Header Navigation --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-10">
        <div class="flex items-center gap-4 mb-8">
            <a href="{{ route('admin.program.keuangan.index') }}"
               class="group flex items-center gap-2 text-[10px] font-black uppercase italic text-gray-400 hover:text-black transition-all">
                <span class="w-8 h-8 flex items-center justify-center rounded-full bg-white shadow-sm group-hover:bg-black group-hover:text-white transition-all">←</span>
                Back to Periods
            </a>
        </div>

        {{-- Summary Card (Quick Info) --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
            <div class="md:col-span-2 bg-white p-8 rounded-[3rem] shadow-xl border border-gray-50 flex flex-col justify-center">
                <h1 class="text-3xl font-black uppercase italic tracking-tighter text-slate-800 leading-none">
                    Review <br> <span class="text-[#800000]">Submissions</span>
                </h1>
                <p class="text-[9px] font-bold text-gray-400 uppercase italic tracking-[0.3em] mt-4">
                    {{ $period->name }} — Fiscal Year {{ $period->start_at->format('Y') }}
                </p>
            </div>

            <div class="bg-black p-8 rounded-[3rem] shadow-xl flex flex-col justify-center items-center text-center">
                <p class="text-[8px] font-black text-slate-500 uppercase italic tracking-widest mb-2">Total Budget Cap</p>
                <h3 class="text-2xl font-black text-white italic">
                    <span class="text-[10px] text-slate-400">Rp</span> {{ number_format($period->max_total_budget, 0, ',', '.') }}
                </h3>
            </div>

            <div class="bg-[#800000] p-8 rounded-[3rem] shadow-xl flex flex-col justify-center items-center text-center">
                <p class="text-[8px] font-black text-red-200 uppercase italic tracking-widest mb-2">Deadline Submission</p>
                <h3 class="text-xl font-black text-white italic uppercase">
                    {{ $period->end_at->format('d M Y') }}
                </h3>
                <p class="text-[8px] font-bold text-red-300 uppercase mt-1">
                    {{ $period->end_at->diffForHumans() }}
                </p>
            </div>
        </div>

        {{-- Main Livewire Component --}}
        <div class="relative">
            {{-- Kita panggil Component List-nya di sini Bos --}}
            @livewire('admin-program.programkhusus.keuangan.submission-list', ['period' => $period])
        </div>

    </div>
</div>

{{-- Notification Toast (Optional - Jika Bos pakai custom notification) --}}
<script>
    window.addEventListener('notify', event => {
        // Logika toast notification Bos di sini (SweetAlert / Alpine.js)
        alert(event.detail.message);
    });
</script>
@endcomponent

