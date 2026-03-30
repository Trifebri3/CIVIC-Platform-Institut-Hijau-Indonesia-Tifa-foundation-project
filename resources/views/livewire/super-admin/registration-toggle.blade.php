<div class="space-y-6">
    @if (session()->has('message'))
        <div class="p-4 bg-green-50 border-l-4 border-green-500 text-green-700 text-xs font-bold uppercase rounded-r-xl animate-bounce">
            {{ session('message') }}
        </div>
    @endif

    <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-xl shadow-gray-200/50">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center gap-5">
                <div class="p-4 {{ $isOpen ? 'bg-green-50 text-green-600' : 'bg-red-50 text-[#800000]' }} rounded-2xl transition-colors duration-500">
                    @if($isOpen)
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path></svg>
                    @else
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    @endif
                </div>
                <div>
                    <h3 class="text-xl font-black text-gray-800 uppercase italic tracking-tighter">Gerbang Registrasi</h3>
                    <p class="text-[10px] font-bold uppercase tracking-[0.2em] {{ $isOpen ? 'text-green-500' : 'text-[#800000]' }}">
                        Status Saat Ini: {{ $isOpen ? 'Terbuka Untuk Umum' : 'Terkunci / Ditutup' }}
                    </p>
                </div>
            </div>

            <button wire:click="toggleStatus"
                class="relative inline-flex h-9 w-20 items-center rounded-full transition-all duration-500 focus:outline-none shadow-inner {{ $isOpen ? 'bg-green-500 shadow-green-900/20' : 'bg-gray-200 shadow-gray-400/20' }}">
                <span class="sr-only">Toggle Pendaftaran</span>
                <span class="inline-block h-7 w-7 transform rounded-full bg-white shadow-lg transition-transform duration-500 {{ $isOpen ? 'translate-x-12' : 'translate-x-1' }}"></span>
            </button>
        </div>

        <div class="mt-8 pt-8 border-t border-gray-50 {{ $isOpen ? 'opacity-40 pointer-events-none' : '' }}">
            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 mb-2 block">Pesan Penutupan (Muncul di Halaman Depan)</label>
            <div class="flex flex-col md:flex-row gap-3">
                <input type="text" wire:model.defer="pesanTutup"
                    class="flex-1 rounded-xl border-gray-100 bg-gray-50 focus:border-[#800000] focus:ring-[#800000] text-sm font-medium">
                <button wire:click="updatePesan"
                    class="bg-[#800000] text-white px-6 py-2.5 rounded-xl font-bold text-xs uppercase hover:bg-black transition-all">
                    Update Pesan
                </button>
            </div>
        </div>
    </div>
</div>
