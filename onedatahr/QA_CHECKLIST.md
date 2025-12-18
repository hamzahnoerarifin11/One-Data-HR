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
- [ ] Candidates per-date:
  - [ ] Verify candidate list appears in modal for the selected date/position
  - [ ] Add existing candidate to date (via select) — verify persisted and visible
  - [ ] Quick-add a candidate from modal (create + assign) — verify candidate record created and assigned
  - [ ] Delete candidate entry from date — verify removal

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

UI Changes Completed ✅
- Dashboard (KPI + charts) — converted to Tailadmin card components
- Calendar (per-position daily input modal) — converted and modal layout improved
- Kandidat (list + CRUD) — converted to use card & unified buttons
- Posisi (CRUD) — converted and improved UX
- Per-stage pages (CV, Psikotes, Kompetensi, Interview HR/User) — converted to card layout
- Pemberkasan Monitor — converted to card layout

Remaining:
- Finish candidate-level calendar interactions (assign candidate names to dates)
- Add automated UI tests for recruitment pages
