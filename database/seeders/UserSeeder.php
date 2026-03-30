<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Data Super Admin
        User::create([
            'name' => 'Super Admin CIVIC',
            'email' => 'superadmin@civic.com',
            'password' => Hash::make('password'),
            'role' => 'superadmin',
        ]);

        // Data Admin Program
        User::create([
            'name' => 'Admin Program',
            'email' => 'program@civic.com',
            'password' => Hash::make('password'),
            'role' => 'adminprogram',
        ]);

        // Data Admin Surat
        User::create([
            'name' => 'Admin Surat',
            'email' => 'surat@civic.com',
            'password' => Hash::make('password'),
            'role' => 'adminsurat',
        ]);

        // Data User Biasa
        User::create([
            'name' => 'Peserta Biasa',
            'email' => 'user@civic.com',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);
    }
}
