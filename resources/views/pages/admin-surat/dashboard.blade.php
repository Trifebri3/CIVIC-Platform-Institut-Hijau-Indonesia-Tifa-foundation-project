@component('pages.admin-surat.layouts.app')
{{-- Contoh Potongan Kode UI di Dashboard --}}
<div class="grid grid-cols-1 md:grid-cols-4 gap-6">
    <div class="bg-amber-50 border border-amber-200 p-6 rounded-[2rem]">
        <p class="text-[10px] font-black uppercase tracking-widest text-amber-600">Perlu Diproses</p>
        <h3 class="text-4xl font-black italic text-amber-700 mt-2">{{ $stats['pending'] }}</h3>
        <p class="text-[9px] text-amber-500 mt-1 uppercase font-bold">Surat Masuk Baru</p>
    </div>

    <div class="bg-emerald-50 border border-emerald-200 p-6 rounded-[2rem]">
        <p class="text-[10px] font-black uppercase tracking-widest text-emerald-600">Selesai</p>
        <h3 class="text-4xl font-black italic text-emerald-700 mt-2">{{ $stats['approved'] }}</h3>
        <p class="text-[9px] text-emerald-500 mt-1 uppercase font-bold">Surat Disetujui</p>
    </div>

    </div>

<div class="mt-10 bg-white border border-gray-100 rounded-[2.5rem] overflow-hidden">
    <div class="p-8 border-b border-gray-50 flex justify-between items-center">
        <h2 class="text-xl font-black italic uppercase text-slate-900">Aktivitas Terbaru</h2>
        <a href="#" class="text-[10px] font-bold text-[#800000] uppercase tracking-widest">Lihat Semua</a>
    </div>
    <table class="w-full">
        <thead class="bg-slate-50 text-[10px] font-black uppercase tracking-widest text-slate-400">
            <tr>
                <th class="px-8 py-4 text-left">Pengaju</th>
                <th class="px-8 py-4 text-left">Wilayah</th>
                <th class="px-8 py-4 text-left">Status</th>
                <th class="px-8 py-4 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @foreach($recentSubmissions as $surat)
            <tr class="hover:bg-slate-50 transition-all">
                <td class="px-8 py-4">
                    <p class="font-bold text-sm text-slate-900">{{ $surat->user->name }}</p>
                    <p class="text-[10px] text-slate-400 font-medium">{{ $surat->nomor_surat ?? 'Drafting...' }}</p>
                </td>
                <td class="px-8 py-4">
                    <span class="text-xs font-bold text-slate-600 italic uppercase">{{ $surat->wilayah_kegiatan }}</span>
                </td>
                <td class="px-8 py-4">
                    <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase border {{ $surat->status_badge }}">
                        {{ $surat->status }}
                    </span>
                </td>
                <td class="px-8 py-4 text-right">
                    <a href="#" class="bg-black text-white px-4 py-2 rounded-lg text-[9px] font-black uppercase italic">Detail</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endcomponent
