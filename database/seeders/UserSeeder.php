<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default admin account
        User::updateOrCreate(
            [
                'email' => 'admin@e-arsip.local',
            ],
            [
                'nama_lengkap' => 'Administrator',
                'username' => 'admin',
                'password' => 'admin12345',
                'nomor_telepon' => null,
                'foto' => null,
                'role' => 'Admin',
                'bagian_id' => null,
            ]
        );
    }
}


