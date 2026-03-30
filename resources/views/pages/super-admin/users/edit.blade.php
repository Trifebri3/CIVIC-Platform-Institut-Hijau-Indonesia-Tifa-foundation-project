@component('pages.super-admin.layouts.app')
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="mb-6 flex items-center justify-between">
                <a href="{{ route('superadmin.users.index') }}" class="group flex items-center text-xs font-bold text-gray-500 uppercase tracking-widest hover:text-[#800000] transition-colors">
                    <svg class="w-4 h-4 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali ke Daftar Peserta
                </a>

                <span class="text-[10px] font-black text-gray-300 uppercase tracking-[0.3em]">Manajemen User v1.0</span>
            </div>
 @livewire('super-admin.user.edit-user', ['user' => $user])


            <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4 text-center">
                <div class="p-4 rounded-xl border border-dashed border-gray-200">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Status Akun</p>
                    <p class="text-sm font-bold text-green-600 uppercase mt-1">{{ $user->is_activated ? 'Terverifikasi' : 'Pending' }}</p>
                </div>
                <div class="p-4 rounded-xl border border-dashed border-gray-200">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Terdaftar Sejak</p>
                    <p class="text-sm font-bold text-gray-700 uppercase mt-1">{{ $user->created_at->format('d M Y') }}</p>
                </div>
                <div class="p-4 rounded-xl border border-dashed border-gray-200">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Terakhir Login</p>
                    <p class="text-sm font-bold text-gray-700 uppercase mt-1">{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Belum Pernah' }}</p>
                </div>
            </div>

        </div>
    </div>
@endcomponent
