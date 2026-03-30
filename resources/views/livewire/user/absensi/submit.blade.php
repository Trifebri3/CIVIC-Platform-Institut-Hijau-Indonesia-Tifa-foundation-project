<?php

use App\Models\{Absensi, KehadiranUser};
use Livewire\Volt\Component;
use Carbon\Carbon;

new class extends Component {
    public Absensi $absensi;
    public $input_code;
    public $has_attended = false;

    public function mount(Absensi $absensi)
    {
        $this->absensi = $absensi;

        // Cek apakah user ini sudah absen sebelumnya
        $this->has_attended = KehadiranUser::where('absensi_id', $this->absensi->id)
            ->where('user_id', auth()->id())
            ->exists();
    }

    public function submitAbsen()
    {
        // 1. Validasi Waktu (Double Check)
        if (!$this->absensi->is_open) {
            session()->flash('error', 'Waktu absensi sudah tertutup atau belum dibuka.');
            return;
        }

        // 2. Validasi Kode Keamanan (Jika diaktifkan admin)
        if ($this->absensi->is_protected) {
            if (strtoupper($this->input_code) !== strtoupper($this->absensi->auth_code)) {
                $this->addError('input_code', 'Kode absensi salah atau tidak valid!');
                return;
            }
        }

        // 3. Simpan Kehadiran
        KehadiranUser::create([
            'absensi_id' => $this->absensi->id,
            'user_id' => auth()->id(),
            'submitted_at' => now(),
            'score' => 1, // Nilai default kehadiran
            'status' => 'present'
        ]);

        $this->has_attended = true;
        session()->flash('success', 'Berhasil! Kehadiran Anda telah dicatat.');
    }
}; ?>
<div class="max-w-md mx-auto py-6 px-4 antialiased">
    {{-- COMPACT ATTENDANCE CARD --}}
    <div class="bg-white rounded-[2rem] shadow-xl shadow-gray-100/50 border border-gray-100 overflow-hidden relative transition-all duration-500 hover:shadow-2xl hover:shadow-gray-200/40">

        {{-- Slim Top Bar Status --}}
        <div class="flex items-center justify-between px-6 py-3 border-b border-gray-50 bg-[#fafafa]">
            <div class="flex items-center gap-2">
                <div class="w-1.5 h-1.5 rounded-full {{ $has_attended ? 'bg-green-500' : ($absensi->is_open ? 'bg-blue-500 animate-pulse' : 'bg-red-500') }}"></div>
                <span class="text-[8px] font-black uppercase tracking-[0.2em] {{ $has_attended ? 'text-green-600' : ($absensi->is_open ? 'text-blue-600' : 'text-red-600') }}">
                    {{ $has_attended ? 'Completed' : ($absensi->is_open ? 'Available Now' : 'Session Closed') }}
                </span>
            </div>
            <span class="text-[8px] font-black text-gray-300 uppercase italic tracking-widest">ID: #{{ $absensi->id }}</span>
        </div>

        <div class="p-6 sm:p-8">
            {{-- Header Section: Icon & Title in Flex --}}
            <div class="flex items-center gap-5 mb-6">
                <div class="flex-shrink-0 w-14 h-14 {{ $has_attended ? 'bg-green-50' : 'bg-gray-50' }} rounded-2xl flex items-center justify-center transition-colors">
                    @if($has_attended)
                        <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    @else
                        <svg class="w-6 h-6 {{ $absensi->is_open ? 'text-[#800000]' : 'text-gray-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"/></svg>
                    @endif
                </div>
                <div class="min-w-0 flex-1">
                    <h2 class="text-xl font-black text-gray-900 uppercase italic tracking-tighter leading-tight truncate">
                        {{ $absensi->title }}
                    </h2>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="text-[9px] text-gray-400 font-bold uppercase tracking-widest italic">{{ $absensi->duration_minutes }} Mins Duration</span>
                        @if($absensi->is_protected)
                            <span class="w-1 h-1 bg-gray-200 rounded-full"></span>
                            <span class="text-[9px] text-[#800000] font-black uppercase italic tracking-widest">Protected</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Alert Section --}}
            @if(session()->has('success'))
                <div class="bg-green-50 text-green-700 p-4 rounded-xl text-[9px] font-black uppercase mb-6 italic border border-green-100/50">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Logic Content --}}
            @if(!$has_attended && $absensi->is_open)
                <form wire:submit.prevent="submitAbsen" class="space-y-4">
                    @if($absensi->is_protected)
                    <div class="relative group">
                        <input type="text" wire:model="input_code" placeholder="ENTER PASSCODE"
                               class="w-full bg-gray-50 border-2 border-transparent rounded-xl py-4 px-6 text-center font-black text-lg tracking-[0.4em] focus:ring-0 focus:border-black transition-all uppercase placeholder:text-gray-300 placeholder:tracking-widest">
                        @error('input_code') <span class="block mt-2 text-[8px] font-bold text-red-500 uppercase italic tracking-wider text-center">{{ $message }}</span> @enderror
                    </div>
                    @endif

                    <button type="submit" class="w-full bg-black text-white py-4 rounded-xl font-black text-[10px] uppercase tracking-[0.3em] italic shadow-lg shadow-black/10 hover:bg-[#800000] transition-all active:scale-[0.98]">
                        Confirm Attendance
                    </button>
                </form>
            @elseif($has_attended)
                <div class="py-5 px-6 bg-green-50/50 rounded-2xl border-2 border-dashed border-green-100 flex items-center justify-center gap-3">
                    <div class="w-5 h-5 rounded-full bg-green-500 flex items-center justify-center">
                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <p class="text-[10px] font-black text-green-700 uppercase tracking-widest italic">Presence Recorded</p>
                </div>
            @else
                <div class="py-5 px-6 bg-red-50/50 rounded-2xl border-2 border-dashed border-red-100 text-center">
                    <p class="text-[9px] font-black text-red-400 uppercase tracking-[0.2em] italic">Attendance session is currently closed</p>
                </div>
            @endif
        </div>

        {{-- Visual Accent --}}
        <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-transparent via-gray-100 to-transparent"></div>
    </div>

    {{-- SLIM FOOTER INFO --}}
    <div class="mt-6 flex justify-between items-center px-4">
        <div class="flex items-center gap-3">
            <div class="w-6 h-6 rounded-lg bg-gray-900 flex items-center justify-center text-[8px] font-black text-white italic shadow-sm">
                {{ substr(auth()->user()->name, 0, 1) }}
            </div>
            <span class="text-[8px] font-black text-gray-400 uppercase tracking-[0.2em] italic">{{ auth()->user()->name }}</span>
        </div>
        <div class="flex flex-col items-end">
            <span class="text-[7px] font-black text-gray-200 uppercase tracking-widest">v1.0.4-HYBRID</span>
        </div>
    </div>
</div>
