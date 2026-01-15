<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempaMateri extends Model
{
    use HasFactory;

    protected $table = 'tempa_materi';
    protected $primaryKey = 'id_materi';

    protected $fillable = [
        'id_tempa',
        'judul',
        'file_path',
        'uploaded_by'
    ];

    /* =====================
     | RELATIONSHIP
     ===================== */

    public function tempa()
    {
        return $this->belongsTo(Tempa::class, 'id_tempa');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
