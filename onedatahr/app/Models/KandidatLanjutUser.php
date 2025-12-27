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
        'user_terkait',
        'tanggal_interview_hr',
        'tanggal_penyerahan',
        'tanggal_interview_user_ass',
        'hasil_ass',
        'tanggal_interview_user_asm',
        'hasil_asm',
        'catatan',
    ];

    public function kandidat()
    {
        return $this->belongsTo(Kandidat::class, 'kandidat_id');
    }
}
