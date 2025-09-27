<?php

namespace Database\Seeders;

use App\Models\Bagian;
use Illuminate\Database\Seeder;

class BagianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bagian = [
            [
                'nama_bagian' => 'Sekretariat',
                'keterangan' => 'Bagian administrasi umum dan koordinasi',
                'kepala_bagian_user_id' => null, // Will be set after users are created
                'status' => 'Aktif',
            ],
            [
                'nama_bagian' => 'Keuangan',
                'keterangan' => 'Bagian pengelolaan keuangan dan anggaran',
                'kepala_bagian_user_id' => null, // Will be set after users are created
                'status' => 'Aktif',
            ],
            [
                'nama_bagian' => 'Umum & Kepegawaian',
                'keterangan' => 'Bagian sumber daya manusia dan administrasi',
                'kepala_bagian_user_id' => null, // Will be set after users are created
                'status' => 'Aktif',
            ],
            [
                'nama_bagian' => 'Program & Evaluasi',
                'keterangan' => 'Bagian perencanaan dan evaluasi program',
                'kepala_bagian_user_id' => null, // Will be set after users are created
                'status' => 'Aktif',
            ],
            [
                'nama_bagian' => 'Teknologi Informasi',
                'keterangan' => 'Bagian sistem informasi dan teknologi',
                'kepala_bagian_user_id' => null, // Will be set after users are created
                'status' => 'Aktif',
            ],
        ];

        foreach ($bagian as $item) {
            Bagian::updateOrCreate(
                [
                    'nama_bagian' => $item['nama_bagian'],
                ],
                $item
            );
        }
    }
}