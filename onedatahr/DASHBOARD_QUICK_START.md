# 🚀 QUICK START GUIDE - Dashboard Rekrutmen

## ⚡ 5 Menit Setup

### 1. Clear Cache (Opsional tapi disarankan)
```bash
php artisan cache:clear
php artisan view:clear
```

### 2. Akses Dashboard
```
Navigate to: http://localhost/rekrutmen/dashboard
atau klik menu: Rekrutmen → Dashboard
```

### 3. Mulai Gunakan
✅ Done! Dashboard ready to use

---

## 📋 Yang Baru Kamu Lihat

### KPI Cards (Atas)
4 kartu dengan warna berbeda menunjukkan:
- 👥 **Total Pelamar** (biru)
- ✅ **Lolos CV** (hijau)
- 🧠 **Lolos Psikotes** (ungu)
- 🏆 **Hired** (oranye)

### Funnel Visualization (Tengah)
Progress dari 100% → 7 tahap recruitment dengan:
- 📊 Progress bar setiap tahap
- 📈 Percentage dari total
- 🎨 Warna berbeda per tahap

### Conversion Rates (Kanan)
Lihat efficiency setiap transisi:
- CV → Psikotes: X%
- Psikotes → Kompetensi: X%
- dst...

### Additional Info
- ⚠️ Rejected candidates count
- 🎯 Overall success rate
- 📍 Breakdown per position (jika belum filter)
- 📅 Monthly trends

---

## 🎛️ Cara Filter

### Filter Tahun
```
1. Klik dropdown "Tahun"
2. Pilih tahun yang mau dilihat
3. Dashboard auto-update
```

### Filter Posisi
```
1. Klik dropdown "Posisi"
2. Pilih posisi spesifik
3. Dashboard auto-update (hanya posisi itu)
```

### Reset Filter
```
1. Pilih "-- Semua Posisi --"
2. Sekarang lihat semua posisi
```

---

## 📊 Baca Dashboard

### Scenario 1: Cek Overall Recruitment Health
```
1. Lihat KPI cards atas
2. Total pelamar: 300
3. Hired: 60
4. Success rate: 20%
✓ Interpretation: 1 dari 5 pelamar diterima
```

### Scenario 2: Cari Bottleneck
```
1. Lihat Funnel Progress
2. Total: 300 → CV: 240 (80%) ✅ OK
3. CV: 240 → Psikotes: 180 (75%) ✅ OK
4. Psikotes: 180 → Kompetensi: 90 (50%) ⚠️ DROP!
✓ Issue: Tahap kompetensi terlalu ketat
```

### Scenario 3: Compare Positions
```
1. Lihat "Statistik per Posisi"
2. Developer: 15 CV, 12 Psikotes, 10 Kompetensi
3. Designer: 8 CV, 5 Psikotes, 2 Kompetensi
✓ Interpretation: Designer lebih competitive
```

### Scenario 4: Trend Hiring
```
1. Scroll ke "Distribusi Pelamar per Bulan"
2. Lihat bar chart
3. Januari: 10 | Februari: 25 | Maret: 15
✓ Interpretation: Peak hiring di bulan 2
```

---

## 🎯 Common Use Cases

### 👔 HR Manager
**Goal**: Monitor recruitment performance

**Steps**:
1. Buka dashboard setiap pagi
2. Lihat KPI cards untuk overview
3. Cek funnel untuk attrition
4. Identify bottlenecks
5. Plan improvement actions

---

### 👨‍💼 Direktur
**Goal**: Lihat ROI recruitment process

**Steps**:
1. Fokus ke KPI cards
2. Lihat "Effective Rate" (hired %)
3. Check monthly trends
4. Monitor per-position performance
5. Make strategic hiring decisions

---

### 📊 Data Analyst
**Goal**: Deep dive data analysis

**Steps**:
1. Filter specific position
2. Analyze conversion rates
3. Identify improvement opportunities
4. Export data (soon)
5. Create reports

---

## 🔍 Interpretasi Data

### Green Flags ✅
- Conversion rate tinggi (> 70%)
- Effective rate > 15%
- Consistent monthly applications
- Balanced position distribution

### Red Flags ⚠️
- Low conversion rates (< 30%)
- High drop-off at certain stage
- No applications in months
- Single position dominating

### Orange Flags 🟠
- Moderate conversion (30-70%)
- Seasonal hiring patterns
- Bottlenecks in middle stages
- Unbalanced position demand

---

## 📱 Mobile Usage

Dashboard fully responsive pada:
- ✅ Smartphone (< 480px)
- ✅ Tablet (480px - 768px)
- ✅ Laptop (> 768px)

**Tips**:
- Scroll horizontally untuk monthly chart
- Tap cards untuk details (future)
- Use landscape mode untuk lebih baik view

---

## 🌙 Dark Mode

Dashboard automatic switch based on:
- System preference
- Manual toggle (future)

**Works perfectly in**:
- ✅ Browser dark mode
- ✅ Phone dark mode
- ✅ Late night viewing

---

## 🔄 Refresh Data

Data auto-update ketika:
- Filter diubah
- Page di-refresh
- New database entries added

**Note**: Dashboard shows data "as-is" dari database, not real-time calculation

---

## ⚙️ Settings & Preferences

### Current Available
- 📅 Year filter
- 💼 Position filter
- 🌙 Dark mode (browser settings)

### Coming Soon (v2.1)
- 📊 Export to Excel/PDF
- 📈 Custom date ranges
- 🔔 Email alerts
- 📍 Advanced position filters
- 👁️ View history

---

## 🐛 Quick Fixes

### Dashboard blank?
```
→ Refresh page (Ctrl + F5)
→ Check database connection
→ Check if data exists for selected year
```

### Filter not working?
```
→ Check dropdown has options
→ Try refresh
→ Check browser console
```

### Styling weird?
```
→ Clear browser cache
→ Try different browser
→ Disable browser extensions
```

### Numbers don't match expected?
```
→ Check tanggal_melamar filled for all candidates
→ Check tgl_lolos_* dates correct
→ Verify pemberkasan.selesai_recruitment filled
```

---

## 📞 Need Help?

1. **Check Documentation**
   - `DASHBOARD_REKRUTMEN_CHANGES.md` - Technical details
   - `DASHBOARD_VERIFICATION_CHECKLIST.md` - Verification
   - This file - Quick reference

2. **Check Database**
   - Verify data exists
   - Check date formats
   - Validate relationships

3. **Check Browser Console**
   - Open DevTools (F12)
   - Check Console tab
   - Report any errors

4. **Ask Development Team**
   - Share screenshot
   - Provide context
   - Include browser info

---

## 🎓 Understanding the Data

### What Each Section Means

**KPI Cards**
- Direct metrics showing key numbers
- Use for quick daily check-ins

**Funnel Progress**
- Shows journey from application → hire
- Identify where candidates drop off
- Each step is independent count

**Conversion Rates**
- Percentage moving from one stage to next
- Higher = better
- Shows efficiency of each stage

**Statistics by Position**
- Breakdown of how each position performing
- Compare competition between positions
- Identify positions with high/low quality

**Monthly Distribution**
- Trend of applications over time
- Identify peak hiring months
- Plan resources accordingly

---

## 💡 Pro Tips

### Tip 1: Daily Check
```
Spend 5 minutes every morning:
1. Glance at KPI cards
2. Check for critical drops in funnel
3. Review monthly trend
4. Plan your day accordingly
```

### Tip 2: Weekly Analysis
```
Every Friday, deep dive:
1. Filter each position separately
2. Analyze conversion rates
3. Identify bottlenecks
4. Plan improvements
```

### Tip 3: Monthly Review
```
End of month review:
1. Compare with previous month
2. Year-over-year comparison
3. Executive summary
4. Strategic adjustments
```

### Tip 4: Benchmark
```
Keep track of benchmarks:
- Average conversion rate
- Average hired percentage
- Best performing position
- Peak hiring month
```

---

## 🎯 Next Steps

1. ✅ Explore the dashboard
2. ✅ Play with filters
3. ✅ Understand your data
4. ✅ Create benchmarks
5. ✅ Setup weekly reviews
6. ✅ Track improvements
7. ✅ Optimize hiring process

---

## 📊 Example Dashboard Reading

### Situation
- Year: 2024
- Position: All
- Total applications: 250

### Reading
```
┌─ TOTAL APPLICATIONS: 250
├─ LOLOS CV: 200 (80%)
│  ✅ Good - most pass initial screening
├─ LOLOS PSIKOTES: 150 (75% of CV)
│  ✅ Good - psychological test effective
├─ LOLOS KOMPETENSI: 90 (60% of Psikotes)
│  ⚠️ Drop here - competency test strict
├─ LOLOS HR: 70 (77% of Kompetensi)
│  ✅ OK - HR interview selective
├─ LOLOS USER: 50 (71% of HR)
│  ✅ OK - final approval reasonable
└─ HIRED: 40 (80% of User)
   ✅ Good - most approved get hired

CONCLUSION:
- Overall effective rate: 16% (40/250)
- Bottleneck: Kompetensi stage (big drop)
- Recommendation: Review competency criteria
```

---

**Happy Analyzing! 🚀**

Last Updated: January 28, 2026
