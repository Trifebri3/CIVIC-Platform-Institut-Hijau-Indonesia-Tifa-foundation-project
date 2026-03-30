<?php

use App\Models\{SubProgram, Absensi};
use Livewire\Volt\Component;
use Illuminate\Support\Str;

new class extends Component {
    public SubProgram $subProgram;

    // Form Properties
    public $title, $type = 'regular', $open_at, $duration_minutes = 30;
    public $is_protected = false; // Checkbox state
    public $auth_code; // Nilai kodenya
    public $editingAbsensiId = null;

    public function mount(SubProgram $subProgram)
    {
        // Pastikan relasi di-load biar tidak error foreach null
        $this->subProgram = $subProgram->load('absensis');
    }

    public function generateCode()
    {
        $this->auth_code = Str::upper(Str::random(6));
    }

    // Listener saat checkbox berubah (Opsional tapi bagus buat UX)
    public function updatedIsProtected($value)
    {
        if ($value && empty($this->auth_code)) {
            $this->generateCode();
        } elseif (!$value) {
            $this->auth_code = null;
        }
    }

    public function save()
    {
        $this->validate([
            'title' => 'required|min:3',
            'type' => 'required|in:regular,pre_test,post_test',
            'open_at' => 'required',
            'duration_minutes' => 'required|integer|min:1',
            'auth_code' => $this->is_protected ? 'required|min:3' : 'nullable'
        ]);

        Absensi::updateOrCreate(
            ['id' => $this->editingAbsensiId],
            [
                'sub_program_id' => $this->subProgram->id,
                'title' => $this->title,
                'type' => $this->type,
                'open_at' => $this->open_at,
                'duration_minutes' => $this->duration_minutes,
                'is_protected' => $this->is_protected,
                // Jika is_protected true, simpan kodenya. Jika false, set NULL.
                'auth_code' => $this->is_protected ? $this->auth_code : null,
                'is_active' => true,
            ]
        );

        $this->reset(['title', 'type', 'open_at', 'duration_minutes', 'is_protected', 'auth_code', 'editingAbsensiId']);
        $this->subProgram->load('absensis'); // Refresh list
        session()->flash('success', 'Absensi Configuration Synced!');
    }

    public function edit(Absensi $absensi)
    {
        $this->editingAbsensiId = $absensi->id;
        $this->title = $absensi->title;
        $this->type = $absensi->type;
        $this->open_at = $absensi->open_at->format('Y-m-d\TH:i');
        $this->duration_minutes = $absensi->duration_minutes;
        $this->is_protected = (bool)$absensi->is_protected;
        $this->auth_code = $absensi->auth_code;
    }

    public function delete(Absensi $absensi)
    {
        $absensi->delete();
        $this->subProgram->load('absensis');
        session()->flash('success', 'Absensi Slot Removed.');
    }
}; ?>

<div class="max-w-6xl mx-auto pb-24 antialiased">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row justify-between items-start mb-12 gap-6">
        <div>
            <h2 class="text-4xl font-black text-gray-900 uppercase italic tracking-tighter leading-none">Attendance <span class="text-[#800000]">Core</span></h2>
            <p class="text-[10px] text-gray-400 font-black uppercase tracking-[0.4em] mt-3 italic">Managing: {{ $subProgram->title }}</p>
        </div>
        <a href="{{ route('admin-program.content.index') }}" wire:navigate class="p-4 bg-gray-50 rounded-2xl hover:bg-black group transition-all shadow-sm">
            <svg class="w-5 h-5 text-gray-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M10 19l-7-7m0 0l7-7m-7 7h18" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        {{-- Form Configuration (Left) --}}
        <div class="lg:col-span-1">
            <div class="bg-white p-8 rounded-[2.5rem] shadow-2xl shadow-gray-200/50 border border-gray-50 sticky top-10">
                <h3 class="text-[11px] font-black uppercase text-[#800000] tracking-[0.3em] mb-8 italic">Config Slot</h3>

                <form wire:submit.prevent="save" class="space-y-6">
                    <div class="space-y-2">
                        <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-4">Slot Title</label>
                        <input type="text" wire:model="title" placeholder="e.g. Absen Pagi" class="w-full rounded-2xl border-none bg-gray-50 p-4 font-bold text-sm focus:ring-2 focus:ring-[#800000]/20 @error('title') ring-2 ring-red-500 @enderror">
                    </div>

                    <div class="space-y-2">
                        <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-4">Type</label>
                        <select wire:model="type" class="w-full rounded-2xl border-none bg-gray-50 p-4 font-bold text-sm focus:ring-2 focus:ring-[#800000]/20">
                            <option value="regular">Regular Check-in</option>
                            <option value="pre_test">Pre-Test Requirement</option>
                            <option value="post_test">Post-Test Final</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-4">Open At</label>
                            <input type="datetime-local" wire:model="open_at" class="w-full rounded-2xl border-none bg-gray-50 p-4 font-bold text-xs">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-4">Duration (Min)</label>
                            <input type="number" wire:model="duration_minutes" class="w-full rounded-2xl border-none bg-gray-50 p-4 font-bold text-sm">
                        </div>
                    </div>

                    {{-- Security Toggle --}}
                    <div class="pt-4 border-t border-gray-100">
                        <label class="flex items-center justify-between cursor-pointer group p-2 hover:bg-gray-50 rounded-xl transition-all">
                            <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest group-hover:text-black">Secure with Code?</span>
                            <div class="relative">
                                <input type="checkbox" wire:model.live="is_protected" class="sr-only peer">
                                <div class="w-10 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[#800000]"></div>
                            </div>
                        </label>
                    </div>

                    {{-- Auth Code Input (Muncul jika is_protected == true) --}}
                    @if($is_protected)
                    <div class="space-y-2 animate-fadeIn">
                        <label class="text-[9px] font-black text-[#800000] uppercase tracking-widest ml-4 italic">Security Passcode</label>
                        <div class="flex items-center gap-2">
                            <input type="text" wire:model="auth_code" placeholder="ENTER CODE"
                                   class="flex-1 rounded-2xl border-none bg-red-50 p-4 font-black text-center text-[#800000] tracking-[0.4em] focus:ring-2 focus:ring-[#800000]/20">
                            <button type="button" wire:click="generateCode" title="Randomize"
                                    class="p-4 bg-black text-white rounded-2xl hover:bg-[#800000] transition-all shadow-lg active:scale-95">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                        </div>
                        @error('auth_code') <span class="text-[8px] font-bold text-red-500 uppercase ml-4 italic">{{ $message }}</span> @enderror
                    </div>
                    @endif

                    <button type="submit" class="w-full bg-black text-white py-4 rounded-2xl font-black text-[10px] uppercase tracking-[0.3em] italic shadow-xl hover:bg-[#800000] transition-all active:scale-[0.98]">
                        {{ $editingAbsensiId ? 'Update Slot' : 'Deploy Slot' }}
                    </button>
                </form>
            </div>
        </div>

        {{-- Slot List (Right) --}}
        <div class="lg:col-span-2 space-y-6">
            @forelse($subProgram->absensis as $absen)
                <div class="bg-white rounded-[2.5rem] p-8 border border-gray-100 flex items-center justify-between group hover:shadow-2xl hover:shadow-gray-200/80 transition-all duration-500 @if(!$absen->is_active) opacity-60 @endif">
                    <div class="flex items-center gap-8">
                        <div class="w-16 h-16 rounded-[1.5rem] {{ $absen->is_active ? 'bg-black' : 'bg-gray-100' }} flex items-center justify-center text-white shadow-lg transition-colors">
                            @if($absen->type == 'regular')
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0114 0z" stroke-width="2.5"/></svg>
                            @else
                                <span class="font-black text-[10px] uppercase italic leading-none text-center">Test<br>Mod</span>
                            @endif
                        </div>

                        <div>
                            <div class="flex items-center gap-3 mb-1">
                                <h4 class="font-black text-gray-900 uppercase italic tracking-tighter">{{ $absen->title }}</h4>
                                @if($absen->is_open && $absen->is_active)
                                    <span class="flex h-2 w-2 rounded-full bg-green-500 animate-pulse"></span>
                                @endif
                            </div>
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest italic">
                                {{ $absen->open_at->format('d M | H:i') }} ({{ $absen->duration_minutes }} Min)
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        @if($absen->is_protected && $absen->auth_code)
                            <div class="px-4 py-2 bg-red-50 rounded-xl border border-red-100 group-hover:bg-[#800000] transition-colors group-hover:border-none">
                                <span class="text-[10px] font-black text-[#800000] tracking-[0.2em] group-hover:text-white">{{ $absen->auth_code }}</span>
                            </div>
                        @endif

                        <div class="h-8 w-[1px] bg-gray-100 mx-2"></div>

                        <button wire:click="edit({{ $absen->id }})" class="p-3 hover:bg-gray-50 rounded-xl text-gray-400 hover:text-black transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" stroke-width="2.5"/></svg>
                        </button>

                        <button wire:click="delete({{ $absen->id }})" wire:confirm="Destroy this slot?" class="p-3 hover:bg-red-50 rounded-xl text-gray-300 hover:text-red-600 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2.5"/></svg>
                        </button>
                    </div>
                </div>
            @empty
                <div class="py-24 text-center bg-gray-50 rounded-[3rem] border-2 border-dashed border-gray-100">
                    <p class="text-[10px] font-black text-gray-300 uppercase tracking-[0.5em]">No Attendance Slots Deployed</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
