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
        'judul_materi',
        'file_materi',
        'uploaded_by'
    ];

    /* =====================
     | RELATIONSHIP
     ===================== */

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
