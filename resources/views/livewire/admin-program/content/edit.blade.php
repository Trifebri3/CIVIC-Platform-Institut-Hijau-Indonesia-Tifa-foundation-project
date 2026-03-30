<?php

use App\Models\{SubProgram, Program};
use Livewire\Volt\Component;
use Livewire\WithFileUploads; // Tambahkan ini Bos
use Illuminate\Support\Facades\Storage;

new class extends Component {
    use WithFileUploads; // Gunakan trait upload

    public SubProgram $subProgram;
    public $title, $order, $deadline, $content_data = [];
    public $new_files = []; // Untuk menampung upload file baru saat edit

    public function mount(SubProgram $subProgram)
    {
        $this->subProgram = $subProgram;
        $this->title = $subProgram->title;
        $this->order = $subProgram->order;
        $this->deadline = $subProgram->deadline ? $subProgram->deadline->format('Y-m-d\TH:i') : null;
        $this->content_data = $subProgram->content_data;
    }

    public function update()
    {
        $this->validate([
            'title' => 'required|min:3',
            'deadline' => 'required',
            'order' => 'required|integer',
        ]);

        $finalData = $this->content_data;

        // Cek jika ada file baru yang diupload saat edit
        foreach ($this->new_files as $key => $file) {
            if ($file) {
                // Hapus file lama jika ada (optional tapi bagus untuk hemat storage)
                if (isset($finalData[$key]) && Storage::disk('public')->exists($finalData[$key])) {
                    Storage::disk('public')->delete($finalData[$key]);
                }

                // Simpan file baru
                $path = $file->store('uploads/content', 'public');
                $finalData[$key] = $path;
            }
        }

        $this->subProgram->update([
            'title' => $this->title,
            'order' => $this->order,
            'deadline' => $this->deadline,
            'content_data' => $finalData,
        ]);

        session()->flash('success', 'Konten Berhasil Diperbarui!');
        return redirect()->route('admin-program.content.index');
    }
}; ?>

<div class="max-w-5xl mx-auto pb-24 antialiased">
    <div class="bg-white rounded-[3.5rem] p-12 shadow-2xl shadow-gray-200/50 border border-gray-50">

        <div class="flex justify-between items-start mb-12">
            <div>
                <h2 class="text-4xl font-black text-gray-900 uppercase italic tracking-tighter leading-none">Edit Entity</h2>
                <p class="text-[10px] text-[#800000] font-black uppercase tracking-[0.4em] mt-3 italic">Updating: {{ $subProgram->slug }}</p>
            </div>
            <a href="{{ route('admin-program.content.index') }}" wire:navigate class="p-4 bg-gray-50 rounded-2xl hover:bg-black group transition-all">
                <svg class="w-5 h-5 text-gray-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M10 19l-7-7m0 0l7-7m-7 7h18" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10">
            <div class="space-y-3">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-4">Headline Title</label>
                <input type="text" wire:model="title" class="w-full rounded-[1.5rem] border-none bg-gray-50 p-5 font-black text-xl focus:ring-2 focus:ring-[#800000]/20 uppercase italic tracking-tighter">
            </div>

            <div class="space-y-3">
                <label class="text-[10px] font-black text-[#800000] uppercase tracking-widest ml-4 flex items-center gap-2">
                    <svg class="w-3 h-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0114 0z" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Adjust Deadline
                </label>
                <input type="datetime-local" wire:model="deadline" class="w-full rounded-[1.5rem] border-none bg-red-50/50 p-5 font-black text-sm focus:ring-2 focus:ring-[#800000]/20 text-gray-700">
            </div>
        </div>

        <div class="h-[1px] w-full bg-gray-100 my-12"></div>

        {{-- Dynamic Fields --}}
        <div class="space-y-8">
            <h3 class="text-[11px] font-black uppercase text-gray-400 tracking-[0.3em] px-4 italic">Content Architecture: {{ $subProgram->template->name }}</h3>

            <div class="grid grid-cols-1 gap-6">
                @foreach($subProgram->template->fields_schema as $field)
                    <div class="bg-gray-50/30 p-8 rounded-[2.5rem] border border-gray-50">
                        <label class="text-[9px] font-black text-gray-500 uppercase tracking-widest mb-4 block">{{ $field['label'] }}</label>

                        @if($field['type'] == 'textarea')
                            <textarea wire:model="content_data.{{ $field['name'] }}" rows="5" class="w-full rounded-[1.5rem] border-none bg-white font-bold text-sm focus:ring-4 focus:ring-[#800000]/5 shadow-sm p-6"></textarea>

                        @elseif($field['type'] == 'file' || $field['type'] == 'image')
                            <div class="flex items-center gap-8">
                                {{-- Preview Current / New Asset --}}
                                <div class="w-24 h-24 rounded-3xl bg-white border-2 border-dashed border-gray-100 flex items-center justify-center overflow-hidden shadow-inner">
                                    @php
                                        $newFile = $new_files[$field['name']] ?? null;
                                        $oldPath = $content_data[$field['name']] ?? null;
                                    @endphp

                                    @if($newFile && in_array($newFile->guessExtension(), ['png', 'jpg', 'jpeg', 'webp']))
                                        <img src="{{ $newFile->temporaryUrl() }}" class="w-full h-full object-cover">
                                    @elseif($oldPath && Str::contains($oldPath, ['uploads/']))
                                        @if(in_array(pathinfo($oldPath, PATHINFO_EXTENSION), ['png', 'jpg', 'jpeg', 'webp']))
                                            <img src="{{ Storage::url($oldPath) }}" class="w-full h-full object-cover">
                                        @else
                                            <svg class="w-8 h-8 text-[#800000]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" stroke-width="2.5"/></svg>
                                        @endif
                                    @else
                                        <svg class="w-8 h-8 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2"/></svg>
                                    @endif
                                </div>

                                <div class="flex-1 space-y-3">
                                    <input type="file" wire:model="new_files.{{ $field['name'] }}"
                                           class="block w-full text-[10px] text-gray-400 file:mr-6 file:py-3 file:px-8 file:rounded-full file:border-0 file:text-[9px] file:font-black file:bg-black file:text-white hover:file:bg-[#800000] cursor-pointer shadow-lg transition-all">
                                    <p class="text-[8px] font-bold text-gray-400 italic">Leave empty to keep current file</p>
                                </div>
                            </div>

                        @else
                            <input type="{{ $field['type'] }}" wire:model="content_data.{{ $field['name'] }}" class="w-full rounded-[1.2rem] border-none bg-white font-black text-sm focus:ring-4 focus:ring-[#800000]/5 shadow-sm p-4 px-6">
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <div class="mt-16 flex items-center justify-between border-t border-gray-50 pt-10">
            <div class="flex items-center gap-4">
                <input type="number" wire:model="order" class="w-20 rounded-xl border-none bg-gray-50 p-4 font-black text-center text-sm focus:ring-2 focus:ring-[#800000]/10">
                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest italic">Order Index</label>
            </div>

            <button wire:click="update" wire:loading.attr="disabled" class="bg-black text-white px-16 py-6 rounded-[2rem] font-black text-[11px] uppercase tracking-[0.4em] italic shadow-2xl hover:bg-[#800000] transition-all disabled:opacity-50">
                <span wire:loading.remove wire:target="update">Push Updates</span>
                <span wire:loading wire:target="update">Syncing Database...</span>
            </button>
        </div>
    </div>
</div>
