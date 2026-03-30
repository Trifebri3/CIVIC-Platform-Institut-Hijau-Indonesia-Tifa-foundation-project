<footer class="bg-white border-t border-gray-100 pt-24 pb-12 overflow-hidden">
    <div class="max-w-7xl mx-auto px-6 lg:px-12">
        <div class="flex flex-col md:flex-row justify-between items-center gap-12">

            {{-- Branding Section --}}
            <div class="flex flex-col items-center md:items-start">
                <div class="flex items-center gap-4 mb-4">
                    <img src="{{ asset('images/logoo.png') }}" alt="CIVIC Logo" class="h-14 w-auto drop-shadow-sm transition-transform hover:scale-105 duration-500">
                    <div class="h-8 w-[1px] bg-gray-100 hidden md:block"></div>
                    <div class="hidden md:block">
                        <p class="text-[8px] font-black text-[#800000] uppercase tracking-[0.3em] leading-none">Official Platform</p>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Civic Education</p>
                    </div>
                </div>

                {{-- Tagline / Mission Statement --}}
                <div class="max-w-sm text-center md:text-left">
                    <p class="text-[10px] font-black text-slate-800 uppercase tracking-[0.3em] leading-relaxed italic">
                        Cultivating Sustainability, <br class="hidden md:block">
                        <span class="text-[#800000]">Inspiring Future Leaders.</span>
                    </p>
                    <p class="mt-4 text-[9px] font-black text-gray-300 uppercase tracking-[0.25em] leading-relaxed">
                        IT Support Tim Institut Hijau Indonesia <br class="hidden md:block">
                        <span class="text-[#800000]/30">—</span> Civic Education Platform
                    </p>
                </div>
            </div>

            {{-- Links --}}
            <div class="flex items-center gap-10">
                <a href="#" class="text-[9px] font-black uppercase tracking-[0.2em] text-gray-400 hover:text-[#800000] transition-colors">Privacy</a>
                <a href="#" class="text-[9px] font-black uppercase tracking-[0.2em] text-gray-400 hover:text-[#800000] transition-colors">Terms</a>
                <a href="#" class="text-[9px] font-black uppercase tracking-[0.2em] text-gray-400 hover:text-[#800000] transition-colors">Contact</a>
            </div>
        </div>

        {{-- Bottom Bar --}}
        <div class="mt-16 pt-8 border-t border-gray-50 flex flex-col items-center gap-4">
            <p class="text-[8px] font-black text-gray-300 uppercase tracking-[0.5em] italic">
                &copy; {{ date('Y') }} CIVIC Platform Management Console
            </p>

            {{-- Dev Watermark (Nyaris tidak terlihat) --}}
            <a href="#" class="text-[7px] font-bold uppercase tracking-[0.8em] text-gray-100 hover:text-gray-200 transition-all duration-1000 cursor-default select-none">
                Dev by Teriyaki
            </a>
        </div>
    </div>
</footer>
