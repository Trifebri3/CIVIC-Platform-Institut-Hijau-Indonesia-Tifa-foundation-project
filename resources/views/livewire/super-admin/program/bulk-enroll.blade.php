<?php

use App\Models\User;
use App\Models\Program;
use App\Models\ProgramParticipant;
use App\Mail\ProgramInvitationMail;
use App\Notifications\ProgramEnrolledNotification; // Pastikan file class ini ada
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

new class extends Component {
    use WithPagination;

    public $search = '';
    public $filter_year = '';
    public $filter_month = '';
    public $target_program_id = '';
    public $selected_users = [];
    public $select_all = false;

    public function mount() {
        $this->filter_year = date('Y');
    }

    // Reset pagination saat search berubah
    public function updatedSearch() { $this->resetPage(); }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selected_users = collect($this->users->items())
                ->filter(fn($user) => !($this->target_program_id && $user->isEnrolledIn($this->target_program_id)))
                ->pluck('id')->map(fn($id) => (string)$id)->toArray();
        } else {
            $this->selected_users = [];
        }
    }

    public function enrollBulk()
    {
        $this->validate([
            'target_program_id' => 'required|exists:programs,id',
            'selected_users' => 'required|array|min:1',
        ], [
            'target_program_id.required' => 'Pilih Programnya dulu, Bos!',
            'selected_users.required' => 'Centang usernya dulu dong.',
        ]);

        $program = Program::find($this->target_program_id);
        $count = 0;

        try {
            DB::transaction(function () use ($program, &$count) {
                foreach ($this->selected_users as $userId) {
                    $user = User::findOrFail($userId);

                    // Proteksi double enrollment
                    if ($user->isEnrolledIn($program->id)) continue;

                    // 1. Simpan ke Pivot Table (enrolled_programs)
                    $regNumber = 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(5));
                    $user->enrolledPrograms()->attach($program->id, [
                        'registration_number' => $regNumber,
                        'enrolment_method'    => 'manual_add',
                        'enrolled_at'         => now(),
                        'status'              => 'active',
                        'created_at'          => now(),
                        'updated_at'          => now(),
                    ]);

                    // 2. Kirim Notifikasi ke Dashboard (Tabel notifications)
                    // Pastikan App\Notifications\ProgramEnrolledNotification sudah dibuat
                    $user->notify(new ProgramEnrolledNotification($program));

                    // 3. Kirim Email Undangan
                    // Pakai send() biar langsung kirim untuk testing, ganti queue() jika worker jalan
                    Mail::to($user->email)->send(new ProgramInvitationMail($user, $program));

                    $count++;
                }
            });

            session()->flash('success', "🚀 MANTAP! $count Undangan meluncur ke Dashboard & Email User.");
            $this->reset(['selected_users', 'select_all']);

        } catch (\Exception $e) {
            Log::error('Bulk Enrollment Error: ' . $e->getMessage());
            session()->flash('error', 'Waduh Gagal: ' . $e->getMessage());
        }
    }

    public function getUsersProperty()
    {
        return User::query()
            ->with(['enrolledPrograms'])
            ->when($this->search, fn($q) =>
                $q->where(function($query) {
                    $query->where('name', 'like', "%{$this->search}%")
                          ->orWhere('email', 'like', "%{$this->search}%");
                })
            )
            ->when($this->filter_year, fn($q) => $q->whereYear('created_at', $this->filter_year))
            ->when($this->filter_month, fn($q) => $q->whereMonth('created_at', $this->filter_month))
            ->latest()
            ->paginate(15);
    }

    public function getProgramsProperty() {
        return Program::where('status', 'active')->get();
    }
}; ?>

<div class="max-w-6xl mx-auto space-y-6 pb-20">
    {{-- Notif Berhasil --}}
    @if (session()->has('success'))
        <div class="fixed top-10 right-10 z-[99] animate-bounce">
            <div class="bg-[#800000] text-white px-8 py-4 rounded-2xl shadow-[0_20px_50px_rgba(128,0,0,0.3)] font-black uppercase italic text-xs border-2 border-white/20">
                {{ session('success') }}
            </div>
        </div>
    @endif

    {{-- Error --}}
    @if (session()->has('error'))
        <div class="bg-red-100 border-l-4 border-red-600 p-4 rounded-xl text-red-700 text-xs font-bold uppercase">
            {{ session('error') }}
        </div>
    @endif

    {{-- Action Card: Header & Button --}}
    <div class="bg-black rounded-[3rem] p-10 shadow-2xl border border-white/10 flex flex-col md:flex-row justify-between items-center gap-8">
        <div class="flex items-center gap-6">
            <div class="w-16 h-16 bg-[#800000] rounded-3xl flex items-center justify-center shadow-lg shadow-red-900/40 relative">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" stroke-width="2" stroke-linecap="round"/></svg>
                <span class="absolute -top-2 -right-2 flex h-5 w-5">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-5 w-5 bg-white text-black text-[10px] items-center justify-center font-black italic">!</span>
                </span>
            </div>
            <div>
                <h1 class="text-3xl font-black text-white uppercase italic tracking-tighter leading-none">Smart Invite</h1>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.4em] mt-2 italic underline decoration-[#800000]">Email & Dashboard Notif Syncing...</p>
            </div>
        </div>

        <div class="flex items-center gap-3 w-full md:w-auto">
            <select wire:model.live="target_program_id" class="bg-white/5 border-none text-white font-black text-[10px] uppercase p-4 rounded-2xl min-w-[250px] focus:ring-2 focus:ring-[#800000]">
                <option value="" class="text-black">-- SELECT PROGRAM --</option>
                @foreach($this->programs as $program)
                    <option value="{{ $program->id }}" class="text-black">{{ $program->name }}</option>
                @endforeach
            </select>

            <button wire:click="enrollBulk"
                    wire:loading.attr="disabled"
                    class="bg-[#800000] hover:bg-white hover:text-black text-white px-10 py-4 rounded-2xl font-black text-[11px] uppercase tracking-widest transition-all shadow-xl active:scale-95 disabled:opacity-50 flex items-center gap-2">
                <span wire:loading.remove>Blast Invite ({{ count($selected_users) }})</span>
                <span wire:loading>Processing...</span>
            </button>
        </div>
    </div>

    {{-- Filter Search --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100">
        <input type="text" wire:model.live.debounce.500ms="search" placeholder="Cari Nama/Email..." class="w-full rounded-xl border-none bg-gray-50 text-xs font-bold p-4 focus:ring-2 focus:ring-[#800000]">

        <select wire:model.live="filter_year" class="rounded-xl border-none bg-gray-50 text-[10px] font-black p-4 uppercase tracking-tighter">
            <option value="">Tahun</option>
            @for($y = date('Y'); $y >= 2024; $y--) <option value="{{ $y }}">{{ $y }}</option> @endfor
        </select>

        <select wire:model.live="filter_month" class="rounded-xl border-none bg-gray-50 text-[10px] font-black p-4 uppercase tracking-tighter">
            <option value="">Bulan</option>
            @foreach(range(1, 12) as $m) <option value="{{ $m }}">{{ date('F', mktime(0,0,0,$m,1)) }}</option> @endforeach
        </select>

        <div class="flex items-center justify-between bg-black rounded-xl text-white px-6">
            <span class="text-[9px] font-black uppercase italic opacity-50">Total User</span>
            <span class="text-sm font-black italic">{{ $this->users->total() }}</span>
        </div>
    </div>

    {{-- Table Peserta --}}
    <div class="bg-white rounded-[3rem] shadow-2xl border border-gray-100 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50 border-b border-gray-100 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">
                <tr>
                    <th class="p-8 w-12 text-center">
                        <input type="checkbox" wire:model.live="select_all" class="rounded-md border-gray-300 text-[#800000] focus:ring-[#800000] w-5 h-5">
                    </th>
                    <th class="p-8">Identitas User</th>
                    <th class="p-8">Program Aktif</th>
                    <th class="p-8 text-right">Terdaftar</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($this->users as $user)
                    @php $isEnrolled = $target_program_id && $user->isEnrolledIn($target_program_id); @endphp
                    <tr class="group hover:bg-gray-50/80 transition-all {{ $isEnrolled ? 'opacity-30 pointer-events-none' : '' }}">
                        <td class="p-8 text-center">
                            @if(!$isEnrolled)
                                <input type="checkbox" wire:model.live="selected_users" value="{{ (string)$user->id }}"
                                       class="rounded-md border-gray-300 text-[#800000] focus:ring-[#800000] w-5 h-5 group-hover:scale-110 transition-transform">
                            @else
                                <div class="w-6 h-6 bg-green-500 rounded-full flex items-center justify-center mx-auto shadow-md">
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </div>
                            @endif
                        </td>
                        <td class="p-8">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-black rounded-2xl flex items-center justify-center text-white font-black italic shadow-lg text-xs border-2 border-white/10 group-hover:bg-[#800000] transition-colors">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-black text-gray-900 uppercase italic tracking-tighter leading-none">{{ $user->name }}</p>
                                    <p class="text-[9px] text-gray-400 font-bold lowercase mt-1">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="p-8">
                            <div class="flex flex-wrap gap-1">
                                @forelse($user->enrolledPrograms as $ep)
                                    <span class="px-2 py-1 bg-gray-100 border border-gray-200 rounded text-[7px] font-black text-gray-600 uppercase italic">
                                        {{ $ep->name }}
                                    </span>
                                @empty
                                    <span class="text-[7px] font-black text-gray-300 uppercase tracking-widest">No Program</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="p-8 text-right">
                            <p class="text-[9px] font-black text-gray-900 uppercase italic tracking-tighter">{{ $user->created_at->format('d M Y') }}</p>
                            <p class="text-[8px] text-gray-400 font-bold mt-1 uppercase">{{ $user->created_at->diffForHumans() }}</p>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="p-10 bg-gray-50 border-t border-gray-100">
            {{ $this->users->links() }}
        </div>
    </div>
</div>
