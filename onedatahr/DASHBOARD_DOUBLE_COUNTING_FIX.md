# Dashboard Recruitment - Fix Double Counting Issue

## 🎯 Masalah Yang Diperbaiki

Dashboard recruitment sebelumnya **menghitung double** karena:
- Menggunakan tabel `proses_rekrutmen` yang menyimpan tanggal-tanggal
- Satu kandidat bisa tercatat di beberapa tahapan jika melewati banyak tahap
- Contoh: Kandidat A yang lolos CV, Psikotes, dan HR akan dihitung **3 kali** (sekali di setiap tahapan)

## ✅ Solusi Yang Diterapkan

Sekarang dashboard menggunakan **field `status_akhir` dari tabel `kandidat`**:
- **SATU KANDIDAT = SATU PERHITUNGAN** berdasarkan tahapan terakhir mereka
- Tidak ada double counting
- Lebih akurat sesuai dengan KandidatObserver logic

## 📊 Perubahan Logika Perhitungan

### Sebelumnya (SALAH):
```php
// Menggunakan tabel proses_rekrutmen dengan tanggal
$cvLolos = DB::table('proses_rekrutmen')
    ->where('cv_lolos', 1)
    ->count(); // Bisa double count jika ada banyak proses

$psikotesLolos = DB::table('proses_rekrutmen')
    ->where('psikotes_lolos', 1)
    ->count(); // Dihitung ulang dari record yang sama
```

### Sekarang (BENAR):
```php
// Menggunakan status_akhir dari tabel kandidat
$cvLolos = DB::table('kandidat')
    ->whereIn('status_akhir', [
        'CV Lolos',
        'Psikotes Lolos',
        'Tes Kompetensi Lolos',
        'Interview HR Lolos',
        'Interview User Lolos',
        'Diterima'
    ])
    ->count(); // Setiap kandidat hanya dihitung 1x di tahapan tertinggi mereka

$psikotesLolos = DB::table('kandidat')
    ->whereIn('status_akhir', [
        'Psikotes Lolos',
        'Tes Kompetensi Lolos',
        'Interview HR Lolos',
        'Interview User Lolos',
        'Diterima'
    ])
    ->count(); // Hanya menghitung yang minimal sudah lolos psikotes
```

## 🔄 Mapping Status_Akhir ke Tahapan

```
Tahapan          | Status_Akhir Yang Included
==================|================================
CV Lolos         | CV Lolos, Psikotes Lolos, Tes Kompetensi Lolos, Interview HR Lolos, Interview User Lolos, Diterima
Psikotes Lolos   | Psikotes Lolos, Tes Kompetensi Lolos, Interview HR Lolos, Interview User Lolos, Diterima
Kompetensi Lolos | Tes Kompetensi Lolos, Interview HR Lolos, Interview User Lolos, Diterima
HR Lolos         | Interview HR Lolos, Interview User Lolos, Diterima
User Lolos       | Interview User Lolos, Diterima
Hired/Diterima   | Diterima
```

## 📝 File Yang Diubah

### [RecruitmentDashboardController.php](app/Http/Controllers/RecruitmentDashboardController.php)

#### Method 1: `index()` (Lines 48-109)
**Sebelum:** Menggunakan `whereNotNull('tgl_lolos_cv')` dll  
**Sesudah:** Menggunakan `whereIn('status_akhir', [...])`
- Total Pelamar: `status_akhir != 'Tidak Lolos'`
- Setiap tahapan: Hanya yang status_akhir-nya >= tahapan tersebut

#### Method 2: `dashboardStats()` (Lines 679-752)
**Sebelum:** Menggunakan tabel `proses_rekrutmen`  
**Sesudah:** Sepenuhnya menggunakan tabel `kandidat` dengan status_akhir
- Lebih sederhana dan akurat
- Consistent dengan KandidatObserver

#### Method 3: `statsByPosition` (Lines 124-137)
**Sebelum:** Case when dengan `tgl_lolos_*`  
**Sesudah:** Case when dengan `status_akhir IN (...)`
- Perhitungan per posisi juga akurat sekarang

## 📈 Contoh Hasil Sebelum vs Sesudah

### Skenario:
```
Posisi: Backend Developer
- Kandidat A: Status_akhir = 'Interview HR Lolos' (lolos CV, Psikotes, Kompetensi, HR)
- Kandidat B: Status_akhir = 'CV Lolos' (hanya lolos CV)
- Kandidat C: Status_akhir = 'Tidak Lolos'
- Kandidat D: Status_akhir = 'Diterima'

Total Pelamar: 4 (tidak termasuk Tidak Lolos)
```

| Tahapan | Sebelumnya (SALAH) | Sekarang (BENAR) | Penjelasan |
|---------|-------------------|-----------------|------------|
| CV Lolos | 4 (A,B,D + double A) | 3 (A, B, D) | ✅ Setiap 1x saja |
| Psikotes Lolos | 2 (A + double A) | 2 (A, D) | ✅ Tidak termasuk yang hanya CV |
| Kompetensi Lolos | 2 (A + double A) | 2 (A, D) | ✅ Akurat |
| HR Lolos | 2 (A + double A) | 2 (A, D) | ✅ Akurat |
| User Lolos | 0 | 1 (D) | ✅ Hanya yang lolos user |
| Diterima | 0 | 1 (D) | ✅ Hanya yang diterima |

## ✨ Keuntungan

✅ **Tidak ada double counting**
- Satu kandidat = satu perhitungan
- Dashboard metrics menjadi akurat

✅ **Konsisten dengan KandidatObserver**
- Menggunakan logic yang sama untuk status_akhir
- Progress_rekrutmen di tabel posisi juga akurat

✅ **Lebih performant**
- Tidak perlu join dengan `proses_rekrutmen`
- Query lebih sederhana

✅ **Mudah dipahami**
- Logic based on status, bukan tanggal
- Lebih intuitif

## 🧪 Testing

### Test 1: Verifikasi Dashboard Metrics
```
1. Buka Dashboard Recruitment (/rekrutmen/dashboard)
2. Verifikasi total di funnel chart:
   - Total Pelamar harusnya = jumlah unik kandidat aktif
   - Setiap tahapan harusnya <= tahapan sebelumnya (funnel shape)
3. Bandingkan dengan database:
   SELECT status_akhir, COUNT(*) FROM kandidat GROUP BY status_akhir;
```

### Test 2: Filter by Posisi
```
1. Filter dashboard by posisi tertentu
2. Verify metrics hanya menghitung kandidat dari posisi itu
3. Cek conversion rate masuk akal
```

### Test 3: API Endpoint
```bash
curl "http://localhost:8000/api/recruitment/dashboard-stats?posisi_id=1"
# Verify JSON response memiliki data yang akurat
```

## 🐛 Troubleshooting

### Metrics masih tidak sesuai?
1. Cek database field `status_akhir` punya data
2. Run: `php artisan view:clear`
3. Cek apakah user mengubah status_akhir melalui form (bukan direct DB)

### Conversion rate tidak masuk akal?
1. Cek apakah total pelamar > 0
2. Verify setiap tahapan <= tahapan sebelumnya
3. Jika tidak, ada data yang tidak konsisten di DB

