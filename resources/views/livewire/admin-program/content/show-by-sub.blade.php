<?php
// resources/views/livewire/admin-program/content/edit.blade.php

use App\Models\{SubProgram, Program};
use Livewire\Volt\Component;

new class extends Component {
    public SubProgram $subProgram;
    public $title, $order, $content_data = [];

    public function mount(SubProgram $subProgram)
    {
        $this->subProgram = $subProgram->load('program');
        $this->title = $subProgram->title;
        $this->order = $subProgram->order;

        // Parsing data content_data (JSON/Array)
        $this->content_data = is_array($subProgram->content_data)
            ? $subProgram->content_data
            : (json_decode($subProgram->content_data, true) ?? []);
    }
}; ?>

<div class="max-w-5xl mx-auto px-4 py-12 pb-32">
    <div class="mb-16 border-b-4 border-black pb-10">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div class="space-y-2">
                <div class="flex items-center gap-3">
                    <span class="bg-[#800000] text-white text-[9px] font-black px-3 py-1 rounded-sm uppercase italic tracking-widest">
                        {{ $subProgram->program->name ?? 'SYSTEM ASSET' }}
                    </span>
                    <span class="text-gray-300 text-[10px] font-black uppercase italic tracking-widest">
                        / Sub-Program Identity
                    </span>
                </div>
                <h1 class="text-6xl font-black text-gray-900 uppercase italic tracking-tighter leading-none">
                    {{ $title }}
                </h1>
                <div class="flex items-center gap-6 mt-4">
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-[0.4em] italic">
                        Sub-ID: #{{ $subProgram->id }}
                    </p>
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-[0.4em] italic border-l pl-6 border-gray-200">
                        Global Order: {{ $order }}
                    </p>
                </div>
            </div>

            <div class="flex gap-3">
                <a href="{{ route('admin-program.content.index') }}" wire:navigate
                   class="bg-black text-white px-8 py-4 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-[#800000] transition-all italic shadow-xl shadow-black/10">
                    Back to Vault
                </a>
            </div>
        </div>
    </div>

    <div class="space-y-12">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-xl font-black text-gray-900 uppercase italic tracking-widest border-l-4 border-[#800000] pl-4">
                Sub-Program Content Data
            </h2>
            <span class="text-[10px] font-black text-gray-300 uppercase italic">
                Raw Content Analysis
            </span>
        </div>

        {{-- Menampilkan data dari field content_data --}}
        @forelse($content_data as $key => $value)
            <div class="relative pl-12 md:pl-20 group">
                <div class="absolute left-0 top-0 bottom-0 w-[2px] bg-gray-100 group-last:h-8"></div>
                <div class="absolute left-[-5px] top-0 w-3 h-3 bg-black rounded-full border-4 border-white shadow-sm transition-transform group-hover:scale-150 group-hover:bg-[#800000]"></div>

                <div class="bg-white rounded-[2rem] p-8 md:p-10 border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-500">
                    <div class="flex flex-col md:flex-row gap-8">
                        <div class="md:w-32 flex-shrink-0">
                            <span class="text-[10px] font-black text-[#800000] uppercase italic tracking-[0.2em] block mb-1">
                                DATA KEY
                            </span>
                            <span class="text-[11px] font-bold text-gray-900 uppercase italic block break-all">
                                {{ str_replace('_', ' ', $key) }}
                            </span>
                        </div>

                        <div class="flex-1 space-y-4">
                            <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100/50 min-h-[60px]">
                                @if(is_array($value))
                                    <pre class="text-[10px] text-gray-600 font-mono overflow-x-auto">{{ json_encode($value, JSON_PRETTY_PRINT) }}</pre>
                                @else
                                    <p class="text-gray-600 text-sm leading-relaxed whitespace-pre-line font-medium italic">
                                        {{ $value ?: 'No Data Available' }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="py-24 bg-gray-50 rounded-[3rem] border-4 border-dashed border-gray-100 text-center">
                <p class="text-gray-300 uppercase font-black italic tracking-[0.5em] text-xs">
                    This Sub-Program has no additional content data.
                </p>
            </div>
        @endforelse
    </div>

    <div class="mt-20 pt-10 border-t border-gray-100 flex flex-col items-center gap-4">
        <div class="text-[9px] font-black text-gray-300 uppercase tracking-[0.5em] italic">
            Last Updated System Sync: {{ now()->format('Y-m-d H:i') }}
        </div>
    </div>
</div>
