<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Bagian;
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
                'username' => 'admin',
            ],
            [
                'nama' => 'Administrator',
                'email' => 'admin@e-arsip.local',
                'password' => 'admin12345', // Plain text password
                'role' => 'Admin',
                'bagian_id' => null,
            ]
        );

        // Create sample users for each bagian
        $bagianIds = Bagian::pluck('id', 'nama_bagian');

        $sampleUsers = [
            [
                'username' => 'sekretariat_kepala',
                'nama' => 'Dr. Ahmad Wijaya, S.H., M.H.',
                'email' => 'sekretariat@e-arsip.local',
                'password' => 'sekretariat123',
                'role' => 'Staf',
                'bagian_id' => $bagianIds['Sekretariat'] ?? null,
            ],
            [
                'username' => 'sekretariat_staf',
                'nama' => 'Siti Nurhaliza, S.Sos.',
                'email' => 'staf.sekretariat@e-arsip.local',
                'password' => 'staf123',
                'role' => 'Staf',
                'bagian_id' => $bagianIds['Sekretariat'] ?? null,
            ],
            [
                'username' => 'keuangan_kepala',
                'nama' => 'Budi Santoso, S.E., M.M.',
                'email' => 'keuangan@e-arsip.local',
                'password' => 'keuangan123',
                'role' => 'Staf',
                'bagian_id' => $bagianIds['Keuangan'] ?? null,
            ],
            [
                'username' => 'umum_kepala',
                'nama' => 'Dr. Rina Dewi, S.H., M.H.',
                'email' => 'umum@e-arsip.local',
                'password' => 'umum123',
                'role' => 'Staf',
                'bagian_id' => $bagianIds['Umum & Kepegawaian'] ?? null,
            ],
            [
                'username' => 'program_kepala',
                'nama' => 'Prof. Dr. Agus Salim, S.T., M.T.',
                'email' => 'program@e-arsip.local',
                'password' => 'program123',
                'role' => 'Staf',
                'bagian_id' => $bagianIds['Program & Evaluasi'] ?? null,
            ],
            [
                'username' => 'ti_kepala',
                'nama' => 'Ir. Muhammad Fahmi, S.T., M.Kom.',
                'email' => 'ti@e-arsip.local',
                'password' => 'ti123',
                'role' => 'Staf',
                'bagian_id' => $bagianIds['Teknologi Informasi'] ?? null,
            ],
        ];

        foreach ($sampleUsers as $userData) {
            User::updateOrCreate(
                [
                    'username' => $userData['username'],
                ],
                $userData
            );
        }

        // Update kepala_bagian_user_id in bagian table
        $this->updateKepalaBagian();
    }

    /**
     * Update kepala_bagian_user_id in bagian table
     */
    private function updateKepalaBagian(): void
    {
        $kepalaBagianMappings = [
            'Sekretariat' => 'sekretariat_kepala',
            'Keuangan' => 'keuangan_kepala',
            'Umum & Kepegawaian' => 'umum_kepala',
            'Program & Evaluasi' => 'program_kepala',
            'Teknologi Informasi' => 'ti_kepala',
        ];

        foreach ($kepalaBagianMappings as $bagianName => $username) {
            $user = User::where('username', $username)->first();
            if ($user) {
                Bagian::where('nama_bagian', $bagianName)
                    ->update(['kepala_bagian_user_id' => $user->id]);
            }
        }
    }
}


