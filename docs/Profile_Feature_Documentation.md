# Fitur Halaman Profile - E-Arsip

## Deskripsi
Halaman profile memungkinkan user untuk melihat dan mengupdate informasi pribadi mereka serta mengubah kata sandi dengan keamanan yang baik.

## Fitur Utama

### 1. **View Profile Information**
- Menampilkan informasi user yang sedang login
- Data yang ditampilkan:
  - Username (read-only)
  - Role (read-only)
  - Nama Lengkap (editable)
  - Email (editable)
  - Nomor Telepon (editable)
  - Bagian (read-only, jika ada)
  - Status Kepala Bagian (read-only, jika ada)
  - Tanggal Terdaftar (read-only)
  - Terakhir Diperbarui (read-only)
  - Status Akun (read-only)

### 2. **Update Profile Data**
- Form terpisah untuk mengupdate data pribadi
- Validasi:
  - Nama lengkap: required, string, max 255 karakter
  - Email: required, valid email format, unique, max 255 karakter
  - Nomor telepon: optional, string, max 20 karakter
- AJAX form submission dengan error handling
- Toast notifications untuk feedback

### 3. **Change Password**
- Form terpisah untuk mengubah password
- Validasi keamanan:
  - Password lama: required, harus sesuai dengan password saat ini
  - Password baru: required, minimal 8 karakter dengan kombinasi:
    - Huruf besar (A-Z)
    - Huruf kecil (a-z)
    - Angka (0-9)
    - Simbol (!@#$%^&*)
  - Konfirmasi password: required, harus sama dengan password baru
- Toggle visibility untuk semua field password
- AJAX form submission dengan error handling

## File yang Dibuat/Dimodifikasi

### Controller
- `app/Http/Controllers/ProfileController.php` - Controller untuk handle profile operations

### Routes
- `routes/web.php` - Menambahkan routes untuk profile:
  - `GET /profile` - Menampilkan halaman profile
  - `PUT /profile` - Update profile data
  - `PUT /profile/password` - Update password

### Views
- `resources/views/pages/profile/index.blade.php` - Halaman profile utama
- `resources/views/layouts/admin.blade.php` - Update navigation menu dan user info

## Keamanan
- CSRF protection pada semua form
- Validasi password dengan Laravel Password rules
- AJAX error handling dengan trait AjaxErrorHandler
- Password hashing otomatis dengan Laravel Hash
- Input sanitization dan validation

## UI/UX Features
- Responsive design mengikuti pola aplikasi
- Toggle password visibility
- Real-time validation feedback
- Toast notifications untuk success/error
- Loading states pada form submission
- Form reset setelah password berhasil diubah
- User info dinamis di sidebar dan navbar

## Cara Mengakses
1. Login ke aplikasi
2. Klik dropdown user di navbar (nama user)
3. Pilih "Profile"
4. Atau akses langsung via URL: `/profile`

## Testing
- Routes terdaftar dengan benar
- Cache sudah dibersihkan
- Server dapat berjalan tanpa error
- Semua fitur mengikuti pola aplikasi yang ada
