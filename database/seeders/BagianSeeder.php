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
                'kepala_bagian' => 'Kepala Sekretariat',
                'status' => 'aktif',
            ],
            [
                'nama_bagian' => 'Keuangan',
                'keterangan' => 'Bagian pengelolaan keuangan dan anggaran',
                'kepala_bagian' => 'Kepala Keuangan',
                'status' => 'aktif',
            ],
            [
                'nama_bagian' => 'Umum & Kepegawaian',
                'keterangan' => 'Bagian sumber daya manusia dan administrasi',
                'kepala_bagian' => 'Kepala Umum & Kepegawaian',
                'status' => 'aktif',
            ],
            [
                'nama_bagian' => 'Program & Evaluasi',
                'keterangan' => 'Bagian perencanaan dan evaluasi program',
                'kepala_bagian' => 'Kepala Program & Evaluasi',
                'status' => 'aktif',
            ],
            [
                'nama_bagian' => 'Teknologi Informasi',
                'keterangan' => 'Bagian sistem informasi dan teknologi',
                'kepala_bagian' => 'Kepala TI',
                'status' => 'aktif',
            ],
        ];

        foreach ($bagian as $item) {
            Bagian::create($item);
        }
    }
}