<?php

namespace Database\Seeders;

use App\Models\SuratMasuk;
use App\Models\Bagian;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class SuratMasukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing bagian and users
        $bagian = Bagian::where('status', 'Aktif')->get();
        $users = User::all();

        if ($bagian->isEmpty() || $users->isEmpty()) {
            $this->command->warn('No bagian or users found. Please run BagianSeeder and UserSeeder first.');
            return;
        }

        $sampleData = [
            [
                'nomor_surat' => 'SM/001/IX/2025',
                'tanggal_surat' => Carbon::now()->subDays(7),
                'tanggal_terima' => Carbon::now()->subDays(6),
                'perihal' => 'Undangan Rapat Koordinasi Antar Instansi',
                'ringkasan_isi' => 'Undangan untuk menghadiri rapat koordinasi antar instansi pemerintah dalam rangka sinkronisasi program kerja.',
                'pengirim' => 'Kementerian Dalam Negeri',
                'sifat_surat' => 'Segera',
                'keterangan' => 'Undangan rapat penting',
            ],
            [
                'nomor_surat' => 'SM/002/IX/2025',
                'tanggal_surat' => Carbon::now()->subDays(5),
                'tanggal_terima' => Carbon::now()->subDays(4),
                'perihal' => 'Permohonan Data dan Informasi',
                'ringkasan_isi' => 'Permohonan data dan informasi terkait kinerja instansi untuk keperluan evaluasi dan pelaporan.',
                'pengirim' => 'Badan Pusat Statistik',
                'sifat_surat' => 'Biasa',
                'keterangan' => 'Permohonan data rutin',
            ],
            [
                'nomor_surat' => 'SM/003/IX/2025',
                'tanggal_surat' => Carbon::now()->subDays(4),
                'tanggal_terima' => Carbon::now()->subDays(3),
                'perihal' => 'Pemberitahuan Audit Eksternal',
                'ringkasan_isi' => 'Pemberitahuan akan dilaksanakannya audit eksternal oleh Badan Pemeriksa Keuangan untuk evaluasi keuangan instansi.',
                'pengirim' => 'Badan Pemeriksa Keuangan',
                'sifat_surat' => 'Penting',
                'keterangan' => 'Audit eksternal rutin',
            ],
            [
                'nomor_surat' => 'SM/004/IX/2025',
                'tanggal_surat' => Carbon::now()->subDays(3),
                'tanggal_terima' => Carbon::now()->subDays(2),
                'perihal' => 'Surat Keputusan Mutasi Pegawai',
                'ringkasan_isi' => 'Surat keputusan mutasi pegawai antar instansi berdasarkan kebutuhan dan kompetensi.',
                'pengirim' => 'Badan Kepegawaian Negara',
                'sifat_surat' => 'Rahasia',
                'keterangan' => 'Informasi personal pegawai',
            ],
            [
                'nomor_surat' => 'SM/005/IX/2025',
                'tanggal_surat' => Carbon::now()->subDays(2),
                'tanggal_terima' => Carbon::now()->subDay(),
                'perihal' => 'Laporan Keuangan Bulanan',
                'ringkasan_isi' => 'Laporan keuangan bulanan yang mencakup realisasi anggaran dan analisis kinerja keuangan instansi.',
                'pengirim' => 'Kementerian Keuangan',
                'sifat_surat' => 'Penting',
                'keterangan' => 'Laporan rutin bulanan',
            ],
            [
                'nomor_surat' => 'SM/006/IX/2025',
                'tanggal_surat' => Carbon::now()->subDay(),
                'tanggal_terima' => Carbon::now()->subHours(12),
                'perihal' => 'Undangan Pelatihan dan Pengembangan',
                'ringkasan_isi' => 'Undangan untuk mengikuti pelatihan dan pengembangan kompetensi pegawai dalam rangka peningkatan kualitas SDM.',
                'pengirim' => 'Lembaga Administrasi Negara',
                'sifat_surat' => 'Biasa',
                'keterangan' => 'Pelatihan pengembangan SDM',
            ],
            [
                'nomor_surat' => 'SM/007/IX/2025',
                'tanggal_surat' => Carbon::now()->subHours(18),
                'tanggal_terima' => Carbon::now()->subHours(10),
                'perihal' => 'Pemberitahuan Perubahan Kebijakan',
                'ringkasan_isi' => 'Pemberitahuan mengenai perubahan kebijakan pemerintah yang berdampak pada operasional instansi.',
                'pengirim' => 'Sekretariat Kabinet',
                'sifat_surat' => 'Segera',
                'keterangan' => 'Perubahan kebijakan penting',
            ],
            [
                'nomor_surat' => 'SM/008/IX/2025',
                'tanggal_surat' => Carbon::now()->subHours(12),
                'tanggal_terima' => Carbon::now()->subHours(6),
                'perihal' => 'Surat Tugas Monitoring dan Evaluasi',
                'ringkasan_isi' => 'Surat tugas untuk melaksanakan monitoring dan evaluasi program kerja instansi dalam rangka peningkatan kinerja.',
                'pengirim' => 'Inspektorat Jenderal',
                'sifat_surat' => 'Penting',
                'keterangan' => 'Monitoring dan evaluasi rutin',
            ],
            [
                'nomor_surat' => 'SM/009/IX/2025',
                'tanggal_surat' => Carbon::now()->subHours(8),
                'tanggal_terima' => Carbon::now()->subHours(4),
                'perihal' => 'Permohonan Kerjasama Teknis',
                'ringkasan_isi' => 'Permohonan kerjasama teknis dalam bidang teknologi informasi untuk mendukung digitalisasi layanan publik.',
                'pengirim' => 'Kementerian Komunikasi dan Informatika',
                'sifat_surat' => 'Biasa',
                'keterangan' => 'Kerjasama teknis IT',
            ],
            [
                'nomor_surat' => 'SM/010/IX/2025',
                'tanggal_surat' => Carbon::now()->subHours(6),
                'tanggal_terima' => Carbon::now()->subHours(2),
                'perihal' => 'Evaluasi Kinerja Instansi',
                'ringkasan_isi' => 'Dokumen evaluasi kinerja instansi untuk periode semester ganjil tahun 2025 yang mencakup berbagai aspek kinerja.',
                'pengirim' => 'Kementerian Pendayagunaan Aparatur Negara',
                'sifat_surat' => 'Rahasia',
                'keterangan' => 'Evaluasi kinerja instansi',
            ],
        ];

        foreach ($sampleData as $data) {
            // Random bagian dan user
            $randomBagian = $bagian->random();
            $randomUser = $users->random();

            SuratMasuk::create([
                'nomor_surat' => $data['nomor_surat'],
                'tanggal_surat' => $data['tanggal_surat'],
                'tanggal_terima' => $data['tanggal_terima'],
                'perihal' => $data['perihal'],
                'ringkasan_isi' => $data['ringkasan_isi'],
                'pengirim' => $data['pengirim'],
                'sifat_surat' => $data['sifat_surat'],
                'keterangan' => $data['keterangan'],
                'tujuan_bagian_id' => $randomBagian->id,
                'user_id' => $randomUser->id,
                'created_by' => $randomUser->id,
                'updated_by' => $randomUser->id,
            ]);
        }

        $this->command->info('SuratMasukSeeder completed successfully!');
    }
}
