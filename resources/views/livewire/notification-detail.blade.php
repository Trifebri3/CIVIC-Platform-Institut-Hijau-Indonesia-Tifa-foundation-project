<?php

use Livewire\Volt\Component;
use Illuminate\Notifications\DatabaseNotification;

new class extends Component {
    public $notification;

    public function mount($id)
    {
        // Cari notifikasi berdasarkan ID milik user yang sedang login
        $this->notification = auth()->user()->notifications()->findOrFail($id);

        // Otomatis tandai sebagai sudah dibaca saat dibuka
        if ($this->notification->unread()) {
            $this->notification->markAsRead();
            $this->dispatch('notifications-updated'); // Update badge di header
        }
    }

    public function deleteNotification()
    {
        $this->notification->delete();
        session()->flash('success', 'Notification removed.');
        return redirect()->route('dashboard'); // Sesuaikan route tujuan setelah hapus
    }
}; ?>

<div class="max-w-3xl mx-auto py-12 px-6 antialiased">
    {{-- Breadcrumb Sederhana --}}
    <div class="flex items-center gap-3 mb-8 text-[10px] font-black uppercase tracking-[0.3em] text-gray-400 italic">
        <a href="/" class="hover:text-black transition-colors">Dashboard</a>
        <svg class="w-2 h-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="3"/></svg>
        <span class="text-[#800000]">Notification Detail</span>
    </div>

    <div class="bg-white rounded-[3rem] shadow-2xl shadow-gray-200/50 border border-gray-50 overflow-hidden">
        {{-- Header Detail --}}
        <div class="p-10 lg:p-14 border-b border-gray-50 bg-gray-50/30">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="flex items-center gap-5">
                    <div class="w-16 h-16 bg-black text-white rounded-[1.5rem] flex items-center justify-center shadow-xl shadow-black/10">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-[#800000] uppercase tracking-[0.4em] mb-1 italic">
                            {{ $notification->data['type'] ?? 'System Alert' }}
                        </p>
                        <h1 class="text-3xl font-black text-gray-900 uppercase italic tracking-tighter leading-none">
                            {{ $notification->data['program_name'] ?? 'Notification Overview' }}
                        </h1>
                    </div>
                </div>
                <div class="text-left md:text-right">
                    <p class="text-[9px] font-black text-gray-300 uppercase tracking-widest italic">Received on</p>
                    <p class="text-sm font-bold text-gray-800 italic">{{ $notification->created_at->format('M d, Y • H:i') }}</p>
                </div>
            </div>
        </div>

        {{-- Content Body --}}
        <div class="p-10 lg:p-14">
            <div class="prose prose-slate max-w-none">
                <p class="text-lg text-gray-600 leading-relaxed font-medium italic mb-10">
                    {{ $notification->data['message'] ?? 'No message content available.' }}
                </p>
            </div>

            {{-- Dynamic Action Button --}}
            @if(isset($notification->data['action_url']))
                <a href="{{ $notification->data['action_url'] }}"
                   class="inline-flex items-center gap-4 bg-black text-white px-10 py-5 rounded-2xl font-black text-[11px] uppercase tracking-[0.3em] italic hover:bg-[#800000] transition-all hover:shadow-2xl hover:shadow-red-900/20 active:scale-95">
                    Go to Resource
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M14 5l7 7-7 7M5 12h16" stroke-width="3"/></svg>
                </a>
            @endif

            <hr class="my-12 border-gray-100">

            {{-- Danger Zone --}}
            <div class="flex items-center justify-between bg-red-50/50 p-6 rounded-2xl border border-red-100">
                <div>
                    <p class="text-[9px] font-black text-red-800 uppercase tracking-widest">Delete this log?</p>
                    <p class="text-[8px] font-bold text-red-400 uppercase italic">This action cannot be undone.</p>
                </div>
                <button wire:click="deleteNotification" wire:confirm="Are you sure you want to delete this notification?"
                        class="px-5 py-2.5 bg-white text-red-600 text-[9px] font-black uppercase rounded-xl border border-red-100 hover:bg-red-600 hover:text-white transition-all">
                    Remove
                </button>
            </div>
        </div>
    </div>

    {{-- Footer Info --}}
    <div class="mt-8 text-center">
        <p class="text-[9px] font-black text-gray-300 uppercase tracking-[0.5em] italic">Informatics Notification System v1.2</p>
    </div>
</div>
