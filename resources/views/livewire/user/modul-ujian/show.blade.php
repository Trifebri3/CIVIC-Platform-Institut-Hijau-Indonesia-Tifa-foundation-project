<?php

use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Models\ModulUjian;
use App\Models\JawabanUjian;
use Illuminate\Support\Facades\Storage;

new class extends Component {
    use WithFileUploads;

    public $modul;
    public $jawaban = [];
    public $files = [];
    public $sudahSubmit = false;
    public $dataJawaban = null; // Untuk nampung record dari DB (termasuk nilai)


public $soalsPg = [];
    public $jawabanPg = [];
    public $sudahSelesaiPg = false;
    public $skorPg = 0;



    public function mount($id)
    {
        $this->modul = ModulUjian::findOrFail($id);
        $this->loadUserJawaban();

        $this->loadSoalPg($id);
    }

    public function loadUserJawaban()
    {
        $this->dataJawaban = JawabanUjian::where('modul_ujian_id', $this->modul->id)
                            ->where('user_id', auth()->id())
                            ->first();

        if ($this->dataJawaban) {
            $this->sudahSubmit = true;
            $this->jawaban = $this->dataJawaban->konten_jawaban;
        } else {
            foreach ($this->modul->konfigurasi_soal as $soal) {
                $this->jawaban[$soal['id']] = '';
            }
        }
    }

    public function submitJawaban()
    {
        // Validasi Deadline & Status
        if ($this->modul->isExpired() || $this->sudahSubmit) {
            session()->flash('error', 'Akses ditutup atau anda sudah submit.');
            return;
        }

        $dataSimpan = $this->jawaban;

        // Proses Upload File
        foreach ($this->files as $soalId => $file) {
            $path = $file->store('jawaban-ujian', 'public');
            $dataSimpan[$soalId] = $path;
        }

        JawabanUjian::updateOrCreate(
            ['modul_ujian_id' => $this->modul->id, 'user_id' => auth()->id()],
            ['konten_jawaban' => $dataSimpan, 'submitted_at' => now()]
        );

        $this->loadUserJawaban();
        session()->flash('success', 'Jawaban dikirim!');
    }

    // Tambahkan panggil fungsi ini di dalam mount($id) aslimu
    // Caranya: cukup tambahkan $this->loadSoalPg($id); di baris terakhir mount.
public function loadSoalPg($id) {
        // Inisialisasi sebagai array kosong dulu supaya Livewire bisa nampung input
        $this->jawabanPg = [];

        $this->soalsPg = \App\Models\PgModul::where('modul_ujian_id', $id)->get();

        $cek = \App\Models\PgJawaban::where('modul_ujian_id', $id)
                ->where('user_id', auth()->id())
                ->first();

        if ($cek) {
            $this->sudahSelesaiPg = true;
            $this->skorPg = $cek->skor_akhir;
            $this->jawabanPg = $cek->list_jawaban;
        } else {
            // Isi array jawabanPg dengan ID soal agar siap diisi (Wire:model)
            foreach ($this->soalsPg as $s) {
                $this->jawabanPg[$s->id] = '';
            }
        }
    }

    public function submitUjianPg() {
        if ($this->sudahSelesaiPg) return;

        // Gunakan fungsi sakti dari Model PgJawaban yang kamu buat
        $hasil = \App\Models\PgJawaban::hitungDanSimpan(
            auth()->id(),
            $this->modul->id,
            $this->jawabanPg
        );

        $this->skorPg = $hasil->skor_akhir;
        $this->sudahSelesaiPg = true;
        session()->flash('success', 'Ujian PG berhasil dikirim!');
    }





}; ?>

<div class="max-w-2xl mx-auto p-4 sm:p-6 antialiased font-sans bg-[#fafafa] min-h-screen">
    {{-- Mini Header --}}
    <div class="mb-6 bg-black p-6 rounded-[2rem] text-white shadow-xl relative overflow-hidden">
        <div class="flex justify-between items-start">
            <div class="flex-1">
                <span class="px-2 py-0.5 bg-white/10 rounded text-[9px] font-black uppercase tracking-widest text-gray-300">
                    {{ $modul->tipe_ujian }}
                </span>
                <h1 class="text-xl font-black mt-1 leading-tight uppercase italic tracking-tighter">{{ $modul->judul }}</h1>
            </div>

            {{-- Status & Nilai Badge --}}
            <div class="text-right">
                @if($dataJawaban && $dataJawaban->nilai)
                    <div class="bg-[#800000] p-3 rounded-2xl border border-white/20 text-center min-w-[60px]">
                        <p class="text-[8px] font-black uppercase text-white/60">Score</p>
                        <p class="text-2xl font-black leading-none">{{ $dataJawaban->nilai }}</p>
                    </div>
                @elseif($sudahSubmit)
                    <span class="px-3 py-1 bg-green-500/20 text-green-400 rounded-full text-[9px] font-black uppercase tracking-widest italic border border-green-500/30">
                        Submitted
                    </span>
                @elseif($modul->isExpired())
                    <span class="px-3 py-1 bg-red-500/20 text-red-400 rounded-full text-[9px] font-black uppercase tracking-widest italic border border-red-500/30">
                        Closed
                    </span>
                @endif
            </div>
        </div>

        <div class="mt-4 flex items-center gap-4 text-[9px] font-bold text-gray-400 uppercase italic tracking-widest border-t border-white/5 pt-4">
            <div class="flex items-center gap-1.5">
                <svg class="w-3 h-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0114 0z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Ends: {{ $modul->deadline?->format('d M, H:i') }}
            </div>
        </div>
    </div>


    {{-- Feedback Admin (Muncul jika sudah dinilai) --}}
    @if($dataJawaban && $dataJawaban->feedback_admin)
    <div class="mb-6 p-4 bg-blue-50 border border-blue-100 rounded-2xl flex gap-3 items-start">
        <div class="p-2 bg-blue-600 rounded-xl text-white">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
        </div>
        <div>
            <p class="text-[9px] font-black uppercase text-blue-400 tracking-widest">Admin Feedback</p>
            <p class="text-xs font-bold text-blue-900 mt-1 leading-relaxed">{{ $dataJawaban->feedback_admin }}</p>
        </div>
    </div>
    @endif

    {{-- Lampiran Kecil --}}
    @if($modul->lampiran_instruksi)
    <div class="mb-6 flex flex-wrap gap-2">
        @foreach($modul->lampiran_instruksi as $lamp)
            <a href="{{ $lamp['url'] }}" target="_blank" class="px-3 py-2 bg-white border border-gray-100 rounded-xl flex items-center gap-2 hover:border-black transition-all">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <span class="text-[9px] font-black uppercase tracking-tight text-gray-600">{{ $lamp['nama'] }}</span>
            </a>
        @endforeach
    </div>
    @endif

{{-- BLOK UI TAMBAHAN PILIHAN GANDA --}}
    @if(count($soalsPg) > 0)
    <div class="mb-10 space-y-6">
        <div class="border-l-4 border-[#800000] pl-4 mb-4">
            <h2 class="text-xl font-black uppercase italic tracking-tighter text-gray-800">Bagian I: Pilihan Ganda</h2>
            <p class="text-[10px] font-bold text-gray-400 uppercase">Pilih satu jawaban yang paling benar</p>
        </div>

        @if(!$sudahSelesaiPg)
            @foreach($soalsPg as $index => $s)
            {{-- Tambahkan wire:key agar Livewire tidak bingung saat render ulang --}}
            <div wire:key="soal-pg-{{ $s->id }}" class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm">
                <div class="flex gap-3 mb-4">
                    <span class="flex-none w-6 h-6 bg-black text-white flex items-center justify-center rounded-lg font-black italic text-[10px]">{{ $index + 1 }}</span>
                    <p class="text-sm font-bold text-gray-800 leading-relaxed uppercase italic">{{ $s->pertanyaan }}</p>
                </div>

                <div class="grid grid-cols-1 gap-2 ml-9">
                    @foreach($s->opsi as $idx => $o)
                    {{-- ID unik untuk label dan input --}}
                    @php $optionId = "soal-{$s->id}-opsi-{$idx}"; @endphp

                    <label for="{{ $optionId }}"
                           class="relative flex items-center p-3 rounded-xl border-2 cursor-pointer transition-all
                           {{ ($jawabanPg[$s->id] ?? '') == $o ? 'border-[#800000] bg-red-50/30' : 'border-gray-50 hover:border-gray-200' }}">

                        {{-- Tambahkan wire:model.live agar perubahan langsung terasa --}}
                        <input type="radio"
                               id="{{ $optionId }}"
                               wire:model.live="jawabanPg.{{ $s->id }}"
                               name="jawaban_pg_{{ $s->id }}"
                               value="{{ $o }}"
                               class="hidden">

                        <span class="w-4 h-4 rounded-full border-2 flex items-center justify-center mr-3
                                    {{ ($jawabanPg[$s->id] ?? '') == $o ? 'border-[#800000] bg-[#800000]' : 'border-gray-200' }}">
                            @if(($jawabanPg[$s->id] ?? '') == $o)
                                <div class="w-1.5 h-1.5 bg-white rounded-full"></div>
                            @endif
                        </span>

                        <span class="text-[11px] font-black uppercase italic tracking-wide {{ ($jawabanPg[$s->id] ?? '') == $o ? 'text-[#800000]' : 'text-gray-600' }}">
                            {{ chr(65+$idx) }}. {{ $o }}
                        </span>
                    </label>
                    @endforeach
                </div>
            </div>
            @endforeach

            <div class="p-4 bg-gray-100 rounded-2xl border border-dashed border-gray-300">
                 <button type="button"
                        wire:click="submitUjianPg"
                        wire:confirm="Kumpulkan jawaban Pilihan Ganda?"
                        class="w-full py-4 bg-black text-white rounded-2xl font-black uppercase italic tracking-widest shadow-xl active:scale-95 transition-all">
                    Submit Jawaban PG
                </button>
            </div>
        @else
            {{-- View Result (Sudah Selesai) --}}
            <div class="bg-white p-8 rounded-[3rem] border border-green-100 text-center shadow-inner">
                <div class="inline-flex items-center gap-2 px-4 py-1 bg-green-500 text-white rounded-full text-[9px] font-black uppercase italic mb-4">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    PG Completed
                </div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Skor Pilihan Ganda</p>
                <h3 class="text-5xl font-black text-[#800000] italic mt-1">{{ round($skorPg) }}</h3>
            </div>
        @endif

        <div class="relative py-8">
            <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-dashed border-gray-200"></div></div>
            <div class="relative flex justify-center text-[9px] font-black uppercase text-gray-300 bg-[#fafafa] px-4 italic">Bagian II: Essay & Lampiran</div>
        </div>
    </div>
    @endif





    {{-- Submission Form --}}
    <form wire:submit.prevent="submitJawaban" class="space-y-4">
        @foreach($modul->konfigurasi_soal as $soal)
            <div class="bg-white p-5 rounded-[1.5rem] border border-gray-100 shadow-sm overflow-hidden">
                <div class="flex items-start justify-between mb-2">
                    <h3 class="text-sm font-black text-gray-900 leading-tight uppercase italic">{{ $soal['pertanyaan'] }}</h3>
                    <span class="text-[8px] font-black text-gray-300">#{{ $loop->iteration }}</span>
                </div>

                {{-- Input Area --}}
                <div class="mt-3">
                    @if($soal['tipe'] == 'teks')
                        <input type="text" wire:model="jawaban.{{ $soal['id'] }}"
                               class="w-full bg-gray-50 border-none rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-black/5"
                               {{ $sudahSubmit || $modul->isExpired() ? 'disabled' : '' }}>

                    @elseif($soal['tipe'] == 'teks_panjang')
                        <textarea wire:model="jawaban.{{ $soal['id'] }}" rows="3"
                                  class="w-full bg-gray-50 border-none rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-black/5"
                                  {{ $sudahSubmit || $modul->isExpired() ? 'disabled' : '' }}></textarea>

                    @elseif($soal['tipe'] == 'link_submission')
                        <div class="flex items-center bg-gray-50 rounded-xl px-3">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            <input type="url" wire:model="jawaban.{{ $soal['id'] }}" placeholder="https://..."
                                   class="w-full bg-transparent border-none rounded-xl py-2.5 text-sm font-medium focus:ring-0"
                                   {{ $sudahSubmit || $modul->isExpired() ? 'disabled' : '' }}>
                        </div>

                    @elseif($soal['tipe'] == 'file_upload')
                        @if($sudahSubmit)
                            <a href="{{ asset('storage/'.$jawaban[$soal['id']]) }}" target="_blank" class="flex items-center justify-between p-3 bg-gray-50 border border-dashed border-gray-200 rounded-xl group hover:bg-black transition-all">
                                <span class="text-[10px] font-bold text-gray-500 group-hover:text-white italic">View Submitted File</span>
                                <svg class="w-4 h-4 text-[#800000] group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" stroke-width="2.5"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" stroke-width="2.5"/></svg>
                            </a>
                        @else
                            <input type="file" wire:model="files.{{ $soal['id'] }}"
                                   class="block w-full text-[10px] text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:bg-black file:text-white hover:file:bg-gray-800 transition-all">
                        @endif
                    @endif
                </div>
            </div>
        @endforeach

        {{-- Action Buttons --}}
        <div class="pt-4">
            @if(!$sudahSubmit && !$modul->isExpired())
                <button type="submit" class="w-full bg-[#800000] text-white py-4 rounded-2xl font-black uppercase tracking-[0.2em] italic shadow-lg shadow-red-900/20 active:scale-95 transition-all">
                    Final Submission
                </button>
            @else
                <a href="{{ url()->previous() }}" wire:navigate class="block w-full text-center bg-gray-100 text-gray-500 py-4 rounded-2xl font-black uppercase tracking-[0.2em] italic border border-gray-200">
                    Back to Dashboard
                </a>
            @endif
        </div>
    </form>
</div>
