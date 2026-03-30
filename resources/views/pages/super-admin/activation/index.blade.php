@component('pages.super-admin.layouts.app')
    {{-- 1. Slot untuk Title di Browser --}}
    <x-slot name="title">Manajemen Pertanyaan Aktivasi</x-slot>
<div class="flex flex-wrap items-center justify-between gap-4">

    <!-- LEFT ACTION -->
    <a href="{{ route('superadmin.activation.create') }}"
       class="inline-flex items-center justify-center gap-2
              bg-[#800000] text-white
              px-6 py-3
              rounded-2xl
              font-black text-[10px] uppercase tracking-widest
              shadow-xl shadow-red-900/20
              hover:bg-black hover:-translate-y-1
              transition-all duration-300">

        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                  d="M12 4v16m8-8H4"></path>
        </svg>

        Tambah Pertanyaan Baru
    </a>


    <!-- RIGHT ACTION -->
    <a href="{{ route('superadmin.activation.rekap') }}"
       class="inline-flex items-center gap-2
              px-5 py-2.5
              text-sm font-bold
              text-teal-700
              bg-white
              border-2 border-teal-500
              rounded-xl
              shadow-md
              hover:bg-teal-500 hover:text-white
              transition-all duration-300
              group">

        <svg class="w-5 h-5 text-teal-500 group-hover:text-white transition-colors"
             fill="none" stroke="currentColor" viewBox="0 0 24 24">

            <path stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
            </path>

        </svg>

        Lihat Rekap Jawaban User
    </a>

</div>
</div>
    {{-- 2. Slot Header --}}
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 w-full">
            <div>
                <h2 class="text-2xl font-black text-gray-800 uppercase italic tracking-tighter">
                    Activation <span class="text-[#800000]">Task Architect</span>
                </h2>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mt-1">
                    Konfigurasi alur aktivasi profil peserta secara dinamis
                </p>
            </div>

            {{-- Tombol Tambah --}}
            <a href="{{ route('superadmin.activation.create') }}"
               class="inline-flex items-center justify-center bg-[#800000] text-white px-6 py-3 rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-xl shadow-red-900/20 hover:bg-black hover:-translate-y-1 transition-all duration-300">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Pertanyaan Baru
            </a>
        </div>
    </x-slot>

    {{-- 3. Konten Utama --}}
    <div class="py-8">
        {{-- Alert Success --}}
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-100 text-green-700 text-[10px] font-black rounded-2xl uppercase tracking-widest animate-pulse">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50 border-b border-gray-100">
                            <th class="px-8 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">Order</th>
                            <th class="px-8 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">Master Story & Question</th>
                            <th class="px-8 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">Response Types</th>
                            <th class="px-8 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 text-center">Status</th>
                            <th class="px-8 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($questions as $q)
                            <tr class="hover:bg-gray-50/80 transition-all duration-300 group">
                                <td class="px-8 py-6">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-red-50 text-[#800000] font-black text-xs shadow-sm border border-red-100">
                                        {{ $q->order }}
                                    </span>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-4">
                                        <div class="relative">
                                            <img src="{{ $q->image_url }}"
                                                 class="w-14 h-14 rounded-2xl object-cover shadow-md border-2 border-white group-hover:scale-110 transition-transform duration-500">
                                            @if($q->story)
                                                <span class="absolute -top-1 -right-1 flex h-3 w-3">
                                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                                    <span class="relative inline-flex rounded-full h-3 w-3 bg-[#800000]"></span>
                                                </span>
                                            @endif
                                        </div>
                                        <div>
                                            <h4 class="font-black text-gray-800 uppercase italic text-sm tracking-tight">{{ $q->title }}</h4>
                                            <p class="text-[10px] text-gray-400 font-bold mt-1 line-clamp-1 max-w-[200px]">
                                                {{ $q->story ?? 'No story attached' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex flex-wrap gap-1.5 max-w-[250px]">
                                        @foreach($q->response_definitions ?? [] as $def)
                                            <span class="px-2 py-1 bg-gray-100 text-gray-600 text-[8px] font-black rounded-lg uppercase tracking-wider border border-gray-200 group-hover:bg-[#800000] group-hover:text-white transition-colors duration-300">
                                                {{ $def['type'] }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-center">
                                    <span class="px-4 py-1.5 {{ $q->is_active ? 'bg-green-50 text-green-600 border-green-100' : 'bg-orange-50 text-orange-600 border-orange-100' }} text-[9px] font-black rounded-xl uppercase border tracking-widest">
                                        {{ $q->is_active ? 'Published' : 'Draft' }}
                                    </span>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <div class="flex justify-end items-center gap-2">
                                        <a href="{{ route('superadmin.activation.edit', $q->id) }}"
                                           class="p-2.5 bg-blue-50 text-blue-600 rounded-xl hover:bg-blue-600 hover:text-white transition-all duration-300 shadow-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </a>

                                        <form action="{{ route('superadmin.activation.destroy', $q->id) }}" method="POST" onsubmit="return confirm('Hapus arsitektur pertanyaan ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-2.5 bg-red-50 text-red-600 rounded-xl hover:bg-red-600 hover:text-white transition-all duration-300 shadow-sm">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-8 py-20 text-center text-gray-400 uppercase font-black text-xs italic tracking-widest">
                                    Belum ada pertanyaan aktivasi.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endcomponent
