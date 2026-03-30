@component('pages.super-admin.layouts.app')
    <x-slot name="title">Ringkasan Sistem</x-slot>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-teal-50">
            <p class="text-sm text-gray-500 font-medium">Total Peserta</p>
            <h3 class="text-3xl font-bold text-civic-teal mt-1">{{ \App\Models\User::where('role', 'user')->count() }}</h3>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-teal-50">
            <p class="text-sm text-gray-500 font-medium">Program Aktif</p>
            <h3 class="text-3xl font-bold text-civic-teal mt-1">0</h3>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-teal-50">
            <p class="text-sm text-gray-500 font-medium">Pending Verification</p>
            <h3 class="text-3xl font-bold text-orange-500 mt-1">0</h3>
        </div>
    </div>
@endcomponent
