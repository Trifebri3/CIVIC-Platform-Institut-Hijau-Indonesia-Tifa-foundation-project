<?php

namespace App\Livewire\User;

use Livewire\Component;
use Livewire\WithFileUploads; // Wajib untuk upload file
use App\Models\ProfileTemplate;
use Illuminate\Support\Facades\Auth;

class ActivationForm extends Component
{
    use WithFileUploads;

    // Properti untuk Form
    public $avatar;
    public $custom_fields = [];

    // Simpan template biar bisa diakses di View
    public $templates;

    public function mount()
    {
        // Ambil template kustom saat komponen pertama kali dimuat
        $this->templates = ProfileTemplate::orderBy('order', 'asc')->get();

        // Inisialisasi array custom_fields agar tidak null
        foreach ($this->templates as $template) {
            $this->custom_fields[$template->field_name] = '';
        }
    }

    public function activate()
    {
        $user = Auth::user();

        // 1. Validasi (Avatar & Custom Fields)
        $rules = [
            'avatar' => 'nullable|image|max:2048', // Max 2MB
        ];

        foreach ($this->templates as $template) {
            if ($template->is_required) {
                $rules['custom_fields.' . $template->field_name] = 'required';
            }
        }

        $this->validate($rules);

        // 2. Handle Upload Avatar (Jika ada)
        if ($this->avatar) {
            $path = $this->avatar->store('avatars', 'public');
            $user->update(['avatar' => $path]);
        }

        // 3. Simpan Data ke Profile JSON
        $user->profile()->update([
            'custom_fields_values' => $this->custom_fields,
        ]);

        // 4. Tandai User Sudah Aktif
        $user->update(['is_profile_completed' => 1]);

        // 5. Tendang ke Dashboard
        session()->flash('message', 'Profil berhasil diaktifkan! Selamat datang.');
        return $this->redirect(route('user.dashboard'), navigate: true);
    }

    public function render()
    {
        return view('livewire.user.activation-form');
    }
}
