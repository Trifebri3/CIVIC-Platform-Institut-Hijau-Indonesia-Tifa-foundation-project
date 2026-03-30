<?php

use App\Models\{Announcement, Program, User};
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Notification;
use App\Notifications\AnnouncementNotification; // Pastikan Bos sudah buat ini (Step di bawah)

new class extends Component {
    use WithFileUploads;

    public $title, $message, $banner, $link_label, $link_url, $type = 'info', $target_type = 'global';
    public $selectedPrograms = [];
    public $announcementId;
    public $isEdit = false;
    public $isOpen = false;

    public function with()
    {
        return [
            'announcements' => Announcement::with('targetPrograms')->latest()->get(),
            'programs' => Program::all(), // Menarik data program
        ];
    }

    public function openModal()
    {
        $this->resetExcept(['type', 'target_type']);
        $this->isOpen = true;
        $this->isEdit = false;
    }

    public function save()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'message' => 'required',
            'type' => 'required',
            'target_type' => 'required',
            'banner' => 'nullable|image|max:2048',
        ]);

        $data = [
            'title' => $this->title,
            'message' => $this->message,
            'link_label' => $this->link_label,
            'link_url' => $this->link_url,
            'type' => $this->type,
            'target_type' => $this->target_type,
        ];

        if ($this->banner) {
            $data['banner'] = $this->banner->store('announcements', 'public');
        }

        if ($this->isEdit) {
            $ann = Announcement::find($this->announcementId);
            $ann->update($data);
            $ann->targetPrograms()->sync($this->target_type === 'program' ? $this->selectedPrograms : []);
        } else {
            $ann = Announcement::create($data);
            if ($this->target_type === 'program') {
                $ann->targetPrograms()->attach($this->selectedPrograms);
            }
        }

        // --- LOGIC KIRIM NOTIFIKASI KE USER & EMAIL ---
        $this->sendNotification($ann);

        $this->isOpen = false;
        $this->reset();
        $this->dispatch('swal', title: 'Berhasil!', text: 'Pengumuman telah disebar.', icon: 'success');
    }

    protected function sendNotification($ann)
    {
        $users = collect();

        if ($ann->target_type === 'global') {
            $users = User::all();
        } else {
            // Ambil user yang ikut program yang dipilih saja
            $users = User::whereHas('programs', function($q) {
                $q->whereIn('programs.id', $this->selectedPrograms);
            })->get();
        }

        // Kirim notifikasi (Ini yang bikin muncul di lonceng & email)
        // Pastikan Bos sudah menjalankan: php artisan make:notification AnnouncementNotification
        Notification::send($users, new \App\Notifications\AnnouncementNotification($ann));
    }

    public function edit($id)
    {
        $ann = Announcement::with('targetPrograms')->find($id);
        $this->announcementId = $id;
        $this->title = $ann->title;
        $this->message = $ann->message;
        $this->link_label = $ann->link_label;
        $this->link_url = $ann->link_url;
        $this->type = $ann->type;
        $this->target_type = $ann->target_type;
        $this->selectedPrograms = $ann->targetPrograms->pluck('id')->toArray();
        $this->isEdit = true;
        $this->isOpen = true;
    }

    public function delete($id)
    {
        Announcement::find($id)->delete();
    }
}; ?>

<div class="p-8 max-w-7xl mx-auto">
    {{-- Header Section --}}
    <div class="flex justify-between items-end mb-10">
        <div>
            <h1 class="text-4xl font-black uppercase italic tracking-tighter">Broadcast <span class="text-[#800000]">Center</span></h1>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-2">Kelola pengumuman global dan program.</p>
        </div>
        <button wire:click="openModal" class="bg-black text-white px-8 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-[#800000] transition-all">
            + Create Announcement
        </button>
    </div>

    {{-- List Table --}}
    <div class="bg-white border border-gray-100 rounded-3xl overflow-hidden shadow-sm">
        <table class="w-full text-left">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4 text-[9px] font-black uppercase text-gray-400">Banner & Info</th>
                    <th class="px-6 py-4 text-[9px] font-black uppercase text-gray-400">Target</th>
                    <th class="px-6 py-4 text-right text-[9px] font-black uppercase text-gray-400">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($announcements as $ann)
                <tr>
                    <td class="px-6 py-4 text-sm font-bold uppercase italic">{{ $ann->title }}</td>
                    <td class="px-6 py-4 text-[10px] font-black uppercase text-blue-500">{{ $ann->target_type }}</td>
                    <td class="px-6 py-4 text-right">
                        <button wire:click="edit({{ $ann->id }})" class="text-blue-500 font-black text-[10px] uppercase mx-2">Edit</button>
                        <button wire:click="delete({{ $ann->id }})" class="text-red-500 font-black text-[10px] uppercase">Hapus</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- MODAL --}}
    @if($isOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
        <div class="bg-white w-full max-w-2xl rounded-3xl shadow-2xl p-8 max-h-[90vh] overflow-y-auto">
            <h2 class="text-2xl font-black uppercase italic tracking-tighter mb-6">{{ $isEdit ? 'Edit' : 'Create' }} Announcement</h2>

            <form wire:submit="save" class="space-y-6">
                {{-- Judul --}}
                <div>
                    <label class="text-[9px] font-black uppercase tracking-widest text-gray-400">Judul Pengumuman</label>
                    <input type="text" wire:model="title" class="w-full border-gray-100 rounded-xl font-bold">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-[9px] font-black uppercase tracking-widest text-gray-400">Type</label>
                        <select wire:model="type" class="w-full border-gray-100 rounded-xl font-bold">
                            <option value="info">Info</option>
                            <option value="warning">Warning</option>
                            <option value="danger">Urgent</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-[9px] font-black uppercase tracking-widest text-gray-400">Target</label>
                        <select wire:model.live="target_type" class="w-full border-gray-100 rounded-xl font-bold">
                            <option value="global">Global</option>
                            <option value="program">Program Tertentu</option>
                        </select>
                    </div>
                </div>

                {{-- PROGRAM CHECKBOX (FIXED) --}}
                @if($target_type === 'program')
                <div class="p-4 bg-gray-50 rounded-2xl border border-dashed">
                    <p class="text-[9px] font-black uppercase text-gray-400 mb-2">Pilih Program:</p>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach($programs as $p)
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" wire:model="selectedPrograms" value="{{ $p->id }}" class="rounded text-[#800000]">
                            {{-- GANTI JADI $p->name --}}
                            <span class="text-[10px] font-black uppercase">{{ $p->name }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endif

                <div>
                    <label class="text-[9px] font-black uppercase tracking-widest text-gray-400">Pesan</label>
                    <textarea wire:model="message" class="w-full border-gray-100 rounded-xl"></textarea>
                </div>

                <div class="flex gap-4">
                    <button type="button" wire:click="$set('isOpen', false)" class="flex-1 border py-3 rounded-xl font-black uppercase text-[10px]">Cancel</button>
                    <button type="submit" class="flex-1 bg-black text-white py-3 rounded-xl font-black uppercase text-[10px] hover:bg-[#800000]">Broadcast Now</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
