<?php
use App\Models\SubProgramContent;
use Livewire\Volt\Component;
use Illuminate\Support\Str;

new class extends Component {
    public SubProgramContent $content;
    public $title;
    public $slug;
    public $order_position;

    public function mount() {
        $this->title = $this->content->title;
        $this->slug = $this->content->slug;
        $this->order_position = $this->content->order_position;
    }

    // Auto-generate slug pas ngetik title
    public function updatedTitle($value) {
        $this->slug = Str::slug($value) . '-' . Str::random(5);
    }

    public function save() {
        $this->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|unique:sub_program_contents,slug,' . $this->content->id,
            'order_position' => 'required|integer',
        ]);

        $this->content->update([
            'title' => $this->title,
            'slug' => $this->slug,
            'order_position' => $this->order_position,
        ]);

        session()->flash('success', 'Modul berhasil diupdate! 🔥');
        return $this->redirect(route('admin-program.subprogram.isicontents', $this->content->sub_program_id), navigate: true);
    }
}; ?>

<div class="bg-white rounded-[3rem] p-10 shadow-2xl border border-gray-100">
    <div class="mb-10">
        <h2 class="text-3xl font-black text-gray-900 uppercase italic tracking-tighter">Edit Module Meta</h2>
        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-2">Update identitas modul kelas</p>
    </div>

    <form wire:submit="save" class="space-y-6">
        {{-- Title --}}
        <div>
            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2 block italic">Module Title</label>
            <input type="text" wire:model.live="title"
                   class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl px-6 py-4 text-sm font-bold focus:border-[#800000] focus:ring-0 transition-all">
            @error('title') <span class="text-red-500 text-[10px] font-bold uppercase mt-1">{{ $message }}</span> @enderror
        </div>

        {{-- Slug --}}
        <div>
            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2 block italic">URL Slug (Auto)</label>
            <input type="text" wire:model="slug" readonly
                   class="w-full bg-gray-100 border-2 border-gray-100 rounded-2xl px-6 py-4 text-sm font-bold text-gray-400 cursor-not-allowed">
        </div>

        {{-- Order Position --}}
        <div>
            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2 block italic">Order Position</label>
            <input type="number" wire:model="order_position"
                   class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl px-6 py-4 text-sm font-bold focus:border-[#800000] transition-all">
        </div>

        <div class="pt-6 flex items-center justify-between">
            <a href="{{ route('admin-program.subprogram.isicontents', $content->sub_program_id) }}" wire:navigate
               class="text-[10px] font-black text-gray-400 hover:text-black uppercase tracking-widest italic transition-colors">
                Cancel
            </a>

            <button type="submit"
                    class="bg-black text-white px-10 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-[#800000] shadow-xl transition-all active:scale-95">
                SAVE CHANGES
            </button>
        </div>
    </form>
</div>
