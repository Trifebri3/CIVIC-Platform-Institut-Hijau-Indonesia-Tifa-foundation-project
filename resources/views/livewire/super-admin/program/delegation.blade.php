<?php

use App\Models\{User, Program};
use Livewire\Volt\Component;
use Illuminate\Support\Facades\DB;

new class extends Component {
    public Program $program;
    public $selectedUsers = [];

    public function mount(Program $program)
    {
        $this->program = $program;
        // Ambil ID user yang sudah terhubung dengan program ini di tabel pivot
        $this->selectedUsers = $this->program->admins()->pluck('users.id')->map(fn($id) => (string)$id)->toArray();
    }

    public function save()
    {
        try {
            DB::transaction(function () {
                // 1. Sinkronisasi tabel pivot (program_admin)
                $this->program->admins()->sync($this->selectedUsers);

                // 2. Update role user yang dipilih menjadi 'adminprogram' jika belum
                if (!empty($this->selectedUsers)) {
                    User::whereIn('id', $this->selectedUsers)
                        ->where('role', '!=', 'superadmin')
                        ->update(['role' => 'adminprogram']);
                }
            });

            session()->flash('success', 'Delegasi otoritas berhasil diperbarui.');

            // Opsional: Redirect kembali ke index program
            // return $this->redirectRoute('super-admin.programs.index', navigate: true);

        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function with()
    {
        return [
            // Mengambil user dengan role adminprogram
            // Tips: Jika ingin bisa promosi user biasa, tambahkan 'user' di whereIn
            'availableUsers' => User::where('role', 'adminprogram')
                ->where('role', '!=', 'superadmin')
                ->orderBy('name', 'asc')
                ->get(),
        ];
    }
}; ?>

<div class="max-w-3xl mx-auto px-4">
    {{-- Notifikasi Sukses/Error --}}
    @if (session()->has('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-2xl animate-in fade-in slide-in-from-top-4">
            <p class="text-[10px] font-black text-green-700 uppercase tracking-widest italic">{{ session('success') }}</p>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-2xl">
            <p class="text-[10px] font-black text-red-700 uppercase tracking-widest italic">{{ session('error') }}</p>
        </div>
    @endif

    <div class="bg-white rounded-[3rem] p-10 shadow-sm border border-gray-100">
        <div class="mb-10">
            <h2 class="text-3xl font-black text-gray-900 uppercase italic tracking-tighter leading-none">Delegasi Otoritas</h2>
            <div class="flex items-center gap-2 mt-3">
                <span class="px-2 py-0.5 bg-red-50 text-[#800000] text-[8px] font-black uppercase rounded">Program</span>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">{{ $program->name }}</p>
            </div>
        </div>

        <div class="space-y-6">
            <label class="text-[9px] font-black text-gray-400 uppercase tracking-[0.3em] block mb-2 italic">
                Pilih Administrator Program
            </label>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse($availableUsers as $user)
                    <label class="relative flex items-center p-5 rounded-[1.5rem] border border-gray-50 bg-gray-50/50 cursor-pointer hover:bg-white hover:border-red-100 hover:shadow-md transition-all duration-300 group">
                        <input type="checkbox"
                               wire:model="selectedUsers"
                               value="{{ $user->id }}"
                               class="w-5 h-5 rounded-lg border-gray-200 text-[#800000] focus:ring-[#800000] focus:ring-offset-0 transition-all">

                        <div class="ml-4">
                            <p class="text-[11px] font-black text-gray-800 uppercase italic group-hover:text-[#800000] transition-colors">{{ $user->name }}</p>
                            <p class="text-[9px] text-gray-400 font-bold uppercase tracking-tighter">{{ $user->email }}</p>
                        </div>
                    </label>
                @empty
                    <div class="col-span-full py-10 text-center border-2 border-dashed border-gray-100 rounded-[2rem]">
                        <p class="text-[10px] font-black text-gray-300 uppercase tracking-widest">Tidak ada user dengan role Admin Program ditemukan.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="mt-12 pt-8 border-t border-gray-50 flex flex-col md:flex-row items-center justify-between gap-4">
            <p class="text-[9px] text-gray-400 font-bold uppercase italic tracking-widest">
                * User yang dipilih akan memiliki akses penuh ke program ini.
            </p>

            <button wire:click="save"
                    wire:loading.attr="disabled"
                    class="w-full md:w-auto bg-black text-white px-12 py-5 rounded-2xl font-black text-[10px] uppercase tracking-[0.3em] shadow-2xl hover:bg-[#800000] transition-all disabled:opacity-50 disabled:cursor-not-allowed group">

                <span wire:loading.remove wire:target="save">Simpan Perubahan</span>
                <span wire:loading wire:target="save" class="flex items-center gap-2">
                    <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    Processing...
                </span>
            </button>
        </div>
    </div>
</div>
