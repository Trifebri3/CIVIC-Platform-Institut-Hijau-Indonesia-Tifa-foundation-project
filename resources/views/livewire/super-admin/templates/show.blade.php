<?php

use App\Models\SubProgram;
use Livewire\Volt\Component;

new class extends Component {
    public SubProgram $subProgram;

    public function mount(SubProgram $subProgram)
    {
        // Load relasi agar tidak n+1 query
        $this->subProgram = $subProgram->load(['template', 'program']);
    }
}; ?>

<div class="max-w-5xl mx-auto pb-20 px-4">
    {{-- Breadcrumb Luxury --}}
    <nav class="flex items-center gap-3 mb-8 px-2">
        <a href="/" class="text-[9px] font-black text-gray-400 uppercase tracking-widest hover:text-[#800000]">Dashboard</a>
        <span class="text-gray-300">/</span>
        <a href="#" class="text-[9px] font-black text-gray-400 uppercase tracking-widest hover:text-[#800000]">{{ $subProgram->program->name }}</a>
        <span class="text-gray-300">/</span>
        <span class="text-[9px] font-black text-[#800000] uppercase tracking-widest italic">{{ $subProgram->template->name }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">

        {{-- Sisi Kiri: Main Content Area --}}
        <div class="lg:col-span-2 space-y-8">
            {{-- Header Card --}}
            <div class="bg-white rounded-[3rem] p-10 shadow-sm border border-gray-100 relative overflow-hidden">
                <div class="absolute top-0 right-0 p-8 opacity-5">
                    <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14.5v-9l6 4.5-6 4.5z"/></svg>
                </div>

                <span class="inline-block bg-red-50 text-[#800000] text-[9px] font-black px-4 py-1.5 rounded-full uppercase tracking-[0.2em] mb-4">
                    {{ $subProgram->template->name }} Entity
                </span>
                <h1 class="text-4xl font-black text-gray-900 uppercase tracking-tighter italic leading-tight mb-4">
                    {{ $subProgram->title }}
                </h1>
                <p class="text-gray-400 font-bold text-xs uppercase tracking-widest">
                    ID: {{ $subProgram->slug }}
                </p>
            </div>

            {{-- DYNAMIC CONTENT RENDERER --}}
            <div class="space-y-6">
                @foreach($subProgram->template->fields_schema as $field)
                    @php
                        $value = $subProgram->getContent($field['name']);
                    @endphp

                    @if($value) {{-- Hanya tampilkan jika ada isinya --}}
                        <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-50 group hover:border-red-100 transition-all">
                            <h4 class="text-[10px] font-black text-gray-300 uppercase tracking-[0.3em] mb-4 flex items-center gap-2">
                                <div class="w-1.5 h-1.5 rounded-full bg-[#800000]"></div>
                                {{ $field['label'] }}
                            </h4>

                            {{-- Render Berdasarkan Tipe Field --}}
                            <div class="prose prose-red max-w-none text-gray-700 font-bold leading-relaxed">
                                @if($field['type'] === 'text' || $field['type'] === 'number')
                                    <p class="text-lg md:text-xl tracking-tight">{{ $value }}</p>

                                @elseif($field['type'] === 'textarea')
                                    <div class="text-sm md:text-base opacity-80 leading-loose">
                                        {!! nl2br(e($value)) !!}
                                    </div>

                                @elseif($field['type'] === 'url')
                                    @if(Str::contains($value, ['youtube.com', 'youtu.be']))
                                        {{-- YouTube Embed --}}
                                        <div class="aspect-video rounded-[2rem] overflow-hidden shadow-2xl border-4 border-white mt-4">
                                            @php $videoId = Str::afterLast($value, '/'); if(Str::contains($value, 'watch?v=')) $videoId = Str::afterLast($value, 'v='); @endphp
                                            <iframe class="w-full h-full" src="https://www.youtube.com/embed/{{ $videoId }}" frameborder="0" allowfullscreen></iframe>
                                        </div>
                                    @else
                                        {{-- Regular Link --}}
                                        <a href="{{ $value }}" target="_blank" class="inline-flex items-center gap-3 bg-gray-900 text-white px-8 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-[#800000] transition-all">
                                            Open External Link
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                        </a>
                                    @endif

                                @elseif($field['type'] === 'file')
                                    <div class="flex items-center justify-between bg-gray-50 p-6 rounded-3xl border border-gray-100">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center shadow-sm">
                                                <svg class="w-6 h-6 text-[#800000]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                            </div>
                                            <div>
                                                <p class="text-[10px] font-black text-gray-900 uppercase">Document Asset</p>
                                                <p class="text-[8px] font-bold text-gray-400 uppercase italic">Ready to download</p>
                                            </div>
                                        </div>
                                        <a href="{{ asset('storage/'.$value) }}" download class="bg-white p-4 rounded-2xl text-[#800000] hover:bg-[#800000] hover:text-white transition-all shadow-sm">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                        </a>
                                    </div>

                                @elseif($field['type'] === 'date')
                                    <p class="text-xl font-black text-gray-800 italic uppercase">{{ \Carbon\Carbon::parse($value)->format('d F Y, H:i') }}</p>
                                @endif
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>

        {{-- Sisi Kanan: Meta Data & Action --}}
        <div class="space-y-6">
            <div class="bg-white rounded-[3rem] p-10 shadow-sm border border-gray-100 sticky top-10">
                <div class="text-center mb-8 pb-8 border-b border-gray-50">
                    <p class="text-[10px] font-black text-gray-300 uppercase tracking-widest mb-2">Program Parent</p>
                    <h5 class="text-sm font-black text-gray-800 uppercase tracking-tighter">{{ $subProgram->program->name }}</h5>
                </div>

                <div class="space-y-4">
                    <a href="#" class="w-full bg-[#800000] text-white py-5 rounded-2xl font-black text-[10px] uppercase tracking-[0.3em] flex items-center justify-center gap-3 shadow-xl hover:bg-black transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                        Edit Content
                    </a>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-gray-50 p-4 rounded-2xl text-center">
                            <p class="text-[8px] font-black text-gray-300 uppercase mb-1">Status</p>
                            <p class="text-[10px] font-black text-green-600 uppercase italic">{{ $subProgram->status }}</p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-2xl text-center">
                            <p class="text-[8px] font-black text-gray-300 uppercase mb-1">Order</p>
                            <p class="text-[10px] font-black text-gray-900 uppercase italic">Seq-{{ $subProgram->order }}</p>
                        </div>
                    </div>
                </div>

                <div class="mt-8 pt-8 border-t border-gray-50">
                    <p class="text-[9px] font-bold text-gray-400 text-center uppercase tracking-widest italic">
                        Created: {{ $subProgram->created_at->format('d/m/Y') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
