<?php

use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Models\SubProgram;
use App\Models\SubProgramContent;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

new class extends Component {
    use WithFileUploads;

    public SubProgram $subProgram;
    public ?SubProgramContent $content = null;

    public $title = '';
    public $modules = [];
    public $isEdit = false;
    public $tempFiles = []; // Menyimpan objek TemporaryUploadedFile

    public function mount(SubProgram $subProgram, ?SubProgramContent $content = null) {
        $this->subProgram = $subProgram;

        if ($content && $content->exists) {
            $this->content = $content;
            $this->title = $content->title;
            // Pastikan modules adalah array (karena biasanya disimpan sebagai JSON di DB)
            $this->modules = is_array($content->modules) ? $content->modules : json_decode($content->modules, true) ?? [];
            $this->isEdit = true;
        } else {
            $this->modules = [
                ['type' => 'text', 'title' => 'Pengantar Materi', 'value' => '']
            ];
        }
    }

    public function addModule($type) {
        $this->modules[] = [
            'type' => $type,
            'title' => '',
            'value' => ''
        ];
    }

    public function removeModule($index) {
        // Hapus juga file temporari jika ada di index tersebut
        if (isset($this->tempFiles[$index])) {
            unset($this->tempFiles[$index]);
        }

        unset($this->modules[$index]);
        $this->modules = array_values($this->modules);

        // Reset tempFiles keys agar sinkron dengan modules
        $this->tempFiles = array_combine(
            array_keys($this->tempFiles),
            array_values($this->tempFiles)
        );
    }

    public function save() {
        $this->validate([
            'title' => 'required|min:5|max:255',
            'modules' => 'required|array|min:1',
            'modules.*.title' => 'required|string|max:100',
        ], [
            'modules.*.title.required' => 'Judul bagian wajib diisi.',
        ]);

        $finalModules = $this->modules;

        // PROSES UPLOAD FILE
        foreach ($this->tempFiles as $index => $file) {
            if ($file) {
                // 1. Hapus file lama dari storage jika mode edit
                if ($this->isEdit && !empty($this->modules[$index]['value']) && $this->modules[$index]['type'] === 'file') {
                    Storage::disk('public')->delete($this->modules[$index]['value']);
                }

                // 2. Simpan file baru
                $path = $file->store('modules/attachments', 'public');
                $finalModules[$index]['value'] = $path;
            }
        }

        try {
            if ($this->isEdit) {
                $this->content->update([
                    'title' => $this->title,
                    'modules' => $finalModules,
                ]);
                $message = 'Konten Materi Berhasil Diperbarui! 💎';
            } else {
                SubProgramContent::create([
                    'sub_program_id' => $this->subProgram->id,
                    'title' => $this->title,
                    'slug' => Str::slug($this->title) . '-' . Str::lower(Str::random(5)),
                    'modules' => $finalModules,
                    'order_position' => $this->subProgram->contents()->count() + 1,
                ]);
                $message = 'Konten Materi Berhasil Diterbitkan! 🚀';
            }

            session()->flash('success', $message);
            return redirect()->route('admin-program.subprogram.show', $this->subProgram->id);

        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }
}; ?>

<div class="max-w-6xl mx-auto pb-32 px-4">
    {{-- Notifikasi --}}
    @if (session()->has('error'))
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-[10px] font-black uppercase italic">
            {{ session('error') }}
        </div>
    @endif

    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-12 gap-6">
        <div class="space-y-2">
            <h2 class="text-4xl font-black uppercase italic tracking-tighter text-gray-900 leading-none">
                {{ $isEdit ? 'Update Content' : 'Content Builder' }}
            </h2>
            <p class="text-[11px] font-bold uppercase tracking-[0.4em] text-[#800000]">
                Subprogram: <span class="text-gray-400">{{ $subProgram->title }}</span>
            </p>
        </div>
        <div class="flex gap-4">
            <a href="{{ route('admin-program.subprogram.show', $subProgram->id) }}" wire:navigate class="px-8 py-4 bg-gray-100 text-gray-500 rounded-2xl font-black uppercase italic text-[10px] tracking-widest hover:bg-gray-200 transition-all">
                Batal
            </a>
            <button wire:click="save" wire:loading.attr="disabled" class="px-10 py-4 bg-black text-white rounded-2xl font-black uppercase italic text-[10px] tracking-[0.2em] hover:bg-[#800000] transition-all shadow-2xl shadow-black/20 active:scale-95 disabled:opacity-50">
                <span wire:loading.remove>{{ $isEdit ? 'Simpan Perubahan' : 'Terbitkan Materi' }}</span>
                <span wire:loading>Memproses...</span>
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
        {{-- Sidebar: Controls --}}
        <div class="lg:col-span-4">
            <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm sticky top-28 space-y-8">
                <div>
                    <label class="text-[10px] font-black uppercase italic text-gray-400 tracking-widest block mb-4">Informasi Utama</label>
                    <input type="text" wire:model="title" placeholder="Judul Materi..."
                           class="w-full bg-gray-50 border-none rounded-2xl p-5 text-sm font-bold focus:ring-2 focus:ring-[#800000]/20 transition-all shadow-inner text-gray-800">
                    @error('title') <span class="text-[9px] text-red-500 font-bold mt-2 block uppercase italic">{{ $message }}</span> @enderror
                </div>

                <div class="pt-6 border-t border-gray-50">
                    <p class="text-[10px] font-black uppercase text-gray-400 mb-5 tracking-widest italic">Sisipkan Modul Tambahan:</p>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach(['text' => 'Naskah/Teks', 'video' => 'Youtube', 'file' => 'Lampiran', 'link' => 'Link Luar'] as $key => $label)
                            <button wire:click="addModule('{{ $key }}')"
                                    class="flex flex-col items-center justify-center p-4 bg-gray-50 rounded-2xl border border-transparent hover:border-[#800000] hover:bg-white transition-all group">
                                <span class="text-[9px] font-black uppercase italic text-gray-900 group-hover:text-[#800000]">{{ $label }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Area --}}
        <div class="lg:col-span-8 space-y-8">
            @forelse($modules as $index => $module)
                <div class="bg-white rounded-[3rem] border border-gray-100 shadow-sm overflow-hidden transition-all hover:shadow-xl hover:shadow-gray-200/40" wire:key="module-{{ $index }}">
                    <div class="px-8 py-5 bg-gray-50/50 border-b border-gray-100 flex justify-between items-center">
                        <div class="flex items-center gap-4">
                            <span class="w-8 h-8 bg-black text-white text-[12px] font-black rounded-xl flex items-center justify-center italic shadow-lg shadow-black/20">{{ $index + 1 }}</span>
                            <span class="text-[10px] font-black uppercase tracking-widest text-[#800000] italic">
                                {{ $module['type'] }} Module
                            </span>
                        </div>
                        <button wire:click="removeModule({{ $index }})" wire:confirm="Hapus bagian ini?" class="p-2 text-gray-300 hover:text-red-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round"/></svg>
                        </button>
                    </div>

                    <div class="p-10 space-y-6">
                        {{-- Field Judul Bagian --}}
                        <div>
                            <label class="text-[8px] font-black text-gray-300 uppercase tracking-widest mb-2 block">Judul Bagian</label>
                            <input type="text" wire:model="modules.{{ $index }}.title" placeholder="Contoh: Video Tutorial atau Materi PDF"
                                   class="w-full border-none bg-gray-50/50 rounded-xl text-xs font-black p-4 focus:ring-1 focus:ring-[#800000]/20">
                            @error("modules.{$index}.title") <span class="text-[8px] text-red-500 font-bold italic mt-1">{{ $message }}</span> @enderror
                        </div>

                        {{-- Render Input Berdasarkan Type --}}
                        @if($module['type'] == 'text')
                            <textarea wire:model="modules.{{ $index }}.value" rows="6" placeholder="Tulis isi materi di sini..."
                                      class="w-full border-none bg-gray-50/50 rounded-xl text-xs font-medium p-5 focus:ring-1 focus:ring-gray-200 leading-relaxed"></textarea>

                        @elseif($module['type'] == 'video')
                            <div class="space-y-2">
                                <input type="text" wire:model="modules.{{ $index }}.value" placeholder="Tempel URL YouTube di sini..."
                                       class="w-full border-none bg-gray-50/50 rounded-xl text-xs font-bold p-4">
                                @if(!empty($module['value']))
                                    <p class="text-[9px] text-blue-500 font-bold italic">Preview Link: <a href="{{ $module['value'] }}" target="_blank" class="underline italic">{{ $module['value'] }}</a></p>
                                @endif
                            </div>

                        @elseif($module['type'] == 'file')
                            <div class="space-y-3">
                                <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-100 border-dashed rounded-2xl cursor-pointer bg-gray-50/30 hover:bg-gray-50 transition-all relative overflow-hidden">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6 text-center px-6">
                                        <svg class="w-6 h-6 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>

                                        <p class="text-[10px] font-black uppercase italic">
                                            @if(isset($tempFiles[$index]))
                                                <span class="text-[#800000]">SIAP GANTI: {{ $tempFiles[$index]->getClientOriginalName() }}</span>
                                            @elseif(!empty($module['value']))
                                                <span class="text-green-600 italic">FILE EKSIS: {{ basename($module['value']) }}</span>
                                                <span class="block text-[8px] text-gray-400 mt-1">(Klik untuk mengganti file)</span>
                                            @else
                                                Klik untuk Upload Lampiran
                                            @endif
                                        </p>
                                    </div>
                                    <input type="file" wire:model="tempFiles.{{ $index }}" class="hidden" />

                                    {{-- Loading State --}}
                                    <div wire:loading wire:target="tempFiles.{{ $index }}" class="absolute inset-0 bg-white/80 flex items-center justify-center">
                                        <span class="text-[8px] font-black animate-pulse">UPLOADING...</span>
                                    </div>
                                </label>

                                @if(!empty($module['value']) && !isset($tempFiles[$index]))
                                    <a href="{{ Storage::url($module['value']) }}" target="_blank" class="text-[9px] font-black text-gray-400 hover:text-black uppercase italic tracking-widest flex items-center gap-2">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        Lihat File Saat Ini
                                    </a>
                                @endif
                            </div>

                        @elseif($module['type'] == 'link')
                            <input type="text" wire:model="modules.{{ $index }}.value" placeholder="https://website-luar.com/sumber"
                                   class="w-full border-none bg-gray-50/50 rounded-xl text-xs font-bold p-4 focus:ring-1 focus:ring-gray-200">
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-gray-50 rounded-[3rem] border-2 border-dashed border-gray-200 py-32 text-center">
                    <p class="text-xs font-black text-gray-300 uppercase tracking-[0.5em] italic">Belum ada modul ditambahkan</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
