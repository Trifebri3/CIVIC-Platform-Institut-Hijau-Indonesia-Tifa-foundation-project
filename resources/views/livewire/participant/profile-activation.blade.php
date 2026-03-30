<?php

use App\Models\ProfileTemplate;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

new class extends Component {
    use WithFileUploads;

    public $avatar;
    public $custom_fields = [];
    public $templates;

    public function mount()
    {
        $this->templates = ProfileTemplate::orderBy('order', 'asc')->get();

        foreach ($this->templates as $template) {
            $this->custom_fields[$template->field_name] = '';
        }
    }

 public function submitProfile()
{
    // Ambil instance user terbaru
    $user = \App\Models\User::find(Auth::id());

    // 1. Validasi Dinamis
    $rules = [
        'avatar' => 'nullable|image|max:2048',
    ];

    foreach ($this->templates as $template) {
        if ($template->validation_rules) {
            $rules['custom_fields.' . $template->field_name] = $template->validation_rules;
        }
    }

    $this->validate($rules);

    // 2. Handle Upload Avatar
    if ($this->avatar) {
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }
        $path = $this->avatar->store('avatars', 'public');
        $user->avatar = $path;
    }

    // 3. UPDATE KEDUA STATUS (SANGAT PENTING!)
    $user->is_profile_completed = 1;
    $user->is_activated = 1; // <--- TAMBAHKAN INI AGAR LOLOS DARI MIDDLEWARE GERBANG 1
    $user->save();

    // 4. REFRESH SESSION AUTH
    Auth::setUser($user->fresh());

    // 5. Simpan ke tabel profiles
    $user->profile()->updateOrCreate(
        ['user_id' => $user->id],
        ['custom_fields_values' => $this->custom_fields]
    );

    session()->flash('success', 'Profil dan Aktivasi Berhasil!');

    // 6. Redirect Standar (Tanpa navigate:true agar Middleware refresh total)
    return redirect()->to(route('user.dashboard'));
}
}; ?>

<div class="min-h-screen bg-[#FCFCFC] p-8 lg:p-20 flex justify-center items-center">
    <div class="max-w-2xl w-full bg-white rounded-[3rem] shadow-xl shadow-slate-200/50 p-10 lg:p-16 border border-slate-50">

        <div class="mb-10 text-center">
            <h2 class="text-3xl font-black uppercase tracking-tighter text-slate-900 italic">Lengkapi Profil</h2>
            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-[0.3em] mt-2 italic">Gerbang Terakhir Akses Platform</p>
        </div>

        <form wire:submit.prevent="submitProfile" class="space-y-8">
            {{-- Upload Avatar Section --}}
            <div class="flex flex-col items-center gap-4">
                <div class="relative group">
                    <div class="w-32 h-32 rounded-full overflow-hidden bg-slate-50 border-4 border-white shadow-xl relative ring-1 ring-slate-100">
                        @if ($avatar)
                            <img src="{{ $avatar->temporaryUrl() }}" class="w-full h-full object-cover">
                        @else
                            <div class="flex items-center justify-center h-full text-slate-200">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                        @endif
                    </div>
                    <label class="absolute bottom-0 right-0 bg-[#800000] w-10 h-10 rounded-full flex items-center justify-center text-white cursor-pointer hover:scale-110 transition shadow-lg border-4 border-white">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <input type="file" wire:model="avatar" class="hidden" accept="image/*">
                    </label>
                </div>
                <div wire:loading wire:target="avatar" class="text-[9px] font-black text-[#800000] uppercase animate-pulse">Sedang Memproses Foto...</div>
                @error('avatar') <span class="text-red-500 text-[10px] font-bold uppercase tracking-widest text-center">{{ $message }}</span> @enderror
            </div>

            {{-- Dynamic Fields --}}
            <div class="grid grid-cols-1 gap-6">
                @foreach($templates as $template)
                    <div class="space-y-2 group">
                        <label class="text-[10px] font-black uppercase text-slate-400 tracking-widest ml-4 group-focus-within:text-[#800000] transition-colors">
                            {{ $template->field_label }}
                        </label>

                        @if($template->field_type === 'textarea')
                            <textarea wire:model="custom_fields.{{ $template->field_name }}"
                                class="w-full p-6 bg-slate-50 rounded-[2rem] border-none focus:ring-4 focus:ring-red-50 text-slate-700 transition-all min-h-[120px]"
                                placeholder="Tuliskan {{ $template->field_label }} Anda..."></textarea>
                        @else
                            <input type="{{ $template->field_type }}"
                                wire:model="custom_fields.{{ $template->field_name }}"
                                class="w-full p-6 bg-slate-50 rounded-full border-none focus:ring-4 focus:ring-red-50 text-slate-700 transition-all"
                                placeholder="Masukkan {{ $template->field_label }}...">
                        @endif

                        @error('custom_fields.' . $template->field_name)
                            <span class="text-red-500 text-[9px] font-black uppercase ml-6">{{ $message }}</span>
                        @enderror
                    </div>
                @endforeach
            </div>
@if ($errors->any())
    <div class="mb-5 p-4 bg-red-50 rounded-2xl border border-red-100">
        <ul class="list-disc list-inside text-[10px] text-red-600 font-bold uppercase tracking-widest">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
            <div class="pt-4">
                <button type="submit" wire:loading.attr="disabled"
                    class="w-full py-6 bg-[#800000] text-white rounded-full font-black uppercase text-[11px] tracking-[0.4em] shadow-2xl shadow-red-900/30 hover:bg-black transition-all active:scale-95 flex items-center justify-center gap-3">
                    <span wire:loading.remove wire:target="submitProfile">Selesaikan Pendaftaran <i class="fa-solid fa-arrow-right ml-2"></i></span>
                    <span wire:loading wire:target="submitProfile">Sinkronisasi Data...</span>
                </button>
            </div>
        </form>
    </div>
</div>
