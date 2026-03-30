<?php

use App\Models\{SubProgram, Absensi, KehadiranUser, User};
use Livewire\Volt\Component;
use Livewire\WithPagination; // Tambahkan ini
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

new class extends Component {
    use WithPagination; // Gunakan trait ini

    public SubProgram $subProgram;

    public function mount(SubProgram $subProgram)
    {
        // Kita cukup load absensinya saja di awal
        $this->subProgram = $subProgram->load(['absensis.kehadirans']);
    }

    public function exportPDF()
    {
        // Untuk PDF, kita ambil SEMUA user (tanpa pagination)
        $data = [
            'subProgram' => $this->subProgram,
            'users' => $this->subProgram->program->users,
            'date' => now()->format('d/m/Y H:i'),
        ];

        $pdf = Pdf::loadView('pdf.attendance-report', $data)->setPaper('a4', 'landscape');
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'rekap-' . $this->subProgram->slug . '.pdf');
    }

    public function with()
    {
        return [
            // Pagination kita pasang di sini (misal: 10 user per halaman)
            'registeredUsers' => $this->subProgram->program->users()->paginate(10),
            'allAbsensi' => $this->subProgram->absensis
        ];
    }
}; ?>
<div class="max-w-7xl mx-auto pb-24 antialiased">
    {{-- Header Mini (Tetap sama) --}}
    <div class="flex justify-between items-center mb-8 px-4">
        <div class="flex items-center gap-6">
            <div class="bg-[#800000] p-4 rounded-2xl shadow-lg shadow-red-900/20">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-width="2.5"/></svg>
            </div>
            <div>
                <h2 class="text-3xl font-black text-gray-900 uppercase italic tracking-tighter leading-none">Attendance <span class="text-[#800000]">Matrix</span></h2>
                <p class="text-[9px] text-gray-400 font-bold uppercase tracking-widest mt-1">{{ $subProgram->title }}</p>
            </div>
        </div>

        <button wire:click="exportPDF" class="bg-black hover:bg-[#800000] text-white px-6 py-3 rounded-xl font-black text-[10px] uppercase tracking-widest transition-all active:scale-95 shadow-lg">
            Generate PDF (Full)
        </button>
    </div>

    {{-- Compact Table Card --}}
    <div class="bg-white rounded-[2rem] shadow-xl border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/80 border-b border-gray-100">
                        <th class="px-8 py-5 text-[9px] font-black uppercase text-gray-400 tracking-widest italic sticky left-0 bg-gray-50 z-10 w-64">Mahasiswa / NIM</th>
                        @foreach($allAbsensi as $absen)
                            <th class="px-4 py-5 text-[8px] font-black uppercase text-gray-400 tracking-widest italic text-center border-l border-gray-100/50">
                                <span title="{{ $absen->title }}">{{ Str::limit($absen->title, 10) }}</span>
                            </th>
                        @endforeach
                        <th class="px-8 py-5 text-[9px] font-black uppercase text-[#800000] tracking-widest italic text-right bg-red-50/30 border-l border-red-100 w-32">Final Rate</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($registeredUsers as $user)
                        @php
                            $userAttendanceCount = 0;
                            $totalSlots = $allAbsensi->count();
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition-colors group">
                            <td class="px-8 py-4 sticky left-0 bg-white group-hover:bg-gray-50 z-10 border-r border-gray-50">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-gray-900 flex-shrink-0 flex items-center justify-center text-[10px] text-white font-black italic shadow-md">
                                        {{ substr($user->name, 0, 1) }}{{ substr($user->name, -1) }}
                                    </div>
                                    <div class="truncate">
                                        <p class="text-xs font-black text-gray-800 uppercase italic tracking-tight leading-none">{{ $user->name }}</p>
                                        <p class="text-[8px] font-bold text-gray-400 uppercase mt-1 tracking-tighter">{{ $user->nim ?? 'NO-ID' }}</p>
                                    </div>
                                </div>
                            </td>

                            @foreach($allAbsensi as $absen)
                                @php
                                    $present = $absen->kehadirans->where('user_id', $user->id)->first();
                                    if($present) $userAttendanceCount++;
                                @endphp
                                <td class="px-4 py-4 text-center border-l border-gray-50/50">
                                    @if($present)
                                        <div class="inline-flex items-center justify-center w-6 h-6 rounded-md bg-green-500 text-white shadow-sm ring-4 ring-green-50">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        </div>
                                    @else
                                        <span class="text-[10px] font-black text-gray-200">0</span>
                                    @endif
                                </td>
                            @endforeach

                            <td class="px-8 py-4 text-right bg-red-50/20 border-l border-red-100">
                                @php $percentage = $totalSlots > 0 ? round(($userAttendanceCount / $totalSlots) * 100) : 0; @endphp
                                <div class="flex flex-col items-end">
                                    <span class="text-sm font-black italic tracking-tighter {{ $percentage < 50 ? 'text-red-500' : 'text-gray-900' }}">{{ $percentage }}%</span>
                                    <span class="text-[7px] font-black text-gray-400 uppercase tracking-widest">{{ $userAttendanceCount }}/{{ $totalSlots }} Slts</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $allAbsensi->count() + 2 }}" class="px-8 py-20 text-center">
                                <p class="text-[10px] font-black text-gray-300 uppercase tracking-[0.5em] italic">No Participants Registered</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination Links --}}
        <div class="px-8 py-6 bg-gray-50/50 border-t border-gray-100">
            {{ $registeredUsers->links() }}
        </div>
    </div>

    {{-- Legend --}}
    <div class="mt-6 flex justify-between items-center px-4">
        <div class="flex gap-4">
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded-sm bg-green-500"></div>
                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Hadir</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded-sm bg-gray-200"></div>
                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Alfa</span>
            </div>
        </div>
        <div class="text-[9px] font-black text-gray-300 uppercase italic">
            Total Records: {{ $registeredUsers->total() }} Users
        </div>
    </div>
</div>
