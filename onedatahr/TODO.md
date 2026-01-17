# TODO: Update TEMPA Peserta Create Form

## Tasks
- [ ] Create migration to add keterangan_pindah field to tempa_peserta table
- [ ] Update TempaPeserta model to include keterangan_pindah in fillable
- [ ] Update TempaPesertaRequest to include keterangan_pindah validation
- [ ] Update TempaKelompok model to add mentor relationship (assuming mentor_id field exists or needs to be added)
- [ ] Update TempaPesertaController create method to differentiate between roles (ketua_tempa vs superadmin/admin)
- [ ] Update create.blade.php view to:
  - Show keterangan_pindah field when status is 2 (Pindah)
  - Show kelompok and mentor as text inputs for ketua_tempa
  - Show kelompok and mentor as selects for superadmin/admin with auto-fill functionality
- [ ] Update edit.blade.php view similarly
- [ ] Add JavaScript for auto-fill mentor when kelompok is selected
- [ ] Test the functionality

## Notes
- For ketua_tempa: kelompok and mentor are manual inputs
- For superadmin/admin: kelompok and mentor are selects, mentor auto-fills based on kelompok
- Status 2 (Pindah) shows additional keterangan_pindah field
