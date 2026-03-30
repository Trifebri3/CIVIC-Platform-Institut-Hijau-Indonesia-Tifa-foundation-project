<?php
use App\Models\SubProgramContent;
use Livewire\Volt\Component;
use Illuminate\Support\Str;

new class extends Component {
    public SubProgramContent $content;

    public $title;
    public $slug;
    public $order_position;
    public $modules = [];

    public function mount() {
        $this->title = $this->content->title;
        $this->slug = $this->content->slug;
        $this->order_position = $this->content->order_position;

        // Sesuaikan dengan casting array dari Laravel
        $data = is_array($this->content->modules)
            ? $this->content->modules
            : (json_decode($this->content->modules, true) ?? []);

        // Pastikan setiap item punya key 'value' biar nggak error di blade
        $this->modules = collect($data)->map(function($item) {
            return [
                'type' => $item['type'] ?? 'text',
                'title' => $item['title'] ?? '',
                'value' => $item['value'] ?? ($item['content'] ?? ''), // Fallback jika ada salah nama key
            ];
        })->toArray();
    }

    public function addModule($type) {
        $this->modules[] = [
            'type' => $type,
            'title' => '',
            'value' => '',
        ];
    }

    public function removeModule($index) {
        unset($this->modules[$index]);
        $this->modules = array_values($this->modules);
    }

    public function updatedTitle($value) {
        $this->slug = Str::slug($value) . '-' . Str::lower(Str::random(5));
    }

    public function update() {
        $this->validate([
            'title' => 'required|min:3',
            'order_position' => 'required|integer',
            'modules.*.title' => 'required',
            'modules.*.value' => 'required',
        ]);

        $this->content->update([
            'title' => $this->title,
            'slug' => $this->slug,
            'order_position' => $this->order_position,
            'modules' => $this->modules,
        ]);

        session()->flash('success', 'Data Berhasil Disinkronkan! ✅');
        return $this->redirect(route('admin-program.subprogram.isicontents', $this->content->sub_program_id), navigate: true);
    }
}; ?>

<div class="max-w-4xl mx-auto pb-20">
    <form wire:submit="update" class="space-y-10">

        {{-- IDENTITY CARD --}}
        <div class="bg-white rounded-[3rem] p-12 shadow-2xl border border-gray-100">
            <h2 class="text-3xl font-black text-gray-900 uppercase italic tracking-tighter mb-8">Main Identity</h2>
            <div class="grid grid-cols-1 gap-8">
                <div>
                    <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-3 block italic">Module Title</label>
                    <input type="text" wire:model.live="title" class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl px-8 py-5 text-sm font-black uppercase italic focus:border-[#800000] focus:ring-0 transition-all">
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-3 block italic">URL Slug</label>
                        <input type="text" wire:model="slug" readonly class="w-full bg-gray-100 border-2 border-gray-100 rounded-2xl px-8 py-5 text-[11px] font-bold text-gray-400 italic">
                    </div>
                    <div>
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-3 block italic">Order Position</label>
                        <input type="number" wire:model="order_position" class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl px-8 py-5 text-sm font-black focus:border-[#800000] focus:ring-0 transition-all">
                    </div>
                </div>
            </div>
        </div>

        {{-- CONTENT BUILDER --}}
        <div class="space-y-6">
            <div class="flex justify-between items-center px-4">
                <h2 class="text-2xl font-black text-gray-900 uppercase italic tracking-tighter">Module Contents</h2>
                <div class="flex gap-2">
                    <button type="button" wire:click="addModule('text')" class="bg-gray-100 px-4 py-2 rounded-xl text-[9px] font-black uppercase hover:bg-black hover:text-white transition-all">+ TEXT</button>
                    <button type="button" wire:click="addModule('video')" class="bg-gray-100 px-4 py-2 rounded-xl text-[9px] font-black uppercase hover:bg-black hover:text-white transition-all">+ VIDEO</button>
                    <button type="button" wire:click="addModule('file')" class="bg-gray-100 px-4 py-2 rounded-xl text-[9px] font-black uppercase hover:bg-black hover:text-white transition-all">+ FILE</button>
                </div>
            </div>

            <div class="space-y-4">
                @foreach($modules as $index => $mod)
                    <div class="bg-white rounded-[2rem] p-8 border-2 border-gray-100 shadow-sm relative group">
                        <button type="button" wire:click="removeModule({{ $index }})" class="absolute -top-2 -right-2 bg-red-600 text-white p-2 rounded-full opacity-0 group-hover:opacity-100 transition-all shadow-lg z-10">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round"/></svg>
                        </button>

                        <div class="flex items-start gap-6">
                            <div class="text-3xl bg-gray-50 p-4 rounded-2xl shadow-inner">
                                @if($mod['type'] == 'video') 🎥 @elseif($mod['type'] == 'file') 📁 @else 📝 @endif
                            </div>

                            <div class="flex-1 space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1 block">Item Title</label>
                                        <input type="text" wire:model="modules.{{ $index }}.title"
                                               class="w-full bg-gray-50 border-none rounded-xl px-5 py-3 text-[11px] font-black uppercase italic focus:ring-2 focus:ring-[#800000]/20">
                                    </div>
                                    <div>
                                        <label class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1 block">Content Type</label>
                                        <div class="bg-gray-50 px-5 py-3 rounded-xl text-[10px] font-black text-[#800000] uppercase italic border border-gray-100">
                                            {{ $mod['type'] }}
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1 block">Value / Content Data</label>
                                    @if($mod['type'] == 'video')
                                        <input type="text" wire:model="modules.{{ $index }}.value" placeholder="YouTube URL"
                                               class="w-full bg-gray-50 border-none rounded-xl px-5 py-3 text-[11px] font-bold focus:ring-2 focus:ring-[#800000]/20">
                                    @elseif($mod['type'] == 'file')
                                        <input type="text" wire:model="modules.{{ $index }}.value" placeholder="File Path (e.g. modules/file.pdf)"
                                               class="w-full bg-gray-50 border-none rounded-xl px-5 py-3 text-[11px] font-bold focus:ring-2 focus:ring-[#800000]/20">
                                    @else
                                        <textarea wire:model="modules.{{ $index }}.value" placeholder="Write text content..." rows="3"
                                                  class="w-full bg-gray-50 border-none rounded-xl px-5 py-3 text-[11px] font-medium focus:ring-2 focus:ring-[#800000]/20"></textarea>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- SAVE BUTTON --}}
        <div class="flex justify-end pt-10 border-t border-gray-100">
            <button type="submit" class="bg-black text-white px-16 py-6 rounded-[2rem] font-black text-[12px] uppercase tracking-[0.3em] shadow-2xl hover:bg-[#800000] active:scale-95 transition-all italic">
                SINKRONKAN DATA SEKARANG
            </button>
        </div>
    </form>
</div>
