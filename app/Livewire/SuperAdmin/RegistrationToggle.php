<?php

namespace App\Livewire\SuperAdmin;

use Livewire\Component;
use App\Models\SettingBuatAkun;

class RegistrationToggle extends Component
{
    public $isOpen;
    public $pesanTutup;

    public function mount()
    {
        $settings = SettingBuatAkun::first() ?? SettingBuatAkun::create(['is_open' => false]);
        $this->isOpen = $settings->is_open;
        $this->pesanTutup = $settings->pesan_tutup ?? 'Mohon maaf, pendaftaran saat ini sedang ditutup.';
    }

    public function toggleStatus()
    {
        $this->isOpen = !$this->isOpen;
        SettingBuatAkun::first()->update(['is_open' => $this->isOpen]);

        $status = $this->isOpen ? 'DIBUKA' : 'DITUTUP';
        session()->flash('message', "Pendaftaran berhasil $status!");
    }

    public function updatePesan()
    {
        SettingBuatAkun::first()->update(['pesan_tutup' => $this->pesanTutup]);
        session()->flash('message', "Pesan penutupan diperbarui!");
    }

    public function render()
    {
        return view('livewire.super-admin.registration-toggle');
    }
}
