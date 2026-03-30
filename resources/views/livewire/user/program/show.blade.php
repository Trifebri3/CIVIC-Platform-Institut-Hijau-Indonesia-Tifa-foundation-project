<?php

use App\Models\Program;
use App\Models\ProgramParticipant;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

new class extends Component {
    public $program;
    public $redeem_input;
    public $error_message;

    public function mount(Program $program)
    {
        $this->program = $program;
    }

    /**
     * Logic pendaftaran program
     */
public function enroll()
{
    $user = Auth::user();

    // 1. Validasi standar (Sudah terdaftar?)
    if ($user->isEnrolledIn($this->program->id)) {
        $this->error_message = "Kamu sudah terdaftar di program ini.";
        return;
    }

    // 2. Cek Waktu & Kuota
    if (!$this->program->isRegistrationOpen()) {
        $this->error_message = "Pendaftaran sudah ditutup atau kuota penuh.";
        return;
    }

    // --- TAMBAHAN PENGECEKAN REDEEM CODE ---
    if (!$this->program->is_open) {
        // Cek apakah input kosong
        if (empty($this->redeem_input)) {
            $this->error_message = "Silakan masukkan Kode Redeem terlebih dahulu!";
            return;
        }

        // Bandingkan input dengan kode di database (Case Insensitive)
        if (strtoupper($this->redeem_input) !== strtoupper($this->program->redeem_code)) {
            $this->error_message = "Kode Redeem yang kamu masukkan salah!";
            return;
        }
    }
    // ---------------------------------------

    try {
        // Retry 3x untuk menangani bentrok ID (Race Condition)
        DB::transaction(function () use ($user) {

            // Generate ID di dalam transaksi agar lebih akurat
            $registrationNumber = $this->generateIdNumber();

            // Eksekusi Enrolment
            $user->enrolledPrograms()->attach($this->program->id, [
                'registration_number' => $registrationNumber,
                'enrolment_method' => $this->program->is_open ? 'open_click' : 'redeem_code',
                'enrolled_at' => now(),
                'status' => 'active'
            ]);
        }, 3);

        session()->flash('success', 'Selamat! Kamu berhasil mendaftar.');
        return redirect()->route('user.my-programs');

    } catch (\Exception $e) {
        if (str_contains($e->getMessage(), '1062')) {
            $this->error_message = "Sistem sibuk (ID Clash). Klik daftar sekali lagi.";
        } else {
            $this->error_message = $e->getMessage();
        }
    }
}

    /**
     * Helper untuk generate ID REG-YYYY-000X
     */
private function generateIdNumber()
{
    // Ambil pendaftar terakhir untuk program ini
    $lastParticipant = ProgramParticipant::where('program_id', $this->program->id)
        ->latest('id')
        ->first();

    // Ambil urutan terakhir
    $nextNumber = $lastParticipant ? ((int) substr($lastParticipant->registration_number, -4) + 1) : 1;

    // FORMAT BARU: REG-TAHUN-[RANDOM]-URUTAN
    // Contoh: REG-2026-X9-0001
    // Random 2 karakter ini buat jaga-jaga kalau ada hit di mili-detik yang sama
    $randomSuffix = strtoupper(Str::random(2));

    return 'REG-' . date('Y') . '-' . $randomSuffix . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
}
}; ?>

<div class="max-w-4xl mx-auto py-10 px-4">
    {{-- Banner Section --}}
    <div class="relative h-64 md:h-96 rounded-[3rem] overflow-hidden shadow-2xl mb-10 border-4 border-white">
        @if($program->banner)
            <img src="{{ asset('storage/'.$program->banner) }}" class="w-full h-full object-cover">
        @else
            <div class="w-full h-full bg-gradient-to-br from-[#800000] to-black flex items-center justify-center">
                <span class="text-white/20 font-black text-6xl italic uppercase tracking-tighter">Program</span>
            </div>
        @endif

        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-transparent to-transparent flex flex-col justify-end p-10">
            <h1 class="text-4xl font-black text-white uppercase tracking-tighter italic leading-none">{{ $program->name }}</h1>
            <div class="flex items-center gap-2 mt-3">
                <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                <p class="text-red-400 text-[10px] font-black uppercase tracking-[0.2em]">Closed at: {{ $program->registration_end->format('d M Y, H:i') }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        {{-- Description Area --}}
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white rounded-[2.5rem] p-10 shadow-sm border border-gray-100">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-8 h-1 bg-[#800000] rounded-full"></div>
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em]">Program Overview</h3>
                </div>
                <div class="prose prose-red max-w-none text-gray-600 font-medium leading-relaxed italic">
                    {!! nl2br(e($program->description)) !!}
                </div>
            </div>
        </div>

        {{-- Registration Sidebar --}}
        <div class="relative">
            <div class="bg-white rounded-[2.5rem] p-8 shadow-2xl border border-gray-100 sticky top-10 overflow-hidden">
                {{-- Decorative Accent --}}
                <div class="absolute top-0 left-0 w-full h-2 bg-[#800000]"></div>

                <div class="text-center mb-10">
                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2">Available Quota</p>
                    <h2 class="text-5xl font-black text-gray-900 italic tracking-tighter">
                        {{ $program->quota > 0 ? ($program->quota - $program->participants()->count()) : '∞' }}
                    </h2>
                    <div class="mt-2 inline-block px-4 py-1 bg-gray-50 rounded-full">
                        <p class="text-[8px] font-black text-gray-400 uppercase italic tracking-widest">Enrolled: {{ $program->participants()->count() }}</p>
                    </div>
                </div>

                @if(Auth::user()->isEnrolledIn($program->id))
                    <div class="bg-green-50 border border-green-100 rounded-[2rem] p-6 text-center">
                        <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-3 shadow-lg shadow-green-200">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                        <p class="text-[10px] font-black text-green-700 uppercase tracking-widest">Akses Diterima</p>
                        <p class="text-[8px] font-bold text-green-600/60 mt-1 italic">Silakan cek dashboard belajar kamu.</p>
                    </div>
                @elseif(!$program->isRegistrationOpen())
                    <div class="bg-gray-50 rounded-[2rem] p-6 text-center border border-gray-100">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest italic">Pendaftaran Ditutup</p>
                    </div>
                @else
                    <form wire:submit="enroll" class="space-y-5">
                        @if(!$program->is_open)
                            <div class="space-y-2">
                                <label class="text-[9px] font-black text-[#800000] uppercase tracking-widest ml-4 block">Redeem Access Code</label>
                                <input type="text" wire:model="redeem_input"
                                       class="w-full rounded-2xl border-gray-100 bg-gray-50 text-center font-black uppercase tracking-[0.3em] focus:ring-[#800000] focus:border-[#800000] p-4 shadow-inner"
                                       placeholder="••••••">
                            </div>
                        @endif

                        @if($error_message)
                            <div class="p-3 bg-red-50 border border-red-100 rounded-xl">
                                <p class="text-[9px] font-bold text-red-600 uppercase text-center italic leading-tight">{{ $error_message }}</p>
                            </div>
                        @endif

                        <button type="submit"
                                wire:loading.attr="disabled"
                                wire:target="enroll"
                                class="w-full bg-black hover:bg-[#800000] text-white py-6 rounded-2xl font-black text-[10px] uppercase tracking-[0.3em] shadow-xl transition-all active:scale-95 disabled:opacity-50 group">
                            <span wire:loading.remove wire:target="enroll">Daftar Sekarang</span>
                            <span wire:loading wire:target="enroll" class="flex items-center justify-center gap-2">
                                <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Processing...
                            </span>
                        </button>
                    </form>
                @endif

                <div class="mt-8 pt-8 border-t border-gray-50 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-1.5 h-1.5 rounded-full bg-[#800000]"></div>
                        <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest italic">Automated ID Verification</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-1.5 h-1.5 rounded-full bg-black"></div>
                        <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest italic">Instant Course Access</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
