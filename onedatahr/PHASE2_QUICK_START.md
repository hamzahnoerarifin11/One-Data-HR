# 🎯 PHASE 2 IMPLEMENTATION - QUICK START GUIDE

## What's New? 🆕

Lock/Unlock mechanism untuk performance evaluation periods dengan complete audit trail.

---

## 🚀 Quick Deploy Steps

### Step 1: Run Migration

```bash
php artisan migrate
```

**What it does**: Creates `performance_locks` table untuk track lock/unlock history

---

### Step 2: Test Lock Feature

#### As Superadmin:

1. Go to Performance Rekap page
2. Click "🔒 Kunci Nilai" button in header
3. First time: Shows lock modal
4. Enter optional reason
5. Click "Kunci Sekarang"
6. See success message
7. Reload page - button now shows unlock option

#### Unlock (Superadmin only):

1. Click "🔒 Kunci Nilai" button again
2. See unlock modal with lock history
3. See who locked, when, and reason
4. Enter optional unlock reason
5. Click "Buka Kunci"
6. See success message

---

## 📂 Files Structure

### New Files

```
database/
└─ migrations/
   └─ 2026_01_28_000000_create_performance_locks_table.php

app/
└─ Models/
   └─ PerformanceLock.php
```

### Modified Files

```
app/
└─ Http/Controllers/
   └─ PerformanceController.php (added 3 methods)

routes/
└─ web.php (added 3 routes)

resources/
└─ views/pages/performance/
   └─ rekap.blade.php (updated kunciNilai function)
```

---

## 🔑 Key Features

### Lock Period

✅ Lock entire year from editing/evaluation
✅ Only superadmin can lock
✅ Reason documentation (optional)
✅ Timestamp tracking
✅ User attribution (who locked)

### Unlock Period

✅ Superadmin can unlock locked periods
✅ Reason documentation (optional)
✅ Preserve lock history
✅ Timestamp tracking
✅ User attribution (who unlocked)

### History Tracking

✅ See who locked and when
✅ See why they locked (reason)
✅ See who unlocked and when (if exists)
✅ See why they unlocked (reason)

---

## 🔐 Security

- ✅ Superadmin/Admin only (403 error if unauthorized)
- ✅ CSRF token protection
- ✅ Input validation (tahun, reason)
- ✅ Atomic operations (previous lock updated before new lock)
- ✅ Full audit trail

---

## 📊 Database

### New Table: performance_locks

```
Column           | Type      | Notes
─────────────────┼───────────┼──────────────────────
id               | BIGINT    | Primary Key
tahun            | INT       | Unique (one lock per year)
status           | ENUM      | locked or unlocked
locked_by        | BIGINT FK | References users.id
locked_at        | TIMESTAMP | When locked
unlocked_by      | BIGINT FK | References users.id
unlocked_at      | TIMESTAMP | When unlocked
locked_reason    | TEXT      | Why locked
unlock_reason    | TEXT      | Why unlocked
created_at       | TIMESTAMP | Created date
updated_at       | TIMESTAMP | Updated date
```

---

## 🛣️ API Endpoints

### Lock Period

```
POST /performance/rekap/lock
Headers: X-CSRF-TOKEN, Content-Type: application/json

Body:
{
  "tahun": 2024,
  "reason": "Periode evaluasi ditutup"
}

Response (Success):
{
  "success": true,
  "message": "Periode performance tahun 2024 berhasil dikunci.",
  "data": { PerformanceLock record }
}

Response (Unauthorized):
{
  "success": false,
  "message": "Anda tidak memiliki izin untuk mengunci periode."
}
```

### Unlock Period

```
POST /performance/rekap/unlock
Headers: X-CSRF-TOKEN, Content-Type: application/json

Body:
{
  "tahun": 2024,
  "reason": "Ada koreksi data"
}

Response (Success):
{
  "success": true,
  "message": "Periode performance tahun 2024 berhasil dibuka kunci."
}
```

### Get Lock History

```
GET /performance/rekap/lock-history?tahun=2024

Response:
{
  "success": true,
  "data": {
    "tahun": 2024,
    "is_locked": true,
    "locked_by_name": "Admin User",
    "locked_at": "28-01-2026 10:30:45",
    "locked_reason": "Periode evaluasi ditutup",
    "unlocked_by_name": "-",
    "unlocked_at": "-",
    "unlock_reason": "-"
  }
}
```

---

## 🎨 UI Modals

### Lock Modal (Unlocked Period)

```
┌─────────────────────────────────────────┐
│ 🔒 Kunci Periode Performance            │
│                                         │
│ Tahun: 2024                             │
│                                         │
│ Alasan Penguncian (Opsional)            │
│ [Textarea for reason]                   │
│                                         │
│ [🔒 Kunci Sekarang] [Batal]             │
└─────────────────────────────────────────┘
```

### Unlock Modal (Locked Period)

```
┌──────────────────────────────────────────┐
│ 🔐 Periode Terkunci                      │
│                                          │
│ Tahun: 2024                              │
│                                          │
│ ┌──────────────────────────────────────┐ │
│ │ 📋 Riwayat Penguncian                │ │
│ │                                      │ │
│ │ Dikunci oleh: Admin User             │ │
│ │ Tanggal Kunci: 28-01-2026 10:30:45   │ │
│ │ Alasan: Periode evaluasi ditutup     │ │
│ └──────────────────────────────────────┘ │
│                                          │
│ Alasan Pembukaan Kunci (Opsional)        │
│ [Textarea for reason]                    │
│                                          │
│ [🔓 Buka Kunci] [Tutup]                  │
│                                          │
│ ⚠️ Hanya Superadmin yang dapat           │
│    membuka kunci periode                 │
└──────────────────────────────────────────┘
```

---

## ⚡ Performance Impact

- ✅ Minimal database queries (one unique lock per year)
- ✅ Indexed on tahun, status, locked_at for fast queries
- ✅ No impact on existing export/filter functionality
- ✅ Modal-based UI (lightweight, no page bloat)

---

## 🧪 Test Cases

### Test 1: Basic Lock

1. Click "Kunci Nilai" → Lock modal appears
2. Enter reason "Testing"
3. Click "Kunci Sekarang" → Success message
4. Check database: New record with status='locked'

### Test 2: Authorization

1. Logout → Login as Manager
2. Click "Kunci Nilai" → No response (authorization fails)
3. Check console: 403 Forbidden error

### Test 3: Lock History

1. Lock year 2024
2. Click "Kunci Nilai" again → Unlock modal with history
3. Verify all fields shown correctly

### Test 4: Atomic Operations

1. Lock year 2024
2. Change reason, lock again
3. Check database: Only one record with status='locked'
4. Verify previous lock marked as unlocked

---

## 📋 Checklist Before Go-Live

- [ ] Run `php artisan migrate`
- [ ] Test lock as superadmin
- [ ] Test lock as manager (should fail)
- [ ] Verify database records created
- [ ] Test unlock as superadmin
- [ ] Verify history displays correctly
- [ ] Test dark mode styling
- [ ] Test modal responsive on mobile
- [ ] Verify CSRF token working
- [ ] Check error messages display

---

## 🔄 Rollback Instructions

If something goes wrong:

```bash
# Revert migration
php artisan migrate:rollback

# Delete files
rm app/Models/PerformanceLock.php

# Revert modified files from git
git checkout app/Http/Controllers/PerformanceController.php
git checkout routes/web.php
git checkout resources/views/pages/performance/rekap.blade.php
```

---

## 📞 Support

### Common Issues

**Q: Lock button not showing**
A: Make sure you're logged in as superadmin/admin

**Q: Modal not appearing**
A: Check browser console for JavaScript errors

**Q: Lock not saving**
A: Verify CSRF token in page meta tag:

```html
<meta name="csrf-token" content="..." />
```

**Q: Authorization error**
A: Ensure user has superadmin or admin role

---

## 🎓 What Happens Next? (Phase 3)

- Mark employees for evaluation follow-up
- Track evaluation status
- Generate evaluation reports

---

**Status**: ✅ READY TO DEPLOY
**Date**: January 28, 2026
**Version**: v2.1
