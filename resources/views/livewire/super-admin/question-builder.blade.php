<div class="space-y-8">
    <form wire:submit.prevent="save" class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white p-8 rounded-[2rem] shadow-xl shadow-gray-200/50 border border-gray-100">
                <h3 class="text-xs font-black text-[#800000] uppercase tracking-[0.2em] mb-6 italic">Master Config</h3>

                <div class="space-y-4">
                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Task Title</label>
                        <input type="text" wire:model="title" class="w-full rounded-2xl border-gray-100 bg-gray-50 focus:border-[#800000] focus:ring-[#800000] text-sm font-bold py-3">
                    </div>

                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Visual Reference</label>
                        <div class="mt-2 flex flex-col items-center p-4 border-2 border-dashed border-gray-100 rounded-2xl">
                            @if($image)
                                <img src="{{ $image->temporaryUrl() }}" class="w-full h-32 object-cover rounded-xl mb-2">
                            @elseif($existingImage)
                                <img src="{{ asset('storage/'.$existingImage) }}" class="w-full h-32 object-cover rounded-xl mb-2">
                            @endif
                            <input type="file" wire:model="image" class="text-xs text-gray-500">
                        </div>
                    </div>

                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">The Story (Narasi)</label>
                        <textarea wire:model="story" rows="4" class="w-full rounded-2xl border-gray-100 bg-gray-50 text-sm font-medium"></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white p-8 rounded-[2.5rem] shadow-xl shadow-gray-200/50 border border-gray-100">
                <div class="flex justify-between items-center mb-8">
                    <h3 class="text-xs font-black text-[#800000] uppercase tracking-[0.2em] italic">Response Architect</h3>
                    <button type="button" wire:click="addField" class="bg-black text-white px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-[#800000] transition-all">
                        + Add Input Type
                    </button>
                </div>

                <div class="space-y-4">
                    @foreach($response_definitions as $index => $field)
                        <div class="p-6 bg-gray-50 rounded-3xl border border-gray-100 relative group animate-fadeIn">
                            <button type="button" wire:click="removeField({{ $index }})" class="absolute -top-2 -right-2 bg-white text-red-500 shadow-md rounded-full p-1 hover:bg-red-500 hover:text-white transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1 block">Input Type</label>
                                    <select wire:model="response_definitions.{{ $index }}.type" class="w-full rounded-xl border-gray-200 text-[11px] font-bold uppercase">
                                        <option value="text">Short Text</option>
                                        <option value="textarea">Long Text / Essay</option>
                                        <option value="select">Dropdown Choice</option>
                                        <option value="checkbox">Multiple Selection</option>
                                        <option value="file">File/Image Upload</option>
                                    </select>
                                </div>

                                <div class="md:col-span-2">
                                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1 block">Question Label</label>
                                    <input type="text" wire:model="response_definitions.{{ $index }}.label" placeholder="e.g. Ceritakan pengalaman anda..." class="w-full rounded-xl border-gray-200 text-xs font-bold">
                                </div>

                                @if(in_array($response_definitions[$index]['type'], ['select', 'checkbox']))
                                <div class="md:col-span-3">
                                    <label class="text-[9px] font-black text-[#800000] uppercase tracking-widest mb-1 block">Choices (Pisahkan dengan koma)</label>
                                    <input type="text" wire:model="response_definitions.{{ $index }}.options" placeholder="Pilihan A, Pilihan B, Pilihan C" class="w-full rounded-xl border-red-100 bg-red-50 text-xs font-bold">
                                </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-10 flex justify-end gap-4 border-t pt-8">
                    <a href="{{ route('superadmin.activation.index') }}" class="px-8 py-4 text-[10px] font-black uppercase text-gray-400 hover:text-black transition-all">Cancel</a>
                    <button type="submit" class="bg-[#800000] text-white px-10 py-4 rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] shadow-xl shadow-red-900/20 hover:scale-105 transition-all">
                        Deploy Task Architect
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
