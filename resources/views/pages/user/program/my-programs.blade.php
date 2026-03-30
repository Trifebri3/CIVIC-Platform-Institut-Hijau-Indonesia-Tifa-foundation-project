@component('pages.user.layouts.app')
    <x-slot name="title">Program Saya</x-slot>

    <div class="max-w-4xl mx-auto px-4 py-8 md:py-12 pb-32"> {{-- Padding bottom lebih besar buat mobile menu --}}

        {{-- Header: Compact & Bold --}}
        <div class="mb-10 px-2 flex justify-between items-end">
            <div>
                <h1 class="text-3xl md:text-5xl font-black text-gray-900 uppercase tracking-tighter italic leading-none">Learning</h1>
                <p class="text-[10px] md:text-xs text-[#800000] font-black uppercase tracking-[0.3em] mt-1">My Active Journeys</p>
            </div>
            <div class="hidden md:block text-right">
                <span class="text-[3rem] font-black text-gray-100 leading-none select-none italic">{{ Auth::user()->enrolledPrograms->count() }}</span>
            </div>
        </div>

        {{-- Grid: Serasa List di Aplikasi Mobile --}}
        <div class="grid grid-cols-1 gap-5">
            @forelse(Auth::user()->enrolledPrograms as $program)
                <a href="{{ route('user.programs.show', $program->slug) }}"
                   class="group relative bg-white rounded-[2.5rem] overflow-hidden border border-gray-100 shadow-sm hover:shadow-2xl transition-all duration-500 flex flex-row items-center h-28 md:h-36">

                    {{-- Thumbnail Kiri: Hemat Ruang & Berkelas --}}
                    <div class="h-full w-28 md:w-44 flex-shrink-0 relative overflow-hidden bg-gray-900">
                        @if($program->banner)
                            <img src="{{ asset('storage/'.$program->banner) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 opacity-80 group-hover:opacity-100">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-[#800000] to-black flex items-center justify-center italic">
                                <span class="text-white/20 font-black text-xl tracking-tighter uppercase">YLE</span>
                            </div>
                        @endif

                        {{-- Badge Status di Atas Gambar --}}
                        <div class="absolute top-3 left-3">
                            <div class="h-2 w-2 rounded-full bg-green-500 shadow-[0_0_8px_#22c55e] animate-pulse"></div>
                        </div>
                    </div>

                    {{-- Konten Kanan: Tipografi Padat --}}
                    <div class="flex-1 px-5 md:px-8 py-4 flex flex-col justify-between h-full">
                        <div>
                            <p class="text-[8px] md:text-[9px] font-black text-[#800000] uppercase tracking-widest mb-1 italic opacity-60">Enrolled Program</p>
                            <h3 class="text-sm md:text-xl font-black text-gray-800 uppercase tracking-tighter leading-tight line-clamp-1 group-hover:text-[#800000] transition-colors">
                                {{ $program->name }}
                            </h3>
                        </div>

                        <div class="flex items-end justify-between border-t border-gray-50 pt-3">
                            <div class="space-y-0.5">
                                <p class="text-[9px] font-black text-gray-900 tracking-tighter uppercase">{{ $program->pivot->registration_number }}</p>
                                <p class="text-[8px] text-gray-400 font-bold uppercase tracking-widest">{{ $program->pivot->enrolled_at->format('M d, Y') }}</p>
                            </div>

                            {{-- Arrow Icon: Mobile-Friendly UI --}}
                            <div class="bg-gray-50 group-hover:bg-[#800000] p-2 md:p-3 rounded-xl md:rounded-2xl transition-all">
                                <svg class="w-4 h-4 md:w-5 md:h-5 text-gray-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                {{-- Empty State Serasa App --}}
                <div class="flex flex-col items-center justify-center py-20 px-6 text-center bg-white rounded-[3rem] border-2 border-dashed border-gray-100">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-10 h-10 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    </div>
                    <h4 class="text-sm font-black text-gray-400 uppercase tracking-[0.3em]">No Active Journey</h4>
                    <p class="text-[10px] text-gray-300 font-bold mt-2 uppercase tracking-widest">Waktunya memulai langkah pertamamu.</p>
                    <a href="/" class="mt-8 bg-black text-white px-8 py-4 rounded-2xl text-[10px] font-black uppercase tracking-[0.3em] shadow-xl active:scale-95 transition-all">Explore Programs</a>
                </div>
            @endforelse
        </div>
    </div>

    {{-- CSS Inline for extra App-Feel --}}
    <style>
        body { background-color: #fafafa; }
        .line-clamp-1 {
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
@endcomponent
