<?php

use App\Models\RabPeriod;
use App\Models\TorSubmission;
use App\Models\ProgramReport;
use Livewire\Volt\Component;

new class extends Component {
    public function with()
    {
        return [
            'periods' => RabPeriod::orderBy('end_at', 'desc')->get(),
        ];
    }

    /**
     * INDIKATOR 1: TOR GLOBAL
     * Cek apakah user sudah punya minimal 1 TOR yang statusnya 'approved'
     */
    public function isTorApproved()
    {
        return TorSubmission::where('user_id', auth()->id())
            ->where('status', 'approved')
            ->exists();
    }

    /**
     * INDIKATOR 2: LAPORAN BERANTAI (STRICT)
     * Cek apakah ada periode lama yang laporannya belum di-ACC
     */
    public function hasPendingPreviousReport($currentPeriodDate)
    {
        // Ambil periode-periode yang sudah berakhir sebelum periode ini
        $previousPeriods = RabPeriod::where('end_at', '<', $currentPeriodDate)->get();

        foreach ($previousPeriods as $p) {
            $reportApproved = ProgramReport::where('user_id', auth()->id())
                ->where('rab_period_id', $p->id)
                ->where('status', 'approved')
                ->exists();

            if (!$reportApproved) {
                return true; // Ditemukan "hutang" laporan
            }
        }
        return false;
    }
}; ?>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
    @forelse($periods as $period)
        @php
            $isOpen = $period->isOpen();

            // Cek TOR (Sekarang Global, tidak peduli ID periode berapa)
            $torApproved = $this->isTorApproved();

            // Cek Laporan (Tetap per periode sebelumnya)
            $hasDebt = $this->hasPendingPreviousReport($period->end_at);

            $canSubmit = $isOpen && $torApproved && !$hasDebt;
        @endphp

        <div class="relative bg-white rounded-[2.5rem] border border-gray-100 shadow-sm transition-all duration-500 hover:shadow-2xl group overflow-hidden">

            {{-- Accent Color --}}
            <div class="h-2 w-full {{ $canSubmit ? 'bg-emerald-500' : ($hasDebt ? 'bg-orange-500' : ($isOpen && !$torApproved ? 'bg-red-500' : 'bg-gray-100')) }}"></div>

            <div class="p-8">
                <div class="flex justify-between items-start mb-6">
                    @if($canSubmit)
                        <span class="text-[8px] font-black uppercase italic text-emerald-600">Access Granted</span>
                    @elseif($hasDebt)
                        <span class="text-[8px] font-black uppercase italic text-orange-600">Chain Locked</span>
                    @elseif(!$torApproved && $isOpen)
                        <span class="text-[8px] font-black uppercase italic text-red-600">TOR Required</span>
                    @else
                        <span class="text-[8px] font-black uppercase italic text-gray-400">Restricted</span>
                    @endif
                </div>

                <h3 class="text-2xl font-black text-slate-800 uppercase italic leading-none mb-6">
                    {{ $period->name }}
                </h3>

                <div class="space-y-4">
                    @if($canSubmit)
                        <a href="{{ route('user.rab.submit', $period->id) }}"
                           class="flex items-center justify-between w-full py-5 px-8 bg-black text-white rounded-2xl text-[10px] font-black uppercase italic hover:bg-[#800000] transition-all">
                            <span>Mulai Isi RAB</span>
                            <span>→</span>
                        </a>
                    @else
                        <div class="relative">
                            <button disabled class="w-full py-5 bg-gray-50 text-gray-300 rounded-2xl text-[10px] font-black uppercase italic border border-gray-100 cursor-not-allowed">
                                @if($hasDebt)
                                    LAPORAN LALU BELUM ACC
                                @elseif(!$torApproved && $isOpen)
                                    TOR BELUM ACC
                                @else
                                    PERIODE TUTUP
                                @endif
                            </button>

                            @if($hasDebt)
                                <p class="mt-4 text-[7px] font-black text-orange-700 uppercase text-center">
                                    ⚠️ Selesaikan Laporan Periode Sebelumnya
                                </p>
                            @elseif(!$torApproved && $isOpen)
                                <div class="mt-4 text-center">
                                    <a href="{{ route('user.tor.submit') }}" class="text-[8px] font-black text-[#800000] underline uppercase italic">
                                        Klik Disini Untuk Ajukan TOR
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @empty
        {{-- Empty State --}}
    @endforelse
</div>
