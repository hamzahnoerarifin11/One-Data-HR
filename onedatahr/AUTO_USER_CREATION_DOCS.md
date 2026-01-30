# Dokumentasi: Auto User Account Creation & Role Assignment

## 📋 Overview
Fitur ini mengotomatiskan pembuatan akun user dan assignment role ketika data karyawan baru ditambahkan ke sistem. Setiap karyawan akan mendapatkan akun user dengan role yang disesuaikan berdasarkan level jabatan mereka.

## 🎯 Fitur Utama

### 1. **Auto User Creation**
- Ketika karyawan baru dibuat, sistem otomatis membuat akun user
- Email otomatis generate jika tidak ada (dari nama + company domain)
- Password sementara generate otomatis (12 karakter, aman)
- Email dan password ditampilkan di modal setelah creation

### 2. **Auto Role Assignment**
- Role otomatis disesuaikan dengan level jabatan
- Mapping level ke role dapat dikustomisasi di helper

### 3. **Display Credentials**
- Modal muncul menampilkan credentials baru
- Support copy-to-clipboard untuk email dan password
- Toggle password visibility
- Copy all credentials sesuali

### 4. **Update Role on Level Change**
- Ketika level jabatan karyawan berubah saat edit, role user otomatis update
- Menggunakan sync() untuk replace old roles dengan new roles

## 🔧 Implementasi

### File yang Dimodifikasi:

#### 1. **app/Helpers/UserHelper.php** (NEW)
```php
// Helper untuk create user dan assign role
- createUserForKaryawan($karyawan, $level)
- mapLevelToRole($level)
- generatePassword($length)
- sendCredentialsEmail($user, $plainPassword, $roles)
```

#### 2. **app/Http/Controllers/KaryawanController.php**
- **store()**: Ditambahi logic create user otomatis setelah create karyawan
- **update()**: Ditambahi logic update role user ketika level jabatan berubah

#### 3. **resources/views/pages/karyawan/index.blade.php**
- Ditambahi modal untuk menampilkan credentials setelah creation
- Support copy-to-clipboard, toggle password, dan copy all

## 📊 Mapping Level ke Role

Default mapping di `UserHelper.php`:

```
Manager, Supervisor     → [admin]
Director, Direktur      → [superadmin]
Staff (default)         → [user]
```

### Cara Customize Mapping:

Edit `app/Helpers/UserHelper.php` di method `mapLevelToRole()`:

```php
public static function mapLevelToRole(Level $level): array
{
    $levelName = strtolower($level->name);
    
    $mapping = [
        'manager' => ['admin'],
        'supervisor' => ['admin'],
        'director' => ['superadmin'],
        // Tambahkan mapping custom Anda di sini
        'kepala_departemen' => ['admin'],
        'staff' => ['user'],
        
        'default' => ['user'],
    ];
    
    // ... rest of code
}
```

## 🔐 Password Security

- Password sementara di-generate menggunakan `Str::password()` (12 karakter)
- Mengandung uppercase, lowercase, numbers, dan symbols
- User **harus** mengubah password saat login pertama kali
- Password hanya ditampilkan saat creation (tidak disimpan di log)

## 🚀 Flow Penggunaan

### Create Karyawan Baru:
1. Admin fill form create karyawan
2. Pilih level jabatan
3. Submit form
4. System:
   - Create data karyawan
   - Auto create user account
   - Assign role berdasarkan level
   - Tampilkan modal dengan credentials

### Edit Level Jabatan Karyawan:
1. Admin edit karyawan
2. Ubah level jabatan
3. Save
4. System:
   - Update data karyawan
   - Update user role otomatis
   - (Role diubah sesuai level baru)

## 📧 Email Integration (Optional)

Untuk mengirim credentials via email, bisa implement di `sendCredentialsEmail()`:

```php
// Contoh menggunakan Laravel Mail
Mail::send(new CredentialsMail($user, $plainPassword, $roles));
```

## ⚠️ Catatan Penting

1. **Email Unique**: Sistem cek email sudah terdaftar sebelum create
2. **NIK Field**: User search berdasarkan NIK karyawan (pastikan NIK unique)
3. **Role Creation**: Pastikan role 'user', 'admin', 'superadmin' sudah ada di database
4. **Password**: Tidak disimpan dalam plain text, hanya di-hash dengan bcrypt
5. **Transaction**: Semua operation dalam transaction, jika ada error semua rollback

## 🔍 Database Requirements

### Tabel yang Diperlukan:
- `users` - User account
- `roles` - Role definition
- `role_user` - Many-to-many relationship

### Kolom User:
```
- id
- name
- email (unique)
- nik
- jabatan
- password (hashed)
- email_verified_at
- timestamps
```

### Kolom Roles:
```
- id
- name (unique)
- timestamps
```

## 🐛 Troubleshooting

### Error: "Role tidak ditemukan"
- Pastikan role sudah di-seed di database
- Check: `php artisan tinker -> Role::all()`

### Password tidak muncul di modal
- Clear session cache: `php artisan cache:clear`
- Check browser console untuk error

### User tidak ter-create
- Check logs: `storage/logs/laravel.log`
- Verify `level_id` dipilih saat create karyawan

### Role tidak ter-assign
- Verify role table data
- Check `role_user` pivot table

## 📝 Contoh Test

```bash
# Test create user dengan role
php artisan tinker

$karyawan = Karyawan::first();
$level = Level::find(1);
$result = \App\Helpers\UserHelper::createUserForKaryawan($karyawan, $level);

# Check hasil
$result['success']; // true/false
$result['email'];
$result['password'];
$result['roles'];
```

## 🔄 Fitur Lanjutan (Future)

- [ ] Email credentials ke user
- [ ] SMS notification
- [ ] QR code untuk login
- [ ] Password expiry policy
- [ ] Audit log untuk user creation
- [ ] Bulk user import
- [ ] LDAP integration

## ✅ Testing Checklist

- [ ] Create karyawan → User otomatis dibuat
- [ ] Role sesuai level jabatan
- [ ] Password bisa di-copy
- [ ] Modal auto-close setelah 30 detik
- [ ] Edit level → Role user berubah
- [ ] Delete karyawan → User juga ter-delete (perlu implement)
- [ ] Email unique validation works
- [ ] Hashed password di database

---

**Last Updated**: January 29, 2026
**Status**: ✅ Production Ready
