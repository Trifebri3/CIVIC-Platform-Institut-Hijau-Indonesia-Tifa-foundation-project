<?php

use Livewire\Volt\Component;
use App\Models\ModulUjian;
use App\Models\PgModul;
use Illuminate\Support\Str;

new class extends Component {
    public $modul;
    public $soals;

    // State Form
    public $isEdit = false;
    public $selectedId;
    public $pertanyaan;
    public $kunci_jawaban;
    public $poin = 10;

    // Inisialisasi Opsi sebagai array dengan key eksplisit agar sinkron
    public $opsi = [
        0 => '',
        1 => '',
        2 => '',
        3 => ''
    ];

    public function mount($id)
    {
        $this->modul = ModulUjian::findOrFail($id);
        $this->loadSoal();
    }

    public function loadSoal()
    {
        // Fresh() digunakan agar data yang diambil benar-benar terbaru dari DB
        $this->soals = PgModul::where('modul_ujian_id', $this->modul->id)->latest()->get();
    }

    public function save()
    {
        $this->validate([
            'pertanyaan' => 'required|min:5',
            'opsi.0' => 'required',
            'opsi.1' => 'required',
            'opsi.2' => 'required',
            'opsi.3' => 'required',
            'kunci_jawaban' => 'required',
            'poin' => 'required|numeric',
        ], [
            'opsi.*.required' => 'Semua opsi harus diisi!',
            'kunci_jawaban.required' => 'Pilih salah satu opsi sebagai kunci jawaban.'
        ]);

        PgModul::updateOrCreate(
            ['id' => $this->selectedId],
            [
                'modul_ujian_id' => $this->modul->id,
                'pertanyaan' => $this->pertanyaan,
                'opsi' => $this->opsi,
                'kunci_jawaban' => $this->kunci_jawaban,
                'poin' => $this->poin,
            ]
        );

        $this->resetForm();
        $this->loadSoal();

        // Trigger notifikasi sukses (Opsional: jika Bos pakai library toast)
        $this->dispatch('swal:success', title: 'Berhasil!', text: 'Soal berhasil diperbarui.');
    }

    public function edit($id)
    {
        $soal = PgModul::findOrFail($id);
        $this->selectedId = $soal->id;
        $this->pertanyaan = $soal->pertanyaan;

        // Pastikan array opsi terisi dengan benar (mapping ulang)
        foreach($soal->opsi as $key => $val) {
            $this->opsi[$key] = $val;
        }

        $this->kunci_jawaban = $soal->kunci_jawaban;
        $this->poin = $soal->poin;
        $this->isEdit = true;
    }

    public function delete($id)
    {
        PgModul::destroy($id);
        $this->loadSoal();
    }

    public function resetForm()
    {
        $this->reset(['pertanyaan', 'kunci_jawaban', 'poin', 'selectedId', 'isEdit']);
        $this->opsi = [0 => '', 1 => '', 2 => '', 3 => ''];
    }
}; ?>

<div class="p-6 bg-[#fafafa] min-h-screen antialiased font-sans">
    {{-- Header --}}
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-black uppercase italic tracking-tighter">
                Manage <span class="text-[#800000]">Multiple Choice</span>
            </h1>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ $modul->judul }}</p>
        </div>
        <a href="{{ url()->previous() }}" wire:navigate class="px-4 py-2 bg-black text-white rounded-xl text-[10px] font-black uppercase italic shadow-lg shadow-black/20">Back</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Form Create/Edit --}}
        <div class="lg:col-span-1">
            <div class="bg-white p-6 rounded-[2rem] shadow-xl border border-gray-100 sticky top-6">
                <h2 class="text-sm font-black uppercase italic mb-6 text-[#800000]">
                    {{ $isEdit ? 'Update Question' : 'Create New Question' }}
                </h2>

                <div class="space-y-4">
                    <div>
                        <label class="text-[9px] font-black uppercase text-gray-400 italic">Pertanyaan</label>
                        <textarea wire:model="pertanyaan" rows="3" class="w-full mt-1 border-gray-100 bg-gray-50 rounded-2xl text-sm font-bold focus:ring-black focus:border-black transition-all"></textarea>
                    </div>

                    {{-- LOOP OPSI --}}
                    @foreach($opsi as $index => $o)
                    <div wire:key="opsi-input-{{ $index }}">
                        <label class="text-[9px] font-black uppercase text-gray-400 italic">Opsi {{ chr(65 + $index) }}</label>
                        <input type="text"
                               wire:model.live="opsi.{{ $index }}"
                               class="w-full mt-1 border-gray-100 bg-gray-50 rounded-xl text-sm font-bold focus:ring-black focus:border-black transition-all">
                    </div>
                    @endforeach

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-[9px] font-black uppercase text-gray-400 italic">Kunci Jawaban</label>
                            <select wire:model="kunci_jawaban" class="w-full mt-1 border-gray-100 bg-gray-50 rounded-xl text-[11px] font-black uppercase italic focus:ring-black">
                                <option value="">Pilih Kunci</option>
                                @foreach($opsi as $index => $val)
                                    @if(!empty($val))
                                        <option value="{{ $val }}">Opsi {{ chr(65 + $index) }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-[9px] font-black uppercase text-gray-400 italic">Poin</label>
                            <input type="number" wire:model="poin" class="w-full mt-1 border-gray-100 bg-gray-50 rounded-xl text-sm font-bold focus:ring-black">
                        </div>
                    </div>

                    <button wire:click="save" class="w-full bg-[#800000] text-white py-4 rounded-2xl font-black uppercase italic tracking-widest shadow-lg shadow-red-900/20 active:scale-95 transition-all mt-4 disabled:opacity-50">
                        <span wire:loading.remove wire:target="save">{{ $isEdit ? 'Update Question' : 'Save Question' }}</span>
                        <span wire:loading wire:target="save">Processing...</span>
                    </button>

                    @if($isEdit)
                        <button wire:click="resetForm" class="w-full bg-gray-100 text-gray-400 py-2 rounded-xl font-black uppercase text-[10px] mt-2">Cancel Edit</button>
                    @endif
                </div>
            </div>
        </div>

        {{-- Table List (Kanan) --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-[2rem] shadow-xl border border-gray-100 overflow-hidden antialiased">
                <table class="w-full text-left">
                    <thead class="bg-gray-50/50 border-b border-gray-50">
                        <tr>
                            <th class="py-4 px-6 text-[9px] font-black text-gray-400 uppercase italic tracking-widest">Question & Options</th>
                            <th class="py-4 px-6 text-[9px] font-black text-gray-400 uppercase italic tracking-widest">Correct Key</th>
                            <th class="py-4 px-6 text-[9px] font-black text-gray-400 uppercase italic tracking-widest">Points</th>
                            <th class="py-4 px-6 text-center text-[9px] font-black text-gray-400 uppercase italic tracking-widest">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($soals as $item)
                        <tr class="hover:bg-gray-50/30 transition-all group">
                            <td class="py-5 px-6">
                                <p class="text-[11px] font-black uppercase italic leading-tight text-gray-900 mb-2">{{ $item->pertanyaan }}</p>
                                <div class="grid grid-cols-2 gap-x-4 gap-y-1">
                                    @foreach($item->opsi as $idx => $op)
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-[8px] font-black {{ $op == $item->kunci_jawaban ? 'text-green-600' : 'text-gray-300' }}">
                                                {{ chr(65+$idx) }}.
                                            </span>
                                            <span class="text-[9px] font-bold {{ $op == $item->kunci_jawaban ? 'text-green-700' : 'text-gray-500' }}">
                                                {{ Str::limit($op, 20) }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </td>
                            <td class="py-5 px-6">
                                <span class="px-2 py-1 bg-green-50 text-green-600 rounded-lg text-[8px] font-black italic uppercase border border-green-100">
                                    {{ Str::limit($item->kunci_jawaban, 12) }}
                                </span>
                            </td>
                            <td class="py-5 px-6 font-black text-xs text-[#800000] italic">{{ $item->poin }} <span class="text-[8px] text-gray-300">pts</span></td>
                            <td class="py-5 px-6">
                                <div class="flex justify-center gap-2 opacity-0 group-hover:opacity-100 transition-all">
                                    <button wire:click="edit({{ $item->id }})" class="p-2 bg-blue-50 text-blue-600 rounded-xl hover:bg-blue-600 hover:text-white transition shadow-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </button>
                                    <button wire:click="delete({{ $item->id }})" wire:confirm="Hapus soal ini selamanya?" class="p-2 bg-red-50 text-red-600 rounded-xl hover:bg-red-600 hover:text-white transition shadow-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-12 text-center">
                                <p class="text-[10px] font-black text-gray-300 uppercase italic tracking-[0.2em]">Belum ada soal tersedia</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
