<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pemberkasan extends Model
{
    use HasFactory;

    protected $table = 'pemberkasan';
    protected $primaryKey = 'id_pemberkasan';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'kandidat_id',
        'follow_up',
        'kandidat_kirim_berkas',
        'selesai_recruitment',
        'selesai_skgk_finance',
        'selesai_ttd_manager_hrd',
        'selesai_ttd_user',
        'selesai_ttd_direktur',
        'jadwal_ttd_kontrak',
        'background_checking',
    ];

    protected $casts = [
        'kandidat_kirim_berkas' => 'date',
        'selesai_recruitment' => 'date',
        'selesai_skgk_finance' => 'date',
        'selesai_ttd_manager_hrd' => 'date',
        'selesai_ttd_user' => 'date',
        'selesai_ttd_direktur' => 'date',
        'jadwal_ttd_kontrak' => 'date',
    ];

    public function kandidat()
    {
        return $this->belongsTo(Kandidat::class, 'kandidat_id', 'id_kandidat');
    }
}
