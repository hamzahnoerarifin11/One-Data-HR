# 🎨 VISUAL SUMMARY: Performance Rekap v2.0

## 📱 Page Layout Overview

```
┌─────────────────────────────────────────────────────────────────┐
│ HEADER & TITLE                                                   │
│ Rekapitulasi Kinerja - Tahun 2025                              │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│ EXECUTIVE SUMMARY (5 CARDS - GRID LAYOUT)                       │
│ ┌─────────────┐ ┌──────────────┐ ┌────────────┐ ┌────────────┐ │
│ │ Avg Score   │ │ Total Dinilai│ │ Grade A    │ │ Grade B+C  │ │
│ │   78.50     │ │    142       │ │    34      │ │    89      │ │
│ └─────────────┘ └──────────────┘ └────────────┘ └────────────┘ │
│ ┌──────────────────────────┐                                     │
│ │ % Bawah Standar          │                                     │
│ │ 6.34% (9 karyawan)       │                                     │
│ └──────────────────────────┘                                     │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│ ANOMALY ALERTS (IF APPLICABLE)                                  │
│ ┌─────────────────────────────────────────────────────────────┐ │
│ │ ⚠️  Grade D Terbanyak: IT Division (7 employees)            │ │
│ └─────────────────────────────────────────────────────────────┘ │
│ ┌─────────────────────────────────────────────────────────────┐ │
│ │ 📈 Performa Terbaik: Finance Division (avg: 82.5)           │ │
│ └─────────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│ FILTER FORM (6 FILTERS + BUTTONS)                              │
│ ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐            │
│ │  Search  │ │  Tahun   │ │Perusahaan│ │  Divisi  │            │
│ └──────────┘ └──────────┘ └──────────┘ └──────────┘            │
│ ┌──────────┐ ┌──────────┐                                        │
│ │Departemen│ │  Grade   │  [Cari] [Reset]                      │
│ └──────────┘ └──────────┘                                        │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│ GROUPED TABLES BY DIVISI                                        │
│ ┌─ IT DIVISION (34 karyawan, avg: 75.2) ──────────────────────┐ │
│ │ Grade Distribution: A:5 | B:12 | C:10 | D:7                │ │
│ ├──────────────────────────────────────────────────────────────┤ │
│ │ │ No │ Nama       │ NIK    │ KPI  │ KBI  │ Score │ Grade    │ │
│ │ ├────┼────────────┼────────┼──────┼──────┼───────┼──────────┤ │
│ │ │ 1  │ John Doe   │ 123456 │ 75.0 │ 72.0 │ 74.1  │ C        │ │
│ │ │ 2  │ Jane Smith │ 234567 │ 85.0 │ 80.0 │ 83.5  │ B        │ │
│ │ │ 3  │ Bob Wilson │ 345678 │ 60.0 │ 65.0 │ 61.5  │ D   [⚠️] │ │
│ │ └────┴────────────┴────────┴──────┴──────┴───────┴──────────┘ │
│ └──────────────────────────────────────────────────────────────┘ │
│                                                                   │
│ ┌─ FINANCE DIVISION (28 karyawan, avg: 82.5) ─────────────────┐ │
│ │ Grade Distribution: A:12 | B:11 | C:5 | D:0                │ │
│ ├──────────────────────────────────────────────────────────────┤ │
│ │ [Detailed employee table...]                                 │ │
│ └──────────────────────────────────────────────────────────────┘ │
│                                                                   │
│ ┌─ HR DIVISION (22 karyawan, avg: 78.5) ────────────────────────┐│
│ │ Grade Distribution: A:8 | B:10 | C:4 | D:0                  │ │
│ ├──────────────────────────────────────────────────────────────┤ │
│ │ [Detailed employee table...]                                 │ │
│ └──────────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│ ACTION BUTTONS (4 BUTTONS)                                      │
│ [📥 Export] [🔒 Lock] [🖨️ Print] [⚠️ Tandai Perlu Evaluasi] │
└─────────────────────────────────────────────────────────────────┘
```

---

## 🎨 Color Scheme

### Cards

```
Executive Summary Cards:
- Blue: Average Score
- Green: Total Counted
- Emerald: Grade A
- Yellow: Grade B+C
- Red: Below Standard

Badges/Badges:
- Grade A: Emerald (bg-emerald-100 text-emerald-700)
- Grade B: Blue (bg-blue-100 text-blue-700)
- Grade C: Yellow (bg-yellow-100 text-yellow-700)
- Grade D: Red (bg-red-100 text-red-700)

Alerts:
- Warning: Orange (bg-orange-50)
- Success: Green (bg-green-50)
```

### Dark Mode

```
All components automatically support dark mode:
- dark:border-gray-700
- dark:bg-gray-800
- dark:text-white
- dark:text-gray-400
```

---

## 📊 Component Breakdown

### 1. Executive Summary Cards

```
┌──────────────────────────────────┐
│      Avg. Final Score            │
│                                  │
│  📈 78.50                         │
│                                  │
│  (Real-time calculated)          │
└──────────────────────────────────┘

Responsive Breakpoints:
- Mobile (xs): 1 column
- Small (sm): 2 columns
- Medium (md): 2 columns
- Large (lg): 5 columns
- XL (xl): 5 columns
```

### 2. Anomaly Alerts

```
┌─────────────────────────────────────┐
│ ⚠️  Perhatian: Grade D Terbanyak     │
│                                     │
│ Divisi IT memiliki 7 karyawan      │
│ dengan Grade D                      │
└─────────────────────────────────────┘
```

### 3. Filter Form

```
Grid Layout: 6 columns
┌─────────┬─────────┬─────────┬─────────┬─────────┬──────────┐
│ Search  │ Tahun   │Perusahaan│ Divisi  │Departemen│ Grade  │
└─────────┴─────────┴─────────┴─────────┴─────────┴──────────┘
          [Cari & Filter]  [Reset]
```

### 4. Grouped Table Header

```
┌─────────────────────────────────────────────────────┐
│  🔵 DIVISI NAME (34 karyawan)      Avg Score: 75.2 │
├─────────────────────────────────────────────────────┤
│  Grade A: 5  │  Grade B: 12  │  Grade C: 10  │ D: 7 │
├─────────────────────────────────────────────────────┤
│ [Employee table with 6 columns...]                  │
└─────────────────────────────────────────────────────┘
```

### 5. Action Buttons

```
┌─────────────────────────────────────────────────────┐
│ [📥 Export Laporan]  [🔒 Kunci Nilai]  [🖨️ Print] │
└─────────────────────────────────────────────────────┘

Button Styling:
- Export: Green (bg-green-600 hover:bg-green-700)
- Lock: Purple (bg-purple-600 hover:bg-purple-700)
- Print: Indigo (bg-indigo-600 hover:bg-indigo-700)
- Mark: Orange (text-orange-600, small icon button)
```

---

## 📱 Responsive Design

### Mobile (< 768px)

```
┌────────────────────┐
│ Card 1 (full width)│
├────────────────────┤
│ Card 2 (full width)│
├────────────────────┤
│ Card 3 (full width)│
├────────────────────┤
│ Card 4 (full width)│
├────────────────────┤
│ Card 5 (full width)│
└────────────────────┘

Filters: Stacked vertically
Buttons: Full width or 2x2 grid
```

### Tablet (768px - 1024px)

```
┌──────────────┬──────────────┐
│   Card 1     │   Card 2     │
├──────────────┼──────────────┤
│   Card 3     │   Card 4     │
├──────────────┐              │
│   Card 5     │              │
└──────────────┴──────────────┘

Filters: 2 rows of 3 filters
Buttons: 2x2 grid
```

### Desktop (1024px+)

```
┌────────┬───────────┬────────┬─────────┬────────────┐
│ Card 1 │ Card 2    │ Card 3 │ Card 4  │ Card 5     │
└────────┴───────────┴────────┴─────────┴────────────┘

Filters: 1 row of 6 filters
Buttons: 1 row
```

---

## 🌙 Dark Mode Example

### Light Mode

```
Background: White
Text: Dark Gray
Cards: Light Blue, Light Green, Light Yellow
Borders: Light Gray
```

### Dark Mode

```
Background: Dark Gray (gray-900)
Text: White/Light Gray
Cards: Dark Blue (blue-900/30), Dark Green (green-900/30)
Borders: Dark Gray (gray-700)
```

---

## 🔄 User Flow

```
1. USER VISITS PAGE
   ↓
2. PAGE LOADS WITH DEFAULT DATA (Current Year, All Employees)
   ↓
3. EXECUTIVE SUMMARY CARDS CALCULATED
   ↓
4. ANOMALY DETECTION RUN
   ↓
5. DATA GROUPED BY DIVISI
   ↓
6. PAGE RENDERED WITH TAILWIND STYLING
   ↓
7. USER CAN:
   ├─ Read Summary Cards
   ├─ View Anomaly Alerts
   ├─ Apply Filters
   ├─ View Grouped Tables
   ├─ Paginate Results
   ├─ Export/Print
   └─ Mark Employees for Evaluation
```

---

## 🎯 Interaction Patterns

### Filter Interaction

```
User selects filter → Form submits → Page reloads with filtered data
                                   → URL params updated
                                   → Summary recalculated
                                   → Tables regrouped
                                   → Results displayed
```

### Action Interactions

```
User clicks button → Confirmation dialog appears (if critical)
                  → User confirms → Action function called
                  → Success message shown
```

### Table Interaction

```
User sees grouped table → Can view per-divisi statistics
                       → Can click employee actions
                       → Can paginate if > 10 records
```

---

## 📐 Grid System

### Executive Summary Grid

```
lg:grid-cols-5    (5 columns on large screens)
md:grid-cols-2    (2 columns on medium screens)
grid-cols-1       (1 column on small screens)
gap-4             (16px gap between cards)
```

### Filter Form Grid

```
grid-cols-1       (1 column on small screens)
md:grid-cols-2    (2 columns on medium)
lg:grid-cols-6    (6 columns on large screens)
gap-4             (16px gap between inputs)
```

### Table Grid

```
Overflow: auto    (Horizontal scroll on mobile)
w-full            (Full width responsive)
responsive        (Columns adjust to fit)
```

---

## 🎨 Typography

### Headings

```
h1: text-2xl font-semibold (Page title)
h3: text-lg font-bold      (Section headers)
h4: text-base font-bold    (Card values)
h5: text-sm font-medium    (Card labels)
```

### Body Text

```
text-sm: Labels, descriptions
text-xs: Secondary info, metadata
text-base: Main content
```

### Colors

```
text-gray-900    (Primary text - dark)
text-gray-600    (Secondary text)
text-gray-400    (Tertiary text - light)
text-white       (On colored backgrounds)
text-blue-700    (Links, accents)
```

---

## 🔲 Spacing & Layout

### Padding

```
p-3: Card content
p-4: Section padding
p-5: Large sections
p-6: Spacious sections
```

### Margins

```
mb-4: Between sections
mb-6: Large gaps
mt-1: Small vertical spacing
gap-3/4: Grid gaps
```

### Borders & Shadows

```
border: 1px solid
rounded-lg: 8px radius
shadow-lg: Elevated effect
dark:shadow-none: No shadow in dark mode
```

---

## ♿ Accessibility Features

```
✅ Semantic HTML (section, article, header)
✅ ARIA labels on buttons
✅ Color not only means (icons + text)
✅ Sufficient color contrast
✅ Readable font sizes (min 16px on mobile)
✅ Keyboard navigation support
✅ Screen reader friendly
✅ Focus indicators
✅ Form labels associated
✅ Error messages descriptive
```

---

## 📊 Performance Optimizations

### Visual Optimizations

```
✅ CSS classes used (no inline styles)
✅ SVG icons (scalable, small)
✅ Minimal JavaScript
✅ No heavy images
✅ Dark mode: No color filters needed
```

### Load Time Optimization

```
✅ Tailwind purges unused CSS
✅ Minimal JavaScript bundle
✅ Deferred loading possible
✅ Responsive images (optional)
```

---

## 🎬 Animations & Transitions

```
Hover Effects:
- Card hover: bg-color change
- Button hover: Darker shade
- Link hover: Color change
- Table row hover: Light background

Transitions:
- transition-all: Smooth color change
- duration-150: 150ms animation
- smooth interactions, no jarring changes
```

---

## 📸 Visual Hierarchy

```
LEVEL 1 (Most Important):
- Executive Summary Cards
- Anomaly Alerts
- Page Title

LEVEL 2 (Important):
- Grouped table headers
- Filter form
- Action buttons

LEVEL 3 (Supporting):
- Employee names in tables
- Score values
- Grade badges

LEVEL 4 (Background):
- Section separators
- Borders
- Labels
```

---

This visual summary demonstrates how the Performance Rekap page is structured visually to provide the best user experience with proper hierarchy, accessibility, and responsiveness.
