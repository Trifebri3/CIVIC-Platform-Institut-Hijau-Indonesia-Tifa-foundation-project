<?php
// resources/views/livewire/user/kelas/show.blade.php

use App\Models\{SubProgram, SubProgramContent};
use Livewire\Volt\Component;
use Carbon\Carbon;

new class extends Component {
    public $subProgram;
    public $content;
    public $modules = [];
    public $completedModules = [];
    public $expandedModule = null;

public function mount(SubProgram $subProgram) {
        $this->subProgram = $subProgram;
        // Mengambil content beserta subProgram untuk akses field deadline
        $this->content = SubProgramContent::with('subProgram')
            ->where('sub_program_id', $subProgram->id)
            ->first();

        if ($this->content) {
            $data = is_array($this->content->modules) ? $this->content->modules : (json_decode($this->content->modules, true) ?? []);
            $this->modules = $data;
            $this->completedModules = session()->get("progress_{$this->subProgram->id}", []);
        }
    }

    public function toggleExpand($index) {
        $this->expandedModule = ($this->expandedModule === $index) ? null : $index;
    }

    public function markAsComplete($contentId)
{
    auth()->user()->progress()->syncWithoutDetaching([$contentId => ['completed_at' => now()]]);
    session()->flash('success', 'Materi berhasil diselesaikan!');
}


    public function toggleComplete($index) {
        if (in_array($index, $this->completedModules)) {
            $this->completedModules = array_diff($this->completedModules, [$index]);
        } else {
            $this->completedModules[] = $index;
        }
        session()->put("progress_{$this->subProgram->id}", $this->completedModules);
    }

    // Helper untuk konversi link YouTube ke Embed
    public function getEmbedUrl($url) {
        if (preg_match('/(youtube.com|youtu.be)\/(watch\?v=|embed\/|v\/|.+\?v=)?([^&=%\?]{11})/', $url, $match)) {
            // Kita tambah rel=0 dan modestbranding=1 agar bersih
            return "https://www.youtube.com/embed/{$match[3]}?rel=0&modestbranding=1&showinfo=0";
        }
        return $url;
    }
}; ?>
<div class="max-w-5xl mx-auto px-4 py-12 pb-32 antialiased">

    @if($content)
        {{-- Header Tetap Sama --}}
        <div class="mb-12 border-b-2 border-black/5 pb-8">
            <h1 class="text-5xl font-black text-gray-900 uppercase italic tracking-tighter leading-none mb-4">
                {{ $content->title }}
            </h1>
            <div class="flex items-center gap-4">
                <span class="text-[10px] font-black text-[#800000] uppercase tracking-[0.3em]">Module Index</span>
                <div class="flex-1 h-[1px] bg-gray-100"></div>
            </div>
        </div>

        <div class="space-y-4">
            @foreach($modules as $index => $m)
                @php
                    $isDone = in_array($index, $completedModules);
                    $isOpen = $expandedModule === $index;
                    $type = strtolower($m['type'] ?? 'text');
                @endphp

                <div class="group border {{ $isOpen ? 'border-black shadow-2xl' : 'border-gray-100 shadow-sm' }} bg-white rounded-[2rem] overflow-hidden transition-all duration-500">

                    {{-- Header Modul (Click to Toggle) --}}
                    <div wire:click="toggleExpand({{ $index }})"
                         class="flex items-center justify-between p-6 cursor-pointer hover:bg-gray-50 transition-colors">
                        <div class="flex items-center gap-6">
                            <div class="w-12 h-12 rounded-2xl flex items-center justify-center font-black text-sm {{ $isDone ? 'bg-green-500 text-white' : 'bg-gray-100 text-gray-400' }}">
                                @if($isDone)
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                                @else
                                    {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                                @endif
                            </div>
                            <div>
                                <span class="text-[8px] font-black uppercase tracking-widest {{ $isDone ? 'text-green-500' : 'text-[#800000]' }}">{{ $type }}</span>
                                <h3 class="text-lg font-black text-gray-900 uppercase italic leading-none">{{ $m['title'] }}</h3>
                            </div>
                        </div>


                        <div class="transform transition-transform duration-300 {{ $isOpen ? 'rotate-180' : '' }}">
                            <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7" /></svg>
                        </div>
                    </div>

                    {{-- Content Body (Expandable) --}}
                    @if($isOpen)
                        <div class="p-8 border-t border-gray-50 bg-[#fafafa]/50 animate-fadeIn">

                            {{-- 1. JIKA TYPE VIDEO/YOUTUBE --}}
                            @if($type == 'video' || $type == 'youtube' || str_contains($m['value'], 'youtube.com'))
                                <div class="aspect-video rounded-3xl overflow-hidden shadow-lg border-4 border-white mb-8 bg-black">
                                    <iframe class="w-full h-full"
                                            src="{{ $this->getEmbedUrl($m['value']) }}"
                                            frameborder="0"
                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                            allowfullscreen>
                                    </iframe>
                                </div>

                            {{-- 2. JIKA TYPE FILE --}}
                            @elseif($type == 'file' || str_contains($m['value'], '.pdf'))
                                <div class="flex items-center gap-6 bg-white p-6 rounded-3xl border border-gray-100 mb-8 group/file hover:border-blue-500 transition-all">
                                    <div class="w-16 h-16 bg-blue-50 text-blue-500 rounded-2xl flex items-center justify-center group-hover/file:bg-blue-500 group-hover/file:text-white transition-all">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-xs font-black uppercase text-gray-400 tracking-widest mb-1">Attachment</p>
                                        <p class="text-sm font-bold text-gray-900 italic">Dokumen Materi Pelajaran</p>
                                    </div>
                                    <a href="{{ asset('storage/' . $m['value']) }}" target="_blank" class="px-6 py-3 bg-black text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-blue-600 transition-all">Open File</a>
                                </div>

                            {{-- 3. JIKA TYPE LINK --}}
                            @elseif($type == 'link' || str_starts_with($m['value'], 'http'))
                                <a href="{{ $m['value'] }}" target="_blank" class="flex items-center justify-between bg-white p-6 rounded-3xl border border-gray-100 mb-8 hover:bg-gray-50 transition-all">
                                    <div class="flex items-center gap-4">
                                        <div class="text-gray-400">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101" stroke-width="2"/><path d="M10.172 13.828a4 4 0 015.656 0l4-4a4 4 0 11-5.656-5.656l-1.102 1.101" stroke-width="2"/></svg>
                                        </div>
                                        <span class="text-sm font-bold text-blue-600 italic underline">{{ $m['value'] }}</span>
                                    </div>
                                    <span class="text-[9px] font-black uppercase text-gray-300 tracking-widest italic">External Link</span>
                                </a>

                            {{-- 4. DEFAULT TEXT --}}
                            @else
                                <div class="prose prose-sm max-w-none text-gray-600 font-medium italic leading-relaxed mb-8 px-2">
                                    {!! nl2br(e($m['value'])) !!}
                                </div>
                            @endif

                            {{-- Action: Mark as Done --}}
                            <div class="flex justify-end pt-4 border-t border-gray-100">
                                <button wire:click.stop="toggleComplete({{ $index }})"
                                        class="flex items-center gap-3 px-6 py-3 rounded-2xl transition-all {{ $isDone ? 'bg-green-500 text-white' : 'bg-gray-900 text-white hover:bg-[#800000]' }}">
                                    <span class="text-[10px] font-black uppercase tracking-widest">
                                        {{ $isDone ? 'Done' : 'Mark Lesson as Done' }}
                                    </span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
    <style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fadeIn { animation: fadeIn 0.4s ease-out forwards; }
</style>

</div>

