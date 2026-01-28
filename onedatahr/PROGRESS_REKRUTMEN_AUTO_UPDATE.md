# Progress Rekrutmen Auto-Update System

## 🎯 Cara Kerja

System progress rekrutmen di aplikasi HR Wadja sudah dirancang untuk **otomatis update** berdasarkan tahapan kandidat menggunakan **Observer Pattern** Laravel.

### Flow:

```
1. User mengubah status_akhir kandidat di form
   ↓
2. KandidatObserver::saving() menangkap perubahan
   - Otomatis isi field tanggal (tgl_lolos_cv, tgl_lolos_psikotes, dll) 
     berdasarkan status_akhir yang dipilih
   ↓
3. KandidatObserver::saved() dipanggil setelah data tersimpan
   - Panggil refreshPosisiProgress($posisi_id)
   ↓
4. refreshPosisiProgress() menganalisis SEMUA kandidat di posisi tersebut
   - Hitung total pelamar aktif (status_akhir != 'Tidak Lolos')
   - Ambil status_akhir masing-masing kandidat
   - Tentukan progress berdasarkan TAHAP TERTINGGI yang dicapai ada kandidat
   ↓
5. Update kolom progress_rekrutmen di tabel posisi
   - progress_rekrutmen otomatis berubah sesuai logika
   ↓
6. PosisiController::manage() hanya menampilkan data yang sudah di-update observer
   - Tidak perlu query kompleks, langsung ambil field progress_rekrutmen
```

## 📊 Logika Progress Rekrutmen

Progress ditentukan berdasarkan **TAHAP TERTINGGI** yang dicapai oleh **MINIMAL 1 KANDIDAT** di posisi tersebut:

| Progress | Kondisi |
|----------|---------|
| **Rekrutmen Selesai** | Ada kandidat dengan status `Diterima` |
| **Pemberkasan** | Ada kandidat dengan status `Interview User Lolos` |
| **Interview User** | Ada kandidat dengan status `Interview HR Lolos` |
| **Interview HR** | Ada kandidat dengan status `Tes Kompetensi Lolos` (tapi bukan CV Lolos saja) |
| **Pre Interview** | Ada kandidat dengan status `Psikotes Lolos` atau `Tes Kompetensi Lolos` |
| **Menerima Kandidat** | Tidak ada kandidat aktif / semua masih CV Lolos |
| **Tidak Menerima Kandidat** | Posisi status = `Nonaktif` |

### Contoh Skenario:

**Posisi: Backend Developer**

**Kandidat Saat Ini:**
- Rina: CV Lolos
- Budi: Psikotes Lolos
- Aji: Interview HR Lolos ← **TAHAP TERTINGGI**
- Siti: Tidak Lolos (tidak dihitung)

**Progress Rekrutmen:** Interview User ✅

Ketika Aji naik menjadi Interview User Lolos → progress otomatis menjadi **Pemberkasan**

## 🔧 File-File Yang Terlibat

### 1. **KandidatObserver** (`app/Observers/KandidatObserver.php`)
- **saving()**: Menangkap perubahan status_akhir dan otomatis isi kolom tanggal
- **saved()**: Trigger refresh progress untuk posisi terkait
- **deleted()**: Update progress saat kandidat dihapus
- **refreshPosisiProgress()**: Main logic yang menghitung progress

### 2. **PosisiObserver** (`app/Observers/PosisiObserver.php`)
- **updated()**: Jika status posisi berubah, refresh progress candidates

### 3. **PosisiController** (`app/Http/Controllers/PosisiController.php`)
- **manage()**: Hanya load data dari DB, tidak menghitung
  ```php
  $pos = Posisi::withCount('kandidat as total_pelamar_view')
      ->orderBy('id_posisi', 'DESC')
      ->get();
  ```

### 4. **Posisi Model** (`app/Models/Posisi.php`)
- Relasi `kandidat()` yang digunakan observer
- Field `progress_rekrutmen` dan `total_pelamar` di fillable

### 5. **Kandidat Model** (`app/Models/Kandidat.php`)
- Fields: tgl_lolos_cv, tgl_lolos_psikotes, tgl_lolos_kompetensi, tgl_lolos_hr, tgl_lolos_user
- Relasi ke Posisi

## ✅ Cara Testing

### Test 1: Update Status Kandidat
```
1. Buka Manajemen Kandidat
2. Edit salah satu kandidat
3. Ubah status_akhir ke "CV Lolos"
4. Simpan
5. Buka Manajemen Posisi → progress untuk posisi tersebut harus berubah
```

### Test 2: Verifikasi Auto-Fill Tanggal
```
1. Edit kandidat dan ubah status_akhir ke "Psikotes Lolos"
2. Simpan
3. Lihat detail kandidat → field tgl_lolos_psikotes harus terisi otomatis hari ini
```

### Test 3: Cek Database Langsung
```sql
-- Cek status terakhir yang tersimpan
SELECT id_posisi, nama_posisi, progress_rekrutmen, total_pelamar 
FROM posisi;

-- Cek kandidat di posisi tertentu
SELECT nama, status_akhir, tgl_lolos_cv, tgl_lolos_psikotes 
FROM kandidat 
WHERE posisi_id = 1;
```

## 🐛 Troubleshooting

### Progress tidak berubah setelah ubah status kandidat?

1. **Cek apakah Observer ter-register:**
   ```bash
   php artisan tinker
   > App\Models\Kandidat::observe(App\Observers\KandidatObserver::class);
   > exit
   ```

2. **Test observer secara manual:**
   ```bash
   php artisan tinker
   > $k = App\Models\Kandidat::first();
   > $k->status_akhir = 'CV Lolos';
   > $k->save();
   > App\Models\Posisi::find($k->posisi_id)->progress_rekrutmen;
   > exit
   ```

3. **Cek tabel posisi apakah memiliki field progress_rekrutmen:**
   ```sql
   DESC posisi;
   ```
   Jika belum ada, jalankan migration untuk menambahkan field tersebut.

### Masih ada field lama di view?

Jalankan:
```bash
php artisan view:clear
```

## 📈 Best Practices

✅ **DO:**
- Selalu update status_akhir kandidat (jangan update tgl_lolos_* langsung)
- Observer akan otomatis isi kolom tanggal sesuai status

✅ **DON'T:**
- Jangan bypass observer dengan raw query
- Jangan manipulasi progress_rekrutmen secara manual (biarkan observer handle)
- Jangan update tgl_lolos_* tanpa update status_akhir terlebih dahulu

## 🚀 Performance Notes

- Observer hanya dijalankan saat save() atau delete()
- `refreshPosisiProgress()` menggunakan `withCount()` yang efficient
- Tidak ada background job, semua synchronous (instant update)

