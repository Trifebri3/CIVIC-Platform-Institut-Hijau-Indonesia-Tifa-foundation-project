<?php
use App\Models\{SubProgram, SubProgramContent};
use Livewire\Volt\Component;

new class extends Component {
    public $subProgram;
    public $content;
    public $modules = [];

    // Pastikan parameter di sini namanya 'subProgram' agar cocok dengan Blade
    public function mount(SubProgram $subProgram) {
        $this->subProgram = $subProgram;

        // Ambil konten pertama dari sub-program ini (atau sesuaikan logic pencariannya)
        $this->content = SubProgramContent::where('sub_program_id', $subProgram->id)
            ->first();

        if ($this->content) {
            $data = is_array($this->content->modules)
                ? $this->content->modules
                : (json_decode($this->content->modules, true) ?? []);

            $this->modules = collect($data)->map(function($item) {
                return [
                    'type' => $item['type'] ?? 'text',
                    'title' => $item['title'] ?? 'Untitled Item',
                    'value' => $item['value'] ?? ($item['content'] ?? ''),
                ];
            })->toArray();
        }
    }
}; ?>
<div class="max-w-5xl mx-auto px-4 py-12 pb-32">
    @if($content)
        <div class="mb-16 border-b-4 border-black pb-10">
            <div class="space-y-2">
                <span class="bg-[#800000] text-white text-[9px] font-black px-3 py-1 rounded-sm uppercase italic tracking-widest">
                    {{ $subProgram->program->name ?? 'N/A' }}
                </span>
                <h1 class="text-6xl font-black text-gray-900 uppercase italic tracking-tighter leading-none">
                    {{ $content->title }}
                </h1>
                <p class="text-[10px] text-gray-400 font-black mt-4 uppercase italic tracking-[0.3em]">
                    Sub-Program: {{ $subProgram->title }}
                </p>
            </div>
        </div>

        <div class="space-y-8">
            @foreach($modules as $m)
                <div class="bg-white border border-gray-100 p-8 rounded-[2rem] shadow-sm">
                    <div class="flex gap-6">
                        <div class="w-24 flex-shrink-0">
                            <span class="text-[10px] font-black text-[#800000] uppercase italic tracking-widest">{{ $m['type'] }}</span>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-xl font-black text-gray-900 uppercase italic mb-4">{{ $m['title'] }}</h3>
                            <div class="bg-gray-50 p-6 rounded-2xl text-sm text-gray-600 font-medium italic whitespace-pre-line">
                                {{ $m['value'] }}
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="py-24 text-center bg-gray-50 rounded-[3rem] border-4 border-dashed border-gray-100">
            <p class="text-[10px] font-black text-gray-300 uppercase italic tracking-[0.5em]">
                Belum ada konten untuk modul ini.
            </p>
        </div>
    @endif
</div>
