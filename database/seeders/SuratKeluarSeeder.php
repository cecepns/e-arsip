<?php

namespace Database\Seeders;

use App\Models\SuratKeluar;
use App\Models\Bagian;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class SuratKeluarSeeder extends Seeder
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
                'nomor_surat' => 'SK/001/IX/2025',
                'tanggal_surat' => Carbon::now()->subDays(5),
                'tanggal_keluar' => Carbon::now()->subDays(4),
                'perihal' => 'Undangan Rapat Koordinasi Bulanan',
                'ringkasan_isi' => 'Undangan untuk menghadiri rapat koordinasi bulanan yang akan membahas program kerja dan evaluasi kinerja.',
                'tujuan' => 'Kepala Bagian Kepegawaian',
                'sifat_surat' => 'Biasa',
                'keterangan' => 'Surat undangan rutin bulanan',
            ],
            [
                'nomor_surat' => 'SK/002/IX/2025',
                'tanggal_surat' => Carbon::now()->subDays(3),
                'tanggal_keluar' => Carbon::now()->subDays(2),
                'perihal' => 'Pemberitahuan Perubahan Jadwal Kerja',
                'ringkasan_isi' => 'Pemberitahuan mengenai perubahan jadwal kerja mulai bulan depan dengan sistem shift.',
                'tujuan' => 'Semua Pegawai',
                'sifat_surat' => 'Segera',
                'keterangan' => 'Perubahan kebijakan penting',
            ],
            [
                'nomor_surat' => 'SK/003/IX/2025',
                'tanggal_surat' => Carbon::now()->subDays(2),
                'tanggal_keluar' => Carbon::now()->subDays(1),
                'perihal' => 'Laporan Keuangan Triwulan III',
                'ringkasan_isi' => 'Laporan keuangan triwulan III tahun 2025 yang mencakup realisasi anggaran dan analisis kinerja keuangan.',
                'tujuan' => 'Kepala Bagian Keuangan',
                'sifat_surat' => 'Penting',
                'keterangan' => 'Laporan rutin triwulanan',
            ],
            [
                'nomor_surat' => 'SK/004/IX/2025',
                'tanggal_surat' => Carbon::now()->subDay(),
                'tanggal_keluar' => Carbon::now(),
                'perihal' => 'Surat Keputusan Pengangkatan Pegawai',
                'ringkasan_isi' => 'Surat keputusan pengangkatan pegawai baru berdasarkan hasil seleksi dan wawancara.',
                'tujuan' => 'HRD dan Bagian Kepegawaian',
                'sifat_surat' => 'Rahasia',
                'keterangan' => 'Informasi personal pegawai',
            ],
            [
                'nomor_surat' => 'SK/005/IX/2025',
                'tanggal_surat' => Carbon::now()->subHours(12),
                'tanggal_keluar' => Carbon::now()->subHours(6),
                'perihal' => 'Permohonan Izin Cuti Tahunan',
                'ringkasan_isi' => 'Permohonan izin cuti tahunan untuk keperluan keluarga dan kesehatan.',
                'tujuan' => 'Atasan Langsung',
                'sifat_surat' => 'Biasa',
                'keterangan' => 'Permohonan cuti rutin',
            ],
            [
                'nomor_surat' => 'SK/006/IX/2025',
                'tanggal_surat' => Carbon::now()->subHours(8),
                'tanggal_keluar' => Carbon::now()->subHours(4),
                'perihal' => 'Pemberitahuan Audit Internal',
                'ringkasan_isi' => 'Pemberitahuan akan dilaksanakannya audit internal untuk evaluasi sistem dan prosedur kerja.',
                'tujuan' => 'Semua Bagian',
                'sifat_surat' => 'Segera',
                'keterangan' => 'Audit rutin tahunan',
            ],
            [
                'nomor_surat' => 'SK/007/IX/2025',
                'tanggal_surat' => Carbon::now()->subHours(6),
                'tanggal_keluar' => Carbon::now()->subHours(2),
                'perihal' => 'Surat Tugas Dinas Luar',
                'ringkasan_isi' => 'Surat tugas untuk melaksanakan dinas luar dalam rangka koordinasi dengan instansi terkait.',
                'tujuan' => 'Petugas yang Ditugaskan',
                'sifat_surat' => 'Penting',
                'keterangan' => 'Tugas koordinasi eksternal',
            ],
            [
                'nomor_surat' => 'SK/008/IX/2025',
                'tanggal_surat' => Carbon::now()->subHours(4),
                'tanggal_keluar' => Carbon::now()->subHour(),
                'perihal' => 'Evaluasi Kinerja Pegawai',
                'ringkasan_isi' => 'Dokumen evaluasi kinerja pegawai untuk periode semester ganjil tahun 2025.',
                'tujuan' => 'Bagian SDM',
                'sifat_surat' => 'Rahasia',
                'keterangan' => 'Data evaluasi personal',
            ],
        ];

        foreach ($sampleData as $data) {
            // Random bagian dan user
            $randomBagian = $bagian->random();
            $randomUser = $users->random();

            SuratKeluar::create([
                'nomor_surat' => $data['nomor_surat'],
                'tanggal_surat' => $data['tanggal_surat'],
                'tanggal_keluar' => $data['tanggal_keluar'],
                'perihal' => $data['perihal'],
                'ringkasan_isi' => $data['ringkasan_isi'],
                'tujuan' => $data['tujuan'],
                'sifat_surat' => $data['sifat_surat'],
                'keterangan' => $data['keterangan'],
                'pengirim_bagian_id' => $randomBagian->id,
                'user_id' => $randomUser->id,
                'created_by' => $randomUser->id,
                'updated_by' => $randomUser->id,
            ]);
        }

        $this->command->info('SuratKeluarSeeder completed successfully!');
    }
}
