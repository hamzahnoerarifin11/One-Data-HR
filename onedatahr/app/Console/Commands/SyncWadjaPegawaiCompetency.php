<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\WadjaIntegrationService;
use App\Models\PegawaiKompetensi;
use App\Models\Karyawan;
use Illuminate\Support\Facades\Log;

class SyncWadjaPegawaiCompetency extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wadja:sync-competency';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menarik data Master Kompetensi Pegawai dari Public Integration API Wadja Institute';

    /**
     * Execute the console command.
     */
    public function handle(WadjaIntegrationService $integrationService)
    {
        $this->info('Memulai koneksi ke Wadja Institute API (Mencari endpoint /integration/pegawai-competencies)...');
        Log::info('Sync Action Initiated: Trying to pull Master Data Wadja Competency.');

        $competencyData = $integrationService->fetchPegawaiCompetencies();

        if ($competencyData === null) {
            $this->error('SINKRONISASI GAGAL TERHENTI! Periksa storage log laravel untuk mendeteksi HTTP / Connection Error.');
            return Command::FAILURE;
        }

        // Asumsi fallback array
        $listsOfData = $competencyData['data'] ?? $competencyData; 
        
        // Cek apabila format yang dikembalikan bukan array numerik / list iterasi
        if (!is_array($listsOfData)) {
            $this->error('Struktur response JSON tidak sesuai dengan ekspektasi (harus mengandung list array).');
            return Command::FAILURE;
        }

        $totalItems = count($listsOfData);

        if ($totalItems === 0) {
            $this->warn('Server berhasil merespons, tetapi paket data kompetensi kosong (0 result).');
            return Command::SUCCESS;
        }
        
        $this->info("Menemukan {$totalItems} baris data kompetensi. Melakukan update lokal...");

        $successCount = 0;
        foreach ($listsOfData as $row) {
            $rawNik = $row['nip'] ?? ($row['nik'] ?? null);
            if (!$rawNik) continue;

            // Cari Karyawan Lokal berdasarkan NIK
            $karyawan = Karyawan::where('NIK', $rawNik)->first();
            
            // Jika Karyawan ditemukan dan dia memiliki array kompetensi_diselesaikan
            if ($karyawan && isset($row['kompetensi_diselesaikan']) && is_array($row['kompetensi_diselesaikan'])) {
                foreach ($row['kompetensi_diselesaikan'] as $itemKomp) {
                    $namaKomp = $itemKomp['nama'] ?? 'Tidak Diketahui';
                    $jenisKomp = $itemKomp['jenis'] ?? null; // Memanfaatkan kolom 'level' untuk menyimpan jenis (Fungsional, dll)

                    // Upsert Pegawai Kompetensi per baris sertifikat
                    PegawaiKompetensi::updateOrCreate(
                        [
                            'karyawan_id' => $karyawan->id_karyawan,
                            'nama_kompetensi' => $namaKomp, // Mencegah duplikat kompetensi yang sama per karyawan
                        ],
                        [
                            'level' => $jenisKomp, 
                            'sumber' => 'LMS Wadja',
                        ]
                    );
                    $successCount++;
                }
            }
        }
        
        $this->info("DONE! Proses tarik data kompetensi pegawai berhasil diselesaikan. ($successCount Saved)");
        return Command::SUCCESS;
    }
}
