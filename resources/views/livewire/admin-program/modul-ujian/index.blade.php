<?php

use Livewire\Volt\Component;
use App\Models\ModulUjian;
use Illuminate\Support\Str;

new class extends Component {
    public $sub_program_id;

    // Properties Form
    public $modul_id = null; // Untuk penanda Edit
    public $judul, $instruksi, $deadline, $tipe_ujian = 'tugas';
    public $inputs = [];
    public $lampiran = [];


    public function mount($sub_program_id)
    {
        $this->sub_program_id = $sub_program_id;
        $this->resetForm();
    }

    // Ambil semua data ujian untuk table
    public function with()
    {
        return [
            'ujian_list' => ModulUjian::where('sub_program_id', $this->sub_program_id)
                            ->orderBy('created_at', 'desc')
                            ->get()
        ];
    }

    public function resetForm()
    {
        $this->modul_id = null;
        $this->judul = '';
        $this->instruksi = '';
        $this->deadline = now()->addDays(7)->format('Y-m-d\TH:i');
        $this->tipe_ujian = 'tugas';
        $this->lampiran = [];
        $this->inputs = [[
            'id' => Str::random(5),
            'tipe' => 'teks',
            'pertanyaan' => ''
        ]];
    }

    // --- LOGIC DYNAMIC INPUT ---
    public function addInput() { $this->inputs[] = ['id' => Str::random(5), 'tipe' => 'teks', 'pertanyaan' => '']; }
    public function removeInput($index) { unset($this->inputs[$index]); $this->inputs = array_values($this->inputs); }
    public function addLampiran() { $this->lampiran[] = ['nama' => '', 'url' => '']; }
    public function removeLampiran($index) { unset($this->lampiran[$index]); $this->lampiran = array_values($this->lampiran); }

    // --- CRUD ACTIONS ---
    public function save()
    {
        $data = $this->validate([
            'judul' => 'required|min:3',
            'deadline' => 'required',
            'inputs.*.pertanyaan' => 'required',
        ]);

        $payload = [
            'sub_program_id' => $this->sub_program_id,
            'judul' => $this->judul,
            'instruksi' => $this->instruksi,
            'lampiran_instruksi' => $this->lampiran,
            'konfigurasi_soal' => $this->inputs,
            'deadline' => $this->deadline,
            'tipe_ujian' => $this->tipe_ujian,
        ];

        if ($this->modul_id) {
            ModulUjian::find($this->modul_id)->update($payload);
            session()->flash('message', 'Ujian berhasil diperbarui!');
        } else {
            ModulUjian::create($payload);
            session()->flash('message', 'Ujian baru berhasil dibuat!');
        }

        $this->resetForm();
    }

    public function edit($id)
    {
        $modul = ModulUjian::findOrFail($id);
        $this->modul_id = $modul->id;
        $this->judul = $modul->judul;
        $this->instruksi = $modul->instruksi;
        $this->deadline = $modul->deadline ? $modul->deadline->format('Y-m-d\TH:i') : '';
        $this->tipe_ujian = $modul->tipe_ujian;
        $this->lampiran = $modul->lampiran_instruksi ?? [];
        $this->inputs = $modul->konfigurasi_soal ?? [];
    }

    public function delete($id)
    {
        ModulUjian::destroy($id);
        session()->flash('message', 'Ujian berhasil dihapus!');
    }
}; ?>

<div class="p-6 space-y-8 bg-gray-50 min-h-screen">
    @if (session()->has('message'))
        <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-100 border border-green-200 shadow-sm">
            {{ session('message') }}
        </div>
    @endif


    <div class="bg-white rounded-2xl shadow-sm border p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-gray-800">{{ $modul_id ? 'Edit Modul Ujian' : 'Buat Modul Ujian Baru' }}</h2>
            @if($modul_id)
                <button wire:click="resetForm" class="text-sm text-gray-500 hover:underline">Batal / Buat Baru</button>
            @endif
        </div>

        <form wire:submit.prevent="save" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="text-sm font-semibold">Judul / Nama Ujian</label>
                    <input type="text" wire:model="judul" class="w-full border-gray-300 rounded-xl focus:ring-blue-500 shadow-sm">
                </div>
                <div class="space-y-1">
                    <label class="text-sm font-semibold">Jenis</label>
                    <select wire:model="tipe_ujian" class="w-full border-gray-300 rounded-xl shadow-sm">
                        <option value="tugas">Tugas</option>
                        <option value="kuis">Kuis</option>
                        <option value="ujian_akhir">Ujian Akhir</option>
                    </select>
                </div>
            </div>

            <div class="space-y-1">
                <label class="text-sm font-semibold">Instruksi</label>
                <textarea wire:model="instruksi" rows="2" class="w-full border-gray-300 rounded-xl shadow-sm"></textarea>
            </div>

            <div class="p-4 bg-blue-50/50 rounded-2xl border border-blue-100">
                <button type="button" wire:click="addLampiran" class="text-xs font-bold text-blue-600 hover:text-blue-800 mb-2 uppercase">+ Link Lampiran</button>
                @foreach($lampiran as $idx => $lamp)
                    <div class="flex gap-2 mb-2">
                        <input type="text" wire:model="lampiran.{{ $idx }}.nama" placeholder="Nama File/Link" class="flex-1 text-sm border-gray-300 rounded-lg">
                        <input type="text" wire:model="lampiran.{{ $idx }}.url" placeholder="URL" class="flex-1 text-sm border-gray-300 rounded-lg">
                        <button type="button" wire:click="removeLampiran({{ $idx }})" class="text-red-500">&times;</button>
                    </div>
                @endforeach
            </div>

            <hr class="border-dashed">

            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <h3 class="font-bold text-gray-700">Konfigurasi Soal (JSON Builder)</h3>
                    <button type="button" wire:click="addInput" class="text-xs bg-indigo-600 text-white px-4 py-2 rounded-full shadow hover:bg-indigo-700 transition">
                        + Tambah Soal
                    </button>
                </div>

                @foreach($inputs as $index => $input)
                    <div class="group relative p-4 border border-gray-200 rounded-2xl bg-white hover:border-blue-400 transition shadow-sm">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                            <div class="md:col-span-8">
                                <label class="text-[10px] font-black text-gray-400 uppercase">Pertanyaan</label>
                                <input type="text" wire:model="inputs.{{ $index }}.pertanyaan" class="w-full mt-1 border-none bg-gray-50 rounded-lg focus:ring-0">
                            </div>
                            <div class="md:col-span-3">
                                <label class="text-[10px] font-black text-gray-400 uppercase">Tipe Jawaban</label>
                                <select wire:model="inputs.{{ $index }}.tipe" class="w-full mt-1 border-none bg-gray-50 rounded-lg focus:ring-0">
                                    <option value="teks">Teks Pendek</option>
                                    <option value="teks_panjang">Essay</option>
                                    <option value="file_upload">Upload File</option>
                                    <option value="link_submission">Link URL</option>
                                </select>
                            </div>
                            <div class="md:col-span-1 flex items-end justify-center">
                                <button type="button" wire:click="removeInput({{ $index }})" class="mb-2 text-red-400 hover:text-red-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="flex flex-col md:flex-row gap-4 items-end pt-4">
                <div class="flex-1 w-full">
                    <label class="text-sm font-semibold text-red-500">Deadline</label>
                    <input type="datetime-local" wire:model="deadline" class="w-full mt-1 border-gray-300 rounded-xl shadow-sm">
                </div>
                <button type="submit" class="w-full md:w-auto px-10 bg-black text-white py-3 rounded-xl font-bold hover:bg-gray-800 transition shadow-lg">
                    {{ $modul_id ? 'UPDATE MODUL' : 'PUBLISH MODUL' }}
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-black/5 border border-gray-100 overflow-hidden antialiased">
    {{-- Header Tabel --}}
    <div class="p-8 bg-gray-50/50 border-b border-gray-100 flex justify-between items-center">
        <div>
            <h3 class="text-xl font-black text-gray-900 uppercase italic tracking-tighter">
                DAFTAR<span class="text-[#800000]">MODUL UJIAN</span>
            </h3>
            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-1 italic">Manajemen evaluasi & penilaian materi</p>
        </div>
        <div class="px-4 py-2 bg-white rounded-2xl border border-gray-100 shadow-sm">
            <span class="text-[10px] font-black text-[#800000] uppercase italic">{{ count($ujian_list) }} Modul Aktif</span>
        </div>
    </div>

    {{-- Body Tabel --}}
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-white border-b border-gray-50">
                    <th class="px-8 py-5 text-[9px] font-black text-gray-400 uppercase italic tracking-[0.2em]">Info Modul</th>
                    <th class="px-6 py-5 text-[9px] font-black text-gray-400 uppercase italic tracking-[0.2em]">Tipe & Soal</th>
                    <th class="px-6 py-5 text-[9px] font-black text-gray-400 uppercase italic tracking-[0.2em]">Deadline</th>
                    <th class="px-8 py-5 text-[9px] font-black text-gray-400 uppercase italic tracking-[0.2em] text-right">Master Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($ujian_list as $item)
                    @php
                        // Logic cek jawaban yang belum dinilai
                        $hasPending = $item->jawaban()->whereNull('nilai')->exists();
                    @endphp
                    <tr class="group hover:bg-gray-50/50 transition-all">
                        {{-- Nama Modul --}}
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center border border-gray-100 group-hover:bg-black group-hover:text-white transition-all duration-300 shadow-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                                </div>
                                <div>
                                    <p class="text-sm font-black text-gray-900 uppercase italic tracking-tight group-hover:text-[#800000] transition-colors leading-none mb-1">{{ $item->judul }}</p>
                                    <span class="text-[8px] font-bold text-gray-300 uppercase tracking-widest">UID: #EXAM-{{ $item->id }}</span>
                                </div>
                            </div>
                        </td>

                        {{-- Tipe & Konfigurasi --}}
                        <td class="px-6 py-6">
                            <div class="flex flex-col gap-1">
                                <span class="w-fit px-3 py-1 bg-gray-100 rounded-lg text-[8px] font-black uppercase tracking-widest text-gray-500 italic">{{ $item->tipe_ujian }}</span>
                                <span class="text-[10px] font-bold text-gray-400 italic">{{ count($item->konfigurasi_soal ?? []) }} Questions Loaded</span>
                            </div>
                        </td>

                        {{-- Deadline --}}
                        <td class="px-6 py-6 text-xs italic font-bold text-gray-600">
                            @if($item->deadline)
                                <span class="block text-[10px] text-gray-900 font-black uppercase leading-none mb-1">{{ $item->deadline->format('d M Y') }}</span>
                                <span class="text-[9px] text-gray-400">{{ $item->deadline->format('H:i') }} WIB</span>
                            @else
                                <span class="text-gray-300">No Deadline</span>
                            @endif
                        </td>

                        {{-- Action Buttons --}}
                        <td class="px-8 py-6 text-right">
                            <div class="flex items-center justify-end gap-2 opacity-40 group-hover:opacity-100 transition-opacity duration-300">

        <a href="{{ route('admin.modul-ujian.kelola-pg', $item->id) }}"
           wire:navigate
           title="Kelola Soal Pilihan Ganda"
           class="p-2.5 text-blue-600 hover:text-white hover:bg-blue-600 rounded-xl transition-all border-2 border-blue-50 group/pg shadow-sm bg-blue-50/30 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0114 0z" />
            </svg>
            <span class="text-[9px] font-black uppercase tracking-widest hidden group-hover:block transition-all">PG</span>
        </a>





                                {{-- Tombol Grading (Review/Penilaian) --}}
                                <a href="{{ route('admin.modul-ujian.grading', $item->id) }}"
                                   wire:navigate
                                   title="{{ $hasPending ? 'Ada jawaban perlu dinilai' : 'Review Nilai' }}"
                                   class="relative p-2.5 rounded-xl border-2 transition-all flex items-center gap-2
                                   {{ $hasPending
                                      ? 'text-white bg-[#800000] border-[#800000] shadow-lg shadow-[#800000]/20'
                                      : 'text-gray-400 bg-white border-gray-100 hover:border-[#800000] hover:text-[#800000]' }}">

                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                    </svg>

                                    @if($hasPending)
                                        <span class="flex h-2 w-2 relative">
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-2 w-2 bg-white"></span>
                                        </span>
                                    @endif

                                    <span class="text-[9px] font-black uppercase tracking-[0.15em] {{ $hasPending ? 'block' : 'hidden group-hover:block' }}">
                                        {{ $hasPending ? 'Grade' : 'Review' }}
                                    </span>
                                </a>

                                {{-- Tombol Edit --}}
                                <button wire:click="edit({{ $item->id }})"
                                        class="p-2.5 bg-white border-2 border-gray-100 text-gray-400 rounded-xl hover:border-black hover:text-black transition-all shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                </button>

                                {{-- Tombol Delete --}}
                                <button wire:click="delete({{ $item->id }})"
                                        wire:confirm="Yakin ingin menghapus modul ini?"
                                        class="p-2.5 bg-red-50 text-red-500 border-2 border-red-50 rounded-xl hover:bg-red-500 hover:text-white hover:border-red-500 transition-all shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>

                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
</div>
