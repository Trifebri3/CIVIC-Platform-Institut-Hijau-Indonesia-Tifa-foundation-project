@component('pages.user.layouts.app') {{-- Gunakan layout utama kamu --}}

    <div class="min-h-screen bg-[#fafafa] py-12 lg:py-20">
        <div class="max-w-4xl mx-auto px-6">

            {{-- HEADER NAVIGATION --}}
            <div class="flex items-center justify-between mb-10">
                <a href="{{ url()->previous() }}"
                   class="group flex items-center gap-3 text-[10px] font-black uppercase tracking-[0.3em] text-gray-400 hover:text-[#800000] transition-all italic">
                    <div class="p-2 rounded-xl bg-white border border-gray-100 group-hover:border-[#800000]/20 shadow-sm transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                    </div>
                    Back to Inbox
                </a>

                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.4)]"></span>
                    <span class="text-[9px] font-black uppercase tracking-widest text-gray-400 italic">Official System Log</span>
                </div>
            </div>

            {{-- THE LIVEWIRE COMPONENT --}}
            <div class="relative">
                {{-- Aksen Dekoratif di Belakang --}}
                <div class="absolute -top-10 -right-10 w-64 h-64 bg-[#800000]/5 rounded-full blur-3xl -z-10"></div>
                <div class="absolute -bottom-10 -left-10 w-48 h-48 bg-gray-200/50 rounded-full blur-3xl -z-10"></div>

                {{-- Memanggil Livewire Volt Detail Notifikasi --}}
                {{-- Pastikan nama di bawah sesuai dengan nama file volt kamu --}}
                <livewire:notification-detail :id="$id" />
            </div>

            {{-- FOOTER PAGE --}}
            <div class="mt-12 border-t border-gray-100 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-[8px] font-bold text-gray-300 uppercase tracking-[0.4em] italic">
                    Security ID: {{ Hash::make(auth()->id()) }}
                </p>
                <div class="flex gap-6">
                    <span class="text-[8px] font-black text-gray-400 uppercase italic">Help Center</span>
                    <span class="text-[8px] font-black text-gray-400 uppercase italic">Privacy Policy</span>
                </div>
            </div>

        </div>
    </div>

@endcomponent
