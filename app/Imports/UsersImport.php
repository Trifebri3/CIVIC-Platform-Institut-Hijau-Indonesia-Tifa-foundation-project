<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Profile;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UsersImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // 1. Buat User Baru
        $user = User::create([
            'name'     => $row['nama'],
            'email'    => $row['email'],
            'password' => Hash::make($row['password'] ?? 'civic123'), // Default password jika kosong
            'role'     => $row['role'] ?? 'user',
        ]);

        // 2. Otomatis buatkan record Profile kosong agar tidak error saat di-export
        Profile::create([
            'user_id' => $user->id,
            'custom_fields_values' => [],
        ]);

        return $user;
    }
}
