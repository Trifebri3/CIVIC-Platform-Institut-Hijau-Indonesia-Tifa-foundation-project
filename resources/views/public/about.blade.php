@component('public.layouts.appumum') {{-- Atau gunakan layout publik Bos yang standar --}}

<div class="min-h-screen bg-white">
    <div class="relative bg-black py-24 px-8 overflow-hidden">
        <div class="absolute top-0 right-0 opacity-10">
            <span class="text-[20rem] font-black italic tracking-tighter text-white">IHI</span>
        </div>

        <div class="max-w-7xl mx-auto relative z-10">
            <span class="inline-block px-4 py-2 bg-[#800000] text-white text-[10px] font-black uppercase italic tracking-[0.3em] mb-6">
                Tentang Platform
            </span>
            <h1 class="text-5xl md:text-7xl font-black text-white italic uppercase leading-none mb-6">
                Civic <span class="text-[#800000]">Platform</span><br>Education.
            </h1>
            <p class="max-w-2xl text-slate-400 text-lg font-medium leading-relaxed">
                Infrastruktur digital terintegrasi untuk transparansi, akuntabilitas, dan akselerasi program-program berdampak sosial di bawah naungan Institut Hijau Indonesia.
            </p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto py-20 px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-20 items-start">

            <div class="space-y-8">
                <div class="space-y-4">
                    <h2 class="text-3xl font-black italic uppercase text-slate-900">Misi Digital Kami</h2>
                    <div class="w-20 h-2 bg-[#800000]"></div>
                </div>

                <p class="text-slate-600 leading-loose text-lg">
                    Platform ini lahir dari kebutuhan akan sistem manajemen data yang presisi dalam memantau sebaran program kemasyarakatan di seluruh penjuru wilayah. Kami percaya bahwa setiap inisiatif hijau harus terdokumentasi dengan baik—mulai dari koordinat lokasi, penanggung jawab lapangan, hingga legalitas persuratan.
                </p>

                <div class="bg-slate-50 p-8 rounded-[3rem] border-l-8 border-[#800000]">
                    <p class="italic text-slate-800 font-bold text-xl">
                        "Teknologi bukan hanya alat, tapi jembatan untuk memastikan setiap amanah sosial tersampaikan tepat sasaran dan terukur secara geografis."
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="p-8 bg-white border-2 border-slate-100 rounded-[2.5rem] hover:border-[#800000] transition-all group">
                    <div class="w-12 h-12 bg-slate-100 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-[#800000] transition-all">
                        <svg class="w-6 h-6 text-slate-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" stroke-width="2"></path><path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" stroke-width="2"></path></svg>
                    </div>
                    <h3 class="font-black italic uppercase text-sm mb-2 text-slate-900">Geospatial Tracking</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">Visualisasi nyata sebaran titik program di seluruh Indonesia melalui pemetaan digital interaktif.</p>
                </div>

                <div class="p-8 bg-white border-2 border-slate-100 rounded-[2.5rem] hover:border-[#800000] transition-all group">
                    <div class="w-12 h-12 bg-slate-100 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-[#800000] transition-all">
                        <svg class="w-6 h-6 text-slate-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-width="2"></path></svg>
                    </div>
                    <h3 class="font-black italic uppercase text-sm mb-2 text-slate-900">Smart Administration</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">Otomasi persuratan dan verifikasi dokumen untuk mempercepat birokrasi internal lembaga.</p>
                </div>

                <div class="p-8 bg-white border-2 border-slate-100 rounded-[2.5rem] hover:border-[#800000] transition-all group">
                    <div class="w-12 h-12 bg-slate-100 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-[#800000] transition-all">
                        <svg class="w-6 h-6 text-slate-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" stroke-width="2"></path></svg>
                    </div>
                    <h3 class="font-black italic uppercase text-sm mb-2 text-slate-900">Community Nodes</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">Menghubungkan korlap wilayah langsung dengan publik untuk kolaborasi yang lebih inklusif.</p>
                </div>

                <div class="p-8 bg-[#800000] rounded-[2.5rem] shadow-xl shadow-red-900/20">
                    <h3 class="font-black italic uppercase text-sm mb-2 text-white">Gabung Bersama</h3>
                    <p class="text-xs text-red-100 leading-relaxed mb-4">Mari menjadi bagian dari gerakan perubahan hijau di Indonesia.</p>
                    <a href="/" class="inline-block bg-white text-[#800000] px-6 py-2 rounded-xl text-[10px] font-black uppercase italic transition-transform hover:scale-105">Eksplorasi Program</a>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-slate-50 py-16 px-8 border-t border-slate-100">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-8">
            <div class="text-center md:text-left">
                <p class="text-[10px] font-black uppercase tracking-[0.4em] text-slate-400">Powered By</p>
                <h4 class="text-2xl font-black italic text-slate-900 uppercase">Institut Hijau Indonesia</h4>
            </div>
            <div class="flex gap-4">
                <div class="text-right">
                    <p class="text-[10px] font-bold text-slate-400 uppercase">Hubungi Kami</p>
                    <p class="font-black italic text-[#800000]">it.support@instituthijauindonesia.or.id</p>
                </div>
            </div>
        </div>
    </div>
</div>

@endcomponent
