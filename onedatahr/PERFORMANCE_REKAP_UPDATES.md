# Performance Rekap - Update Features (v2.0)

## 📋 Ringkasan Update

Tampilan rekap performance telah diperbarui dengan fitur-fitur lengkap untuk superadmin, termasuk:

### 1️⃣ Executive Summary (KPI Dashboard)

Menampilkan ringkasan performa karyawan secara keseluruhan:

- **Rata-rata Final Score**: Nilai rata-rata KPI (70%) + KBI (30%) seluruh karyawan
- **Total Karyawan Dinilai**: Jumlah total karyawan yang telah dievaluasi
- **Distribusi Grade A**: Jumlah karyawan dengan grade A (score ≥ 89)
- **Grade B+C**: Jumlah karyawan dengan grade B+C (score 69-88)
- **% Bawah Standar**: Persentase karyawan dengan grade D (score < 69)

### 2️⃣ Filter Dimensi Organisasi

Filter komprehensif untuk melihat data berdasarkan struktur organisasi:

- **Tahun**: Filter berdasarkan tahun penilaian
- **Perusahaan**: Filter berdasarkan perusahaan/entitas bisnis
- **Divisi**: Filter berdasarkan divisi organisasi
- **Departemen**: Filter berdasarkan departemen spesifik
- **Grade**: Filter berdasarkan grade (A, B, C, D)
- **Nama/NIK**: Pencarian karyawan berdasarkan nama atau NIK

### 3️⃣ Highlight Anomali

Menampilkan insight anomali otomatis untuk perhatian khusus:

- **⚠ Grade D Terbanyak**: Divisi mana yang memiliki karyawan dengan grade D paling banyak
- **📈 Performa Terbaik**: Divisi dengan rata-rata score tertinggi

### 4️⃣ Grouping & Summary Per Divisi

Data ditampilkan terstruktur dengan grouping:

- **Header Divisi**: Nama divisi dengan rata-rata score keseluruhan
- **Grade Distribution**: Breakdown jumlah karyawan per grade (A, B, C, D) per divisi
- **Detail Tabel**: List karyawan dalam divisi dengan semua metrik
- **Summary Baris**: Statistik lengkap per divisi

### 5️⃣ Aksi & Keputusan

Action buttons untuk keputusan operasional:

- **📥 Export Laporan**: Export data penilaian ke Excel/PDF per tahun atau divisi
- **🔒 Kunci Nilai (Lock Period)**: Mengunci nilai penilaian agar tidak bisa diubah
- **🖨️ Print Laporan**: Mencetak laporan untuk keperluan dokumentasi

**Fitur Tambahan per Karyawan:**

- **⚠️ Tandai Perlu Evaluasi**: Menandai karyawan dengan grade D untuk follow-up
- **👁️ Lihat Detail**: Melihat detail breakdown penilaian karyawan

---

## 🔧 Perubahan Backend

### 1. PerformanceController Update

File: `app/Http/Controllers/PerformanceController.php`

**Perubahan:**

- Menambahkan eager load untuk `division`, `department`, `company`
- Menambahkan filter untuk perusahaan, divisi, departemen
- Menghitung executive summary (rata-rata, distribusi grade, % bawah standar)
- Menghitung anomali (divisi dengan grade D terbanyak, divisi performa terbaik)
- Grouping data berdasarkan divisi dengan summary per group
- Menyediakan data untuk dropdown filter (companies, divisions, departments)

**Method Baru:**

- `hitungLonjakan()`: Menghitung divisi dengan performa terbaik

### 2. Data yang Dikirim ke View

```php
compact(
    'rekap',                    // Paginated data karyawan
    'tahun',                    // Tahun yang dipilih
    'totalKaryawan',            // Total karyawan dinilai
    'avgFinalScore',            // Rata-rata final score
    'gradeDistribution',        // Array: [A => count, B => count, ...]
    'belowStandard',            // Jumlah karyawan grade D
    'pctBelowStandard',         // Persentase karyawan grade D
    'divisiGradeD',             // Divisi dengan grade D terbanyak
    'lonjakan',                 // Divisi dengan performa terbaik
    'groupedByDivisi',          // Data grouped by divisi
    'companies',                // Dropdown options
    'divisions',                // Dropdown options
    'departments'               // Dropdown options
)
```

---

## 🎨 Perubahan Frontend

### 1. Tampilan Baru

File: `resources/views/pages/performance/rekap.blade.php`

**Sections:**

1. **Header**: Judul dan deskripsi halaman
2. **Executive Summary Cards**: 5 card KPI utama
3. **Anomali Highlights**: Alert box untuk anomali terdeteksi
4. **Filter Form**: Filter komprehensif dengan grid layout
5. **Grouped Tables**: Data per divisi dengan summary
6. **Action Buttons**: Export, lock, print

### 2. Design Improvements

- Menggunakan Tailwind gradient backgrounds
- Dark mode support penuh
- Responsive grid layout
- Better visual hierarchy dengan warna-kode
- Icon-enhanced buttons dan alerts
- Smooth transitions dan hover effects

---

## 📦 Dependency Requirements

Pastikan models berikut memiliki relasi yang tepat:

### Karyawan Model

```php
public function division() { return $this->belongsTo(Division::class, 'division_id'); }
public function department() { return $this->belongsTo(Department::class, 'department_id'); }
public function company() { return $this->belongsTo(Company::class, 'company_id'); }
public function pekerjaan() { return $this->hasMany(Pekerjaan::class, 'karyawan_id'); }
```

### Models Required

- `App\Models\Karyawan`
- `App\Models\Company`
- `App\Models\Division`
- `App\Models\Department`
- `App\Models\KbiAssessment`
- `App\Models\KpiAssessment`

---

## 🚀 Fitur yang Akan Datang (TODO)

Fitur berikut masih dalam tahap implementasi:

1. **Export to Excel**: Implementasi export menggunakan Maatwebsite/Laravel-Excel
2. **Export to PDF**: Implementasi export PDF dengan DOMPDF
3. **Lock Period**: Menyimpan lock status di database
4. **Mark as Needs Evaluation**: Menyimpan flag evaluasi di database
5. **History Tracking**: Mencatat perubahan status evaluasi

---

## 🔐 Security & Permissions

Akses diproteksi berdasarkan role:

- **Superadmin**: Melihat semua karyawan
- **Admin**: Melihat semua karyawan
- **Manager/GM**: Hanya melihat bawahan langsung
- **Staff**: Hanya melihat diri sendiri

---

## 📊 Database Schema (Recommended)

Jika ingin menambahkan kolom untuk lock period dan evaluasi status:

```sql
-- Add columns to assessment tables
ALTER TABLE kpi_assessments ADD COLUMN locked_at TIMESTAMP NULL;
ALTER TABLE kbi_assessments ADD COLUMN locked_at TIMESTAMP NULL;

-- New table for evaluation tracking
CREATE TABLE performance_evaluations (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    karyawan_id BIGINT NOT NULL,
    tahun INT NOT NULL,
    status ENUM('normal', 'perlu_evaluasi', 'dalam_evaluasi', 'selesai') DEFAULT 'normal',
    notes TEXT NULL,
    created_by BIGINT,
    updated_by BIGINT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (karyawan_id) REFERENCES karyawans(id_karyawan)
);
```

---

## 📝 Usage Examples

### Viewing Report

1. Go to: `/performance/rekap`
2. Default menampilkan tahun saat ini
3. Gunakan filter untuk narrow down data
4. Click "Cari & Filter" untuk apply filters
5. Click "Reset" untuk clear semua filter

### Exporting Data

1. Set filter yang diinginkan
2. Click "📥 Export Laporan"
3. Pilih format (Excel/PDF) dan periode
4. File akan di-download

### Locking Period

1. Pastikan semua penilaian sudah selesai
2. Click "🔒 Kunci Nilai"
3. Confirm action
4. Nilai akan terkunci dan tidak bisa diubah

---

## 🐛 Known Issues & Limitations

1. **Export functionality**: Belum terimplementasi (buttons ada tapi belum functional)
2. **Lock functionality**: Belum terimplementasi (buttons ada tapi belum functional)
3. **Pagination**: Data grouped by divisi - pagination di level top, bukan per divisi
4. **Performance**: Untuk dataset besar (>1000 karyawan), mungkin perlu optimasi query

---

## 🔄 Future Enhancements

- [ ] Real-time export functionality
- [ ] Scheduled exports (auto-generate monthly/quarterly)
- [ ] Advanced analytics (trend analysis, predictions)
- [ ] Comparative analysis (year-over-year)
- [ ] Custom report builder
- [ ] Email notifications untuk grade D employees
- [ ] Integration dengan performance development plan
- [ ] Approval workflow untuk lock period

---

## 👨‍💻 Support & Documentation

Untuk pertanyaan atau issues, silakan hubungi:

- HRD Team
- System Administrator

Last Updated: {{ date('Y-m-d H:i:s') }}
