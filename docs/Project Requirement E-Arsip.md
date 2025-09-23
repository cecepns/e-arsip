# **Project Requirement E-Arsip**

## **1\. Ringkasan Umum**

Website E-Arsip adalah sistem digital terintegrasi yang dirancang untuk mengelola surat masuk dan keluar secara efisien. Sistem ini tidak hanya berfungsi sebagai platform pengarsipan digital, tetapi juga mencakup manajemen alur distribusi dan tindak lanjut surat melalui fitur disposisi. Fokus utamanya adalah sentralisasi dan otomatisasi proses administrasi surat-menyurat untuk meningkatkan produktivitas dan ketertelusuran dokumen.

Fungsi Utama:  
Menjadi alat bantu digital untuk pengelolaan surat secara menyeluruh, mulai dari pencatatan, distribusi, disposisi, hingga pengarsipan dan kemudahan akses kembali oleh unit kerja terkait.

### **1.1. Fitur Antarmuka Umum**

* **Mode Tampilan (Tema):** Seluruh antarmuka aplikasi, kecuali halaman login, mendukung dua mode tampilan: mode terang (light mode) dan mode gelap (dark mode). Pengguna dapat mengganti tema sesuai preferensi untuk kenyamanan visual.  
* **Notifikasi:** Peringatan untuk aktivitas terbaru (contoh: "Surat masuk baru diterima", "2 disposisi menunggu persetujuan").

## **2\. Fitur Utama**

Fitur utama adalah komponen sistem yang secara langsung mendukung alur kerja pengelolaan surat.

### **2.1. Dashboard**

Halaman utama yang menyajikan ringkasan visual dan informasi penting secara *real-time*.

* **Kartu Statistik:** Menampilkan jumlah total surat masuk, surat keluar, dan disposisi.  
* **Grafik (Chart):** Visualisasi data surat dengan filter berdasarkan rentang waktu (misal: 30, 60, 90 hari) dan tahun.  
* **Tabel Aktivitas Terbaru:** Daftar 5-10 surat masuk atau keluar yang baru saja ditambahkan.  
* **Hak Akses:**  
  * **Admin:** Dapat melihat semua statistik dan aktivitas dari seluruh bagian.  
  * **Staf:** Hanya melihat statistik dan aktivitas yang relevan dengan bagiannya.

### **2.2. Manajemen Surat Masuk**

Fitur untuk mencatat, mendistribusikan, dan mengarsipkan semua surat yang diterima instansi.

#### **Halaman Utama (Tampilan Kelola)**

Menampilkan daftar semua surat masuk dalam format tabel yang informatif.

* **Tampilan Tabel:**  
  * **Kolom Data:** Nomor, Nomor Surat, Tanggal Surat, Tanggal Terima, Perihal, Ringkasan Isi, Pengirim, dan Bagian Tujuan.  
  * **Lampiran:** Ikon yang menandakan adanya lampiran dan dapat diklik untuk melihat.  
* **Fitur Tambahan:**  
  * **Tombol Aksi Utama:** Tambah Data, Filter (untuk pencarian spesifik), dan Refresh.  
  * **Aksi per Baris:** Opsi Lihat Detail, Edit, dan Hapus.

#### **Formulir Tambah/Edit Data**

* **Input Data Surat:** Nomor Surat, Tanggal Surat, Tanggal Diterima, Perihal, Ringkasan Isi, Pengirim, Bagian Tujuan, Sifat Surat, Keterangan.  
* **Unggah Lampiran:**  
  * **File Surat Utama (Wajib):** Format **PDF**.  
  * **Dokumen Pendukung (Opsional):** Format **ZIP, RAR, DOCX, XLSX**.  
* **Integrasi Disposisi:** Opsi untuk langsung membuat disposisi saat surat dicatat.

#### **Halaman Detail Surat**

Menyajikan informasi lengkap satu surat masuk, termasuk semua data dari formulir dan tombol untuk melihat/mengunduh lampiran.

#### **Hak Akses**

* **Admin & Staf:** Keduanya memiliki akses penuh (Tambah, Lihat, Edit, Hapus, Upload Lampiran, Buat Disposisi). Hak akses Staf terbatas pada surat yang terkait dengan bagiannya.

### **2.3. Manajemen Surat Keluar**

Fitur untuk mencatat dan mengarsipkan semua surat yang dikeluarkan instansi.

#### **Halaman Utama (Tampilan Kelola)**

Menampilkan daftar semua surat keluar dalam format tabel.

* **Tampilan Tabel:**  
  * **Kolom Data:** Nomor, Nomor Surat, Tanggal Surat, Perihal, Ringkasan Isi, Tujuan, dan Bagian Pengirim.  
  * **Lampiran:** Ikon untuk melihat lampiran.  
* **Fitur Tambahan:** Tombol Tambah Data, Filter, dan Refresh.  
* **Aksi per Baris:** Opsi Lihat Detail, Edit, dan Hapus.

#### **Formulir Tambah/Edit Data**

* **Input Data Surat:** Nomor Surat, Tanggal Surat, Perihal, Ringkasan Isi, Tujuan Surat, Bagian Pengirim, Sifat Surat, Keterangan.  
* **Unggah Lampiran:**  
  * **File Surat Utama (Wajib):** Format **PDF**.  
  * **Dokumen Pendukung (Opsional):** Format **ZIP, RAR, DOCX, XLSX**.

#### **Halaman Detail Surat**

Menyajikan informasi lengkap satu surat keluar dengan akses untuk melihat/mengunduh lampiran.

#### **Hak Akses**

* **Admin & Staf:** Keduanya memiliki akses penuh (Tambah, Lihat, Edit, Hapus). Hak akses Staf terbatas pada surat yang terkait dengan bagiannya.

### **2.4. Manajemen Bagian (Divisi)**

Fitur data master untuk mengelola unit kerja di dalam instansi.

#### **Halaman Utama (Tampilan Kelola)**

* **Tampilan Tabel**:  
  **Kolom:** No, Nama Bagian, Kepala Bagian,  Surat (counter surat masuk dan keluar), Jumlah Staff, Status  
* **Aksi:** Tambah Data, Lihat Detail, Edit, Hapus.

#### **Halaman Detail Bagian (Khusus Admin) (Popup)**

Popup ini diakses melalui tombol Lihat Detail dan menampilkan:

* **Informasi Bagian:** No, Nama Bagian, Kepala Bagian, Jumlah Staff, Surat (counter surat masuk dan keluar), Status  
* **Tabel Surat Masuk:** Daftar semua surat yang ditujukan ke bagian tersebut.  
* **Tabel Surat Keluar:** Daftar semua surat yang dikirim dari bagian tersebut.

#### **Formulir Tambah/Edit Data (Popup)**

* **Input Data:** Nama Bagian(text), Kepala Bagian(text), Deskripsi(textarea) , Status(select)  
* **Action Buttons:** Batal, Simpan 

#### **Hak Akses**

* **Admin:** Memiliki akses penuh (Tambah, Lihat Detail, Edit, Hapus).  
* **Staf:** Tidak memiliki akses ke menu ini.

### **2.5. Manajemen Pengguna (User)**

Fitur data master untuk mengelola akun dan hak akses pengguna sistem.

#### **Halaman Utama (Tampilan Kelola)**

* **Tampilan Tabel:** Daftar pengguna dengan kolom No, Username, Email, Bagian, Password  dan Role.  
* **Aksi:** Tambah Data, Edit, Hapus.

#### **Formulir Tambah/Edit Data**

* **Input Data:** Username(text), Email(email), Bagian(select), Password(password)  dan Role(select).

#### **Hak Akses**

* **Admin:** Memiliki akses penuh (Tambah, Edit, Hapus, Lihat).  
* **Staf:** Tidak memiliki akses ke menu ini.

## **3\. Fitur Pendukung**

### **3.1. Disposisi**

Fitur untuk memberikan instruksi atau tindak lanjut terhadap surat masuk.

#### **Halaman Utama (Tampilan Kelola)**

* **Tampilan Tabel:** Daftar disposisi dengan kolom Nomor Surat, Tujuan Disposisi, Isi Instruksi, dan Status Tindak Lanjut.  
* **Fitur Tambahan:** Filter berdasarkan status atau bagian.  
* **Aksi per Baris:** Lihat Detail, Edit, Hapus, dan Update Status (khusus Staf).  
* **Catatan:** Pembuatan disposisi baru dilakukan terintegrasi melalui formulir pada menu **Manajemen Surat Masuk**. Tidak ada tombol Tambah Data di halaman ini.

#### **Formulir Edit Disposisi**

* **Input Data:** Tujuan (Bagian), Isi Instruksi, Sifat (Segera, Penting), Catatan.

#### **Proses Tindak Lanjut**

* Staf di bagian tujuan menerima notifikasi dan dapat memperbarui status disposisi (misal: "Menunggu", "Dikerjakan", "Selesai").

#### **Hak Akses**

* **Admin:** Dapat **Melihat, Mengedit, dan Menghapus** disposisi. Admin tidak dapat memperbarui status tindak lanjut.  
* **Staf:** Dapat **Melihat, Mengedit, Menghapus,** dan **Memperbarui Status** tindak lanjut.

### **3.2. Laporan**

Fitur untuk merekap dan mengekspor data surat.

* **Filter Data:** Berdasarkan rentang tanggal, tahun, bagian, dan jenis surat (masuk/keluar).  
* **Format Laporan:** Menampilkan data dalam format tabel yang rapi.  
* **Ekspor Dokumen:** Opsi unduh dalam format **PDF** atau **Cetak** langsung dari browser.  
* **Hak Akses:**  
  * **Admin:** Dapat memfilter dan mengunduh semua laporan dari semua bagian.  
  * **Staf:** Hanya dapat memfilter dan mengunduh laporan yang relevan dengan bagiannya.

### **3.3. Pengaturan**

Halaman konfigurasi untuk menyesuaikan informasi dasar sistem.

* **Fungsi:** Mengatur identitas instansi (Nama, Alamat, Logo) dan pejabat penandatangan untuk kop laporan.  
* **Hak Akses:**  
  * **Admin:** Memiliki akses penuh.  
  * **Staf:** Tidak memiliki akses.

### **3.4. Profil Pengguna**

Halaman personal untuk setiap pengguna.

* **Fungsi:** Mengubah informasi akun (Nama, Telepon, Foto) dan kata sandi.  
* **Hak Akses:**  
  * **Admin & Staf:** Keduanya dapat mengakses dan mengedit profil masing-masing.

## **4\. Peran Pengguna (Roles)**

### **4.1. Admin**

* **Kewenangan Penuh:** Mengelola seluruh data surat (masuk dan keluar) dari semua bagian.  
* **Manajemen Data Master:** Mengelola data pengguna dan bagian, termasuk melihat riwayat surat per bagian.  
* **Manajemen Disposisi:** Membuat disposisi (via surat masuk), melihat, mengedit, dan menghapus disposisi untuk semua surat.  
* **Akses Laporan:** Mengakses dan menghasilkan laporan dari seluruh bagian.  
* **Konfigurasi Sistem:** Mengakses halaman pengaturan.  
* **Profil:** Mengelola profil pribadi.

### **4.2. Staf**

* **Manajemen Surat Terbatas:** Mengelola penuh (tambah, lihat, edit, hapus) data surat masuk dan keluar yang terkait **hanya dengan bagiannya**.  
* **Manajemen Disposisi & Aksi:** Membuat disposisi (via surat masuk), melihat, mengedit, menghapus, dan **memperbarui status tindak lanjutnya**.  
* **Akses Laporan Terbatas:** Mengakses dan menghasilkan laporan yang relevan dengan bagiannya.  
* **Profil:** Mengelola profil pribadi.

