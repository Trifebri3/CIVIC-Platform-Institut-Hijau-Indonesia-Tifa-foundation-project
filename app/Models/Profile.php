<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = [
        'user_id',
        'avatar',
        'custom_fields_values', // Tempat menyimpan semua jawaban form dinamis
    ];

    protected $casts = [
        'custom_fields_values' => 'array', // Penting agar JSON bisa langsung diakses sebagai array
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function getFieldValue($key)
{
    // Mengembalikan nilai dari JSON, atau strip (-) jika tidak ada
    return $this->custom_fields_values[$key] ?? '-';
}
}
