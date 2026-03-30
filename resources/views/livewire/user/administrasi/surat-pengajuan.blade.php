<?php

use App\Models\SuratSubmission;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;

    public $wilayah_kegiatan, $penerima_surat, $hari_tanggal, $waktu_pelaksanaan, $tempat_pelaksanaan, $kontak_person;
    public $file_lampiran;

    public function submit()
    {
        // Validasi
        $validated = $this->validate([
            'wilayah_kegiatan'   => 'required|string|max:255',
            'penerima_surat'     => 'required|string|max:255',
            'hari_tanggal'       => 'required|string|max:255',
            'waktu_pelaksanaan'  => 'required|string|max:255',
            'tempat_pelaksanaan' => 'required|string|max:255',
            'kontak_person'      => 'required|numeric',
            'file_lampiran'      => 'nullable|file|mimes:pdf,docx,png,jpg|max:5120', // Max 5MB
        ]);

        try {
            $path = null;
            if ($this->file_lampiran) {
                // Simpan ke folder public/lampiran-surat
                $path = $this->file_lampiran->store('lampiran-surat', 'public');
            }

            SuratSubmission::create([
                'user_id'            => auth()->id(),
                'wilayah_kegiatan'   => $this->wilayah_kegiatan,
                'penerima_surat'     => $this->penerima_surat,
                'hari_tanggal'       => $this->hari_tanggal,
                'waktu_pelaksanaan'  => $this->waktu_pelaksanaan,
                'tempat_pelaksanaan' => $this->tempat_pelaksanaan,
                'kontak_person'      => $this->kontak_person,
                'lampiran'           => $this->file_lampiran ? '1 Berkas' : '-',
                'admin_note'         => $path, // Simpan path TOR disini
                'status'             => 'pending',
            ]);

            session()->flash('message', 'Pesan: Pengajuan Berhasil Terkirim!');

            // Reset Form
            $this->reset(['wilayah_kegiatan', 'penerima_surat', 'hari_tanggal', 'waktu_pelaksanaan', 'tempat_pelaksanaan', 'kontak_person', 'file_lampiran']);

        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function with()
    {
        return [
            'mySubmissions' => SuratSubmission::where('user_id', auth()->id())->latest()->get(),
        ];
    }
}; ?>

<div class="max-w-6xl mx-auto py-10 px-4">
    {{-- Notifikasi Sukses --}}
    @if (session()->has('message'))
        <div class="mb-6 p-4 bg-emerald-500 text-white rounded-2xl font-black uppercase italic text-xs shadow-lg animate-bounce">
            {{ session('message') }}
        </div>
    @endif

    {{-- Notifikasi Error --}}
    @if (session()->has('error'))
        <div class="mb-6 p-4 bg-red-500 text-white rounded-2xl font-black uppercase italic text-xs shadow-lg">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-[#800000] p-8 rounded-[2rem] text-white shadow-xl">
                <h2 class="text-2xl font-black italic uppercase leading-none">Pengajuan Surat</h2>
                <p class="text-[10px] font-bold opacity-70 mt-2 tracking-widest">CIVIC EDUCATION PLATFORM</p>
            </div>

            <form wire:submit.prevent="submit" class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100 space-y-4">
                {{-- Input Wilayah --}}
                <div>
                    <label class="text-[10px] font-black uppercase text-slate-400 ml-2 italic">Wilayah FGD</label>
                    <input type="text" wire:model="wilayah_kegiatan" class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl p-4 text-sm font-bold focus:border-[#800000] outline-none">
                    @error('wilayah_kegiatan') <span class="text-[9px] text-red-500 font-bold ml-2">{{ $message }}</span> @enderror
                </div>

                {{-- Input Penerima --}}
                <div>
                    <label class="text-[10px] font-black uppercase text-slate-400 ml-2 italic">Penerima (Yth.)</label>
                    <input type="text" wire:model="penerima_surat" class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl p-4 text-sm font-bold focus:border-[#800000] outline-none">
                    @error('penerima_surat') <span class="text-[9px] text-red-500 font-bold ml-2">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-[10px] font-black uppercase text-slate-400 ml-2 italic">Hari/Tgl</label>
                        <input type="text" wire:model="hari_tanggal" placeholder="Senin, 30 Mar" class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl p-4 text-sm font-bold focus:border-[#800000] outline-none">
                        @error('hari_tanggal') <span class="text-[9px] text-red-500 font-bold ml-2">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="text-[10px] font-black uppercase text-slate-400 ml-2 italic">Waktu</label>
                        <input type="text" wire:model="waktu_pelaksanaan" placeholder="09:00" class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl p-4 text-sm font-bold focus:border-[#800000] outline-none">
                        @error('waktu_pelaksanaan') <span class="text-[9px] text-red-500 font-bold ml-2">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label class="text-[10px] font-black uppercase text-slate-400 ml-2 italic">Tempat</label>
                    <input type="text" wire:model="tempat_pelaksanaan" class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl p-4 text-sm font-bold focus:border-[#800000] outline-none">
                    @error('tempat_pelaksanaan') <span class="text-[9px] text-red-500 font-bold ml-2">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="text-[10px] font-black uppercase text-slate-400 ml-2 italic">No WA Narahubung</label>
                    <input type="text" wire:model="kontak_person" class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl p-4 text-sm font-bold focus:border-[#800000] outline-none">
                    @error('kontak_person') <span class="text-[9px] text-red-500 font-bold ml-2">{{ $message }}</span> @enderror
                </div>

                {{-- Upload File --}}
                <div class="p-4 border-2 border-dashed border-slate-200 rounded-3xl bg-slate-50/50">
                    <label class="text-[10px] font-black uppercase text-slate-400 block mb-2 italic">Lampiran TOR (PDF/JPG)</label>
                    <input type="file" wire:model="file_lampiran" class="text-[10px] font-bold text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:bg-slate-200 hover:file:bg-black hover:file:text-white transition-all">
                    <div wire:loading wire:target="file_lampiran" class="text-[8px] font-black text-amber-600 mt-2 uppercase">Sedang mengupload file...</div>
                    @error('file_lampiran') <span class="text-[9px] text-red-500 font-bold ml-2">{{ $message }}</span> @enderror
                </div>

                <button type="submit" wire:loading.attr="disabled" class="w-full bg-black text-white py-5 rounded-3xl font-black uppercase italic hover:bg-[#800000] transition-all shadow-xl shadow-black/10">
                    <span wire:loading.remove>Kirim Sekarang</span>
                    <span wire:loading>Memproses Data...</span>
                </button>
            </form>
        </div>

        {{-- RIWAYAT --}}
        <div class="lg:col-span-2 space-y-6">
            <h3 class="text-xl font-black italic uppercase text-slate-800">Status Pengajuan</h3>
            <div class="space-y-4">
                @forelse($mySubmissions as $surat)
                <div class="bg-white border border-slate-100 p-6 rounded-[2.5rem] flex items-center justify-between group hover:border-[#800000] transition-all shadow-sm">
                    <div class="flex items-center gap-6">
                        <div class="w-14 h-14 rounded-2xl flex items-center justify-center border-2 {{ $surat->status_badge }}">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-width="2.5"></path></svg>
                        </div>
                        <div>
                            <h4 class="font-black italic uppercase text-sm text-slate-800">{{ $surat->penerima_surat }}</h4>
                            <p class="text-[9px] font-bold text-slate-400 mt-1 uppercase tracking-widest">
                                FGD: {{ $surat->wilayah_kegiatan }} • {{ $surat->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>

                    <div>
                        @if($surat->status === 'approved')
                            <a href="{{ route('user.surat.download', $surat->id) }}" target="_blank" class="px-8 py-3 bg-black text-white rounded-2xl text-[10px] font-black uppercase italic hover:bg-[#800000] transition-all shadow-lg">
                                Cetak PDF
                            </a>
                        @else
                            <div class="px-6 py-3 {{ $surat->status_badge }} rounded-2xl text-[9px] font-black uppercase italic border-2">
                                {{ $surat->status }}
                            </div>
                        @endif
                    </div>
                </div>
                @empty
                    <div class="p-20 text-center bg-slate-50 rounded-[3rem] border-2 border-dashed border-slate-200 text-slate-300 font-black uppercase italic">
                        Belum ada riwayat pengajuan.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
