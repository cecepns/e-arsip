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
            // ANCHOR: Data untuk 7 Hari Terakhir (Current Week)
            [
                'isi_instruksi' => 'Mohon segera diproses dan dilaporkan hasilnya dalam waktu 3 hari kerja.',
                'sifat' => 'Segera',
                'catatan' => 'Surat ini memerlukan tindak lanjut segera karena berkaitan dengan rapat koordinasi.',
                'status' => 'Menunggu',
                'tanggal_disposisi' => Carbon::now()->subDays(6),
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
                'tanggal_disposisi' => Carbon::now()->subDays(2),
                'batas_waktu' => Carbon::now()->addDays(10),
            ],
            [
                'isi_instruksi' => 'Mohon koordinasi dengan bagian terkait untuk persiapan dokumen mutasi pegawai.',
                'sifat' => 'Rahasia',
                'catatan' => 'Informasi ini bersifat rahasia dan hanya untuk keperluan internal.',
                'status' => 'Selesai',
                'tanggal_disposisi' => Carbon::now()->subDay(),
                'batas_waktu' => Carbon::now()->subHours(12),
            ],

            // ANCHOR: Data untuk 30 Hari Terakhir (Current Month)
            [
                'isi_instruksi' => 'Mohon analisis dan review laporan keuangan bulanan sebelum disampaikan ke pimpinan.',
                'sifat' => 'Penting',
                'catatan' => 'Pastikan semua angka dan analisis sudah sesuai dengan standar akuntansi.',
                'status' => 'Dikerjakan',
                'tanggal_disposisi' => Carbon::now()->subDays(12),
                'batas_waktu' => Carbon::now()->addDays(5),
            ],
            [
                'isi_instruksi' => 'Mohon koordinasi dengan bagian SDM untuk menentukan peserta pelatihan yang sesuai.',
                'sifat' => 'Biasa',
                'catatan' => 'Prioritaskan pegawai yang memerlukan pengembangan kompetensi.',
                'status' => 'Menunggu',
                'tanggal_disposisi' => Carbon::now()->subDays(18),
                'batas_waktu' => Carbon::now()->addDays(7),
            ],
            [
                'isi_instruksi' => 'Mohon sosialisasi perubahan kebijakan kepada seluruh pegawai di bagian masing-masing.',
                'sifat' => 'Segera',
                'catatan' => 'Perubahan kebijakan ini akan berlaku efektif mulai bulan depan.',
                'status' => 'Dikerjakan',
                'tanggal_disposisi' => Carbon::now()->subDays(25),
                'batas_waktu' => Carbon::now()->addDays(3),
            ],

            // ANCHOR: Data untuk 90 Hari Terakhir (Last 3 Months)
            [
                'isi_instruksi' => 'Mohon persiapan dokumen dan data untuk monitoring dan evaluasi program kerja.',
                'sifat' => 'Penting',
                'catatan' => 'Monitoring akan dilaksanakan secara berkala setiap bulan.',
                'status' => 'Menunggu',
                'tanggal_disposisi' => Carbon::now()->subDays(40),
                'batas_waktu' => Carbon::now()->addDays(14),
            ],
            [
                'isi_instruksi' => 'Mohon koordinasi teknis untuk implementasi sistem informasi yang baru.',
                'sifat' => 'Biasa',
                'catatan' => 'Kerjasama ini akan meningkatkan efisiensi layanan publik.',
                'status' => 'Selesai',
                'tanggal_disposisi' => Carbon::now()->subDays(55),
                'batas_waktu' => Carbon::now()->subDays(35),
            ],
            [
                'isi_instruksi' => 'Mohon review dan evaluasi kinerja instansi berdasarkan indikator yang telah ditetapkan.',
                'sifat' => 'Rahasia',
                'catatan' => 'Hasil evaluasi akan digunakan untuk perbaikan kinerja ke depan.',
                'status' => 'Dikerjakan',
                'tanggal_disposisi' => Carbon::now()->subDays(70),
                'batas_waktu' => Carbon::now()->addDays(21),
            ],

            // ANCHOR: Data untuk Tahun 2024
            [
                'isi_instruksi' => 'Mohon persiapan laporan akhir tahun 2024 dan evaluasi pencapaian target.',
                'sifat' => 'Penting',
                'catatan' => 'Laporan ini akan digunakan untuk perencanaan tahun 2025.',
                'status' => 'Selesai',
                'tanggal_disposisi' => Carbon::create(2024, 12, 10),
                'batas_waktu' => Carbon::create(2024, 12, 20),
            ],
            [
                'isi_instruksi' => 'Mohon koordinasi untuk persiapan rencana kerja tahun 2025.',
                'sifat' => 'Segera',
                'catatan' => 'Rencana kerja harus diselesaikan sebelum akhir tahun.',
                'status' => 'Dikerjakan',
                'tanggal_disposisi' => Carbon::create(2024, 11, 25),
                'batas_waktu' => Carbon::create(2024, 12, 15),
            ],
            [
                'isi_instruksi' => 'Mohon persiapan dokumen untuk audit kinerja triwulan III 2024.',
                'sifat' => 'Penting',
                'catatan' => 'Dokumen harus lengkap dan sesuai dengan standar audit.',
                'status' => 'Menunggu',
                'tanggal_disposisi' => Carbon::create(2024, 10, 15),
                'batas_waktu' => Carbon::create(2024, 10, 25),
            ],

            // ANCHOR: Data untuk Tahun 2023
            [
                'isi_instruksi' => 'Mohon evaluasi kinerja akhir tahun 2023 dan analisis pencapaian target.',
                'sifat' => 'Penting',
                'catatan' => 'Evaluasi ini penting untuk perbaikan kinerja tahun 2024.',
                'status' => 'Selesai',
                'tanggal_disposisi' => Carbon::create(2023, 12, 20),
                'batas_waktu' => Carbon::create(2023, 12, 30),
            ],
            [
                'isi_instruksi' => 'Mohon sosialisasi kebijakan baru yang akan berlaku tahun 2024.',
                'sifat' => 'Biasa',
                'catatan' => 'Pastikan semua pegawai memahami kebijakan baru.',
                'status' => 'Dikerjakan',
                'tanggal_disposisi' => Carbon::create(2023, 11, 20),
                'batas_waktu' => Carbon::create(2023, 12, 10),
            ],

            // ANCHOR: Data Tambahan untuk Variasi Lebih Banyak
            [
                'isi_instruksi' => 'Mohon segera proses permohonan bantuan teknis sistem informasi manajemen.',
                'sifat' => 'Segera',
                'catatan' => 'Prioritas tinggi untuk perbaikan sistem yang mengalami gangguan.',
                'status' => 'Dikerjakan',
                'tanggal_disposisi' => Carbon::now()->subDays(1),
                'batas_waktu' => Carbon::now()->addDays(2),
            ],
            [
                'isi_instruksi' => 'Mohon koordinasikan pelaksanaan workshop digitalisasi layanan publik.',
                'sifat' => 'Biasa',
                'catatan' => 'Pastikan semua stakeholder terlibat dalam workshop ini.',
                'status' => 'Dikerjakan',
                'tanggal_disposisi' => Carbon::now()->subHours(6),
                'batas_waktu' => Carbon::now()->addDays(5),
            ],
            [
                'isi_instruksi' => 'Mohon laksanakan survei kepuasan masyarakat terhadap pelayanan publik.',
                'sifat' => 'Penting',
                'catatan' => 'Gunakan metodologi yang valid dan representatif.',
                'status' => 'Dikerjakan',
                'tanggal_disposisi' => Carbon::now()->subDays(8),
                'batas_waktu' => Carbon::now()->addDays(7),
            ],
            [
                'isi_instruksi' => 'Mohon koordinasikan penanganan bencana alam dan darurat.',
                'sifat' => 'Segera',
                'catatan' => 'Pastikan kesiapsiagaan dan koordinasi dengan instansi terkait.',
                'status' => 'Dikerjakan',
                'tanggal_disposisi' => Carbon::now()->subDays(10),
                'batas_waktu' => Carbon::now()->addDays(3),
            ],
            [
                'isi_instruksi' => 'Mohon evaluasi program unggulan instansi untuk mengukur dampak implementasi.',
                'sifat' => 'Penting',
                'catatan' => 'Gunakan indikator kinerja yang telah ditetapkan sebelumnya.',
                'status' => 'Dikerjakan',
                'tanggal_disposisi' => Carbon::now()->subDays(14),
                'batas_waktu' => Carbon::now()->addDays(10),
            ],
            [
                'isi_instruksi' => 'Mohon persiapan dan pelaksanaan audit sertifikasi ISO 9001:2015.',
                'sifat' => 'Biasa',
                'catatan' => 'Pastikan semua dokumen dan prosedur sesuai standar ISO.',
                'status' => 'Dikerjakan',
                'tanggal_disposisi' => Carbon::now()->subDays(16),
                'batas_waktu' => Carbon::now()->addDays(14),
            ],
            [
                'isi_instruksi' => 'Mohon koordinasikan kerjasama riset dan pengembangan teknologi.',
                'sifat' => 'Biasa',
                'catatan' => 'Fokus pada teknologi yang mendukung peningkatan layanan publik.',
                'status' => 'Dikerjakan',
                'tanggal_disposisi' => Carbon::now()->subDays(21),
                'batas_waktu' => Carbon::now()->addDays(21),
            ],
            [
                'isi_instruksi' => 'Mohon laksanakan program peningkatan kapasitas sumber daya manusia.',
                'sifat' => 'Penting',
                'catatan' => 'Prioritaskan kompetensi yang dibutuhkan untuk peningkatan kinerja.',
                'status' => 'Dikerjakan',
                'tanggal_disposisi' => Carbon::now()->subDays(24),
                'batas_waktu' => Carbon::now()->addDays(30),
            ],
            [
                'isi_instruksi' => 'Mohon implementasikan sistem e-government untuk digitalisasi layanan.',
                'sifat' => 'Segera',
                'catatan' => 'Pastikan integrasi dengan sistem yang sudah ada.',
                'status' => 'Dikerjakan',
                'tanggal_disposisi' => Carbon::now()->subDays(35),
                'batas_waktu' => Carbon::now()->addDays(60),
            ],
            [
                'isi_instruksi' => 'Mohon laksanakan audit sistem keamanan informasi untuk perlindungan data.',
                'sifat' => 'Rahasia',
                'catatan' => 'Pastikan tidak ada kebocoran informasi selama audit.',
                'status' => 'Dikerjakan',
                'tanggal_disposisi' => Carbon::now()->subDays(42),
                'batas_waktu' => Carbon::now()->addDays(14),
            ],
            [
                'isi_instruksi' => 'Mohon laksanakan penilaian kinerja organisasi berdasarkan indikator kinerja utama.',
                'sifat' => 'Penting',
                'catatan' => 'Gunakan data yang akurat dan objektif untuk penilaian.',
                'status' => 'Dikerjakan',
                'tanggal_disposisi' => Carbon::now()->subDays(48),
                'batas_waktu' => Carbon::now()->addDays(21),
            ],
            [
                'isi_instruksi' => 'Mohon koordinasikan pengadaan barang dan jasa untuk operasional instansi.',
                'sifat' => 'Biasa',
                'catatan' => 'Pastikan sesuai dengan regulasi pengadaan yang berlaku.',
                'status' => 'Dikerjakan',
                'tanggal_disposisi' => Carbon::now()->subDays(52),
                'batas_waktu' => Carbon::now()->addDays(30),
            ],
            [
                'isi_instruksi' => 'Mohon susun standar operasional prosedur untuk efektivitas kerja.',
                'sifat' => 'Biasa',
                'catatan' => 'Pastikan SOP mudah dipahami dan dapat dilaksanakan.',
                'status' => 'Dikerjakan',
                'tanggal_disposisi' => Carbon::now()->subDays(58),
                'batas_waktu' => Carbon::now()->addDays(45),
            ],
            [
                'isi_instruksi' => 'Mohon laksanakan monitoring dan evaluasi kinerja untuk pencapaian target.',
                'sifat' => 'Penting',
                'catatan' => 'Gunakan sistem monitoring yang terintegrasi dan real-time.',
                'status' => 'Dikerjakan',
                'tanggal_disposisi' => Carbon::now()->subDays(65),
                'batas_waktu' => Carbon::now()->addDays(15),
            ],
            [
                'isi_instruksi' => 'Mohon sosialisasikan perubahan regulasi kepada stakeholder terkait.',
                'sifat' => 'Segera',
                'catatan' => 'Pastikan semua pihak memahami implikasi perubahan regulasi.',
                'status' => 'Dikerjakan',
                'tanggal_disposisi' => Carbon::now()->subDays(72),
                'batas_waktu' => Carbon::now()->addDays(10),
            ],
            [
                'isi_instruksi' => 'Mohon implementasikan sistem akuntansi berbasis akrual untuk transparansi.',
                'sifat' => 'Penting',
                'catatan' => 'Pastikan kompatibilitas dengan sistem yang sudah ada.',
                'status' => 'Dikerjakan',
                'tanggal_disposisi' => Carbon::now()->subDays(78),
                'batas_waktu' => Carbon::now()->addDays(90),
            ],
            [
                'isi_instruksi' => 'Mohon koordinasikan program pemberdayaan masyarakat untuk partisipasi publik.',
                'sifat' => 'Biasa',
                'catatan' => 'Libatkan masyarakat dalam perencanaan dan pelaksanaan program.',
                'status' => 'Dikerjakan',
                'tanggal_disposisi' => Carbon::now()->subDays(85),
                'batas_waktu' => Carbon::now()->addDays(60),
            ],
            [
                'isi_instruksi' => 'Mohon laksanakan audit kepatuhan internal untuk ketaatan regulasi.',
                'sifat' => 'Rahasia',
                'catatan' => 'Pastikan kerahasiaan dan objektivitas dalam audit.',
                'status' => 'Dikerjakan',
                'tanggal_disposisi' => Carbon::now()->subDays(88),
                'batas_waktu' => Carbon::now()->addDays(21),
            ],
            [
                'isi_instruksi' => 'Mohon implementasikan konsep smart office untuk efisiensi kerja.',
                'sifat' => 'Biasa',
                'catatan' => 'Fokus pada teknologi yang meningkatkan produktivitas.',
                'status' => 'Dikerjakan',
                'tanggal_disposisi' => Carbon::now()->subDays(95),
                'batas_waktu' => Carbon::now()->addDays(120),
            ],
            [
                'isi_instruksi' => 'Mohon koordinasikan pengelolaan aset negara untuk optimalisasi pemanfaatan.',
                'sifat' => 'Penting',
                'catatan' => 'Pastikan inventarisasi aset yang akurat dan terupdate.',
                'status' => 'Dikerjakan',
                'tanggal_disposisi' => Carbon::now()->subDays(102),
                'batas_waktu' => Carbon::now()->addDays(45),
            ],
            [
                'isi_instruksi' => 'Mohon laksanakan program sertifikasi kompetensi profesi untuk kualitas SDM.',
                'sifat' => 'Biasa',
                'catatan' => 'Prioritaskan kompetensi yang sesuai dengan kebutuhan instansi.',
                'status' => 'Dikerjakan',
                'tanggal_disposisi' => Carbon::now()->subDays(108),
                'batas_waktu' => Carbon::now()->addDays(90),
            ],
            [
                'isi_instruksi' => 'Mohon implementasikan sistem manajemen risiko untuk identifikasi risiko.',
                'sifat' => 'Penting',
                'catatan' => 'Gunakan metodologi yang terstandar untuk identifikasi risiko.',
                'status' => 'Dikerjakan',
                'tanggal_disposisi' => Carbon::now()->subDays(115),
                'batas_waktu' => Carbon::now()->addDays(60),
            ],
            [
                'isi_instruksi' => 'Mohon koordinasikan program inovasi untuk solusi kreatif pelayanan publik.',
                'sifat' => 'Biasa',
                'catatan' => 'Libatkan seluruh stakeholder dalam proses inovasi.',
                'status' => 'Dikerjakan',
                'tanggal_disposisi' => Carbon::now()->subDays(120),
                'batas_waktu' => Carbon::now()->addDays(75),
            ],
            [
                'isi_instruksi' => 'Mohon laksanakan audit sistem informasi manajemen untuk keamanan teknologi.',
                'sifat' => 'Rahasia',
                'catatan' => 'Pastikan keamanan data dan sistem selama audit.',
                'status' => 'Dikerjakan',
                'tanggal_disposisi' => Carbon::now()->subDays(125),
                'batas_waktu' => Carbon::now()->addDays(21),
            ],
            [
                'isi_instruksi' => 'Mohon implementasikan good corporate governance untuk transparansi.',
                'sifat' => 'Penting',
                'catatan' => 'Pastikan implementasi sesuai dengan standar internasional.',
                'status' => 'Dikerjakan',
                'tanggal_disposisi' => Carbon::now()->subDays(130),
                'batas_waktu' => Carbon::now()->addDays(90),
            ],
            [
                'isi_instruksi' => 'Mohon koordinasikan program corporate social responsibility untuk kontribusi sosial.',
                'sifat' => 'Biasa',
                'catatan' => 'Fokus pada program yang berdampak positif bagi masyarakat.',
                'status' => 'Dikerjakan',
                'tanggal_disposisi' => Carbon::now()->subDays(135),
                'batas_waktu' => Carbon::now()->addDays(60),
            ],
            [
                'isi_instruksi' => 'Mohon implementasikan sistem manajemen lingkungan untuk operasional ramah lingkungan.',
                'sifat' => 'Penting',
                'catatan' => 'Pastikan compliance dengan regulasi lingkungan yang berlaku.',
                'status' => 'Dikerjakan',
                'tanggal_disposisi' => Carbon::now()->subDays(140),
                'batas_waktu' => Carbon::now()->addDays(120),
            ],
            [
                'isi_instruksi' => 'Mohon koordinasikan program keselamatan dan kesehatan kerja untuk perlindungan karyawan.',
                'sifat' => 'Segera',
                'catatan' => 'Prioritaskan keselamatan dan kesehatan sebagai aset utama.',
                'status' => 'Dikerjakan',
                'tanggal_disposisi' => Carbon::now()->subDays(145),
                'batas_waktu' => Carbon::now()->addDays(30),
            ],
            [
                'isi_instruksi' => 'Mohon implementasikan sistem manajemen mutu untuk kualitas produk dan layanan.',
                'sifat' => 'Penting',
                'catatan' => 'Pastikan standar mutu yang konsisten dan terukur.',
                'status' => 'Dikerjakan',
                'tanggal_disposisi' => Carbon::now()->subDays(150),
                'batas_waktu' => Carbon::now()->addDays(90),
            ],
            [
                'isi_instruksi' => 'Mohon koordinasikan program pengembangan usaha kecil dan menengah.',
                'sifat' => 'Biasa',
                'catatan' => 'Fokus pada program yang dapat meningkatkan kapasitas UKM.',
                'status' => 'Dikerjakan',
                'tanggal_disposisi' => Carbon::now()->subDays(155),
                'batas_waktu' => Carbon::now()->addDays(75),
            ],
            [
                'isi_instruksi' => 'Mohon implementasikan sistem manajemen energi untuk efisiensi penggunaan energi.',
                'sifat' => 'Biasa',
                'catatan' => 'Gunakan teknologi ramah lingkungan untuk efisiensi energi.',
                'status' => 'Dikerjakan',
                'tanggal_disposisi' => Carbon::now()->subDays(160),
                'batas_waktu' => Carbon::now()->addDays(90),
            ],
            [
                'isi_instruksi' => 'Mohon koordinasikan program pengembangan pariwisata untuk pertumbuhan ekonomi.',
                'sifat' => 'Biasa',
                'catatan' => 'Libatkan masyarakat lokal dalam pengembangan pariwisata.',
                'status' => 'Dikerjakan',
                'tanggal_disposisi' => Carbon::now()->subDays(165),
                'batas_waktu' => Carbon::now()->addDays(60),
            ],
            [
                'isi_instruksi' => 'Mohon implementasikan sistem manajemen pengetahuan untuk pembelajaran organisasi.',
                'sifat' => 'Penting',
                'catatan' => 'Pastikan knowledge sharing yang efektif antar unit.',
                'status' => 'Dikerjakan',
                'tanggal_disposisi' => Carbon::now()->subDays(170),
                'batas_waktu' => Carbon::now()->addDays(120),
            ],
            [
                'isi_instruksi' => 'Mohon koordinasikan program digitalisasi untuk efisiensi layanan publik.',
                'sifat' => 'Segera',
                'catatan' => 'Prioritaskan layanan yang paling dibutuhkan masyarakat.',
                'status' => 'Dikerjakan',
                'tanggal_disposisi' => Carbon::now()->subDays(175),
                'batas_waktu' => Carbon::now()->addDays(90),
            ],
            [
                'isi_instruksi' => 'Mohon implementasikan sistem manajemen proyek untuk efektivitas program kerja.',
                'sifat' => 'Penting',
                'catatan' => 'Gunakan metodologi manajemen proyek yang terstandar.',
                'status' => 'Dikerjakan',
                'tanggal_disposisi' => Carbon::now()->subDays(180),
                'batas_waktu' => Carbon::now()->addDays(60),
            ],

            // ANCHOR: Data untuk Tahun 2022
            [
                'isi_instruksi' => 'Mohon persiapan laporan akhir tahun 2022 dengan evaluasi pencapaian target.',
                'sifat' => 'Penting',
                'catatan' => 'Gunakan data yang akurat dan komprehensif untuk evaluasi.',
                'status' => 'Selesai',
                'tanggal_disposisi' => Carbon::create(2022, 12, 15),
                'batas_waktu' => Carbon::create(2022, 12, 25),
            ],
            [
                'isi_instruksi' => 'Mohon susun rencana kerja tahun 2023 dengan program dan target pencapaian.',
                'sifat' => 'Segera',
                'catatan' => 'Pastikan rencana kerja realistis dan dapat dicapai.',
                'status' => 'Selesai',
                'tanggal_disposisi' => Carbon::create(2022, 11, 20),
                'batas_waktu' => Carbon::create(2022, 12, 5),
            ],
            [
                'isi_instruksi' => 'Mohon laksanakan audit kinerja triwulan III 2022 dengan rekomendasi perbaikan.',
                'sifat' => 'Penting',
                'catatan' => 'Fokus pada identifikasi area yang perlu perbaikan.',
                'status' => 'Selesai',
                'tanggal_disposisi' => Carbon::create(2022, 10, 10),
                'batas_waktu' => Carbon::create(2022, 10, 25),
            ],
            [
                'isi_instruksi' => 'Mohon implementasikan sistem akuntansi berbasis akrual untuk transparansi keuangan.',
                'sifat' => 'Penting',
                'catatan' => 'Pastikan pelatihan yang memadai untuk pengguna sistem.',
                'status' => 'Selesai',
                'tanggal_disposisi' => Carbon::create(2022, 9, 5),
                'batas_waktu' => Carbon::create(2022, 12, 31),
            ],
            [
                'isi_instruksi' => 'Mohon koordinasikan program reformasi birokrasi untuk peningkatan kualitas pelayanan.',
                'sifat' => 'Segera',
                'catatan' => 'Libatkan seluruh stakeholder dalam proses reformasi.',
                'status' => 'Selesai',
                'tanggal_disposisi' => Carbon::create(2022, 8, 15),
                'batas_waktu' => Carbon::create(2022, 12, 31),
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