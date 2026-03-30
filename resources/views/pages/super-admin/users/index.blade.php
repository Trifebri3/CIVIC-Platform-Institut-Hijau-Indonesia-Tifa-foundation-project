@component('pages.super-admin.layouts.app')
    <x-slot name="title">Manajemen Peserta</x-slot>

    <div class="space-y-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Daftar Peserta</h2>
                <p class="text-sm text-gray-500">Mengelola dan memantau seluruh aktivitas akun peserta CIVIC.</p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('superadmin.export') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Export CSV
                </a>
                <a href="{{ route('superadmin.export.pdf') }}" class="inline-flex items-center px-4 py-2 bg-[#800000] border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#a52a2a] transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    Cetak PDF
                </a>

            <a href="{{ route('superadmin.profile-settings') }}"
           class="group flex items-center gap-3 rounded-xl px-4 py-3.5 transition-all duration-200 {{ Request::routeIs('superadmin.profile-settings') ? 'bg-gradient-to-r from-[#800000] to-[#a52a2a] text-white shadow-lg shadow-red-900/20' : 'text-gray-500 hover:bg-red-50 hover:text-[#800000]' }}">
            <svg class="h-5 w-5 {{ Request::routeIs('superadmin.profile-settings') ? 'text-white' : 'text-gray-400 group-hover:text-[#800000]' }}"
                 fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                <circle cx="12" cy="12" r="3"></circle>
            </svg>
            <span class="text-sm font-semibold">Format Profile</span>
        </a>
            </div>
        </div>
<div id="modalImport" class="fixed inset-0 z-[60] hidden bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-md rounded-3xl overflow-hidden shadow-2xl border-t-8 border-[#800000] animate-fadeIn">
        <form action="{{ route('superadmin.users.import') }}" method="POST" enctype="multipart/form-data" class="p-8">
            @csrf
            <div class="flex items-center gap-3 mb-6">
                <div class="p-2 bg-red-50 rounded-lg text-[#800000]">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a2 2 0 00-2-2H5a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2zm0 0h2a2 2 0 002-2v-2a2 2 0 00-2-2H9m12 10V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2z"></path></svg>
                </div>
                <div>
                    <h3 class="text-lg font-black text-gray-800 uppercase italic">Import Data Massal</h3>
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Gunakan format CSV/XLSX</p>
                </div>
            </div>

            <div class="space-y-4">
                <div class="border-2 border-dashed border-gray-100 rounded-2xl p-6 text-center hover:border-[#800000] transition-colors group">
                    <input type="file" name="file" id="fileInput" class="hidden" required onchange="updateFileName(this)">
                    <label for="fileInput" class="cursor-pointer">
                        <svg class="h-10 w-10 text-gray-300 mx-auto mb-2 group-hover:text-[#800000]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                        <p id="fileNameDisplay" class="text-sm font-medium text-gray-500">Klik untuk pilih file template</p>
                    </label>
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-4 border-t pt-6">
                <button type="button" onclick="closeModal('modalImport')" class="text-xs font-bold text-gray-400 uppercase tracking-widest">Batal</button>
                <button type="submit" class="bg-[#800000] text-white px-8 py-3 rounded-xl font-bold text-xs uppercase tracking-widest shadow-lg shadow-red-900/30 hover:bg-black transition-all">
                    Mulai Import
                </button>
            </div>
        </form>
    </div>
</div>
<div class="flex items-center gap-3">
    <button onclick="document.getElementById('modalManual').classList.remove('hidden')"
            class="bg-white border-2 border-[#800000] text-[#800000] px-5 py-2 rounded-xl font-bold text-xs uppercase hover:bg-red-50 transition">
        + Buat Akun Manual
    </button>

    <div class="flex items-center bg-[#800000] rounded-xl shadow-lg shadow-red-900/20 overflow-hidden">
        <button onclick="document.getElementById('modalImport').classList.remove('hidden')"
                class="px-5 py-2.5 text-white font-bold text-xs uppercase border-r border-white/20 hover:bg-[#a52a2a] transition">
            ↑ Import Excel
        </button>
<a href="{{ route('superadmin.users.template') }}"
   class="p-2.5 text-white hover:bg-[#a52a2a] transition border-l border-white/20"
   title="Download Template CSV">
    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
    </svg>
</a>
    </div>
</div>

<div id="modalManual" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-lg rounded-3xl overflow-hidden shadow-2xl border-t-8 border-[#800000]">
        <form action="{{ route('superadmin.users.store') }}" method="POST" class="p-8">
            @csrf
            <h3 class="text-xl font-black text-gray-800 mb-6 uppercase italic">Registrasi Akun Manual</h3>

            <div class="space-y-4">
                <div>
                    <label class="text-[10px] font-black text-[#800000] uppercase tracking-widest ml-1">Nama Lengkap</label>
                    <input type="text" name="name" required class="w-full rounded-xl border-gray-100 bg-gray-50 focus:border-[#800000] focus:ring-[#800000] text-sm font-medium">
                </div>
                <div>
                    <label class="text-[10px] font-black text-[#800000] uppercase tracking-widest ml-1">Email Peserta</label>
                    <input type="email" name="email" required class="w-full rounded-xl border-gray-100 bg-gray-50 focus:border-[#800000] focus:ring-[#800000] text-sm font-medium">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-[10px] font-black text-[#800000] uppercase tracking-widest ml-1">Password</label>
                        <input type="password" name="password" required class="w-full rounded-xl border-gray-100 bg-gray-50 focus:border-[#800000] focus:ring-[#800000] text-sm">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-[#800000] uppercase tracking-widest ml-1">Role</label>
                        <select name="role" class="w-full rounded-xl border-gray-100 bg-gray-50 focus:border-[#800000] focus:ring-[#800000] text-sm font-bold uppercase">
                            <option value="user">USER (Peserta)</option>
                            <option value="adminprogram">ADMIN PROGRAM</option>
                            <option value="superadmin">SUPERADMIN</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-4 border-t pt-6">
                <button type="button" onclick="document.getElementById('modalManual').classList.add('hidden')" class="text-xs font-bold text-gray-400 uppercase tracking-widest">Batal</button>
                <button type="submit" class="bg-[#800000] text-white px-8 py-3 rounded-xl font-bold text-xs uppercase tracking-widest shadow-lg shadow-red-900/30">Simpan Akun</button>
            </div>
        </form>
    </div>
</div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-xl border-l-4 border-[#800000] shadow-sm">
                <p class="text-xs font-bold text-gray-400 uppercase">Total Peserta Terdaftar</p>
                <p class="text-3xl font-black text-gray-800">{{ $users->total() }}</p>
            </div>
        </div>





<div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 mb-6">
    <form action="{{ route('superadmin.users.index') }}" method="GET" class="flex flex-wrap items-end gap-4">

        <div class="flex-1 min-w-[200px]">
            <label class="text-xs font-bold text-teal-700 uppercase mb-1 block">Cari User</label>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Nama atau email..."
                   class="w-full rounded-lg border-gray-200 focus:border-teal-500 focus:ring-teal-500 text-sm">
        </div>

        <div class="w-40">
            <label class="text-xs font-bold text-teal-700 uppercase mb-1 block">Bulan</label>
            <select name="month" class="w-full rounded-lg border-gray-200 text-sm focus:border-teal-500">
                <option value="">Semua Bulan</option>
                @foreach(range(1, 12) as $m)
                    <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                        {{ Carbon\Carbon::create()->month($m)->format('F') }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="w-32">
            <label class="text-xs font-bold text-teal-700 uppercase mb-1 block">Tahun</label>
            <select name="year" class="w-full rounded-lg border-gray-200 text-sm focus:border-teal-500">
                <option value="">Semua</option>
                @foreach($years as $year)
                    <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                @endforeach
            </select>
        </div>

        <div class="w-40">
            <label class="text-xs font-bold text-teal-700 uppercase mb-1 block">Urutkan</label>
            <select name="sort" class="w-full rounded-lg border-gray-200 text-sm focus:border-teal-500">
                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Terbaru</option>
                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama</option>
            </select>
        </div>

        <div class="flex gap-2">
            <button type="submit" class="bg-teal-600 text-white px-4 py-2 rounded-lg hover:bg-teal-700 transition font-bold text-sm shadow-sm">
                Filter
            </button>
            <a href="{{ route('superadmin.users.index') }}" class="bg-gray-100 text-gray-600 px-4 py-2 rounded-lg hover:bg-gray-200 transition font-bold text-sm">
                Reset
            </a>
        </div>
    </form>
</div>











        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="p-4 text-xs font-bold text-gray-600 uppercase">Nama Peserta</th>
                            <th class="p-4 text-xs font-bold text-gray-600 uppercase">Email</th>
                            @foreach($templates->take(2) as $template) <th class="p-4 text-xs font-bold text-gray-600 uppercase">{{ $template->field_label }}</th>
                            @endforeach
                            <th class="p-4 text-xs font-bold text-gray-600 uppercase text-center">Aksi</th>
                        </tr>
                    </thead>
                   <tbody class="divide-y divide-gray-50">
    @foreach($users as $user)
    <tr class="hover:bg-red-50/30 transition-colors">
        <td class="p-4">
            <div class="flex items-center gap-3">
                <div class="h-8 w-8 rounded-full bg-red-100 flex items-center justify-center text-[#800000] font-bold text-xs">
                    {{ substr($user->name, 0, 1) }}
                </div>
                <span class="font-medium text-gray-700">{{ $user->name }}</span>
            </div>
        </td>
        <td class="p-4 text-sm text-gray-600">{{ $user->email }}</td>

        @foreach($templates->take(2) as $template)
            <td class="p-4 text-sm text-gray-500">
                {{ $user->profile->custom_fields_values[$template->field_name] ?? '-' }}
            </td>
        @endforeach

<td class="p-4">
    <div class="flex items-center justify-center gap-3">
        <a href="{{ route('superadmin.users.show', $user->id) }}"
           class="text-[#800000] hover:text-red-700 font-bold text-xs uppercase tracking-widest transition-colors">
            Detail
        </a>

        <span class="text-gray-200">|</span>

        <a href="{{ route('superadmin.users.edit', $user->id) }}"
           class="text-amber-600 hover:text-amber-700 font-bold text-xs uppercase tracking-widest transition-colors">
            Edit
        </a>

        <span class="text-gray-200">|</span>

        <form action="{{ route('superadmin.users.destroy', $user->id) }}"
              method="POST"
              onsubmit="return confirm('PERHATIAN: Menghapus akun {{ $user->name }} akan menghapus seluruh data profilnya secara permanen. Lanjutkan?')">
            @csrf
            @method('DELETE')

            <button type="submit"
                    class="text-red-500 hover:text-red-700 font-bold text-xs uppercase tracking-widest transition-colors">
                Hapus
            </button>
        </form>
    </div>
</td>
    </tr>
    @endforeach
</tbody>
                </table>
            </div>
            @if($users->hasPages())
                <div class="p-4 border-t border-gray-50">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>


    <div class="max-w-3xl mx-auto space-y-8">
        <div>
            <h2 class="text-2xl font-black text-gray-800 italic uppercase">Pusat Kendali</h2>
            <p class="text-sm text-gray-500">Kelola aksesibilitas fitur platform secara real-time.</p>
        </div>

        @livewire('super-admin.registration-toggle')

        </div>
    <script>
    // Fungsi buka modal
    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden'; // Kunci scroll layar
    }

    // Fungsi tutup modal
    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto'; // Aktifkan scroll kembali
    }

    // Fungsi tampilkan nama file saat dipilih
    function updateFileName(input) {
        const display = document.getElementById('fileNameDisplay');
        if (input.files.length > 0) {
            display.innerText = "File terpilih: " + input.files[0].name;
            display.classList.remove('text-gray-500');
            display.classList.add('text-[#800000]', 'font-bold');
        }
    }

    // Menutup modal jika klik di luar area modal
    window.onclick = function(event) {
        const modalImport = document.getElementById('modalImport');
        const modalManual = document.getElementById('modalManual');
        if (event.target == modalImport) closeModal('modalImport');
        if (event.target == modalManual) closeModal('modalManual');
    }
</script>
@endcomponent

