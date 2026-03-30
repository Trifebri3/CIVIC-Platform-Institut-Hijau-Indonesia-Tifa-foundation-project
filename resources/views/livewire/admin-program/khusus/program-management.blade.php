<?php

use Livewire\Volt\Component;
use App\Models\User;
use App\Models\ProgramKhusus;
use App\Models\ProgramKhususParticipant;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $search = '';
    public $selectedProgram = '';
    public $accessRole = 'member';
    public $selectedUsers = [];

    // Reset pagination monitor kalau lagi search
    public function updatingSearch() { $this->resetPage('participantsPage'); }

    public function getProgramsProperty()
    {
        return ProgramKhusus::where('is_active', true)->orderBy('nama_program', 'asc')->get();
    }

    // LIST KANDIDAT: Scrollable, limit 50 biar ringan
    public function getCandidatesProperty()
    {
        $query = User::where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->whereIn('role', ['user', 'participant']);

        if ($this->selectedProgram) {
            $query->whereDoesntHave('specialProgramDetails', function($q) {
                $q->where('program_khusus_id', $this->selectedProgram);
            });
        }

        return $query->limit(50)->get();
    }

public function processWhitelisting($forceJoin = false)
{
    if (!$this->selectedProgram || empty($this->selectedUsers)) {
        session()->flash('error', 'Pilih Program & User dulu!');
        return;
    }

    foreach ($this->selectedUsers as $userId) {
        $participant = ProgramKhususParticipant::updateOrCreate(
            ['user_id' => $userId, 'program_khusus_id' => $this->selectedProgram],
            [
                'access_role' => $this->accessRole,
                'is_active' => $forceJoin,
                'invited_at' => now(),
                'joined_at' => $forceJoin ? now() : null,
            ]
        );

        $participant->load('program');
        $user = User::find($userId);

        if ($user) {
            // Ini yang memicu masuk ke tabel notifications & email
            $user->notify(new \App\Notifications\ProgramInvitationNotification($participant));
        }
    }

    $this->selectedUsers = [];
    $this->search = '';

    // Gunakan emit/dispatch jika header menggunakan Livewire agar lonceng langsung update
    $this->dispatch('notification-sent');

    session()->flash('success', 'Akses Berhasil Diberikan & Notifikasi Terkirim! 🚀');
}

    public function removeAccess($id)
    {
        ProgramKhususParticipant::find($id)->delete();
        session()->flash('success', 'Akses dicabut!');
    }

    public function getParticipantsProperty()
    {
        return ProgramKhususParticipant::with(['user', 'program'])
            ->latest()
            ->paginate(10, ['*'], 'participantsPage');
    }
}; ?>

<div class="p-8 space-y-10 bg-[#fafafa] min-h-screen font-sans relative">

    {{-- ALERT NOTIFIKASI --}}
    <div class="fixed top-5 right-5 z-[999] space-y-3">
        @if (session()->has('success'))
            <div class="bg-black text-white px-8 py-4 rounded-2xl shadow-2xl border-l-4 border-green-500 animate-bounce text-xs font-black uppercase italic">
                {{ session('success') }}
            </div>
        @endif
        @if (session()->has('error'))
            <div class="bg-[#800000] text-white px-8 py-4 rounded-2xl shadow-2xl text-xs font-black uppercase italic">
                {{ session('error') }}
            </div>
        @endif
    </div>

    {{-- HEADER --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-4xl font-black italic uppercase tracking-tighter text-slate-800">
                VIP <span class="text-[#800000]">Whitelist</span>
            </h1>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.3em] mt-1 italic">Civic Platform Enrollment System</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
        {{-- KIRI: SELECTION PANEL --}}
        <div class="lg:col-span-5 space-y-6">
            <div class="bg-white p-8 rounded-[3rem] border border-gray-100 shadow-2xl">
                <div class="space-y-6">

                    {{-- 1. PILIH PROGRAM --}}
                    <div>
                        <label class="text-[9px] font-black uppercase text-gray-400 ml-4 mb-2 block italic tracking-widest">1. Target Program</label>
                        <select wire:model.live="selectedProgram" class="w-full bg-gray-50 border-none rounded-2xl px-6 py-4 text-sm font-bold focus:ring-2 focus:ring-[#800000] cursor-pointer">
                            <option value="">-- Pilih Program Master --</option>
                            @foreach($this->programs as $prog)
                                <option value="{{ $prog->id }}">{{ $prog->nama_program }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 2. SEARCH & SCROLL LIST --}}
                    <div>
                        <label class="text-[9px] font-black uppercase text-gray-400 ml-4 mb-2 block italic tracking-widest">2. Search & Checklist Users</label>
                        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Ketik nama mahasiswa..."
                               class="w-full bg-gray-50 border-none rounded-2xl px-6 py-4 text-sm font-bold focus:ring-2 focus:ring-[#800000] mb-4 shadow-inner">

                        {{-- AREA SCROLL --}}
                        <div class="max-h-80 overflow-y-auto pr-2 space-y-2 custom-scrollbar border-b border-gray-50 pb-4">
                            @forelse($this->candidates as $user)
                                <label wire:key="cand-{{ $user->id }}" class="flex items-center justify-between p-4 {{ in_array($user->id, $selectedUsers) ? 'bg-[#800000]/5 border-2 border-[#800000]/20' : 'bg-gray-50 border-2 border-transparent' }} rounded-2xl cursor-pointer hover:bg-gray-100 transition-all group">
                                    <div class="flex items-center gap-4">
                                        <input type="checkbox" wire:model.live="selectedUsers" value="{{ $user->id }}"
                                               class="w-6 h-6 rounded-lg border-none bg-white text-[#800000] focus:ring-0 shadow-sm cursor-pointer">
                                        <div>
                                            <p class="text-[11px] font-black uppercase italic text-slate-800 group-hover:text-[#800000]">{{ $user->name }}</p>
                                            <p class="text-[9px] font-bold text-gray-400">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                    <span class="text-[8px] font-black text-gray-300">#{{ $user->id }}</span>
                                </label>
                            @empty
                                <div class="py-12 text-center">
                                    <p class="text-[10px] font-black text-gray-300 uppercase italic">User tidak ditemukan.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- 3. ACTION BUTTONS --}}
                    @if(count($selectedUsers) > 0)
                    <div class="grid grid-cols-2 gap-4 pt-4">
                        <button type="button" wire:click="processWhitelisting(false)" class="bg-black text-white p-4 rounded-2xl text-[10px] font-black uppercase italic hover:bg-slate-800 transition-all shadow-lg">
                            Send Invite ({{ count($selectedUsers) }})
                        </button>
                        <button type="button" wire:click="processWhitelisting(true)" class="bg-[#800000] text-white p-4 rounded-2xl text-[10px] font-black uppercase italic hover:bg-red-900 transition-all shadow-lg">
                            Force Join ({{ count($selectedUsers) }})
                        </button>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- KANAN: MONITORING --}}
        <div class="lg:col-span-7">
            <div class="bg-white rounded-[3rem] border border-gray-100 shadow-2xl overflow-hidden">
                <div class="p-8 border-b border-gray-50 flex justify-between items-center bg-gray-50/50">
                    <h3 class="text-sm font-black uppercase italic tracking-widest">Active <span class="text-[#800000]">Whitelist Monitor</span></h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th class="px-8 py-4 text-[9px] font-black uppercase text-gray-400 italic">Participant</th>
                                <th class="px-8 py-4 text-[9px] font-black uppercase text-gray-400 italic">Program</th>
                                <th class="px-8 py-4 text-right text-[9px] font-black uppercase text-gray-400 italic">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($this->participants as $p)
                            <tr wire:key="part-{{ $p->id }}" class="hover:bg-gray-50/50 transition-all">
                                <td class="px-8 py-5">
                                    <p class="text-[11px] font-black uppercase italic text-slate-800">{{ $p->user->name ?? 'User Hilang' }}</p>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $p->is_active ? 'bg-green-500' : 'bg-yellow-500' }}"></span>
                                        <span class="text-[8px] font-black uppercase italic {{ $p->is_active ? 'text-green-600' : 'text-yellow-600' }}">
                                            {{ $p->is_active ? 'Active' : 'Pending' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-8 py-5">
                                    <span class="text-[10px] font-black uppercase italic text-slate-600">{{ $p->program->nama_program ?? '-' }}</span>
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <button type="button" wire:click="removeAccess({{ $p->id }})" wire:confirm="Hapus akses?" class="p-2 text-gray-300 hover:text-red-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-8 py-20 text-center text-[10px] font-black text-gray-300 uppercase italic tracking-widest">No Records Found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-6 bg-gray-50/30">
                    {{ $this->participants->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- STYLE DI DALAM DIV ROOT --}}
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #800000; border-radius: 10px; }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }
        .animate-bounce { animation: bounce 1s infinite; }
    </style>
</div>
