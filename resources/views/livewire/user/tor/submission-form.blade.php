<?php

use Livewire\Volt\Component;
use App\Models\TorPeriod;
use App\Models\TorSubmission;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

new class extends Component {
    use WithFileUploads;

    public TorPeriod $period;
    public $answers = [];
    public $temp_files = [];

    public function mount(TorPeriod $period)
    {
        $this->period = $period;

        // Inisialisasi default answers berdasarkan template dari Admin
        foreach ($this->period->form_template as $field) {
            if ($field['type'] === 'table') {
                $this->answers[$field['id']] = [array_fill_keys($field['columns'], '')];
            } elseif ($field['type'] === 'date') {
                $this->answers[$field['id']] = now()->format('Y-m-d');
            } else {
                $this->answers[$field['id']] = '';
            }
        }
    }

    public function addRow($fieldId, $columns)
    {
        $this->answers[$fieldId][] = array_fill_keys($columns, '');
    }

    public function removeRow($fieldId, $index)
    {
        unset($this->answers[$fieldId][$index]);
        $this->answers[$fieldId] = array_values($this->answers[$fieldId]);
    }

public function submit()
{
    // 1. Validasi
    $this->validate([
        'answers.*' => 'required',
        'temp_files.*' => 'nullable|file|max:10240',
    ]);

    // 2. Ambil ID dari field pertama di template untuk dijadikan TITLE
    // Kita ambil key pertama dari array form_template
    $firstField = collect($this->period->form_template)->first();
    $firstFieldId = $firstField['id'] ?? null;

    // Ambil value dari jawaban user berdasarkan ID field pertama tersebut
    $generatedTitle = $this->answers[$firstFieldId] ?? 'Untitled Proposal';

    // 3. Handle Upload File (Sama seperti sebelumnya)
    foreach ($this->temp_files as $fieldId => $file) {
        if ($file) {
            $path = $file->store('submissions/files', 'public');
            $this->answers[$fieldId] = $path;
        }
    }

    // 4. Simpan ke Database
// 4. Simpan ke Database
TorSubmission::create([
    'user_id' => auth()->id(),
    'tor_period_id' => $this->period->id,
    'title' => $generatedTitle,
    'answers' => $this->answers,
    'submission_data' => $this->answers, // <--- TAMBAHKAN INI BOS!
    'status' => 'pending',
    'submission_code' => 'TOR-' . now()->year . '-' . strtoupper(Str::random(5)),
]);

    $this->dispatch('swal', ['title' => 'BERHASIL!', 'text' => 'Proposal TOR Anda sudah terkirim.', 'icon' => 'success']);

    return redirect()->route('user.tor.history');
}
}; ?>
<div class="max-w-5xl mx-auto p-12 bg-white rounded-[4rem] shadow-2xl border border-gray-50 my-10">
    {{-- Header --}}
    <div class="mb-12 border-b border-gray-50 pb-8 flex justify-between items-end">
        <div>
            <h1 class="text-4xl font-black uppercase italic tracking-tighter text-slate-800">Submit <span class="text-[#800000]">Proposal</span></h1>
            <p class="text-[10px] font-bold text-gray-400 uppercase italic tracking-widest mt-2">Periode Aktif: {{ $period->name }}</p>
        </div>
        <div class="text-right">
            <p class="text-[9px] font-black text-slate-300 uppercase italic">User: {{ auth()->user()->name }}</p>
        </div>
    </div>

    <form wire:submit.prevent="submit" class="space-y-12">
        @foreach($period->form_template as $field)
            <div class="group space-y-4">
                <label class="text-[11px] font-black uppercase italic text-slate-500 group-focus-within:text-[#800000] transition-colors flex items-center gap-2">
                    {{ $field['label'] }}
                    @if($field['required'] ?? true) <span class="text-[#800000]">*</span> @endif
                </label>

                {{-- TIPE TEXT & LINK --}}
                @if(in_array($field['type'], ['text', 'link', 'url']))
                    <input type="{{ $field['type'] == 'link' ? 'url' : 'text' }}"
                           wire:model.defer="answers.{{ $field['id'] }}"
                           placeholder="Input {{ $field['label'] }}..."
                           class="w-full bg-gray-50 border-none rounded-2xl p-5 text-sm font-bold focus:ring-2 focus:ring-[#800000] transition-all">

                {{-- TIPE DATE (FIXED) --}}
                @elseif($field['type'] === 'date')
                    <input type="date"
                           wire:model.defer="answers.{{ $field['id'] }}"
                           class="w-full bg-gray-50 border-none rounded-2xl p-5 text-sm font-bold focus:ring-2 focus:ring-[#800000]">

                {{-- TIPE FILE + PREVIEW (FIXED) --}}
                @elseif($field['type'] === 'file')
                    <div class="relative">
                        <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-100 rounded-[2rem] cursor-pointer hover:bg-gray-50 transition-all">
                            <input type="file" wire:model="temp_files.{{ $field['id'] }}" class="hidden" />
                            <p class="text-[10px] font-black uppercase italic text-gray-300">Upload File — {{ $field['label'] }}</p>
                        </label>

                        {{-- Image Preview Logic --}}
                        @if(isset($temp_files[$field['id']]))
                            <div class="mt-4 p-4 bg-[#800000]/5 rounded-3xl flex items-center gap-4 border border-[#800000]/10">
                                @if(in_array($temp_files[$field['id']]->extension(), ['jpg', 'jpeg', 'png', 'webp']))
                                    <img src="{{ $temp_files[$field['id']]->temporaryUrl() }}" class="w-16 h-16 object-cover rounded-xl shadow-lg">
                                @else
                                    <div class="w-16 h-16 bg-white rounded-xl flex items-center justify-center text-xl shadow-sm">📄</div>
                                @endif
                                <div class="overflow-hidden">
                                    <p class="text-[10px] font-black text-[#800000] italic uppercase truncate">{{ $temp_files[$field['id']]->getClientOriginalName() }}</p>
                                    <p class="text-[8px] font-bold text-gray-400 uppercase">Ready to push — {{ number_format($temp_files[$field['id']]->getSize() / 1024, 2) }} KB</p>
                                </div>
                            </div>
                        @endif
                    </div>

                {{-- TIPE RICHTEXT / TEXTAREA --}}
                @elseif($field['type'] === 'richtext' || $field['type'] === 'textarea')
                    <textarea wire:model.defer="answers.{{ $field['id'] }}" rows="6"
                              placeholder="Describe here..."
                              class="w-full bg-gray-50 border-none rounded-[2rem] p-6 text-sm font-bold focus:ring-2 focus:ring-[#800000]"></textarea>

                {{-- TIPE TABLE (Dinamis) --}}
                @elseif($field['type'] === 'table')
                    <div class="overflow-hidden rounded-[2rem] border border-gray-100 shadow-sm">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50">
                                <tr>
                                    @foreach($field['columns'] as $col)
                                        <th class="p-5 text-[9px] font-black uppercase italic text-gray-400 tracking-widest">{{ $col }}</th>
                                    @endforeach
                                    <th class="w-10"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($answers[$field['id']] as $rowIndex => $row)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        @foreach($field['columns'] as $col)
                                            <td class="p-3">
                                                <input type="text"
                                                       wire:model.defer="answers.{{ $field['id'] }}.{{ $rowIndex }}.{{ $col }}"
                                                       class="w-full bg-transparent border-none text-[11px] font-bold focus:ring-0 placeholder-gray-200"
                                                       placeholder="...">
                                            </td>
                                        @endforeach
                                        <td class="p-3 text-center">
                                            <button type="button" wire:click="removeRow('{{ $field['id'] }}', {{ $rowIndex }})" class="text-gray-300 hover:text-[#800000] transition-colors">×</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <button type="button" wire:click="addRow('{{ $field['id'] }}', {{ json_encode($field['columns']) }})"
                                class="w-full p-4 bg-gray-50 text-[9px] font-black uppercase italic text-gray-400 hover:bg-gray-100 transition-all border-t border-gray-100">
                            + Add New Row
                        </button>
                    </div>
                @endif

                {{-- Individual Error Message --}}
                @error('answers.'.$field['id'])
                    <p class="text-[8px] font-black text-[#800000] uppercase italic tracking-widest mt-1">Kolom ini wajib diisi!</p>
                @enderror
            </div>
        @endforeach

        {{-- Action Buttons --}}
        <div class="pt-10 flex flex-col md:flex-row gap-4">
            <button type="submit" wire:loading.attr="disabled"
                    class="flex-[2] bg-black text-white py-6 rounded-[2rem] text-[11px] font-black uppercase italic hover:bg-[#800000] transition-all shadow-2xl shadow-red-900/20 disabled:bg-gray-300">
                <span wire:loading.remove italic>Finalize & Submit Proposal —</span>
                <span wire:loading>Uploading Data... Please Wait</span>
            </button>

            <a href="{{ route('user.tor.history') }}" class="flex-1 bg-gray-50 text-gray-400 py-6 rounded-[2rem] text-[11px] font-black uppercase italic text-center hover:bg-gray-100 transition-all">
                Cancel
            </a>
        </div>
    </form>
</div>
