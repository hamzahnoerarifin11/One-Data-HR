<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KandidatLanjutUser extends Model
{
    protected $table = 'kandidat_lanjut_user';
    protected $primaryKey = 'id_kandidat_lanjut_user';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'kandidat_id',
        'posisi_id',
        'user_terkait',
        'tanggal_interview_hr',
        'tanggal_penyerahan',
        'detail_interview',
        'catatan',
    ];
    protected $casts = [
        'detail_interview' => 'array', // Kunci agar data dinamis tersimpan otomatis
        // 'tanggal_penyerahan' => 'date',
    ];

   public function kandidat() {
    return $this->belongsTo(Kandidat::class, 'kandidat_id', 'id_kandidat');
    }
    public function posisi()
    {
        return $this->belongsTo(Posisi::class, 'posisi_id', 'id_posisi');
    }
}
