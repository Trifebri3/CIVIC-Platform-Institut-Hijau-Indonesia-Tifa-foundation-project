<?php

use App\Models\ActivationQuestion;
use App\Models\ActivationAnswer;
use Illuminate\Support\Facades\DB;
use function Livewire\Volt\{state, computed, mount};

// 1. State Management
state([
    'currentStep' => 0,
    'totalSteps' => 0,
    'answers' => []
]);

mount(function () {
    $questions = ActivationQuestion::where('is_active', true)->orderBy('order', 'asc')->get();
    $this->totalSteps = $questions->count();

    if ($this->totalSteps === 0) {
        auth()->user()->update(['is_activated' => true]);
        return redirect()->route('user.profile.edit');
    }

    // Inisialisasi struktur jawaban
    foreach ($questions as $q) {
        $this->answers[$q->id] = [];
        foreach ($q->response_definitions as $def) {
            // Penting: Inisialisasi sesuai ID field agar wire:model sinkron
            $this->answers[$q->id][$def['id']] = ($def['type'] === 'checkbox') ? [] : '';
        }
    }
});

// 2. Computed Properties
$currentQuestion = computed(fn () =>
    ActivationQuestion::where('is_active', true)
        ->orderBy('order', 'asc')
        ->skip($this->currentStep)
        ->first()
);

// 3. Actions (Gunakan penamaan method agar mudah dipanggil di Blade)

$nextStep = function () {
    if ($this->currentStep < $this->totalSteps - 1) {
        $this->currentStep++;
    } else {
        // Panggil fungsi simpan
        $this->submitData();
    }
};

$previousStep = function () {
    if ($this->currentStep > 0) $this->currentStep--;
};

// Ubah nama menjadi submitData agar tidak bentrok dengan keyword internal
$submitData = function () {
    DB::beginTransaction();
    try {
        foreach ($this->answers as $qId => $content) {
            ActivationAnswer::create([
                'user_id' => auth()->id(),
                'activation_question_id' => $qId,
                'content' => $content
            ]);
        }

        // Update status user
        auth()->user()->update(['is_activated' => true]);

        DB::commit();
        return redirect()->route('user.profile.edit');

    } catch (\Exception $e) {
        DB::rollBack();
        session()->flash('error', 'Gagal: ' . $e->getMessage());
    }
};

?>

<div class="fixed inset-0 flex flex-col lg:flex-row bg-white overflow-hidden" wire:key="step-{{ $currentStep }}">
    @if($item = $this->currentQuestion)
    {{-- Kiri: Visual --}}
    <div class="relative w-full lg:w-1/2 h-[35%] lg:h-full bg-slate-950 flex items-end p-8 lg:p-20">
        <div class="absolute inset-0">
            @if($item->image)
                <img src="{{ asset('storage/' . $item->image) }}" class="w-full h-full object-cover opacity-40 animate-fade-in">
            @endif
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/20 to-transparent"></div>
        </div>

        <div class="relative z-10 w-full animate-slide-up">
            <span class="text-[10px] font-black text-red-500 uppercase tracking-[0.4em]">Bagian {{ $currentStep + 1 }} / {{ $totalSteps }}</span>
            <div class="prose prose-invert italic text-slate-300 mt-4 leading-relaxed">
                {!! $item->story !!}
            </div>
        </div>

        <div class="absolute bottom-0 left-0 w-full h-1 bg-white/10">
            <div class="h-full bg-red-600 transition-all duration-1000 ease-in-out" style="width: {{ (($currentStep + 1) / $totalSteps) * 100 }}%"></div>
        </div>
    </div>

    {{-- Kanan: Form --}}
    <div class="w-full lg:w-1/2 h-[65%] lg:h-full p-8 lg:p-24 overflow-y-auto no-scrollbar bg-[#FCFCFC]">
        <div class="max-w-xl mx-auto animate-fade-in-right">
            @if (session()->has('error'))
                <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-2xl text-xs font-bold uppercase">{{ session('error') }}</div>
            @endif

            <h2 class="text-4xl lg:text-5xl font-black uppercase tracking-tighter text-slate-900 mb-8">{{ $item->title }}</h2>

            <div class="space-y-8">
                @foreach($item->response_definitions as $def)
                    <div class="space-y-3">
                        <label class="text-[10px] font-black uppercase text-slate-400 tracking-widest ml-1">{{ $def['label'] }}</label>

                        @if($def['type'] === 'text')
                            {{-- Gunakan .defer agar tidak request tiap ngetik --}}
                            <textarea wire:model.defer="answers.{{ $item->id }}.{{ $def['id'] }}"
                                class="w-full p-6 lg:p-8 bg-slate-100/80 rounded-[2rem] border-none focus:ring-4 focus:ring-red-50 text-slate-700 text-lg min-h-[150px] transition-all"
                                placeholder="Tuliskan jawaban Anda di sini..."></textarea>
                        @endif

                        @if($def['type'] === 'checkbox' || $def['type'] === 'radio')
                            <div class="grid grid-cols-1 gap-3">
                                @php $options = is_array($def['options']) ? $def['options'] : explode(',', $def['options']); @endphp
                                @foreach($options as $option)
                                    @php $option = trim($option); @endphp
                                    <label class="relative group cursor-pointer">
                                        <input type="{{ $def['type'] }}"
                                               wire:model="answers.{{ $item->id }}.{{ $def['id'] }}"
                                               value="{{ $option }}"
                                               class="hidden peer">
                                        <div class="flex items-center p-5 bg-white border border-slate-100 rounded-2xl shadow-sm transition-all peer-checked:border-red-500 peer-checked:bg-red-50 peer-checked:scale-[1.02]">
                                            <div class="w-5 h-5 rounded-full border-2 border-slate-200 flex items-center justify-center mr-4 peer-checked:border-red-500">
                                                <div class="w-2.5 h-2.5 rounded-full bg-red-600 scale-0 peer-checked:scale-100 transition-transform"></div>
                                            </div>
                                            <span class="text-sm font-bold text-slate-700">{{ $option }}</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach

                <div class="flex items-center gap-6 pt-8">
                    <button wire:click="nextStep" wire:loading.attr="disabled"
                            class="px-12 py-5 bg-red-600 text-white rounded-full font-black uppercase text-[10px] tracking-[0.3em] shadow-xl shadow-red-600/20 hover:bg-red-700 transition-all active:scale-95">
                        <span wire:loading.remove wire:target="nextStep">
                            {{ $currentStep === $totalSteps - 1 ? 'Selesaikan' : 'Lanjutkan' }}
                        </span>
                        <span wire:loading wire:target="nextStep">Memproses...</span>
                    </button>

                    @if($currentStep > 0)
                        <button wire:click="previousStep" class="px-6 py-5 text-slate-300 font-black uppercase text-[10px] tracking-[0.3em] hover:text-red-600 transition-colors">
                            Kembali
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    <style>
        .animate-fade-in { animation: fadeIn 1s ease-out forwards; }
        .animate-fade-in-right { animation: fadeInRight 0.8s cubic-bezier(0.16, 1, 0.3, 1); }
        .animate-slide-up { animation: slideUp 1s ease-out; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 0.4; } }
        @keyframes fadeInRight { from { opacity: 0; transform: translateX(30px); } to { opacity: 1; transform: translateX(0); } }
        @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .no-scrollbar::-webkit-scrollbar { display: none; }
    </style>
</div>
