# **User Routes Documentation**

## **1. Pendahuluan**

Dokumen ini menjelaskan routes yang telah disetup untuk manajemen user dalam sistem E-Arsip. Routes ini mengikuti pattern yang sama dengan routes bagian untuk konsistensi.

## **2. Routes yang Sudah Disetup**

### **2.1. Lokasi File**
- **File**: `routes/web.php`
- **Middleware**: `auth` (memerlukan autentikasi)
- **Controller**: `UserController`

### **2.2. Daftar Routes**

| Method | URI | Action | Name | Description |
|--------|-----|--------|------|-------------|
| GET | `/user` | `UserController@index` | `user.index` | Halaman utama manajemen user |
| POST | `/user` | `UserController@store` | `user.store` | Tambah user baru |
| PUT | `/user/{id}` | `UserController@update` | `user.update` | Update data user |
| DELETE | `/user/{id}` | `UserController@destroy` | `user.destroy` | Hapus user |
| GET | `/user/{id}` | `UserController@show` | `user.show` | Detail user (opsional) |

## **3. Detail Routes**

### **3.1. GET /user (user.index)**
- **Purpose**: Menampilkan halaman utama manajemen user
- **View**: `pages.user.index`
- **Data**: List semua user dengan pagination dan search
- **Access**: Admin only

### **3.2. POST /user (user.store)**
- **Purpose**: Menyimpan user baru
- **Method**: POST
- **Validation**: Username, email, password, role required
- **Redirect**: Back to user.index dengan success message

### **3.3. PUT /user/{id} (user.update)**
- **Purpose**: Update data user existing
- **Method**: PUT (via form method spoofing)
- **Parameters**: `{id}` - ID user yang akan diupdate
- **Validation**: Same as store
- **Redirect**: Back to user.index dengan success message

### **3.4. DELETE /user/{id} (user.destroy)**
- **Purpose**: Menghapus user
- **Method**: DELETE (via form method spoofing)
- **Parameters**: `{id}` - ID user yang akan dihapus
- **Confirmation**: Via modal confirmation
- **Redirect**: Back to user.index dengan success message

### **3.5. GET /user/{id} (user.show)**
- **Purpose**: Menampilkan detail user (opsional)
- **Method**: GET
- **Parameters**: `{id}` - ID user
- **View**: Detail user (jika diperlukan)

## **4. Middleware Protection**

### **4.1. Authentication Middleware**
```php
Route::middleware(['auth'])->group(function () {
    // User management routes
});
```

### **4.2. Access Control**
- **Admin**: Full access (CRUD operations)
- **Staf**: No access (sesuai requirement)

## **5. Route Naming Convention**

### **5.1. Naming Pattern**
- **Resource**: `user`
- **Actions**: `index`, `store`, `update`, `destroy`, `show`
- **Names**: `user.{action}`

### **5.2. Consistency**
- Mengikuti pattern yang sama dengan routes bagian
- Naming convention Laravel standard

## **6. Form Method Spoofing**

### **6.1. PUT Method**
```html
<input type="hidden" name="_method" value="PUT">
```

### **6.2. DELETE Method**
```html
<input type="hidden" name="_method" value="DELETE">
```

## **7. CSRF Protection**

### **7.1. CSRF Token**
```html
@csrf
```

### **7.2. Automatic Protection**
- Semua routes POST, PUT, DELETE otomatis protected
- Token validation di middleware

## **8. Route Parameters**

### **8.1. ID Parameter**
- **Type**: Integer
- **Validation**: Required, exists in users table
- **Usage**: `{id}` untuk update, delete, show

### **8.2. Query Parameters**
- **Search**: `?search=keyword` untuk pencarian
- **Page**: `?page=1` untuk pagination

## **9. Redirect Patterns**

### **9.1. Success Redirects**
- **Store**: `redirect()->route('user.index')->with('success', 'User berhasil ditambahkan')`
- **Update**: `redirect()->route('user.index')->with('success', 'User berhasil diupdate')`
- **Delete**: `redirect()->route('user.index')->with('success', 'User berhasil dihapus')`

### **9.2. Error Redirects**
- **Validation Error**: `redirect()->back()->withErrors($validator)->withInput()`
- **Not Found**: `abort(404)` atau redirect dengan error message

## **10. Route Testing**

### **10.1. Manual Testing**
```bash
# Test routes
php artisan route:list --name=user
```

### **10.2. Browser Testing**
- `/user` - Halaman index
- Form submission untuk CRUD operations
- Modal interactions

## **11. Integration dengan Views**

### **11.1. Form Actions**
```html
<!-- Store -->
<form action="{{ route('user.store') }}" method="POST">

<!-- Update -->
<form action="{{ route('user.update', $user->id) }}" method="POST">
<input type="hidden" name="_method" value="PUT">

<!-- Delete -->
<form action="{{ route('user.destroy', $user->id) }}" method="POST">
<input type="hidden" name="_method" value="DELETE">
```

### **11.2. Links**
```html
<!-- Index -->
<a href="{{ route('user.index') }}">User Management</a>

<!-- Show -->
<a href="{{ route('user.show', $user->id) }}">Detail User</a>
```

## **12. Security Considerations**

### **12.1. Authentication**
- Semua routes protected dengan `auth` middleware
- User harus login untuk mengakses

### **12.2. Authorization**
- Hanya Admin yang dapat mengakses (implementasi di controller)
- Staf tidak memiliki akses (sesuai requirement)

### **12.3. CSRF Protection**
- Semua form menggunakan CSRF token
- Automatic validation di middleware

## **13. Next Steps**

### **13.1. Controller Implementation**
- Buat `UserController` dengan semua method
- Implementasi CRUD operations
- Validation dan error handling

### **13.2. Testing**
- Unit tests untuk controller methods
- Feature tests untuk routes
- Browser tests untuk UI interactions

## **14. Notes**

✅ **Routes Setup**: Semua routes sudah disetup dengan benar

✅ **Consistency**: Mengikuti pattern yang sama dengan routes bagian

✅ **Security**: Protected dengan authentication middleware

🔄 **Next**: Implementasi controller dan integrasi dengan database
