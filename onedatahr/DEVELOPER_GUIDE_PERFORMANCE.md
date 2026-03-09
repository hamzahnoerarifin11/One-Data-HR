# Developer Guide: Performance Rekap Module

## 🔧 Technical Implementation Guide

### Database & Model Relations

#### Model Relationships Used

```
Karyawan (1) -----> (*) Pekerjaan
                        ├---> (1) Company
                        ├---> (1) Division
                        └---> (1) Department
```

#### Key Fields

- **Karyawan**: `id_karyawan`, `NIK`, `Nama_Lengkap_Sesuai_Ijazah`
- **Pekerjaan**: `Jabatan`, `company_id`, `division_id`, `department_id`
- **KbiAssessment**: `karyawan_id`, `tahun`, `rata_rata_akhir`
- **KpiAssessment**: `karyawan_id`, `tahun`, `total_skor_akhir`

---

## 📋 Implementation Checklist

### Required Models (Verify Relations Exist)

- [x] `Karyawan::pekerjaan()` → hasMany Pekerjaan
- [x] `Pekerjaan::company()` → belongsTo Company
- [x] `Pekerjaan::division()` → belongsTo Division
- [x] `Pekerjaan::department()` → belongsTo Department
- [x] `KbiAssessment` model exists
- [x] `KpiAssessment` model exists

### Model Verification Commands

```php
// Test in tinker
php artisan tinker

// Check if relations work
$karyawan = Karyawan::with('pekerjaan')->first();
$pekerjaan = $karyawan->pekerjaan()->latest('id_pekerjaan')->first();
dd($pekerjaan->division, $pekerjaan->department, $pekerjaan->company);
```

---

## 🎯 Controller Flow

### Data Processing Pipeline

```
1. GET request dengan filter parameters
   ↓
2. Query building dengan filters
   ↓
3. Map karyawan dengan nilai KPI/KBI
   ↓
4. Calculate statistics (summary, anomaly)
   ↓
5. Group by divisi dengan summary per group
   ↓
6. Paginate results
   ↓
7. Return to view dengan semua data
```

### Key Calculations

#### Final Score Formula

```
Final Score = (KPI Score × 0.7) + (KBI Score × 0.3)
```

#### Grade Determination

```
Score ≥ 89  → Grade A (Excellent)
Score 79-88 → Grade B (Good)
Score 69-78 → Grade C (Satisfactory)
Score < 69  → Grade D (Below Standard)
```

#### Statistics Calculation

```php
$totalKaryawan = count($rekapCollection);
$avgFinalScore = $rekapCollection->avg('final_score');
$gradeDistribution = array of grade counts
$belowStandard = count(grade D employees)
$pctBelowStandard = (belowStandard / total) × 100
```

---

## 🚀 API Endpoints

### Main Route

```
GET /performance/rekap
```

### Query Parameters

```
tahun=2025              // Filter tahun (required, default: current year)
perusahaan=1            // Filter company_id
divisi=2                // Filter division_id
departemen=3            // Filter department_id
grade=A                 // Filter grade (A|B|C|D)
search=john             // Filter nama/NIK
page=1                  // Pagination
```

### Response Data Structure

```php
[
    'rekap' => LengthAwarePaginator, // Current page items
    'tahun' => integer,
    'totalKaryawan' => integer,
    'avgFinalScore' => float,
    'gradeDistribution' => [
        'A' => integer,
        'B' => integer,
        'C' => integer,
        'D' => integer,
    ],
    'belowStandard' => integer,
    'pctBelowStandard' => float,
    'divisiGradeD' => array|null,  // Divisi dengan grade D terbanyak
    'lonjakan' => array|null,       // Divisi dengan performa terbaik
    'groupedByDivisi' => Collection, // Grouped data
    'companies' => Collection,
    'divisions' => Collection,
    'departments' => Collection,
]
```

---

## 🎨 View Structure

### Key Components

#### 1. Executive Summary Cards

```blade
@include('components.card', [
    'title' => 'Avg. Final Score',
    'value' => $avgFinalScore,
    'icon' => 'chart-icon',
    'color' => 'blue'
])
```

#### 2. Anomaly Alerts

```blade
@if($divisiGradeD)
    <div class="alert alert-warning">
        {{ $divisiGradeD['divisi'] }} memiliki {{ $divisiGradeD['count'] }} Grade D
    </div>
@endif
```

#### 3. Filter Form

- Grid layout dengan 6 input fields
- Submit dan reset buttons
- Auto-submit pada tahun change

#### 4. Grouped Tables

```blade
@foreach($groupedByDivisi as $group)
    {{-- Header dengan statistik --}}
    {{-- Grade distribution bar --}}
    {{-- Tabel karyawan dalam divisi --}}
@endforeach
```

#### 5. Action Buttons

- Export (Excel/PDF)
- Lock period
- Print

---

## 🔐 Security Considerations

### Authorization

- Middleware: `auth`, `role:admin|superadmin|manager|gm`
- Manager/GM: View hanya bawahan langsung
- Staff: View hanya diri sendiri
- Admin/Superadmin: View semua

### Data Protection

- Query menggunakan parameterized filters
- Role-based access control
- Safe navigation operators (`?->`) untuk null safety

---

## 📊 Performance Optimization

### Current Bottlenecks

1. **Eager Loading**: Using `with('pekerjaan')` untuk optimize
2. **N+1 Queries**: Diminimalisir dengan eager load dan map
3. **Collection Processing**: Data di-filter dan di-group dalam collection (PHP)

### For Large Datasets (>1000 records)

```php
// Consider moving grouping to database query:
DB::table('karyawan')
    ->join('pekerjaan', 'karyawan.id_karyawan', '=', 'pekerjaan.id_karyawan')
    ->selectRaw('pekerjaan.division_id, COUNT(*) as total')
    ->groupBy('pekerjaan.division_id')
    ->get();
```

---

## 🧪 Testing

### Unit Test Example

```php
public function test_executive_summary_calculation()
{
    $response = $this->actingAs($admin)
        ->get('/performance/rekap?tahun=2025');

    $response->assertViewHas('totalKaryawan');
    $response->assertViewHas('avgFinalScore');
    $response->assertViewHas('gradeDistribution');
}
```

### Test Queries

```php
// Test filter
$response = $this->get('/performance/rekap?divisi=1&grade=A');
$this->assertCount(5, $response->viewData('rekap')); // if 5 match

// Test anomaly detection
$response = $this->get('/performance/rekap');
$this->assertNotNull($response->viewData('divisiGradeD'));
```

---

## 📝 TODO: Future Implementation

### Phase 1: Export Functionality

- [ ] Export to Excel (Maatwebsite/Laravel-Excel)
- [ ] Export to PDF (barryvdh/laravel-dompdf)
- [ ] Export by tahun/divisi/departemen
- [ ] Scheduled exports

### Phase 2: Lock Period

- [ ] Database table untuk lock status
- [ ] Lock confirmation modal
- [ ] Lock history logging
- [ ] Unlock permissions (superadmin only)

### Phase 3: Evaluation Tracking

- [ ] Mark karyawan untuk perlu evaluasi
- [ ] Evaluation status tracking
- [ ] Follow-up notifications
- [ ] Evaluation report generation

### Phase 4: Advanced Analytics

- [ ] Trend analysis (YoY comparison)
- [ ] Predictive analytics
- [ ] Benchmarking across divisi
- [ ] Custom report builder

---

## 🐛 Troubleshooting

### Common Issues

#### Issue: Divisi tidak muncul

**Solution**: Pastikan Pekerjaan model memiliki relasi ke Division

```php
// Di Pekerjaan model
public function division() {
    return $this->belongsTo(Division::class, 'division_id');
}
```

#### Issue: Score tidak dihitung

**Solution**: Pastikan data KPI/KBI assessment ada untuk tahun tersebut

```php
// Debug query
KpiAssessment::where('karyawan_id', $id)->where('tahun', $tahun)->get();
```

#### Issue: Filter tidak bekerja

**Solution**: Check URL parameters dan query builder

```php
dd($request->all()); // Verify parameters received
```

---

## 📞 Support

Untuk questions atau issues:

1. Check PERFORMANCE_REKAP_UPDATES.md untuk user guide
2. Review code comments dalam controller
3. Test dengan tinker untuk debug relasi
4. Check database schema untuk foreign key

---

**Last Updated**: 2026-01-28
**Version**: 2.0
**Status**: Production Ready (Partial - Export/Lock/Evaluation features pending)
