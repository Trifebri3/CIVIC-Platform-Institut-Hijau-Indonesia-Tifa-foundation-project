<?php

use App\Models\{Validasinilai, User, PenilaianUser};
use Livewire\Volt\Component;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

new class extends Component {
    public $selected_template_id, $selected_user_id;
    public $form_fields = [];
    public $isi_nilai = [];

    // Properti untuk Filter & Search
    public $search_nama = '';
    public $filter_template = '';

    public function with()
    {
        // 1. Ambil Mahasiswa yang BELUM dinilai pada template terpilih
        $users_available = collect();
        if ($this->selected_template_id) {
            $already_rated_ids = PenilaianUser::where('validasinilai_id', $this->selected_template_id)
                                ->pluck('user_id');

            $users_available = User::where('role', 'user')
                                ->whereNotIn('id', $already_rated_ids)
                                ->select('id', 'name')
                                ->get();
        }

        // 2. Query Riwayat dengan Filter & Grouping
        $riwayat_query = PenilaianUser::with(['template', 'user'])
            ->when($this->filter_template, fn($q) => $q->where('validasinilai_id', $this->filter_template))
            ->when($this->search_nama, function($q) {
                $q->whereHas('user', fn($u) => $u->where('name', 'like', "%{$this->search_nama}%"));
            })
            ->latest()
            ->get()
            ->groupBy('template.template_name'); // Grouping Berdasarkan Nama Template

        return [
            'templates' => Validasinilai::all(),
            'users' => $users_available,
            'grouped_riwayat' => $riwayat_query,
        ];
    }

    // Download QR Langsung
public function downloadQR($secret, $name)
{
    $url = route('nilai.verify', $secret);

    // Ganti format('png') jadi format('svg')
    $image = QrCode::format('svg')
             ->size(500)
             ->margin(2)
             ->errorCorrection('H')
             ->generate($url);

    return response()->streamDownload(function () use ($image) {
        echo $image;
    }, "QR_CIVIC_" . Str::slug($name) . ".svg"); // Ganti ekstensi jadi .svg
}

    public function updatedSelectedTemplateId($id)
    {
        $template = Validasinilai::find($id);
        if ($template) {
            $this->form_fields = $template->schema['kriteria'] ?? [];
            $this->isi_nilai = [];
            foreach ($this->form_fields as $f) {
                $this->isi_nilai[$f['key']] = null;
            }
        }
        $this->selected_user_id = null; // Reset user saat template ganti
    }

    public function save()
    {
        $rules = [
            'selected_template_id' => 'required|exists:validasinilais,id',
            'selected_user_id' => 'required|exists:users,id',
        ];
        foreach ($this->form_fields as $field) {
            $rules["isi_nilai.{$field['key']}"] = 'required|numeric|min:0|max:100';
        }

        $this->validate($rules);

        PenilaianUser::create([
            'validasinilai_id' => $this->selected_template_id,
            'user_id' => $this->selected_user_id,
            'isi_nilai' => $this->isi_nilai,
            'qr_code_secret' => (string) Str::uuid(),
        ]);

        $this->reset(['selected_user_id', 'isi_nilai']);
        $this->dispatch('swal', title: 'Data Berhasil Diterbitkan!', icon: 'success');
    }

    public function delete($id) {
        PenilaianUser::destroy($id);
        $this->dispatch('swal', title: 'Terhapus!', icon: 'info');
    }
}; ?>
<div class="p-8 space-y-8">
    {{-- BAGIAN ATAS: INPUT & FILTER --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- CARD INPUT --}}
        <div class="lg:col-span-1 bg-white p-8 rounded-[2rem] border border-gray-100 shadow-2xl shadow-gray-200/40">
            <h2 class="text-xl font-black uppercase italic italic tracking-tighter mb-6 border-b-4 border-[#800000] inline-block">New Entry</h2>

            <div class="space-y-4">
                <select wire:model.live="selected_template_id" class="w-full bg-gray-50 border-0 rounded-2xl font-bold py-4">
                    <option value="">-- Pilih Program --</option>
                    @foreach($templates as $t)
                        <option value="{{ $t->id }}">{{ $t->template_name }}</option>
                    @endforeach
                </select>

                @if($selected_template_id)
                <select wire:model="selected_user_id" class="w-full bg-gray-50 border-0 rounded-2xl font-bold py-4 animate-in fade-in duration-300">
                    <option value="">-- Pilih Mahasiswa --</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                    @endforeach
                </select>

                <div class="space-y-3 mt-4">
                    @foreach($form_fields as $field)
                    <div class="flex items-center justify-between bg-gray-50 p-4 rounded-2xl border border-gray-100">
                        <span class="text-[9px] font-black uppercase text-gray-400">{{ $field['label'] }}</span>
                        <input type="number" wire:model="isi_nilai.{{ $field['key'] }}" class="w-20 bg-white border-0 rounded-lg font-black text-right text-[#800000]">
                    </div>
                    @endforeach
                </div>

                <button wire:click="save" class="w-full bg-[#800000] text-white py-5 rounded-2xl font-black uppercase text-xs tracking-widest mt-6 hover:scale-95 transition-all shadow-xl shadow-red-900/20">
                    Publish Result
                </button>
                @endif
            </div>
        </div>

        {{-- BAGIAN RIWAYAT DENGAN GROUPING & FILTER --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- TOOLBAR FILTER --}}
            <div class="bg-black p-4 rounded-3xl flex flex-wrap gap-4 items-center">
                <div class="flex-1 min-w-[200px]">
                    <input type="text" wire:model.live="search_nama" placeholder="Cari Nama Mahasiswa..." class="w-full bg-white/10 border-0 rounded-xl text-white placeholder:text-gray-500 font-bold px-6">
                </div>
                <select wire:model.live="filter_template" class="bg-white/10 border-0 rounded-xl text-white font-bold px-6">
                    <option value="" class="text-black">Semua Program</option>
                    @foreach($templates as $t)
                        <option value="{{ $t->id }}" class="text-black">{{ $t->template_name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- LIST DATA (GROUPED) --}}
            <div class="space-y-8">
                @foreach($grouped_riwayat as $template_name => $items)
                <div class="space-y-3">
                    <div class="flex items-center gap-3 px-2">
                        <div class="h-[2px] flex-1 bg-gray-100"></div>
                        <h3 class="text-[10px] font-black uppercase text-gray-400 tracking-[0.3em] italic">{{ $template_name }}</h3>
                        <div class="h-[2px] flex-1 bg-gray-100"></div>
                    </div>

                    @foreach($items as $row)
                    <div class="bg-white p-5 rounded-3xl border border-gray-50 flex flex-wrap justify-between items-center hover:shadow-xl hover:shadow-gray-100/50 transition-all border-l-8 border-l-[#800000]">
                        <div class="flex items-center gap-4">
                            <div class="font-black italic text-lg text-slate-800">{{ $row->user->name }}</div>
                        </div>

                        <div class="flex gap-2">
                            {{-- TOMBOL BARU: DOWNLOAD QR --}}
                            <button wire:click="downloadQR('{{ $row->qr_code_secret }}', '{{ $row->user->name }}')" class="p-3 bg-blue-50 text-blue-600 rounded-xl hover:bg-blue-600 hover:text-white transition-all group" title="Download QR Code">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>

                            <a href="{{ route('nilai.pdf', $row->qr_code_secret) }}" target="_blank" class="px-4 py-3 bg-red-50 text-red-700 rounded-xl font-black text-[10px] uppercase hover:bg-red-700 hover:text-white transition-all">PDF</a>

                            <button wire:click="delete({{ $row->id }})" wire:confirm="Hapus data ini?" class="p-3 text-gray-300 hover:text-red-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
