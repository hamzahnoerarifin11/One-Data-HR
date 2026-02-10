<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Pemberkasan extends Model
{
    use HasFactory;

    protected $table = 'pemberkasan';
    protected $primaryKey = 'id_pemberkasan';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'kandidat_id',
        'posisi_id',
        'follow_up',
        'kandidat_kirim_berkas',
        'selesai_recruitment',
        'selesai_skgk_finance',
        'selesai_ttd_manager_hrd',
        'selesai_ttd_user',
        'selesai_ttd_direktur',
        'jadwal_ttd_kontrak',
        'background_checking',
        'keterangan',
    ];

    protected $casts = [
        'follow_up' => 'date',
        'kandidat_kirim_berkas' => 'date',
        'selesai_recruitment' => 'date',
        'selesai_skgk_finance' => 'date',
        'selesai_ttd_manager_hrd' => 'date',
        'selesai_ttd_user' => 'date',
        'selesai_ttd_direktur' => 'date',
        'jadwal_ttd_kontrak' => 'date',
    ];

    /* ================= HELPERS: TIMELINE DURATIONS ================ */

    /**
     * Hitung selisih hari antara dua kolom tanggal.
     * Jika kedua tanggal ada, kembalikan integer hari, jika hanya tanggal awal ada kembalikan null (sedang berlangsung), jika tidak ada kembalikan null.
     */
    public function durationDays($from, $to)
    {
        if (!$this->$from) return null;
        if (!$this->$to) return null;

        return Carbon::parse($this->$from)->diffInDays(Carbon::parse($this->$to));
    }

    /**
     * Kembalikan label yang siap ditampilkan.
     */
    public function durationLabel($from, $to)
    {
        $days = $this->durationDays($from, $to);

        if (!is_null($days)) {
            return $days . ' hari';
        }

        if ($this->$from && !$this->$to) {
            $since = Carbon::parse($this->$from)->diffInDays(now());
            return "Sedang berlangsung ({$since} hari)";
        }

        return '-';
    }

    /**
     * Menyusun daftar durasi antar tahapan untuk ditampilkan di view
     */
    public function getTimelineDurationsAttribute()
    {
        return [
            'Kandidat Kirim Berkas → Selesai di HR/Rekrutmen' => $this->durationLabel('kandidat_kirim_berkas', 'selesai_recruitment'),
            'Selesai di HR/Rekrutmen → SKGK Finance' => $this->durationLabel('selesai_recruitment', 'selesai_skgk_finance'),
            'SKGK Finance → TTD Manager HRD' => $this->durationLabel('selesai_skgk_finance', 'selesai_ttd_manager_hrd'),
            'TTD Manager HRD → TTD User' => $this->durationLabel('selesai_ttd_manager_hrd', 'selesai_ttd_user'),
            'TTD User → TTD Direktur' => $this->durationLabel('selesai_ttd_user', 'selesai_ttd_direktur'),
        ];
    }

    public function kandidat()
    {
        return $this->belongsTo(Kandidat::class, 'kandidat_id', 'id_kandidat');
    }
    public function posisi()
    {
        return $this->belongsTo(Posisi::class, 'posisi_id', 'id_posisi');
    }

}
