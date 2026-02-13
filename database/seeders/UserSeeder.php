<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Superadmin - Protected
        User::create([
            'nama' => 'Auliya Putra Azhari',
            'nip' => '199609102018011005',
            'email' => 'auliyaputraazhari@kemenkeu.go.id',
            'email_pribadi' => 'auliyaputraazhari@gmail.com',
            'no_hp' => '082211581510',
            'password' => Hash::make('password123'),
            'role' => 'superadmin',
        ]);

        // Admin
        User::create([
            'nama' => 'Admin SiTUMAN',
            'nip' => '199001011990011001',
            'email' => 'admin@kemenkeu.go.id',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        // User Biasa
        User::create([
            'nama' => 'User Demo',
            'nip' => '199502021995021001',
            'email' => 'user@kemenkeu.go.id',
            'password' => Hash::make('password123'),
            'role' => 'user',
        ]);

        // User Tanpa Role
        User::create([
            'nama' => 'User Tanpa Role',
            'nip' => '199803031998031001',
            'email' => 'norole@kemenkeu.go.id',
            'password' => Hash::make('password123'),
            'role' => null,
        ]);
    }
}
