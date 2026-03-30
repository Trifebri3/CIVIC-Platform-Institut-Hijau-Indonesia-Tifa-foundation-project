@component('public.layouts.app')
@slot('title', 'SPEAK Justice - ToR Program')

    {{-- Hero Section --}}
    <section class="relative bg-white pt-20 pb-32 overflow-hidden">
        <div class="absolute top-0 right-0 w-1/3 h-full bg-[#800000]/5 -skew-x-12 transform translate-x-20"></div>

        <div class="max-w-7xl mx-auto px-6 lg:px-12 relative z-10">
            <div class="flex flex-col gap-4 mb-8">
                <span class="text-[#800000] text-[10px] font-black uppercase tracking-[0.5em] italic">Digital Democracy Initiative</span>
                <h1 class="text-6xl md:text-8xl font-black tracking-tighter uppercase leading-none italic">
                    SPEAK <span class="text-[#800000] not-italic">Justice</span>
                </h1>
            </div>

            <div class="grid md:grid-cols-2 gap-12 items-end">
                <div>
                    <p class="text-xl text-gray-600 font-medium leading-relaxed italic">
                        "Strengthening Democracy Participation to Promote Social and Ecological Justice."
                    </p>
                </div>
                <div class="flex flex-col md:items-end gap-2">
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Periode Program</p>
                    <p class="text-lg font-bold text-gray-900 uppercase italic">Maret – Mei 2026</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Bento Grid: Latar Belakang --}}
    <section class="py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-6 lg:px-12">
            <div class="grid lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 bg-white p-12 rounded-[2rem] shadow-sm border border-gray-100">
                    <h2 class="text-[10px] font-black uppercase tracking-[0.4em] text-[#800000] mb-8 italic underline underline-offset-8">Latar Belakang</h2>
                    <div class="prose prose-slate max-w-none text-gray-600 leading-relaxed text-sm space-y-6">
                        <p>Demokrasi di Indonesia menghadapi tantangan serius: menurunnya kualitas partisipasi publik dan menguatnya oligarkisasi. <strong>SPEAK Justice</strong> hadir sebagai respons mendesak untuk membangun kapasitas generasi muda sebagai <em>community organizer</em> yang mampu membaca realitas secara empiris.</p>
                        <p>Program ini bukan sekadar pelatihan, melainkan ruang produksi pengetahuan kolektif tentang kondisi demokrasi di tingkat akar rumput.</p>
                    </div>
                </div>

                <div class="bg-[#800000] p-12 rounded-[2rem] text-white flex flex-col justify-between shadow-xl shadow-red-900/20">
                    <svg class="w-12 h-12 text-white/20" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21L14.017 18C14.017 16.8954 13.1216 16 12.017 16H8.01703V14H12.017C14.2262 14 16.017 12.2091 16.017 10V7C16.017 5.89543 15.1216 5 14.017 5H10.017C7.80789 5 6.01703 6.79086 6.01703 9V21H14.017Z"/></svg>
                    <div>
                        <h3 class="text-2xl font-black italic uppercase leading-tight mb-4">Supported by TiFA Foundation</h3>
                        <p class="text-[10px] uppercase tracking-widest text-white/60 font-bold">Institut Hijau Indonesia</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Horizontal Scroll: Landasan Konseptual --}}
    <section class="py-24 bg-white overflow-hidden">
        <div class="max-w-7xl mx-auto px-6 lg:px-12 mb-16">
            <h2 class="text-4xl font-black uppercase tracking-tighter italic">Landasan <span class="text-[#800000]">Konseptual</span></h2>
        </div>

        <div class="flex gap-8 overflow-x-auto px-6 lg:px-12 pb-12 no-scrollbar">
            @php
                $concepts = [
                    ['title' => 'Demokrasi Partisipatoris', 'desc' => 'Menekankan keterlibatan aktif warga dalam deliberasi publik dan pengawasan kebijakan.'],
                    ['title' => 'Demokrasi Deliberatif', 'desc' => 'Dialog publik yang rasional melalui FGD sebagai ruang pembelajaran politik.'],
                    ['title' => 'Community Organizing', 'desc' => 'Menempatkan warga sebagai aktor utama dalam perubahan sosial melalui aksi kolektif.'],
                    ['title' => 'Experiential Learning', 'desc' => 'Pembelajaran berbasis pengalaman langsung: riset, fasilitasi, dan kampanye digital.'],
                    ['title' => 'Keadilan Ekologis', 'desc' => 'Memastikan kebijakan pembangunan tidak merusak lingkungan dan menciptakan ketimpangan.'],
                ];
            @endphp

            @foreach($concepts as $item)
            <div class="min-w-[300px] bg-gray-50 p-8 rounded-3xl border border-gray-100 transition-transform hover:-translate-y-2">
                <span class="text-[#800000] text-lg font-black italic">0{{ $loop->iteration }}.</span>
                <h4 class="mt-4 text-sm font-black uppercase tracking-widest text-gray-900">{{ $item['title'] }}</h4>
                <p class="mt-4 text-xs text-gray-500 leading-relaxed font-medium uppercase tracking-wider">{{ $item['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </section>

    {{-- Program Components: Dark Section --}}
    <section class="py-24 bg-black text-white rounded-[4rem] mx-4 my-8">
        <div class="max-w-7xl mx-auto px-6 lg:px-12">
            <div class="grid lg:grid-cols-2 gap-20">
                <div>
                    <h2 class="text-5xl font-black italic uppercase tracking-tighter mb-8 leading-none">
                        Komponen <br> <span class="text-[#800000]">Utama Program</span>
                    </h2>
                    <p class="text-gray-400 text-sm uppercase tracking-[0.2em] font-bold">Implementasi Strategis 2026</p>
                </div>

                <div class="space-y-12">
                    <div class="group border-b border-white/10 pb-8">
                        <div class="flex justify-between items-center mb-4">
                            <h5 class="text-lg font-black uppercase tracking-widest italic text-[#800000]">Pendidikan Digital Democracy</h5>
                            <span class="text-[10px] font-bold text-white/30 tracking-[0.5em]">STEP 01</span>
                        </div>
                        <p class="text-xs text-gray-400 leading-loose uppercase tracking-widest">Materi mencakup Oligarki Lokal, Community Organizing, dan Teknik FGD Partisipatif.</p>
                    </div>

                    <div class="group border-b border-white/10 pb-8">
                        <div class="flex justify-between items-center mb-4">
                            <h5 class="text-lg font-black uppercase tracking-widest italic">Platform "Lapor Demokrasi"</h5>
                            <span class="text-[10px] font-bold text-white/30 tracking-[0.5em]">STEP 02</span>
                        </div>
                        <p class="text-xs text-gray-400 leading-loose uppercase tracking-widest">Dokumentasi temuan lapangan, laporan naratif, dan refleksi komunitas berbasis digital.</p>
                    </div>

                    <div class="group border-b border-white/10 pb-8">
                        <div class="flex justify-between items-center mb-4">
                            <h5 class="text-lg font-black uppercase tracking-widest italic">Produksi Pengetahuan</h5>
                            <span class="text-[10px] font-bold text-white/30 tracking-[0.5em]">STEP 03</span>
                        </div>
                        <p class="text-xs text-gray-400 leading-loose uppercase tracking-widest">Penerbitan laporan publik "Demokrasi dalam Pandangan Orang Muda" dan Buku Bunga Rampai.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="py-32 text-center">
        <div class="max-w-3xl mx-auto px-6">
            <h2 class="text-3xl font-black italic uppercase tracking-widest mb-10">Siap Menjadi Community Organizer?</h2>
            <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-12 py-6 bg-[#800000] text-white rounded-full font-black text-[10px] uppercase tracking-[0.4em] shadow-2xl shadow-red-900/40 hover:scale-105 transition-transform active:scale-95">
                Gabung Sekarang
                <svg class="w-4 h-4 ml-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 8l4 4m0 0l-4 4m4-4H3" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </a>
        </div>
    </section>


<style>
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endcomponent
