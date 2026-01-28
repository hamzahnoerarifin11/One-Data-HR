# 🎨 Dashboard Rekrutmen - Visual Guide

## Layout Structure

```
╔══════════════════════════════════════════════════════════════╗
║                    DASHBOARD REKRUTMEN 📊                    ║
║        Overview statistik funnel rekrutmen tahun 2024        ║
║                      Update: 28 Jan 2024                     ║
╚══════════════════════════════════════════════════════════════╝

┌──────────────────────────────────────────────────────────────┐
│                     FILTER SECTION                            │
├──────────────────────────┬──────────────────────────────────┤
│ 📅 Tahun: [▼ 2024      ] │ 💼 Posisi: [▼ -- Semua Posisi --]│
└──────────────────────────┴──────────────────────────────────┘

┌─────────────┬─────────────┬─────────────┬─────────────┐
│             │             │             │             │
│  👥 TOTAL   │  ✅ LOLOS   │  🧠 LOLOS   │  🏆 HIRED   │
│  PELAMAR    │  CV         │  PSIKOTES   │             │
│             │             │             │             │
│    300      │    240      │    180      │     60      │
│  (Orang)    │  (80%)      │  (75%)      │  (20%)      │
│             │             │             │             │
└─────────────┴─────────────┴─────────────┴─────────────┘

┌────────────────────────────────────┬────────────────────────┐
│                                    │                        │
│    FUNNEL PROGRESS                 │  CONVERSION RATE       │
│    ═══════════════════════          │  ════════════════     │
│                                    │                        │
│ 1️⃣  Total Pelamar      300        │  CV → Psikotes        │
│     ████████████████  100%         │  ████████░░░░  80%    │
│                                    │                        │
│ 2️⃣  Lolos CV           240        │  Psikotes → Kom       │
│     ████████████░░░░░  80%         │  ████████░░░░  75%    │
│                                    │                        │
│ 3️⃣  Lolos Psikotes     180        │  HR → User            │
│     ████████░░░░░░░░░  60%         │  ████████░░░░  71%    │
│                                    │                        │
│ 4️⃣  Lolos Kompetensi   135        │  User → Hired         │
│     ██████░░░░░░░░░░░  45%         │  ████████░░░░  80%    │
│                                    │                        │
│ 5️⃣  Lolos Interview HR 105        │ ───────────────────   │
│     █████░░░░░░░░░░░░  35%         │                        │
│                                    │  ⚠️  DITOLAK           │
│ 6️⃣  Lolos User         84         │  ═════════════        │
│     ████░░░░░░░░░░░░░░  28%        │                        │
│                                    │      60 (20%)          │
│ 7️⃣  Hired (Selesai)    60         │                        │
│     ███░░░░░░░░░░░░░░░░  20%       │ 🎯 EFFECTIVE RATE      │
│                                    │ ═══════════════════   │
│                                    │                        │
│                                    │       20%              │
│                                    │  60 dari 300 diterima  │
│                                    │                        │
└────────────────────────────────────┴────────────────────────┘

┌────────────────────────────────────────────────────────────┐
│          STATISTIK PER POSISI (Tahun 2024)                 │
├────────────────────────────────────────────────────────────┤
│                                                             │
│  SOFTWARE ENGINEER          PRODUCT MANAGER    DESIGNER    │
│  ═════════════════          ════════════════   ═════════   │
│  📊 100 pelamar            📊 80 pelamar      📊 20 pel   │
│  ✅ 85 CV                  ✅ 70 CV           ✅ 15 CV   │
│  🧠 65 Psikotes            🧠 55 Psikotes     🧠 10 Psi   │
│  ◆ 50 Kompetensi           ◆ 40 Kompetensi    ◆ 8 Komp   │
│                                                             │
└────────────────────────────────────────────────────────────┘

┌────────────────────────────────────────────────────────────┐
│     DISTRIBUSI PELAMAR PER BULAN (Tahun 2024)              │
├────────────────────────────────────────────────────────────┤
│                                                             │
│  30│         ██                                             │
│  25│    ██   ██    ██                   ██                 │
│  20│    ██   ██    ██   ██              ██                 │
│  15│    ██   ██    ██   ██   ██         ██   ██            │
│  10│    ██   ██    ██   ██   ██   ██    ██   ██   ██      │
│   5│    ██   ██    ██   ██   ██   ██    ██   ██   ██   ██ │
│    │────────────────────────────────────────────────────   │
│    └──────────────────────────────────────────────────────┘
│  Jan Feb Mar Apr May Jun Jul Aug Sep Oct Nov Dec
│
│  📊 Total: 300 | Rata-rata: 25/bulan | Peak: Feb (30)
│
└────────────────────────────────────────────────────────────┘
```

---

## Color Code Legend

```
🟦 BIRU (#3b82f6)       - Primary, Total Pelamar, CV Progress
🟩 HIJAU (#10b981)      - Success, Lolos CV, Positive indicators
🟪 UNGU (#a855f7)       - Accent, Psikotes, Emphasis
🟦 CYAN (#06b6d4)       - Info, Kompetensi, Additional data
🟧 ORANYE (#f59e0b)     - Warning, Hired, Call to action
🟥 MERAH (#ef4444)      - Danger, Rejected, Negative
```

---

## Interactive Elements

### KPI Cards (Hover Effect)
```
┌──────────────────┐
│  Total Pelamar   │  → Mouse hover:
│      300         │    - Card moves up slightly
│    (Orang)       │    - Shadow increases
└──────────────────┘    - Icon scales up

Effect: Smooth 300ms transition
```

### Filter Dropdowns
```
Select: [▼ 2024 ──────────────]
        ├─ Tahun 2023
        ├─ Tahun 2024 ✓ selected
        └─ Tahun 2025

Auto-submits form on selection
```

### Progress Bars
```
████████░░░░░░░░  75%
└─ Animated fill effect
└─ Smooth 500ms animation
└─ Gradient color
```

---

## Responsive Design

### Desktop (> 1024px)
```
┌─────────────────────────────────────────────────────────┐
│ HEADER (Full width)                                     │
├─────────────────────────────────────────────────────────┤
│ FILTERS (2 columns)                                     │
├──────────────┬──────────────┬──────────────┬────────────┤
│ KPI Card 1   │ KPI Card 2   │ KPI Card 3   │ KPI Card 4 │
└──────────────┴──────────────┴──────────────┴────────────┘
├─────────────────────────────────┬───────────────────────┤
│ Funnel Progress (2/3)           │ Summary Cards (1/3)   │
├─────────────────────────────────┼───────────────────────┤
│ Statistics (Full width)         │                       │
├─────────────────────────────────┼───────────────────────┤
│ Monthly Chart (Full width)      │                       │
└─────────────────────────────────┴───────────────────────┘
```

### Tablet (768px - 1024px)
```
┌─────────────────────────────────┐
│ HEADER                          │
├─────────────────────────────────┤
│ FILTERS (Full width)            │
├─────────────────┬───────────────┤
│ KPI Card 1      │ KPI Card 2    │
├─────────────────┼───────────────┤
│ KPI Card 3      │ KPI Card 4    │
├─────────────────┴───────────────┤
│ Funnel Progress                 │
├─────────────────────────────────┤
│ Summary Cards                   │
├─────────────────────────────────┤
│ Statistics                      │
├─────────────────────────────────┤
│ Monthly Chart                   │
└─────────────────────────────────┘
```

### Mobile (< 768px)
```
┌─────────────────┐
│     HEADER      │
├─────────────────┤
│   FILTERS       │
├─────────────────┤
│  KPI Card 1     │
├─────────────────┤
│  KPI Card 2     │
├─────────────────┤
│  KPI Card 3     │
├─────────────────┤
│  KPI Card 4     │
├─────────────────┤
│  Funnel Prog    │
├─────────────────┤
│  Summary Card   │
├─────────────────┤
│  Statistics     │
├─────────────────┤
│ Monthly Chart   │
│ (scrollable)    │
└─────────────────┘
```

---

## Dark Mode Comparison

### Light Mode
```
┌──────────────────────────────┐
│  WHITE BACKGROUND            │  ← Bright white
│  Dark text on light bg       │  ← Black/dark gray text
│  Blue/green accents          │  ← Vibrant colors
│  Light borders               │  ← Light gray borders
│  Soft shadows                │  ← Subtle shadows
└──────────────────────────────┘
```

### Dark Mode
```
┌──────────────────────────────┐
│  DARK BACKGROUND             │  ← Dark gray (#1f2937)
│  Light text on dark bg       │  ← White/light gray text
│  Blue/green accents          │  ← Slightly muted colors
│  Dark borders                │  ← Dark gray borders
│  No harsh shadows            │  ← Subtle, integrated
└──────────────────────────────┘
```

---

## Animation Timeline

### Page Load Animation
```
0ms:    Opacity: 0%, Transform: translateY(10px)
        └─ Elements start hidden and below
        
500ms:  Opacity: 100%, Transform: translateY(0px)
        └─ Elements slide up and become visible
        
Effect: slideInUp animation
Timing: ease-out, smooth entrance
```

### Card Hover Animation
```
0ms:    Transform: none
        Box-shadow: subtle
        
300ms:  Transform: translateY(-4px)
        Box-shadow: larger
        
Effect: Smooth elevation on hover
Duration: 300ms cubic-bezier
```

### Progress Bar Fill
```
0ms:    Width: 0%
        
1000ms: Width: var(--progress-width)
        
Effect: progressFill animation
Timing: ease-out, draws attention
```

---

## Typography Hierarchy

```
┌─────────────────────────────────┐
│  📊 Dashboard Rekrutmen         │  ← H1 (text-3xl, bold)
│  Overview statistik funnel...   │  ← Subtitle (text-sm, gray)
├─────────────────────────────────┤
│  Total Pelamar                  │  ← Label (text-sm, medium)
│  300                            │  ← Value (text-3xl, bold)
├─────────────────────────────────┤
│  Funnel Progress                │  ← H3 (text-lg, bold)
│  1. Total Pelamar    300        │  ← Item (text-base, medium)
│  ████████░░░░░░░░  100%         │  ← Stats (text-xs, gray)
└─────────────────────────────────┘
```

---

## Icon Reference

```
👥 Users/Pelamar           fa-users
✅ Check/Success           fa-check-circle
📋 Document/CV             fa-file-check
🧠 Psychological Test      fa-brain
🎯 Target/Goal             fa-target
📊 Chart/Analytics         fa-chart-line
📈 Trend/Up                fa-chart-line
📅 Calendar/Date           fa-calendar-alt
💼 Briefcase/Position      fa-briefcase
🏆 Trophy/Achievement      fa-trophy
⭐ Star/Quality            fa-star
🤝 Handshake/Interview     fa-handshake
📊 Filter/Funnel           fa-filter
❌ Times/Rejected          fa-times-circle
```

---

## Color Palette

```
┌─────────────────────────────────────────────────────┐
│ PRIMARY COLORS                                      │
├─────────────────────────────────────────────────────┤
│ 🟦 Blue (#3b82f6)       Main, Primary Actions      │
│ 🟪 Purple (#a855f7)     Accent, Secondary          │
│ 🟥 Red (#ef4444)        Danger, Rejection          │
├─────────────────────────────────────────────────────┤
│ SEMANTIC COLORS                                     │
├─────────────────────────────────────────────────────┤
│ 🟩 Green (#10b981)      Success, Positive          │
│ 🟧 Orange (#f59e0b)     Warning, Hired             │
│ 🟦 Cyan (#06b6d4)       Info, Additional Data      │
│ 🟨 Yellow (#eab308)     Caution, Pending           │
├─────────────────────────────────────────────────────┤
│ NEUTRAL COLORS                                      │
├─────────────────────────────────────────────────────┤
│ ⚫ Dark (#1f2937)        Dark mode background       │
│ ⚪ Light (#ffffff)       Light mode background      │
│ 🟫 Gray (#64748b)       Text, Secondary            │
│ 🟩 Light Gray (#e2e8f0) Borders, Dividers          │
└─────────────────────────────────────────────────────┘
```

---

## Visual Example - Full Dashboard View

```
╔════════════════════════════════════════════════════════════════════╗
║                     📊 DASHBOARD REKRUTMEN                        ║
║              Overview statistik funnel rekrutmen tahun 2024        ║
║                        Update: 28 Jan 2024 14:35                  ║
╚════════════════════════════════════════════════════════════════════╝

┌─────────────────────────────────────────────────────────────────────┐
│ 📅 Tahun: [▼ 2024]              💼 Posisi: [▼ -- Semua Posisi --] │
└─────────────────────────────────────────────────────────────────────┘

┌──────────────┐ ┌──────────────┐ ┌──────────────┐ ┌──────────────┐
│              │ │              │ │              │ │              │
│ 👥 TOTAL     │ │ ✅ LOLOS CV  │ │ 🧠 PSIKOTES │ │ 🏆 HIRED     │
│ PELAMAR      │ │              │ │              │ │              │
│              │ │ 240          │ │ 180          │ │ 60           │
│ 300          │ │ 80%          │ │ 75%          │ │ 20%          │
│ orang        │ │              │ │              │ │              │
│              │ │              │ │              │ │              │
└──────────────┘ └──────────────┘ └──────────────┘ └──────────────┘

┌─────────────────────────────────────────┬──────────────────────────┐
│ FUNNEL PROGRESS                         │ CONVERSION RATE          │
│ ═════════════════════════════════════   │ ════════════════════     │
│                                         │                          │
│ 1️⃣  Total Pelamar        (300)         │ CV → Psikotes    80%    │
│    ██████████████████████ 100%          │ ██████████░░░░░░░      │
│                                         │                          │
│ 2️⃣  Lolos CV             (240)         │ Psikotes → Kom   75%    │
│    ██████████████████░░░░  80%          │ ██████████░░░░░░░      │
│                                         │                          │
│ 3️⃣  Lolos Psikotes       (180)         │ HR → User        71%    │
│    ██████████░░░░░░░░░░░░  60%          │ ██████████░░░░░░░      │
│                                         │                          │
│ 4️⃣  Lolos Kompetensi     (135)         │ User → Hired     80%    │
│    ███████░░░░░░░░░░░░░░░  45%          │ ██████████░░░░░░░      │
│                                         │                          │
│ 5️⃣  Lolos Interview HR   (105)         │ ─────────────────────── │
│    █████░░░░░░░░░░░░░░░░░  35%          │                          │
│                                         │ ⚠️ DITOLAK: 60 (20%)    │
│ 6️⃣  Lolos User           (84)          │                          │
│    ████░░░░░░░░░░░░░░░░░░  28%          │ 🎯 SUCCESS RATE: 20%    │
│                                         │                          │
│ 7️⃣  Hired (Selesai)      (60)          │ 60 dari 300 diterima   │
│    ███░░░░░░░░░░░░░░░░░░░  20%          │                          │
│                                         │                          │
└─────────────────────────────────────────┴──────────────────────────┘

┌────────────────────────────────────────────────────────────────────┐
│                STATISTIK PER POSISI (2024)                         │
├────────────────────────────────────────────────────────────────────┤
│                                                                    │
│ 🟩 SOFTWARE ENGINEER      🟩 PRODUCT MANAGER    🟩 DESIGNER      │
│    100 pelamar              80 pelamar            20 pelamar      │
│    ✅ 85 CV (85%)          ✅ 70 CV (87%)        ✅ 15 CV (75%)  │
│    🧠 65 Psikotes (76%)    🧠 55 Psikotes (78%)  🧠 10 Psi (66%)│
│    ◆ 50 Kompetensi (76%)   ◆ 40 Kompetensi (72%) ◆ 8 Komp (80%)│
│                                                                    │
└────────────────────────────────────────────────────────────────────┘

┌────────────────────────────────────────────────────────────────────┐
│                DISTRIBUSI PELAMAR PER BULAN (2024)                 │
├────────────────────────────────────────────────────────────────────┤
│                                                                    │
│ 30 │         ██                                                    │
│ 25 │    ██   ██    ██                   ██                        │
│ 20 │    ██   ██    ██   ██              ██                        │
│ 15 │    ██   ██    ██   ██   ██         ██   ██                  │
│ 10 │    ██   ██    ██   ██   ██   ██    ██   ██   ██             │
│  5 │    ██   ██    ██   ██   ██   ██    ██   ██   ██   ██        │
│    │────────────────────────────────────────────────────────────  │
│    └──────────────────────────────────────────────────────────    │
│   Jan  Feb  Mar  Apr  May  Jun  Jul  Aug  Sep  Oct  Nov  Dec     │
│                                                                    │
│ 📊 Total: 300  |  Rata-rata: 25/bulan  |  Peak: Feb (30)        │
│                                                                    │
└────────────────────────────────────────────────────────────────────┘
```

---

**Visual Guide Version:** 2.0
**Last Updated:** January 28, 2026
