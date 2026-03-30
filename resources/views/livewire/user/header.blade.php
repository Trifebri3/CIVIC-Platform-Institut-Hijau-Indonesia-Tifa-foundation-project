<?php

use Livewire\Volt\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

new class extends Component {
    // Fungsi untuk menandai semua notifikasi sudah dibaca
    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();

        // Memberi sinyal ke browser/komponen lain jika diperlukan
        $this->dispatch('notifications-updated');
    }
}; ?>

<header class="sticky top-0 z-40 flex h-16 lg:h-20 w-full items-center justify-between border-b border-gray-100 bg-white/80 px-5 lg:px-12 backdrop-blur-xl transition-all duration-300">

    {{-- LEFT SIDE: LOGO & BREADCRUMB --}}
    <div class="flex items-center">
        {{-- Logo Mobile --}}
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-7 w-auto lg:hidden mr-4 object-contain">

        {{-- Breadcrumb Desktop --}}
        <nav class="hidden lg:flex items-center space-x-3 text-[10px] font-bold uppercase tracking-[0.3em]">
            <span class="text-gray-300 text-[8px]">Platform</span>
            <svg class="h-2.5 w-2.5 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path>
            </svg>
            <span class="text-[#800000] drop-shadow-sm italic">
                {{ str_replace('-', ' ', Request::segment(2) ?? 'Dashboard') }}
            </span>
        </nav>
    </div>

    {{-- RIGHT SIDE: PROFILE & NOTIFICATION --}}
    <div class="flex items-center gap-3 lg:gap-6" x-data="{ notifOpen: false }">

        {{-- Info User & Role --}}
        <div class="hidden sm:flex flex-col items-end leading-tight border-r border-gray-100 pr-4 lg:pr-6">
            <p class="text-[12px] lg:text-[13px] font-extrabold tracking-tight text-gray-900 uppercase italic">
                {{ Auth::user()->name }}
            </p>
            <p class="text-[8px] lg:text-[9px] font-bold uppercase tracking-[0.15em] text-[#800000]/80 mt-0.5">
                {{ Auth::user()->role ?? 'Peserta Utama' }}
            </p>
        </div>

        {{-- Notification System --}}
        <div class="relative">
            {{-- Bell Trigger --}}
            <button @click="notifOpen = true"
                    class="relative p-2.5 text-gray-400 bg-gray-50 rounded-xl hover:bg-black hover:text-white transition-all duration-300 active:scale-90 border border-transparent">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>

                {{-- Reactive Badge --}}
                @php $unreadCount = auth()->user()->unreadNotifications->count(); @endphp
                @if($unreadCount > 0)
                    <span class="absolute -top-1 -right-1 flex h-4 w-4">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                        <span class="relative inline-flex items-center justify-center rounded-full h-4 w-4 bg-[#800000] text-white text-[7px] font-black border-2 border-white">
                            {{ $unreadCount }}
                        </span>
                    </span>
                @endif
            </button>

            {{-- SLIDE OVER PANEL (Teleported to Body for best UI) --}}
            <template x-teleport="body">
                <div x-show="notifOpen" class="fixed inset-0 z-[100] overflow-hidden">
                    {{-- Backdrop Blur --}}
                    <div x-show="notifOpen"
                         x-transition:enter="ease-in-out duration-500" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                         x-transition:leave="ease-in-out duration-500" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                         @click="notifOpen = false"
                         class="absolute inset-0 bg-black/40 backdrop-blur-sm transition-opacity"></div>

                    <div class="fixed inset-y-0 right-0 flex max-w-full pl-10">
                        {{-- Content Slide --}}
                        <div x-show="notifOpen"
                             x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                             x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
                             class="relative w-screen max-w-md">

                            <div class="flex h-full flex-col overflow-y-auto bg-white shadow-2xl rounded-l-[3rem] border-l border-gray-100">

                                {{-- Panel Header --}}
                                <div class="px-8 py-10 bg-black text-white rounded-bl-[3rem]">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h2 class="text-2xl font-black uppercase italic tracking-tighter">Notifications</h2>
                                            <p class="text-[10px] font-bold uppercase tracking-[0.3em] text-[#800000] mt-1">Inbox Activity</p>
                                        </div>
                                        <button @click="notifOpen = false" class="p-3 bg-white/10 hover:bg-[#800000] rounded-2xl transition-all">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                </div>

                                {{-- Notif List --}}
                                <div class="relative flex-1 px-4 py-6 sm:px-6">
                                    <div class="space-y-4">
                                        @forelse(auth()->user()->unreadNotifications as $notification)
                                            <div class="group relative bg-gray-50 hover:bg-white hover:shadow-xl hover:shadow-gray-200/50 p-6 rounded-[2rem] border border-transparent hover:border-gray-100 transition-all duration-300">
                                                <div class="flex items-start gap-4">
                                                    <div class="w-12 h-12 bg-black text-white rounded-2xl flex items-center justify-center flex-shrink-0 group-hover:bg-[#800000] transition-colors shadow-lg">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z" stroke-width="2"/></svg>
                                                    </div>
                                                    <div class="flex-1">
                                                        <div class="flex justify-between items-start">
                                                            <span class="text-[10px] font-black uppercase italic text-[#800000]">New Program</span>
                                                            <span class="text-[8px] font-bold text-gray-400 uppercase tracking-tighter">{{ $notification->created_at->diffForHumans() }}</span>
                                                        </div>
                                                        <h4 class="text-sm font-black text-gray-900 mt-1 uppercase italic tracking-tighter">{{ $notification->data['program_name'] ?? 'Notification' }}</h4>
                                                        <p class="text-[11px] text-gray-500 font-medium mt-1 leading-relaxed">{{ $notification->data['message'] ?? 'Click for details.' }}</p>

<a href="{{ route('notification.detail', $notification->id) }}"
   @click.stop
   class="px-4 py-2 bg-black text-white text-[9px] font-black uppercase rounded-lg hover:bg-[#800000] transition-all italic">
   View Detail
</a>

                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="flex flex-col items-center justify-center py-20 opacity-20 text-center">
                                                <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0l-8 5-8-5" stroke-width="1.5"/></svg>
                                                <p class="text-[10px] font-black uppercase mt-4 italic tracking-widest">Inbox Empty</p>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>

                                {{-- Action Footer --}}
                                @if($unreadCount > 0)
                                    <div class="p-8 border-t border-gray-50">
                                        <button wire:click="markAllAsRead" @click="notifOpen = false"
                                                class="w-full py-5 bg-[#800000] text-white rounded-[1.5rem] font-black uppercase italic tracking-[0.2em] shadow-xl shadow-red-900/20 hover:scale-[1.02] transition-transform">
                                            Mark All as Read
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        {{-- Avatar Section --}}
        <a href="{{ route('user.settings') }}" class="relative group cursor-pointer transition-transform active:scale-95">
            <div class="p-0.5 rounded-full border-2 border-gray-50 group-hover:border-[#800000]/40 transition-all duration-500">
                @if(Auth::user()->avatar && \Storage::disk('public')->exists(Auth::user()->avatar))
                    <img class="h-9 w-9 lg:h-11 lg:w-11 rounded-full object-cover shadow-sm ring-1 ring-black/5" src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar">
                @else
                    <img class="h-9 w-9 lg:h-11 lg:w-11 rounded-full object-cover shadow-sm" src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=800000&color=ffffff&bold=true&font-size=0.35&uppercase=true" alt="Avatar">
                @endif
            </div>
            <span class="absolute bottom-0.5 right-0.5 h-3 w-3 rounded-full border-2 border-white bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.6)] z-10"></span>
        </a>
    </div>
</header>
