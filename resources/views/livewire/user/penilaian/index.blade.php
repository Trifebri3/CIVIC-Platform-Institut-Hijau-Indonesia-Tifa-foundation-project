<?php

use App\Models\PenilaianUser;
use Livewire\Volt\Component;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    public $search = '';

    public function with()
    {
        return [
            'my_grades' => PenilaianUser::with('template')
                ->where('user_id', Auth::id()) // Proteksi: Hanya data milik user login
                ->when($this->search, function($q) {
                    $q->whereHas('template', fn($t) => $t->where('template_name', 'like', "%{$this->search}%"));
                })
                ->latest()
                ->get()
        ];
    }

    // Fungsi Download QR Langsung (Format SVG)
    public function downloadQR($secret, $templateName)
    {
        $url = route('nilai.verify', $secret);
        $qr = QrCode::format('svg')->size(500)->margin(2)->generate($url);

        return response()->streamDownload(function () use ($qr) {
            echo $qr;
        }, "QR_VERIFY_" . Str::slug($templateName) . ".svg");
    }
}; ?>

<div class="p-6 lg:p-12 space-y-8">
    {{-- Header & Search --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h1 class="text-3xl font-black uppercase italic tracking-tighter">My <span class="text-[#800000]">Certificates</span></h1>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.3em] mt-1">Official Digital Validation Dashboard</p>
        </div>

        <div class="relative group">
            <input type="text" wire:model.live="search" placeholder="Cari nama program..."
                   class="bg-gray-50 border-0 rounded-2xl px-6 py-4 w-full md:w-80 font-bold focus:ring-2 focus:ring-[#800000] transition-all">
            <div class="absolute right-4 top-4 text-gray-300 group-focus-within:text-[#800000]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="3" stroke-linecap="round"/></svg>
            </div>
        </div>
    </div>

    {{-- Grid Daftar Nilai --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($my_grades as $grade)
        <div class="bg-white rounded-[2.5rem] border border-gray-100 p-8 shadow-xl shadow-gray-200/50 hover:border-[#800000]/20 transition-all group relative overflow-hidden">
            {{-- Aksen Background --}}
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-gray-50 rounded-full group-hover:bg-red-50 transition-colors"></div>

            <div class="relative">
                <span class="text-[9px] font-black uppercase tracking-widest text-[#800000] bg-red-50 px-3 py-1 rounded-full">Validated</span>
                <h3 class="text-xl font-black uppercase italic mt-4 leading-tight text-slate-800">{{ $grade->template->template_name }}</h3>
                <p class="text-[10px] text-gray-400 font-bold mt-1 uppercase italic">Published: {{ $grade->created_at->format('d M Y') }}</p>

                {{-- Preview Nilai Singkat --}}
                <div class="mt-6 flex gap-2 overflow-x-auto pb-2">
                    @foreach(array_slice($grade->isi_nilai, 0, 3) as $key => $val)
                    <div class="bg-gray-50 px-3 py-2 rounded-xl flex flex-col items-center min-w-[60px]">
                        <span class="text-[7px] font-black text-gray-400 uppercase">{{ $key }}</span>
                        <span class="text-xs font-black italic text-[#800000]">{{ $val }}</span>
                    </div>
                    @endforeach
                </div>

                <div class="grid grid-cols-2 gap-3 mt-8">
                    {{-- Download PDF --}}
                    <a href="{{ route('nilai.pdf', $grade->qr_code_secret) }}" target="_blank"
                       class="flex items-center justify-center gap-2 bg-black text-white py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-[#800000] transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        PDF
                    </a>

                    {{-- Download QR --}}
                    <button wire:click="downloadQR('{{ $grade->qr_code_secret }}', '{{ $grade->template->template_name }}')"
                            class="flex items-center justify-center gap-2 bg-gray-100 text-slate-800 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-gray-200 transition-all text-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        QR Code
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full py-20 text-center bg-gray-50 rounded-[3rem] border-2 border-dashed border-gray-100">
            <div class="text-gray-200 mb-4">
                <svg class="w-20 h-20 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>
            <p class="text-sm font-black uppercase text-gray-300 italic tracking-widest">Belum ada nilai yang dipublish untuk Anda</p>
        </div>
        @endforelse
    </div>
</div>
