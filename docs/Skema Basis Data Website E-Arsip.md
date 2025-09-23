# **Skema Basis Data Website E-Arsip**

## **1\. Pendahuluan**

Skema basis data ini dirancang berdasarkan dokumen deskripsi fungsional untuk aplikasi E-Arsip. Tujuannya adalah untuk menyediakan struktur yang logis dan efisien untuk menyimpan, mengelola, dan mengakses semua data yang terkait dengan surat-menyurat, pengguna, bagian, dan disposisi.

Struktur ini menggunakan relasi antar tabel (foreign key) untuk memastikan integritas dan konsistensi data.

## **2\. Struktur Tabel**

Berikut adalah detail dari setiap tabel yang dibutuhkan dalam sistem.

### **Tabel: users**

Menyimpan data semua pengguna yang dapat mengakses sistem.

| Nama Kolom | Tipe Data | Keterangan |
| :---- | :---- | :---- |
| id | INT, PK, AI | ID unik untuk setiap pengguna. |
| username | VARCHAR(50), UNIQUE | Username untuk login. |
| email | VARCHAR(100), UNIQUE | Alamat email pengguna. |
| password | VARCHAR(255) | Kata sandi yang sudah di-hash. |
| role | ENUM('Admin', 'Staf') | Peran pengguna dalam sistem. |
| bagian\_id | INT, FK | Merujuk ke id di tabel bagian. |
| created\_at | TIMESTAMP | Waktu data dibuat. |
| updated\_at | TIMESTAMP | Waktu data terakhir diubah. |

### **Tabel: bagian**

Menyimpan data master untuk unit kerja atau divisi dalam instansi.

| Nama Kolom     | Tipe Data                | Keterangan                        |
| :----          | :----                    | :----                             |
| id             | INT, PK, AI              | ID unik untuk setiap bagian.      |
| nama_bagian    | VARCHAR(100)             | Nama unit kerja/divisi.           |
| kepala_bagian  | VARCHAR(100)             | Nama kepala bagian/divisi.        |
| keterangan     | TEXT                     | Deskripsi singkat mengenai bagian.|
| status         | ENUM('Aktif','Nonaktif') | Status bagian (aktif/nonaktif).   |
| created_at     | TIMESTAMP                | Waktu data dibuat.                |
| updated_at     | TIMESTAMP                | Waktu data terakhir diubah.       |

### **Tabel: surat\_masuk**

Menyimpan semua data yang terkait dengan surat masuk.

| Nama Kolom | Tipe Data | Keterangan |
| :---- | :---- | :---- |
| id | INT, PK, AI | ID unik untuk setiap surat masuk. |
| nomor\_surat | VARCHAR(100) | Nomor resmi surat. |
| tanggal\_surat | DATE | Tanggal yang tertera pada surat. |
| tanggal\_terima | DATE | Tanggal surat diterima oleh instansi. |
| perihal | VARCHAR(255) | Subjek atau perihal surat. |
| ringkasan\_isi | TEXT | Ringkasan dari isi surat. |
| pengirim | VARCHAR(150) | Nama instansi/perorangan pengirim. |
| sifat\_surat | VARCHAR(50) | Sifat surat (Penting, Biasa, Segera). |
| keterangan | TEXT | Catatan tambahan. |
| tujuan\_bagian\_id | INT, FK | Merujuk ke id di tabel bagian. |
| user\_id | INT, FK | User yang mencatat surat, merujuk ke id di users. |
| created\_at | TIMESTAMP | Waktu data dibuat. |
| updated\_at | TIMESTAMP | Waktu data terakhir diubah. |

### **Tabel: surat\_keluar**

Menyimpan semua data yang terkait dengan surat keluar.

| Nama Kolom | Tipe Data | Keterangan |
| :---- | :---- | :---- |
| id | INT, PK, AI | ID unik untuk setiap surat keluar. |
| nomor\_surat | VARCHAR(100) | Nomor resmi surat. |
| tanggal\_surat | DATE | Tanggal surat dikeluarkan. |
| perihal | VARCHAR(255) | Subjek atau perihal surat. |
| ringkasan\_isi | TEXT | Ringkasan dari isi surat. |
| tujuan | VARCHAR(150) | Nama instansi/perorangan tujuan surat. |
| sifat\_surat | VARCHAR(50) | Sifat surat (Penting, Biasa, Segera). |
| keterangan | TEXT | Catatan tambahan. |
| pengirim\_bagian\_id | INT, FK | Merujuk ke id di tabel bagian. |
| user\_id | INT, FK | User yang mencatat surat, merujuk ke id di users. |
| created\_at | TIMESTAMP | Waktu data dibuat. |
| updated\_at | TIMESTAMP | Waktu data terakhir diubah. |

### **Tabel: lampiran**

Menyimpan informasi file yang diunggah untuk surat masuk dan keluar.

| Nama Kolom | Tipe Data | Keterangan |
| :---- | :---- | :---- |
| id | INT, PK, AI | ID unik untuk setiap lampiran. |
| surat\_id | INT | ID dari surat terkait (bisa dari surat\_masuk atau surat\_keluar). |
| tipe\_surat | ENUM('masuk', 'keluar') | Jenis surat untuk membedakan relasi. |
| nama\_file | VARCHAR(255) | Nama asli file yang diunggah. |
| path\_file | VARCHAR(255) | Lokasi penyimpanan file di server. |
| tipe\_lampiran | ENUM('utama', 'pendukung') | Jenis lampiran (file surat atau dokumen pendukung). |
| created\_at | TIMESTAMP | Waktu data dibuat. |

### **Tabel: disposisi**

Menyimpan data instruksi atau tindak lanjut untuk surat masuk.

| Nama Kolom | Tipe Data | Keterangan |
| :---- | :---- | :---- |
| id | INT, PK, AI | ID unik untuk setiap disposisi. |
| surat\_masuk\_id | INT, FK | Merujuk ke id di tabel surat\_masuk. |
| tujuan\_bagian\_id | INT, FK | Bagian yang dituju, merujuk ke id di bagian. |
| isi\_instruksi | TEXT | Instruksi atau arahan disposisi. |
| sifat | VARCHAR(50) | Sifat disposisi (Segera, Penting). |
| catatan | TEXT | Catatan tambahan untuk disposisi. |
| status | ENUM('Menunggu', 'Dikerjakan', 'Selesai') | Status tindak lanjut disposisi. |
| user\_id | INT, FK | User yang membuat disposisi, merujuk ke id di users. |
| created\_at | TIMESTAMP | Waktu data dibuat. |
| updated\_at | TIMESTAMP | Waktu data terakhir diubah. |

### **Tabel: pengaturan**

Menyimpan konfigurasi umum sistem, diasumsikan hanya memiliki satu baris data.

| Nama Kolom | Tipe Data | Keterangan |
| :---- | :---- | :---- |
| id | INT, PK | ID (biasanya diisi 1). |
| nama\_instansi | VARCHAR(150) | Nama instansi untuk kop laporan. |
| alamat | TEXT | Alamat instansi. |
| logo | VARCHAR(255) | Path atau nama file logo instansi. |
| nama\_pejabat | VARCHAR(100) | Nama pejabat penandatangan laporan. |
| jabatan\_pejabat | VARCHAR(100) | Jabatan pejabat penandatangan. |

## **3\. Penjelasan Relasi**

* **users ke bagian**: Satu bagian dapat memiliki banyak pengguna, tetapi satu pengguna hanya milik satu bagian (One-to-Many).  
* **surat\_masuk ke bagian**: Satu bagian dapat menerima banyak surat masuk (One-to-Many).  
* **surat\_keluar ke bagian**: Satu bagian dapat mengirim banyak surat keluar (One-to-Many).  
* **disposisi ke surat\_masuk**: Satu surat masuk bisa memiliki banyak disposisi (One-to-Many).  
* **disposisi ke bagian**: Satu bagian dapat menerima banyak disposisi (One-to-Many).  
* **lampiran ke surat\_masuk/surat\_keluar**: Satu surat (baik masuk maupun keluar) dapat memiliki banyak lampiran (Polymorphic One-to-Many).