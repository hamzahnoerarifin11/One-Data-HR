# Dokumentasi Fitur Level Jabatan

## Ringkasan
Saya telah membuat fitur lengkap untuk manajemen **Level Jabatan** dengan controller, views, dan terintegrasi dengan form create karyawan.

## File yang Telah Dibuat

### 1. Controller
**File:** [app/Http/Controllers/LevelController.php](app/Http/Controllers/LevelController.php)

**Method:**
- `index()` - Menampilkan list semua level jabatan dengan pagination, search, dan sort
- `create()` - Form untuk membuat level baru (optional)
- `store(Request $request)` - Menyimpan level baru via API/Form (return JSON)
- `edit(Level $level)` - Form edit level
- `update(Request $request, Level $level)` - Update level (return JSON)
- `destroy(Level $level)` - Hapus level dengan validasi (cek apakah level sedang digunakan)
- `getLevels()` - API endpoint untuk fetch semua level

**Fitur:**
- Validasi unique name dan level_order
- Validasi duplikasi level_order
- Cegah penghapusan level yang sedang digunakan oleh karyawan
- Return JSON response untuk AJAX requests

### 2. Views

#### a. Index Page (List dengan Modal)
**File:** [resources/views/pages/organization/level/index.blade.php](resources/views/pages/organization/level/index.blade.php)

**Fitur:**
- Table interaktif dengan pagination, search, dan sorting
- Tampilkan jumlah karyawan yang menggunakan level
- Modal untuk Add/Edit Level Jabatan
- Modal konfirmasi untuk delete dengan cek validasi
- Support Dark Mode
- Responsive design (mobile friendly)
- Button "Tambah Level Jabatan" di header

**Kolom Tabel:**
- No
- Nama Level Jabatan
- Urutan (dengan badge)
- Jumlah Karyawan (dengan badge)
- Tanggal Dibuat
- Aksi (Edit, Hapus)

#### b. Level Modal (dalam Index)
**Komponen Modal:**
- Form input untuk Nama Level dan Urutan
- Validasi real-time
- Error messages inline
- Loading state saat submit
- Success/Error alerts

#### c. Partial Modal untuk Create Karyawan
**File:** [resources/views/pages/karyawan/partials/level-modal.blade.php](resources/views/pages/karyawan/partials/level-modal.blade.php)

**Fitur:**
- Modal yang dapat diakses dari form create karyawan
- Tombol "Tambah Level" di sebelah label Level Jabatan
- Auto-close dan refresh select list setelah berhasil menambah
- Integrasi seamless dengan form create karyawan

### 3. Routes
**File:** [routes/web.php](routes/web.php)

**Route Resource:**
```
GET    /organization/level              → LevelController@index (organization.level.index)
GET    /organization/level/create       → LevelController@create (organization.level.create)
POST   /organization/level              → LevelController@store (organization.level.store)
GET    /organization/level/{level}      → LevelController@show (organization.level.show)
GET    /organization/level/{level}/edit → LevelController@edit (organization.level.edit)
PUT    /organization/level/{level}      → LevelController@update (organization.level.update)
DELETE /organization/level/{level}      → LevelController@destroy (organization.level.destroy)
GET    /organization/level/api/get-levels → LevelController@getLevels (organization.level.api)
```

**Middleware:**
- `auth` - Harus login
- `role:admin|superadmin` - Hanya admin dan superadmin

## Integrasi dengan Create Karyawan

### Modifikasi di Form Create Karyawan
**File:** [resources/views/pages/karyawan/create.blade.php](resources/views/pages/karyawan/create.blade.php)

**Perubahan:**
1. Tambah tombol "Tambah Level" di sebelah label Level Jabatan (line ~886)
2. Tambah ID `levelSelect` pada select element untuk level
3. Include partial modal di akhir file

**Cara Penggunaan:**
1. User membuka form create karyawan
2. Pada field "Level Jabatan", user bisa klik tombol "Tambah Level"
3. Modal akan muncul untuk input nama dan urutan level baru
4. Setelah berhasil, level baru otomatis ditambahkan ke select dan dipilih
5. Form tetap terbuka dan user bisa lanjutkan isi form lainnya

## Desain UI/UX

### Consistency dengan Struktur Lain
- **Styling:** Menggunakan Tailwind CSS sama seperti company/division/department/unit/position
- **Layout:** Table interaktif dengan fitur search, sort, pagination
- **Modal:** Menggunakan Alpine.js dengan design yang konsisten
- **Colors:** 
  - Blue untuk primary action (add, edit)
  - Yellow untuk edit action
  - Red untuk delete action
  - Green untuk badge/count
- **Dark Mode:** Support penuh untuk dark mode

### Fitur UI Lanjutan
- ✅ Pagination dengan smart page display
- ✅ Search/Filter real-time
- ✅ Sortable columns
- ✅ Configurable items per page (5, 10, 25, 50)
- ✅ Modal confirm sebelum delete
- ✅ Loading states dan spinners
- ✅ Error/Success notifications
- ✅ Responsive untuk mobile

## Database Model

**Model:** [app/Models/Level.php](app/Models/Level.php)

**Struktur Database:**
```
Table: levels
- id (Primary Key)
- name (VARCHAR 255, Unique)
- level_order (INT, Unique)
- created_at (Timestamp)
- updated_at (Timestamp)
```

**Relationship:**
- Level `hasMany` Pekerjaan (via level_id)

## Validasi

### Store/Update
- `name` - Required, String, Max 255, Unique
- `level_order` - Required, Integer, Min 1, Unique

### Destroy
- Cegah hapus jika level sedang digunakan (pekerjaan_count > 0)

## Testing

Untuk test fitur:

1. **Akses Index Page:**
   - Login sebagai admin/superadmin
   - Buka: `http://localhost/organization/level`

2. **Tambah Level dari Index:**
   - Klik tombol "Tambah Level Jabatan"
   - Isi nama level (contoh: Manager, Supervisor, Staff)
   - Isi urutan level (contoh: 1, 2, 3)
   - Klik "Simpan Level"

3. **Edit Level:**
   - Klik tombol edit (pencil icon) di table
   - Update data
   - Klik "Perbarui"

4. **Delete Level:**
   - Klik tombol delete (trash icon) di table
   - Confirm di modal
   - Jika level sedang digunakan, akan ada peringatan

5. **Tambah Level dari Create Karyawan:**
   - Buka form create karyawan
   - Scroll ke field "Level Jabatan"
   - Klik tombol "Tambah Level"
   - Isi data dalam modal
   - Level baru otomatis ditambahkan ke select

## Catatan Teknis

1. **AJAX/API Response Format:**
   ```json
   {
     "success": true/false,
     "message": "string",
     "data": { id, name, level_order, created_at },
     "errors": { field: [messages] }
   }
   ```

2. **Error Handling:**
   - Validasi server-side di controller
   - Validasi client-side di modal
   - User-friendly error messages

3. **Security:**
   - CSRF token validation
   - Role-based access control (admin/superadmin only)
   - Prevent unauthorized deletion

4. **Performance:**
   - Lazy loading dengan pagination
   - Indexed queries untuk search/sort
   - Efficient relationship loading
