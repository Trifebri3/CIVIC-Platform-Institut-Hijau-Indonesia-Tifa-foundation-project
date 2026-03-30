<?php

use Livewire\Volt\Component;
use App\Models\ModulUjian;
use App\Models\JawabanUjian;
use Illuminate\Support\Facades\Response;
use Barryvdh\DomPDF\Facade\Pdf;

new class extends Component {
    public $modul;
    public $search = '';
    public $selectedJawaban = null; // Untuk Modal Penilaian
    public $nilai, $feedback;

    public function mount($id)
    {
        $this->modul = ModulUjian::with('jawaban.user')->findOrFail($id);
    }

    public function with()
    {
        return [
            'listJawaban' => JawabanUjian::where('modul_ujian_id', $this->modul->id)
                ->whereHas('user', fn($q) => $q->where('name', 'like', "%{$this->search}%"))
                ->with('user')
                ->get()
        ];
    }

    // --- LOGIC PENILAIAN ---
    public function setGrading($id)
    {
        $jawaban = JawabanUjian::find($id);
        $this->selectedJawaban = $jawaban;
        $this->nilai = $jawaban->nilai;
        $this->feedback = $jawaban->feedback_admin;
    }

    public function saveGrade()
    {
        $this->selectedJawaban->update([
            'nilai' => $this->nilai,
            'feedback_admin' => $this->feedback,
            'graded_at' => now()
        ]);

        $this->selectedJawaban = null;
        session()->flash('success', 'Nilai berhasil disimpan!');
    }

    // --- EXPORT CSV ---
    public function exportCSV()
    {
        $filename = "Rekap-Nilai-{$this->modul->judul}.csv";
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Nama Mahasiswa', 'Email', 'Nilai', 'Feedback', 'Tanggal Submit'];

        $callback = function() {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['REKAP NILAI: ' . $this->modul->judul]);
            fputcsv($file, []);
            fputcsv($file, ['Nama Mahasiswa', 'Email', 'Nilai', 'Feedback', 'Tanggal Submit']);

            foreach ($this->modul->jawaban as $row) {
                fputcsv($file, [$row->user->name, $row->user->email, $row->nilai ?? '0', $row->feedback_admin, $row->created_at]);
            }
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}; ?>

<div class="p-8 bg-[#fafafa] min-h-screen antialiased">
    {{-- Header --}}
    <div class="flex justify-between items-end mb-10">
        <div>
            <h1 class="text-3xl font-black uppercase italic italic tracking-tighter">Grading <span class="text-[#800000]">Center</span></h1>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.3em] mt-1">{{ $modul->judul }}</p>
        </div>
        <div class="flex gap-2">
            <button wire:click="exportCSV" class="px-5 py-2.5 bg-green-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-green-700 transition">Export CSV</button>
            <a href="{{ route('admin.modul-ujian.rekap-pdf', $modul->id) }}" target="_blank" class="px-5 py-2.5 bg-black text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-gray-800 transition">Rekap PDF</a>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-[2rem] border border-gray-100 shadow-xl overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/50 border-b border-gray-100">
                    <th class="py-5 px-8 text-[10px] font-black text-gray-400 uppercase italic">Mahasiswa</th>
                    <th class="py-5 px-6 text-[10px] font-black text-gray-400 uppercase italic">Status</th>
                    <th class="py-5 px-6 text-[10px] font-black text-gray-400 uppercase italic">Nilai</th>
                    <th class="py-5 px-8 text-center text-[10px] font-black text-gray-400 uppercase italic">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($listJawaban as $jawaban)
                <tr class="hover:bg-gray-50/50 transition-all">
                    <td class="py-5 px-8 font-bold text-sm uppercase italic">{{ $jawaban->user->name }}</td>
                    <td class="py-5 px-6">
                        @if($jawaban->nilai)
                            <span class="px-3 py-1 bg-green-50 text-green-600 rounded-lg text-[9px] font-black italic">GRADED</span>
                        @else
                            <span class="px-3 py-1 bg-orange-50 text-orange-600 rounded-lg text-[9px] font-black italic">PENDING</span>
                        @endif
                    </td>
                    <td class="py-5 px-6 font-black text-lg">{{ $jawaban->nilai ?? '-' }}</td>
                    <td class="py-5 px-8 text-center flex justify-center gap-2">
                        <button wire:click="setGrading({{ $jawaban->id }})" class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition">Grade</button>
                        <a href="{{ route('admin.modul-ujian.cetak-satuan', $jawaban->id) }}" target="_blank" class="p-2 bg-gray-50 text-gray-600 rounded-lg hover:bg-black hover:text-white transition">PDF</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Penilaian Modal (Simple Overlay) --}}
    @if($selectedJawaban)
    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-2xl rounded-[2.5rem] p-10 shadow-2xl">
            <h2 class="text-2xl font-black uppercase italic italic mb-6">Grade: {{ $selectedJawaban->user->name }}</h2>

            <div class="space-y-4 max-h-[50vh] overflow-y-auto mb-6 pr-4">
                @foreach($modul->konfigurasi_soal as $soal)
                    <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100">
                        <p class="text-[9px] font-black text-gray-400 uppercase mb-1">{{ $soal['pertanyaan'] }}</p>
                        <p class="text-sm font-bold">{{ $selectedJawaban->konten_jawaban[$soal['id']] ?? 'Tidak diisi' }}</p>
                    </div>
                @endforeach
            </div>

            <div class="grid grid-cols-4 gap-4">
                <div class="col-span-1">
                    <label class="text-[10px] font-black uppercase italic">Nilai (0-100)</label>
                    <input type="number" wire:model="nilai" class="w-full mt-1 border-gray-200 rounded-xl font-black text-xl text-center">
                </div>
                <div class="col-span-3">
                    <label class="text-[10px] font-black uppercase italic">Feedback / Catatan Admin</label>
                    <textarea wire:model="feedback" rows="2" class="w-full mt-1 border-gray-200 rounded-xl text-sm font-medium"></textarea>
                </div>
            </div>

            <div class="flex gap-3 mt-8">
                <button wire:click="saveGrade" class="flex-1 bg-[#800000] text-white py-4 rounded-2xl font-black uppercase italic tracking-widest shadow-lg">Simpan Nilai</button>
                <button wire:click="$set('selectedJawaban', null)" class="px-8 bg-gray-100 text-gray-500 rounded-2xl font-black uppercase italic">Batal</button>
            </div>
        </div>
    </div>
    @endif
</div>
