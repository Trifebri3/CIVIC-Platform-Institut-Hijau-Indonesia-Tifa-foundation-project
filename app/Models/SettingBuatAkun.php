<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettingBuatAkun extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang digunakan oleh model ini.
     * Karena kamu minta namanya 'settingsbuatakun', kita deklarasikan di sini.
     * * @var string
     */
    protected $table = 'settingsbuatakun';

    /**
     * Atribut yang dapat diisi (Mass Assignable).
     * * @var array
     */
    protected $fillable = [
        'is_open',
        'pesan_tutup',
    ];

    /**
     * Casting tipe data otomatis.
     * is_open akan otomatis dianggap sebagai boolean (true/false).
     * * @var array
     */
    protected $casts = [
        'is_open' => 'boolean',
    ];

    /**
     * Helper static untuk mengambil status pendaftaran dengan cepat.
     * Penggunaan: SettingBuatAkun::isOpen()
     */
    public static function isOpen()
    {
        $setting = self::first();
        return $setting ? $setting->is_open : false;
    }
}
