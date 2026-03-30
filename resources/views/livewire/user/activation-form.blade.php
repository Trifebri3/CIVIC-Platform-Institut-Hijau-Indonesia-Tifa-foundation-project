<div class="max-w-4xl mx-auto py-10 px-4 animate-fadeIn">
    <form wire:submit="activate" class="bg-white rounded-[2.5rem] shadow-2xl border border-gray-100 overflow-hidden">
        <div class="bg-[#800000] px-8 py-4 flex justify-between items-center">
            <span class="text-[10px] font-black text-white uppercase tracking-[0.3em]">Aktivasi Identitas Peserta</span>
        </div>

        <div class="p-8 md:p-12 space-y-10">
            <div class="flex flex-col items-center space-y-4">
                <div class="relative group">
                    <div class="w-32 h-32 rounded-full border-4 border-[#800000] p-1 shadow-xl overflow-hidden bg-gray-50">
                        @if ($avatar)
                            <img src="{{ $avatar->temporaryUrl() }}" class="w-full h-full rounded-full object-cover">
                        @else
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=800000&color=fff" class="w-full h-full rounded-full object-cover">
                        @endif
                    </div>

                    <label class="absolute bottom-0 right-0 bg-black text-white p-2 rounded-full cursor-pointer hover:bg-[#800000] transition-all shadow-lg border-2 border-white">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path></svg>
                        <input type="file" wire:model="avatar" class="hidden" accept="image/*">
                    </label>
                </div>
                <div wire:loading wire:target="avatar" class="text-[9px] font-black text-[#800000] uppercase animate-pulse">Sedang Mengunggah...</div>
            </div>

            <hr class="border-gray-50">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                @foreach($templates as $field)
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">
                            {{ $field->field_label }} @if($field->is_required) <span class="text-[#800000]">*</span> @endif
                        </label>

                        @if($field->field_type == 'select')
                            <select wire:model="custom_fields.{{ $field->field_name }}" class="w-full rounded-2xl border-gray-100 bg-gray-50 focus:border-[#800000] focus:ring-[#800000] text-sm font-medium py-3">
                                <option value="">Pilih {{ $field->field_label }}</option>
                                {{-- Loop Opsi jika ada --}}
                            </select>
                        @else
                            <input type="{{ $field->field_type }}" wire:model="custom_fields.{{ $field->field_name }}"
                                   placeholder="Masukkan {{ strtolower($field->field_label) }}..."
                                   class="w-full rounded-2xl border-gray-100 bg-gray-50 focus:border-[#800000] focus:ring-[#800000] text-sm font-medium py-3">
                        @endif
                        @error('custom_fields.'.$field->field_name) <span class="text-[9px] text-red-600 font-bold uppercase">{{ $message }}</span> @enderror
                    </div>
                @endforeach
            </div>

            <div class="flex justify-center pt-6">
                <button type="submit" wire:loading.attr="disabled" class="bg-[#800000] text-white px-12 py-4 rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-xl hover:bg-black transition-all">
                    <span wire:loading.remove wire:target="activate">Aktifkan Profil Sekarang</span>
                    <span wire:loading wire:target="activate text-white">Memproses...</span>
                </button>
            </div>
        </div>
    </form>
</div>
