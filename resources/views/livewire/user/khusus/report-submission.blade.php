<?php

use App\Models\RabPeriod;
use App\Models\ReportTemplate;
use App\Models\ProgramReport;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

new class extends Component {
    use WithFileUploads;

    public RabPeriod $period;
    public $template;
    public $alreadySubmitted = false;
    public $submittedReport = null; // Tambahkan ini untuk menampung data laporan yang sudah ada

    public $responses = [];
    public $attachments = [];

    public function mount(RabPeriod $period)
    {
        $this->period = $period;

        // Ambil data laporan jika sudah pernah submit
        $this->submittedReport = ProgramReport::where('user_id', auth()->id())
            ->where('rab_period_id', $this->period->id)
            ->first();

        if ($this->submittedReport) {
            $this->alreadySubmitted = true;
            return;
        }

        $this->template = ReportTemplate::where('rab_period_id', $this->period->id)
            ->where('is_active', true)
            ->first();

        if ($this->template) {
            foreach ($this->template->fields as $field) {
                $this->responses[$field['name']] = '';
                if (in_array($field['type'], ['image', 'file'])) {
                    $this->attachments[$field['name']] = null;
                }
            }
        }
    }

    public function submit()
    {
        if ($this->alreadySubmitted || !$this->template) return;

        $this->validate(['responses.*' => 'required'], ['responses.*.required' => 'Wajib diisi.']);

        try {
            DB::beginTransaction();

            $finalContent = $this->responses;

            foreach ($this->attachments as $key => $file) {
                if ($file instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                    $finalContent[$key] = $file->store('reports', 'public');
                }
            }

            $report = new ProgramReport();
            $report->user_id = auth()->id();
            $report->rab_period_id = $this->period->id;
            $report->report_template_id = $this->template->id;
            $report->content = $finalContent;
            $report->status = 'pending';
            $report->submitted_at = now();

            if ($report->save()) {
                DB::commit();
                $this->alreadySubmitted = true;
                $this->submittedReport = $report; // Simpan ke property agar bisa diakses di view
                session()->flash('message', 'BERHASIL!');
                return redirect()->to(request()->header('Referer'));
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('ERR: ' . $e->getMessage());
            session()->flash('error', 'Gagal: ' . $e->getMessage());
        }
    }
}; ?>

<div class="max-w-4xl mx-auto py-10 px-4">
    @if($alreadySubmitted && $submittedReport)
        {{-- VIEW STATUS LAPORAN (PENDING/APPROVED/REJECTED) --}}
        <div class="space-y-6">
            @php
                $statusConfig = [
                    'pending' => ['bg' => 'bg-amber-50', 'border' => 'border-amber-200', 'text' => 'text-amber-800', 'label' => 'Menunggu Peninjauan'],
                    'approved' => ['bg' => 'bg-emerald-50', 'border' => 'border-emerald-200', 'text' => 'text-emerald-800', 'label' => 'Laporan Disetujui'],
                    'rejected' => ['bg' => 'bg-red-50', 'border' => 'border-red-200', 'text' => 'text-red-800', 'label' => 'Laporan Ditolak'],
                ];
                $conf = $statusConfig[$submittedReport->status] ?? $statusConfig['pending'];
            @endphp

            <div class="{{ $conf['bg'] }} border-4 border-dashed {{ $conf['border'] }} p-12 rounded-[4rem] text-center shadow-sm">
                <div class="inline-block px-4 py-1 rounded-full border {{ $conf['border'] }} {{ $conf['text'] }} text-[8px] font-black uppercase tracking-widest mb-6">
                    Status: {{ $submittedReport->status }}
                </div>

                <h2 class="text-4xl font-black italic uppercase {{ $conf['text'] }} leading-none">
                    {{ $conf['label'] }}
                </h2>

                @if($submittedReport->status === 'rejected')
                    <div class="mt-6 p-6 bg-white/50 rounded-3xl border border-red-100 mx-auto max-w-lg">
                        <p class="text-[10px] font-black text-red-900 uppercase italic mb-2">Alasan Penolakan:</p>
                        <p class="text-sm font-bold text-red-700 italic">"{{ $submittedReport->admin_feedback ?? 'Tidak ada catatan tambahan.' }}"</p>
                    </div>
                @endif

                <div class="mt-10 flex flex-col sm:flex-row gap-4 justify-center">
                    {{-- TOMBOL PREVIEW PDF --}}
{{-- TOMBOL PREVIEW PDF --}}
<a href="{{ route('user.report.pdf', $submittedReport->id) }}" target="_blank"
   class="flex items-center justify-center gap-3 px-10 py-5 bg-black text-white rounded-2xl text-[10px] font-black uppercase italic hover:bg-[#800000] transition-all active:scale-95">
    <span>Preview PDF Laporan</span>
    <span class="text-lg">→</span>
</a>

                    @if($submittedReport->status === 'rejected')
                        {{-- Opsi Resubmit jika ditolak (Opsional, tinggal arahkan ke route hapus/edit) --}}
                        <button wire:click="$set('alreadySubmitted', false)" class="text-[9px] font-black uppercase italic underline text-slate-400 hover:text-black">
                            Re-Upload Laporan Baru?
                        </button>
                    @endif
                </div>
            </div>

            {{-- Ringkasan Info --}}
            <div class="bg-white border border-slate-100 p-8 rounded-[3rem] flex justify-between items-center px-12">
                <div>
                    <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Waktu Kirim</p>
                    <p class="text-xs font-bold text-slate-800 uppercase italic">{{ $submittedReport->submitted_at->format('d M Y, H:i') }} WIB</p>
                </div>
                <div class="text-right">
                    <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest">ID Laporan</p>
                    <p class="text-xs font-bold text-slate-800 uppercase italic">#REP-{{ str_pad($submittedReport->id, 5, '0', STR_PAD_LEFT) }}</p>
                </div>
            </div>
        </div>

    @elseif($template)
        {{-- FORM INPUT (Sama seperti kode awal kamu) --}}
        <form wire:submit.prevent="submit" class="space-y-8">
            <div class="bg-[#800000] p-10 rounded-[3rem] text-white shadow-xl">
                <h1 class="text-4xl font-black italic uppercase">{{ $template->title }}</h1>
                <p class="text-xs font-bold opacity-70">{{ $period->name }}</p>
            </div>

            <div class="bg-white p-10 rounded-[3rem] shadow-sm border border-slate-100 space-y-8">
                @foreach($template->fields as $field)
                    <div class="space-y-3">
                        <label class="text-xs font-black uppercase italic text-slate-700">{{ $field['label'] }}</label>

                        @if($field['type'] === 'text')
                            <input type="text" wire:model="responses.{{ $field['name'] }}" class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl p-5 font-bold outline-none focus:border-[#800000] transition-colors">
                        @elseif($field['type'] === 'textarea')
                            <textarea wire:model="responses.{{ $field['name'] }}" rows="4" class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl p-5 font-bold outline-none focus:border-[#800000] transition-colors"></textarea>
                        @elseif(in_array($field['type'], ['image', 'file']))
                            <div class="relative group">
                                <input type="file" wire:model="attachments.{{ $field['name'] }}" class="w-full text-xs font-bold text-slate-500 file:mr-4 file:py-3 file:px-6 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-slate-200 file:text-slate-700 hover:file:bg-black hover:file:text-white file:transition-all">
                            </div>
                        @endif
                        @error('responses.'.$field['name']) <span class="text-[9px] font-black text-red-600 uppercase italic">{{ $message }}</span> @enderror
                    </div>
                @endforeach

                <button type="submit" wire:loading.attr="disabled" class="w-full bg-black text-white py-6 rounded-2xl font-black uppercase italic hover:bg-[#800000] transition-all disabled:bg-slate-300 shadow-xl shadow-black/10">
                    <span wire:loading.remove>Kirim Laporan</span>
                    <span wire:loading>Memproses...</span>
                </button>
            </div>
        </form>
    @else
        {{-- Jika Template Tidak Ada --}}
        <div class="p-20 text-center uppercase font-black italic text-slate-300 tracking-widest">
            Template laporan belum tersedia untuk periode ini.
        </div>
    @endif
</div>
