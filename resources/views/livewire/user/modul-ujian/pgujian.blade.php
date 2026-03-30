<?php

use Livewire\Volt\Component;
use App\Models\ModulUjian;
use App\Models\PgModul;
use App\Models\JawabanUser; // Asumsi nama tabel jawaban

new class extends Component {
    public $modul;
    public $soals;
    public $jawaban = []; // Menyimpan pilihan user [soal_id => 'teks_jawaban']
    public $sudah_selesai = false;
    public $skor = 0;

    public function mount($id) {
        $this->modul = ModulUjian::with('pg_soals')->findOrFail($id);
        $this->soals = $this->modul->pg_soals; // Ambil relasi soal PG
    }

    public function submitUjian() {
        $totalPoin = 0;
        $skorUser = 0;

        foreach ($this->soals as $soal) {
            $totalPoin += $soal->poin;
            $jawabanUser = $this->jawaban[$soal->id] ?? null;

            if ($jawabanUser === $soal->kunci_jawaban) {
                $skorUser += $soal->poin;
            }
        }

        // Kalkulasi nilai akhir (skala 100)
        $this->skor = ($skorUser / $totalPoin) * 100;

        // Simpan ke Database
        JawabanUser::create([
            'user_id' => auth()->id(),
            'modul_ujian_id' => $this->modul->id,
            'list_jawaban' => $this->jawaban, // JSON
            'nilai' => $this->skor,
            'status' => 'completed'
        ]);

        $this->sudah_selesai = true;
    }
}; ?>

<div class="max-w-3xl mx-auto py-12 px-4">
    @if(!$sudah_selesai)
        {{-- Header Ujian --}}
        <div class="mb-10 text-center">
            <h1 class="text-3xl font-black uppercase italic tracking-tighter">{{ $modul->judul }}</h1>
            <p class="text-xs font-bold text-gray-400 mt-2 uppercase tracking-[0.2em]">Pilihlah jawaban yang paling tepat</p>
        </div>

        {{-- List Soal --}}
        <div class="space-y-8">
            @foreach($soals as $index => $soal)
            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
                <div class="flex gap-4 mb-6">
                    <span class="flex-none w-8 h-8 bg-black text-white flex items-center justify-center rounded-lg font-black italic text-xs">
                        {{ $index + 1 }}
                    </span>
                    <p class="text-sm font-bold text-gray-800 leading-relaxed uppercase italic">
                        {{ $soal->pertanyaan }}
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-3 ml-12">
                    @foreach($soal->opsi as $idx => $opsi)
                    <label class="relative flex items-center p-4 rounded-2xl border-2 cursor-pointer transition-all
                        {{ ($jawaban[$soal->id] ?? '') == $opsi ? 'border-[#800000] bg-red-50/30' : 'border-gray-50 hover:border-gray-200' }}">

                        <input type="radio"
                               wire:model="jawaban.{{ $soal->id }}"
                               value="{{ $opsi }}"
                               class="hidden">

                        <span class="w-6 h-6 rounded-full border-2 flex items-center justify-center mr-4
                            {{ ($jawaban[$soal->id] ?? '') == $opsi ? 'border-[#800000] bg-[#800000]' : 'border-gray-200' }}">
                            @if(($jawaban[$soal->id] ?? '') == $opsi)
                                <div class="w-2 h-2 bg-white rounded-full"></div>
                            @endif
                        </span>

                        <span class="text-xs font-black uppercase italic tracking-wide {{ ($jawaban[$soal->id] ?? '') == $opsi ? 'text-[#800000]' : 'text-gray-500' }}">
                            {{ chr(65+$idx) }}. {{ $opsi }}
                        </span>
                    </label>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>

        {{-- Action --}}
        <div class="mt-12 flex justify-center">
            <button wire:click="submitUjian"
                    wire:confirm="Yakin ingin mengumpulkan jawaban?"
                    class="px-12 py-4 bg-black text-white rounded-2xl font-black uppercase italic tracking-[0.2em] shadow-2xl hover:scale-105 transition-transform">
                Finish & Submit
            </button>
        </div>

    @else
        {{-- Result View (Success State) --}}
        <div class="bg-white p-12 rounded-[3rem] shadow-2xl border border-gray-100 text-center animate-bounce-short">
            <div class="w-20 h-20 bg-green-50 text-green-500 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
            </div>
            <h2 class="text-2xl font-black uppercase italic tracking-tighter">Ujian Selesai!</h2>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-2">Terima kasih telah mengerjakan dengan jujur.</p>

            <div class="mt-8 p-6 bg-gray-50 rounded-2xl border border-gray-100">
                <p class="text-[9px] font-black text-gray-400 uppercase italic">Skor Anda</p>
                <h3 class="text-6xl font-black text-[#800000] italic leading-none mt-2">{{ round($skor) }}</h3>
            </div>

            <a href="{{ route('user.dashboard') }}" wire:navigate class="inline-block mt-10 text-[10px] font-black uppercase italic tracking-widest text-gray-400 hover:text-black">
                Kembali ke Dashboard →
            </a>
        </div>
    @endif
</div>
