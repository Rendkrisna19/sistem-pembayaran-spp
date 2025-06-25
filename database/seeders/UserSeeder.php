<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User; // Pastikan ini diimpor
use Illuminate\Support\Facades\Hash; // Pastikan ini diimpor

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat akun Admin
        User::create([
            'name' => 'Admin Utama',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin123'), // Password default: 'password'
            'role' => 'admin',
        ]);

        // Buat akun Bendahara
        User::create([
            'name' => 'Bendahara Keuangan',
            'email' => 'bendahara@egmail.com',
            'password' => Hash::make('bendahara123'), // Password default: 'password'
            'role' => 'bendahara',
        ]);

        // Buat akun Siswa
        User::create([
            'name' => 'Siswa Contoh',
            'email' => 'siswa@gmai.com',
            'password' => Hash::make('siswa123'), // Password default: 'password'
            'role' => 'siswa',
        ]);

        $this->command->info('Akun default (admin, bendahara, siswa) berhasil dibuat!');
    }
}