@component('pages.user.layouts.app')
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

<div class="bg-white rounded-[2rem] p-8 lg:p-10 shadow-xl shadow-gray-200/50 border border-gray-50 relative overflow-hidden group">
    <div class="absolute -top-10 -right-10 h-32 w-32 bg-[#800000]/5 rounded-full blur-3xl group-hover:bg-[#800000]/10 transition-all duration-500"></div>

    <div class="flex flex-col md:flex-row items-center gap-8">
        <div class="relative">
            <div class="h-20 w-20 bg-green-50 rounded-2xl flex items-center justify-center rotate-3 group-hover:rotate-0 transition-transform duration-500">
                <svg class="h-10 w-10 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.335-1.662c1.72.937 3.659 1.43 5.632 1.43h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                </svg>
            </div>
            <span class="absolute -bottom-2 -right-2 flex h-6 w-6">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-6 w-6 bg-green-500 border-2 border-white"></span>
            </span>
        </div>

        <div class="flex-1 text-center md:text-left">
            <h3 class="text-xl font-extrabold text-gray-900 tracking-tight">Grup Koordinasi Peserta</h3>
            <p class="text-sm text-gray-500 mt-2 leading-relaxed">
                Bergabunglah dengan komunitas resmi CIVIC untuk mendapatkan informasi terbaru, diskusi materi, dan bantuan teknis secara real-time.
            </p>
        </div>

        <div class="w-full md:w-auto">
            <a href="https://chat.whatsapp.com/EH4Rzmdc0HmIyoe6Um18gJ"
               target="_blank"
               class="inline-flex items-center justify-center w-full md:w-auto px-8 py-4 bg-[#800000] text-white rounded-2xl font-bold text-xs uppercase tracking-[0.2em] hover:bg-black hover:shadow-2xl hover:shadow-red-900/40 transition-all duration-300 group/btn">
                <span>Gabung Sekarang</span>
                <svg class="ml-3 h-4 w-4 transition-transform group-hover/btn:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>
    </div>




    <div class="mt-8 pt-6 border-t border-gray-50 flex items-center justify-center md:justify-start gap-6">
        <div class="flex items-center gap-2">
            <div class="h-1.5 w-1.5 rounded-full bg-green-500"></div>
            <span class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Grup Aktif</span>
        </div>
        <div class="flex items-center gap-2">
            <div class="h-1.5 w-1.5 rounded-full bg-[#800000]"></div>
            <span class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Hanya untuk Peserta</span>
        </div>
    </div>
</div>


{{-- SECTION: EXCLUSIVE PROGRAM STATUS --}}
{{-- SECTION: EXCLUSIVE PROGRAM STATUS --}}
{{-- --- SECTION 1: UNDANGAN KHUSUS (ALERTA!) --- --}}
@foreach($pendingInvites as $invite)
<div class="mb-6 relative overflow-hidden bg-black rounded-[2rem] p-8 shadow-2xl border border-white/10 animate-pulse">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-center gap-5">
            <div class="w-14 h-14 bg-[#800000] rounded-2xl flex items-center justify-center shadow-lg shadow-[#800000]/20">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" stroke-width="2"/></svg>
            </div>
            <div>
                <h3 class="text-xl font-black text-white uppercase italic tracking-tighter">Undangan Program Khusus</h3>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">
                    Target Program: <span class="text-white">{{ $invite->program->nama_program ?? 'Program Unknown' }}</span>
                </p>
            </div>
        </div>

        {{-- Tombol Terima (Bisa arahkan ke route khusus) --}}
<a href="{{ route('program.accept', $invite->id) }}"
   class="px-8 py-3 bg-white text-black text-[10px] font-black uppercase rounded-xl hover:bg-gray-200 transition-all italic text-center shadow-lg">
    Terima Akses
</a>
    </div>
</div>
@endforeach

{{-- --- SECTION 2: PROGRAM AKTIF SAYA --- --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
    @forelse($activePrograms as $active)
    <div class="bg-white p-6 rounded-[2.5rem] border border-gray-100 shadow-xl relative overflow-hidden group">
        <div class="flex items-center justify-between">
            <div class="space-y-2">
                <span class="px-3 py-1 bg-green-100 text-green-600 text-[8px] font-black uppercase italic rounded-full">Authorized Access</span>
                <h4 class="text-lg font-black text-slate-800 uppercase italic tracking-tighter group-hover:text-[#800000] transition-colors">
                    {{ $active->program->nama_program ?? 'N/A' }}
                </h4>
                <p class="text-[9px] text-gray-400 uppercase font-bold tracking-widest italic">Role: {{ $active->access_role }}</p>
            </div>
            <div class="w-12 h-12 bg-gray-50 rounded-2xl flex items-center justify-center text-gray-300 group-hover:bg-[#800000] group-hover:text-white transition-all">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" stroke-width="2"/></svg>
            </div>
        </div>
    </div>
    @empty
        {{-- Jika tidak ada program aktif --}}
    @endforelse
</div>
{{-- SECTION: EXCLUSIVE PROGRAM STATUS --}}









    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- PANGGIL KOMPONEN LEARNING HUB --}}
            @livewire('user.learning-hub')

        </div>
    </div>

@endcomponent


