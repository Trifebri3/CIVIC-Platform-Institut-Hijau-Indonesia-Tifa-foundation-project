<?php

use App\Models\User;
use App\Models\Program;
use App\Models\ProgramParticipant;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

new class extends Component {
    use WithPagination;

    // Filters
    public $search = '';
    public $filter_year = '';
    public $filter_month = '';
    public $target_program_id = '';

    // Selection
    public $selected_users = [];
    public $select_all = false;

    public function mount()
    {
        // Set default ke tahun sekarang agar table tidak kosong di awal
        $this->filter_year = date('Y');
    }

    // Reset pagination kalau filter berubah
    public function updatedSearch() { $this->resetPage(); }
    public function updatedFilterYear() { $this->resetPage(); }
    public function updatedFilterMonth() { $this->resetPage(); }

    public function updatedSelectAll($value)
    {
        if ($value) {
            // Hanya ambil user yang muncul di halaman saat ini yang BELUM terdaftar
            $this->selected_users = collect($this->users->items())
                ->filter(function($user) {
                    return !($this->target_program_id && $user->isEnrolledIn($this->target_program_id));
                })
                ->pluck('id')
                ->map(fn($id) => (string)$id)
                ->toArray();
        } else {
            $this->selected_users = [];
        }
    }

    public function enrollBulk()
    {
        // 1. Validasi
        if (empty($this->target_program_id)) {
            session()->flash('error', 'Pilih Program tujuannya dulu bos!');
            return;
        }

        if (count($this->selected_users) === 0) {
            session()->flash('error', 'Centang dulu usernya minimal satu!');
            return;
        }

        $program = Program::find($this->target_program_id);
        $count = 0;

        try {
DB::transaction(function () use ($program, &$count) {
                foreach ($this->selected_users as $userId) {
                    $user = User::findOrFail($userId);

                    if ($user->isEnrolledIn($program->id)) continue;

                    // Pastikan variabel ini benar-benar string
                    $regNumber = (string) 'BULK-' . date('Ymd') . '-' . strtoupper(Str::random(4)) . '-' . $user->id;

                    // Pakai array biasa, pastikan value-nya di-cast ke string
                    $user->enrolledPrograms()->attach((int) $program->id, [
                        'registration_number' => $regNumber,
                        'enrolment_method'    => 'manual_add', // Pastikan ini string statis
                        'enrolled_at'         => now()->format('Y-m-d H:i:s'),
                        'status'              => 'active',
                        'created_at'          => now(),
                        'updated_at'          => now(),
                    ]);

                    $count++;
                }
            });

            if ($count > 0) {
                session()->flash('success', "BERHASIL! $count peserta dimasukkan ke program {$program->name}");
            } else {
                session()->flash('error', "Tidak ada peserta baru yang ditambahkan (mungkin sudah terdaftar semua).");
            }

            // Reset selection setelah berhasil
            $this->selected_users = [];
            $this->select_all = false;

        } catch (\Exception $e) {
            session()->flash('error', 'Gagal simpan: ' . $e->getMessage());
        }
    }

    public function getUsersProperty()
    {
        return User::query()
            ->with(['enrolledPrograms']) // Eager loading biar nggak N+1 query
            ->when($this->search, function($q) {
                $q->where(function($sq) {
                    $sq->where('name', 'like', "%{$this->search}%")
                       ->orWhere('email', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filter_year, fn($q) => $q->whereYear('created_at', $this->filter_year))
            ->when($this->filter_month, fn($q) => $q->whereMonth('created_at', $this->filter_month))
            ->latest()
            ->paginate(15);
    }

    public function getProgramsProperty()
    {
        return Program::where('status', 'active')->orderBy('name', 'asc')->get();
    }
}; ?>

<div class="max-w-6xl mx-auto space-y-6 pb-20">
    {{-- Notifikasi --}}
    @if (session()->has('success'))
        <div class="fixed top-5 right-5 z-50 animate-bounce">
            <div class="bg-green-600 text-white px-8 py-4 rounded-2xl shadow-2xl font-black uppercase italic text-xs tracking-widest">
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-600 text-white p-4 rounded-2xl font-black uppercase text-[10px] italic text-center mb-4">
            {{ session('error') }}
        </div>
    @endif

    {{-- Header Card --}}
    <div class="bg-black rounded-[3rem] p-10 shadow-2xl border border-white/10 relative overflow-hidden">
        <div class="absolute top-0 right-0 p-10 opacity-10">
            <svg class="w-32 h-32 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-5-9h10v2H7z"/></svg>
        </div>

        <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-8">
            <div class="flex items-center gap-6">
                <div class="w-16 h-16 bg-[#800000] rounded-3xl flex items-center justify-center shadow-lg shadow-red-900/50">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" stroke-width="2" stroke-linecap="round"/></svg>
                </div>
                <div>
                    <h1 class="text-3xl font-black text-white uppercase italic tracking-tighter leading-none">Mass Enrollment</h1>
                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-[0.4em] mt-2">Force assign users to learning systems</p>
                </div>
            </div>

            <div class="flex items-center gap-3 w-full md:w-auto bg-white/5 p-2 rounded-[2rem] border border-white/5">
                <select wire:model.live="target_program_id" class="bg-transparent border-none text-white font-black text-[10px] uppercase tracking-widest px-6 py-2 focus:ring-0 min-w-[200px]">
                    <option value="" class="text-black">-- SELECT TARGET PROGRAM --</option>
                    @foreach($this->programs as $program)
                        <option value="{{ $program->id }}" class="text-black">{{ $program->name }}</option>
                    @endforeach
                </select>

                <button wire:click="enrollBulk" wire:loading.attr="disabled" class="bg-[#800000] hover:bg-white hover:text-black text-white px-8 py-4 rounded-[1.5rem] font-black text-[11px] uppercase tracking-widest transition-all shadow-xl active:scale-95 disabled:opacity-50">
                    <span wire:loading.remove>ENROLL ({{ count($selected_users) }})</span>
                    <span wire:loading>Processing...</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100">
        <div class="relative">
            <input type="text" wire:model.live.debounce.500ms="search" placeholder="Search Identity..." class="w-full rounded-xl border-none bg-gray-50 text-xs font-bold p-4 focus:ring-2 focus:ring-[#800000]">
        </div>

        <select wire:model.live="filter_year" class="rounded-xl border-none bg-gray-50 text-[10px] font-black uppercase p-4 focus:ring-2 focus:ring-[#800000]">
            <option value="">All Years</option>
            @for($y = date('Y'); $y >= 2023; $y--) <option value="{{ $y }}">{{ $y }}</option> @endfor
        </select>

        <select wire:model.live="filter_month" class="rounded-xl border-none bg-gray-50 text-[10px] font-black uppercase p-4 focus:ring-2 focus:ring-[#800000]">
            <option value="">All Months</option>
            @foreach(range(1, 12) as $m) <option value="{{ $m }}">{{ date('F', mktime(0,0,0,$m,1)) }}</option> @endforeach
        </select>

        <div class="flex items-center justify-between px-6 bg-gray-900 rounded-xl text-white">
            <span class="text-[9px] font-black uppercase tracking-widest italic">Found</span>
            <span class="text-sm font-black italic">{{ $this->users->total() }} Users</span>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="bg-white rounded-[3rem] shadow-2xl border border-gray-100 overflow-hidden">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="p-8 w-12 text-center">
                        <input type="checkbox" wire:model.live="select_all" class="rounded-md border-gray-300 text-[#800000] focus:ring-[#800000] w-5 h-5">
                    </th>
                    <th class="p-8 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Participant Identity</th>
                    <th class="p-8 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Status & Programs</th>
                    <th class="p-8 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-right">Registered At</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($this->users as $user)
                    @php
                        $isAlreadyEnrolled = $target_program_id && $user->isEnrolledIn($target_program_id);
                    @endphp
                    <tr class="group transition-all hover:bg-gray-50/80 {{ $isAlreadyEnrolled ? 'bg-gray-50/30' : '' }}">
                        <td class="p-8 text-center">
                            @if(!$isAlreadyEnrolled)
                                <input type="checkbox" wire:model.live="selected_users" value="{{ (string)$user->id }}"
                                       class="rounded-md border-gray-300 text-[#800000] focus:ring-[#800000] w-5 h-5 transition-transform group-hover:scale-110">
                            @else
                                <svg class="w-5 h-5 text-green-500 mx-auto" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/></svg>
                            @endif
                        </td>
                        <td class="p-8">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-black rounded-2xl flex items-center justify-center text-white font-black italic shadow-lg">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-black text-gray-900 uppercase italic tracking-tighter">{{ $user->name }}</p>
                                    <p class="text-[10px] text-gray-400 font-bold lowercase tracking-tight">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="p-8">
                            <div class="flex flex-wrap gap-2">
                                @forelse($user->enrolledPrograms as $ep)
                                    <span class="px-3 py-1.5 bg-white border border-gray-100 rounded-lg text-[8px] font-black text-gray-600 uppercase italic shadow-sm">
                                        {{ $ep->name }}
                                    </span>
                                @empty
                                    <span class="text-[9px] font-black text-gray-300 uppercase italic tracking-widest">Fresh Account</span>
                                @endforelse

                                @if($isAlreadyEnrolled)
                                    <span class="px-3 py-1.5 bg-[#800000] text-white rounded-lg text-[8px] font-black uppercase italic animate-pulse">
                                        TARGET REACHED
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="p-8 text-right">
                            <p class="text-[10px] font-black text-gray-900 uppercase italic">{{ $user->created_at->format('M d, Y') }}</p>
                            <p class="text-[9px] text-gray-400 font-bold">{{ $user->created_at->diffForHumans() }}</p>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-20 text-center">
                            <p class="text-[10px] font-black text-gray-300 uppercase tracking-[0.5em] italic">No user data found for this period</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="p-10 bg-gray-50 border-t border-gray-100">
            {{ $this->users->links() }}
        </div>
    </div>
</div>
