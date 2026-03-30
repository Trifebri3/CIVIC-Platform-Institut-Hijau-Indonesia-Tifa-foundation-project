<div class="p-8 lg:p-12 animate-fadeIn">
    @if (session()->has('message'))
        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-r-xl font-bold flex items-center gap-3 animate-bounce">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('message') }}
        </div>
    @endif

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-4">
        <div>
            <h3 class="text-2xl font-extrabold tracking-tight text-gray-900">Pengaturan Field Profil</h3>
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-[#a52a2a] mt-1">Konfigurasi Form Dinamis Peserta</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('superadmin.export.pdf') }}"
               class="flex items-center gap-2 bg-white border-2 border-[#800000] text-[#800000] px-5 py-2.5 rounded-xl font-bold text-sm shadow-sm hover:bg-[#800000] hover:text-white transition-all duration-300">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                PDF Report
            </a>

            <a href="{{ route('superadmin.export') }}"
               class="flex items-center gap-2 bg-gradient-to-r from-[#800000] to-[#a52a2a] text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-lg shadow-red-900/20 hover:brightness-110 transition-all">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Export CSV
            </a>
        </div>
    </div>

    <div class="bg-white p-6 rounded-2xl mb-10 border border-gray-100 shadow-xl shadow-gray-200/40">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
            <div class="space-y-1">
                <label class="text-[10px] font-bold uppercase text-gray-400 ml-1">Label Field</label>
                <input type="text" wire:model="field_label" placeholder="Contoh: No WhatsApp"
                       class="w-full rounded-xl border-gray-100 bg-gray-50 focus:border-[#800000] focus:ring-[#800000] transition-all text-sm font-medium">
            </div>

            <div class="space-y-1">
                <label class="text-[10px] font-bold uppercase text-gray-400 ml-1">Key (JSON)</label>
                <input type="text" wire:model="field_name" placeholder="Contoh: no_wa"
                       class="w-full rounded-xl border-gray-100 bg-gray-50 focus:border-[#800000] focus:ring-[#800000] transition-all text-sm font-mono italic">
            </div>

            <div class="space-y-1">
                <label class="text-[10px] font-bold uppercase text-gray-400 ml-1">Tipe Data</label>
                <select wire:model="field_type" class="w-full rounded-xl border-gray-100 bg-gray-50 focus:border-[#800000] focus:ring-[#800000] transition-all text-sm font-medium">
                    <option value="text">Text (Pendek)</option>
                    <option value="number">Number (Angka)</option>
                    <option value="date">Date (Tanggal)</option>
                    <option value="textarea">Textarea (Panjang)</option>
                </select>
            </div>

            <div class="flex items-end">
                <button wire:click="store" wire:loading.attr="disabled"
                        class="w-full bg-[#800000] text-white py-2.5 rounded-xl font-bold text-sm hover:bg-black transition-all duration-300 transform active:scale-95 shadow-md disabled:opacity-50 disabled:cursor-not-allowed">
                    <span wire:loading.remove>+ Tambah Field</span>
                    <span wire:loading class="flex items-center justify-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        Memproses...
                    </span>
                </button>
            </div>
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="p-5 text-[11px] font-bold uppercase tracking-widest text-gray-500">Label</th>
                    <th class="p-5 text-[11px] font-bold uppercase tracking-widest text-gray-500">Key (JSON)</th>
                    <th class="p-5 text-[11px] font-bold uppercase tracking-widest text-gray-500">Tipe</th>
                    <th class="p-5 text-[11px] font-bold uppercase tracking-widest text-gray-500 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($templates as $t)
                <tr wire:key="field-{{ $t->id }}" class="group hover:bg-red-50/30 transition-colors">
                    <td class="p-5 text-sm font-bold text-gray-700">{{ $t->field_label }}</td>
                    <td class="p-5">
                        <span class="rounded-md bg-gray-100 px-2.5 py-1 text-xs font-mono text-gray-600 group-hover:bg-white border border-transparent group-hover:border-gray-200 transition-all">
                            {{ $t->field_name }}
                        </span>
                    </td>
                    <td class="p-5">
                        <div class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-[#800000]"></span>
                            <span class="text-sm text-gray-500">{{ ucfirst($t->field_type) }}</span>
                        </div>
                    </td>
                    <td class="p-5 text-right">
                        <button wire:click="delete({{ $t->id }})"
                                wire:confirm="Apakah Anda yakin ingin menghapus field ini?"
                                class="inline-flex items-center gap-1.5 text-xs font-bold text-red-500 hover:text-red-700 hover:underline transition-all active:scale-90">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            Hapus
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @if($templates->isEmpty())
            <div class="p-10 text-center">
                <p class="text-sm text-gray-400 italic">Belum ada field profil yang dikonfigurasi.</p>
            </div>
        @endif
    </div>
</div>
