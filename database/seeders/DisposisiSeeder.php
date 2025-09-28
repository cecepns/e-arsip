<?php

namespace Database\Seeders;

use App\Models\Disposisi;
use App\Models\SuratMasuk;
use App\Models\Bagian;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DisposisiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing data
        $suratMasuk = SuratMasuk::all();
        $bagian = Bagian::where('status', 'Aktif')->get();
        $users = User::all();

        if ($suratMasuk->isEmpty() || $bagian->isEmpty() || $users->isEmpty()) {
            $this->command->warn('No surat masuk, bagian, or users found. Please run SuratMasukSeeder, BagianSeeder, and UserSeeder first.');
            return;
        }

        $sampleDisposisiData = [
            [
                'isi_instruksi' => 'Mohon segera diproses dan dilaporkan hasilnya dalam waktu 3 hari kerja.',
                'sifat' => 'Segera',
                'catatan' => 'Surat ini memerlukan tindak lanjut segera karena berkaitan dengan rapat koordinasi.',
                'status' => 'Menunggu',
                'tanggal_disposisi' => Carbon::now()->subDays(5),
                'batas_waktu' => Carbon::now()->addDays(3),
            ],
            [
                'isi_instruksi' => 'Mohon data dan informasi yang diminta disiapkan sesuai dengan format yang telah ditentukan.',
                'sifat' => 'Biasa',
                'catatan' => 'Pastikan data yang diberikan akurat dan lengkap.',
                'status' => 'Dikerjakan',
                'tanggal_disposisi' => Carbon::now()->subDays(4),
                'batas_waktu' => Carbon::now()->addDays(7),
            ],
            [
                'isi_instruksi' => 'Mohon persiapan dokumen dan data yang diperlukan untuk audit eksternal.',
                'sifat' => 'Penting',
                'catatan' => 'Audit akan dilaksanakan dalam waktu 2 minggu ke depan.',
                'status' => 'Menunggu',
                'tanggal_disposisi' => Carbon::now()->subDays(3),
                'batas_waktu' => Carbon::now()->addDays(10),
            ],
            [
                'isi_instruksi' => 'Mohon koordinasi dengan bagian terkait untuk persiapan dokumen mutasi pegawai.',
                'sifat' => 'Rahasia',
                'catatan' => 'Informasi ini bersifat rahasia dan hanya untuk keperluan internal.',
                'status' => 'Selesai',
                'tanggal_disposisi' => Carbon::now()->subDays(2),
                'batas_waktu' => Carbon::now()->subDays(1),
            ],
            [
                'isi_instruksi' => 'Mohon analisis dan review laporan keuangan bulanan sebelum disampaikan ke pimpinan.',
                'sifat' => 'Penting',
                'catatan' => 'Pastikan semua angka dan analisis sudah sesuai dengan standar akuntansi.',
                'status' => 'Dikerjakan',
                'tanggal_disposisi' => Carbon::now()->subDays(1),
                'batas_waktu' => Carbon::now()->addDays(5),
            ],
            [
                'isi_instruksi' => 'Mohon koordinasi dengan bagian SDM untuk menentukan peserta pelatihan yang sesuai.',
                'sifat' => 'Biasa',
                'catatan' => 'Prioritaskan pegawai yang memerlukan pengembangan kompetensi.',
                'status' => 'Menunggu',
                'tanggal_disposisi' => Carbon::now()->subHours(12),
                'batas_waktu' => Carbon::now()->addDays(7),
            ],
            [
                'isi_instruksi' => 'Mohon sosialisasi perubahan kebijakan kepada seluruh pegawai di bagian masing-masing.',
                'sifat' => 'Segera',
                'catatan' => 'Perubahan kebijakan ini akan berlaku efektif mulai bulan depan.',
                'status' => 'Dikerjakan',
                'tanggal_disposisi' => Carbon::now()->subHours(8),
                'batas_waktu' => Carbon::now()->addDays(3),
            ],
            [
                'isi_instruksi' => 'Mohon persiapan dokumen dan data untuk monitoring dan evaluasi program kerja.',
                'sifat' => 'Penting',
                'catatan' => 'Monitoring akan dilaksanakan secara berkala setiap bulan.',
                'status' => 'Menunggu',
                'tanggal_disposisi' => Carbon::now()->subHours(6),
                'batas_waktu' => Carbon::now()->addDays(14),
            ],
            [
                'isi_instruksi' => 'Mohon koordinasi teknis untuk implementasi sistem informasi yang baru.',
                'sifat' => 'Biasa',
                'catatan' => 'Kerjasama ini akan meningkatkan efisiensi layanan publik.',
                'status' => 'Selesai',
                'tanggal_disposisi' => Carbon::now()->subHours(4),
                'batas_waktu' => Carbon::now()->subHours(2),
            ],
            [
                'isi_instruksi' => 'Mohon review dan evaluasi kinerja instansi berdasarkan indikator yang telah ditetapkan.',
                'sifat' => 'Rahasia',
                'catatan' => 'Hasil evaluasi akan digunakan untuk perbaikan kinerja ke depan.',
                'status' => 'Dikerjakan',
                'tanggal_disposisi' => Carbon::now()->subHours(2),
                'batas_waktu' => Carbon::now()->addDays(21),
            ],
        ];

        $disposisiCount = 0;
        $maxDisposisiPerSurat = 3; // Maximum disposisi per surat masuk

        foreach ($suratMasuk as $surat) {
            // Random number of disposisi per surat (1-3)
            $numberOfDisposisi = rand(1, $maxDisposisiPerSurat);
            
            for ($i = 0; $i < $numberOfDisposisi; $i++) {
                // Get random disposisi data
                $disposisiData = $sampleDisposisiData[array_rand($sampleDisposisiData)];
                
                // Get random tujuan bagian (different from surat tujuan)
                $availableBagian = $bagian->where('id', '!=', $surat->tujuan_bagian_id);
                if ($availableBagian->isEmpty()) {
                    continue; // Skip if no other bagian available
                }
                $randomTujuanBagian = $availableBagian->random();
                
                // Get random user
                $randomUser = $users->random();

                Disposisi::create([
                    'surat_masuk_id' => $surat->id,
                    'tujuan_bagian_id' => $randomTujuanBagian->id,
                    'isi_instruksi' => $disposisiData['isi_instruksi'],
                    'sifat' => $disposisiData['sifat'],
                    'catatan' => $disposisiData['catatan'],
                    'status' => $disposisiData['status'],
                    'tanggal_disposisi' => $disposisiData['tanggal_disposisi'],
                    'batas_waktu' => $disposisiData['batas_waktu'],
                    'user_id' => $randomUser->id,
                ]);

                $disposisiCount++;
            }
        }

        $this->command->info("DisposisiSeeder completed successfully! Created {$disposisiCount} disposisi records.");
    }
}