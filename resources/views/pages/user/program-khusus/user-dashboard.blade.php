@component('pages.user.layouts.app')
    <div class="py-12 bg-[#FAFAFA] min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">

            {{-- 1. HEADER HERO --}}
            <div class="relative bg-black rounded-[3rem] p-10 overflow-hidden shadow-2xl">
                <div class="relative z-10">
                    <span class="px-4 py-1 bg-[#800000] text-white text-[9px] font-black uppercase italic rounded-full shadow-lg">Student Access</span>
                    <h1 class="text-4xl md:text-5xl font-black text-white uppercase italic tracking-tighter mt-4 leading-none">
                        {{ $program->nama_program }}
                    </h1>
                    <p class="text-gray-400 text-sm mt-4 max-w-2xl font-medium leading-relaxed">
                        Selamat datang di dashboard program. Semua jadwal, materi, dan informasi resmi akan diperbarui secara berkala di halaman ini.
                    </p>
                </div>
                {{-- Dekorasi Abstrak --}}
                <div class="absolute right-0 top-0 w-1/3 h-full bg-gradient-to-l from-[#800000]/20 to-transparent"></div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">

                {{-- KIRI: TIMELINE & INFORMASI (8 COL) --}}
                <div class="lg:col-span-8 space-y-12">

                    {{-- SECTION: INFORMASI TERBARU (HIGH PRIORITY) --}}
                    @php
                        $informations = $program->contents()->where('type', 'information')->orderBy('order')->get();
                    @endphp

                    @if($informations->count() > 0)
                    <section>
                        <h3 class="text-xs font-black uppercase italic tracking-[0.3em] text-gray-400 mb-6 flex items-center gap-4">
                            Important Information <span class="h-[1px] flex-1 bg-gray-200"></span>
                        </h3>
                        <div class="space-y-4">
                            @foreach($informations as $info)
                                <div class="p-6 {{ ($info->data['priority'] ?? 'normal') === 'high' ? 'bg-red-50 border-l-4 border-red-600' : 'bg-white border border-gray-100' }} rounded-3xl shadow-sm">
                                    <h4 class="text-sm font-black uppercase italic text-slate-800 mb-2">{{ $info->title }}</h4>
                                    <p class="text-xs text-slate-600 leading-relaxed">{{ $info->data['body'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    </section>
                    @endif







{{-- SECTION: UNDANGAN PROGRAM KHUSUS (HANYA UNTUK USER YANG DIUNDANG) --}}
<div class="space-y-10">
    {{-- Section Pilihan Program --}}
    <div>
        <div class="flex items-center justify-between mb-6 px-4">
            <div>
                <h3 class="text-xl font-black uppercase italic tracking-tighter text-slate-800 leading-none">
                    Available <span class="text-[#800000]">Programs</span>
                </h3>
                <p class="text-[9px] font-bold text-gray-400 uppercase italic tracking-widest mt-1">Pilih kategori program yang ingin Anda ajukan</p>
            </div>
        </div>

        @php
            // Ambil semua periode yang statusnya Aktif dan belum expired
            $availablePeriods = \App\Models\TorPeriod::where('is_active', true)
                                ->where('end_at', '>', now())
                                ->latest()
                                ->get();
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse($availablePeriods as $period)
                <div class="bg-white p-8 rounded-[3rem] shadow-xl border border-gray-100 relative overflow-hidden group hover:border-[#800000]/20 transition-all duration-500">
                    {{-- Dekorasi BG --}}
                    <div class="absolute -right-6 -top-6 w-24 h-24 bg-gray-50 rounded-full group-hover:bg-red-50 transition-colors duration-500"></div>

                    <div class="relative z-10">
                        <div class="flex justify-between items-start">
                            <span class="px-3 py-1 bg-black text-[7px] font-black uppercase text-white rounded-lg italic tracking-widest group-hover:bg-[#800000] transition-colors">
                                Active Batch
                            </span>
                            <p class="text-[8px] font-bold text-red-400 uppercase italic">Ends: {{ $period->end_at->format('d M Y') }}</p>
                        </div>

                        <h4 class="text-lg font-black uppercase italic tracking-tighter text-slate-800 mt-6 leading-tight">
                            {{ $period->name }}
                        </h4>

                        <p class="text-[9px] font-medium text-gray-400 mt-2 mb-8 line-clamp-2">
                            Gunakan template ini untuk pengajuan proposal kategori {{ strtolower($period->name) }}. Pastikan dokumen lengkap sebelum submit.
                        </p>

                        <a href="{{ route('user.tor.submit', $period->id) }}"
                           class="inline-flex items-center gap-3 bg-[#FAFAFA] text-slate-800 px-6 py-4 rounded-2xl text-[9px] font-black uppercase italic hover:bg-black hover:text-white transition-all shadow-sm">
                            Mulai Draft Baru
                            <span class="text-lg leading-none">→</span>
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-16 text-center bg-gray-50 rounded-[3rem] border-2 border-dashed border-gray-100">
                    <p class="text-[10px] font-black uppercase italic text-gray-300 tracking-[0.3em]">Saat ini tidak ada program yang dibuka</p>
                </div>
            @endforelse
        </div>
    </div>

























    {{-- Tracking Section (Sama seperti sebelumnya) --}}
    <div class="bg-white p-8 rounded-[3rem] shadow-lg border border-gray-50">
        <div class="flex justify-between items-center mb-6">
            <h4 class="text-xs font-black uppercase italic tracking-widest text-slate-800">Status Pengajuan Terakhir</h4>
            <a href="{{ route('user.tor.history') }}" class="text-[9px] font-black uppercase text-gray-300 hover:text-[#800000] italic transition-colors">Lihat Semua Riwayat —</a>
        </div>

        <div class="space-y-4">
            @php
                $mySubmissions = \App\Models\TorSubmission::where('user_id', auth()->id())
                                ->with('period')
                                ->latest()
                                ->take(3)
                                ->get();
            @endphp

            @forelse($mySubmissions as $sub)
                <div class="flex items-center justify-between p-5 bg-[#FAFAFA] rounded-2xl border border-gray-100 group hover:border-red-100 transition-all">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-sm text-xs">
                            📄
                        </div>
                        <div>
                            <p class="text-[10px] font-black uppercase italic text-slate-700 leading-none">{{ $sub->period->name }}</p>
                            <p class="text-[8px] font-bold text-gray-400 uppercase mt-1">Dikirim pada: {{ $sub->created_at->format('d M, H:i') }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-6">
                        @php
                            $statusColors = [
                                'pending' => 'bg-amber-100 text-amber-600',
                                'approved' => 'bg-emerald-100 text-emerald-600',
                                'rejected' => 'bg-red-100 text-red-600',
                                'revision' => 'bg-blue-100 text-blue-600'
                            ];
                        @endphp
                        <span class="px-4 py-1.5 {{ $statusColors[$sub->status] ?? 'bg-gray-100' }} rounded-lg text-[8px] font-black uppercase italic">
                            {{ $sub->status }}
                        </span>

                        <a href="{{ route('admin.tor.download', $sub->id) }}" target="_blank" class="opacity-0 group-hover:opacity-100 transition-opacity text-[10px] font-bold text-slate-400 hover:text-black">
                            ⬇ PDF
                        </a>
                    </div>
                </div>
            @empty
                <div class="py-10 text-center border-2 border-dashed border-gray-50 rounded-[2rem]">
                    <p class="text-[9px] font-black uppercase italic text-gray-300 tracking-widest">Belum ada pengajuan proposal</p>
                </div>
            @endforelse
        </div>
    </div>
</div>







   <div class="space-y-4">
        <h4 class="px-4 text-[10px] font-black uppercase italic tracking-[0.3em] text-slate-400">Other Programs</h4>
        <div class="bg-white/40 backdrop-blur-md rounded-[3rem] border border-white/60 shadow-sm p-2 transition-all hover:bg-white hover:shadow-xl">
            @livewire('user.khusus.program-list')
        </div>
    </div>


    @php
    $hasProfile = auth()->user()->programProfile()->exists();
@endphp

<a href="{{ route('user.program-profile') }}"
   class="flex items-center justify-between p-5 rounded-3xl transition-all {{ $hasProfile ? 'bg-white border border-gray-100 hover:shadow-lg' : 'bg-red-50 border border-red-100 animate-pulse' }}">
    <div class="flex items-center gap-4">
        <div class="p-3 {{ $hasProfile ? 'bg-slate-100' : 'bg-red-500 text-white' }} rounded-2xl">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" stroke-width="2"></path></svg>
        </div>
        <div>
            <h4 class="text-sm font-black italic uppercase text-slate-800">Profil Program</h4>
            <p class="text-[9px] font-bold {{ $hasProfile ? 'text-emerald-500' : 'text-red-500' }} uppercase italic">
                {{ $hasProfile ? '● Terverifikasi' : '● Wajib Diisi' }}
            </p>
        </div>
    </div>
    <svg class="w-5 h-5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>
</a>

                    {{-- SECTION: TIMELINE JADWAL --}}
                    <section>
                        <h3 class="text-xs font-black uppercase italic tracking-[0.3em] text-gray-400 mb-6 flex items-center gap-4">
                            Program Timeline <span class="h-[1px] flex-1 bg-gray-200"></span>
                        </h3>
                        <div class="relative border-l-2 border-gray-100 ml-4 pl-8 space-y-10">
                            @foreach($program->contents()->where('type', 'timeline')->orderBy('order')->get() as $time)
                                <div class="relative">
                                    {{-- Dot --}}
                                    <div class="absolute -left-[41px] top-0 w-5 h-5 bg-white border-4 border-[#800000] rounded-full shadow-sm"></div>

                                    <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-50 hover:shadow-md transition-shadow">
                                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                                            <div>
                                                <div class="flex items-center gap-3 mb-1">
                                                    <span class="text-[10px] font-black text-[#800000] uppercase italic">{{ \Carbon\Carbon::parse($time->data['date'])->format('d M Y') }}</span>
                                                    <span class="text-[10px] font-bold text-gray-300">/</span>
                                                    <span class="text-[10px] font-black text-gray-500 uppercase italic">{{ $time->data['time'] }}</span>
                                                </div>
                                                <h4 class="text-lg font-black uppercase italic text-slate-800">{{ $time->title }}</h4>
                                                <p class="text-xs text-gray-400 font-bold uppercase mt-1 tracking-widest">{{ $time->data['location'] }}</p>
                                            </div>
                                        </div>
                                        @if($time->data['desc'])
                                            <div class="mt-4 pt-4 border-t border-gray-50 text-xs text-slate-500 leading-relaxed italic">
                                                {{ $time->data['desc'] }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                </div>





                {{-- KANAN: RESOURCES & QUICK LINKS (4 COL) --}}
               <div class="lg:col-span-4 space-y-8">

    {{-- SECTION: LEARNING RESOURCES --}}
 <div class="lg:col-span-4 space-y-10"> {{-- Tambah space antar card --}}

    {{-- SECTION: LEARNING RESOURCES --}}
    <div class="bg-white p-8 rounded-[3rem] shadow-xl border border-gray-100 relative overflow-hidden transition-all hover:shadow-2xl">
        <div class="flex items-center justify-between mb-8 border-b border-gray-50 pb-4">
            <h3 class="text-xs font-black uppercase italic tracking-[0.2em] text-slate-800">Resources</h3>
            <span class="text-[8px] font-bold text-[#800000] bg-red-50 px-2 py-0.5 rounded-full uppercase italic">Update 2026</span>
        </div>

        <div class="space-y-4">
            @forelse($program->contents()->where('type', 'resource')->orderBy('order')->get() as $res)
                <a href="{{ $res->data['link'] }}" target="_blank"
                    class="flex flex-col p-5 bg-gray-50/50 rounded-[2rem] border border-transparent hover:border-red-100 hover:bg-white hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group">
                    <span class="text-[11px] font-black uppercase italic tracking-tighter text-slate-700 group-hover:text-[#800000]">{{ $res->title }}</span>
                    <div class="flex items-center justify-between mt-2">
                        <span class="text-[8px] font-bold text-gray-400 uppercase tracking-widest leading-none">
                            {{ $res->data['label'] ?? 'Open Resource' }}
                        </span>
                        <svg class="w-3 h-3 text-gray-300 group-hover:text-[#800000] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M14 5l7 7m0 0l-7 7m7-7H3" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                </a>
            @empty
                <div class="py-8 text-center flex flex-col items-center">
                    <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center text-gray-300 mb-3 italic font-black text-xs">!</div>
                    <p class="text-[10px] font-bold text-gray-300 uppercase italic tracking-widest">Belum ada resource tersedia</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- CARD: BANTUAN --}}
    <div class="bg-[#800000] p-10 rounded-[3rem] shadow-xl text-white relative overflow-hidden group">
        <div class="absolute -right-6 -top-6 w-24 h-24 bg-white/5 rounded-full blur-xl group-hover:scale-150 transition-transform duration-700"></div>
        <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/10 rounded-full blur-2xl group-hover:scale-110 transition-transform duration-700"></div>

        <div class="relative z-10">
            <h3 class="text-[10px] font-black uppercase italic tracking-[0.3em] mb-4 opacity-70">Butuh Bantuan?</h3>
            <p class="text-sm font-extrabold leading-snug italic uppercase tracking-tighter">
                Hubungi instruktur atau admin melalui grup koordinasi jika ada kendala akses.
            </p>

            <a href="#" class="inline-flex items-center mt-8 text-[10px] font-black uppercase italic group/link">
                <span class="border-b-2 border-white pb-1 group-hover/link:text-black group-hover/link:border-black transition-all">Contact Admin</span>
                <span class="ml-2 transform group-hover/link:translate-x-2 transition-transform">→</span>
            </a>
        </div>
    </div>

    {{-- PROGRAM LIST SECTION (DILUAR CARD LAIN) --}}


</div>



            </div>
        </div>
    </div>
@endcomponent
