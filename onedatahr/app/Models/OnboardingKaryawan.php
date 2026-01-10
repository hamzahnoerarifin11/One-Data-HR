<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    'visi_misi' => 'integer',
    'wadja_philosophy' => 'integer',
    'sejarah_perusahaan' => 'integer',
    'kondisi_perizinan' => 'integer',
    'tata_tertib' => 'integer',
    'bpjs' => 'integer',
    'k3' => 'integer',
    ];


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
