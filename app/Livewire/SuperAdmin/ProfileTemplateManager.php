<?php

namespace App\Livewire\SuperAdmin;

use Livewire\Component;
use App\Models\ProfileTemplate;
use Livewire\Attributes\Rule;

class ProfileTemplateManager extends Component
{
    // Menggunakan Atribut Rule (Livewire v3) untuk validasi instan
    #[Rule('required|min:3')]
    public $field_label = '';

    #[Rule('required|alpha_dash|unique:profile_templates,field_name')]
    public $field_name = '';

    #[Rule('required')]
    public $field_type = 'text';

    public $is_required = false;

public function store()
{
    try {
        $this->validate([
            'field_label' => 'required',
            'field_name'  => 'required|alpha_dash|unique:profile_templates,field_name',
            'field_type'  => 'required',
        ]);

        \App\Models\ProfileTemplate::create([
            'field_label' => $this->field_label,
            'field_name'  => $this->field_name,
            'field_type'  => $this->field_type,
            'is_required' => $this->is_required ?? false,
            'order'       => \App\Models\ProfileTemplate::count() + 1,
        ]);

        $this->reset(['field_label', 'field_name']);
        session()->flash('message', 'Data Berhasil Masuk!');

    } catch (\Exception $e) {
        // Hapus tag </div> yang tadi ada di sini
        session()->flash('error', $e->getMessage());
    }
}

    public function delete($id)
    {
        ProfileTemplate::findOrFail($id)->delete();
        session()->flash('message', 'Field berhasil dihapus.');
    }

    public function render()
    {
        // PENTING: Panggil data di sini agar selalu fresh setiap request
        return view('livewire.super-admin.profile-template-manager', [
            'templates' => ProfileTemplate::orderBy('order')->get()
        ]);
    }
}
