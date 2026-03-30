@component('pages.super-admin.layouts.app') {{-- Pastikan nama layout superadmin Bos sudah sesuai, misal 'layouts.admin' --}}
    <div class="min-h-screen bg-[#FDFDFD]">

        {{-- Breadcrumbs & Top Navigation --}}
        <div class="px-8 py-6 bg-white border-b border-gray-100">
            <div class="max-w-7xl mx-auto flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-black rounded-xl flex items-center justify-center shadow-lg shadow-black/20">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 text-[10px] font-black text-gray-400 uppercase tracking-widest italic">
                            <span>Superadmin</span>
                            <span class="text-gray-200">/</span>
                            <span class="text-[#800000]">Communications</span>
                        </div>
                        <h2 class="text-lg font-black uppercase italic tracking-tighter leading-none mt-1">
                            Broadcast <span class="text-[#800000]">Management</span>
                        </h2>
                    </div>
                </div>

                {{-- Status Indicator --}}
                <div class="hidden md:flex items-center gap-4">
                    <div class="text-right">
                        <p class="text-[9px] font-black uppercase tracking-widest text-gray-400">System Status</p>
                        <p class="text-[10px] font-bold text-green-500 uppercase">Omni-Channel Ready</p>
                    </div>
                    <div class="w-px h-8 bg-gray-100"></div>
                    <div class="flex -space-x-2">
                        {{-- Avatar placeholder buat variasi visual --}}
                        <div class="w-8 h-8 rounded-full border-2 border-white bg-gray-100 flex items-center justify-center text-[8px] font-black">SA</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Content: Memanggil Livewire Volt --}}
        <div class="py-10">
            {{-- Nama komponen harus sesuai dengan path file: livewire.super-admin.announcement.index --}}
            @livewire('super-admin.announcement.index')
        </div>

    </div>

    {{-- Script Tambahan jika diperlukan untuk animasi Modal --}}
    @push('scripts')
    <script>
        window.addEventListener('close-modal', event => {
            // Logic tambahan jika ingin menutup modal dengan effect khusus
        });
    </script>
    @endpush
    <style>
    /* Custom Scrollbar biar makin Macho */
    ::-webkit-scrollbar {
        width: 6px;
    }
    ::-webkit-scrollbar-track {
        background: #f9f9f9;
    }
    ::-webkit-scrollbar-thumb {
        background: #e5e7eb;
        border-radius: 10px;
    }
    ::-webkit-scrollbar-thumb:hover {
        background: #800000;
    }
</style>
@endcomponent


