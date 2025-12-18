QA Checklist — Recruitment UI

1) Dashboard
- [ ] Open /dashboard as admin
- [ ] Verify KPI counts show numbers (Total Kandidat, CV Lolos, Psikotes Lolos)
- [ ] Use Filters: select a Posisi, set From/To months, apply and verify charts update
- [ ] Test Add Posisi modal: open, enter name, confirm — the new posisi should appear in select and be selected

2) Posisi Management
- [ ] Open /rekrutmen/posisi as admin
- [ ] Create a new posisi, update it, delete it; verify operations work and UI updates
- [ ] As non-admin, confirm create/update/delete are forbidden (403)

3) Calendar (Kalender Rekrutmen)
- [ ] Open /rekrutmen/calendar as admin
- [ ] Refresh calendar; verify days show counts for positions
- [ ] Click a day → open editor modal → add count → save → verify it is persisted and visible
- [ ] Edit same day and delete entry; verify the change
- [ ] As non-admin, confirm mutations are forbidden

4) Per-stage pages
- [ ] Open CV, Psikotes, Kompetensi, Interview HR/User pages
- [ ] Verify charts render and filters update data
- [ ] Export CSV buttons produce downloadable CSVs

5) Pemberkasan Monitor
- [ ] Open /rekrutmen/metrics/pemberkasan-page
- [ ] Verify table and progress percentages show

6) Security / Authorization
- [ ] Verify mutation endpoints return 403 for non-admin (posisi, rekrutmen daily)

Notes:
- Use admin (admin@example.com or existing 'Admin One') and normal user (test@example.com) to validate auth.
- If you find any UI issues, capture screenshot, describe steps to reproduce, and add to issue tracker.
