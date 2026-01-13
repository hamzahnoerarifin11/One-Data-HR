<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class OnboardingKaryawan extends Model
{
    use HasFactory;

    protected $table = 'onboarding_karyawan';
    protected $primaryKey = 'id_onboarding';

    protected $fillable = [
        'kandidat_id',
        'posisi_id',

        'pendidikan_terakhir',
        'nama_sekolah',
        'alamat_domisili',
        'nomor_wa',

        'jadwal_ttd_kontrak',

        'tanggal_resign',
        'alasan_resign',

        'masa_kerja_hari',
        'status_turnover',
        'tanggal_lolos_probation',

        'id_card_status',
        'id_card_proses',
        'id_card_jadi',
        'id_card_diambil',

        'no_rekening',

        'fingerprint_status',
        'fingerprint_sudah',

        'link_data_dikirim_hr',
        'link_data_dilengkapi_karyawan',

        'ijazah_diterima_hr',
        'kontrak_ttd_pusat',

        'visi_misi',
        'wadja_philosophy',
        'sejarah_perusahaan',
        'kondisi_perizinan',
        'tata_tertib',
        'bpjs',
        'k3',
        'tanggal_induction',

        'evaluasi',
        'status_onboarding'
    ];

    protected $casts = [
        'visi_misi' => 'boolean',
        'wadja_philosophy' => 'boolean',
        'sejarah_perusahaan' => 'boolean',
        'kondisi_perizinan' => 'boolean',
        'tata_tertib' => 'boolean',
        'bpjs' => 'boolean',
        'k3' => 'boolean',
    ];

    /* ================= HELPER ================= */

    public function formatTanggal($field)
    {
        return $this->$field
            ? Carbon::parse($this->$field)->translatedFormat('d F Y')
            : '-';
    }
    /* ================= MASA KERJA ================= */

    /**
     * Masa kerja dalam HARI
     * Mulai: jadwal_ttd_kontrak
     * Akhir: tanggal_resign / hari ini
     */
    public function getMasaKerjaHariAttribute()
    {
        if (!$this->jadwal_ttd_kontrak) {
            return 0;
        }

        $mulai = Carbon::parse($this->jadwal_ttd_kontrak);

        $akhir = $this->tanggal_resign
            ? Carbon::parse($this->tanggal_resign)
            : Carbon::now();

        if ($akhir->lt($mulai)) {
            return 0;
        }

        return (int) $mulai->diffInDays($akhir);
    }


    /**
     * Masa kerja dalam BULAN (estimasi HR)
     */
    public function getMasaKerjaBulanAttribute()
    {
        return (int) floor($this->masa_kerja_hari / 30);
    }


    /* ================= SCOPE ================= */

    public function getStatusTurnoverAutoAttribute()
    {
        if (!$this->jadwal_ttd_kontrak) {
            return 'belum';
        }

        $tanggalKontrak = Carbon::parse($this->jadwal_ttd_kontrak);
        $batasProbation = $tanggalKontrak->copy()->addMonths(3);

        // JIKA SUDAH RESIGN
        if ($this->tanggal_resign) {
            return Carbon::parse($this->tanggal_resign)->lt($batasProbation)
                ? 'turnover'
                : 'lolos';
        }

        // JIKA BELUM RESIGN
        return now()->lt($batasProbation)
            ? 'belum'
            : 'lolos';
    }

    /* ================= RELATION ================= */

    public function kandidat()
    {
        return $this->belongsTo(Kandidat::class, 'kandidat_id', 'id_kandidat');
    }

    public function posisi()
    {
        return $this->belongsTo(Posisi::class, 'posisi_id', 'id_posisi');
    }
}
