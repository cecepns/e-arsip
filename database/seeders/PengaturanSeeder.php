<?php

namespace Database\Seeders;

use App\Models\Pengaturan;
use Illuminate\Database\Seeder;

class PengaturanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create or update default institutional settings
        Pengaturan::updateOrCreate(
            [
                'id' => 1, // Assuming there's only one settings record
            ],
            [
                'nama_instansi' => 'Pemerintah Kabupaten/Kota',
                'alamat' => 'Jl. Raya Pemerintahan No. 1, Kota Administratif, Provinsi',
                'logo' => '', // Empty initially, can be uploaded later
                'nama_pejabat' => 'Dr. Ahmad Wijaya, S.H., M.H.',
                'jabatan_pejabat' => 'Kepala Dinas',
            ]
        );
    }
}
