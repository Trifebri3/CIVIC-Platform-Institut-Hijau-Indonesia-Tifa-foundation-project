<?php

use App\Models\{SubProgram, SubProgramTemplate, Program};
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;

new class extends Component {
    use WithFileUploads;

    public $program_id, $template_id, $title, $order = 0;
    public $deadline; // Kolom Baru untuk Tanggal & Jam
    public $content_data = [];
    public $activeTemplate = null;

    public function updatedTemplateId($value)
    {
        $this->activeTemplate = SubProgramTemplate::find($value);
        $this->content_data = [];

        if ($this->activeTemplate) {
            foreach ($this->activeTemplate->fields_schema as $field) {
                $this->content_data[$field['name']] = '';
            }
        }
    }

    public function save()
    {
        $this->validate([
            'program_id'  => 'required',
            'template_id' => 'required',
            'title'       => 'required|min:3',
            'deadline'    => 'required|date', // Validasi Tanggal
            'order'       => 'required|integer',
        ]);

        // Proses Upload File/Image secara dinamis dari content_data
        $finalData = $this->content_data;
        foreach ($finalData as $key => $value) {
            if ($value instanceof \Illuminate\Http\UploadedFile) {
                // Simpan ke folder public/uploads/content
                $path = $value->store('uploads/content', 'public');
                $finalData[$key] = $path;
            }
        }

        // Simpan ke Database
        SubProgram::create([
            'program_id'   => $this->program_id,
            'template_id'  => $this->template_id,
            'title'        => $this->title,
            'slug'         => Str::slug($this->title) . '-' . Str::random(5),
            'content_data' => $finalData,
            'deadline'     => $this->deadline, // Data Tanggal & Jam
            'order'        => $this->order,
            'status'       => 'active',
        ]);

        session()->flash('success', 'Entri Program Berhasil Dipublikasikan!');
        return redirect()->route('admin-program.content.index');
    }

    public function with()
    {
        return [
            // Pastikan user memiliki relasi managedPrograms di model User
            'myPrograms' => auth()->user()->managedPrograms ?? Program::all(),
            'templates'  => SubProgramTemplate::all(),
        ];
    }
}; ?>

<div class="max-w-5xl mx-auto pb-24 antialiased">
    <div class="bg-white rounded-[3.5rem] p-12 shadow-2xl shadow-gray-200/50 border border-gray-50 relative overflow-hidden">

        {{-- Decorative Element --}}
        <div class="absolute top-0 right-0 w-32 h-32 bg-[#800000]/5 rounded-bl-full"></div>

        <div class="mb-12">
            <h2 class="text-4xl font-black text-gray-900 uppercase italic tracking-tighter leading-none">New Entry</h2>
            <div class="flex items-center gap-3 mt-3">
                <span class="h-[2px] w-12 bg-[#800000]"></span>
                <p class="text-[10px] text-[#800000] font-black uppercase tracking-[0.4em] italic">YOTA Digital Infrastructure</p>
            </div>
        </div>

        {{-- Section 1: Core Configuration --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10">
            <div class="space-y-3">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-4">Otoritas Program</label>
                <select wire:model="program_id" class="w-full rounded-[1.5rem] border-none bg-gray-50 p-4 font-bold text-sm focus:ring-2 focus:ring-[#800000]/20 transition-all">
                    <option value="">-- Select Authority --</option>
                    @foreach($myPrograms as $p) <option value="{{ $p->id }}">{{ $p->name }}</option> @endforeach
                </select>
                @error('program_id') <p class="text-[8px] font-bold text-red-500 uppercase ml-4 italic">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-3">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-4">Arsitektur Template</label>
                <select wire:model.live="template_id" class="w-full rounded-[1.5rem] border-none bg-gray-50 p-4 font-bold text-sm focus:ring-2 focus:ring-[#800000]/20 transition-all text-[#800000]">
                    <option value="">-- Select Template --</option>
                    @foreach($templates as $t) <option value="{{ $t->id }}">{{ $t->name }}</option> @endforeach
                </select>
                @error('template_id') <p class="text-[8px] font-bold text-red-500 uppercase ml-4 italic">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-3">
                <label class="text-[10px] font-black text-[#800000] uppercase tracking-widest ml-4 flex items-center gap-2">
                    <svg class="w-3 h-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0114 0z" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Waktu Pelaksanaan
                </label>
                <input type="datetime-local" wire:model="deadline"
                       class="w-full rounded-[1.5rem] border-none bg-red-50/50 p-4 font-black text-sm focus:ring-2 focus:ring-[#800000]/20 text-gray-700">
                @error('deadline') <p class="text-[8px] font-bold text-red-500 uppercase ml-4 italic">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Section 2: Content Title --}}
        <div class="mb-12 group">
            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-4 block mb-3 group-hover:text-black transition-colors">Headline Entry Title</label>
            <input type="text" wire:model="title"
                   class="w-full rounded-[2rem] border-none bg-gray-50 p-6 font-black text-2xl focus:ring-4 focus:ring-[#800000]/5 placeholder-gray-200 uppercase italic tracking-tighter"
                   placeholder="MASUKKAN JUDUL MATERI...">
            @error('title') <p class="text-[8px] font-bold text-red-500 uppercase ml-6 mt-2 italic">{{ $message }}</p> @enderror
        </div>

        <div class="h-[1px] w-full bg-gradient-to-r from-transparent via-gray-100 to-transparent my-12"></div>

        {{-- Section 3: Dynamic Template Fields --}}
        @if($activeTemplate)
            <div class="space-y-10 animate-in slide-in-from-bottom-4 duration-700">
                <div class="flex items-center justify-between px-4">
                    <h3 class="text-[11px] font-black uppercase text-gray-400 tracking-[0.3em]">Arsitektur Data: {{ $activeTemplate->name }}</h3>
                    <span class="px-3 py-1 bg-black text-white text-[8px] font-black rounded-full italic tracking-widest">DYNAMIC FIELD</span>
                </div>

                <div class="grid grid-cols-1 gap-8">
                    @foreach($activeTemplate->fields_schema as $field)
                        <div class="bg-gray-50/30 p-8 rounded-[2.5rem] border border-gray-50 hover:border-gray-200 transition-all group">
                            <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-4 block group-hover:text-[#800000] transition-colors">{{ $field['label'] }}</label>

                            @if($field['type'] == 'textarea')
                                <textarea wire:model="content_data.{{ $field['name'] }}" rows="5"
                                          class="w-full rounded-[1.5rem] border-none bg-white font-bold text-sm focus:ring-4 focus:ring-[#800000]/5 shadow-sm p-6 placeholder-gray-100"
                                          placeholder="Tulis deskripsi lengkap di sini..."></textarea>

                            @elseif($field['type'] == 'file' || $field['type'] == 'image')
                                <div class="flex items-center gap-8">
                                    <div class="w-24 h-24 rounded-3xl bg-white border-2 border-dashed border-gray-100 flex items-center justify-center overflow-hidden shadow-inner group-hover:border-[#800000]/30 transition-all">
                                        @php
                                            $val = $content_data[$field['name']] ?? null;
                                            $isImg = $val && $val instanceof \Illuminate\Http\UploadedFile && in_array($val->guessExtension(), ['png', 'jpg', 'jpeg', 'webp']);
                                        @endphp

                                        @if($isImg)
                                            <img src="{{ $val->temporaryUrl() }}" class="w-full h-full object-cover">
                                        @elseif($val)
                                            <div class="text-center p-2">
                                                <svg class="w-8 h-8 text-black mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" stroke-width="2.5"/></svg>
                                                <p class="text-[7px] font-black text-gray-400 mt-1 uppercase italic">{{ $val->getClientOriginalExtension() }}</p>
                                            </div>
                                        @else
                                            <svg class="w-8 h-8 text-gray-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="3" stroke-linecap="round"/></svg>
                                        @endif
                                    </div>

                                    <div class="flex-1 space-y-3">
                                        <input type="file" wire:model="content_data.{{ $field['name'] }}"
                                               class="block w-full text-[10px] text-gray-400 file:mr-6 file:py-3 file:px-8 file:rounded-full file:border-0 file:text-[9px] file:font-black file:bg-black file:text-white hover:file:bg-[#800000] cursor-pointer transition-all shadow-lg">
                                        <p class="text-[8px] font-bold text-gray-300 italic tracking-wider">Format: PDF, PNG, JPG (MAX 10MB)</p>
                                        <div wire:loading wire:target="content_data.{{ $field['name'] }}" class="text-[8px] font-black text-[#800000] italic animate-pulse tracking-[0.2em]">PROCESSING ASSET...</div>
                                    </div>
                                </div>

                            @else
                                <input type="{{ $field['type'] }}" wire:model="content_data.{{ $field['name'] }}"
                                       class="w-full rounded-[1.2rem] border-none bg-white font-black text-sm focus:ring-4 focus:ring-[#800000]/5 shadow-sm p-4 px-6">
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            {{-- Empty State --}}
            <div class="py-20 text-center bg-gray-50 rounded-[3rem] border border-dashed border-gray-100 shadow-inner">
                <svg class="w-12 h-12 text-gray-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <p class="text-[10px] font-black text-gray-300 uppercase tracking-[0.4em] italic leading-loose">Silakan pilih Arsitektur Template<br>untuk memuat bidang input dinamis.</p>
            </div>
        @endif

        {{-- Finalize Action --}}
        <div class="mt-20 flex flex-col md:flex-row items-center justify-between gap-6 px-4">
            <div class="flex items-center gap-4">
                <input type="number" wire:model="order" class="w-20 rounded-xl border-none bg-gray-50 p-4 font-black text-center text-sm focus:ring-2 focus:ring-[#800000]/10">
                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest italic">Urutan Tampilan</label>
            </div>

            <button wire:click="save" wire:loading.attr="disabled"
                    class="w-full md:w-auto bg-black text-white px-16 py-6 rounded-[2rem] font-black text-[11px] uppercase tracking-[0.4em] italic shadow-2xl hover:bg-[#800000] hover:scale-[1.02] active:scale-[0.98] transition-all disabled:opacity-50">
                <span wire:loading.remove wire:target="save">Finalize & Publish Entry</span>
                <span wire:loading wire:target="save" class="flex items-center gap-3">
                    <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    SYSTEM DEPLOYING...
                </span>
            </button>
        </div>
    </div>
</div>
