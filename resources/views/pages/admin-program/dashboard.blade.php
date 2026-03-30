@component('pages.admin-program.layouts.app')
    <div class="py-12 bg-[#fafafa] min-h-screen">
        <div class="max-w-7xl mx-auto px-4">
            {{-- Header --}}
            <div class="mb-12">
                <h1 class="text-4xl font-black text-gray-900 uppercase italic tracking-tighter leading-none">
                    Program Manager <span class="text-[#800000]">.</span>
                </h1>
                <p class="text-[10px] text-gray-400 font-black uppercase tracking-[0.4em] mt-3">
                    Selamat datang, {{ auth()->user()->name }}
                </p>
            </div>

            {{-- Stats / Program Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @forelse($programs as $program)
                    <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-500 group">
                        <div class="flex justify-between items-start mb-6">
                            <span class="px-3 py-1 bg-red-50 text-[#800000] text-[8px] font-black uppercase rounded-full">
                                Managed Program
                            </span>
                            <span class="text-[10px] font-black text-gray-200">#{{ $loop->iteration }}</span>
                        </div>

                        <h3 class="text-xl font-black text-gray-800 uppercase italic mb-2 group-hover:text-[#800000] transition-colors">
                            {{ $program->name }}
                        </h3>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-6">
                            {{ $program->sub_programs_count }} Total Content Units
                        </p>

                        <div class="pt-6 border-t border-gray-50 flex justify-between items-center">
                            <a href="#"
                               class="text-[9px] font-black text-gray-900 uppercase tracking-widest hover:underline">
                                Manage Content &rarr;
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-20 bg-white rounded-[3rem] border-2 border-dashed border-gray-100 text-center">
                        <p class="text-[10px] font-black text-gray-300 uppercase tracking-[0.5em]">Belum ada program yang didelegasikan untuk Anda.</p>
                    </div>
                @endforelse
            </div>
        </div>


    </div>
@endcomponent

