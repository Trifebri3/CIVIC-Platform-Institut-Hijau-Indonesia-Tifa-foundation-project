@component('pages.super-admin.layouts.app')
<x-slot name="title">Detail Peserta: {{ $user->name }}</x-slot>

    <div class="space-y-6">
        {{-- Header & Breadcrumb --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <nav class="flex text-sm font-medium text-gray-500">
                <a href="{{ route('superadmin.users.index') }}" class="hover:text-[#800000] transition">Peserta</a>
                <span class="mx-2">/</span>
                <span class="text-gray-800">Detail Profil</span>
            </nav>
            <a href="{{ route('superadmin.users.index') }}" class="inline-flex items-center text-xs font-bold uppercase tracking-widest text-gray-400 hover:text-[#800000] transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke Daftar
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- Sidebar Profil (Kiri) --}}
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                    <div class="h-28 bg-[#800000] bg-opacity-90"></div>
                    <div class="px-6 pb-8 text-center">
                        {{-- FOTO PROFIL --}}
<div class="relative -mt-14 mb-4">
    <div class="inline-block p-1 rounded-full bg-white shadow-2xl">
        @if($user->avatar && \Storage::disk('public')->exists($user->avatar))
            {{-- Foto yang diupload peserta --}}
            <img class="h-32 w-32 rounded-full border-4 border-white object-cover shadow-sm"
                 src="{{ asset('storage/'.$user->avatar) }}"
                 alt="{{ $user->name }}">
        @else
            {{-- Inisial Luxury Maroon --}}
            <img class="h-32 w-32 rounded-full border-4 border-white object-cover shadow-sm"
                 src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=800000&color=fff&size=256&bold=true"
                 alt="Avatar">
        @endif

        {{-- Badge Status Online/Aktif --}}
        <span class="absolute bottom-2 right-2 h-5 w-5 rounded-full border-4 border-white {{ $user->is_activated ? 'bg-green-500' : 'bg-gray-300' }} shadow-sm"></span>
    </div>
</div>
                        <h3 class="text-2xl font-black text-gray-800 tracking-tighter">{{ $user->name }}</h3>
                        <p class="text-sm text-gray-500 font-medium">{{ $user->email }}</p>

                        <div class="mt-8 pt-6 border-t border-gray-50 grid grid-cols-2 gap-4 text-left">
                            <div class="space-y-1">
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em]">Role</p>
                                <span class="inline-block px-3 py-1 rounded-full bg-red-50 text-[#800000] text-[10px] font-black uppercase tracking-wider">
                                    {{ $user->role }}
                                </span>
                            </div>
                            <div class="space-y-1">
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em]">Status Verif</p>
                                @if($user->email_verified_at)
                                    <span class="flex items-center text-[10px] font-bold text-green-600 uppercase">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                        Verified
                                    </span>
                                @else
                                    <span class="text-[10px] font-bold text-red-500 uppercase">Unverified</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Informasi Sistem --}}
                <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-gray-100">
                    <h4 class="text-[10px] font-black text-[#800000] uppercase mb-6 tracking-[0.3em]">Informasi Sistem</h4>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-400 font-medium">Join Date</span>
                            <span class="text-gray-700 font-bold">{{ $user->created_at->format('d M Y') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-400 font-medium">User ID</span>
                            <span class="text-[#800000] font-mono font-black">#{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-400 font-medium">Activation</span>
                            <span class="text-xs font-bold uppercase {{ $user->is_activated ? 'text-green-600' : 'text-gray-400' }}">
                                {{ $user->is_activated ? 'Active' : 'Pending' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Detail Content (Kanan) --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden h-full">
                    <div class="px-10 py-8 border-b border-gray-50 flex justify-between items-center bg-gray-50/30">
                        <div>
                            <h3 class="font-black text-gray-900 uppercase tracking-tighter italic text-xl">Profil Kustom</h3>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">Data atribut tambahan peserta</p>
                        </div>
                        <div class="p-3 bg-red-50 rounded-2xl">
                            <svg class="w-6 h-6 text-[#800000]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                    </div>

                    <div class="p-10">
                        @if($user->profile && is_array($user->profile->custom_fields_values))
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-10">
                                @foreach($templates as $field)
                                    <div class="group">
                                        <p class="text-[10px] font-black text-[#800000] uppercase tracking-[0.2em] mb-2 group-hover:translate-x-1 transition-transform">
                                            {{ $field->field_label }}
                                        </p>
                                        <div class="p-4 bg-gray-50 rounded-2xl border border-transparent group-hover:border-red-100 group-hover:bg-white transition-all">
                                            <p class="text-gray-700 font-semibold leading-relaxed">
                                                {{ $user->profile->custom_fields_values[$field->field_name] ?? '-' }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="flex flex-col items-center justify-center py-20 text-center">
                                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-4 border border-gray-100">
                                    <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <h4 class="text-gray-800 font-bold uppercase text-xs tracking-widest">Belum Ada Data</h4>
                                <p class="text-gray-400 text-sm mt-2 max-w-xs mx-auto italic">Peserta ini belum menyelesaikan proses pengisian profil kustom.</p>
                            </div>
                        @endif
                    </div>

                    <div class="px-10 py-6 bg-gray-50/50 border-t border-gray-50 mt-auto flex items-center">
                        <div class="w-2 h-2 rounded-full bg-red-600 animate-pulse mr-3"></div>
                        <span class="text-[9px] text-gray-400 font-black uppercase tracking-[0.3em]">
                            CIVIC Platform Security &middot; Confidential Data
                        </span>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endcomponent
